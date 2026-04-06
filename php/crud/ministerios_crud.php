<?php
/**
 * php/crud/ministerios_crud.php
 * CRUD completo para a tabela `ministerios`
 *
 * Requer: $pdo (PDO) — conexão com o banco.
 */

// =============================================================================
// LISTAR
// =============================================================================

/**
 * Lista todos os ministérios, ordenados pela coluna `ordem`.
 *
 * @param PDO  $pdo           Conexão com o banco
 * @param bool $apenas_ativos Se true, retorna apenas os ativos
 * @return array Lista de ministérios
 */
function ministerios_listar(PDO $pdo, bool $apenas_ativos = false): array {
    $where = $apenas_ativos ? 'WHERE ativo = 1' : '';
    $stmt  = $pdo->query("SELECT * FROM ministerios $where ORDER BY ordem ASC, nome ASC");
    return $stmt->fetchAll();
}

// =============================================================================
// BUSCAR POR ID
// =============================================================================

/**
 * Retorna um ministério pelo seu ID.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do ministério
 * @return array|false
 */
function ministerios_buscar_por_id(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare('SELECT * FROM ministerios WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// =============================================================================
// CRIAR
// =============================================================================

/**
 * Insere um novo ministério no banco.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param array $dados Dados do ministério:
 *                     nome, descricao, icone, imagem, responsavel, ordem, ativo
 * @return int ID do registro criado
 */
function ministerios_criar(PDO $pdo, array $dados): int {
    $sql = 'INSERT INTO ministerios (nome, descricao, icone, imagem, responsavel, ordem, ativo)
            VALUES (:nome, :descricao, :icone, :imagem, :responsavel, :ordem, :ativo)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'        => $dados['nome'],
        ':descricao'   => $dados['descricao']  ?? null,
        ':icone'       => $dados['icone']      ?? null,
        ':imagem'      => $dados['imagem']     ?? null,
        ':responsavel' => $dados['responsavel'] ?? null,
        ':ordem'       => $dados['ordem']      ?? 0,
        ':ativo'       => $dados['ativo']      ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

// =============================================================================
// ATUALIZAR
// =============================================================================

/**
 * Atualiza os dados de um ministério.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param int   $id    ID do ministério
 * @param array $dados Novos dados
 * @return int Linhas afetadas
 */
function ministerios_atualizar(PDO $pdo, int $id, array $dados): int {
    $sql = 'UPDATE ministerios SET
                nome        = :nome,
                descricao   = :descricao,
                icone       = :icone,
                imagem      = :imagem,
                responsavel = :responsavel,
                ordem       = :ordem,
                ativo       = :ativo
            WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'        => $dados['nome'],
        ':descricao'   => $dados['descricao']  ?? null,
        ':icone'       => $dados['icone']      ?? null,
        ':imagem'      => $dados['imagem']     ?? null,
        ':responsavel' => $dados['responsavel'] ?? null,
        ':ordem'       => $dados['ordem']      ?? 0,
        ':ativo'       => $dados['ativo']      ?? 1,
        ':id'          => $id,
    ]);

    return $stmt->rowCount();
}

// =============================================================================
// DELETAR
// =============================================================================

/**
 * Remove um ministério do banco.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do ministério
 * @return int Linhas afetadas
 */
function ministerios_deletar(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare('DELETE FROM ministerios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}
