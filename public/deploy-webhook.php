<?php

$secret = getenv('DEPLOY_WEBHOOK_SECRET');
$signature = 'sha256='.hash_hmac('sha256', file_get_contents('php://input'), $secret);

if (! hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    http_response_code(401);
    exit('Unauthorized');
}

$payload = json_decode(file_get_contents('php://input'), true);

if (($payload['ref'] ?? '') !== 'refs/heads/main') {
    http_response_code(200);
    exit('Not main branch, skipping.');
}

$output = [];
$exit = 0;
exec('cd /home/natanewn/repositories/Construction_ERP && /bin/bash deploy.sh >> /tmp/deploy.log 2>&1 &', $output, $exit);

http_response_code(200);
echo 'Deployment triggered.';
