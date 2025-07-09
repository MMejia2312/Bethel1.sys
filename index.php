
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bethel1.Sys</title>
  <link rel="stylesheet" href="../../css/animation.css">
  <link rel="coneccion" href="../../includes/coneccion.php">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
  
  <!-- link rel="stylesheet" href="./css/style1.css" -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" -->


</head>
<body id="body" class="d-flex justify-content-center align-items-center vh-100 bg-dark" >
  <div id="container" class="container w-75" style="border-radius: 20px;">
    <div id="datos" class="row">
      <div class="col px-0 bg d-flex">
        <img  class="img-fluid" src="img/Destacamento_Bethel_Nueva_Imagen.png" alt="logoPrin">
      </div>
      <div  class="col-sm px-5 ">
        <div class="text-end ">
          <img class="rounded-circle m-2" src="img/Emblema-Exploradores-del-Rey.png" alt="logo" width="48">
        </div>
        <h1 class="fw-bold text-center pt-2 mb-4"><span style="color:rgba(255, 255, 0, 0.911); ">
          Bienvenido</span>
        </h1>
        <form action="validar_login.php" method="post">
          <div class="mb-4">
            <label for="Usuario" class="form-label fs-4"><span style="color:rgba(255, 255, 255, 0.91); "> Usuario</span></label>
            <input type="text" class="form-control" name="usuario" placeholder="Usuario">
          </div>
          <div class="mb-4">
            <label for="password" class="form-label fs-4"><span style="color:rgba(255, 255, 255, 0.91); "> Password</span></label>
            <input type="password" class="form-control" name="password" placeholder="contraseña">
          </div> 
          
          <div class="d-grid">
            <button type="submit" class="btn btn-primary" name="btn_iniciar"><span style="color:rgba(255, 255, 255, 0.91); ">Iniciar Sesión</span></button>
          </div>
          <div class="my-3 text-center">
            <span style="color:rgba(255, 255, 255, 0.91); ">No tienes cuenta? <a href="#">Registrate</a></span>
            <div class="mt-sm-3 mb-sm-4 ">
              <!-- span ><a href="#" class="text-decoration-none">Recuperar Contraseña</a></span>
            </div-->
            
          </di>
        </form>
      </div>
    </div>
  </div>

</body>
</html>