<?php
/**
 * admin/eventos.php — Gestão de Eventos
 * CRUD completo via parâmetro GET "acao".
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/eventos_crud.php';

$titulo_admin = 'Eventos';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

// Processa POST (salvar/atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_post = $_POST['acao'] ?? '';
    $dados = [
        'titulo'      => trim($_POST['titulo'] ?? ''),
        'descricao'   => trim($_POST['descricao'] ?? ''),
        'imagem'      => trim($_POST['imagem'] ?? ''),
        'local'       => trim($_POST['local'] ?? ''),
        'data_inicio' => $_POST['data_inicio'] ?? '',
        'data_fim'    => !empty($_POST['data_fim']) ? $_POST['data_fim'] : null,
        'publicado'   => isset($_POST['publicado']) ? 1 : 0,
    ];
    if ($acao_post === 'inserir') {
        eventos_criar($pdo, $dados);
        definir_flash('Evento criado com sucesso!', 'success');
    } elseif ($acao_post === 'atualizar') {
        $id_upd = (int)($_POST['id'] ?? 0);
        eventos_atualizar($pdo, $id_upd, $dados);
        definir_flash('Evento atualizado com sucesso!', 'success');
    }
    redirecionar('/admin/eventos.php');
}

// Excluir
if ($acao === 'excluir' && $id > 0) {
    eventos_deletar($pdo, $id);
    definir_flash('Evento excluído.', 'success');
    redirecionar('/admin/eventos.php');
}

$evento_edicao = null;
if ($acao === 'editar' && $id > 0) {
    $evento_edicao = eventos_buscar_por_id($pdo, $id);
}

$resultado     = eventos_listar($pdo, 1, 50);
$eventos       = $resultado['dados'];

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Eventos</h2>
    <a href="?acao=novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Evento</a>
</div>

<?php if ($acao === 'novo' || $acao === 'editar'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <?= $acao === 'editar' ? 'Editar Evento' : 'Novo Evento' ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $acao === 'editar' ? 'atualizar' : 'inserir' ?>">
            <?php if ($evento_edicao): ?>
            <input type="hidden" name="id" value="<?= $evento_edicao['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control" required maxlength="255"
                           value="<?= sanitizar($evento_edicao['titulo'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="4"><?= sanitizar($evento_edicao['descricao'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Data de início <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="data_inicio" class="form-control" required
                           value="<?= isset($evento_edicao['data_inicio']) ? date('Y-m-d\TH:i', strtotime($evento_edicao['data_inicio'])) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Data de encerramento</label>
                    <input type="datetime-local" name="data_fim" class="form-control"
                           value="<?= isset($evento_edicao['data_fim']) ? date('Y-m-d\TH:i', strtotime($evento_edicao['data_fim'])) : '' ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Local</label>
                    <input type="text" name="local" class="form-control" maxlength="255"
                           value="<?= sanitizar($evento_edicao['local'] ?? '') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="publicado" id="pub_evento"
                               <?= (!empty($evento_edicao) && $evento_edicao['publicado']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="pub_evento">Publicar evento</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Imagem (caminho /img/...)</label>
                    <input type="text" name="imagem" class="form-control"
                           value="<?= sanitizar($evento_edicao['imagem'] ?? '') ?>">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $acao === 'editar' ? 'Salvar' : 'Criar evento' ?>
                    </button>
                    <a href="/admin/eventos.php" class="btn btn-outline-secondary">Cancelar</a>
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
                <tr><th>#</th><th>Título</th><th>Data Início</th><th>Local</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($eventos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum evento cadastrado.</td></tr>
                <?php else: ?>
                <?php foreach ($eventos as $ev):
                    $passado = strtotime($ev['data_inicio']) < time();
                ?>
                <tr>
                    <td class="text-muted small"><?= $ev['id'] ?></td>
                    <td><?= sanitizar(truncar($ev['titulo'], 60)) ?></td>
                    <td class="small"><?= formatar_data($ev['data_inicio'], true) ?></td>
                    <td class="small text-muted"><?= sanitizar($ev['local'] ?? '—') ?></td>
                    <td>
                        <?php if ($passado): ?>
                        <span class="badge bg-secondary">Encerrado</span>
                        <?php elseif ($ev['publicado']): ?>
                        <span class="badge bg-success">Publicado</span>
                        <?php else: ?>
                        <span class="badge bg-warning text-dark">Rascunho</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?acao=editar&id=<?= $ev['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="?acao=excluir&id=<?= $ev['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Excluir este evento?')">
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
