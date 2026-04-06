<?php
/**
 * admin/configuracoes.php — Configurações do Site
 * Exibe e salva as configurações gerais da tabela `configuracoes`.
 * Acessível apenas a usuários com nível 'admin'.
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../php/crud/configuracoes_crud.php';

// Apenas administradores podem acessar configurações
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    definir_flash('Acesso negado. Apenas administradores.', 'danger');
    redirecionar('/admin/index.php');
}

$titulo_admin = 'Configurações';

// Salva as configurações quando o formulário é submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lista das chaves permitidas para edição (evita que campos arbitrários sejam salvos)
    $chaves_permitidas = [
        'site_nome', 'site_nome_curto', 'site_email', 'site_telefone',
        'site_whatsapp', 'site_endereco', 'site_horario_culto',
        'site_facebook', 'site_instagram', 'site_youtube', 'site_descricao_meta',
    ];

    foreach ($chaves_permitidas as $chave) {
        if (isset($_POST[$chave])) {
            configuracoes_salvar($pdo, $chave, trim($_POST[$chave]));
        }
    }

    definir_flash('Configurações salvas com sucesso!', 'success');
    redirecionar('/admin/configuracoes.php');
}

// Carrega todas as configurações atuais
$configs = configuracoes_listar_todas($pdo);

// Função auxiliar para obter o valor atual de uma chave
$val = fn($chave) => sanitizar($configs[$chave]['valor'] ?? '');

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php exibir_flash(); ?>

<div class="mb-4">
    <h2 class="h4 fw-bold mb-0">Configurações do Site</h2>
    <p class="text-muted small">Essas informações aparecem no rodapé, no header e no SEO do site.</p>
</div>

<form method="POST">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Informações Gerais</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nome completo do site</label>
                    <input type="text" name="site_nome" class="form-control" value="<?= $val('site_nome') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nome abreviado (header)</label>
                    <input type="text" name="site_nome_curto" class="form-control" value="<?= $val('site_nome_curto') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição para SEO (meta description)</label>
                    <textarea name="site_descricao_meta" class="form-control" rows="2"><?= $val('site_descricao_meta') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Horários dos cultos</label>
                    <input type="text" name="site_horario_culto" class="form-control" value="<?= $val('site_horario_culto') ?>"
                           placeholder="Domingos às 9h e 19h | Quartas às 19h30">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Endereço</label>
                    <input type="text" name="site_endereco" class="form-control" value="<?= $val('site_endereco') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Contato</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">E-mail de contato</label>
                    <input type="email" name="site_email" class="form-control" value="<?= $val('site_email') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Telefone</label>
                    <input type="text" name="site_telefone" class="form-control" value="<?= $val('site_telefone') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">WhatsApp (só números com DDI)</label>
                    <input type="text" name="site_whatsapp" class="form-control" value="<?= $val('site_whatsapp') ?>"
                           placeholder="5514999999999">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Redes Sociais</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-facebook me-1"></i>Facebook</label>
                    <input type="url" name="site_facebook" class="form-control" value="<?= $val('site_facebook') ?>"
                           placeholder="https://facebook.com/sua-pagina">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-instagram me-1"></i>Instagram</label>
                    <input type="url" name="site_instagram" class="form-control" value="<?= $val('site_instagram') ?>"
                           placeholder="https://instagram.com/seu-perfil">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-youtube me-1"></i>YouTube</label>
                    <input type="url" name="site_youtube" class="form-control" value="<?= $val('site_youtube') ?>"
                           placeholder="https://youtube.com/seu-canal">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg">
        <i class="bi bi-check-lg me-2"></i>Salvar Configurações
    </button>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
