<?php 
// solicita conección con la base de datos
include '../../../includes/coneccion.php';
$consultaGEN = "select m.*, d.ID_DEPARTAMENTO, d.NOMBRE_DEP
from muchachos m
join departamento d ON m.DEPARTAMENTO = d.ID_DEPARTAMENTO
where Departamento = '9'";

$guardar = $conexion->query($consultaGEN);
?>


<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registros Exploradores Bethel 1</title>
    <link  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
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

        <!-- ▼ Dropdown : Unidades -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="unidadesDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Muchachos
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="unidadesDropdown">
            <li><a class="dropdown-item" href="navegantes.php">Navegantes</a></li>
            <li><a class="dropdown-item" href="pioneros.php">Pioneros</a></li>
            <li><a class="dropdown-item" href="pioneras.php">Pioneras</a></li>
            <li><a class="dropdown-item" href="seguidores.php">Seguidores</a></li>
            <li><a class="dropdown-item" href="señoritas.php">Señoritas</a></li>
            <li><a class="dropdown-item" href="exploradores.php">Exploradores</a></li>
          </ul>
        </li>

        <!-- ▼ Dropdown : Administración -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="adminDropdown"
             role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Administración
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
            
            <li><a class="dropdown-item" href="insumos.php">Insumos</a></li>
            <li><a class="dropdown-item" href="informacion.php">Información</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- FIN barra de navegación superior -->

    <!--Registros Exploradores-->
 <section class="bg-dark text-light text-center p-5">
  <div class="container " id="TablaExploradores" >
    <h1 class="my-5"><span style="color:rgba(255, 255, 0, 0.911);">Registro Exploradores</span></h1>
    <!--Filtros-->
    
    <div class="d-sm-flex align-item-center justify-content">
      <div class="p-2">
        <h2>Filtros</h2>
      </div>
      
      
      <div class="dropdown p-2" style="max-width: 350px">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Filtrar por
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#">Predeterminado</a></li>
          <li><a class="dropdown-item" href="#">Nombre</a></li>
          <li><a class="dropdown-item" href="#">Apellido</a></li>
          <li><a class="dropdown-item" href="#">Edad</a></li>
          <li><a class="dropdown-item" href="#">Padres</a></li>
        </ul>
      </div>

      <div class="dropdown p-2">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Ordenar
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#">Predeterminado</a></li>
          <li><a class="dropdown-item" href="#">Ascendente</a></li>
        </ul>

            <!-- Botón que lanza el modal de agregar muchachos-->
            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalMuchacho">
              <i class="fa fa-plus me-1"></i> Agregar
            </button>
            <?php include "agregar_muchachos.php"; ?>
      </div>
    </div>
    <!--FIN Filtros-->
    <!-- tabla -->
        <table class="table" >
          <thead style="color:rgb(255, 255, 0);">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Apellido</th>
              <th scope="col">Departamento</th>
              <th scope="col">Fecha de nacimiento</th>
              <th scope="col">Alergias</th>
              <th scope="col">Premios</th>
              <th scope="col">Acciones</th>
              
            </tr>
          </thead>
          <tbody style="color:#ffffff;">
          <?php while ($row = $guardar->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $row['ID_MUCHACHO']; ?></td>
          <td><?php echo $row['NOMBRE_MUC']; ?></td>
          <td><?php echo $row['APELLIDO_MUC']; ?></td>
          <td><?php echo $row['NOMBRE_DEP']; ?></td>
          <td><?php echo $row['FECHA_NA']; ?></td>
          <th scope="col"><button type="button" class="btn btn-primary">
              <span class="glyphicon glyphicon-plus"></span> Alergias <i class="fa fa-plus"></i> </a></button>
          </th>
          <th scope="col"><button type="button" class="btn btn-success" data-toggle="modal" data-target="#grado">
                    <span class="glyphicon glyphicon-plus"></span> Premios <i class="fa fa-plus"></i> </a></button>
          </th>
          <th scope="col"><button type="button" class="btn btn-warning" data-toggle="modal" data-target="#grado">
                    <span class="glyphicon glyphicon-plus"></span> Editar <i class="fa fa-plus"></i> </a></button>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#grado">
                    <span class="glyphicon glyphicon-plus"></span> ELIMINAR <i class="fa fa-plus"></i> </a></button>
            </th>
            
        </tr>
        <?php } ?>
              
          </tbody>
        </table>
        <!--FIN tabla aun por conectar-->
    <!--FIN Registro de Exploradores aun por conectar-->
  </div>
</section>
<!--FIN Registros Exploradores-->

 <!-- redes sociales-->
<section class="bg-dark text-light text-center p-5">
  <div class="container " id="contacto" >
    <h1 class="my-5"><span style="color:rgba(255, 255, 0, 0.911);">Nuestras Redes Sociales</span></h1>
    <div class="d-sm-flex align-item-center justify-content-between">
      <li>
        <img src="../../../img/gmail-logo.jpg"  alt="" width="55px">
        <a href="mailto:destacamento1bethel@gmail.com" class="btn btn-primary">Correo Destacamento</a>
      </li>
      <li>
        <img src="../../../img/gmail-logo.jpg"  alt="" width="55px">
        <a href="mailto:canogabu25@gmail.com" class="btn btn-primary">Correo Gaby</a>
      </li>
      <li>
        <img src="../../../img/gmail-facebook.jpg"  alt="" width="55px">
        <a href="https://www.facebook.com/EDRBethel1" class="btn btn-primary">Facebook</a>
      </li>
      <li>
        <img src="../../../img/gmail-Instagram.jpg"  alt="" width="55px">
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
        <h8><img src="./img/mmejia.jpg" alt="" width="25px">
          Mario Edgardo Mejía Guevara
          <img src="./img/mmejia.jpg" alt="" width="25px"></h8><br>
        <h8><img src="./img/Emblema-Exploradores-del-Rey.png" alt="" width="35px">
          Exploradores del Rey, Destacamento Bethel 1
          <img src="./img/Emblema-Exploradores-del-Rey.png" alt="" width="35px"></h8><br>
        <h8>Todos los derechos reservados.</h8><br>
        <a type="button" class="btn btn-primary my-5" href="#">Volver al Inicio</a>
      </div>
    </div>
</footer>
</section>
<!--FIN Foother-->
  </body>