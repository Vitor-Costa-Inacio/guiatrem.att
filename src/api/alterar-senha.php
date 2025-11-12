<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';
require_once '../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Simulação de usuário logado (em produção, usar sessão/JWT)
$usuario_id = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $senha_atual = $dados['senha_atual'] ?? '';
        $nova_senha = $dados['nova_senha'] ?? '';
        $confirmar_senha = $dados['confirmar_senha'] ?? '';

        // Validações
        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios']);
            exit;
        }

        if ($nova_senha !== $confirmar_senha) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'A nova senha e a confirmação não coincidem']);
            exit;
        }

        // Validar força da senha
        $validacao_senha = $usuario->validarSenha($nova_senha);
        if ($validacao_senha !== true) {
            echo json_encode(['sucesso' => false, 'mensagem' => $validacao_senha]);
            exit;
        }

        // Verificar senha atual
        $usuario->id_usuario = $usuario_id;
        if (!$usuario->verificarSenha($senha_atual)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha atual incorreta']);
            exit;
        }

        // Gerar hash da nova senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualizar senha
        if ($usuario->alterarSenha($nova_senha_hash)) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao alterar senha']);
        }

    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
    }
}
?>