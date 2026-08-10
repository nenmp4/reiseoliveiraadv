<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

function lerJsonl($caminho) {
    if (!is_file($caminho)) return [];
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $registros = [];
    foreach ($linhas as $linha) {
        $dado = json_decode($linha, true);
        if ($dado) $registros[] = $dado;
    }
    return array_reverse($registros);
}

$leads = lerJsonl(__DIR__ . '/../data/leads.jsonl');
$visitas = lerJsonl(__DIR__ . '/../data/visits.jsonl');

$totalLeads = count($leads);
$totalVisitas = count($visitas);
$taxaConversao = $totalVisitas > 0 ? round(($totalLeads / $totalVisitas) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Leads - Reis e Oliveira Advocacia</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000000;
            --secondary: #121212;
            --accent: #D4AF37;
            --accent-light: #FFD700;
            --text: #FFFFFF;
            --text-secondary: #CCCCCC;
            --card-bg: #1A1A1A;
            --border: #333333;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary);
            color: var(--text);
            padding: 2rem 1.5rem 4rem;
        }
        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto 2rem;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 1.6rem;
        }
        a.sair {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            border: 1px solid var(--border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        a.sair:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            max-width: 1200px;
            margin: 0 auto 2.5rem;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .card strong {
            display: block;
            font-size: 2rem;
            color: var(--accent);
            font-family: 'Playfair Display', serif;
        }
        .card span {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        .tabela-wrap {
            max-width: 1200px;
            margin: 0 auto;
            overflow-x: auto;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        th, td {
            padding: 0.9rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        th {
            color: var(--accent);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background: rgba(212, 175, 55, 0.05);
        }
        .vazio {
            padding: 3rem;
            text-align: center;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="topo">
        <h1>Painel de Leads — Rescisão de Contrato Imobiliário</h1>
        <a class="sair" href="logout.php">Sair</a>
    </div>

    <div class="cards">
        <div class="card"><strong><?= $totalVisitas ?></strong><span>Acessos únicos (1 por IP/dia)</span></div>
        <div class="card"><strong><?= $totalLeads ?></strong><span>Leads recebidos</span></div>
        <div class="card"><strong><?= $taxaConversao ?>%</strong><span>Taxa de conversão</span></div>
    </div>

    <div class="tabela-wrap">
        <?php if (empty($leads)): ?>
            <div class="vazio">Nenhum lead recebido ainda.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Nome</th>
                    <th>WhatsApp</th>
                    <th>Situação</th>
                    <th>Multa &gt;25%</th>
                    <th>Atraso 180+ dias</th>
                    <th>Valor pago</th>
                    <th>Origem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><?= htmlspecialchars($lead['data'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['nome'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['telefone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['situacao'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['multa25'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($lead['atraso180'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($lead['valor'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($lead['origem'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
