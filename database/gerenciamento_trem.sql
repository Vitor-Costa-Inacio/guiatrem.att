create database gerenciamento_trem;

use gerenciamento_trem;

-- Tabela de Trens
CREATE TABLE trens (
    id_trem INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    numero_trem VARCHAR(20) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    linha VARCHAR(50) NOT NULL,
    capacidade INT NOT NULL,
    status_trem ENUM('ativo', 'manutencao', 'inativo') DEFAULT 'ativo',
    data_ultima_manutencao DATE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Manutenções
CREATE TABLE manutencoes (
    id_manutencao INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_trem INT NOT NULL,
    tipo_manutencao ENUM('preventiva', 'corretiva') NOT NULL,
    prioridade ENUM('A', 'B', 'C') NOT NULL,
    servico_solicitado TEXT NOT NULL,
    status_manutencao ENUM('nao_iniciada', 'em_andamento', 'concluida') DEFAULT 'nao_iniciada',
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_prevista DATE,
    data_conclusao DATE,
    custo_estimado DECIMAL(10,2),
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem)
);

-- Tabela de Histórico de Manutenções
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

-- Inserir dados iniciais
INSERT INTO trens (numero_trem, modelo, linha, capacidade, status_trem, data_ultima_manutencao) VALUES
('TR001', 'Modelo A', 'Linha Azul', 200, 'ativo', '2024-01-15'),
('TR002', 'Modelo B', 'Linha Verde', 180, 'manutencao', '2024-02-20'),
('TR003', 'Modelo C', 'Linha Vermelha', 220, 'ativo', '2024-03-10'),
('TR004', 'Modelo A', 'Linha Amarela', 200, 'ativo', '2024-01-30');

INSERT INTO manutencoes (id_trem, tipo_manutencao, prioridade, servico_solicitado, status_manutencao, data_prevista) VALUES
(1, 'preventiva', 'B', 'Revisão periódica do sistema de freios', 'concluida', '2024-04-25'),
(2, 'corretiva', 'A', 'Reparo no sistema elétrico', 'em_andamento', '2024-04-28'),
(3, 'preventiva', 'C', 'Troca de óleo e filtros', 'nao_iniciada', '2024-05-02');