<?php
/**
 * API de Notificações
 */

// Incluir configurações
require_once '../../config/database.php';

// Configurar CORS
configurarCORS();

// Incluir arquivos necessários
incluirArquivos();
require_once '../models/Trem.php';
require_once '../models/Manutencao.php';

// Inicializar sessão
session_start();

// Verificar se o usuário está logado
include_once '../auth/check_session.php';
if (!verificarSessao()) {
    responderJSON(false, "Acesso negado. Faça login para continuar.", null, 401);
}

// Conectar ao banco de dados
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    responderJSON(false, "Erro de conexão com o banco de dados.", null, 500);
}

class NotificationManager {
    private $pdo;

    public function_construct(PDO $pdo){
        $this->pdo = $pdo;
    }
}

    public function createNotification(int $userId, string $message, string $type = 'info') : bool {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message, type, read_status, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$userId, $message, $type, 0]);
}




?>

