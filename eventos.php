<?php
/**
 * eventos.php — Listagem de Eventos
 * Exibe os eventos publicados ordenados por data de início.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/php/crud/eventos_crud.php';

$titulo_pagina = 'Eventos';

// Busca todos os eventos publicados (passados e futuros)
$resultado = eventos_listar($pdo, 1, 50, false, true);
$eventos   = $resultado['dados'];

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Agenda de Eventos</h1>

        <?php if (empty($eventos)): ?>
            <div class="alert alert-info">Nenhum evento cadastrado no momento.</div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($eventos as $evento):
                // Verifica se o evento já passou
                $is_passado = strtotime($evento['data_inicio']) < time();
            ?>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm <?= $is_passado ? 'opacity-60' : '' ?>">
                    <?php if (!empty($evento['imagem'])): ?>
                    <img src="<?= sanitizar($evento['imagem']) ?>"
                         class="card-img-top" alt="<?= sanitizar($evento['titulo']) ?>"
                         style="height:200px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <!-- Bloco de data estilizado -->
                            <div class="text-center bg-primary text-white rounded p-2" style="min-width:56px;">
                                <div class="fs-3 fw-bold lh-1">
                                    <?= date('d', strtotime($evento['data_inicio'])) ?>
                                </div>
                                <div class="small text-uppercase">
                                    <?= date('M', strtotime($evento['data_inicio'])) ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($is_passado): ?>
                                    <span class="badge bg-secondary mb-1">Encerrado</span>
                                <?php endif; ?>
                                <h2 class="h5 fw-semibold mb-1"><?= sanitizar($evento['titulo']) ?></h2>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= formatar_data($evento['data_inicio'], true) ?>
                                    <?php if (!empty($evento['data_fim'])): ?>
                                     &bull; até <?= formatar_data($evento['data_fim'], true) ?>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($evento['local'])): ?>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= sanitizar($evento['local']) ?>
                                </p>
                                <?php endif; ?>
                                <?php if (!empty($evento['descricao'])): ?>
                                <p class="text-muted small mb-0">
                                    <?= sanitizar(truncar($evento['descricao'], 150)) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
