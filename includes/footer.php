<?php
/**
 * includes/footer.php
 * Rodapé HTML reutilizável do site público
 *
 * Inclui: informações de contato, redes sociais, scripts Bootstrap 5 e main.js.
 * Variável esperada: $pdo (PDO) — para carregar dados de configuração.
 */

// Carrega dados do rodapé a partir das configurações do banco
$_endereco  = isset($pdo) ? obter_config($pdo, 'site_endereco', '')  : '';
$_email_rd  = isset($pdo) ? obter_config($pdo, 'site_email', '')     : '';
$_tel       = isset($pdo) ? obter_config($pdo, 'site_telefone', '')  : '';
$_wpp       = isset($pdo) ? obter_config($pdo, 'site_whatsapp', '')  : '';
$_fb        = isset($pdo) ? obter_config($pdo, 'site_facebook', '')  : '';
$_insta     = isset($pdo) ? obter_config($pdo, 'site_instagram', '') : '';
$_yt        = isset($pdo) ? obter_config($pdo, 'site_youtube', '')   : '';
$_ano_atual = date('Y');
?>

<!-- ============================================================
     RODAPÉ DO SITE
     ============================================================ -->
<footer class="bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">

            <!-- Coluna 1: Sobre a Igreja -->
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-book-half me-2 text-primary"></i>PIB Pirajuí
                </h5>
                <p class="text-secondary small">
                    Comprometidos com o Evangelho de Jesus Cristo, servindo à nossa comunidade
                    com amor, fé e esperança.
                </p>
                <!-- Redes sociais -->
                <div class="d-flex gap-3 mt-3">
                    <?php if (!empty($_fb)): ?>
                        <a href="<?= sanitizar($_fb) ?>" target="_blank" class="text-secondary fs-5" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($_insta)): ?>
                        <a href="<?= sanitizar($_insta) ?>" target="_blank" class="text-secondary fs-5" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($_yt)): ?>
                        <a href="<?= sanitizar($_yt) ?>" target="_blank" class="text-secondary fs-5" title="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($_wpp)): ?>
                        <a href="https://wa.me/<?= sanitizar($_wpp) ?>" target="_blank" class="text-secondary fs-5" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coluna 2: Navegação rápida -->
            <div class="col-md-4">
                <h6 class="fw-bold mb-3 text-uppercase text-secondary small">Navegação</h6>
                <ul class="list-unstyled">
                    <li><a href="/"               class="text-secondary text-decoration-none small">Início</a></li>
                    <li><a href="/noticias.php"    class="text-secondary text-decoration-none small">Notícias</a></li>
                    <li><a href="/eventos.php"     class="text-secondary text-decoration-none small">Eventos</a></li>
                    <li><a href="/ministerios.php" class="text-secondary text-decoration-none small">Ministérios</a></li>
                    <li><a href="/contato.php"     class="text-secondary text-decoration-none small">Contato</a></li>
                </ul>
            </div>

            <!-- Coluna 3: Informações de contato -->
            <div class="col-md-4">
                <h6 class="fw-bold mb-3 text-uppercase text-secondary small">Contato</h6>
                <ul class="list-unstyled text-secondary small">
                    <?php if (!empty($_endereco)): ?>
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                        <?= sanitizar($_endereco) ?>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($_tel)): ?>
                    <li class="mb-2">
                        <i class="bi bi-telephone-fill me-2 text-primary"></i>
                        <?= sanitizar($_tel) ?>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($_email_rd)): ?>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill me-2 text-primary"></i>
                        <a href="mailto:<?= sanitizar($_email_rd) ?>" class="text-secondary">
                            <?= sanitizar($_email_rd) ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div><!-- /.row -->

        <hr class="border-secondary mt-4">

        <!-- Copyright -->
        <div class="text-center text-secondary small">
            &copy; <?= $_ano_atual ?> Primeira Igreja Batista de Pirajuí. Todos os direitos reservados.
            &nbsp;|&nbsp;
            <a href="/admin/login.php" class="text-secondary text-decoration-none">Área Administrativa</a>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (inclui Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript próprio do site -->
<script src="/js/main.js"></script>

</body>
</html>
