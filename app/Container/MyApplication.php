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
            $domainParts = explode('.', $domain);
            if(count($domainParts) > 2) {
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
//            $subdomain = str_replace('.rutviknabhoya.me', '', $domain);
            $domainParts = explode('.', $domain);
            if (count($domainParts) > 2) {
                if (count($domainParts) > 3) {
                    return '.env.app.' . $domainParts[0];
                } else {
                    return '.env.' . $domainParts[0];

                }
            }
        }

        return '.env';
    }

    public function getCachedConfigPath() {
        $env = env('APP_ENV');
        return $this->bootstrapPath()."/cache/config-{$env}.php";
    }
}
