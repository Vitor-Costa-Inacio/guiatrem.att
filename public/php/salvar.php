<?php
include "conex_sensor.php";

if (!empty($_POST['sensor_01']) && !empty($_POST['tipo_sensor'])){

    $sensor_01 = $_POST['sensor_01'];
    $tipo_sensor = $_POST['tipo_sensor'];
    $loca_sensor = $_POST['loca_sensor'];
    $status = $_POST['status'];

    $sql = "INSERT INTO sensores (sensor_01, tipo_sensor, loca_sensor, status) 
            VALUES ('$sensor_01', '$tipo_sensor', '$loca_sensor', '$status')";
    
    if($conn->query($sql) === TRUE){
        header("Location: index.php?msg=Sensor cadastrado com sucesso!");
    } else {
        header("Location: index.php?erro=Erro ao salvar sensor: " . $conn->error);
    }

} else {
    header("Location: index.php?erro=Preencha os campos obrigatórios.");
}
?>