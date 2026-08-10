<?php
/**
 * Registra um acesso à página de anúncio (rescisao-contrato-imobiliario), contando
 * no máximo 1 vez por IP por dia — recarregar a página ou voltar não soma de novo.
 */

header('Content-Type: application/json; charset=utf-8');

$ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
$hoje = date('Y-m-d');

$dir = __DIR__ . '/../data';
if (!is_dir($dir)) {
    mkdir($dir, 0750, true);
}
$arquivo = $dir . '/visits.jsonl';

$jaVisitouHoje = false;
if (is_file($arquivo)) {
    foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        $registro = json_decode($linha, true);
        if ($registro && ($registro['ip'] ?? '') === $ip && ($registro['data'] ?? '') === $hoje) {
            $jaVisitouHoje = true;
            break;
        }
    }
}

if (!$jaVisitouHoje) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $registro = [
        'data'     => $hoje,
        'hora'     => date('H:i:s'),
        'ip'       => $ip,
        'origem'   => mb_substr(trim($input['origem'] ?? ''), 0, 200),
        'referrer' => mb_substr(trim($input['referrer'] ?? ''), 0, 300),
    ];
    $fp = fopen($arquivo, 'a');
    if ($fp && flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($registro, JSON_UNESCAPED_UNICODE) . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

echo json_encode(['ok' => true, 'novo' => !$jaVisitouHoje]);
