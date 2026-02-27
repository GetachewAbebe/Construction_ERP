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
    
    $vendorDir = __DIR__.'/../vendor';
    $vendorExists = is_dir($vendorDir) ? 'Yes' : 'No';
    $vendorFiles = is_dir($vendorDir) ? implode(', ', array_slice(scandir($vendorDir), 0, 20)) : 'N/A';
    
    // Attempt Auto-Repair if not already triggered in this request
    $repairOutput = 'Not attempted';
    if (isset($_GET['repair'])) {
        $repairOutput = shell_exec('cd .. && bash deploy.sh 2>&1');
    }

    echo '<h1>System Maintenance</h1>';
    echo '<p>The application is currently rebuilding its dependencies. Please refresh in a few moments.</p>';
    echo '<p><a href="?repair=1" style="color: #666; text-decoration: none; font-size: 12px;">Trigger Manual Rebuild</a></p>';
    if (isset($_GET['repair'])) {
        echo '<pre style="background: #f4f4f4; padding: 10px; font-size: 10px; overflow: auto; max-height: 200px;">'.htmlspecialchars($repairOutput).'</pre>';
    }
    echo '<!-- Diagnostics: Autoloader: '.$autoloader.' | Vendor exists: '.$vendorExists.' | Vendor Contents (partial): '.$vendorFiles.' -->';
    exit;
}

require $autoloader;

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(
    Request::capture()
);
