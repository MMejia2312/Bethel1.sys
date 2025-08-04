<?php

session_start();
if (!isset($_SESSION['ID_USUARIO'])) {
    $_SESSION['error'] = "Debes iniciar sesión primero.";
    header("Location: ../../index.php");  // Ajusta ruta si es necesario
    exit();
}
/*--------------------------------------------------
  Conexión + funciones CRUD
--------------------------------------------------*/
include '../../../includes/coneccion.php';
require '../../../includes/funcions.php'; 

// Evitar cache del navegador para prevenir acceso con botón atrás después de cerrar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// cerrar sesion//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../../../index.php"); // Cambia a la página de login
    exit;
}

// ---------- Agregar ----------//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'agregar') {
        agregar_nivelUs($conexion, $_POST['cargo']);
        header("Location: NivelesUsuarios.php?ok=1"); exit;
    }

    if ($_POST['accion'] === 'editar') {
        editar_nivelUs($conexion, $_POST['id'], $_POST['cargo']);
        header("Location: NivelesUsuarios.php?ok=1"); exit;
    }

    if ($_POST['accion'] === 'eliminar') {
        eliminar_nivelUs($conexion, $_POST['id']);
        header("Location: NivelesUsuarios.php?eliminado=1"); exit;
    }
}

//----------------------------------------------------------------//

// Consulta niveles
$consultaGEN = "SELECT * FROM nivel";
$guardar = $conexion->query($consultaGEN);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Niveles de Acceso por usuario</title>

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
            <li><a class="dropdown-item" href="senoritas.php">Señoritas</a></li>
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

<section class="bg-dark text-light text-center p-5">
  <div class="container" id="TablaNiveles">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Niveles de Usuarios dentro del Sistema</span></h1>
    <div class="d-sm-flex align-items-center justify-content">
      <input type="text" id="tableSearch" class="form-control me-2 mb-2" style="max-width:240px" placeholder="Buscar…">
      <select id="tableSort" class="form-select mb-2" style="max-width:180px">
        <option value="">Ordenar…</option>
        <option value="asc">Nombre A → Z</option>
        <option value="desc">Nombre Z → A</option>
      </select>
      <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Nivel de Usuario</button>
    </div>
    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <th>ID Nivel</th>
            <th>Cargo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="color:rgb(255,255,0)">
        <?php while ($row = $guardar->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['ID_NIVEL']) ?></td>
            <td><?= htmlspecialchars($row['DESCRIP']) ?></td>
            <td>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar" data-id="<?= $row['ID_NIVEL'] ?>" data-nombre="<?= htmlspecialchars($row['DESCRIP']) ?>">Editar</button>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $row['ID_NIVEL'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Modal Edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
      <form method="post">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Editar Nivel</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" id="edit-cargo" class="form-control" required>
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

<script>
document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('edit-id').value = btn.dataset.id;
  document.getElementById('edit-cargo').value = btn.dataset.nombre;
});

(() => {
  const searchInput = document.getElementById('tableSearch');
  const sortSelect  = document.getElementById('tableSort');
  const tbody       = document.querySelector('#TablaNiveles tbody');

  searchInput.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    for (const row of tbody.rows) {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    }
  });

  sortSelect.addEventListener('change', () => {
    const rows = Array.from(tbody.rows).filter(r => r.style.display !== 'none');
    rows.sort((a, b) => {
      const aName = a.cells[1].innerText.trim().toLowerCase();
      const bName = b.cells[1].innerText.trim().toLowerCase();
      return sortSelect.value === 'asc' ? aName.localeCompare(bName) : bName.localeCompare(aName);
    });
    rows.forEach(r => tbody.appendChild(r));
  });
})();
</script>

<section id="Foother" class="bottom">
  <footer>
    <div class="bg-dark text-light text-center p-5">
      <h8><img src="./img/mmejia.jpg" alt="" width="25px"> Mario Edgardo Mejía Guevara <img src="./img/mmejia.jpg" alt="" width="25px"></h8><br>
      <h8><img src="./img/Emblema-Exploradores-del-Rey.png" alt="" width="35px"> Exploradores del Rey, Destacamento Bethel 1 <img src="./img/Emblema-Exploradores-del-Rey.png" alt="" width="35px"></h8><br>
      <h8>Todos los derechos reservados.</h8><br>
      <a type="button" class="btn btn-primary my-5" href="#">Volver al Inicio</a>
    </div>
  </footer>
</section>
</body>
</html>
