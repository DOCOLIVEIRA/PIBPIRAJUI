<?php
/**
 * php/crud/eventos_crud.php
 * CRUD completo para a tabela `eventos`
 *
 * Inclua este arquivo onde for necessário gerenciar eventos.
 * Requer: $pdo (PDO) — conexão com o banco.
 */

// =============================================================================
// LISTAR — Retorna eventos com paginação
// =============================================================================

/**
 * Lista eventos ordenados por data de início.
 *
 * @param PDO  $pdo              Conexão com o banco
 * @param int  $pagina           Número da página (começa em 1)
 * @param int  $por_pagina       Registros por página
 * @param bool $apenas_futuros   Se true, retorna apenas eventos a partir de hoje
 * @param bool $apenas_publicados Se true, retorna apenas eventos publicados
 * @return array ['dados' => [...], 'total' => int]
 */
function eventos_listar(
    PDO  $pdo,
    int  $pagina            = 1,
    int  $por_pagina        = 10,
    bool $apenas_futuros    = false,
    bool $apenas_publicados = false
): array {
    $offset = ($pagina - 1) * $por_pagina;

    // Monta cláusulas WHERE dinamicamente
    $condicoes = [];
    if ($apenas_futuros)    $condicoes[] = 'data_inicio >= NOW()';
    if ($apenas_publicados) $condicoes[] = 'publicado = 1';
    $where = count($condicoes) ? 'WHERE ' . implode(' AND ', $condicoes) : '';

    // Conta total para paginação
    $stmt_total = $pdo->query("SELECT COUNT(*) FROM eventos $where");
    $total = (int) $stmt_total->fetchColumn();

    $sql = "SELECT * FROM eventos $where ORDER BY data_inicio ASC LIMIT :limite OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
    $stmt->execute();

    return ['dados' => $stmt->fetchAll(), 'total' => $total];
}

// =============================================================================
// BUSCAR POR ID
// =============================================================================

/**
 * Retorna um evento pelo seu ID.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do evento
 * @return array|false
 */
function eventos_buscar_por_id(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare('SELECT * FROM eventos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// =============================================================================
// CRIAR
// =============================================================================

/**
 * Insere um novo evento no banco.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param array $dados Dados do evento:
 *                     titulo, descricao, imagem, local, data_inicio,
 *                     data_fim, publicado
 * @return int ID do evento criado
 */
function eventos_criar(PDO $pdo, array $dados): int {
    $sql = 'INSERT INTO eventos (titulo, descricao, imagem, local, data_inicio, data_fim, publicado)
            VALUES (:titulo, :descricao, :imagem, :local, :data_inicio, :data_fim, :publicado)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'      => $dados['titulo'],
        ':descricao'   => $dados['descricao']  ?? null,
        ':imagem'      => $dados['imagem']     ?? null,
        ':local'       => $dados['local']      ?? null,
        ':data_inicio' => $dados['data_inicio'],
        ':data_fim'    => $dados['data_fim']   ?? null,
        ':publicado'   => $dados['publicado']  ?? 0,
    ]);

    return (int) $pdo->lastInsertId();
}

// =============================================================================
// ATUALIZAR
// =============================================================================

/**
 * Atualiza os dados de um evento existente.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param int   $id    ID do evento
 * @param array $dados Novos dados (mesma estrutura de eventos_criar)
 * @return int Linhas afetadas
 */
function eventos_atualizar(PDO $pdo, int $id, array $dados): int {
    $sql = 'UPDATE eventos SET
                titulo      = :titulo,
                descricao   = :descricao,
                imagem      = :imagem,
                local       = :local,
                data_inicio = :data_inicio,
                data_fim    = :data_fim,
                publicado   = :publicado
            WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'      => $dados['titulo'],
        ':descricao'   => $dados['descricao']  ?? null,
        ':imagem'      => $dados['imagem']     ?? null,
        ':local'       => $dados['local']      ?? null,
        ':data_inicio' => $dados['data_inicio'],
        ':data_fim'    => $dados['data_fim']   ?? null,
        ':publicado'   => $dados['publicado']  ?? 0,
        ':id'          => $id,
    ]);

    return $stmt->rowCount();
}

// =============================================================================
// DELETAR
// =============================================================================

/**
 * Remove um evento do banco pelo ID.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do evento
 * @return int Linhas afetadas
 */
function eventos_deletar(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare('DELETE FROM eventos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}
