<?php
// Configurar caminhos relativos
$config_path = dirname(dirname(__FILE__)) . '/../config/config.php';
$database_path = dirname(dirname(__FILE__)) . '/../config/database.php';

include_once $config_path;
include_once $database_path;

if($_POST){
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $linha = $_POST['linha'];
        $numero_trem = $_POST['numero_trem'];
        $modelo = $_POST['modelo'];
        $capacidade = $_POST['capacidade'];
        $status_trem = $_POST['status_trem'];
        $data_ultima_manutencao = $_POST['data_ultima_manutencao'] ?: null;
        
        $query = "INSERT INTO trens SET linha=:linha, numero_trem=:numero_trem, modelo=:modelo, capacidade=:capacidade, status_trem=:status_trem, data_ultima_manutencao=:data_ultima_manutencao";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":linha", $linha);
        $stmt->bindParam(":numero_trem", $numero_trem);
        $stmt->bindParam(":modelo", $modelo);
        $stmt->bindParam(":capacidade", $capacidade);
        $stmt->bindParam(":status_trem", $status_trem);
        $stmt->bindParam(":data_ultima_manutencao", $data_ultima_manutencao);
        
        if($stmt->execute()){
            $_SESSION['message'] = "Trem cadastrado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao cadastrar trem.";
            $_SESSION['message_type'] = "error";
        }
    } catch(PDOException $exception) {
        if($exception->getCode() == 23000) {
            $_SESSION['message'] = "Erro: Já existe um trem com este número.";
            $_SESSION['message_type'] = "error";
        } else {
            $_SESSION['message'] = "Erro ao cadastrar trem: " . $exception->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    
    redirect('./index.php');
    exit();
}
?>