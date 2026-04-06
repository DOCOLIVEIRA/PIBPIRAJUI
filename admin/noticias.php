<?php
/**
 * admin/noticias.php — Gestão de Notícias
 * Lista notícias, permite criar, editar e excluir.
 * Ação determinada pelo parâmetro GET "acao" (listar | novo | editar | salvar | excluir).
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/noticias_crud.php';

$titulo_admin = 'Notícias';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

// -----------------------------------------------------------------------
// Processar ações de POST (salvar e excluir)
// -----------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'listar';

    // Monta array de dados do formulário
    $dados = [
        'titulo'       => trim($_POST['titulo'] ?? ''),
        'resumo'       => trim($_POST['resumo'] ?? ''),
        'conteudo'     => $_POST['conteudo'] ?? '',
        'imagem'       => trim($_POST['imagem'] ?? ''),
        'publicado'    => isset($_POST['publicado']) ? 1 : 0,
        'usuario_id'   => $_SESSION['admin_id'],
        'publicado_em' => !empty($_POST['publicado_em']) ? $_POST['publicado_em'] : date('Y-m-d H:i:s'),
    ];

    // Gera slug apenas no cadastro (evita mudar URL de notícia existente)
    if ($acao === 'inserir') {
        $dados['slug'] = gerar_slug($dados['titulo']);
        try {
            noticias_criar($pdo, $dados);
            definir_flash('Notícia criada com sucesso!', 'success');
        } catch (PDOException $e) {
            definir_flash('Erro ao criar notícia: ' . $e->getMessage(), 'danger');
        }
        redirecionar('/admin/noticias.php');
    }

    if ($acao === 'atualizar') {
        $id_upd = (int)($_POST['id'] ?? 0);
        $noticia_atual = noticias_buscar_por_id($pdo, $id_upd);
        $dados['slug'] = $noticia_atual['slug']; // Mantém o slug original
        try {
            noticias_atualizar($pdo, $id_upd, $dados);
            definir_flash('Notícia atualizada com sucesso!', 'success');
        } catch (PDOException $e) {
            definir_flash('Erro ao atualizar: ' . $e->getMessage(), 'danger');
        }
        redirecionar('/admin/noticias.php');
    }
}

// Excluir via GET
if ($acao === 'excluir' && $id > 0) {
    noticias_deletar($pdo, $id);
    definir_flash('Notícia excluída.', 'success');
    redirecionar('/admin/noticias.php');
}

// -----------------------------------------------------------------------
// Busca de dados para formulários de edição
// -----------------------------------------------------------------------
$noticia_edicao = null;
if ($acao === 'editar' && $id > 0) {
    $noticia_edicao = noticias_buscar_por_id($pdo, $id);
    if (!$noticia_edicao) {
        definir_flash('Notícia não encontrada.', 'danger');
        redirecionar('/admin/noticias.php');
    }
}

// Paginação para listagem
$pagina_atual  = max(1, (int)($_GET['pagina'] ?? 1));
$resultado     = noticias_listar($pdo, $pagina_atual, 15);
$noticias      = $resultado['dados'];
$total_paginas = (int) ceil($resultado['total'] / 15);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Notícias</h2>
    <a href="?acao=novo" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nova Notícia
    </a>
</div>

<?php if ($acao === 'novo' || $acao === 'editar'): ?>
<!-- ============================================================
     FORMULÁRIO DE CRIAÇÃO / EDIÇÃO
     ============================================================ -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <?= $acao === 'editar' ? 'Editar Notícia' : 'Nova Notícia' ?>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/noticias.php">
            <input type="hidden" name="acao" value="<?= $acao === 'editar' ? 'atualizar' : 'inserir' ?>">
            <?php if ($noticia_edicao): ?>
            <input type="hidden" name="id" value="<?= $noticia_edicao['id'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control" required maxlength="255"
                           value="<?= sanitizar($noticia_edicao['titulo'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Resumo</label>
                    <textarea name="resumo" class="form-control" rows="2"
                              placeholder="Breve descrição exibida na listagem"><?= sanitizar($noticia_edicao['resumo'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Conteúdo <span class="text-danger">*</span></label>
                    <textarea name="conteudo" class="form-control" rows="10" required><?= $noticia_edicao['conteudo'] ?? '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Imagem (caminho /img/...)</label>
                    <input type="text" name="imagem" class="form-control"
                           value="<?= sanitizar($noticia_edicao['imagem'] ?? '') ?>"
                           placeholder="ex: /img/noticias/foto.jpg">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data de publicação</label>
                    <input type="datetime-local" name="publicado_em" class="form-control"
                           value="<?= isset($noticia_edicao['publicado_em']) ? date('Y-m-d\TH:i', strtotime($noticia_edicao['publicado_em'])) : date('Y-m-d\TH:i') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="publicado" id="publicado"
                               <?= (!empty($noticia_edicao) && $noticia_edicao['publicado']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="publicado">Publicar</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $acao === 'editar' ? 'Salvar alterações' : 'Criar notícia' ?>
                    </button>
                    <a href="/admin/noticias.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ============================================================
     TABELA DE LISTAGEM
     ============================================================ -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Publicado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($noticias)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma notícia cadastrada.</td></tr>
                    <?php else: ?>
                    <?php foreach ($noticias as $n): ?>
                    <tr>
                        <td class="text-muted small"><?= $n['id'] ?></td>
                        <td><?= sanitizar(truncar($n['titulo'], 70)) ?></td>
                        <td>
                            <span class="badge <?= $n['publicado'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $n['publicado'] ? 'Publicada' : 'Rascunho' ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= formatar_data($n['publicado_em']) ?></td>
                        <td>
                            <a href="?acao=editar&id=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?acao=excluir&id=<?= $n['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Excluir esta notícia?')">
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
    <?php if ($total_paginas > 1): ?>
    <div class="card-footer bg-white">
        <nav><ul class="pagination pagination-sm mb-0 justify-content-center">
            <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
            <li class="page-item <?= ($p === $pagina_atual) ? 'active' : '' ?>">
                <a class="page-link" href="?pagina=<?= $p ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
