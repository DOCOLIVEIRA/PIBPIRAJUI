<?php
/**
 * includes/conexao.php
 * Alias → delega para db.php (fonte oficial de conexão do projeto).
 * Garante compatibilidade com index.php sem duplicar credenciais.
 */
require_once __DIR__ . '/db.php';
