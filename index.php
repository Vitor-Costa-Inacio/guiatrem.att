<?php
/**
 * Ponto de entrada principal da aplicação Guia Trem
 */

// Incluir configurações
require_once 'config/config.php';

// Configurar CORS
configurarCORS();

// Inicializar sessão
session_start();

// Verificar se o usuário está logado
include_once 'src/auth/check_session.php';

if (verificarSessao()) {
    // Se estiver logado, redirecionar para dashboard
    header('Location: public/html/dashboard.html');
    exit();
} else {
    // Se não estiver logado, redirecionar para login
    header('Location: public/html/login.html');
    exit();
}
?>

