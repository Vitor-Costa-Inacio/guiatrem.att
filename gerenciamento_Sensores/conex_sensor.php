<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbsensor = "sistemas_sensores";

$conn = new mysqli($host, $user, $pass, $dbsensor);

if($conn ->connect_error){
    die("Erro na conecxão " . $conn->connect_error);
}

?>