<?php
/**
 * admin/contatos.php — Visualização e gestão de mensagens de contato
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$titulo_admin = 'Mensagens de Contato';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

// Marca como lida
if ($acao === 'marcar_lida' && $id > 0) {
    $pdo->prepare('UPDATE contatos SET lido = 1 WHERE id = :id')->execute([':id' => $id]);
    definir_flash('Mensagem marcada como lida.', 'success');
    redirecionar('/admin/contatos.php');
}

// Excluir
if ($acao === 'excluir' && $id > 0) {
    $pdo->prepare('DELETE FROM contatos WHERE id = :id')->execute([':id' => $id]);
    definir_flash('Mensagem excluída.', 'success');
    redirecionar('/admin/contatos.php');
}

// Busca contato para visualização
$contato_detalhe = null;
if ($acao === 'ver' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM contatos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $contato_detalhe = $stmt->fetch();
    // Marca automaticamente como lida ao visualizar
    $pdo->prepare('UPDATE contatos SET lido = 1 WHERE id = :id')->execute([':id' => $id]);
}

// Listagem
$stmt = $pdo->query('SELECT * FROM contatos ORDER BY lido ASC, criado_em DESC');
$contatos = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Mensagens de Contato</h2>
</div>

<?php if ($acao === 'ver' && $contato_detalhe): ?>
<!-- Detalhe de mensagem -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between">
        <span class="fw-semibold">Mensagem de <?= sanitizar($contato_detalhe['nome']) ?></span>
        <a href="/admin/contatos.php" class="btn btn-sm btn-outline-secondary">Voltar</a>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-2">Nome</dt>
            <dd class="col-sm-10"><?= sanitizar($contato_detalhe['nome']) ?></dd>
            <dt class="col-sm-2">E-mail</dt>
            <dd class="col-sm-10"><a href="mailto:<?= sanitizar($contato_detalhe['email']) ?>"><?= sanitizar($contato_detalhe['email']) ?></a></dd>
            <dt class="col-sm-2">Telefone</dt>
            <dd class="col-sm-10"><?= sanitizar($contato_detalhe['telefone'] ?: '—') ?></dd>
            <dt class="col-sm-2">Assunto</dt>
            <dd class="col-sm-10"><?= sanitizar($contato_detalhe['assunto'] ?: '—') ?></dd>
            <dt class="col-sm-2">Recebida em</dt>
            <dd class="col-sm-10"><?= formatar_data($contato_detalhe['criado_em'], true) ?></dd>
            <dt class="col-sm-2">IP</dt>
            <dd class="col-sm-10 text-muted small"><?= sanitizar($contato_detalhe['ip'] ?? '—') ?></dd>
        </dl>
        <hr>
        <h6 class="fw-semibold">Mensagem:</h6>
        <p class="bg-light p-3 rounded"><?= nl2br(sanitizar($contato_detalhe['mensagem'])) ?></p>
        <a href="mailto:<?= sanitizar($contato_detalhe['email']) ?>?subject=Re: <?= urlencode($contato_detalhe['assunto'] ?? '') ?>"
           class="btn btn-primary">
            <i class="bi bi-reply me-1"></i>Responder por e-mail
        </a>
        <a href="?acao=excluir&id=<?= $contato_detalhe['id'] ?>"
           class="btn btn-outline-danger ms-2"
           onclick="return confirm('Excluir esta mensagem?')">
            <i class="bi bi-trash me-1"></i>Excluir
        </a>
    </div>
</div>
<?php else: ?>
<!-- Tabela de listagem -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:30px;"></th>
                    <th>Nome</th>
                    <th>Assunto</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contatos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma mensagem recebida.</td></tr>
                <?php else: ?>
                <?php foreach ($contatos as $c): ?>
                <tr class="<?= !$c['lido'] ? 'fw-semibold' : '' ?>">
                    <td>
                        <?php if (!$c['lido']): ?>
                        <span class="badge bg-warning text-dark" title="Não lida">●</span>
                        <?php endif; ?>
                    </td>
                    <td><?= sanitizar($c['nome']) ?><br><small class="text-muted fw-normal"><?= sanitizar($c['email']) ?></small></td>
                    <td><?= sanitizar(truncar($c['assunto'] ?? 'Sem assunto', 50)) ?></td>
                    <td class="text-muted small"><?= formatar_data($c['criado_em']) ?></td>
                    <td>
                        <a href="?acao=ver&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="?acao=excluir&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Excluir mensagem?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
