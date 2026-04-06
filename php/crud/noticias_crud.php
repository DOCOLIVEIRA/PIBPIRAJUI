<?php
/**
 * php/crud/noticias_crud.php
 * CRUD completo para a tabela `noticias`
 *
 * Inclua este arquivo nas páginas que precisam manipular notícias.
 * Requer: $pdo (PDO) — conexão com o banco, já disponível via includes/db.php
 */

// =============================================================================
// LISTAR — Retorna notícias com paginação e filtro opcional
// =============================================================================

/**
 * Lista notícias com suporte a paginação.
 *
 * @param PDO  $pdo        Conexão com o banco
 * @param int  $pagina     Número da página atual (começa em 1)
 * @param int  $por_pagina Quantidade de registros por página
 * @param bool $apenas_publicadas Se true, traz apenas notícias publicadas
 * @return array ['dados' => [...], 'total' => int]
 */
function noticias_listar(PDO $pdo, int $pagina = 1, int $por_pagina = 10, bool $apenas_publicadas = false): array {
    $offset = ($pagina - 1) * $por_pagina;
    $where  = $apenas_publicadas ? 'WHERE n.publicado = 1' : '';

    // Conta o total para paginação
    $stmt_total = $pdo->query("SELECT COUNT(*) FROM noticias n $where");
    $total = (int) $stmt_total->fetchColumn();

    // Busca as notícias com nome do autor
    $sql = "SELECT n.*, u.nome AS autor_nome
            FROM noticias n
            LEFT JOIN usuarios u ON n.usuario_id = u.id
            $where
            ORDER BY n.publicado_em DESC, n.criado_em DESC
            LIMIT :limite OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
    $stmt->execute();

    return [
        'dados' => $stmt->fetchAll(),
        'total' => $total,
    ];
}

// =============================================================================
// BUSCAR POR ID — Retorna uma notícia específica
// =============================================================================

/**
 * Busca uma notícia pelo seu ID.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID da notícia
 * @return array|false Dados da notícia ou false se não encontrada
 */
function noticias_buscar_por_id(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare(
        'SELECT n.*, u.nome AS autor_nome
         FROM noticias n
         LEFT JOIN usuarios u ON n.usuario_id = u.id
         WHERE n.id = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Busca uma notícia pelo seu slug (URL amigável).
 *
 * @param PDO    $pdo  Conexão com o banco
 * @param string $slug Slug da notícia
 * @return array|false
 */
function noticias_buscar_por_slug(PDO $pdo, string $slug): array|false {
    $stmt = $pdo->prepare(
        'SELECT n.*, u.nome AS autor_nome
         FROM noticias n
         LEFT JOIN usuarios u ON n.usuario_id = u.id
         WHERE n.slug = :slug AND n.publicado = 1
         LIMIT 1'
    );
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

// =============================================================================
// CRIAR — Insere uma nova notícia
// =============================================================================

/**
 * Insere uma nova notícia no banco de dados.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param array $dados Array com os dados da notícia:
 *                     titulo, slug, resumo, conteudo, imagem, publicado,
 *                     usuario_id, publicado_em
 * @return int ID do registro inserido
 */
function noticias_criar(PDO $pdo, array $dados): int {
    $sql = 'INSERT INTO noticias
                (titulo, slug, resumo, conteudo, imagem, publicado, usuario_id, publicado_em)
            VALUES
                (:titulo, :slug, :resumo, :conteudo, :imagem, :publicado, :usuario_id, :publicado_em)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'       => $dados['titulo'],
        ':slug'         => $dados['slug'],
        ':resumo'       => $dados['resumo']       ?? null,
        ':conteudo'     => $dados['conteudo'],
        ':imagem'       => $dados['imagem']       ?? null,
        ':publicado'    => $dados['publicado']    ?? 0,
        ':usuario_id'   => $dados['usuario_id'],
        ':publicado_em' => $dados['publicado_em'] ?? null,
    ]);

    return (int) $pdo->lastInsertId();
}

// =============================================================================
// ATUALIZAR — Edita uma notícia existente
// =============================================================================

/**
 * Atualiza os dados de uma notícia.
 *
 * @param PDO   $pdo   Conexão com o banco
 * @param int   $id    ID da notícia a ser atualizada
 * @param array $dados Novos dados (mesma estrutura de noticias_criar)
 * @return int Número de linhas afetadas
 */
function noticias_atualizar(PDO $pdo, int $id, array $dados): int {
    $sql = 'UPDATE noticias SET
                titulo       = :titulo,
                slug         = :slug,
                resumo       = :resumo,
                conteudo     = :conteudo,
                imagem       = :imagem,
                publicado    = :publicado,
                publicado_em = :publicado_em
            WHERE id = :id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'       => $dados['titulo'],
        ':slug'         => $dados['slug'],
        ':resumo'       => $dados['resumo']       ?? null,
        ':conteudo'     => $dados['conteudo'],
        ':imagem'       => $dados['imagem']       ?? null,
        ':publicado'    => $dados['publicado']    ?? 0,
        ':publicado_em' => $dados['publicado_em'] ?? null,
        ':id'           => $id,
    ]);

    return $stmt->rowCount();
}

// =============================================================================
// DELETAR — Remove uma notícia
// =============================================================================

/**
 * Remove uma notícia do banco de dados pelo ID.
 * Atenção: esta operação é irreversível.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID da notícia
 * @return int Número de linhas afetadas (1 = sucesso, 0 = não encontrado)
 */
function noticias_deletar(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare('DELETE FROM noticias WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}
