<?php 
// solicita conección con la base de datos
include '../../includes/coneccion.php';

?>
<!DOCTYPE HTML>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registros Bethel 1</title>
    <link rel="stylesheet" href="../../css/animation.css">
    <link rel="coneccion" href="../../includes/coneccion.php">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
  </head>
  <body>


<!-- Barra de navegación superior -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark " >
  <div class="container ">

    <!-- Marca -->
    <a href="../Inicio.php" class="navbar-brand">Bethel 1</a>

    <!-- Botón hamburguesa (vista móvil) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Enlaces -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">

        <!-- Dropdown : Departamentos -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="departamentoDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Muchachos
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="departamentoDropdown">
            <li><a class="dropdown-item" href="navegantes.php">Navegantes</a></li>
            <li><a class="dropdown-item" href="pioneros.php">Pioneros</a></li>
            <li><a class="dropdown-item" href="pioneras.php">Pioneras</a></li>
            <li><a class="dropdown-item" href="seguidores.php">Seguidores</a></li>
            <li><a class="dropdown-item" href="señoritas.php">Señoritas</a></li>
            <li><a class="dropdown-item" href="exploradores.php">Exploradores</a></li>
          </ul>
        </li>

        <!-- Dropdown : Administración -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Administración
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
            
            <li><a class="dropdown-item" href="inventario.php">Inventarios</a></li>
            <li><a class="dropdown-item" href="lideres.php">Lideres</a></li>
            <li><a class="dropdown-item" href="Premios.php">Premios</a></li>
            <li><a class="dropdown-item" href="usuarios.php">Usuarios</a></li>
            <li><a class="dropdown-item" href="NivelesUsuarios.php">Niveles Usuarios</a></li>
          </ul>
        </li>

        <!-- Dropdown : Eventos -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="eventosDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Eventos
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="eventosDropdown">
            <li><a class="dropdown-item" href="eventos.php">Lista de Eventos</a></li>
            <li><a class="dropdown-item" href="resumenEventos.php">Resumen de Eventos.</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- FIN barra de navegación superior -->


 <!-- Estadisticas generales y ya--><!--PENDIENTE ESTADISTICAS REALES falta conexiones con bd-->
 
<section class="bg-dark text-light text-center p-5 d-sm-flex align-item-center justify-content-between" >
  <div class="container-lg " id="estadisticas" >
  <h1><span style="color:rgba(255, 255, 0, 0.911); ">Destacamento Bethel 1</span></h1>
    <div class="d-sm-flex align-item-center justify-content-between">
      
        <!-- Miembros del destacamento-->
        <div class="card bg-dark card mb-3 width 15% text-ajust" style="max-width: 350px">
          <img src="../../img/persona-ico.png" class="card-img-top img-fluid rounded-start" alt="" 
          style="width:300px;">
          <div class="card-body">
          <h5 class="card-title">Miembros del Destacamento</h5>
                <p class="card-text">9 </p>
                <p class="card-text"> personas</p>
                <!-- p class="card-text"><small class="text-muted">Estadisticas 2023</small></p -->
          </div>
        </div>
    
        <!--FIN Miembros del destacamento-->
        <!-- Insumos del destacamento-->
        <div class="card bg-dark card mb-3 width 15%" style="max-width: 350px">
          <img src="../../img/banck-ico.webp" class="card-img-top img-fluid rounded-start" alt=""
          style="width:300px;">
          <div class="card-body">
          <h5 class="card-title">Insumos del Destacamento</h5>
                <p class="card-text">0 </p>
                <!-- p class="card-text"><small class="text-muted">Estadisticas 2023</small></p -->
          </div>
        </div>
     
     
        <!-- FIN Insumos del destacamento-->
        <!-- Miembros nuevos del destacamento-->

        <div class="card bg-dark card mb-3 width 15%" style="max-width: 350px">
          <img src="../../img/estadistica-ico.jpg" class="card-img-top img-fluid rounded-start" alt="" 
          style="width:300px;">
          <div class="card-body">
                <h5 class="card-title">Miembros Nuevos este Año</h5>
                <p class="card-text">9 </p>
                <p class="card-text"> personas</p>
                <!-- p class="card-text"><small class="text-muted">Estadisticas 2023</small></p -->
          </div>
        </div>
     
        
      
        <!-- Miembros nuevos del destacamento-->
        <!-- Premios Otorgados este año-->

        <div class="card bg-dark card mb-3 width 15%" style="max-width: 350px">
          <img src="../../img/medalla-ico.jpg" class="card-img-top img-fluid rounded-start" alt="" 
          style="width:300px;">
          <div class="card-body">
                <h5 class="card-title">Premios Otorgados este Año</h5>
                <p class="card-text">0</p>
          </div>
        </div>
        <!--FIN Premios Otorgados este año-->
    </div>
  </div>
</section>
 <!-- FIN Estadisticas generales y ya-->

 
<!-- esto es relleno hasta que sepa que diablos meter aqui XD-->
    <section class="bg-dark text-light text-center p-5">
      <div class="container" id="Inicio">
        <div class="d-sm-flex align-item-center justify-content-between " >
          <div>
            <a href="#">
            <img src="../../img/Destacamento_Bethel_Nueva_Imagen.png" alt="" class="img-fluid whidt: 10%" >
          </a>
          </div>
        </div>
      </div>
    </section>
<!-- esto es relleno hasta que sepa que diablos meter aqui XD-->

