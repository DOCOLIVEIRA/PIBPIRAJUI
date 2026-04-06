<?php
/**
 * php/logout.php
 * Encerra a sessão do usuário logado no painel administrativo.
 *
 * Destrói todos os dados da sessão e redireciona para a tela de login.
 */

// Inicia a sessão para poder destruí-la
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis da sessão
$_SESSION = [];

// Apaga o cookie de sessão do navegador do cliente
if (isset($_COOKIE[session_name()])) {
    setcookie(
        session_name(),  // nome do cookie de sessão (PHPSESSID)
        '',              // valor vazio
        time() - 42000, // data no passado para expirar o cookie
        '/'             // caminho raiz
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona para a página de login com mensagem de sucesso
// (A mensagem precisa ser definida via GET pois a sessão foi destruída)
header('Location: /admin/login.php?logout=1');
exit;
