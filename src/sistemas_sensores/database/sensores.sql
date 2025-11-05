-- Criar database (se não existir)
CREATE DATABASE IF NOT EXISTS sistemas_sensores 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Selecionar o database
USE sistemas_sensores;


-- TABELA: sensores

CREATE TABLE IF NOT EXISTS sensores (
    id_sensor INT AUTO_INCREMENT PRIMARY KEY,
    nome_sensor VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(50) NOT NULL,
    localizacao VARCHAR(200) NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- DADOS DE EXEMPLO

INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status) VALUES
('Sensor Temperatura 001', 'Temperatura', 'Vagão A - Compartimento Motor', 'ativo'),
('Sensor Vibração 002', 'Vibração', 'Vagão B - Eixo Dianteiro', 'ativo'),
('Sensor Pressão 003', 'Pressão', 'Sistema de Freio - Vagão C', 'inativo'),
('Sensor Proximidade 004', 'Proximidade', 'Porta Principal - Vagão A', 'ativo');


-- CONFIRMAR CRIAÇÃO

SELECT '✅ Banco de dados e tabela criados com sucesso!' as Status;