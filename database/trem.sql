-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 29/10/2025 às 11:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `trem`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `estacoes`
--

CREATE TABLE `estacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `endereco` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('ativa','inativa') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estacoes`
--

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `rotas`
--

CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `distancia_km` decimal(8,2) DEFAULT NULL,
  `tempo_estimado_min` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('ativa','inativa') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rotas`
--

INSERT INTO `rotas` (`id`, `nome`, `distancia_km`, `tempo_estimado_min`, `data_criacao`, `status`) VALUES
(1, 'Rota Sul-Norte', 3511.25, 3511, '2025-10-27 15:36:21', 'ativa'),
(2, 'Ruta Costeira', 2850.75, 2850, '2025-10-29 10:00:00', 'ativa'),
(3, 'Rota Centro', 1250.30, 1250, '2025-10-29 10:00:00', 'ativa'),
(4, 'Rota Sudeste', 980.45, 980, '2025-10-29 10:00:00', 'ativa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rota_estacoes`
--

CREATE TABLE `rota_estacoes` (
  `id` int(11) NOT NULL,
  `id_rota` int(11) NOT NULL,
  `id_estacao` int(11) NOT NULL,
  `ordem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rota_estacoes`
--

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `linhas`
--

CREATE TABLE `linhas` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cor` varchar(20) NOT NULL,
  `status` enum('ativa','inativa') DEFAULT 'ativa',
  `id_rota` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `linhas`
--

INSERT INTO `linhas` (`id`, `nome`, `cor`, `status`, `id_rota`) VALUES
(1, 'Linha Amarela', 'amarela', 'ativa', 1),
(2, 'Linha Azul', 'azul', 'ativa', 2),
(3, 'Linha Verde', 'verde', 'ativa', 3),
(4, 'Linha Vermelha', 'vermelha', 'ativa', 4);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `estacoes`
--
ALTER TABLE `estacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `linhas`
--
ALTER TABLE `linhas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rota` (`id_rota`);

--
-- Índices de tabela `rotas`
--
ALTER TABLE `rotas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rota_estacoes`
--
ALTER TABLE `rota_estacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rota` (`id_rota`),
  ADD KEY `id_estacao` (`id_estacao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `estacoes`
--
ALTER TABLE `estacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `linhas`
--
ALTER TABLE `linhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `rotas`
--
ALTER TABLE `rotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `rota_estacoes`
--
ALTER TABLE `rota_estacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `linhas`
--
ALTER TABLE `linhas`
  ADD CONSTRAINT `linhas_ibfk_1` FOREIGN KEY (`id_rota`) REFERENCES `rotas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `rota_estacoes`
--
ALTER TABLE `rota_estacoes`
  ADD CONSTRAINT `rota_estacoes_ibfk_1` FOREIGN KEY (`id_rota`) REFERENCES `rotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rota_estacoes_ibfk_2` FOREIGN KEY (`id_estacao`) REFERENCES `estacoes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;