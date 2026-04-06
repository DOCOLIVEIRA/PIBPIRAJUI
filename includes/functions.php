<?php
/**
 * includes/functions.php
 * Funções auxiliares reutilizáveis em todo o projeto
 *
 * Inclua este arquivo junto com db.php nas páginas que precisarem dessas funções.
 */

// Inicia a sessão PHP caso ainda não tenha sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================================================
// SEGURANÇA E SANITIZAÇÃO
// =============================================================================

/**
 * Sanitiza uma string para exibição segura no HTML.
 * Previne ataques XSS (Cross-Site Scripting).
 *
 * @param string $dado O valor a ser sanitizado
 * @return string Valor seguro para exibição em HTML
 */
function sanitizar(string $dado): string {
    return htmlspecialchars(trim($dado), ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica se o usuário está logado no painel administrativo.
 * Redireciona para a página de login se não estiver autenticado.
 *
 * @return void
 */
function verificar_autenticacao(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) {
        // Salva a URL atual para redirecionar após o login
        $_SESSION['redirect_apos_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /admin/login.php');
        exit;
    }
}

// =============================================================================
// REDIRECIONAMENTO E MENSAGENS FLASH
// =============================================================================

/**
 * Redireciona o usuário para uma URL especificada.
 *
 * @param string $url URL de destino
 * @return void
 */
function redirecionar(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Define uma mensagem flash para ser exibida na próxima requisição.
 * Muito útil após operações CRUD (ex: "Notícia salva com sucesso!").
 *
 * @param string $mensagem Texto da mensagem
 * @param string $tipo     Tipo Bootstrap: 'success', 'danger', 'warning', 'info'
 * @return void
 */
function definir_flash(string $mensagem, string $tipo = 'success'): void {
    $_SESSION['flash'] = [
        'mensagem' => $mensagem,
        'tipo'     => $tipo,
    ];
}

/**
 * Exibe e limpa a mensagem flash da sessão.
 * Chame esta função nos templates onde deseja exibir alertas.
 *
 * @return void
 */
function exibir_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // Remove após exibir (one-time message)
        echo '<div class="alert alert-' . sanitizar($flash['tipo']) . ' alert-dismissible fade show" role="alert">';
        echo sanitizar($flash['mensagem']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

// =============================================================================
// FORMATAÇÃO
// =============================================================================

/**
 * Formata uma data do formato MySQL (YYYY-MM-DD ou YYYY-MM-DD HH:II:SS)
 * para o formato brasileiro (DD/MM/YYYY ou DD/MM/YYYY HH:II).
 *
 * @param string|null $data_mysql Data no formato MySQL
 * @param bool        $incluir_hora Se true, inclui hora:minuto
 * @return string Data formatada ou traço se nula
 */
function formatar_data(?string $data_mysql, bool $incluir_hora = false): string {
    if (empty($data_mysql)) return '—';
    $formato = $incluir_hora ? 'd/m/Y H:i' : 'd/m/Y';
    return date($formato, strtotime($data_mysql));
}

/**
 * Gera um slug URL-amigável a partir de um texto.
 * Exemplo: "Culto de Natal 2025" → "culto-de-natal-2025"
 *
 * @param string $texto Texto original
 * @return string Slug gerado
 */
function gerar_slug(string $texto): string {
    // Converte para minúsculas e remove acentos
    $texto = strtolower($texto);
    $mapa = [
        'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ç'=>'c','ñ'=>'n',
    ];
    $texto = strtr($texto, $mapa);
    // Substitui tudo que não for letra ou número por hífen
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    // Remove hífens extras no início e fim
    return trim($texto, '-');
}

/**
 * Recupera o valor de uma configuração do site diretamente pelo banco.
 * Usa cache em variável estática para evitar múltiplas consultas.
 *
 * @param PDO    $pdo   Conexão com o banco de dados
 * @param string $chave Nome da configuração
 * @param string $padrao Valor retornado se chave não existir
 * @return string
 */
function obter_config(PDO $pdo, string $chave, string $padrao = ''): string {
    static $cache = [];
    if (!isset($cache[$chave])) {
        $stmt = $pdo->prepare('SELECT valor FROM configuracoes WHERE chave = ? LIMIT 1');
        $stmt->execute([$chave]);
        $resultado = $stmt->fetchColumn();
        $cache[$chave] = ($resultado !== false) ? $resultado : $padrao;
    }
    return $cache[$chave];
}

/**
 * Trunca um texto longo adicionando reticências no final.
 *
 * @param string $texto     Texto original
 * @param int    $limite    Número máximo de caracteres
 * @param string $sufixo    Sufixo a adicionar (padrão: "...")
 * @return string
 */
function truncar(string $texto, int $limite = 150, string $sufixo = '...'): string {
    $texto = strip_tags($texto); // Remove tags HTML antes de contar
    if (mb_strlen($texto) <= $limite) return $texto;
    return mb_substr($texto, 0, $limite) . $sufixo;
}

/**
 * Verifica se uma string é um e-mail válido.
 *
 * @param string $email
 * @return bool
 */
function email_valido(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Obtém o IP real do visitante, considerando proxies.
 *
 * @return string
 */
function obter_ip(): string {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $chave) {
        if (!empty($_SERVER[$chave])) {
            return filter_var($_SERVER[$chave], FILTER_VALIDATE_IP) ?: 'desconhecido';
        }
    }
    return 'desconhecido';
}
