<?php include "conex_sensor.php"; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Sensores</title>
</head>
<body>

<h1>Gerenciamento de Sensores</h1>

<!-- Mensagens -->
<?php
if (isset($_GET['msg'])) echo "<p style='color:green;'>".$_GET['msg']."</p>";
if (isset($_GET['erro'])) echo "<p style='color:red;'>".$_GET['erro']."</p>";  
?>

<!-- Formulário -->
<h2>Cadastrar novo sensor</h2>
<form action="salvar.php" method="POST">
    Nome do Sensor: <input type="text" name="sensor_01" required><br><br>
    Tipo do Sensor: <input type="text" name="tipo_sensor" required><br><br>
    Localização: <input type="text" name="loca_sensor"><br><br>
    Status: 
    <select name="status">
        <option value="ativo">Ativo</option>
        <option value="inativo">Inativo</option>
    </select><br><br>
    <button type="submit">Salvar</button>
    <button type="reset">Cancelar</button>
</form>

<hr>

<!-- Lista -->
<h2>Lista de Sensores</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Tipo</th>
        <th>Localização</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

    <?php
    $sql = "SELECT * FROM sensores ORDER BY id_sensor DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($sensor = $result->fetch_assoc()) {
            echo "<tr>
                <td>".$sensor['id_sensor']."</td>
                <td>".$sensor['sensor_01']."</td>
                <td>".$sensor['tipo_sensor']."</td>
                <td>".$sensor['loca_sensor']."</td>
                <td>".$sensor['status']."</td>
                <td>
                    <a href='editar.php?id=".$sensor['id_sensor']."'>Editar</a> | 
                    <a href='excluir.php?id=".$sensor['id_sensor']."' onclick='return confirm(\"Tem certeza que deseja excluir?\");'>Excluir</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Nenhum sensor cadastrado.</td></tr>";
    }
    ?>
</table>

</body>
</html>
