<?php
/**
 * noticias.php — Listagem de Notícias
 * Exibe notícias publicadas com paginação.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/php/crud/noticias_crud.php';

$titulo_pagina = 'Notícias';

// Paginação: lê o parâmetro "pagina" da URL, mínimo 1
$pagina_atual = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina   = 9;

// Busca notícias publicadas
$resultado = noticias_listar($pdo, $pagina_atual, $por_pagina, true);
$noticias  = $resultado['dados'];
$total     = $resultado['total'];

// Calcula total de páginas para a paginação
$total_paginas = (int) ceil($total / $por_pagina);

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Notícias</h1>

        <?php if (empty($noticias)): ?>
            <div class="alert alert-info">Nenhuma notícia publicada ainda.</div>
        <?php else: ?>

        <!-- Grade de notícias -->
        <div class="row g-4">
            <?php foreach ($noticias as $noticia): ?>
            <div class="col-md-6 col-lg-4">
                <article class="card h-100 shadow-sm border-0">
                    <?php if (!empty($noticia['imagem'])): ?>
                    <img src="<?= sanitizar($noticia['imagem']) ?>"
                         class="card-img-top" alt="<?= sanitizar($noticia['titulo']) ?>"
                         style="height:200px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted small mb-1">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= formatar_data($noticia['publicado_em']) ?>
                            <?php if (!empty($noticia['autor_nome'])): ?>
                             &bull; <?= sanitizar($noticia['autor_nome']) ?>
                            <?php endif; ?>
                        </p>
                        <h2 class="h5 card-title fw-semibold"><?= sanitizar($noticia['titulo']) ?></h2>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= sanitizar(truncar($noticia['resumo'] ?? $noticia['conteudo'], 130)) ?>
                        </p>
                        <a href="/noticia.php?slug=<?= urlencode($noticia['slug']) ?>"
                           class="btn btn-primary btn-sm mt-2 align-self-start">Ler mais &rarr;</a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_paginas > 1): ?>
        <nav class="mt-5" aria-label="Paginação de notícias">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($pagina_atual <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                <li class="page-item <?= ($p === $pagina_atual) ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $p ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= ($pagina_atual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>">
                        Próxima <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
