<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bethel1.Sys</title>
  <link rel="stylesheet" href="../../css/animation.css">
  <link rel="coneccion" href="../../includes/coneccion.php">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body id="body" class="d-flex justify-content-center align-items-center vh-100 bg-dark">

  <div class="container mt-5" style="max-width: 800px;">

    <!-- ✅ Alerta única -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="col px-0 bg d-flex mb-3">
    <!-- Imagen superior -->
    <div class="col px-0 bg d-flex mb-3">
      <img class="img-fluid" src="img/Destacamento_Bethel_Nueva_Imagen.png" alt="logoPrin">
    </div>
    <div>
    <!-- Encabezado -->
    <h1 class="fw-bold text-center pt-2 mb-4">
      <span style="color:rgba(255, 255, 0, 0.911);">Bienvenido</span>
    </h1>

    <!-- Formulario -->
    <form action="validar_login.php" method="post">
      <div class="mb-4">
        <label for="usuario" class="form-label fs-4" style="color:rgba(255, 255, 255, 0.91);">Usuario</label>
        <input type="text" class="form-control" name="usuario" placeholder="Usuario" required>
      </div>
      <div class="mb-4">
        <label for="contrasena" class="form-label fs-4" style="color:rgba(255, 255, 255, 0.91);">Password</label>
        <input type="password" class="form-control" name="contrasena" placeholder="Contraseña" required>
      </div> 
      
      <div class="d-grid">
        <button type="submit" class="btn btn-primary" name="btn_iniciar">
          <span style="color:rgba(255, 255, 255, 0.91);">Iniciar Sesión</span>
        </button>
      </div>

      <div class="my-3 text-center">
        <span style="color:rgba(255, 255, 255, 0.91);">¿No tienes cuenta? contacta con un administrador </span>
        <!-- <div class="mt-sm-3 mb-sm-4">
          <span><a href="#" class="text-decoration-none">Recuperar Contraseña</a></span>
        </div> -->
      </div>
    </form>
    </div>
  </div>
    </div>

  <!-- Alerta desaparece automáticamente -->
  <script>
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) {
        alert.classList.remove('show');
        alert.classList.add('hide');
      }
    }, 5000);
  </script>

</body>
</html>
