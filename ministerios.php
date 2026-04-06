<?php
/**
 * ministerios.php — Página de Ministérios
 * Lista todos os ministérios ativos da igreja.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/php/crud/ministerios_crud.php';

$titulo_pagina  = 'Ministérios';
$ministerios    = ministerios_listar($pdo, true); // Apenas ativos

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Nossos Ministérios</h1>
        <p class="text-muted mb-5">Conheça os departamentos e grupos de trabalho da nossa igreja.</p>

        <?php if (empty($ministerios)): ?>
            <div class="alert alert-info">Nenhum ministério cadastrado.</div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ministerios as $min): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if (!empty($min['imagem'])): ?>
                    <img src="<?= sanitizar($min['imagem']) ?>"
                         class="card-img-top" alt="<?= sanitizar($min['nome']) ?>"
                         style="height:200px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <i class="<?= sanitizar($min['icone'] ?: 'bi-star') ?> fs-1 text-primary mb-3 d-block"></i>
                        <h2 class="h5 fw-bold"><?= sanitizar($min['nome']) ?></h2>
                        <?php if (!empty($min['descricao'])): ?>
                        <p class="text-muted small"><?= sanitizar($min['descricao']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($min['responsavel'])): ?>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-person-fill me-1"></i>
                            Responsável: <strong><?= sanitizar($min['responsavel']) ?></strong>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
