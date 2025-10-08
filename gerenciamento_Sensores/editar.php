colocar para rodar
<?php
include "conex_sensor.php";

if(!isset($_GET[id])){
    header("Location: index.php?erro=ID invalido.");
    exit;
}

$id_sensor = $_GET['id'];
$sql = "SELECT * FROM sensores WHERE id_sensor=$id_sensor";
$result=$conn->query($sql);

if ($result->num_rows == 0){
    header("Location: index.php?erro=Sensor não encontrado.");
    exit;
}

$sensor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Sensor</title>
</head>
<body>

<h1>Editar Sensor</h1>

<form action="atualizar.php" method="POST">
    <input type="hidden" name="id_sensor" value="<?php echo $sensor['id_sensor']; ?>">

    Nome do Sensor: <input type="text" name="sensor_01" value="<?php echo $sensor['sensor_01']; ?>" required><br><br>
    Tipo do Sensor: <input type="text" name="tipo_sensor" value="<?php echo $sensor['tipo_sensor']; ?>" required><br><br>
    Localização: <input type="text" name="loca_sensor" value="<?php echo $sensor['loca_sensor']; ?>"><br><br>
    Status:
    <select name="status">
        <option value="ativo" <?php if($sensor['status']=='ativo') echo 'selected'; ?>>Ativo</option>
        <option value="inativo" <?php if($sensor['status']=='inativo') echo 'selected'; ?>>Inativo</option>
    </select><br><br>

    <button type="submit">Atualizar</button>
    <a href="index.php">Cancelar</a>
</form>

</body>
</html>

?>