-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS guiatrem
USE guia_trem_db;

-- Tabela de usuários
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

-- Tabela de histórico de login
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

-- Tabela de tokens de redefinição de senha
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

-- Tabela de preferências do usuário
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

-- Inserir usuário de exemplo
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
) VALUES (
    'Carlos Alberto Silva',
    'carlos.silva@guiatrem.com',
    '(11) 98765-4321',
    'Manutenção e Operações',
    'Supervisor de Manutenção',
    '2019-03-15',
    'Rua das Flores, 123',
    'Apto 45B',
    'São Paulo',
    'SP',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' -- password
);

-- Inserir histórico de login de exemplo
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
) VALUES 
(1, NOW() - INTERVAL 1 HOUR, 'Computador', 'Chrome', 'Windows', 'São Paulo, SP', '192.168.1.100', 'bem-sucedido', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'),
(1, NOW() - INTERVAL 1 DAY, 'Mobile', 'Safari', 'iOS', 'São Paulo, SP', '192.168.1.101', 'bem-sucedido', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1'),
(1, NOW() - INTERVAL 3 DAY, 'Computador', 'Firefox', 'Windows', 'Rio de Janeiro, RJ', '200.150.100.50', 'suspeito', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'),
(1, NOW() - INTERVAL 6 DAY, 'Mobile', 'Chrome', 'Android', 'São Paulo, SP', '192.168.1.100', 'bem-sucedido', 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36'),
(1, NOW() - INTERVAL 8 DAY, 'Computador', 'Chrome', 'Windows', 'São Paulo, SP', '192.168.1.100', 'falhou', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

-- Inserir preferências de exemplo
INSERT INTO preferencias_usuario (usuario_id, tipo_preferencia, valor_preferencia) VALUES
(1, 'notificacoes_email', '1'),
(1, 'modo_escuro', '0'),
(1, 'atualizacao_automatica', '1'),
(1, 'idioma', 'pt-BR');

CREATE TABLE trem(
    id_trem INT PRIMARY KEY AUTO_INCREMENT,
    linha_trem ENUM('amarela', 'azul', 'vermelha', 'verde') NOT NULL
);

CREATE TABLE manutencao (
    id_manutencao INT PRIMARY KEY AUTO_INCREMENT,
    tipo_manutencao ENUM('preventiva', 'corretiva') NOT NULL,
    prioridade ENUM('baixa', 'média', 'alta', 'urgente') NOT NULL,
    servico TEXT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    fk_trem INT NOT NULL,
    fk_linha ENUM('amarela', 'azul', 'vermelha', 'verde') NOT NULL,
    FOREIGN KEY (fk_trem) REFERENCES trem(id_trem),
    FOREIGN KEY (fk_linha) REFERENCES trem(id_linha)
);