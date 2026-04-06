<?php
/**
 * admin/includes/admin_header.php
 * Cabeçalho HTML reutilizável do painel administrativo.
 *
 * Variáveis esperadas:
 *   $titulo_admin (string) — Título da página no painel
 */

// Recupera o nome do usuário logado da sessão
$_admin_nome  = $_SESSION['admin_nome']  ?? 'Administrador';
$_admin_nivel = $_SESSION['admin_nivel'] ?? 'editor';
$_titulo_adm  = isset($titulo_admin) ? htmlspecialchars($titulo_admin, ENT_QUOTES, 'UTF-8') : 'Painel';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_titulo_adm ?> — Painel PIB Pirajuí</title>
    <!-- Impede que robôs indexem o painel admin -->
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">

    <style>
        /* Layout sidebar do painel admin */
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        #sidebar { width: 240px; min-height: 100vh; background: #1e293b; flex-shrink: 0; }
        #sidebar .nav-link { color: #94a3b8; border-radius: 8px; padding: 10px 16px; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { background: #334155; color: #fff; }
        #sidebar .nav-link i { width: 20px; }
        #main-content { flex: 1; overflow-x: auto; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="d-flex">

    <!-- ============================================================
         SIDEBAR — Menu lateral do painel
         ============================================================ -->
    <nav id="sidebar" class="d-flex flex-column p-3">
        <!-- Logo do painel -->
        <a href="/admin/index.php" class="text-white text-decoration-none mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-book-half fs-5"></i>
            <span class="fw-bold">PIB Pirajuí</span>
        </a>

        <!-- Menu de navegação -->
        <ul class="nav flex-column gap-1 flex-grow-1">
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active' : '' ?>"
                   href="/admin/index.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'noticias.php') ? 'active' : '' ?>"
                   href="/admin/noticias.php">
                    <i class="bi bi-newspaper me-2"></i>Notícias
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'eventos.php') ? 'active' : '' ?>"
                   href="/admin/eventos.php">
                    <i class="bi bi-calendar-event me-2"></i>Eventos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'ministerios.php') ? 'active' : '' ?>"
                   href="/admin/ministerios.php">
                    <i class="bi bi-people me-2"></i>Ministérios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'galeria.php') ? 'active' : '' ?>"
                   href="/admin/galeria.php">
                    <i class="bi bi-images me-2"></i>Galeria
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'contatos.php') ? 'active' : '' ?>"
                   href="/admin/contatos.php">
                    <i class="bi bi-envelope me-2"></i>Contatos
                </a>
            </li>

            <li><hr class="border-secondary"></li>

            <?php if ($_admin_nivel === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'usuarios.php') ? 'active' : '' ?>"
                   href="/admin/usuarios.php">
                    <i class="bi bi-person-gear me-2"></i>Usuários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'configuracoes.php') ? 'active' : '' ?>"
                   href="/admin/configuracoes.php">
                    <i class="bi bi-gear me-2"></i>Configurações
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item mt-auto">
                <a class="nav-link text-danger" href="/php/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                </a>
            </li>
        </ul>
    </nav>

    <!-- Conteúdo Principal -->
    <div id="main-content">
        <!-- Barra superior -->
        <div class="topbar px-4 py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold text-muted"><?= $_titulo_adm ?></h6>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-muted"></i>
                <small class="text-muted"><?= htmlspecialchars($_admin_nome, ENT_QUOTES, 'UTF-8') ?></small>
                <span class="badge bg-secondary"><?= $_admin_nivel ?></span>
                <a href="/" target="_blank" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver site
                </a>
            </div>
        </div>

        <!-- Área de conteúdo da página -->
        <div class="p-4">
