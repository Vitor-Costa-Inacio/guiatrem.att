colocar para rodar
<?php
include "conex_sensor.php";

if (!empty($_POST[sensor_01]) && !empty($_POST[tipo_sensor])){

    $sensor_01 = $_POST['sensor_01'];
    $tipo_sensor = $_POST['tipo_sensor'];
    $loca_sensor = $_POST['loca_sensor'];
    $status_sensor = $_POST['status'];

    $sql = "INSERT INTO sensores (sensor_01, tipo_sensor, loca_sensor, status_sensor) values ('$sensor_01', '$tipo_sensor', '$loca_sensor', '$status')";
    
    if($conn->query($sql) === true){
        header("Location: index.php?msg=Sensor cadastrado com sucesso!");
    } else {
        header("Location: index.php?erro=Erro ao salvar sensor.");
    }


} else {
    header("Location: index.php?erro=Preencha os campos obrigatorios.");
}


?>