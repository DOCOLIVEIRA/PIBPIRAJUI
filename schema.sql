-- =============================================================================
-- SCHEMA DO BANCO DE DADOS — Site Institucional PibPirajui
-- Servidor: MySQL (Hostgator)
-- Encoding: UTF-8 (utf8mb4)
-- Autor: Sistema gerado por IA
-- =============================================================================

-- Define o conjunto de caracteres padrão para suportar emojis e acentos
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- =============================================================================
-- TABELA: usuarios
-- Armazena os usuários com acesso ao painel administrativo
-- =============================================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `nome`           VARCHAR(100) NOT NULL COMMENT 'Nome completo do usuário',
    `email`          VARCHAR(150) NOT NULL COMMENT 'E-mail único para login',
    `senha_hash`     VARCHAR(255) NOT NULL COMMENT 'Senha criptografada com password_hash()',
    `nivel`          ENUM('admin','editor') NOT NULL DEFAULT 'editor' COMMENT 'Nível de permissão',
    `ativo`          TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = bloqueado',
    `criado_em`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuários do painel administrativo';

-- Usuário administrador padrão — Senha: admin123
-- Gerada com: password_hash("admin123", PASSWORD_DEFAULT)
INSERT INTO `usuarios` (`nome`, `email`, `senha_hash`, `nivel`) VALUES
('Administrador', 'admin@pibpirajui.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


-- =============================================================================
-- TABELA: configuracoes
-- Armazena as configurações gerais do site (chave → valor)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `configuracoes` (
    `id`       INT(11)      NOT NULL AUTO_INCREMENT,
    `chave`    VARCHAR(100) NOT NULL COMMENT 'Nome da configuração, ex: site_nome',
    `valor`    TEXT         NULL     COMMENT 'Valor da configuração',
    `descricao` VARCHAR(255) NULL    COMMENT 'Descrição amigável para o painel',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Configurações do site em formato chave-valor';

-- Dados iniciais de configuração
INSERT INTO `configuracoes` (`chave`, `valor`, `descricao`) VALUES
('site_nome',          'Primeira Igreja Batista de Pirajuí',       'Nome completo do site'),
('site_nome_curto',    'PIB Pirajuí',                               'Nome abreviado exibido no header'),
('site_email',         'contato@pibpirajui.com.br',                 'E-mail de contato principal'),
('site_telefone',      '(14) 99999-9999',                           'Telefone de contato'),
('site_whatsapp',      '5514999999999',                             'Número WhatsApp (com DDI, sem espaços)'),
('site_endereco',      'Rua Exemplo, 123 – Centro – Pirajuí/SP',    'Endereço da sede'),
('site_horario_culto', 'Domingos às 9h e 19h | Quartas às 19h30',   'Horários dos cultos'),
('site_facebook',      '',                                           'URL da página do Facebook'),
('site_instagram',     '',                                           'URL do Instagram'),
('site_youtube',       '',                                           'URL do canal do YouTube'),
('site_descricao_meta','Igreja evangélica batista localizada em Pirajuí/SP, comprometida com o Evangelho.', 'Descrição meta para SEO');


-- =============================================================================
-- TABELA: noticias
-- Armazena notícias e artigos do site
-- =============================================================================
CREATE TABLE IF NOT EXISTS `noticias` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `titulo`       VARCHAR(255) NOT NULL COMMENT 'Título da notícia',
    `slug`         VARCHAR(255) NOT NULL COMMENT 'URL amigável, ex: nome-da-noticia',
    `resumo`       TEXT         NULL     COMMENT 'Texto curto exibido na listagem',
    `conteudo`     LONGTEXT     NOT NULL COMMENT 'Conteúdo completo (pode conter HTML)',
    `imagem`       VARCHAR(255) NULL     COMMENT 'Caminho relativo da imagem de capa',
    `publicado`    TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 = publicado, 0 = rascunho',
    `usuario_id`   INT(11)      NOT NULL COMMENT 'Autor da notícia',
    `publicado_em` DATETIME     NULL     COMMENT 'Data/hora de publicação',
    `criado_em`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_slug` (`slug`),
    KEY `fk_noticia_usuario` (`usuario_id`),
    CONSTRAINT `fk_noticia_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Notícias e artigos publicados no site';


-- =============================================================================
-- TABELA: eventos
-- Armazena cultos, conferências e eventos da igreja
-- =============================================================================
CREATE TABLE IF NOT EXISTS `eventos` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `titulo`       VARCHAR(255) NOT NULL COMMENT 'Nome do evento',
    `descricao`    TEXT         NULL     COMMENT 'Descrição detalhada do evento',
    `imagem`       VARCHAR(255) NULL     COMMENT 'Imagem de divulgação',
    `local`        VARCHAR(255) NULL     COMMENT 'Local onde ocorrerá o evento',
    `data_inicio`  DATETIME     NOT NULL COMMENT 'Data e hora de início',
    `data_fim`     DATETIME     NULL     COMMENT 'Data e hora de encerramento (opcional)',
    `publicado`    TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 = visível no site',
    `criado_em`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Eventos, cultos e conferências da igreja';


-- =============================================================================
-- TABELA: ministerios
-- Lista os ministérios e departamentos da igreja
-- =============================================================================
CREATE TABLE IF NOT EXISTS `ministerios` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `nome`         VARCHAR(150) NOT NULL COMMENT 'Nome do ministério, ex: Jovens, Missões',
    `descricao`    TEXT         NULL     COMMENT 'Descrição e propósito do ministério',
    `icone`        VARCHAR(100) NULL     COMMENT 'Classe CSS do ícone (ex: Bootstrap Icons)',
    `imagem`       VARCHAR(255) NULL     COMMENT 'Foto do grupo ou banner',
    `responsavel`  VARCHAR(100) NULL     COMMENT 'Nome do líder responsável',
    `ordem`        INT(11)      NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição no site',
    `ativo`        TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1 = exibido no site',
    `criado_em`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Ministérios e departamentos da igreja';

-- Ministérios de exemplo
INSERT INTO `ministerios` (`nome`, `descricao`, `icone`, `responsavel`, `ordem`, `ativo`) VALUES
('Louvor e Adoração', 'Ministério responsável pela liderança musical nos cultos.', 'bi-music-note-beamed', 'A definir', 1, 1),
('Jovens',            'Departamento voltado para a juventude da igreja.',           'bi-people-fill',       'A definir', 2, 1),
('Crianças (ABCs)',   'Escola Bíblica dedicada às crianças.',                       'bi-hearts',            'A definir', 3, 1),
('Missões',           'Suporte à obra missionária local e global.',                'bi-globe2',            'A definir', 4, 1);


-- =============================================================================
-- TABELA: galeria
-- Armazena fotos da galeria de imagens do site
-- =============================================================================
CREATE TABLE IF NOT EXISTS `galeria` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `titulo`       VARCHAR(255) NULL     COMMENT 'Legenda da foto',
    `arquivo`      VARCHAR(255) NOT NULL COMMENT 'Caminho relativo do arquivo de imagem',
    `categoria`    VARCHAR(100) NULL     COMMENT 'Categoria para filtro, ex: Cultos, Eventos',
    `ordem`        INT(11)      NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição',
    `ativo`        TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1 = visível no site',
    `criado_em`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Galeria de fotos do site';


-- =============================================================================
-- TABELA: contatos
-- Armazena mensagens enviadas pelo formulário de contato do site
-- =============================================================================
CREATE TABLE IF NOT EXISTS `contatos` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `nome`         VARCHAR(150) NOT NULL COMMENT 'Nome de quem enviou',
    `email`        VARCHAR(150) NOT NULL COMMENT 'E-mail para resposta',
    `telefone`     VARCHAR(20)  NULL     COMMENT 'Telefone opcional',
    `assunto`      VARCHAR(255) NULL     COMMENT 'Assunto da mensagem',
    `mensagem`     TEXT         NOT NULL COMMENT 'Corpo da mensagem',
    `lido`         TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '0 = não lido, 1 = lido',
    `ip`           VARCHAR(45)  NULL     COMMENT 'IP de origem para auditoria',
    `criado_em`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Mensagens recebidas pelo formulário de contato';
