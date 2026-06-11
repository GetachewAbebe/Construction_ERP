<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appPath = '/home/natanewn/repositories/Construction_ERP';

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

(require_once $appPath.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
