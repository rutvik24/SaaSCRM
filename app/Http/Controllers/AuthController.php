<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use josegonzalez\Dotenv\Loader;
use Log;
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
        $data['db_password'] = $this->generateRandomString();
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
//
        $newEnvoyCommand = sprintf("cd %s && %s vendor/bin/envoy run addDB --db_name=\"%s\" --db_username=\"%s\" --db_password=\"%s\" --siteDomain=\"%s\" ", base_path(), env('PHP'), $db_name, $db_username, $db_password, explode('https://',env('APP_URL'))[1]);

        Log::info($newEnvoyCommand);

        $output = shell_exec($newEnvoyCommand);

        Log::info("output1: " . $output);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->company_name = $data['company_name'];
        $user->subdomain = $data['subdomain'];
        $user->username = $data['username'];
        $user->db_name = $data['db_name'];
        $user->db_password = $data['db_password'];
        if($data['planType'] === 'free') {
            $user->plan_type = 'free';
            $user->allowed_data = 5;
            $user->subscription_end_date = date('Y-m-d', strtotime('+1 month -1 day'));
        }

        $user->save();

        $envFile = file_get_contents(base_path('.env'));
        $envFile = str_replace('APP_URL=' . env('APP_URL'), 'APP_URL=https://' . $data['subdomain'] . '.' . env('DOMAIN'), $envFile);
        $envFile = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE=' . $db_name, $envFile);
        $envFile = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME=' . $db_username, $envFile);
        $envFile = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD=' . $db_password, $envFile);

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

        if ($data['planType'] === 'free') {
            return redirect('https://' . $data['subdomain'] . '.' . env('DOMAIN') . '/new/app');
        } else {
            return redirect()->route('checkout', ['userId' => $user->id, 'planType' => $data['planType']]);
        }

    }

    public static function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
