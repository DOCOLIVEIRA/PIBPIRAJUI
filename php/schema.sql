-- ============================================================
-- schema.sql — PIB Pirajuí
-- Banco MySQL/MariaDB | charset utf8mb4 | padrão HostGator
-- Execute via phpMyAdmin ou CLI: mysql -u user -p banco < schema.sql
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- ── 1. CONFIGURAÇÕES DA IGREJA ──────────────────────────────
CREATE TABLE IF NOT EXISTS configuracoes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chave       VARCHAR(60)  NOT NULL UNIQUE   COMMENT 'Nome interno da configuração',
    valor       TEXT         NOT NULL           COMMENT 'Valor da configuração',
    descricao   VARCHAR(200) DEFAULT NULL       COMMENT 'Descrição legível para o admin',
    atualizado  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados iniciais
INSERT INTO configuracoes (chave, valor, descricao) VALUES
('nome_igreja',  'PIB Pirajuí',                                       'Nome completo da igreja'),
('descricao',    'Proclamando Cristo com amor e compromisso.',          'Slogan/subtítulo do hero'),
('pastor_nome',  'Pr. Nome do Pastor',                                 'Nome do pastor titular'),
('pastor_bio',   'Escreva aqui a biografia e mensagem do pastor.',     'Biografia exibida na seção Pastor'),
('pastor_foto',  'img/pastor.jpg',                                     'Caminho relativo da foto do pastor'),
('missao',       'Proclamar o Evangelho de Cristo a toda criatura.',   'Missão da igreja'),
('visao',        'Ser uma igreja que transforma vidas e comunidades.', 'Visão da igreja'),
('valores',      'Fé • Amor • Serviço • Unidade',                     'Valores institucionais'),
('endereco',     'Rua da Igreja, 100 – Pirajuí/SP',                   'Endereço completo'),
('telefone',     '(14) 9XXXX-XXXX',                                   'Telefone de contato'),
('email',        'contato@pibpirajui.com.br',                          'E-mail de contato'),
('horarios',     'Dom 09h e 19h • Qua 19h30',                         'Horários dos cultos regulares'),
('whatsapp',     '5514999999999',                                      'Número WhatsApp (somente dígitos, com DDI)');


-- ── 2. MINISTÉRIOS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ministerios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    descricao   TEXT         DEFAULT NULL,
    icone       VARCHAR(80)  DEFAULT 'fas fa-church' COMMENT 'Classe Font Awesome',
    ordem       TINYINT UNSIGNED DEFAULT 0            COMMENT 'Ordem de exibição no grid',
    ativo       TINYINT(1)   DEFAULT 1                COMMENT '1 = visível, 0 = oculto',
    criado_em   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ministerios (nome, descricao, icone, ordem) VALUES
('Ministério de Louvor',  'Adoração a Deus através da música e do canto.',           'fas fa-music',         1),
('Ministério Infantil',   'Cuidando e formando as crianças na Palavra.',              'fas fa-child',         2),
('Grupos de Células',     'Comunhão, crescimento e cuidado em pequenos grupos.',     'fas fa-users',         3),
('Diaconia',              'Serviço prático ao próximo com amor fraternal.',           'fas fa-hands-helping', 4),
('Escola Bíblica',        'Estudo aprofundado da Palavra de Deus.',                  'fas fa-bible',         5),
('Juventude',             'Jovens comprometidos com Cristo e com a missão.',         'fas fa-user-graduate', 6);


-- ── 3. AGENDA / EVENTOS ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS agenda (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo        VARCHAR(150) NOT NULL,
    descricao     TEXT         DEFAULT NULL,
    data_evento   DATE         NOT NULL,
    hora_evento   TIME         DEFAULT NULL,
    local_evento  VARCHAR(200) DEFAULT NULL,
    publico       TINYINT(1)   DEFAULT 1    COMMENT '1 = público, 0 = interno',
    criado_em     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_data (data_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Eventos de exemplo
INSERT INTO agenda (titulo, data_evento, hora_evento, local_evento) VALUES
('Culto de Celebração',    '2026-04-07', '19:00:00', 'Templo Principal'),
('Reunião de Células',     '2026-04-10', '20:00:00', 'Casas dos líderes'),
('Escola Bíblica Dominical','2026-04-14','09:00:00', 'Salas de Aula'),
('Seminário de Casais',    '2026-04-20', '14:00:00', 'Salão Social');


-- ── 4. MENSAGENS DE CONTATO ─────────────────────────────────
CREATE TABLE IF NOT EXISTS contato_mensagens (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120) NOT NULL,
    email       VARCHAR(180) NOT NULL,
    mensagem    TEXT         NOT NULL,
    ip          VARCHAR(45)  DEFAULT NULL  COMMENT 'IP do remetente (IPv4/IPv6)',
    lida        TINYINT(1)   DEFAULT 0     COMMENT '0 = não lida, 1 = lida',
    recebida_em TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lida (lida),
    INDEX idx_data (recebida_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 5. USUÁRIOS DO PAINEL ADMIN ─────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(100) NOT NULL,
    email        VARCHAR(180) NOT NULL UNIQUE,
    senha_hash   VARCHAR(255) NOT NULL   COMMENT 'password_hash() PHP',
    nivel        ENUM('admin','editor') DEFAULT 'editor',
    ativo        TINYINT(1)   DEFAULT 1,
    criado_em    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP    DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário admin padrão (senha: Pirajui@2026 — TROQUE IMEDIATAMENTE)
INSERT INTO usuarios (nome, email, senha_hash, nivel) VALUES
(
    'Administrador',
    'admin@pibpirajui.com.br',
    '$2y$12$YourHashHere',   -- ← substitua pelo hash gerado com password_hash()
    'admin'
);

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================
