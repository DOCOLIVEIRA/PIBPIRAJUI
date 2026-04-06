<?php
/**
 * php/login.php
 * Processa o formulário de login do painel administrativo.
 *
 * Fluxo: recebe POST (email + senha) → valida credenciais
 * → inicia sessão → redireciona ao painel ou exibe erro.
 *
 * Este script NÃO é uma página: ele apenas processa e redireciona.
 */

// Carrega dependências
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/usuarios_crud.php';

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------
// 1. Aceita apenas requisições POST
// -----------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('/admin/login.php');
}

// -----------------------------------------------------------------------
// 2. Coleta e valida os campos do formulário
// -----------------------------------------------------------------------
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha']    ?? '';

// Valida campos obrigatórios
if (empty($email) || empty($senha)) {
    definir_flash('Preencha o e-mail e a senha para continuar.', 'warning');
    redirecionar('/admin/login.php');
}

// Valida formato do e-mail
if (!email_valido($email)) {
    definir_flash('Formato de e-mail inválido.', 'warning');
    redirecionar('/admin/login.php');
}

// -----------------------------------------------------------------------
// 3. Busca o usuário no banco e verifica a senha
// -----------------------------------------------------------------------
$usuario = usuarios_buscar_para_login($pdo, $email);

// Verifica se encontrou o usuário E se a senha bate com o hash
if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    // Mensagem genérica para não revelar se o e-mail existe
    definir_flash('E-mail ou senha incorretos. Tente novamente.', 'danger');
    redirecionar('/admin/login.php');
}

// -----------------------------------------------------------------------
// 4. Regenera o ID da sessão (previne Session Fixation)
// -----------------------------------------------------------------------
session_regenerate_id(true);

// -----------------------------------------------------------------------
// 5. Armazena os dados do usuário na sessão
// -----------------------------------------------------------------------
$_SESSION['admin_id']    = $usuario['id'];
$_SESSION['admin_nome']  = $usuario['nome'];
$_SESSION['admin_email'] = $usuario['email'];
$_SESSION['admin_nivel'] = $usuario['nivel'];

// -----------------------------------------------------------------------
// 6. Redireciona para a URL que tentaram acessar antes do login,
//    ou para o dashboard por padrão
// -----------------------------------------------------------------------
$destino = $_SESSION['redirect_apos_login'] ?? '/admin/index.php';
unset($_SESSION['redirect_apos_login']); // Limpa a URL salva

redirecionar($destino);
