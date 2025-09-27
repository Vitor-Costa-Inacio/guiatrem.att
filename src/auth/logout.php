<?php
/**
 * Script de logout de usuário - Versão melhorada
 */

// Headers de segurança e CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Headers de segurança adicionais
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Tratar requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inicializar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log da tentativa de logout (opcional - para auditoria)
function logLogoutAttempt($user_id = null, $success = false) {
    $log_entry = date('Y-m-d H:i:s') . " - Logout attempt - User ID: " . ($user_id ?? 'unknown') . " - Success: " . ($success ? 'yes' : 'no') . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
    // Descomente a linha abaixo se quiser salvar logs
    // file_put_contents(__DIR__ . '/../../logs/logout.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Verificar se é uma requisição POST ou GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    try {
        // Capturar dados da sessão antes de destruir
        $user_id = $_SESSION['usuario_id'] ?? null;
        $user_name = $_SESSION['usuario_nome'] ?? null;
        $tinha_sessao = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
        
        // Log da tentativa
        logLogoutAttempt($user_id, true);
        
        // Limpar dados específicos do usuário da sessão
        $session_keys_to_clear = [
            'usuario_id',
            'usuario_nome', 
            'usuario_email',
            'usuario_tipo',
            'usuario_permissoes',
            'last_activity',
            'login_time',
            'csrf_token'
        ];
        
        foreach ($session_keys_to_clear as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }
        
        // Destruir todas as variáveis de sessão
        $_SESSION = array();
        
        // Limpar cookies de sessão e outros cookies relacionados
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Limpar outros cookies que podem conter dados do usuário
        $cookies_to_clear = [
            'user_token',
            'auth_token', 
            'remember_token',
            'user_preferences',
            'last_page'
        ];
        
        foreach ($cookies_to_clear as $cookie_name) {
            if (isset($_COOKIE[$cookie_name])) {
                setcookie($cookie_name, '', time() - 42000, '/');
                setcookie($cookie_name, '', time() - 42000, '/', $_SERVER['HTTP_HOST']);
            }
        }
        
        // Destruir a sessão
        session_destroy();
        
        // Regenerar ID da sessão para segurança
/*         session_start();
        session_regenerate_id(true);
        session_destroy(); */
        
        // Resposta de sucesso
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => $tinha_sessao ? "Logout realizado com sucesso!" : "Nenhuma sessão ativa encontrada.",
            "user_name" => $user_name,
            "timestamp" => date('Y-m-d H:i:s'),
            "redirect" => "login.html"
        ));
        
    } catch (Exception $e) {
        // Log do erro
        error_log("Erro no logout: " . $e->getMessage());
        
        // Mesmo com erro, tentar limpar a sessão
        session_destroy();
        
        http_response_code(500);
        echo json_encode(array(
            "success" => false, 
            "message" => "Erro interno no servidor durante o logout.",
            "error_code" => "LOGOUT_ERROR"
        ));
    }
    
} else {
    http_response_code(405);
    echo json_encode(array(
        "success" => false, 
        "message" => "Método não permitido. Use POST ou GET.",
        "allowed_methods" => ["POST", "GET"]
    ));
}
?>

