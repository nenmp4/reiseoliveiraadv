<?php
session_start();

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: leads.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $hashFile = __DIR__ . '/.admin_password';

    $ok = false;
    if (is_file($hashFile)) {
        $hash = trim(file_get_contents($hashFile));
        if ($usuario === 'admin' && $hash !== '' && password_verify($senha, $hash)) {
            $ok = true;
        }
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: leads.php');
        exit;
    }
    $erro = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Leads - Reis e Oliveira Advocacia</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 380px;
            width: 100%;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 0.3rem;
            color: var(--accent);
        }
        p.subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1.2rem;
            border-radius: 8px;
            border: 2px solid var(--border);
            background: var(--secondary);
            color: var(--text);
            font-family: inherit;
            font-size: 1rem;
        }
        input:focus {
            outline: none;
            border-color: var(--accent);
        }
        button {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: var(--primary);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
        }
        .erro {
            background: rgba(192, 57, 43, 0.15);
            border: 1px solid #C0392B;
            color: var(--text);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Painel de Leads</h1>
        <p class="subtitle">Rescisão de Contrato Imobiliário</p>
        <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <form method="post">
            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" required autofocus autocomplete="username">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required autocomplete="current-password">
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
