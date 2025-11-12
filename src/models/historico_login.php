<?php
/**
 * Modelo de Histórico de Login
 */

require_once __DIR__ . '/../../config/database.php';

class HistoricoLogin {
    private $conn;
    private $table_name = "historico_login";

    public $id;
    public $usuario_id;
    public $data_hora;
    public $dispositivo;
    public $navegador;
    public $sistema_operacional;
    public $localizacao;
    public $endereco_ip;
    public $status;
    public $user_agent;

    public function __construct($db) {
        $this->conn = $db;
        $this->criarTabelaSeNaoExistir();
    }

    /**
     * Criar tabela se não existir
     */
    private function criarTabelaSeNaoExistir() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    dispositivo VARCHAR(255),
                    navegador VARCHAR(100),
                    sistema_operacional VARCHAR(100),
                    localizacao VARCHAR(255),
                    endereco_ip VARCHAR(45),
                    status ENUM('bem-sucedido', 'falhou', 'suspeito') DEFAULT 'bem-sucedido',
                    user_agent TEXT,
                    FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
                    INDEX idx_usuario_data (usuario_id, data_hora)
                )";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    /**
     * Registrar tentativa de login
     */
    public function registrarLogin() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET usuario_id = :usuario_id,
                      data_hora = CURRENT_TIMESTAMP,
                      dispositivo = :dispositivo,
                      navegador = :navegador,
                      sistema_operacional = :sistema_operacional,
                      localizacao = :localizacao,
                      endereco_ip = :endereco_ip,
                      status = :status,
                      user_agent = :user_agent";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':dispositivo', $this->dispositivo);
        $stmt->bindParam(':navegador', $this->navegador);
        $stmt->bindParam(':sistema_operacional', $this->sistema_operacional);
        $stmt->bindParam(':localizacao', $this->localizacao);
        $stmt->bindParam(':endereco_ip', $this->endereco_ip);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':user_agent', $this->user_agent);

        return $stmt->execute();
    }

    /**
     * Buscar histórico por usuário
     */
    public function buscarPorUsuario($usuario_id, $limite = 50) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = ? 
                  ORDER BY data_hora DESC 
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usuario_id);
        $stmt->bindParam(2, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Buscar estatísticas de login
     */
    public function buscarEstatisticas($usuario_id) {
        $query = "SELECT 
                    COUNT(*) as total_logins,
                    COUNT(DISTINCT dispositivo) as total_dispositivos,
                    COUNT(DISTINCT localizacao) as total_localizacoes,
                    MAX(data_hora) as ultimo_login
                  FROM " . $this->table_name . " 
                  WHERE usuario_id = ? AND status = 'bem-sucedido'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>