CREATE DATABASE IF NOT EXISTS guiatrem
USE guiatrem;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    departamento VARCHAR(100),
    cargo VARCHAR(100),
    data_admissao DATE,
    endereco VARCHAR(255),
    complemento VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    senha_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'default-avatar.png',
    notificacoes_email BOOLEAN DEFAULT TRUE,
    modo_escuro BOOLEAN DEFAULT FALSE,
    atualizacao_automatica BOOLEAN DEFAULT TRUE,
    idioma VARCHAR(10) DEFAULT 'pt-BR',
    autenticacao_dois_fatores BOOLEAN DEFAULT FALSE,
    conta_verificada BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL,
    status ENUM('ativo', 'inativo', 'suspenso') DEFAULT 'ativo'
);

CREATE TABLE IF NOT EXISTS historico_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dispositivo VARCHAR(255),
    navegador VARCHAR(100),
    sistema_operacional VARCHAR(100),
    localizacao VARCHAR(255),
    endereco_ip VARCHAR(45),
    status ENUM('bem-sucedido', 'falhou', 'suspeito') DEFAULT 'bem-sucedido',
    user_agent TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_data (usuario_id, data_hora)
);

CREATE TABLE IF NOT EXISTS tokens_redefinicao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    data_expiracao TIMESTAMP NOT NULL,
    utilizado BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token)
);

CREATE TABLE IF NOT EXISTS preferencias_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_preferencia VARCHAR(50) NOT NULL,
    valor_preferencia TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_preferencia (usuario_id, tipo_preferencia)
);

INSERT INTO usuarios (
    nome_completo, 
    email, 
    telefone, 
    departamento, 
    cargo, 
    data_admissao, 
    endereco, 
    complemento, 
    cidade, 
    estado, 
    senha_hash
);

INSERT INTO historico_login (
    usuario_id,
    data_hora,
    dispositivo,
    navegador,
    sistema_operacional,
    localizacao,
    endereco_ip,
    status,
    user_agent
);

CREATE TABLE trem(
    id_trem INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    linha_trem ENUM('amarela', 'azul', 'vermelha', 'verde') NOT NULL,
    numero_trem VARCHAR(20) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    capacidade INT NOT NULL,
    status_trem ENUM('ativo', 'manutencao', 'inativo') DEFAULT 'ativo',
    data_ultima_manutencao DATE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE manutencao (
    id_manutencao INT PRIMARY KEY AUTO_INCREMENT,
    tipo_manutencao ENUM('preventiva', 'corretiva') NOT NULL,
    prioridade ENUM('baixa', 'média', 'alta', 'urgente') NOT NULL,
    servico TEXT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_manutencao ENUM('nao_iniciada', 'em_andamento', 'concluida') DEFAULT 'nao_iniciada',
    fk_trem INT NOT NULL,
    fk_linha ENUM('amarela', 'azul', 'vermelha', 'verde') NOT NULL,
    FOREIGN KEY (fk_trem) REFERENCES trem(id_trem),
    FOREIGN KEY (fk_linha) REFERENCES trem(id_linha)
);

CREATE TABLE historico_manutencoes (
    id_historico INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_manutencao INT NOT NULL,
    id_trem INT NOT NULL,
    descricao TEXT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tecnico_responsavel VARCHAR(100),
    custo_real DECIMAL(10,2),
    FOREIGN KEY (id_manutencao) REFERENCES manutencoes(id_manutencao),
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem)
);

