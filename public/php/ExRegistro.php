<?php
// processar_exclusao.php

require_once '../../config/database.php';
include_once '../../src/auth/check_session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    // Conexão com o banco de dados (substitua pelos seus dados)
    $conexao = mysqli_connect("localhost", "root", "root", "banco_de_dados");

    // Verifique se a conexão foi bem-sucedida
    
    if (!verificarSessao()) {
        responderJSON(false, "Acesso negado. Faça login para continuar.", null, 401);
    }

    // Limpa o ID para prevenir SQL injection
    $id = mysqli_real_escape_string($conexao, $_POST['excluir']);

    // Comando SQL para exclusão
    $sql = "DELETE FROM sua_tabela WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        // Redireciona com mensagem de sucesso
        header("Location: sua_pagina.php?status=sucesso");
    } else {
        // Redireciona com mensagem de erro
        header("Location: sua_pagina.php?status=erro");
    }
    mysqli_close($conexao);
    exit();
}
?>