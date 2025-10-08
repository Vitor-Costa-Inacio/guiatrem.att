CREATE DATABASE sistemas_sensores;

CREATE table sensores(
    id_sensor int AUTO_INCREMENT PRIMARY key, /identificador do sensor/
    sensor_01 VARCHAR(100) not null, /nome de identificação (nome do sensor sendo usado)/
    tipo_sensor VARCHAR(50)not null, 
    loca_sensor VARCHAR(100), /local onde ele sera colocado (s1, s2, s3, s4)/
    status_sensor ENUM ('ativo', 'inativo') default 'ativo' /funcionalidade, funcionando ou nn/
)