-- MAPA --

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE estacoes (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  latitude decimal(10,8) NOT NULL,
  longitude decimal(11,8) NOT NULL,
  endereco text DEFAULT NULL,
  data_criacao timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO estacoes (id, 'nome', 'latitude', 'longitude', 'endereco', 'data_criacao') VALUES
(1, 'Estação 1', -26.30400000, -48.84600000, 'Joinville', '2025-10-27 11:11:09'),
(2, 'Estação 2', -3.73045100, -38.52179900, 'Fortaleza', '2025-10-27 11:13:07'),
(3, 'Estação 1.1', -25.42770000, -49.27310000, 'Curitiba', '2025-10-27 14:58:37'),
(4, 'Estação 1.2', -23.59140000, -48.05310000, 'Itapetininga', '2025-10-27 14:59:50'),
(5, 'Estação 1.3', -19.92270000, -43.94510000, 'Belo Horizonte', '2025-10-27 15:00:29'),
(6, 'Estação 1.4', -18.85000000, -41.94000000, 'Governador Valadares', '2025-10-27 15:01:26'),
(7, 'Estação 1.5', -9.39000000, -40.50000000, 'Petrolina', '2025-10-27 15:02:21'),
(8, 'Estação 1.6', -7.21300000, -39.31500000, 'Juazeiro do Norte', '2025-10-27 15:03:02'),
(9, 'Estação 1.7', -7.23000000, -35.88000000, 'Campina Grande', '2025-10-27 15:04:08'),
(10, 'Estação 1.8', -5.96166700, -35.20888900, 'Natal', '2025-10-27 15:05:12'),
(11, 'Estação 1.9', -5.18412850, -37.34778050, 'Mossoró', '2025-10-27 15:05:57');

CREATE TABLE rotas (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  distancia_km decimal(8,2) DEFAULT NULL,
  tempo_estimado_min int(11) DEFAULT NULL,
  data_criacao timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO rotas ('id', 'nome', 'distancia_km', 'tempo_estimado_min', 'data_criacao') VALUES
(1, 'Rota Sul-Norte', 3511.25, 3511, '2025-10-27 15:36:21');

CREATE TABLE rota_estacoes (
  id int(11) NOT NULL,
  id_rota int(11) NOT NULL,
  id_estacao int(11) NOT NULL,
  ordem int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO rota_estacoes (id, id_rota, id_estacao, ordem) VALUES
(1, 1, 1, 0),
(2, 1, 3, 1),
(3, 1, 4, 2),
(4, 1, 5, 3),
(5, 1, 6, 4),
(6, 1, 7, 5),
(7, 1, 8, 6),
(8, 1, 9, 7),
(9, 1, 10, 8),
(10, 1, 11, 9),
(11, 1, 2, 10);

ALTER TABLE estacoes
  ADD PRIMARY KEY (id);

ALTER TABLE rotas
  ADD PRIMARY KEY (id);

ALTER TABLE rota_estacoes
  ADD PRIMARY KEY (id),
  ADD KEY id_rota (id_rota),
  ADD KEY id_estacao (id_estacao);

ALTER TABLE estacoes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE rotas
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE rota_estacoes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE rota_estacoes
  ADD CONSTRAINT rota_estacoes_ibfk_1 FOREIGN KEY (id_rota) REFERENCES rotas (id) ON DELETE CASCADE,
  ADD CONSTRAINT rota_estacoes_ibfk_2 FOREIGN KEY (id_estacao) REFERENCES estacoes (id) ON DELETE CASCADE;
COMMIT;

-- TREM --

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `estacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `endereco` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('ativa','inativa') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `estacoes` (`id`, `nome`, `latitude`, `longitude`, `endereco`, `data_criacao`, `status`) VALUES
(1, 'Estação Central Joinville', -26.30400000, -48.84600000, 'Joinville, Santa Catarina', '2025-10-27 11:11:09', 'ativa'),
(2, 'Estação Fortaleza', -3.73045100, -38.52179900, 'Fortaleza, Ceará', '2025-10-27 11:13:07', 'ativa'),
(3, 'Estação Curitiba', -25.42770000, -49.27310000, 'Curitiba, Paraná', '2025-10-27 14:58:37', 'ativa'),
(4, 'Estação Itapetininga', -23.59140000, -48.05310000, 'Itapetininga, São Paulo', '2025-10-27 14:59:50', 'ativa'),
(5, 'Estação Belo Horizonte', -19.92270000, -43.94510000, 'Belo Horizonte, Minas Gerais', '2025-10-27 15:00:29', 'ativa'),
(6, 'Estação Governador Valadares', -18.85000000, -41.94000000, 'Governador Valadares, Minas Gerais', '2025-10-27 15:01:26', 'ativa'),
(7, 'Estação Petrolina', -9.39000000, -40.50000000, 'Petrolina, Pernambuco', '2025-10-27 15:02:21', 'ativa'),
(8, 'Estação Juazeiro do Norte', -7.21300000, -39.31500000, 'Juazeiro do Norte, Ceará', '2025-10-27 15:03:02', 'ativa'),
(9, 'Estação Campina Grande', -7.23000000, -35.88000000, 'Campina Grande, Paraíba', '2025-10-27 15:04:08', 'ativa'),
(10, 'Estação Natal', -5.96166700, -35.20888900, 'Natal, Rio Grande do Norte', '2025-10-27 15:05:12', 'ativa'),
(11, 'Estação Mossoró', -5.18412850, -37.34778050, 'Mossoró, Rio Grande do Norte', '2025-10-27 15:05:57', 'ativa'),
(12, 'Estação São Paulo', -23.55052000, -46.63330800, 'São Paulo, São Paulo', '2025-10-29 10:00:00', 'ativa'),
(13, 'Estação Rio de Janeiro', -22.90684700, -43.17289600, 'Rio de Janeiro, Rio de Janeiro', '2025-10-29 10:00:00', 'ativa'),
(14, 'Estação Porto Alegre', -30.03464700, -51.21765800, 'Porto Alegre, Rio Grande do Sul', '2025-10-29 10:00:00', 'ativa'),
(15, 'Estação Salvador', -12.97140000, -38.50140000, 'Salvador, Bahia', '2025-10-29 10:00:00', 'ativa');

CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `distancia_km` decimal(8,2) DEFAULT NULL,
  `tempo_estimado_min` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('ativa','inativa') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rotas` (`id`, `nome`, `distancia_km`, `tempo_estimado_min`, `data_criacao`, `status`) VALUES
(1, 'Rota Sul-Norte', 3511.25, 3511, '2025-10-27 15:36:21', 'ativa'),
(2, 'Ruta Costeira', 2850.75, 2850, '2025-10-29 10:00:00', 'ativa'),
(3, 'Rota Centro', 1250.30, 1250, '2025-10-29 10:00:00', 'ativa'),
(4, 'Rota Sudeste', 980.45, 980, '2025-10-29 10:00:00', 'ativa');

CREATE TABLE `rota_estacoes` (
  `id` int(11) NOT NULL,
  `id_rota` int(11) NOT NULL,
  `id_estacao` int(11) NOT NULL,
  `ordem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rota_estacoes` (`id`, `id_rota`, `id_estacao`, `ordem`) VALUES
(1, 1, 1, 0),
(2, 1, 3, 1),
(3, 1, 4, 2),
(4, 1, 5, 3),
(5, 1, 6, 4),
(6, 1, 7, 5),
(7, 1, 8, 6),
(8, 1, 9, 7),
(9, 1, 10, 8),
(10, 1, 11, 9),
(11, 1, 2, 10),
(12, 2, 14, 0),
(13, 2, 12, 1),
(14, 2, 13, 2),
(15, 2, 15, 3),
(16, 3, 12, 0),
(17, 3, 5, 1),
(18, 3, 13, 2),
(19, 4, 12, 0),
(20, 4, 4, 1),
(21, 4, 3, 2);

CREATE TABLE linhas (
  id int(11) NOT NULL,
  nome varchar(50) NOT NULL,
  cor varchar(20) NOT NULL,
  status enum('ativa','inativa') DEFAULT 'ativa',
  id_rota int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO linhas (id, 'nome', 'cor', 'status', 'id_rota') VALUES
(1, 'Linha Amarela', 'amarela', 'ativa', 1),
(2, 'Linha Azul', 'azul', 'ativa', 2),
(3, 'Linha Verde', 'verde', 'ativa', 3),
(4, 'Linha Vermelha', 'vermelha', 'ativa', 4);

ALTER TABLE estacoes
  ADD PRIMARY KEY (id);

ALTER TABLE linhas
  ADD PRIMARY KEY (id),
  ADD KEY id_rota (id_rota);

ALTER TABLE rotas
  ADD PRIMARY KEY (id);

ALTER TABLE rota_estacoes
  ADD PRIMARY KEY (id),
  ADD KEY id_rota (id_rota),
  ADD KEY id_estacao (id_estacao);

ALTER TABLE estacoes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE linhas
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE rotas
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE rota_estacoes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE linhas
  ADD CONSTRAINT linhas_ibfk_1 FOREIGN KEY (id_rota) REFERENCES rotas (id) ON DELETE SET NULL;

ALTER TABLE rota_estacoes
  ADD CONSTRAINT rota_estacoes_ibfk_1 FOREIGN KEY (id_rota) REFERENCES rotas (id) ON DELETE CASCADE,
  ADD CONSTRAINT rota_estacoes_ibfk_2 FOREIGN KEY (id_estacao) REFERENCES estacoes (id) ON DELETE CASCADE;
COMMIT;

--sensores--

CREATE TABLE IF NOT EXISTS sensores (
    id_sensor INT AUTO_INCREMENT PRIMARY KEY,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(50) NOT NULL,
    localizacao VARCHAR(200) NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status) VALUES
('Sensor Temperatura 001', 'Temperatura', 'Vagão A - Compartimento Motor', 'ativo'),