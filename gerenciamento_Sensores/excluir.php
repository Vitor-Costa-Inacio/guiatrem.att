colocar para rodar
<?php
include "conex_sensor.php";

if (isset($_GET['id'])) {
    $id_sensor = $_GET['id'];
    $sql = "DELETE FROM sensores WHERE id_sensor=$id_sensor";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=Sensor excluído com sucesso!");
    } else {
        header("Location: index.php?erro=Erro ao excluir sensor.");
    }
} else {
    header("Location: index.php?erro=ID inválido.");
}
?>