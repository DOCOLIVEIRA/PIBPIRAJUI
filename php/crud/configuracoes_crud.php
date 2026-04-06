<?php
/**
 * php/crud/configuracoes_crud.php
 * CRUD para a tabela `configuracoes` (sistema chave-valor)
 *
 * Requer: $pdo (PDO) — conexão com o banco.
 */

// =============================================================================
// LISTAR TODAS
// =============================================================================

/**
 * Retorna todas as configurações do site como array associativo
 * no formato ['chave' => 'valor', ...].
 *
 * @param PDO $pdo Conexão com o banco
 * @return array
 */
function configuracoes_listar_todas(PDO $pdo): array {
    $stmt = $pdo->query('SELECT chave, valor, descricao FROM configuracoes ORDER BY chave ASC');
    $resultado = [];
    foreach ($stmt->fetchAll() as $linha) {
        // Indexa pelo nome da chave para acesso fácil: $configs['site_nome']
        $resultado[$linha['chave']] = $linha;
    }
    return $resultado;
}

// =============================================================================
// BUSCAR POR CHAVE
// =============================================================================

/**
 * Retorna o valor de uma configuração específica pela chave.
 *
 * @param PDO    $pdo    Conexão com o banco
 * @param string $chave  Nome da configuração
 * @param string $padrao Valor padrão se a chave não existir
 * @return string
 */
function configuracoes_obter(PDO $pdo, string $chave, string $padrao = ''): string {
    $stmt = $pdo->prepare('SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1');
    $stmt->execute([':chave' => $chave]);
    $valor = $stmt->fetchColumn();
    return ($valor !== false) ? (string) $valor : $padrao;
}

// =============================================================================
// ATUALIZAR — Salva um valor de configuração
// =============================================================================

/**
 * Atualiza o valor de uma configuração existente (pelo nome da chave).
 * Usa INSERT ... ON DUPLICATE KEY UPDATE para upsert seguro.
 *
 * @param PDO    $pdo   Conexão com o banco
 * @param string $chave Nome da configuração
 * @param string $valor Novo valor
 * @return void
 */
function configuracoes_salvar(PDO $pdo, string $chave, string $valor): void {
    $sql = 'INSERT INTO configuracoes (chave, valor)
            VALUES (:chave, :valor)
            ON DUPLICATE KEY UPDATE valor = :valor2';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':chave'  => $chave,
        ':valor'  => $valor,
        ':valor2' => $valor,
    ]);
}

/**
 * Salva múltiplas configurações de uma vez (útil ao salvar formulário do painel).
 * Recebe um array associativo: ['chave' => 'valor', ...]
 *
 * @param PDO   $pdo    Conexão com o banco
 * @param array $configs Array no formato ['chave' => 'novo_valor']
 * @return void
 */
function configuracoes_salvar_multiplas(PDO $pdo, array $configs): void {
    foreach ($configs as $chave => $valor) {
        configuracoes_salvar($pdo, (string) $chave, (string) $valor);
    }
}

// =============================================================================
// CRIAR (além das iniciais) — Insere nova configuração
// =============================================================================

/**
 * Insere uma nova entrada de configuração.
 * Se a chave já existir, atualiza o valor (comportamento de upsert).
 *
 * @param PDO    $pdo      Conexão com o banco
 * @param string $chave    Nome único da configuração
 * @param string $valor    Valor inicial
 * @param string $descricao Descrição amigável da configuração
 * @return void
 */
function configuracoes_criar(PDO $pdo, string $chave, string $valor, string $descricao = ''): void {
    $sql = 'INSERT INTO configuracoes (chave, valor, descricao)
            VALUES (:chave, :valor, :descricao)
            ON DUPLICATE KEY UPDATE valor = :valor2';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':chave'     => $chave,
        ':valor'     => $valor,
        ':descricao' => $descricao,
        ':valor2'    => $valor,
    ]);
}

// =============================================================================
// DELETAR — Remove uma configuração
// =============================================================================

/**
 * Remove uma configuração do banco pelo nome da chave.
 * CUIDADO: configurações padrão do sistema não devem ser removidas.
 *
 * @param PDO    $pdo   Conexão com o banco
 * @param string $chave Nome da configuração
 * @return int Linhas afetadas
 */
function configuracoes_deletar(PDO $pdo, string $chave): int {
    $stmt = $pdo->prepare('DELETE FROM configuracoes WHERE chave = :chave');
    $stmt->execute([':chave' => $chave]);
    return $stmt->rowCount();
}
