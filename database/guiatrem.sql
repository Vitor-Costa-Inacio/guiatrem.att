CREATE DATABASE IF NOT EXISTS guiatrem;
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
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tokens_redefinicao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    data_expiracao TIMESTAMP NOT NULL,
    utilizado BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS preferencias_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_preferencia VARCHAR(50) NOT NULL,
    valor_preferencia TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trens (
    id_trem INT AUTO_INCREMENT PRIMARY KEY,
    linha VARCHAR(50) NOT NULL,
    numero_trem VARCHAR(20) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    capacidade INT NOT NULL,
    status_trem ENUM('ativo', 'manutencao', 'inativo') DEFAULT 'ativo',
    data_ultima_manutencao DATE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS manutencao (
    id_manutencao INT AUTO_INCREMENT PRIMARY KEY,
    tipo_manutencao VARCHAR(50) NOT NULL,
    prioridade VARCHAR(20) NOT NULL,
    servico TEXT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_manutencao VARCHAR(20),
    fk_trem INT NOT NULL,
    fk_linha VARCHAR(50) NOT NULL,
    FOREIGN KEY (fk_trem) REFERENCES trens(id_trem)
);

CREATE TABLE IF NOT EXISTS historico_manutencoes (
    id_historico INT AUTO_INCREMENT PRIMARY KEY,
    id_manutencao INT NOT NULL,
    id_trem INT NOT NULL,
    descricao TEXT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tecnico_responsavel VARCHAR(100),
    custo_real DECIMAL(10,2),
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem)
);

CREATE TABLE IF NOT EXISTS estacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  latitude DECIMAL(10,8) NOT NULL,
  longitude DECIMAL(11,8) NOT NULL,
  endereco TEXT DEFAULT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('ativa','inativa') DEFAULT 'ativa'
);

CREATE TABLE IF NOT EXISTS rotas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  distancia_km DECIMAL(8,2) DEFAULT NULL,
  tempo_estimado_min INT DEFAULT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('ativa','inativa') DEFAULT 'ativa'
);

