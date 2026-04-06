<?php
/**
 * admin/usuarios.php — Gestão de Usuários do Painel
 * Permite criar, editar (dados básicos), redefinir senha e excluir.
 * Exclusivo para nível 'admin'.
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/usuarios_crud.php';

// Apenas admins podem gerenciar usuários
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    definir_flash('Acesso negado.', 'danger');
    redirecionar('/admin/index.php');
}

$titulo_admin = 'Usuários';
$acao = $_GET['acao'] ?? 'listar';
$id   = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_post = $_POST['acao'] ?? '';

    if ($acao_post === 'inserir') {
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $nivel = $_POST['nivel'] ?? 'editor';
        if ($nome && $email && $senha) {
            try {
                usuarios_criar($pdo, $nome, $email, $senha, $nivel);
                definir_flash('Usuário criado com sucesso!', 'success');
            } catch (PDOException $e) {
                definir_flash('Erro: e-mail já cadastrado ou dados inválidos.', 'danger');
            }
        } else {
            definir_flash('Preencha todos os campos obrigatórios.', 'warning');
        }
        redirecionar('/admin/usuarios.php');
    }

    if ($acao_post === 'atualizar') {
        $id_upd = (int)($_POST['id'] ?? 0);
        usuarios_atualizar(
            $pdo,
            $id_upd,
            trim($_POST['nome'] ?? ''),
            trim($_POST['email'] ?? ''),
            $_POST['nivel'] ?? 'editor',
            isset($_POST['ativo']) ? 1 : 0
        );
        // Atualiza senha apenas se preenchida
        if (!empty($_POST['nova_senha'])) {
            usuarios_atualizar_senha($pdo, $id_upd, $_POST['nova_senha']);
        }
        definir_flash('Usuário atualizado!', 'success');
        redirecionar('/admin/usuarios.php');
    }
}

if ($acao === 'excluir' && $id > 0) {
    // Impede que o admin corrente se auto-exclua
    if ($id === (int)$_SESSION['admin_id']) {
        definir_flash('Você não pode excluir seu próprio usuário.', 'warning');
    } else {
        usuarios_deletar($pdo, $id);
        definir_flash('Usuário excluído.', 'success');
    }
    redirecionar('/admin/usuarios.php');
}

$usuario_edicao = null;
if ($acao === 'editar' && $id > 0) {
    $usuario_edicao = usuarios_buscar_por_id($pdo, $id);
}

$usuarios = usuarios_listar($pdo);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">Usuários</h2>
    <a href="?acao=novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Usuário</a>
</div>

<?php if ($acao === 'novo' || $acao === 'editar'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <?= $acao === 'editar' ? 'Editar Usuário' : 'Novo Usuário' ?>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $acao === 'editar' ? 'atualizar' : 'inserir' ?>">
            <?php if ($usuario_edicao): ?>
            <input type="hidden" name="id" value="<?= $usuario_edicao['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" required maxlength="100"
                           value="<?= sanitizar($usuario_edicao['nome'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required maxlength="150"
                           value="<?= sanitizar($usuario_edicao['email'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <?= $acao === 'editar' ? 'Nova Senha (deixe em branco para manter)' : 'Senha *' ?>
                    </label>
                    <input type="password" name="<?= $acao === 'editar' ? 'nova_senha' : 'senha' ?>"
                           class="form-control" <?= $acao !== 'editar' ? 'required' : '' ?>
                           autocomplete="new-password" minlength="8">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nível de acesso</label>
                    <select name="nivel" class="form-select">
                        <option value="editor" <?= (($usuario_edicao['nivel'] ?? '') === 'editor') ? 'selected' : '' ?>>Editor</option>
                        <option value="admin"  <?= (($usuario_edicao['nivel'] ?? '') === 'admin')  ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <?php if ($acao === 'editar'): ?>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ativo" id="ativo_usr"
                               <?= (($usuario_edicao['ativo'] ?? 1) == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="ativo_usr">Usuário ativo</label>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $acao === 'editar' ? 'Salvar alterações' : 'Criar usuário' ?>
                    </button>
                    <a href="/admin/usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
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
                <tr><th>Nome</th><th>E-mail</th><th>Nível</th><th>Status</th><th>Criado em</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum usuário.</td></tr>
                <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= sanitizar($u['nome']) ?></td>
                    <td class="text-muted small"><?= sanitizar($u['email']) ?></td>
                    <td><span class="badge <?= $u['nivel'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>"><?= $u['nivel'] ?></span></td>
                    <td><span class="badge <?= $u['ativo'] ? 'bg-success' : 'bg-danger' ?>"><?= $u['ativo'] ? 'Ativo' : 'Bloqueado' ?></span></td>
                    <td class="text-muted small"><?= formatar_data($u['criado_em']) ?></td>
                    <td>
                        <a href="?acao=editar&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] !== (int)$_SESSION['admin_id']): ?>
                        <a href="?acao=excluir&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Excluir usuário <?= sanitizar($u['nome']) ?>?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
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
