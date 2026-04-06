<?php
/**
 * php/contato.php
 * Endpoint AJAX — recebe POST do formulário de contato
 * Retorna JSON: { "ok": bool, "mensagem": string }
 */

// Apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['ok' => false, 'mensagem' => 'Método não permitido.']));
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/conexao.php';

// ── Coleta e sanitiza entradas ─────────────────────────────
$nome     = trim(filter_input(INPUT_POST, 'nome',     FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL) ?? '');
$mensagem = trim(filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

// ── Validação básica ───────────────────────────────────────
if (empty($nome) || strlen($nome) < 2) {
    echo json_encode(['ok' => false, 'mensagem' => 'Nome inválido.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'mensagem' => 'E-mail inválido.']);
    exit;
}

if (empty($mensagem) || strlen($mensagem) < 10) {
    echo json_encode(['ok' => false, 'mensagem' => 'Mensagem muito curta (mínimo 10 caracteres).']);
    exit;
}

// ── Grava no banco ─────────────────────────────────────────
try {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO contato_mensagens (nome, email, mensagem, ip)
        VALUES (:nome, :email, :mensagem, :ip)
    ");

    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':mensagem' => $mensagem,
        ':ip'       => $ip,
    ]);

    echo json_encode([
        'ok'      => true,
        'mensagem' => 'Mensagem recebida! Entraremos em contato em breve.'
    ]);

} catch (PDOException $e) {
    error_log('[PIB Pirajuí] Erro ao salvar contato: ' . $e->getMessage());
    echo json_encode([
        'ok'      => false,
        'mensagem' => 'Erro interno. Tente novamente mais tarde.'
    ]);
}
