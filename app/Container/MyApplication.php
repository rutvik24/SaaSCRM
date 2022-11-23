<?php

namespace App\Container;


use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

class MyApplication extends Application {

    public function environmentPath() {
        $path = $this->basePath ;

        $isConsole = $this->runningInConsole() && ($input = new ArgvInput())->hasParameterOption('--env');

        if(env('SAAS_ENV') || $isConsole){
            $path .= DIRECTORY_SEPARATOR . "env";
        }

        return $path;
    }

    public function environmentFile()
    {

        if ($this->runningInConsole()) {
            return '.env';
        }

        $domain = request()->getHttpHost();

        $subdomain = explode('.', $domain)[0];

        return '.env.' . $subdomain;
    }

    public function getCachedConfigPath() {
        $env = env('APP_ENV');
        return $this->bootstrapPath()."/cache/config-{$env}.php";
    }
}
