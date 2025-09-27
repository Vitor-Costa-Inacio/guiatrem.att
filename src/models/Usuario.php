<?php
/**
 * Modelo de Usuário
 */

require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuario";

    public $id_usuario;
    public $nome_usuario;
    public $email_usuario;
    public $senha_usuario;
    public $funcao;
    public $data_criacao;
    public $data_atualizacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Cadastra um novo usuário
     */
    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome_usuario=:nome_usuario, 
                      email_usuario=:email_usuario, 
                      senha_usuario=:senha_usuario, 
                      funcao=:funcao";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->nome_usuario = htmlspecialchars(strip_tags($this->nome_usuario));
        $this->email_usuario = htmlspecialchars(strip_tags($this->email_usuario));
        $this->senha_usuario = password_hash($this->senha_usuario, PASSWORD_DEFAULT);
        $this->funcao = htmlspecialchars(strip_tags($this->funcao));

        // Bind dos parâmetros
        $stmt->bindParam(":nome_usuario", $this->nome_usuario);
        $stmt->bindParam(":email_usuario", $this->email_usuario);
        $stmt->bindParam(":senha_usuario", $this->senha_usuario);
        $stmt->bindParam(":funcao", $this->funcao);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o email já existe
     */
    public function emailExiste() {
        $query = "SELECT id_usuario, nome_usuario, senha_usuario, funcao 
                  FROM " . $this->table_name . " 
                  WHERE email_usuario = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email_usuario);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_usuario = $row['id_usuario'];
            $this->nome_usuario = $row['nome_usuario'];
            $this->senha_usuario = $row['senha_usuario'];
            $this->funcao = $row['funcao'];
            return true;
        }

        return false;
    }

    /**
     * Valida o nome do usuário
     */
    public function validarNome($nome) {
        if (empty($nome) || strlen($nome) < 2) {
            return "Nome deve ter pelo menos 2 caracteres.";
        }
        if (strlen($nome) > 100) {
            return "Nome deve ter no máximo 100 caracteres.";
        }
        return true;
    }

    /**
     * Valida o email do usuário
     */
    public function validarEmail($email) {
        if (empty($email)) {
            return "E-mail é obrigatório.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Formato de e-mail inválido.";
        }
        if (strlen($email) > 100) {
            return "E-mail deve ter no máximo 100 caracteres.";
        }
        return true;
    }

    /**
     * Valida a senha do usuário
     */
    public function validarSenha($senha) {
        if (empty($senha)) {
            return "Senha é obrigatória.";
        }
        if (strlen($senha) < 8) {
            return "Senha deve ter pelo menos 8 caracteres.";
        }
        if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)/', $senha)) {
            return "Senha deve conter pelo menos uma letra e um número.";
        }
        return true;
    }

    /**
     * Busca usuário por ID
     */
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_usuario = $row['id_usuario'];
            $this->nome_usuario = $row['nome_usuario'];
            $this->email_usuario = $row['email_usuario'];
            $this->funcao = $row['funcao'];
            $this->data_criacao = $row['data_criacao'];
            $this->data_atualizacao = $row['data_atualizacao'];
            return true;
        }

        return false;
    }
}
?>

