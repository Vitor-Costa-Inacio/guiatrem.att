<?php
/**
 * Script de login de usuário
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
    $email = $_POST['email'] ?? $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? $_POST['password'] ?? '';
} else {
    $email = $data->email ?? $data->login ?? '';
    $senha = $data->senha ?? $data->password ?? '';
}

// Validar se todos os campos foram preenchidos
if (empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "E-mail e senha são obrigatórios."));
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

// Verificar se o email existe
$usuario->email_usuario = $email;
if (!$usuario->emailExiste()) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "E-mail ou senha incorretos."));
    exit();
}

// Verificar a senha
if (!password_verify($senha, $usuario->senha_usuario)) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "E-mail ou senha incorretos."));
    exit();
}

// Login bem-sucedido - criar sessão
$_SESSION['usuario_id'] = $usuario->id_usuario;
$_SESSION['usuario_nome'] = $usuario->nome_usuario;
$_SESSION['usuario_email'] = $usuario->email_usuario;
$_SESSION['usuario_funcao'] = $usuario->funcao;
$_SESSION['login_time'] = time();

// Resposta de sucesso
http_response_code(200);
echo json_encode(array(
    "success" => true,
    "message" => "Login realizado com sucesso!",
    "usuario" => array(
        "id" => $usuario->id_usuario,
        "nome" => $usuario->nome_usuario,
        "email" => $usuario->email_usuario,
        "funcao" => $usuario->funcao
    ),
    "redirect" => "dashboard.html"
));
?>

