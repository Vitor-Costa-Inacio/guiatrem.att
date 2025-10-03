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

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function createNotification(int $userId, string $message, string $type = 'info') : bool {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message, type, read_status, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$userId, $message, $type, 0]);
    }

    public function getNotifications(int $userId) : array {
        $stmt = $this->pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $notificationId, int $userId) : bool {
        $stmt = $this->pdo->prepare("UPDATE notifications SET read_status = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    public function markAllAsRead(int $userId) : bool {
        $stmt = $this->pdo->prepare("UPDATE notifications SET read_status = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function deleteNotification(int $notificationId, int $userId) : bool {
        $stmt = $this->pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    public function getUnreadCount(int $userId) : int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND read_status = 0");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }
}

// Processar requisições
$method = $_SERVER['REQUEST_METHOD'];
$notificationManager = new NotificationManager($db);
$userId = $_SESSION['user_id'] ?? 1; // Usar ID do usuário da sessão

switch ($method) {
    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'unread_count':
                    $count = $notificationManager->getUnreadCount($userId);
                    responderJSON(true, "Contagem de notificações não lidas obtida com sucesso.", ['count' => $count]);
                    break;
                
                default:
                    $notifications = $notificationManager->getNotifications($userId);
                    responderJSON(true, "Notificações obtidas com sucesso.", $notifications);
                    break;
            }
        } else {
            $notifications = $notificationManager->getNotifications($userId);
            responderJSON(true, "Notificações obtidas com sucesso.", $notifications);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'create':
                    $message = $input['message'] ?? '';
                    $type = $input['type'] ?? 'info';
                    
                    if (empty($message)) {
                        responderJSON(false, "Mensagem é obrigatória.", null, 400);
                    }
                    
                    $success = $notificationManager->createNotification($userId, $message, $type);
                    
                    if ($success) {
                        responderJSON(true, "Notificação criada com sucesso.");
                    } else {
                        responderJSON(false, "Erro ao criar notificação.", null, 500);
                    }
                    break;

                case 'mark_read':
                    $notificationId = $input['notification_id'] ?? 0;
                    
                    if ($notificationId <= 0) {
                        responderJSON(false, "ID da notificação é obrigatório.", null, 400);
                    }
                    
                    $success = $notificationManager->markAsRead($notificationId, $userId);
                    
                    if ($success) {
                        responderJSON(true, "Notificação marcada como lida.");
                    } else {
                        responderJSON(false, "Erro ao marcar notificação como lida.", null, 500);
                    }
                    break;

                case 'mark_all_read':
                    $success = $notificationManager->markAllAsRead($userId);
                    
                    if ($success) {
                        responderJSON(true, "Todas as notificações foram marcadas como lidas.");
                    } else {
                        responderJSON(false, "Erro ao marcar todas as notificações como lidas.", null, 500);
                    }
                    break;

                default:
                    responderJSON(false, "Ação não reconhecida.", null, 400);
                    break;
            }
        } else {
            responderJSON(false, "Ação não especificada.", null, 400);
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $notificationId = $input['notification_id'] ?? 0;
        
        if ($notificationId <= 0) {
            responderJSON(false, "ID da notificação é obrigatório.", null, 400);
        }
        
        $success = $notificationManager->deleteNotification($notificationId, $userId);
        
        if ($success) {
            responderJSON(true, "Notificação excluída com sucesso.");
        } else {
            responderJSON(false, "Erro ao excluir notificação.", null, 500);
        }
        break;

    default:
        responderJSON(false, "Método não permitido.", null, 405);
        break;
}

?>