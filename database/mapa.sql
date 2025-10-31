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
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estacoes`
--

INSERT INTO `estacoes` (`id`, `nome`, `latitude`, `longitude`, `endereco`, `data_criacao`) VALUES
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `rotas`
--

CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `distancia_km` decimal(8,2) DEFAULT NULL,
  `tempo_estimado_min` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rotas`
--

INSERT INTO `rotas` (`id`, `nome`, `distancia_km`, `tempo_estimado_min`, `data_criacao`) VALUES
(1, 'Rota Sul-Norte', 3511.25, 3511, '2025-10-27 15:36:21');

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
(11, 1, 2, 10);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `estacoes`
--
ALTER TABLE `estacoes`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `rotas`
--
ALTER TABLE `rotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `rota_estacoes`
--
ALTER TABLE `rota_estacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

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