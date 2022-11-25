<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use josegonzalez\Dotenv\Loader;
use Log;
use Razorpay\Api\Api;
use Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class AuthController extends Controller
{
    public function create($subdomain, $planType)
    {
        $data = DB::connection('mysql2')->table('users')->where('subdomain', $subdomain)->first();
        if (!$data && $planType === 'free' || 'basic' || 'premium') {
            return view('auth.create', ['subdomain' => $subdomain, 'planType' => $planType]);
        } else {
            return redirect()->route('home');
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if ($input['planType'] !== 'free') {

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $payment = $api->payment->fetch($input['razorpay_payment_id']);

            if (count($input) && !empty($input['razorpay_payment_id'])) {
                try {
                    $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));

                } catch (Exception $e) {
                    return $e->getMessage();
                    Session::put('error', $e->getMessage());
                    return redirect()->back()->withErrors('subdomain', 'Payment failed for this subdomain');
                }
            }

            Session::put('success', 'Payment successful');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'alpha_num', 'min:3', Rule::unique('users', 'subdomain')],
            'username' => ['required', 'alpha_num', 'min:3', Rule::unique('users', 'username')],
            'planType' => ['required', 'string', 'max:255']
        ]);

        $data['db_name'] = 'saas-' . $data['subdomain'];
        $data['db_password'] = FormDataController::generateRandomString();
        $data['password'] = bcrypt($data['password']);
        $db_username = $data['username'];
        $db_name = $data['db_name'];
        $db_password = $data['db_password'];

//        $createDb = 'CREATE DATABASE ' . $db_name; // create database
//        $createUser = 'CREATE USER \'' . $db_username . '\'@\'localhost\' IDENTIFIED BY \'' . $db_password . '\''; // create user
//        $grantPrivileges = 'GRANT ALL PRIVILEGES ON ' . $db_name . '.* TO \'' . $db_username . '\'@\'localhost\''; // grant privileges
//
//        $output1 = DB::connection()->statement($createDb);
//        $output2 = DB::connection()->statement($createUser);
//        $output3 = DB::connection()->statement($grantPrivileges);
//
//        Log::info($output1);
//        Log::info($output2);
//        Log::info($output3);

        $newEnvoyCommand = sprintf("cd %s && %s vendor/bin/envoy run addDB --db_name=\"%s\" --db_username=\"%s\" --db_password=\"%s\" ", base_path(), env('PHP'), $db_name, $db_username, $db_password);

        Log::info($newEnvoyCommand);

        $output = shell_exec($newEnvoyCommand);

        Log::info("output1: " . $output);

        $allowed_data = $data['planType'] === 'free' ? 5 : ($data['planType'] === 'basic' ? 10 : ($data['planType'] === 'premium' ? 15 : 0));

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->company_name = $data['company_name'];
        $user->subdomain = $data['subdomain'];
        $user->username = $data['username'];
        $user->db_name = $data['db_name'];
        $user->db_password = $data['db_password'];
        $user->plan_type = $data['planType'];
        $user->allowed_data = $allowed_data;

        $user->save();

        $response = Http::withHeaders(['X-Auth-Email' => env('CLOUDFLARE_EMAIL'),
            'X-Auth-Key' => env('CLOUDFLARE_KEY'),
            'Authorization' => 'Bearer ' . env('CLOUDFLARE_TOKEN')])->post('https://api.cloudflare.com/client/v4/zones/b325d932406272ef4dd734643cf8f40b/dns_records', [
            'type' => 'A',
            'name' => $data['subdomain'] . '.rutviknabhoya.me',
            'content' => env('HOST'),
            'ttl' => 1,
            'proxied' => true,
        ]);

        $err = $response->failed();
        $responseData = $response->json();

        if ($err) {
            $user->delete();
            $newEnvoyCommand = 'cd ' . base_path() . ' && /usr/local/opt/php@8.0/bin/php vendor/bin/envoy run delete-db --db_name="' . $db_name . '"';
            Log::info($newEnvoyCommand);
            $output = shell_exec($newEnvoyCommand);
            Log::info($output);
            return response()->json(['message' => $responseData['errors'][0]['message']], 500);
        } else {
            Log::info($responseData);
        }

        $envFile = file_get_contents(base_path('.env'));
        $envFile = str_replace('APP_URL=https://app.rutviknabhoya.me', 'APP_URL=http://' . $data['subdomain'] . '.rutviknabhoya.me', $envFile);
        $envFile = str_replace('DB_DATABASE=saasdb', 'DB_DATABASE=' . $db_name, $envFile);
        $envFile = str_replace('DB_USERNAME=rutvik-demo', 'DB_USERNAME=' . $db_username, $envFile);
        $envFile = str_replace('DB_PASSWORD=Admin@123', 'DB_PASSWORD=' . $db_password, $envFile);
//        $envFile = str_replace('APP_URL=https://saascrm.test', 'APP_URL=http://' . $data['subdomain'] . '.saascrm.test', $envFile);
//        $envFile = str_replace('DB_DATABASE=saascrm', 'DB_DATABASE=' . $db_name, $envFile);
//        $envFile = str_replace('DB_USERNAME=root', 'DB_USERNAME=' . $db_username, $envFile);
//        $envFile = str_replace('DB_PASSWORD=', 'DB_PASSWORD=' . $db_password, $envFile);

        file_put_contents(base_path('env/.env.' . $data['subdomain']), $envFile);

        $environment = (new Loader(base_path('env/.env.' . $data['subdomain'])))->parse()->toArray();

        $command = 'php artisan migrate';

        Log::info($environment);

        $process = Process::fromShellCommandline($command, base_path(), $environment);
        $process->run();
        if (!$process->isSuccessful()) {
            Log::info($process->getErrorOutput());
            throw new ProcessFailedException($process);
        }
        Log::info($process->getOutput());

        return redirect('http://' . $data['subdomain'] . '.rutviknabhoya.me' . '/new/app');
//        return redirect('http://' . $data['subdomain'] . '.saascrm.test' . '/new/app');
    }
}
