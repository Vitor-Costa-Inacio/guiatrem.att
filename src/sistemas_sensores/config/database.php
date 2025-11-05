<?php
/**
 * CONFIGURAÇÃO DE BANCO DE DADOS
 * Arquivo: config/database.php
 */

class Database {
    private $connection;
    
    // CONFIGURAÇÕES - AJUSTE CONFORME SEU AMBIENTE
    private $host = 'localhost';
    private $dbname = 'sistemas_sensores';
    private $username = 'root';
    private $password = '';  // Senha do seu MySQL
    
    public function __construct() {
        try {
            // Criar conexão PDO
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Debug: Mostrar se conectou com sucesso (remover depois)
            error_log("✅ Conexão com banco estabelecida com sucesso!");
            
        } catch (PDOException $e) {
            // Log detalhado do erro
            error_log("❌ ERRO DE CONEXÃO: " . $e->getMessage());
            error_log("📋 Detalhes: Host={$this->host}, DB={$this->dbname}, User={$this->username}");
            
            die("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
?>