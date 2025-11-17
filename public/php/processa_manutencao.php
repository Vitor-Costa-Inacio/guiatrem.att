<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Receber o valor selecionado
    $manutencao_selecionada = $_POST['solicitadas'];
    
    // Validar se foi selecionada uma opção válida
    if (empty($manutencao_selecionada) || $manutencao_selecionada == "Selecione uma manutenção") {
        echo "erro_selecao";
        exit;
    }
    
    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "guiatrem";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Inserir no banco de dados
        $sql = "INSERT INTO manutencao (tipo_manutencao, data_registro, status) 
                VALUES (:id_manutencao, NOW(), 'solicitada')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_manutencao', $manutencao_selecionada);
        $stmt->execute();
        
        echo "sucesso";
        
    } catch(PDOException $e) {
        echo "erro_banco";
    }
    
    $conn = null;
    
} else {
    echo "erro_metodo";
}
?>