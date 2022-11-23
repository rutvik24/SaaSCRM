<?php

$host = explode('.', $_SERVER['HTTP_HOST']);
$env = [];

if(count($host) === 3) {
    $env['APP_ENV'] = $host[0];
    $env['SAAS_ENV'] = 1;
}

return [
    '*' => $env,
];
