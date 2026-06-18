<?php
// deploy.php — GitHub webhook receiver
// Coloca este archivo en el servidor y configura el webhook en GitHub
// apuntando a: https://tudominio.com/fhbrestaurant/web/html/deploy.php

define('DEPLOY_SECRET', '4e99e49fd29113de4a1dc362ca8392c58d88241dac888117d2120d94678e2afc');
define('REPO_PATH',     'C:/Users/administrator/Yael');

// Solo aceptar POST de GitHub
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Verificar firma HMAC de GitHub
$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected  = 'sha256=' . hash_hmac('sha256', $payload, DEPLOY_SECRET);

if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    exit('Forbidden');
}

// Solo actuar en push a main
$data = json_decode($payload, true);
if (($data['ref'] ?? '') !== 'refs/heads/main') {
    http_response_code(200);
    exit('Ignored — not main branch');
}

// Ejecutar git pull
$output = shell_exec('cd ' . escapeshellarg(REPO_PATH) . ' && git pull origin main 2>&1');

// Log del resultado
$log = date('Y-m-d H:i:s') . " | " . trim($output) . "\n";
file_put_contents(REPO_PATH . '/deploy.log', $log, FILE_APPEND);

http_response_code(200);
echo 'OK: ' . htmlspecialchars(trim($output));
