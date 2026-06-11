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
$signature = 'sha256=' . hash_hmac('sha256', $body, $secret);

if (! hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    http_response_code(401);
    exit('Unauthorized');
}

$payload = json_decode($body, true);

if (($payload['ref'] ?? '') !== 'refs/heads/main') {
    http_response_code(200);
    exit('Not main branch, skipping.');
}

exec('cd /home/natanewn/repositories/Construction_ERP && /bin/bash deploy.sh >> /tmp/deploy.log 2>&1 &');

http_response_code(200);
echo 'Deployment triggered.';
