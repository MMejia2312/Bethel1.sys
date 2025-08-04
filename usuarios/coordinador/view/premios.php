<?php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) {
    $_SESSION['error'] = "Debes iniciar sesión primero.";
    header("Location: ../../../index.php");  
    exit();
}
include '../../../includes/coneccion.php';
include '../../../includes/funcions.php';

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'agregar') {
    agregar_premio_tabla($conexion, $_POST['nombre_pre'], $_POST['id_departamento'], $_POST['senda'], $_POST['requisitos']);
    header("Location: premios.php?ok=1");
    exit;
}

// ---------- Editar ----------//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    editar_premio_tabla($conexion, $_POST['id'], $_POST['nombre_pre'], $_POST['id_departamento'], $_POST['senda'], $_POST['requisitos']);
    header("Location: premios.php?ok=1");
    exit;
}

// ---------- Eliminar ----------//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
    eliminar_premio_tabla($conexion, $_POST['id']);
    header("Location: premios.php?eliminado=1");
    exit;
}

// Consulta Premios
$consultaGEN = "SELECT p.*, d.NOMBRE_DEP 
                FROM premios p
                LEFT JOIN departamento d ON p.ID_DEPARTAMENTO = d.ID_DEPARTAMENTO";
$guardar = $conexion->query($consultaGEN);
?>


<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Premios</title>
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
  <div class="container" id="TablaPremios">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Gestión de Premios</span></h1>

    <div class="d-sm-flex align-items-center justify-content">
      <input type="text" id="tableSearch" class="form-control me-2 mb-2" style="max-width:240px" placeholder="Buscar…">
      <select id="tableSort" class="form-select mb-2" style="max-width:180px">
        <option value="">Ordenar…</option>
        <option value="asc">Nombre A → Z</option>
        <option value="desc">Nombre Z → A</option>
      </select>
      <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Premio</button>
    </div>

    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <th>Nombre Premio</th>
            <th>Departamento</th>
            <th>Senda</th>
            <th>Requisitos</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="color:rgb(255,255,0)">
        <?php while ($row = $guardar->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['NOMBRE_PRE']) ?></td>
            <td><?= htmlspecialchars($row['NOMBRE_DEP']) ?></td>
            <td><?= htmlspecialchars($row['SENDA']) ?></td>
            <td><?= htmlspecialchars($row['REQUISITOS']) ?></td>
            <td>
              <button class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#modalEditar"
                data-id="<?= $row['ID_PREMIO'] ?>"
                data-nombre="<?= htmlspecialchars($row['NOMBRE_PRE']) ?>"
                data-departamento="<?= $row['ID_DEPARTAMENTO'] ?>"
                data-senda="<?= htmlspecialchars($row['SENDA']) ?>"
                data-requisitos="<?= htmlspecialchars($row['REQUISITOS']) ?>">
                  Editar
              </button>

              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $row['ID_PREMIO'] ?>">
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

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="agregar">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Premio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre_pre" class="form-control mb-2" placeholder="Nombre del Premio" required>
        <select name="departamento" class="form-select mb-2" required>
          <option value="">Seleccione Departamento</option>
          <?php $deps = $conexion->query("SELECT * FROM departamento");
          while ($d = $deps->fetch_assoc()): ?>
            <option value="<?= $d['ID_DEPARTAMENTO'] ?>"><?= $d['NOMBRE_DEP'] ?></option>
          <?php endwhile; ?>
        </select>
        <input type="text" name="senda" class="form-control mb-2" placeholder="Senda" required>
        <textarea name="requisitos" class="form-control mb-2" placeholder="Requisitos" required></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title">Editar Premio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre_pre" id="edit-nombre" class="form-control mb-2" required>
        <select name="id_departamento" class="form-select mb-2" id="edit-departamento" required>
          <?php
          $departamentos = $conexion->query("SELECT ID_DEPARTAMENTO, NOMBRE_DEP FROM departamento");
          while ($dep = $departamentos->fetch_assoc()):
          ?>
            <option value="<?= $dep['ID_DEPARTAMENTO'] ?>">
              <?= htmlspecialchars($dep['NOMBRE_DEP']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <input type="text" name="senda" id="edit-senda" class="form-control mb-2" required>
        <textarea name="requisitos" id="edit-requisitos" class="form-control mb-2" required></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('edit-id').value = btn.dataset.id;
  document.getElementById('edit-nombre').value = btn.dataset.nombre;
  document.getElementById('edit-senda').value = btn.dataset.senda;
  document.getElementById('edit-requisitos').value = btn.dataset.requisitos;

  const departamentoSelect = document.getElementById('edit-departamento');
  departamentoSelect.value = btn.dataset.departamento;
});

</script>

<script>
(() => {
  const searchInput = document.getElementById('tableSearch');
  const sortSelect  = document.getElementById('tableSort');
  const tbody       = document.querySelector('#TablaPremios tbody');

  searchInput.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    for (const row of tbody.rows) {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    }
  });

  sortSelect.addEventListener('change', () => {
    const rows = Array.from(tbody.rows)
                       .filter(r => r.style.display !== 'none');
    rows.sort((a, b) => {
      const aName = a.cells[0].innerText.trim().toLowerCase();
      const bName = b.cells[0].innerText.trim().toLowerCase();
      if (aName === bName) return 0;
      return sortSelect.value === 'asc'
             ? aName.localeCompare(bName)
             : bName.localeCompare(aName);
    });
    rows.forEach(r => tbody.appendChild(r));
  });
})();
</script>

</body>
</html>
  