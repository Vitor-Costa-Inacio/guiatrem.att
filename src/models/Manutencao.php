<?php
/**
 * Modelo de Manutenção
 */

require_once __DIR__ . '/../../config/database.php';

class Manutencao {
    private $conn;
    private $table_name = "manutencao";

    public $id_manutencao;
    public $data_manutencao;
    public $tipo_manutencao;
    public $descricao_manutencao;
    public $observacao_manutencao;
    public $status_manutencao;
    public $fk_trem;
    public $fk_usuario;
    public $data_criacao;
    public $data_atualizacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Criar nova manutenção
     */
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET data_manutencao=:data_manutencao, 
                      tipo_manutencao=:tipo_manutencao, 
                      descricao_manutencao=:descricao_manutencao, 
                      observacao_manutencao=:observacao_manutencao,
                      status_manutencao=:status_manutencao,
                      fk_trem=:fk_trem,
                      fk_usuario=:fk_usuario";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->data_manutencao = htmlspecialchars(strip_tags($this->data_manutencao));
        $this->tipo_manutencao = htmlspecialchars(strip_tags($this->tipo_manutencao));
        $this->descricao_manutencao = htmlspecialchars(strip_tags($this->descricao_manutencao));
        $this->observacao_manutencao = htmlspecialchars(strip_tags($this->observacao_manutencao));
        $this->status_manutencao = htmlspecialchars(strip_tags($this->status_manutencao));

        // Bind dos parâmetros
        $stmt->bindParam(":data_manutencao", $this->data_manutencao);
        $stmt->bindParam(":tipo_manutencao", $this->tipo_manutencao);
        $stmt->bindParam(":descricao_manutencao", $this->descricao_manutencao);
        $stmt->bindParam(":observacao_manutencao", $this->observacao_manutencao);
        $stmt->bindParam(":status_manutencao", $this->status_manutencao);
        $stmt->bindParam(":fk_trem", $this->fk_trem);
        $stmt->bindParam(":fk_usuario", $this->fk_usuario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Listar manutenções com filtros
     */
    public function listar($filtros = array()) {
        $query = "SELECT m.*, t.linha_trem, u.nome_usuario 
                  FROM " . $this->table_name . " m
                  LEFT JOIN trem t ON m.fk_trem = t.id_trem
                  LEFT JOIN usuario u ON m.fk_usuario = u.id_usuario
                  WHERE 1=1";

        $params = array();

        if (!empty($filtros['linha'])) {
            $query .= " AND t.linha_trem LIKE ?";
            $params[] = "%" . $filtros['linha'] . "%";
        }

        if (!empty($filtros['trem_id'])) {
            $query .= " AND m.fk_trem = ?";
            $params[] = $filtros['trem_id'];
        }

        if (!empty($filtros['tipo'])) {
            $query .= " AND m.tipo_manutencao = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['status'])) {
            $query .= " AND m.status_manutencao = ?";
            $params[] = $filtros['status'];
        }

        if (!empty($filtros['data_inicio'])) {
            $query .= " AND m.data_manutencao >= ?";
            $params[] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $query .= " AND m.data_manutencao <= ?";
            $params[] = $filtros['data_fim'];
        }

        $query .= " ORDER BY m.data_manutencao DESC, m.data_criacao DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Buscar manutenção por ID
     */
    public function buscarPorId($id) {
        $query = "SELECT m.*, t.linha_trem, u.nome_usuario 
                  FROM " . $this->table_name . " m
                  LEFT JOIN trem t ON m.fk_trem = t.id_trem
                  LEFT JOIN usuario u ON m.fk_usuario = u.id_usuario
                  WHERE m.id_manutencao = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $num = $stmt->rowCount();
        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_manutencao = $row['id_manutencao'];
            $this->data_manutencao = $row['data_manutencao'];
            $this->tipo_manutencao = $row['tipo_manutencao'];
            $this->descricao_manutencao = $row['descricao_manutencao'];
            $this->observacao_manutencao = $row['observacao_manutencao'];
            $this->status_manutencao = $row['status_manutencao'];
            $this->fk_trem = $row['fk_trem'];
            $this->fk_usuario = $row['fk_usuario'];
            return true;
        }
        return false;
    }

    /**
     * Atualizar status da manutenção
     */
    public function atualizarStatus($id, $status, $observacao = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status_manutencao = :status";
        
        if ($observacao !== null) {
            $query .= ", observacao_manutencao = :observacao";
        }
        
        $query .= " WHERE id_manutencao = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        
        if ($observacao !== null) {
            $stmt->bindParam(":observacao", $observacao);
        }

        return $stmt->execute();
    }

    /**
     * Obter estatísticas de manutenção
     */
    public function obterEstatisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status_manutencao = 'Em andamento' THEN 1 END) as em_andamento,
                    COUNT(CASE WHEN status_manutencao = 'Concluída' THEN 1 END) as concluidas,
                    COUNT(CASE WHEN status_manutencao = 'Pendente' THEN 1 END) as pendentes,
                    COUNT(CASE WHEN tipo_manutencao = 'Preventiva' THEN 1 END) as preventivas,
                    COUNT(CASE WHEN tipo_manutencao = 'Corretiva' THEN 1 END) as corretivas
                  FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obter manutenções recentes
     */
    public function obterRecentes($limite = 5) {
        $query = "SELECT m.*, t.linha_trem, u.nome_usuario 
                  FROM " . $this->table_name . " m
                  LEFT JOIN trem t ON m.fk_trem = t.id_trem
                  LEFT JOIN usuario u ON m.fk_usuario = u.id_usuario
                  ORDER BY m.data_criacao DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>

