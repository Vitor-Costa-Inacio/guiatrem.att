<?php
/**
 * Modelo de Usuário - Versão Expandida
 */

require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuario";

    // Propriedades básicas (existentes)
    public $id_usuario;
    public $nome_usuario;
    public $email_usuario;
    public $senha_usuario;
    public $funcao;
    public $data_criacao;
    public $data_atualizacao;

    // Novas propriedades para perfil
    public $telefone;
    public $departamento;
    public $cargo;
    public $data_admissao;
    public $endereco;
    public $complemento;
    public $cidade;
    public $estado;
    public $foto_perfil;
    public $notificacoes_email;
    public $modo_escuro;
    public $atualizacao_automatica;
    public $idioma;
    public $autenticacao_dois_fatores;
    public $conta_verificada;
    public $ultimo_login;

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

    // ========== NOVOS MÉTODOS PARA PERFIL ==========

    /**
     * Atualizar informações do perfil
     */
    public function atualizarPerfil() {
        // Primeiro precisamos verificar se a tabela usuario tem essas colunas
        // Se não tiver, vamos usar uma tabela separada usuario_perfil
        $query = "UPDATE " . $this->table_name . " 
                  SET nome_usuario = :nome_usuario,
                      telefone = :telefone,
                      departamento = :departamento,
                      cargo = :cargo,
                      data_admissao = :data_admissao,
                      endereco = :endereco,
                      complemento = :complemento,
                      cidade = :cidade,
                      estado = :estado,
                      foto_perfil = :foto_perfil,
                      data_atualizacao = CURRENT_TIMESTAMP
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->nome_usuario = htmlspecialchars(strip_tags($this->nome_usuario));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->departamento = htmlspecialchars(strip_tags($this->departamento));
        $this->cargo = htmlspecialchars(strip_tags($this->cargo));
        $this->endereco = htmlspecialchars(strip_tags($this->endereco));
        $this->complemento = htmlspecialchars(strip_tags($this->complemento));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->foto_perfil = htmlspecialchars(strip_tags($this->foto_perfil));

        // Bind dos parâmetros
        $stmt->bindParam(':nome_usuario', $this->nome_usuario);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':departamento', $this->departamento);
        $stmt->bindParam(':cargo', $this->cargo);
        $stmt->bindParam(':data_admissao', $this->data_admissao);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':complemento', $this->complemento);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':foto_perfil', $this->foto_perfil);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Se a tabela não tiver essas colunas, criar tabela usuario_perfil
            if ($e->getCode() == '42S22') { // Coluna não encontrada
                return $this->criarTabelaPerfilEAtualizar();
            }
            return false;
        }
    }

    /**
     * Criar tabela de perfil separada se não existir
     */
    private function criarTabelaPerfilEAtualizar() {
        // Criar tabela usuario_perfil
        $query = "CREATE TABLE IF NOT EXISTS usuario_perfil (
                    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    telefone VARCHAR(20),
                    departamento VARCHAR(100),
                    cargo VARCHAR(100),
                    data_admissao DATE,
                    endereco VARCHAR(255),
                    complemento VARCHAR(100),
                    cidade VARCHAR(100),
                    estado VARCHAR(2),
                    foto_perfil VARCHAR(255) DEFAULT 'default-avatar.png',
                    notificacoes_email BOOLEAN DEFAULT TRUE,
                    modo_escuro BOOLEAN DEFAULT FALSE,
                    atualizacao_automatica BOOLEAN DEFAULT TRUE,
                    idioma VARCHAR(10) DEFAULT 'pt-BR',
                    autenticacao_dois_fatores BOOLEAN DEFAULT FALSE,
                    conta_verificada BOOLEAN DEFAULT TRUE,
                    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
                    UNIQUE KEY uk_usuario_perfil (usuario_id)
                )";

        $stmt = $this->conn->prepare($query);
        if (!$stmt->execute()) {
            return false;
        }

        // Agora inserir/atualizar na nova tabela
        return $this->atualizarPerfilSeparado();
    }

    /**
     * Atualizar perfil na tabela separada
     */
    private function atualizarPerfilSeparado() {
        // Verificar se já existe perfil
        $queryCheck = "SELECT id_perfil FROM usuario_perfil WHERE usuario_id = ?";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(1, $this->id_usuario);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Atualizar
            $query = "UPDATE usuario_perfil 
                      SET telefone = :telefone,
                          departamento = :departamento,
                          cargo = :cargo,
                          data_admissao = :data_admissao,
                          endereco = :endereco,
                          complemento = :complemento,
                          cidade = :cidade,
                          estado = :estado,
                          foto_perfil = :foto_perfil,
                          data_atualizacao = CURRENT_TIMESTAMP
                      WHERE usuario_id = :usuario_id";
        } else {
            // Inserir
            $query = "INSERT INTO usuario_perfil 
                      SET usuario_id = :usuario_id,
                          telefone = :telefone,
                          departamento = :departamento,
                          cargo = :cargo,
                          data_admissao = :data_admissao,
                          endereco = :endereco,
                          complemento = :complemento,
                          cidade = :cidade,
                          estado = :estado,
                          foto_perfil = :foto_perfil";
        }

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':usuario_id', $this->id_usuario);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':departamento', $this->departamento);
        $stmt->bindParam(':cargo', $this->cargo);
        $stmt->bindParam(':data_admissao', $this->data_admissao);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':complemento', $this->complemento);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':foto_perfil', $this->foto_perfil);

        return $stmt->execute();
    }

    /**
     * Atualizar preferências do usuário
     */
    public function atualizarPreferencias() {
        $query = "UPDATE usuario_perfil 
                  SET notificacoes_email = :notificacoes_email,
                      modo_escuro = :modo_escuro,
                      atualizacao_automatica = :atualizacao_automatica,
                      idioma = :idioma,
                      autenticacao_dois_fatores = :autenticacao_dois_fatores,
                      data_atualizacao = CURRENT_TIMESTAMP
                  WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':notificacoes_email', $this->notificacoes_email, PDO::PARAM_BOOL);
        $stmt->bindParam(':modo_escuro', $this->modo_escuro, PDO::PARAM_BOOL);
        $stmt->bindParam(':atualizacao_automatica', $this->atualizacao_automatica, PDO::PARAM_BOOL);
        $stmt->bindParam(':idioma', $this->idioma);
        $stmt->bindParam(':autenticacao_dois_fatores', $this->autenticacao_dois_fatores, PDO::PARAM_BOOL);
        $stmt->bindParam(':usuario_id', $this->id_usuario);

        return $stmt->execute();
    }

    /**
     * Buscar perfil completo do usuário
     */
    public function buscarPerfilCompleto($id) {
        // Primeiro buscar dados básicos
        if (!$this->buscarPorId($id)) {
            return false;
        }

        // Agora buscar dados do perfil
        $query = "SELECT * FROM usuario_perfil WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->telefone = $row['telefone'];
            $this->departamento = $row['departamento'];
            $this->cargo = $row['cargo'];
            $this->data_admissao = $row['data_admissao'];
            $this->endereco = $row['endereco'];
            $this->complemento = $row['complemento'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->foto_perfil = $row['foto_perfil'];
            $this->notificacoes_email = (bool)$row['notificacoes_email'];
            $this->modo_escuro = (bool)$row['modo_escuro'];
            $this->atualizacao_automatica = (bool)$row['atualizacao_automatica'];
            $this->idioma = $row['idioma'];
            $this->autenticacao_dois_fatores = (bool)$row['autenticacao_dois_fatores'];
            $this->conta_verificada = (bool)$row['conta_verificada'];
        }

        return true;
    }

    /**
     * Verificar senha atual
     */
    public function verificarSenha($senha_atual) {
        $query = "SELECT senha_usuario FROM " . $this->table_name . " WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return password_verify($senha_atual, $row['senha_usuario']);
        }
        return false;
    }

    /**
     * Alterar senha
     */
    public function alterarSenha($nova_senha_hash) {
        $query = "UPDATE " . $this->table_name . " 
                  SET senha_usuario = :senha_usuario,
                      data_atualizacao = CURRENT_TIMESTAMP
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':senha_usuario', $nova_senha_hash);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }

    /**
     * Atualizar último login
     */
    public function atualizarUltimoLogin() {
        // Primeiro verificar se a coluna existe
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET ultimo_login = CURRENT_TIMESTAMP
                      WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $this->id_usuario);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Se a coluna não existir, adicionar
            if ($e->getCode() == '42S22') {
                $this->adicionarColunaUltimoLogin();
                return $this->atualizarUltimoLogin();
            }
            return false;
        }
    }

    /**
     * Adicionar coluna ultimo_login se não existir
     */
    private function adicionarColunaUltimoLogin() {
        $query = "ALTER TABLE " . $this->table_name . " 
                  ADD COLUMN ultimo_login TIMESTAMP NULL AFTER data_atualizacao";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>