<!-- Informacion departamentos-->
<section class="bg-dark text-light text-center text-ajust p-5 d-sm-flex align-item-center justify-content-between" >
  <div class="container " id="estadisticas" >
    <div class="d-sm-flex align-item-center justify-content-between">
      
        <!-- Navegantes-->
        <a href="informacion.html" style="width: 33%;"><span style="color:rgba(255, 255, 255, 0.91); ">
        <div class="card bg-dark" >
          <img src="../../img/navegantesC.png" class="card-img-top" alt=""  width="150 px">
          <div class="card-body">
            <h3 class="card-title">Navegantes</h3>
            <p class="card-text">de 4 a 8 años</p>
            <p class="card-text"><small class="text-muted">El Inicio de una Aventura</small></p>
          </div>
        </div>
      </span></a>
    
        <!--FIN Navegantes-->
        <!-- Pioneros-->
        <a href="informacion.html" style="width: 33%;" ><span style="color:rgba(255, 255, 255, 0.91); ">
        <div class="card bg-dark" >
          <img src="../../img/pionerosC.png" class="card-img-top" alt="" width="150 px">
          <div class="card-body">
            <h3 class="card-title">Pioneros</h3>
            <p class="card-text">Varones de 8 a 12 años</p>
            <p class="card-text"><small class="text-muted">Cada Día Una Aventura Nueva</small></p>
          </div>
        </div>
        </span></a>
        <!-- FIN Pioneros -->

        <!-- Pioneras-->
        <div class="card bg-dark" style="width: 33%;">
          <img src="../../img/pionerosC.png" class="card-img-top" alt="" width="150 px">
          <div class="card-body">
            <h3 class="card-title">Señoritas Pioneras</h3>
            <p class="card-text">Señoritas de 8 a 12 años</p>
            <p class="card-text"><small class="text-muted">Cada Día Una Aventura Nueva</small></p>
          </div>
        </div>
        <!-- FIN Pioneras-->
    </div>

        <!--corte para organizar-->
        <div class="d-sm-flex align-item-center justify-content-between">
        <!-- Seguidores de la Senda-->

        <div class="card bg-dark" style="width: 33%;">
          <img src="../../img/SeguidoresC.png" class="card-img-top" alt="" width="150px">
          <div class="card-body">
            <h3 class="card-title">Seguidores de la Senda</h3>
            <p class="card-text">Muchachos de 12 a 15 años</p>
            <p class="card-text"><small class="text-muted">Forjando un Caracter</small></p>
          </div>
        </div>
        <!-- Seguidores de la Senda-->
        <!-- Exploradores-->
        <div class="card bg-dark" style="width: 33%;">
          <img src="../../img/ExploradoresC.png" class="card-img-top" alt="" width="150px">
          <div class="card-body">
            <h3 class="card-title">Exploradores</h3>
            <p class="card-text">Muchachos de 15 a 18 años)</p>
            <p class="card-text"><small class="text-muted">Hombres de Verdad</small></p>
          </div>
        </div>
        <!--FIN Exploradores-->

        <!-- Señoritas-->
        <div class="card bg-dark" style="width: 33%;">
          <img src="../../img/Señoitas.jpg" class="card-img-top" alt="" width="150px">
          <div class="card-body">
            <h3 class="card-title">Señoritas</h3>
            <p class="card-text">Señoritas de 12 a 18 años</p>
            <p class="card-text"><small class="text-muted">Mujeres de Valor</small></p>
          </div>
        </div>
        <!--FIN Señoritas-->
    </div>
  </div>
</section>
<!--FIN Informacion departamentos-->


<!-- redes sociales-->
<section class="bg-dark text-light text-center p-5">
  <div class="container " id="contacto" >
    <h1 class="my-5"><span style="color:rgba(255, 255, 0, 0.911);">Nuestras Redes Sociales</span></h1>
    <div class="d-sm-flex align-item-center justify-content-between">
      <li>
        <img src="../../img/gmail-logo.jpg"  alt="" width="55px">
        <a href="mailto:destacamento1bethel@gmail.com" class="btn btn-primary">Correo Destacamento</a>
      </li>
      <li>
        <img src="../../img/gmail-logo.jpg"  alt="" width="55px">
        <a href="mailto:canogabu25@gmail.com" class="btn btn-primary">Correo Gaby</a>
      </li>
      <li>
        <img src="../../img/gmail-facebook.jpg"  alt="" width="55px">
        <a href="https://www.facebook.com/EDRBethel1" class="btn btn-primary">Facebook</a>
      </li>
      <li>
        <img src="../../img/gmail-Instagram.jpg"  alt="" width="55px">
        <a href="https://www.instagram.com/destacamentobethel1/" class="btn btn-primary">Instagram</a>
      </li>
    </div>
  </div>
</section>
<!-- FIN redes sociales-->

<!--Foother--><!--PENDIENTE-->
    <section id="Foother" class="bottom">
      <footer > 
        <div class="bg-dark text-light text-center p-5 " >
            <h8><img src="../../img/mmejia.jpg" alt="" width="25px">
              Mario Edgardo Mejía Guevara
              <img src="../../img/mmejia.jpg" alt="" width="25px"></h8><br>
            <h8><img src="../../img/Emblema-Exploradores-del-Rey.png" alt="" width="35px">
              Exploradores del Rey, Destacamento Bethel 1
              <img src="../../img/Emblema-Exploradores-del-Rey.png" alt="" width="35px"></h8><br>
            <h8>Todos los derechos reservados.</h8><br>
            <a type="button" class="btn btn-primary my-5" href="#">Volver al Inicio</a>
          </div>
        </div>
    </footer>
    </section>
  <!--FIN Foother-->
  </body>


</html>
