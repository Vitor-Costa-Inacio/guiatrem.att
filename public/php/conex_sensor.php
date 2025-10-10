<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbsensor = "sistemas_sensores";

$conn = new mysqli($host, $user, $pass, $dbsensor);

if($conn->connect_error){
    die("Erro na conexão: " . $conn->connect_error);
}
?>