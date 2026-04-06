<?php
/**
 * php/crud/usuarios_crud.php
 * CRUD para a tabela `usuarios` (gestão de usuários do painel)
 *
 * Requer: $pdo (PDO) — conexão com o banco.
 * ATENÇÃO: Nunca exiba senha_hash para o usuário final.
 */

// =============================================================================
// LISTAR
// =============================================================================

/**
 * Lista todos os usuários cadastrados no sistema (sem expor o hash da senha).
 *
 * @param PDO $pdo Conexão com o banco
 * @return array
 */
function usuarios_listar(PDO $pdo): array {
    $stmt = $pdo->query(
        'SELECT id, nome, email, nivel, ativo, criado_em, atualizado_em
         FROM usuarios
         ORDER BY nome ASC'
    );
    return $stmt->fetchAll();
}

// =============================================================================
// BUSCAR POR ID / EMAIL
// =============================================================================

/**
 * Retorna um usuário pelo seu ID (sem o hash da senha).
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do usuário
 * @return array|false
 */
function usuarios_buscar_por_id(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare(
        'SELECT id, nome, email, nivel, ativo, criado_em FROM usuarios WHERE id = :id LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Busca um usuário pelo e-mail para fins de autenticação.
 * Esta função retorna o hash da senha (necessário para password_verify).
 *
 * @param PDO    $pdo   Conexão com o banco
 * @param string $email E-mail do usuário
 * @return array|false Retorna todos os campos, incluindo senha_hash
 */
function usuarios_buscar_para_login(PDO $pdo, string $email): array|false {
    $stmt = $pdo->prepare(
        'SELECT * FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

// =============================================================================
// CRIAR
// =============================================================================

/**
 * Cria um novo usuário no sistema.
 * A senha é criptografada automaticamente com password_hash().
 *
 * @param PDO    $pdo    Conexão com o banco
 * @param string $nome   Nome completo
 * @param string $email  E-mail único
 * @param string $senha  Senha em texto puro (será hasheada)
 * @param string $nivel  'admin' ou 'editor'
 * @return int           ID do usuário criado
 * @throws PDOException  Se o e-mail já estiver cadastrado (violação de unique key)
 */
function usuarios_criar(PDO $pdo, string $nome, string $email, string $senha, string $nivel = 'editor'): int {
    // Gera o hash seguro da senha (bcrypt com custo padrão)
    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nome, email, senha_hash, nivel) VALUES (:nome, :email, :hash, :nivel)'
    );
    $stmt->execute([
        ':nome'  => $nome,
        ':email' => $email,
        ':hash'  => $hash,
        ':nivel' => $nivel,
    ]);

    return (int) $pdo->lastInsertId();
}

// =============================================================================
// ATUALIZAR DADOS BÁSICOS
// =============================================================================

/**
 * Atualiza nome, e-mail, nível e status ativo de um usuário.
 *
 * @param PDO    $pdo   Conexão com o banco
 * @param int    $id    ID do usuário
 * @param string $nome
 * @param string $email
 * @param string $nivel
 * @param int    $ativo 1 = ativo, 0 = bloqueado
 * @return int Linhas afetadas
 */
function usuarios_atualizar(PDO $pdo, int $id, string $nome, string $email, string $nivel, int $ativo = 1): int {
    $stmt = $pdo->prepare(
        'UPDATE usuarios SET nome = :nome, email = :email, nivel = :nivel, ativo = :ativo WHERE id = :id'
    );
    $stmt->execute([
        ':nome'  => $nome,
        ':email' => $email,
        ':nivel' => $nivel,
        ':ativo' => $ativo,
        ':id'    => $id,
    ]);
    return $stmt->rowCount();
}

/**
 * Atualiza apenas a senha de um usuário.
 * A nova senha é criptografada automaticamente.
 *
 * @param PDO    $pdo       Conexão com o banco
 * @param int    $id        ID do usuário
 * @param string $nova_senha Senha em texto puro
 * @return int Linhas afetadas
 */
function usuarios_atualizar_senha(PDO $pdo, int $id, string $nova_senha): int {
    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE usuarios SET senha_hash = :hash WHERE id = :id');
    $stmt->execute([':hash' => $hash, ':id' => $id]);
    return $stmt->rowCount();
}

// =============================================================================
// DELETAR
// =============================================================================

/**
 * Remove um usuário do sistema.
 * CUIDADO: não permita remover o único usuário admin existente.
 *
 * @param PDO $pdo Conexão com o banco
 * @param int $id  ID do usuário
 * @return int Linhas afetadas
 */
function usuarios_deletar(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}
