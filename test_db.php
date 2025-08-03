<?php
$conexion = new mysqli("localhost", "root", "", "bethel1_sys");

if ($conexion->connect_error) {
    die("Fallo en la conexión a la base de datos: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>
