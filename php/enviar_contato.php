<?php
/**
 * php/enviar_contato.php
 * Processa o formulário de contato da página pública.
 *
 * Fluxo: valida campos → salva no banco (tabela contatos)
 * → redireciona com mensagem de sucesso ou erro.
 *
 * Optamos por salvar no banco (em vez de só enviar e-mail)
 * para garantir que nenhuma mensagem seja perdida.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Aceita apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('/contato.php');
}

// -----------------------------------------------------------------------
// 1. Coleta os campos do formulário
// -----------------------------------------------------------------------
$nome      = trim($_POST['nome']      ?? '');
$email     = trim($_POST['email']     ?? '');
$telefone  = trim($_POST['telefone']  ?? '');
$assunto   = trim($_POST['assunto']   ?? '');
$mensagem  = trim($_POST['mensagem']  ?? '');

// -----------------------------------------------------------------------
// 2. Validações básicas
// -----------------------------------------------------------------------
$erros = [];

if (empty($nome)) {
    $erros[] = 'O campo Nome é obrigatório.';
}
if (empty($email) || !email_valido($email)) {
    $erros[] = 'Informe um e-mail válido.';
}
if (empty($mensagem)) {
    $erros[] = 'O campo Mensagem é obrigatório.';
}
if (mb_strlen($mensagem) < 10) {
    $erros[] = 'A mensagem deve ter pelo menos 10 caracteres.';
}

// Se houver erros, volta para o formulário com a mensagem
if (!empty($erros)) {
    definir_flash(implode('<br>', $erros), 'warning');
    redirecionar('/contato.php');
}

// -----------------------------------------------------------------------
// 3. Proteção anti-spam simples (honeypot)
// O campo "website" é oculto via CSS; bots costumam preenchê-lo.
// -----------------------------------------------------------------------
if (!empty($_POST['website'])) {
    // Redireciona silenciosamente sem salvar
    redirecionar('/contato.php?enviado=1');
}

// -----------------------------------------------------------------------
// 4. Salva a mensagem no banco de dados
// -----------------------------------------------------------------------
try {
    $stmt = $pdo->prepare(
        'INSERT INTO contatos (nome, email, telefone, assunto, mensagem, ip)
         VALUES (:nome, :email, :telefone, :assunto, :mensagem, :ip)'
    );
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':telefone' => $telefone ?: null,   // Salva null se não preenchido
        ':assunto'  => $assunto  ?: null,
        ':mensagem' => $mensagem,
        ':ip'       => obter_ip(),           // Registra o IP para auditoria
    ]);

    // -----------------------------------------------------------------------
    // 5. (Opcional) Envia e-mail de notificação para o administrador
    //    Descomente e configure se o servidor suportar mail()
    // -----------------------------------------------------------------------
    /*
    $email_admin = obter_config($pdo, 'site_email', '');
    if (!empty($email_admin)) {
        $assunto_email = 'Nova mensagem de contato: ' . ($assunto ?: 'Sem assunto');
        $corpo_email   = "Nome: $nome\nE-mail: $email\nTelefone: $telefone\n\nMensagem:\n$mensagem";
        mail($email_admin, $assunto_email, $corpo_email,
             "From: $email_admin\r\nReply-To: $email\r\nContent-Type: text/plain; charset=utf-8");
    }
    */

    // Sucesso: redireciona com parâmetro para exibir mensagem na página
    definir_flash('Mensagem enviada com sucesso! Em breve entraremos em contato.', 'success');
    redirecionar('/contato.php');

} catch (PDOException $e) {
    // Registra o erro em log (não exibe detalhes ao usuário)
    error_log('[PibPirajui] Erro ao salvar contato: ' . $e->getMessage());
    definir_flash('Ocorreu um erro ao enviar sua mensagem. Tente novamente mais tarde.', 'danger');
    redirecionar('/contato.php');
}
