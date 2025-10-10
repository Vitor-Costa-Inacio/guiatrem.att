create database gerenciamento_trem;

use gerenciamento_trem;

create table trensGeral(
    id_trem int primary key not null auto_increment,
    nomeTrem varchar(50) not null,
    statusTrem varchar(50) not null,
    capacidadeTrem int not null,
    linha_term varchar(1000) not null,
);