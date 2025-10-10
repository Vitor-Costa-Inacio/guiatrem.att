CREATE DATABASE IF NOT EXISTS sistemas_sensores;
USE sistemas_sensores;

CREATE TABLE IF NOT EXISTS sensores (
    id_sensor INT AUTO_INCREMENT PRIMARY KEY,
    sensor_01 VARCHAR(100) NOT NULL,
    tipo_sensor VARCHAR(100) NOT NULL,
    loca_sensor VARCHAR(200),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);