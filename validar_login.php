<<<<<<< HEAD
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
                    header("Location: usuarios/Administrador/inicio.php");
                    break;
                case 2:
                    header("Location: usuarios/coordinador/inicio.php");
                    break;
                case 3:
                    header("Location: usuarios/lider/inicio.php");
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
=======
<?php
session_start();


    function mostrarMensajeYRedireccionar($mensaje, $url) {
        echo "<script>alert('$mensaje'); window.location.href = '$url';</script>";
        exit;
    }
    
    if (empty($_POST['usuario']) || empty($_POST['password'])) {
        mostrarMensajeYRedireccionar("Por favor, complete todos los campos.", 'index.php');
    }
    
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    
    $conexion = mysqli_connect("localhost", "root", "", "bethel1_sys");
    
    if (!$conexion) {
        die("Error en la conexión a la base de datos: " . mysqli_connect_error());
    }
    
    $consulta = "SELECT id_nivel FROM usuarios WHERE usuario = ? AND contrasena = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . mysqli_error($conexion));
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $id_nivel);
        mysqli_stmt_fetch($stmt);
        
        // Mapeo de roles a rutas de redirección
        $rutas = [
            1 => 'usuarios/Administrador/Inicio.php',
            2 => 'usuarios/coordinador/index.php',
            3 => 'usuarios/lider/index.php'
        ];
        
        // Verifica si el cargo está en el mapeo
        if (isset($rutas[$id_nivel])) {
            $_SESSION['id'] = $id_nivel;
            header("Location: ". $rutas[$id_nivel]);
        } else {
            mostrarMensajeYRedireccionar("Rol desconocido.", 'index.php');
        }
    } else {
        mostrarMensajeYRedireccionar("Credenciales incorrectas, por favor verifícalas.", 'index.php');
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    

?>



>>>>>>> e40e7ef (Add files via upload)
