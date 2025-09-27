<?php
/**
 * Script para verificar sessão de usuário
 */

// Headers para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inicializar sessão
session_start();

/**
 * Verifica se o usuário está logado
 */
function verificarSessao() {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        return false;
    }

    // Verificar se a sessão não expirou (24 horas)
    if (isset($_SESSION['login_time'])) {
        $tempo_limite = 24 * 60 * 60; // 24 horas em segundos
        if ((time() - $_SESSION['login_time']) > $tempo_limite) {
            // Sessão expirada
            session_destroy();
            return false;
        }
    }

    return true;
}

/**
 * Obter dados do usuário da sessão
 */
function obterDadosUsuario() {
    if (!verificarSessao()) {
        return null;
    }

    return array(
        "id" => $_SESSION['usuario_id'],
        "nome" => $_SESSION['usuario_nome'],
        "email" => $_SESSION['usuario_email'],
        "funcao" => $_SESSION['usuario_funcao'],
        "login_time" => $_SESSION['login_time']
    );
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verificarSessao()) {
        $usuario = obterDadosUsuario();
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "logado" => true,
            "usuario" => $usuario
        ));
    } else {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "logado" => false,
            "message" => "Usuário não está logado ou sessão expirou."
        ));
    }
} else {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Método não permitido."));
}
?>

