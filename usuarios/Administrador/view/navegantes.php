<?php
/*--------------------------------------------------
  Conexión + (opcional) funciones CRUD
--------------------------------------------------*/
include '../../../includes/coneccion.php';
require '../../../includes/funcions.php'; 

// Evitar cache del navegador para prevenir acceso con botón atrás después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirigir a login
    header("Location: ../../../index.php");
    exit;
}

// cerrar sesion//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../../../index.php"); // Cambia a la página de login
    exit;
}

// ---------- Agregar ----------//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'agregar') {
    agregar_muchacho($_POST['nombre'], $_POST['apellido'], $_POST['departamento'], $_POST['fecha_na']);
    header("Location: navegantes.php?ok=1");
    exit;
}
// ---------- Editar / Eliminar ----------//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'editar') {
        editar_muchacho($_POST['id'], $_POST['nombre'], $_POST['apellido'],
                         $_POST['departamento'], $_POST['fecha_na']);
        header('Location: navegantes.php?ok=1'); exit;
    }
    if ($_POST['accion'] === 'eliminar') {
        eliminar_muchacho($_POST['id']);
        header('Location: navegantes.php?eliminado=1'); exit;
    }
}
//----------------------------------------------------------------//

// Consulta Navegantes (Departamento 4)
$consultaGEN = "SELECT m.*, d.ID_DEPARTAMENTO, d.NOMBRE_DEP
                FROM muchachos m
                JOIN departamento d ON m.DEPARTAMENTO = d.ID_DEPARTAMENTO
                WHERE m.DEPARTAMENTO = '4'";
$guardar = $conexion->query($consultaGEN);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registros Navegantes Bethel 1</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-dark text-light">

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
            
            <li><a class="dropdown-item" href="Inventario.php">Inventarios</a></li>
            <li><a class="dropdown-item" href="lideres.php">Lideres</a></li>
            <li><a class="dropdown-item" href="premios.php">Premios</a></li>
            <li><a class="dropdown-item" href="Usuarios.php">Usuarios</a></li>
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
        <form class="d-flex ms-auto" method="post">
          <button type="submit" name="logout" class="btn btn-outline-light">Cerrar Sesión</button>
        </form>
      </ul>
    </div>
  </div>
</nav>
<!-- FIN barra de navegación superior -->

<!-- ========= REGISTRO NAVEGANTES ========= -->
<section class="bg-dark text-light text-center p-5">
  <div class="container" id="TablaNavegantes">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Registro de Navegantes</span></h1>

    <!-- Filtros + Botón Agregar -->
    <div class="d-sm-flex align-items-center justify-content">
      <!-- Búsqueda en vivo -->
      <input type="text"
            id="tableSearch"
            class="form-control me-2 mb-2"
            style="max-width:240px"
            placeholder="Buscar…">

      <!-- Ordenar por nombre asc/desc -->
      <select id="tableSort" class="form-select mb-2" style="max-width:180px">
        <option value="">Ordenar…</option>
        <option value="asc">Nombre A → Z</option>
        <option value="desc">Nombre Z → A</option>
      </select>


      <!-- Botón Agregar (abre modal de agregar muchacho) -->
      <button type="button" class="btn btn-success mb-3"
              data-bs-toggle="modal" data-bs-target="#modalMuchacho">
        <i class="fa fa-plus me-1"></i> Agregar
      </button>
      <?php include "agregar_muchachos.php"; ?>
    </div>

    <!-- ======= TABLA ======= -->
    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <!-- th>#</th -->
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Departamento</th>
            <th>Fecha&nbsp;nacimiento</th>
            <th>Alergias</th>
            <th>Premios</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="color:rgb(255,255,0)">
        <?php while ($row = $guardar->fetch_assoc()): ?>
          <tr>
            <!-- td><?= $row['ID_MUCHACHO'] ?></td -->
            <td><?= htmlspecialchars($row['NOMBRE_MUC']) ?></td>
            <td><?= htmlspecialchars($row['APELLIDO_MUC']) ?></td>
            <td><?= htmlspecialchars($row['NOMBRE_DEP']) ?></td>
            <td><?= htmlspecialchars($row['FECHA_NA']) ?></td>

            <!-- Botones Alergias y Premios -->
            <td><a href="alergias.php?id=<?= $row['ID_MUCHACHO'] ?>" class="btn btn-warning btn-sm">Alergias</a></td>
            <td><a href="reg_premios.php?id=<?= $row['ID_MUCHACHO'] ?>"  class="btn btn-info btn-sm">Premios</a></td>

            <!-- Botones Editar y Eliminar -->
            <td>
              <!-- Editar → modal reutilizable -->
              <button class="btn btn-primary btn-sm"
                      data-bs-toggle="modal" data-bs-target="#modalEditar"
                      data-id="<?= $row['ID_MUCHACHO'] ?>"
                      data-nombre="<?= htmlspecialchars($row['NOMBRE_MUC']) ?>"
                      data-apellido="<?= htmlspecialchars($row['APELLIDO_MUC']) ?>"
                      data-departamento="<?= htmlspecialchars($row['NOMBRE_DEP']) ?>"
                      data-fecha="<?= $row['FECHA_NA'] ?>">
                Editar
              </button>

              <!-- Eliminar → confirm() -->
              <form method="post" class="d-inline"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $row['ID_MUCHACHO'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div><!-- /.table-responsive -->

  </div><!-- /.container -->
</section>

<!-- ======= MODAL REUTILIZABLE DE EDICIÓN ======= -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
      <form method="post">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id" id="edit-id">

        <div class="modal-header">
          <h5 class="modal-title">Editar Muchacho</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" id="edit-apellido" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Departamento</label>
           <select name="departamento" id="edit-departamento" class="form-select" required>
              <?php
                $departamentos = $conexion->query("SELECT ID_DEPARTAMENTO, NOMBRE_DEP FROM departamento");
                while ($dep = $departamentos->fetch_assoc()):
              ?>
                <option value="<?= $dep['ID_DEPARTAMENTO'] ?>">
                  <?= htmlspecialchars($dep['NOMBRE_DEP']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" name="fecha_na" id="edit-fecha" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========= Script para rellenar el modal de edición ========= -->
<script>
document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('edit-id').value           = btn.dataset.id;
  document.getElementById('edit-nombre').value       = btn.dataset.nombre;
  document.getElementById('edit-apellido').value     = btn.dataset.apellido;
  document.getElementById('edit-departamento').value = btn.dataset.departamento;
  document.getElementById('edit-fecha').value        = btn.dataset.fecha;
});
</script>

<script>
(() => {
  /* ------------- Referencias ------------- */
  const searchInput = document.getElementById('tableSearch');
  const sortSelect  = document.getElementById('tableSort');
  const tbody       = document.querySelector('#TablaNavegantes tbody');

  /* ------------- Búsqueda ------------- */
  searchInput.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    for (const row of tbody.rows) {
      // concatena el texto de todas las celdas de la fila
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    }
  });

  /* ------------- Orden asc/desc ------------- */
  sortSelect.addEventListener('change', () => {
    const rows = Array.from(tbody.rows)
                       .filter(r => r.style.display !== 'none'); // solo visibles
    rows.sort((a, b) => {
      const aName = a.cells[1].innerText.trim().toLowerCase(); // columna Nombre
      const bName = b.cells[1].innerText.trim().toLowerCase();
      if (aName === bName) return 0;
      return sortSelect.value === 'asc'
             ? aName.localeCompare(bName)
             : bName.localeCompare(aName);
    });
    // re‑añade las filas ordenadas
    rows.forEach(r => tbody.appendChild(r));
  });
})();
</script>

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
</html>
