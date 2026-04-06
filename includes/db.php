<?php
/**
 * includes/db.php
 * Conexão com o banco de dados MySQL via PDO
 *
 * Use este arquivo em qualquer página com: require_once __DIR__ . '/../includes/db.php';
 * Após o include, use a variável $pdo para realizar consultas.
 *
 * Hostgator: as credenciais ficam no cPanel → MySQL Databases
 */

// -----------------------------------------------------------------------------
// CONFIGURAÇÕES DE CONEXÃO
// Substitua os valores abaixo pelas credenciais reais da Hostgator
// -----------------------------------------------------------------------------
define('DB_HOST', 'localhost');          // Geralmente "localhost" na Hostgator
define('DB_NAME', 'seu_banco_de_dados'); // Nome do banco criado no cPanel
define('DB_USER', 'seu_usuario');        // Usuário do banco (formato: conta_usuario)
define('DB_PASS', 'sua_senha');          // Senha do usuário do banco
define('DB_CHARSET', 'utf8mb4');         // Charset completo para suporte a emojis e acentos

// -----------------------------------------------------------------------------
// DSN — Data Source Name para o PDO
// -----------------------------------------------------------------------------
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

// Opções de configuração do PDO
$opcoes_pdo = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lança exceções em caso de erro SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retorna resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Usa prepared statements nativos do MySQL
];

// -----------------------------------------------------------------------------
// CRIAÇÃO DA CONEXÃO PDO
// Encapsulada em try/catch para tratamento de erros de conexão
// -----------------------------------------------------------------------------
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes_pdo);
} catch (PDOException $e) {
    // Em produção, não exiba detalhes do erro ao usuário — registre em log
    error_log('[PibPirajui] Falha na conexão com o banco: ' . $e->getMessage());
    die('<p style="font-family:sans-serif;color:red;text-align:center;margin-top:50px;">'
        . 'Erro interno do servidor. Por favor, tente novamente mais tarde.</p>');
}
