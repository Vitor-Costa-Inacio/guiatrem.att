<?php
$config_path = dirname(dirname(__FILE__)) . '/../config/config.php';
$database_path = dirname(dirname(__FILE__)) . '/../config/database.php';

include_once $config_path;
include_once $database_path;

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
    
    redirect('./index.php');
    exit();
}
?>