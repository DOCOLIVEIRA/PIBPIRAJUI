<?php
/**
 * admin/index.php — Dashboard do Painel Administrativo
 * Exibe contadores de registros e acesso rápido às seções.
 */

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$titulo_admin = 'Dashboard';

// -----------------------------------------------------------------------
// Contadores para os cards de resumo
// -----------------------------------------------------------------------

// Total de notícias publicadas
$total_noticias  = (int) $pdo->query('SELECT COUNT(*) FROM noticias  WHERE publicado = 1')->fetchColumn();

// Total de eventos futuros
$total_eventos   = (int) $pdo->query('SELECT COUNT(*) FROM eventos   WHERE publicado = 1 AND data_inicio >= NOW()')->fetchColumn();

// Total de contatos não lidos
$total_contatos  = (int) $pdo->query('SELECT COUNT(*) FROM contatos  WHERE lido = 0')->fetchColumn();

// Total de ministérios ativos
$total_ministerios = (int) $pdo->query('SELECT COUNT(*) FROM ministerios WHERE ativo = 1')->fetchColumn();

// Últimas 5 mensagens de contato não lidas
$stmt_contatos = $pdo->query('SELECT id, nome, email, assunto, criado_em FROM contatos WHERE lido = 0 ORDER BY criado_em DESC LIMIT 5');
$contatos_recentes = $stmt_contatos->fetchAll();

// Últimas 5 notícias criadas
$stmt_noticias = $pdo->query('SELECT id, titulo, publicado, criado_em FROM noticias ORDER BY criado_em DESC LIMIT 5');
$noticias_recentes = $stmt_noticias->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<!-- Mensagens flash -->
<?php exibir_flash(); ?>

<!-- Cards de resumo -->
<div class="row g-4 mb-4">
    <!-- Card Notícias -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-newspaper text-primary fs-3"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $total_noticias ?></div>
                    <div class="text-muted small">Notícias publicadas</div>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="/admin/noticias.php" class="btn btn-sm btn-outline-primary w-100">Gerenciar</a>
            </div>
        </div>
    </div>

    <!-- Card Eventos -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-calendar-event text-success fs-3"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $total_eventos ?></div>
                    <div class="text-muted small">Eventos futuros</div>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="/admin/eventos.php" class="btn btn-sm btn-outline-success w-100">Gerenciar</a>
            </div>
        </div>
    </div>

    <!-- Card Contatos -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-envelope text-warning fs-3"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $total_contatos ?></div>
                    <div class="text-muted small">Mensagens não lidas</div>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="/admin/contatos.php" class="btn btn-sm btn-outline-warning w-100">Ver mensagens</a>
            </div>
        </div>
    </div>

    <!-- Card Ministérios -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="bi bi-people text-info fs-3"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $total_ministerios ?></div>
                    <div class="text-muted small">Ministérios ativos</div>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <a href="/admin/ministerios.php" class="btn btn-sm btn-outline-info w-100">Gerenciar</a>
            </div>
        </div>
    </div>
</div>

<!-- Atividade recente -->
<div class="row g-4">
    <!-- Tabela de mensagens não lidas -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                Mensagens Recentes
                <a href="/admin/contatos.php" class="btn btn-sm btn-outline-secondary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($contatos_recentes)): ?>
                <p class="p-3 text-muted small mb-0">Nenhuma mensagem não lida.</p>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($contatos_recentes as $c): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong class="small"><?= sanitizar($c['nome']) ?></strong>
                                <br><span class="text-muted small"><?= sanitizar($c['assunto'] ?: 'Sem assunto') ?></span>
                            </div>
                            <small class="text-muted"><?= formatar_data($c['criado_em']) ?></small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabela de notícias recentes -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                Notícias Recentes
                <a href="/admin/noticias.php" class="btn btn-sm btn-outline-secondary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($noticias_recentes)): ?>
                <p class="p-3 text-muted small mb-0">Nenhuma notícia cadastrada.</p>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($noticias_recentes as $n): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="small"><?= sanitizar(truncar($n['titulo'], 50)) ?></span>
                        <span class="badge <?= $n['publicado'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $n['publicado'] ? 'Publicada' : 'Rascunho' ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
