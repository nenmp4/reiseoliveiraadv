<?php
/**
 * Recebe um lead do formulário da página de anúncio (rescisao-contrato-imobiliario)
 * e grava em data/leads.jsonl. Chamado via fetch() pelo JS da página, em paralelo
 * ao redirecionamento pro WhatsApp — não substitui o WhatsApp, só registra.
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['nome']) || empty($input['telefone'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'dados incompletos']);
    exit;
}

$dir = __DIR__ . '/../data';
if (!is_dir($dir)) {
    mkdir($dir, 0750, true);
}

$lead = [
    'data'      => date('Y-m-d H:i:s'),
    'nome'      => mb_substr(trim($input['nome']), 0, 200),
    'telefone'  => mb_substr(trim($input['telefone']), 0, 50),
    'situacao'  => mb_substr(trim($input['situacao'] ?? ''), 0, 200),
    'multa25'   => mb_substr(trim($input['multa25'] ?? ''), 0, 20),
    'atraso180' => mb_substr(trim($input['atraso180'] ?? ''), 0, 20),
    'valor'     => mb_substr(trim($input['valor'] ?? ''), 0, 100),
    'origem'    => mb_substr(trim($input['origem'] ?? ''), 0, 200),
    'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
];

$fp = fopen($dir . '/leads.jsonl', 'a');
if ($fp && flock($fp, LOCK_EX)) {
    fwrite($fp, json_encode($lead, JSON_UNESCAPED_UNICODE) . "\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

echo json_encode(['ok' => true]);
