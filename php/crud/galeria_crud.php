<?php
/**
 * php/crud/galeria_crud.php
 * CRUD completo para a tabela `galeria`
 *
 * Requer: $pdo (PDO) — conexão com o banco.
 */

// =============================================================================
// LISTAR
// =============================================================================

/**
 * Lista fotos da galeria com suporte a filtro por categoria e paginação.
 *
 * @param PDO         $pdo           Conexão com o banco
 * @param int         $pagina        Página atual
 * @param int         $por_pagina    Itens por página
 * @param string|null $categoria     Filtro por categoria (null = todas)
 * @param bool        $apenas_ativos Retorna apenas fotos ativas
 * @return array ['dados' => [...], 'total' => int]
 */
function galeria_listar(
    PDO     $pdo,
    int     $pagina        = 1,
    int     $por_pagina    = 20,
    ?string $categoria     = null,
    bool    $apenas_ativos = false
): array {
    $offset     = ($pagina - 1) * $por_pagina;
    $condicoes  = [];
    $parametros = [];

    if ($apenas_ativos) {
        $condicoes[] = 'ativo = 1';
    }
    if ($categoria !== null) {
        $condicoes[] = 'categoria = :categoria';
        $parametros[':categoria'] = $categoria;
    }

    $where = count($condicoes) ? 'WHERE ' . implode(' AND ', $condicoes) : '';

    // Conta total
    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM galeria $where");
    $stmt_total->execute($parametros);
    $total = (int) $stmt_total->fetchColumn();

    // Busca registros
    $sql = "SELECT * FROM galeria $where ORDER BY ordem ASC, criado_em DESC LIMIT :limite OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($parametros as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
    $stmt->execute();

    return ['dados' => $stmt->fetchAll(), 'total' => $total];
}

/**
 * Retorna lista de categorias únicas da galeria.
 *
 * @param PDO $pdo Conexão com o banco
 * @return array Lista de strings com as categorias
 */
function galeria_listar_categorias(PDO $pdo): array {
    $stmt = $pdo->query('SELECT DISTINCT categoria FROM galeria WHERE categoria IS NOT NULL ORDER BY categoria ASC');
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// =============================================================================
// BUSCAR POR ID
// =============================================================================

/**
 * Retorna uma foto da galeria pelo seu ID.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID da foto
 * @return array|false
 */
function galeria_buscar_por_id(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare('SELECT * FROM galeria WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// =============================================================================
// CRIAR
// =============================================================================

/**
 * Insere uma nova foto na galeria.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param array $dados Dados da foto: arquivo, titulo, categoria, ordem, ativo
 * @return int ID do registro criado
 */
function galeria_criar(PDO $pdo, array $dados): int {
    $sql = 'INSERT INTO galeria (arquivo, titulo, categoria, ordem, ativo)
            VALUES (:arquivo, :titulo, :categoria, :ordem, :ativo)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':arquivo'   => $dados['arquivo'],
        ':titulo'    => $dados['titulo']    ?? null,
        ':categoria' => $dados['categoria'] ?? null,
        ':ordem'     => $dados['ordem']     ?? 0,
        ':ativo'     => $dados['ativo']     ?? 1,
    ]);

    return (int) $pdo->lastInsertId();
}

// =============================================================================
// ATUALIZAR
// =============================================================================

/**
 * Atualiza os metadados de uma foto da galeria.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param int   $id    ID da foto
 * @param array $dados Novos dados
 * @return int Linhas afetadas
 */
function galeria_atualizar(PDO $pdo, int $id, array $dados): int {
    $sql = 'UPDATE galeria SET
                titulo    = :titulo,
                categoria = :categoria,
                ordem     = :ordem,
                ativo     = :ativo
            WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'    => $dados['titulo']    ?? null,
        ':categoria' => $dados['categoria'] ?? null,
        ':ordem'     => $dados['ordem']     ?? 0,
        ':ativo'     => $dados['ativo']     ?? 1,
        ':id'        => $id,
    ]);

    return $stmt->rowCount();
}

// =============================================================================
// DELETAR
// =============================================================================

/**
 * Remove uma foto da galeria do banco (não apaga o arquivo físico).
 * Para apagar o arquivo físico, utilize unlink() antes de chamar esta função.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID da foto
 * @return int Linhas afetadas
 */
function galeria_deletar(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare('DELETE FROM galeria WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}
