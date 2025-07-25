<?php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) {
    $_SESSION['error'] = "Debes iniciar sesión primero.";
    header("Location: ../../../index.php");  
    exit();
}
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

// AGREGAR EVENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'agregar') {
    agregar_evento($conexion, $_POST['nombre'], $_POST['fecha'], $_POST['lugar'], $_POST['estado']);
    header("Location: eventos.php?ok=1");
    exit;
}

// EDITAR EVENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    editar_evento($conexion, $_POST['id'], $_POST['nombre'], $_POST['fecha'], $_POST['lugar'], $_POST['estado']);
    header('Location: eventos.php?ok=1'); exit;
}

// ELIMINAR EVENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
    eliminar_evento($conexion, $_POST['id']);
    header('Location: eventos.php?eliminado=1'); exit;
}

// CONSULTA GENERAL
$consultaGEN = "SELECT * FROM eventos";
$guardar = $conexion->query($consultaGEN);
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eventos Bethel 1</title>
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

<section class="bg-dark text-light text-center p-5">
  <div class="container" id="TablaEventos">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Eventos Registrados</span></h1>

    <div class="d-sm-flex align-items-center justify-content">
      <input type="text" id="tableSearch" class="form-control me-2 mb-2" style="max-width:240px" placeholder="Buscar…">
      <select id="tableSort" class="form-select mb-2" style="max-width:180px">
        <option value="">Ordenar…</option>
        <option value="asc">Nombre A → Z</option>
        <option value="desc">Nombre Z → A</option>
      </select>
      <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Evento</button>
    </div>

    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Lugar</th>
            <th>Estado</th>
            <th>Asistentes</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="color:rgb(255,255,0)">
        <?php while ($row = $guardar->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['NOMBRE_EVENTO']) ?></td>
            <td><?= htmlspecialchars($row['FECHA']) ?></td>
            <td><?= htmlspecialchars($row['LUGAR']) ?></td>
            <td><?= htmlspecialchars($row['ESTADO']) ?></td>
            <td>
              <a href="asistentesEventos.php?id=<?= $row['ID_EVENTO'] ?>" class="btn btn-warning btn-sm">Asistentes</a>
            </td>
            <td>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                      data-id="<?= $row['ID_EVENTO'] ?>"
                      data-nombre="<?= htmlspecialchars($row['NOMBRE_EVENTO']) ?>"
                      data-fecha="<?= $row['FECHA'] ?>"
                      data-lugar="<?= htmlspecialchars($row['LUGAR']) ?>"
                      data-estado="<?= htmlspecialchars($row['ESTADO']) ?>">
                Editar
              </button>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este evento?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $row['ID_EVENTO'] ?>">
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

<!-- MODAL AGREGAR EVENTO -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="agregar">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Evento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
        <input type="date" name="fecha" class="form-control mb-2" required>
        <input type="text" name="lugar" class="form-control mb-2" placeholder="Lugar" required>
        <select name="estado" class="form-select mb-2" required>
          <option value="Programado">Programado</option>
          <option value="Realizado">Realizado</option>
          <option value="Cancelado">Cancelado</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR EVENTO -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title">Editar Evento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre" id="edit-nombre" class="form-control mb-2" required>
        <input type="date" name="fecha" id="edit-fecha" class="form-control mb-2" required>
        <input type="text" name="lugar" id="edit-lugar" class="form-control mb-2" required>
        <select name="estado" id="edit-estado" class="form-select mb-2" required>
          <option value="Programado">Programado</option>
          <option value="Realizado">Realizado</option>
          <option value="Cancelado">Cancelado</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
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
  document.getElementById('edit-fecha').value = btn.dataset.fecha;
  document.getElementById('edit-lugar').value = btn.dataset.lugar;
  document.getElementById('edit-estado').value = btn.dataset.estado;
});

// Búsqueda y Ordenación
(() => {
  const searchInput = document.getElementById('tableSearch');
  const sortSelect = document.getElementById('tableSort');
  const tbody = document.querySelector('#TablaEventos tbody');

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
      const aName = a.cells[0].innerText.trim().toLowerCase();
      const bName = b.cells[0].innerText.trim().toLowerCase();
      return sortSelect.value === 'asc' ? aName.localeCompare(bName) : bName.localeCompare(aName);
    });
    rows.forEach(r => tbody.appendChild(r));
  });
})();
</script>

</body>
</html>
