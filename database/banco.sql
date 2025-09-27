CREATE DATABASE IF NOT EXISTS guiatrem;

USE guiatrem;

CREATE TABLE usuario(
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome_usuario VARCHAR(100) NOT NULL,
    email_usuario VARCHAR(100) NOT NULL UNIQUE,
    senha_usuario VARCHAR(255) NOT NULL,
    funcao VARCHAR(50) NOT NULL DEFAULT 'usuario',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE trem(
    id_trem INT PRIMARY KEY AUTO_INCREMENT,
    linha_trem VARCHAR(50) NOT NULL
);

CREATE TABLE manutencao(
    id_manutencao INT PRIMARY KEY AUTO_INCREMENT,
    data_manutencao DATE NOT NULL,
    tipo_manutencao VARCHAR(50),
    descricao_manutencao VARCHAR(100) NOT NULL,
    observacao_manutencao VARCHAR(200),
    status_manutencao VARCHAR(50) NOT NULL,
    fk_trem INT NOT NULL, 
    fk_usuario INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_trem) REFERENCES trem(id_trem),
    FOREIGN KEY (fk_usuario) REFERENCES usuario(id_usuario)
);

-- Inserir dados de exemplo para trens
INSERT INTO trem (linha_trem) VALUES
('Linha Amarela'),
('Linha Azul'),
('Linha Verde'),
('Linha Vermelha');

