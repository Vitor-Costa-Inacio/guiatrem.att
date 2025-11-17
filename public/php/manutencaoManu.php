<?php
// processar_manutencao.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Configurações do banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "guiatrem";
    
    // Coletar dados do formulário
    $linha = $_POST['linha'] ?? '';
    $trem = $_POST['trem'] ?? '';
    $tipo_manutencao = $_POST['manutencao'] ?? '';
    $prioridade = $_POST['prioridade'] ?? '';
    $servico = $_POST['servico'] ?? '';
    $data_solicitacao = date('Y-m-d H:i:s');
    
    // Validação básica
    if (empty($linha) || empty($trem) || empty($tipo_manutencao) || empty($prioridade) || empty($servico)) {
        die("Erro: Todos os campos são obrigatórios!");
    }
    
    try {
        // Conexão com o banco de dados
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Preparar e executar a query
        $sql = "INSERT INTO manutencoes (linha, trem, tipo_manutencao, prioridade, servico, data_solicitacao, status) 
                VALUES (:linha, :trem, :tipo_manutencao, :prioridade, :servico, :data_solicitacao, 'Pendente')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':linha', $linha);
        $stmt->bindParam(':trem', $trem);
        $stmt->bindParam(':tipo_manutencao', $tipo_manutencao);
        $stmt->bindParam(':prioridade', $prioridade);
        $stmt->bindParam(':servico', $servico);
        $stmt->bindParam(':data_solicitacao', $data_solicitacao);
        
        $stmt->execute();
        
        echo "sucesso";
        
    } catch(PDOException $e) {
        echo "Erro no banco de dados: " . $e->getMessage();
    }
    
    $conn = null;
    
} else {
    echo "Método de requisição inválido!";
}
?>