<?php
$config_path = __DIR__ . '/../../config/config.php';
$database_path = __DIR__ . '/../../config/database.php';

include_once $config_path;
include_once $database_path;

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função de redirecionamento
function redirect($url) {
    header("Location: $url");
    exit();
}

if(isset($_GET['id'])){
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $id = $_GET['id'];
        
        $query = "DELETE FROM trens WHERE id_trem = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()){
            $_SESSION['message'] = "Trem excluído com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao excluir trem.";
            $_SESSION['message_type'] = "error";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Erro ao excluir trem: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    // Redirecionar para a página principal (index.php na mesma pasta)
    redirect('index.php');
    exit();
}
?>