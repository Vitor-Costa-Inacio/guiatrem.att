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