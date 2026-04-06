<?php
/**
 * contato.php — Página de Contato
 * Exibe informações de contato e formulário de mensagem.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$titulo_pagina = 'Contato';

// Dados de contato do banco
$site_telefone = obter_config($pdo, 'site_telefone', '');
$site_whatsapp = obter_config($pdo, 'site_whatsapp', '');
$site_email    = obter_config($pdo, 'site_email', '');
$site_endereco = obter_config($pdo, 'site_endereco', '');
$horario_culto = obter_config($pdo, 'site_horario_culto', '');

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Entre em Contato</h1>
        <p class="text-muted mb-5">Estamos aqui para atendê-lo. Envie sua mensagem!</p>

        <!-- Exibe mensagem flash (sucesso ou erro) -->
        <?php exibir_flash(); ?>

        <div class="row g-5">

            <!-- Coluna esquerda: informações de contato -->
            <div class="col-lg-4">
                <h5 class="fw-bold mb-4">Informações</h5>
                <ul class="list-unstyled">
                    <?php if (!empty($site_endereco)): ?>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-geo-alt-fill text-primary fs-5"></i>
                        <div>
                            <strong>Endereço</strong><br>
                            <span class="text-muted small"><?= sanitizar($site_endereco) ?></span>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($site_telefone)): ?>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-telephone-fill text-primary fs-5"></i>
                        <div>
                            <strong>Telefone</strong><br>
                            <span class="text-muted small"><?= sanitizar($site_telefone) ?></span>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($site_whatsapp)): ?>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-whatsapp text-success fs-5"></i>
                        <div>
                            <strong>WhatsApp</strong><br>
                            <a href="https://wa.me/<?= sanitizar($site_whatsapp) ?>" target="_blank"
                               class="text-muted small text-decoration-none">Conversar agora</a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($site_email)): ?>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-envelope-fill text-primary fs-5"></i>
                        <div>
                            <strong>E-mail</strong><br>
                            <a href="mailto:<?= sanitizar($site_email) ?>"
                               class="text-muted small text-decoration-none"><?= sanitizar($site_email) ?></a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($horario_culto)): ?>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-clock-fill text-primary fs-5"></i>
                        <div>
                            <strong>Cultos</strong><br>
                            <span class="text-muted small"><?= sanitizar($horario_culto) ?></span>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Coluna direita: formulário de contato -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-4">Envie uma Mensagem</h5>

                    <form action="/php/enviar_contato.php" method="POST" novalidate>

                        <!-- Campo honeypot anti-spam (oculto por CSS) -->
                        <div style="display:none;" aria-hidden="true">
                            <label for="website">Não preencha este campo</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome"
                                       maxlength="150" required placeholder="Seu nome completo">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       maxlength="150" required placeholder="seu@email.com">
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone"
                                       maxlength="20" placeholder="(00) 00000-0000">
                            </div>
                            <div class="col-md-6">
                                <label for="assunto" class="form-label fw-semibold">Assunto</label>
                                <input type="text" class="form-control" id="assunto" name="assunto"
                                       maxlength="255" placeholder="Assunto da mensagem">
                            </div>
                            <div class="col-12">
                                <label for="mensagem" class="form-label fw-semibold">Mensagem <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="mensagem" name="mensagem"
                                          rows="5" required placeholder="Escreva sua mensagem aqui..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-send me-2"></i>Enviar Mensagem
                                </button>
                            </div>
                        </div><!-- /.row -->
                    </form>
                </div>
            </div>

        </div><!-- /.row -->
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
