<?php
    $host = 'localhost';
    $usuario = 'DB_USER';
    $password = 'DB_PASSWD';
    $base_datos = 'DB_NAME';

    $conn = new mysqli($host, $usuario, $password, $base_datos);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
?>