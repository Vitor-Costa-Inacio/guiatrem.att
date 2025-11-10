<?php
/**
 * Configuração de conexão com o banco de dados
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'guiatrem';
    private $username = 'root';
    private $password = 'root';
    private $conn;

    /**
     * Conecta ao banco de dados
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>