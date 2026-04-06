<?php
/**
 * admin/includes/auth_check.php
 * Verificação de autenticação para o painel administrativo.
 *
 * Inclua este arquivo no TOPO de todas as páginas admin (antes de qualquer output).
 * Se o usuário não estiver logado, redireciona para a tela de login.
 */

// Inicia sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se existe um ID de usuário na sessão
if (empty($_SESSION['admin_id'])) {
    // Salva a URL atual para redirecionar após o login bem-sucedido
    $_SESSION['redirect_apos_login'] = $_SERVER['REQUEST_URI'];
    header('Location: /admin/login.php');
    exit;
}
