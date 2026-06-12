<?php

$envPath = '/home/natanewn/repositories/Construction_ERP/.env';
$secret = '';

if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strncmp($line, 'DEPLOY_WEBHOOK_SECRET=', 22) === 0) {
            $secret = substr($line, 22);
            break;
        }
    }
}

$body = file_get_contents('php://input');
$signature = 'sha256='.hash_hmac('sha256', $body, $secret);

if (! hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    http_response_code(401);
    exit('Unauthorized');
}

$payload = json_decode($body, true);

if (($payload['ref'] ?? '') !== 'refs/heads/main') {
    http_response_code(200);
    exit('Not main branch, skipping.');
}

// Respond immediately so the caller is not blocked waiting for deploy to finish
http_response_code(200);
echo 'Deployment triggered.';

if (ob_get_level()) {
    ob_end_flush();
}
flush();

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

// Run deploy fully detached from this PHP process
exec('nohup /bin/bash -c "cd /home/natanewn/repositories/Construction_ERP && /bin/bash deploy.sh >> /tmp/deploy.log 2>&1" > /dev/null 2>&1 &');
