<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Production (cPanel) keeps the app outside the web root; fall back to the
// repo-relative path so the app also boots locally and in CI.
$appPath = is_dir('/home/natanewn/repositories/Construction_ERP')
    ? '/home/natanewn/repositories/Construction_ERP'
    : dirname(__DIR__);

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

(require_once $appPath.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
