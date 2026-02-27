<?php

ini_set('memory_limit', '512M');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Resolve & Check Autoloader
$autoloader = __DIR__.'/../vendor/autoload.php';

if (! file_exists($autoloader)) {
    header('HTTP/1.1 503 Service Unavailable');
    header('Retry-After: 300'); // 5 minutes
    echo "<h1>System Maintenance</h1><p>The application is currently rebuilding its dependencies. Please refresh in a few moments.</p>";
    exit;
}

require $autoloader;

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(
    Request::capture()
);
