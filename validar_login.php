<?php
session_start();
include 'includes/coneccion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE USUARIO = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuarioData = $resultado->fetch_assoc();

        if ($usuarioData['ESTADO'] != 1) {
            $_SESSION['error'] = "Tu cuenta está inactiva.";
            header("Location: index.php");
            exit;
        }

        if ($contrasena === $usuarioData['CONTRASENA']) {
            // Guardar datos en la sesión
            $_SESSION['ID_USUARIO'] = $usuarioData['ID_USUARIO'];
            $_SESSION['ID_LIDER'] = $usuarioData['ID_LIDER'];
            $_SESSION['ID_NIVEL'] = $usuarioData['ID_NIVEL'];
            $_SESSION['USUARIO'] = $usuarioData['USUARIO'];

            // Redirigir según el nivel
            switch ($usuarioData['ID_NIVEL']) {
                case 1:
                    header("Location: usuarios/Administrador/Inicio.php");
                    break;
                case 2:
                    header("Location: usuarios/coordinador/Inicio.php");
                    break;
                case 3:
                    header("Location: usuarios/lider/Inicio.php");
                    break;
                default:
                    $_SESSION['error'] = "No tienes un nivel de acceso asignado.";
                    header("Location: index.php");
                    break;
            }
            exit;
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Usuario no registrado.";
        header("Location: index.php");
        exit;
    }
}
?>
