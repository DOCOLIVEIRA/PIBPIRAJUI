<?php
/**
 * noticia.php — Página de Detalhe de Notícia
 * Exibe o conteúdo completo de uma notícia buscada pelo slug.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/php/crud/noticias_crud.php';

// Lê o slug da URL (parâmetro GET "slug")
$slug = trim($_GET['slug'] ?? '');

// Se não tem slug, redireciona para a listagem
if (empty($slug)) {
    redirecionar('/noticias.php');
}

// Busca a notícia pelo slug (apenas publicadas)
$noticia = noticias_buscar_por_slug($pdo, $slug);

// Se não encontrou, redireciona
if (!$noticia) {
    redirecionar('/noticias.php');
}

$titulo_pagina = $noticia['titulo'];

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Início</a></li>
                        <li class="breadcrumb-item"><a href="/noticias.php">Notícias</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= sanitizar(truncar($noticia['titulo'], 50)) ?>
                        </li>
                    </ol>
                </nav>

                <!-- Artigo -->
                <article>
                    <!-- Imagem de capa -->
                    <?php if (!empty($noticia['imagem'])): ?>
                    <img src="<?= sanitizar($noticia['imagem']) ?>"
                         class="img-fluid rounded mb-4 w-100"
                         alt="<?= sanitizar($noticia['titulo']) ?>"
                         style="max-height:400px;object-fit:cover;">
                    <?php endif; ?>

                    <!-- Metadados -->
                    <p class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        Publicado em <?= formatar_data($noticia['publicado_em'], true) ?>
                        <?php if (!empty($noticia['autor_nome'])): ?>
                         &bull; por <strong><?= sanitizar($noticia['autor_nome']) ?></strong>
                        <?php endif; ?>
                    </p>

                    <!-- Título -->
                    <h1 class="fw-bold mb-3"><?= sanitizar($noticia['titulo']) ?></h1>

                    <!-- Conteúdo completo — allow_html pois é conteúdo editorial salvo pelo admin -->
                    <div class="article-content lh-lg">
                        <?= $noticia['conteudo'] ?>
                    </div>
                </article>

                <!-- Botão voltar -->
                <div class="mt-5">
                    <a href="/noticias.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para notícias
                    </a>
                </div>

            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
