<?php
/**
 * includes/header.php
 * Cabeçalho HTML reutilizável do site público
 *
 * Inclui: Bootstrap 5, Bootstrap Icons, CSS próprio, navbar responsiva.
 * Variáveis esperadas (defina antes de incluir):
 *   $titulo_pagina (string) — Título da aba do navegador
 *   $pdo           (PDO)    — Conexão com o banco (para carregar configs)
 */

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Carrega configurações do site
$_site_nome   = isset($pdo) ? obter_config($pdo, 'site_nome_curto', 'PIB Pirajuí') : 'PIB Pirajuí';
$_meta_desc   = isset($pdo) ? obter_config($pdo, 'site_descricao_meta', '') : '';
$_titulo_aba  = isset($titulo_pagina) ? sanitizar($titulo_pagina) . ' | ' . $_site_nome : $_site_nome;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitizar($_meta_desc) ?>">

    <title><?= $_titulo_aba ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/img/favicon.png">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">

    <!-- CSS próprio do site -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<!-- ============================================================
     NAVBAR — Barra de navegação responsiva
     ============================================================ -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container">

        <!-- Logo / Nome do site -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
            <i class="bi bi-book-half fs-4"></i>
            <?= sanitizar($_site_nome) ?>
        </a>

        <!-- Botão hambúrguer para mobile -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarPrincipal"
                aria-controls="navbarPrincipal" aria-expanded="false"
                aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links de navegação -->
        <div class="collapse navbar-collapse" id="navbarPrincipal">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/noticias.php">Notícias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/eventos.php">Eventos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ministerios.php">Ministérios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/contato.php">Contato</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Espaçador para compensar a navbar fixada no topo -->
<div style="padding-top: 70px;"></div>
