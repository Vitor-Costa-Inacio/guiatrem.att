<?php
include "conex_sensor.php";

if(!isset($_GET['id'])){
    header("Location: index.php?erro=ID inválido.");
    exit;
}

$id_sensor = $_GET['id'];
$sql = "SELECT * FROM sensores WHERE id_sensor=$id_sensor";
$result = $conn->query($sql);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sensor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
        input, select { margin: 5px; padding: 8px; }
        button { padding: 8px 15px; margin: 5px; }
    </style>
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
    <a href="index.php"><button type="button">Cancelar</button></a>
</form>

</body>
</html>