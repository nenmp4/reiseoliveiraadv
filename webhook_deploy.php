<?php
/**
 * GitHub Webhook — Auto Deploy (Reis & Oliveira Advocacia)
 * Payload URL: https://reiseoliveiraadv.com.br/webhook_deploy.php
 * Content type: application/json
 * Secret: valor gravado em .webhook_secret (criado manualmente no servidor, fora do git)
 * Events: Just the push event
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$secretFile = __DIR__ . '/.webhook_secret';
if (!is_file($secretFile)) {
    http_response_code(500);
    exit('Webhook secret not configured');
}
$secret = trim(file_get_contents($secretFile));

$payload   = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if ($secret === '' || $sigHeader === '') {
    http_response_code(401);
    exit('Unauthorized');
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expected, $sigHeader)) {
    http_response_code(403);
    exit('Forbidden — invalid signature');
}

// Aceita tanto "application/json" quanto "application/x-www-form-urlencoded"
// (a assinatura acima já validou o corpo bruto nos dois casos)
$data = json_decode($payload, true);
if (!is_array($data) && isset($_POST['payload'])) {
    $data = json_decode($_POST['payload'], true);
}

$branch = $data['ref'] ?? '';

if ($branch !== 'refs/heads/main') {
    http_response_code(200);
    exit('Ignored — not a push to main');
}

file_put_contents(__DIR__ . '/.deploy_pending', json_encode([
    'pusher'    => $data['pusher']['name'] ?? 'unknown',
    'commits'   => count($data['commits'] ?? []),
    'message'   => $data['head_commit']['message'] ?? '',
    'timestamp' => date('Y-m-d H:i:s'),
]));

http_response_code(200);
echo json_encode(['ok' => true, 'msg' => 'Deploy scheduled']);
