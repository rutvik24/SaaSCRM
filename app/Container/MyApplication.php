<?php

namespace App\Container;


use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

class MyApplication extends Application {

    public function environmentPath() {
        $path = $this->basePath ;

        $isConsole = $this->runningInConsole() && ($input = new ArgvInput())->hasParameterOption('--env');

        if($isConsole){
            $path .= DIRECTORY_SEPARATOR . "env";
        } else if (!$this->runningInConsole()) {
            $domain = request()->getHttpHost();
            $subdomain = str_replace('.rutviknabhoya.me', '', $domain);
            if(!empty($subdomain)) {
                $path .= DIRECTORY_SEPARATOR . "env";
                return $path;
            }
        }

        return $path;
    }

    public function environmentFile()
    {
        $isConsole = $this->runningInConsole() && ($input = new ArgvInput())->hasParameterOption('--env');
        if ( $isConsole) {
            return '.env.' . $input->getParameterOption('--env');
        } else if (!$this->runningInConsole()) {
            $domain = request()->getHttpHost();
            $subdomain = str_replace('.rutviknabhoya.me', '', $domain);
            if(!empty($subdomain)) {
                return '.env.' . $subdomain;
            }
        }

        return '.env';
    }

    public function getCachedConfigPath() {
        $env = env('APP_ENV');
        return $this->bootstrapPath()."/cache/config-{$env}.php";
    }
}
