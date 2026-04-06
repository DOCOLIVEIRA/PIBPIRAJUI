<?php
/**
 * admin/login.php
 * Página de login do painel administrativo.
 * Exibe o formulário e mensagens de erro/sucesso via flash.
 */

// Inicia sessão para verificar flash messages e redirecionar se já logado
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../includes/functions.php';

// Se já está logado, vai direto ao dashboard
if (!empty($_SESSION['admin_id'])) {
    redirecionar('/admin/index.php');
}

// Verifica mensagem de logout
$logout_message = isset($_GET['logout']) ? 'Você saiu do sistema com sucesso.' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Painel PIB Pirajuí</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <!-- Logo / título -->
    <div class="text-center mb-4">
        <i class="bi bi-book-half text-primary" style="font-size:3rem;"></i>
        <h1 class="h4 fw-bold mt-2">Painel Administrativo</h1>
        <p class="text-muted small">Primeira Igreja Batista de Pirajuí</p>
    </div>

    <!-- Mensagem de logout bem-sucedido -->
    <?php if ($logout_message): ?>
    <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <?= sanitizar($logout_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Mensagens flash (erros de login) -->
    <?php exibir_flash(); ?>

    <!-- Formulário de login -->
    <form action="/php/login.php" method="POST" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                       required autofocus autocomplete="username"
                       placeholder="admin@pibpirajui.com.br">
            </div>
        </div>
        <div class="mb-4">
            <label for="senha" class="form-label fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="senha" name="senha"
                       required autocomplete="current-password" placeholder="••••••••">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="/" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao site
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
