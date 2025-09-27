<?php
/**
 * Modelo de Trem
 */

require_once __DIR__ . '/../../config/database.php';

class Trem {
    private $conn;
    private $table_name = "trem";

    public $id_trem;
    public $linha_trem;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Listar todos os trens
     */
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY linha_trem";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Buscar trem por ID
     */
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_trem = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $num = $stmt->rowCount();
        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_trem = $row['id_trem'];
            $this->linha_trem = $row['linha_trem'];
            return true;
        }
        return false;
    }

    /**
     * Buscar trens por linha
     */
    public function buscarPorLinha($linha) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE linha_trem LIKE ? ORDER BY id_trem";
        $stmt = $this->conn->prepare($query);
        $linha_param = "%" . $linha . "%";
        $stmt->bindParam(1, $linha_param);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Contar trens por status (baseado nas manutenções)
     */
    public function contarPorStatus() {
        $query = "SELECT 
                    t.linha_trem,
                    COUNT(t.id_trem) as total_trens,
                    COUNT(CASE WHEN m.status_manutencao = 'Em andamento' THEN 1 END) as em_manutencao,
                    COUNT(CASE WHEN m.status_manutencao IS NULL OR m.status_manutencao = 'Concluída' THEN 1 END) as operando
                  FROM " . $this->table_name . " t
                  LEFT JOIN manutencao m ON t.id_trem = m.fk_trem AND m.status_manutencao = 'Em andamento'
                  GROUP BY t.linha_trem
                  ORDER BY t.linha_trem";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

