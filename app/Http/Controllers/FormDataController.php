<?php

namespace App\Http\Controllers;

use App\Models\FormData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FormDataController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subdomain' => ['required', 'alpha_num', Rule::unique('form_data', 'subdomain')],
            'db_username' => ['required', 'alpha_num', Rule::unique('form_data', 'db_username')],
            'email' => ['required', 'email', Rule::unique('form_data', 'email')],
        ]);

        $db_name = 'saas-' . $validatedData['subdomain'];
        $db_username = $validatedData['db_username'];
        $db_password = $this->generateRandomString();
        $host = '129.154.232.195';

//        $createDb = 'CREATE DATABASE ' . $db_name; // create database
//        $createUser = 'CREATE USER \'' . $db_username . '\'@\'localhost\' IDENTIFIED BY \'' . $db_password . '\''; // create user
//        $grantPrivileges = 'GRANT ALL PRIVILEGES ON ' . $db_name . '.* TO \'' . $db_username . '\'@\'localhost\''; // grant privileges
//
//        $output1 = DB::connection()->statement($createDb);
//        $output2 = DB::connection()->statement($createUser);
//        $output3 = DB::connection()->statement($grantPrivileges);
//
//        \Log::info($output1);
//        \Log::info($output2);
//        \Log::info($output3);

        $newEnvoyCommand = sprintf("cd %s && %s vendor/bin/envoy run addDB --db_name=\"%s\" --db_username=\"%s\" --db_password=\"%s\" ", base_path(), env('PHP'), $db_name, $db_username, $db_password);

        \Log::info($newEnvoyCommand);

        $output = shell_exec($newEnvoyCommand);

        \Log::info("output1: " . $output);

        $formData = new FormData();
        $formData->subdomain = strtolower($validatedData['subdomain']);
        $formData->db_username = strtolower($db_username);
        $formData->db_name = strtolower($db_name);
        $formData->db_password = $db_password;
        $formData->email = $validatedData['email'];
        $formData->save();

        $response = Http::withHeaders(['X-Auth-Email' => env('CLOUDFLARE_EMAIL'),
            'X-Auth-Key' => env('CLOUDFLARE_KEY'),
            'Authorization' => 'Bearer ' . env('CLOUDFLARE_TOKEN')])->post('https://api.cloudflare.com/client/v4/zones/b325d932406272ef4dd734643cf8f40b/dns_records', [
            'type' => 'A',
            'name' => $validatedData['subdomain'] . '.rutviknabhoya.me',
            'content' => $host,
            'ttl' => 1,
            'proxied' => true,
        ]);

        $err = $response->failed();
        $responseData = $response->json();

        if ($err) {
            $formData->delete();
            $newEnvoyCommand = 'cd ' . base_path() . ' && /usr/local/opt/php@8.0/bin/php vendor/bin/envoy run delete-db --db_name="' . $db_name . '"';
            \Log::info($newEnvoyCommand);
            $output = shell_exec($newEnvoyCommand);
            \Log::info($output);
            return response()->json(['message' => $responseData['errors'][0]['message']], 500);
        } else {
            \Log::info($responseData);
        }

        $envFile = file_get_contents(base_path('.env'));
        $envFile = str_replace('APP_URL=https://demo.rutviknabhoya.me', 'APP_URL=http://' . $validatedData['subdomain'] . '.rutviknabhoya.me', $envFile);
        $envFile = str_replace('DB_DATABASE=saascrm', 'DB_DATABASE=' . $db_name, $envFile);
        $envFile = str_replace('DB_USERNAME=rutviknabhoya-demo', 'DB_USERNAME=' . $db_username, $envFile);
        $envFile = str_replace('DB_PASSWORD=Admin@123', 'DB_PASSWORD=' . $db_password, $envFile);

        file_put_contents(base_path('env/.env.' . $validatedData['subdomain']), $envFile);

        $environment = (new \josegonzalez\Dotenv\Loader(base_path('env/.env.' . $validatedData['subdomain'])))->parse()->toArray();

        $command = 'php artisan migrate:fresh';

        $process = Process::fromShellCommandline($command, base_path(), $environment);
        $process->run();
        if (!$process->isSuccessful()) {
            \Log::info($process->getErrorOutput());
            throw new ProcessFailedException($process);
        }
        \Log::info($process->getOutput());;

        return redirect('http://' . $validatedData['subdomain'] . '.rutviknabhoya.me' . '/new/app');
    }

    public function create()
    {
        return view('new');
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
