<?php
/**
 * Ponto de entrada principal da aplicação Guia Trem
 */

// Incluir configurações
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Configurar CORS
configurarCORS();

// Inicializar sessão
session_start();

// Verificar se o usuário está logado
include_once __DIR__ . '/../src/auth/check_session.php';

if (verificarSessao()) {
    // Se estiver logado, redirecionar para dashboard
    header('Location: html/dashboard.html');
    exit();
} else {
    // Se não estiver logado, redirecionar para login
    header('Location: html/login.html');
    exit();
}
?>