CREATE TABLE IF NOT EXISTS rota_estacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_rota INT NOT NULL,
  id_estacao INT NOT NULL,
  ordem INT NOT NULL,
  FOREIGN KEY (id_rota) REFERENCES rotas(id) ON DELETE CASCADE,
  FOREIGN KEY (id_estacao) REFERENCES estacoes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS linhas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  cor VARCHAR(20) NOT NULL,
  status ENUM('ativa','inativa') DEFAULT 'ativa',
  id_rota INT DEFAULT NULL,
  FOREIGN KEY (id_rota) REFERENCES rotas(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sensores (
    id_sensor INT AUTO_INCREMENT PRIMARY KEY,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(50) NOT NULL,
    localizacao VARCHAR(200) NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome_completo, email, telefone, departamento, cargo, senha_hash)
VALUES ('Ana Pereira', 'ana.pereira@example.com', '48999990001', 'RH', 'Analista', 'hash1');

INSERT INTO usuarios (nome_completo, email, telefone, departamento, cargo, senha_hash)
VALUES ('Bruno Souza', 'bruno.souza@example.com', '11988887777', 'TI', 'Desenvolvedor', 'hash2');

INSERT INTO usuarios (nome_completo, email, telefone, departamento, cargo, senha_hash)
VALUES ('Carla Lima', 'carla.lima@example.com', '21970001122', 'Financeiro', 'Contadora', 'hash3');

INSERT INTO historico_login (usuario_id, dispositivo, navegador, sistema_operacional, endereco_ip, status)
VALUES (1, 'Notebook Dell', 'Chrome', 'Windows 11', '192.168.0.1', 'bem-sucedido');

INSERT INTO historico_login (usuario_id, dispositivo, navegador, sistema_operacional, endereco_ip, status)
VALUES (2, 'iPhone 13', 'Safari', 'iOS 17', '10.0.0.5', 'falhou');

INSERT INTO historico_login (usuario_id, dispositivo, navegador, sistema_operacional, endereco_ip, status)
VALUES (3, 'Samsung S23', 'Chrome Mobile', 'Android 14', '172.16.0.2', 'suspeito');

INSERT INTO tokens_redefinicao_senha (usuario_id, token, data_expiracao)
VALUES (1, 'token_abc123', DATE_ADD(NOW(), INTERVAL 1 DAY));

INSERT INTO tokens_redefinicao_senha (usuario_id, token, data_expiracao)
VALUES (2, 'token_def456', DATE_ADD(NOW(), INTERVAL 1 DAY));

INSERT INTO tokens_redefinicao_senha (usuario_id, token, data_expiracao)
VALUES (3, 'token_xyz789', DATE_ADD(NOW(), INTERVAL 1 DAY));

INSERT INTO preferencias_usuario (usuario_id, tipo_preferencia, valor_preferencia)
VALUES (1, 'tema', 'escuro');

INSERT INTO preferencias_usuario (usuario_id, tipo_preferencia, valor_preferencia)
VALUES (2, 'idioma', 'pt-BR');

INSERT INTO preferencias_usuario (usuario_id, tipo_preferencia, valor_preferencia)
VALUES (3, 'notificacoes', 'desativadas');

INSERT INTO trens (linha, numero_trem, modelo, capacidade)
VALUES ('Linha Azul', 'TA101', 'Modelo X', 600);

INSERT INTO trens (linha, numero_trem, modelo, capacidade)
VALUES ('Linha Vermelha', 'TV202', 'Modelo Y', 500);

INSERT INTO trens (linha, numero_trem, modelo, capacidade)
VALUES ('Linha Verde', 'TG303', 'Modelo Z', 550);

INSERT INTO manutencao (tipo_manutencao, prioridade, servico, fk_trem, fk_linha)
VALUES ('Preventiva', 'Média', 'Troca de filtros', 1, 'Linha Azul');

INSERT INTO manutencao (tipo_manutencao, prioridade, servico, fk_trem, fk_linha)
VALUES ('Corretiva', 'Alta', 'Reparo no sistema elétrico', 2, 'Linha Vermelha');

INSERT INTO manutencao (tipo_manutencao, prioridade, servico, fk_trem, fk_linha)
VALUES ('Preventiva', 'Baixa', 'Lubrificação de peças', 3, 'Linha Verde');

INSERT INTO historico_manutencoes (id_manutencao, id_trem, descricao, tecnico_responsavel, custo_real)
VALUES (1, 1, 'Substituição de filtros concluída', 'Técnico A', 150.00);

INSERT INTO historico_manutencoes (id_manutencao, id_trem, descricao, tecnico_responsavel, custo_real)
VALUES (2, 2, 'Reparo realizado com sucesso', 'Técnico B', 800.00);

INSERT INTO historico_manutencoes (id_manutencao, id_trem, descricao, tecnico_responsavel, custo_real)
VALUES (3, 3, 'Lubrificação aplicada', 'Técnico C', 90.00);

INSERT INTO estacoes (nome, latitude, longitude, endereco)
VALUES ('Estação Central', -23.55052, -46.633308, 'São Paulo');

INSERT INTO estacoes (nome, latitude, longitude, endereco)
VALUES ('Estação Norte', -22.906847, -43.172896, 'Rio de Janeiro');

INSERT INTO estacoes (nome, latitude, longitude, endereco)
VALUES ('Estação Sul', -30.034647, -51.217658, 'Porto Alegre');

INSERT INTO rotas (nome, distancia_km, tempo_estimado_min)
VALUES ('Rota Leste-Oeste', 1200.50, 1300);

INSERT INTO rotas (nome, distancia_km, tempo_estimado_min)
VALUES ('Rota Metropolitana', 350.20, 280);

INSERT INTO rotas (nome, distancia_km, tempo_estimado_min)
VALUES ('Rota Interestadual', 900.70, 950);

INSERT INTO rota_estacoes (id_rota, id_estacao, ordem)
VALUES (1, 1, 0);

INSERT INTO rota_estacoes (id_rota, id_estacao, ordem)
VALUES (1, 2, 1);

INSERT INTO rota_estacoes (id_rota, id_estacao, ordem)
VALUES (2, 3, 0);

INSERT INTO linhas (nome, cor, status, id_rota)
VALUES ('Linha Amarela', 'amarela', 'ativa', 1);

INSERT INTO linhas (nome, cor, status, id_rota)
VALUES ('Linha Cinza', 'cinza', 'ativa', 2);

INSERT INTO linhas (nome, cor, status, id_rota)
VALUES ('Linha Roxa', 'roxa', 'ativa', 3);

INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status)
VALUES ('Sensor Temp 01', 'Temperatura', 'Vagão A', 'ativo');

INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status)
VALUES ('Sensor Pressão 02', 'Pressão', 'Vagão B', 'ativo');

INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status)
VALUES ('Sensor Vibração 03', 'Vibração', 'Motor Principal', 'inativo');
