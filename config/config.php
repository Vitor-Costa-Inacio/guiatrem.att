<?php
/**
 * Configurações gerais do sistema
 */

// Definir timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro (para desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mudar para 1 em HTTPS

// Constantes do sistema
define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH', BASE_PATH . '/src');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Configurações de CORS
function configurarCORS() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    
    // Responder a requisições OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Função para incluir arquivos necessários
function incluirArquivos() {
    require_once CONFIG_PATH . '/database.php';
    require_once SRC_PATH . '/models/Usuario.php';
}

// Função para resposta JSON padronizada
function responderJSON($success, $message, $data = null, $http_code = 200) {
    http_response_code($http_code);
    $response = array(
        "success" => $success,
        "message" => $message
    );
    
    if ($data !== null) {
        $response["data"] = $data;
    }
    
    echo json_encode($response);
    exit();
}

// Função para validar requisição POST
function validarPOST() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJSON(false, "Método não permitido.", null, 405);
    }
}

// Função para obter dados da requisição
function obterDadosRequisicao() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        return $_POST;
    }
    
    return $data;
} 
?>