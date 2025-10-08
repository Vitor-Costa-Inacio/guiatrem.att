colocar para rodar
<?php
include "conex_sensor.php";

if (!empty($_POST['id_sensor']) && !empty($_POST['sensor_01']) && !empty($_POST['tipo_sensor'])){
    $id_sensor = $_POST['id_sensor'];
    $sensor_01 = $_POST['sensor_01'];
    $tipo_sensor = $_POST['tipo_sensor'];
    $loca_sensor = $_POST['loca_sensor'];
    $status = $_POST['status'];

    $sql = "UPDATE sensores SET 
            sensor_01='$sensor_01', 
            tipo_sensor='$tipo_sensor', 
            loca_sensor='$loca_sensor', 
            status='$status' 
            WHERE id_sensor=$id_sensor";
    
    if($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=Sensor atualizado com sucesso!");
    } else {
        header("Location: index.php?erro=Erro ao atualizar sensor.");
    }
} else {
    header("Location: index.php?erro=Preencha os campos obrigatórios.");
}


?>

