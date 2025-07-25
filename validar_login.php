<?php
session_start();
include 'includes/coneccion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conexion->prepare("SELECT u.USUARIO, u.CONTRASENA, u.ESTADO, u.ID_NIVEL, n.DESCRIP 
                                FROM usuarios u 
                                LEFT JOIN nivel n ON u.ID_NIVEL = n.ID_NIVEL
                                WHERE u.USUARIO = ?");
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
            $_SESSION['usuario'] = $usuarioData['USUARIO'];
            $_SESSION['nivel'] = $usuarioData['ID_NIVEL'];

            // Redirigir según nivel
            switch ($usuarioData['ID_NIVEL']) {
                case 1: // Administrador
                    header("Location: usuarios/Administrador/Inicio.php");
                    break;
                case 2: // Coordinador
                    header("Location: usuarios/Coordinador/Inicio.php");
                    break;
                case 3: // Líder
                    header("Location: usuarios/Lider/Inicio.php");
                    break;
                default:
                    $_SESSION['error'] = "Nivel no reconocido.";
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
