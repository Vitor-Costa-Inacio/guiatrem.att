<?php
/**
 * Script de cadastro de usuário
 */

// Headers para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir arquivos necessários
include_once '../models/Usuario.php';

// Inicializar sessão
session_start();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Método não permitido."));
    exit();
}

// Obter dados do POST
$data = json_decode(file_get_contents("php://input"));

// Se não há dados JSON, tentar $_POST
if (!$data) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
} else {
    $nome = $data->nome ?? '';
    $email = $data->email ?? '';
    $senha = $data->senha ?? '';
    $confirmar_senha = $data->confirmar_senha ?? '';
}

// Validar se todos os campos foram preenchidos
if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Todos os campos são obrigatórios."));
    exit();
}

// Verificar se as senhas coincidem
if ($senha !== $confirmar_senha) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "As senhas não coincidem."));
    exit();
}

// Conectar ao banco de dados
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Erro de conexão com o banco de dados."));
    exit();
}

// Instanciar objeto usuário
$usuario = new Usuario($db);

// Validar nome
$validacao_nome = $usuario->validarNome($nome);
if ($validacao_nome !== true) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => $validacao_nome));
    exit();
}

// Validar email
$validacao_email = $usuario->validarEmail($email);
if ($validacao_email !== true) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => $validacao_email));
    exit();
}

// Validar senha
$validacao_senha = $usuario->validarSenha($senha);
if ($validacao_senha !== true) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => $validacao_senha));
    exit();
}

// Verificar se o email já existe
$usuario->email_usuario = $email;
if ($usuario->emailExiste()) {
    http_response_code(409);
    echo json_encode(array("success" => false, "message" => "Este e-mail já está cadastrado."));
    exit();
}

// Definir propriedades do usuário
$usuario->nome_usuario = $nome;
$usuario->email_usuario = $email;
$usuario->senha_usuario = $senha;
$usuario->funcao = 'usuario'; // Função padrão

// Tentar cadastrar o usuário
if ($usuario->cadastrar()) {
    http_response_code(201);
    echo json_encode(array(
        "success" => true, 
        "message" => "Usuário cadastrado com sucesso! Você será redirecionado para a página de login."
    ));
} else {
    http_response_code(503);
    echo json_encode(array("success" => false, "message" => "Não foi possível cadastrar o usuário. Tente novamente."));
}
?>

