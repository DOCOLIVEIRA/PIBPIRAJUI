/**
 * js/main.js
 * JavaScript do site público PIB Pirajuí
 * Interações leves — sem dependências além do Bootstrap 5
 */

/* ==========================================================================
   1. MÁSCARA DE TELEFONE — Aplica formatação automática no campo do formulário
   ========================================================================== */

/**
 * Aplica máscara de telefone: (00) 00000-0000 ou (00) 0000-0000
 * Chamada no evento 'input' do campo.
 *
 * @param {HTMLInputElement} campo - O campo de telefone a ser formatado
 */
function mascaraTelefone(campo) {
    // Remove tudo que não for dígito
    let v = campo.value.replace(/\D/g, '');

    if (v.length > 11) v = v.substring(0, 11);

    if (v.length > 10) {
        // Celular: (99) 99999-9999
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (v.length > 6) {
        // Fixo: (99) 9999-9999
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
    } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
    } else if (v.length > 0) {
        v = v.replace(/^(\d{0,2})$/, '($1');
    }

    campo.value = v;
}

/* ==========================================================================
   2. NAVBAR — Adiciona sombra ao rolar a página
   ========================================================================== */
function atualizarNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    if (window.scrollY > 50) {
        navbar.classList.add('shadow');
    } else {
        navbar.classList.remove('shadow');
    }
}

/* ==========================================================================
   3. INICIALIZAÇÃO — Aguarda o DOM estar pronto
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

    // Aplica máscara ao campo de telefone do formulário de contato
    const campoTelefone = document.getElementById('telefone');
    if (campoTelefone) {
        campoTelefone.addEventListener('input', function () {
            mascaraTelefone(this);
        });
    }

    // Navbar com sombra ao rolar
    window.addEventListener('scroll', atualizarNavbar, { passive: true });

    // Tooltips Bootstrap — ativa todos os elementos com data-bs-toggle="tooltip"
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // Destaca o link ativo na navbar comparando com a URL atual
    const paginaAtual = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        if (link.getAttribute('href') === paginaAtual) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
});
