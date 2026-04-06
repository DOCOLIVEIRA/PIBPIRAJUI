<?php
/**
 * index.php — Página Inicial do Site Público
 * Exibe um hero, últimas notícias, próximos eventos e ministérios.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/php/crud/noticias_crud.php';
require_once __DIR__ . '/php/crud/eventos_crud.php';
require_once __DIR__ . '/php/crud/ministerios_crud.php';

// Título da aba
$titulo_pagina = 'Início';

// Busca dados para a página inicial
$ultimas_noticias  = noticias_listar($pdo, 1, 3, true)['dados'];      // 3 notícias publicadas
$proximos_eventos  = eventos_listar($pdo, 1, 4, true, true)['dados']; // 4 eventos futuros
$ministerios_lista = ministerios_listar($pdo, true);                   // Ministérios ativos

// Configurações do site
$site_whatsapp = obter_config($pdo, 'site_whatsapp', '');
$horario_culto = obter_config($pdo, 'site_horario_culto', '');

// Inclui o cabeçalho HTML
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     SEÇÃO HERO — Banner principal com chamada para ação
     ============================================================ -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary mb-3">Bem-vindo</span>
                <h1 class="display-4 fw-bold mb-3">
                    Primeira Igreja Batista de Pirajuí
                </h1>
                <p class="lead mb-4 opacity-75">
                    Comprometidos com o Evangelho de Jesus Cristo,<br>
                    servindo à nossa comunidade com amor, fé e esperança.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/contato.php" class="btn btn-light btn-lg fw-semibold">
                        <i class="bi bi-geo-alt me-2"></i>Como chegar
                    </a>
                    <?php if (!empty($site_whatsapp)): ?>
                    <a href="https://wa.me/<?= sanitizar($site_whatsapp) ?>" target="_blank"
                       class="btn btn-outline-light btn-lg">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
                <?php if (!empty($horario_culto)): ?>
                <p class="mt-4 mb-0 small opacity-75">
                    <i class="bi bi-clock me-1"></i> <?= sanitizar($horario_culto) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SEÇÃO NOTÍCIAS — Últimas 3 notícias publicadas
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Últimas Notícias</h2>
            <a href="/noticias.php" class="btn btn-outline-primary btn-sm">Ver todas</a>
        </div>

        <?php if (empty($ultimas_noticias)): ?>
            <p class="text-muted">Nenhuma notícia publicada ainda.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ultimas_noticias as $noticia): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <?php if (!empty($noticia['imagem'])): ?>
                    <img src="<?= sanitizar($noticia['imagem']) ?>"
                         class="card-img-top" alt="<?= sanitizar($noticia['titulo']) ?>"
                         style="height:200px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <p class="text-muted small mb-1">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= formatar_data($noticia['publicado_em']) ?>
                        </p>
                        <h5 class="card-title fw-semibold"><?= sanitizar($noticia['titulo']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= sanitizar(truncar($noticia['resumo'] ?? $noticia['conteudo'], 120)) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="/noticia.php?slug=<?= urlencode($noticia['slug']) ?>"
                           class="btn btn-primary btn-sm">Ler mais &rarr;</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     SEÇÃO EVENTOS — Próximos eventos
     ============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Próximos Eventos</h2>
            <a href="/eventos.php" class="btn btn-outline-primary btn-sm">Ver agenda</a>
        </div>

        <?php if (empty($proximos_eventos)): ?>
            <p class="text-muted">Nenhum evento programado no momento.</p>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($proximos_eventos as $evento): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-6 text-primary fw-bold mb-1">
                            <?= date('d', strtotime($evento['data_inicio'])) ?>
                        </div>
                        <div class="text-muted small text-uppercase">
                            <?= date('M', strtotime($evento['data_inicio'])) ?>
                        </div>
                        <hr class="my-2">
                        <h6 class="fw-semibold"><?= sanitizar($evento['titulo']) ?></h6>
                        <?php if (!empty($evento['local'])): ?>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-geo-alt me-1"></i><?= sanitizar($evento['local']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     SEÇÃO MINISTÉRIOS
     ============================================================ -->
<?php if (!empty($ministerios_lista)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">Nossos Ministérios</h2>
        <div class="row g-3">
            <?php foreach ($ministerios_lista as $min): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-3">
                    <div class="card-body">
                        <i class="<?= sanitizar($min['icone'] ?: 'bi-star') ?> display-5 text-primary mb-3 d-block"></i>
                        <h6 class="fw-bold"><?= sanitizar($min['nome']) ?></h6>
                        <?php if (!empty($min['descricao'])): ?>
                        <p class="text-muted small"><?= sanitizar(truncar($min['descricao'], 80)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/ministerios.php" class="btn btn-outline-primary">Ver todos os ministérios</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
