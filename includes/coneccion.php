<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// declarar variables de conexión
$host = "localhost";
$user = "bethel_user";
$password = "TuPasswordSegura";
$database = "bethel1_sys";


$conexion = mysqli_connect($host, $user, $password, $database);
if (!$conexion) {
    echo "No se realizo la conexion a la basa de datos, el error fue:" .
        mysqli_connect_error();
}
mysqli_set_charset($conexion, "utf8mb4");
?>
