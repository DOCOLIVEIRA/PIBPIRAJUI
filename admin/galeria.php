<?php
/**
 * admin/galeria.php — Gestão da Galeria de Fotos
 * Upload de imagens e CRUD da tabela `galeria`.
 * O arquivo físico é enviado para /img/galeria/.
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/galeria_crud.php';

$titulo_admin = 'Galeria de Fotos';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

// Diretório físico de destino dos uploads
$dir_upload = __DIR__ . '/../img/galeria/';

// Garante que o diretório existe
if (!is_dir($dir_upload)) {
    mkdir($dir_upload, 0755, true);
}

// -----------------------------------------------------------------------
// Processar upload e salvar
// -----------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_post = $_POST['acao'] ?? '';

    if ($acao_post === 'inserir') {
        // Verifica se um arquivo foi enviado
        if (empty($_FILES['arquivo']['name'])) {
            definir_flash('Selecione uma imagem para upload.', 'warning');
            redirecionar('/admin/galeria.php?acao=novo');
        }

        $arquivo    = $_FILES['arquivo'];
        $extensao   = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $tipo_mime  = mime_content_type($arquivo['tmp_name']);

        // Tipos de arquivo permitidos
        $tipos_ok = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($tipo_mime, $tipos_ok)) {
            definir_flash('Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF.', 'danger');
            redirecionar('/admin/galeria.php?acao=novo');
        }

        // Gera nome único para o arquivo para evitar sobrescrita
        $nome_arquivo = uniqid('foto_') . '.' . $extensao;
        $caminho_destino = $dir_upload . $nome_arquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $caminho_destino)) {
            $dados = [
                'arquivo'   => '/img/galeria/' . $nome_arquivo,
                'titulo'    => trim($_POST['titulo'] ?? ''),
                'categoria' => trim($_POST['categoria'] ?? ''),
                'ordem'     => (int)($_POST['ordem'] ?? 0),
                'ativo'     => isset($_POST['ativo']) ? 1 : 0,
            ];
            galeria_criar($pdo, $dados);
            definir_flash('Foto adicionada com sucesso!', 'success');
        } else {
            definir_flash('Erro ao fazer upload. Verifique as permissões da pasta /img/galeria/.', 'danger');
        }
        redirecionar('/admin/galeria.php');
    }

    if ($acao_post === 'atualizar') {
        $id_upd = (int)($_POST['id'] ?? 0);
        $dados = [
            'titulo'    => trim($_POST['titulo'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'ordem'     => (int)($_POST['ordem'] ?? 0),
            'ativo'     => isset($_POST['ativo']) ? 1 : 0,
        ];
        galeria_atualizar($pdo, $id_upd, $dados);
        definir_flash('Foto atualizada!', 'success');
        redirecionar('/admin/galeria.php');
    }
}

// Excluir: remove do banco e apaga o arquivo físico
if ($acao === 'excluir' && $id > 0) {
    $foto = galeria_buscar_por_id($pdo, $id);
    if ($foto) {
        $caminho_fisico = __DIR__ . '/..' . $foto['arquivo'];
        if (is_file($caminho_fisico)) {
            unlink($caminho_fisico); // Apaga o arquivo da pasta
        }
        galeria_deletar($pdo, $id);
        definir_flash('Foto excluída.', 'success');
    }
    redirecionar('/admin/galeria.php');
}

$foto_edicao = null;
if ($acao === 'editar' && $id > 0) {
    $foto_edicao = galeria_buscar_por_id($pdo, $id);
}

$resultado  = galeria_listar($pdo, 1, 50);
$fotos      = $resultado['dados'];
$categorias = galeria_listar_categorias($pdo);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Galeria de Fotos</h2>
    <a href="?acao=novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Adicionar Foto</a>
</div>

<?php if ($acao === 'novo'): ?>
<!-- Formulário de upload -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Adicionar Nova Foto</div>
    <div class="card-body">
        <!-- enctype multipart OBRIGATÓRIO para upload de arquivos -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="inserir">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Arquivo de imagem <span class="text-danger">*</span></label>
                    <!-- Tamanho máximo: 5MB. Ajuste no php.ini se necessário -->
                    <input type="file" name="arquivo" class="form-control" required accept="image/*">
                    <div class="form-text">Formatos aceitos: JPG, PNG, WebP, GIF. Máximo: 5MB</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Legenda</label>
                    <input type="text" name="titulo" class="form-control" maxlength="255"
                           placeholder="Descrição da foto">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Categoria</label>
                    <input type="text" name="categoria" class="form-control" maxlength="100"
                           placeholder="ex: Cultos, Eventos"
                           list="categorias_lista">
                    <!-- Lista de categorias existentes para autocomplete -->
                    <datalist id="categorias_lista">
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= sanitizar($cat) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Ordem</label>
                    <input type="number" name="ordem" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ativo" id="ativo_gal" checked>
                        <label class="form-check-label fw-semibold" for="ativo_gal">Ativo</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i>Fazer upload
                    </button>
                    <a href="/admin/galeria.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php elseif ($acao === 'editar' && $foto_edicao): ?>
<!-- Formulário de edição (sem alterar arquivo) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Editar Foto</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <img src="<?= sanitizar($foto_edicao['arquivo']) ?>" class="img-fluid rounded" alt="Preview">
            </div>
            <div class="col-md-9">
                <form method="POST">
                    <input type="hidden" name="acao" value="atualizar">
                    <input type="hidden" name="id" value="<?= $foto_edicao['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Legenda</label>
                            <input type="text" name="titulo" class="form-control"
                                   value="<?= sanitizar($foto_edicao['titulo'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoria</label>
                            <input type="text" name="categoria" class="form-control"
                                   value="<?= sanitizar($foto_edicao['categoria'] ?? '') ?>"
                                   list="categorias_lista">
                            <datalist id="categorias_lista">
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= sanitizar($cat) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ordem</label>
                            <input type="number" name="ordem" class="form-control" min="0"
                                   value="<?= (int)$foto_edicao['ordem'] ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo_ed"
                                       <?= $foto_edicao['ativo'] ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="ativo_ed">Ativo</label>
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <a href="/admin/galeria.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Grade de fotos -->
<?php if (empty($fotos)): ?>
<div class="alert alert-info">Nenhuma foto na galeria. Clique em "Adicionar Foto" para começar.</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($fotos as $foto): ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 <?= !$foto['ativo'] ? 'opacity-50' : '' ?>">
            <img src="<?= sanitizar($foto['arquivo']) ?>"
                 class="card-img-top" alt="<?= sanitizar($foto['titulo'] ?? '') ?>"
                 style="height:150px;object-fit:cover;">
            <div class="card-body p-2">
                <p class="small mb-1 text-truncate"><?= sanitizar($foto['titulo'] ?: 'Sem legenda') ?></p>
                <?php if (!empty($foto['categoria'])): ?>
                <span class="badge bg-secondary small"><?= sanitizar($foto['categoria']) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white p-2 d-flex gap-1 justify-content-center">
                <a href="?acao=editar&id=<?= $foto['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="?acao=excluir&id=<?= $foto['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Excluir foto? Esta ação é irreversível.')">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
