<?php
/**
 * admin/ministerios.php — Gestão de Ministérios
 * CRUD completo via parâmetro GET "acao".
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/ministerios_crud.php';

$titulo_admin = 'Ministérios';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_post = $_POST['acao'] ?? '';
    $dados = [
        'nome'        => trim($_POST['nome'] ?? ''),
        'descricao'   => trim($_POST['descricao'] ?? ''),
        'icone'       => trim($_POST['icone'] ?? ''),
        'imagem'      => trim($_POST['imagem'] ?? ''),
        'responsavel' => trim($_POST['responsavel'] ?? ''),
        'ordem'       => (int)($_POST['ordem'] ?? 0),
        'ativo'       => isset($_POST['ativo']) ? 1 : 0,
    ];
    if ($acao_post === 'inserir') {
        ministerios_criar($pdo, $dados);
        definir_flash('Ministério criado!', 'success');
    } elseif ($acao_post === 'atualizar') {
        ministerios_atualizar($pdo, (int)($_POST['id'] ?? 0), $dados);
        definir_flash('Ministério atualizado!', 'success');
    }
    redirecionar('/admin/ministerios.php');
}

if ($acao === 'excluir' && $id > 0) {
    ministerios_deletar($pdo, $id);
    definir_flash('Ministério excluído.', 'success');
    redirecionar('/admin/ministerios.php');
}

$min_edicao = null;
if ($acao === 'editar' && $id > 0) {
    $min_edicao = ministerios_buscar_por_id($pdo, $id);
}

$ministerios = ministerios_listar($pdo);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Ministérios</h2>
    <a href="?acao=novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Ministério</a>
</div>

<?php if ($acao === 'novo' || $acao === 'editar'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <?= $acao === 'editar' ? 'Editar Ministério' : 'Novo Ministério' ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $acao === 'editar' ? 'atualizar' : 'inserir' ?>">
            <?php if ($min_edicao): ?>
            <input type="hidden" name="id" value="<?= $min_edicao['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" required
                           value="<?= sanitizar($min_edicao['nome'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ícone (Bootstrap Icons)</label>
                    <input type="text" name="icone" class="form-control"
                           value="<?= sanitizar($min_edicao['icone'] ?? '') ?>"
                           placeholder="bi-people-fill">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ordem de exibição</label>
                    <input type="number" name="ordem" class="form-control" min="0"
                           value="<?= (int)($min_edicao['ordem'] ?? 0) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3"><?= sanitizar($min_edicao['descricao'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Responsável</label>
                    <input type="text" name="responsavel" class="form-control"
                           value="<?= sanitizar($min_edicao['responsavel'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Imagem (/img/...)</label>
                    <input type="text" name="imagem" class="form-control"
                           value="<?= sanitizar($min_edicao['imagem'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ativo" id="ativo_min"
                               <?= (!isset($min_edicao) || $min_edicao['ativo']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="ativo_min">Ativo</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $acao === 'editar' ? 'Salvar' : 'Criar ministério' ?>
                    </button>
                    <a href="/admin/ministerios.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Ord.</th><th>Nome</th><th>Responsável</th><th>Ícone</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($ministerios)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum ministério cadastrado.</td></tr>
                <?php else: ?>
                <?php foreach ($ministerios as $m): ?>
                <tr>
                    <td class="text-muted small"><?= $m['ordem'] ?></td>
                    <td><i class="<?= sanitizar($m['icone'] ?: 'bi-star') ?> me-2 text-primary"></i><?= sanitizar($m['nome']) ?></td>
                    <td class="text-muted small"><?= sanitizar($m['responsavel'] ?? '—') ?></td>
                    <td><code class="small"><?= sanitizar($m['icone'] ?? '—') ?></code></td>
                    <td><span class="badge <?= $m['ativo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $m['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
                    <td>
                        <a href="?acao=editar&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="?acao=excluir&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Excluir ministério?')">
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
