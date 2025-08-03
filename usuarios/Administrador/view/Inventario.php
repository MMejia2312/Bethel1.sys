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

// AGREGAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'agregar') {
    agregar_insumo($_POST['nombre'], $_POST['descripcion'], $_POST['entrada'], $_POST['mantenimiento'], $_POST['estado'], $conexion);
    header("Location: Inventario.php?ok=1");
    exit;
}

// EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    editar_insumo($_POST['id'], $_POST['nombre'], $_POST['descripcion'], $_POST['entrada'], $_POST['mantenimiento'], $_POST['estado'], $conexion);
    header("Location: Inventario.php?ok=1");
    exit;
}

// ELIMINAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
    eliminar_insumo($_POST['id'], $conexion);
    header("Location: Inventario.php?eliminado=1");
    exit;
} 

// Consulta
$consultaGEN = "SELECT * FROM inventario";
$guardar = $conexion->query($consultaGEN);
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario Destacamento Bethel 1</title>
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
            <li><a class="dropdown-item" href="señoritas.php">Senoritas</a></li>
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
  <div class="container" id="TablaInventario">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Inventario Destacamento Bethel 1</span></h1>

    <div class="d-sm-flex align-items-center justify-content">
      <input type="text" id="tableSearch" class="form-control me-2 mb-2" style="max-width:240px" placeholder="Buscar…">
      <select id="tableSort" class="form-select mb-2" style="max-width:180px">
        <option value="">Ordenar…</option>
        <option value="asc">Nombre A → Z</option>
        <option value="desc">Nombre Z → A</option>
      </select>
      <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Insumos al Inventario</button>
    </div>

    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha de Entrada</th>
            <th>Fecha Mantenimiento</th>
            <th>Salida</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody style="color:rgb(255,255,0)">
        <?php while ($row = $guardar->fetch_assoc()): ?>
          <tr>
            <td><?= $row['ID_INSUMO'] ?></td>
            <td><?= htmlspecialchars($row['NOMBRE']) ?></td>
            <td><?= htmlspecialchars($row['DESCRIPCION']) ?></td>
            <td><?= htmlspecialchars($row['ENTRADA']) ?></td>
            <td><?= htmlspecialchars($row['MANTENIMIENTO']) ?></td>
            <td>
            <a href="salida_insumos.php?id=<?= $row['ID_INSUMO'] ?>" class="btn btn-warning btn-sm">Salida</a>
            </td>
            <td><?= htmlspecialchars($row['ESTADO']) ?></td>
            <td>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                      data-id="<?= $row['ID_INSUMO'] ?>"
                      data-nombre="<?= htmlspecialchars($row['NOMBRE']) ?>"
                      data-descripcion="<?= htmlspecialchars($row['DESCRIPCION']) ?>"
                      data-entrada="<?= $row['ENTRADA'] ?>"
                      data-mantenimiento="<?= $row['MANTENIMIENTO'] ?>"
                      data-estado="<?= $row['ESTADO'] ?>">
                Editar
              </button>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $row['ID_INSUMO'] ?>">
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
          <h5 class="modal-title">Editar Insumo</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="nombre" id="edit-nombre" class="form-control mb-2" placeholder="Nombre" required>
          <input type="text" name="descripcion" id="edit-descripcion" class="form-control mb-2" placeholder="Descripción">
          <input type="date" name="entrada" id="edit-entrada" class="form-control mb-2">
          <input type="date" name="mantenimiento" id="edit-mantenimiento" class="form-control mb-2">
          <input type="text" name="estado" id="edit-estado" class="form-select" placeholder="Estado">
          
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script: Rellenar modal editar -->
<script>
document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('edit-id').value = btn.dataset.id;
  document.getElementById('edit-nombre').value = btn.dataset.nombre;
  document.getElementById('edit-descripcion').value = btn.dataset.descripcion;
  document.getElementById('edit-entrada').value = btn.dataset.entrada;
  document.getElementById('edit-mantenimiento').value = btn.dataset.mantenimiento;
  document.getElementById('edit-estado').value = btn.dataset.estado;
});
</script>

<!-- Modal agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content bg-dark text-light">
            <input type="hidden" name="accion" value="agregar">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Insumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <h6 class="modal-title">Nombre Insumo</h6>
                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
                <h6 class="modal-title">Descripción</h6>
                <input type="text" name="descripcion" class="form-control mb-2" placeholder="Descripcion" required>

                <div class="mb-3">
                  <label class="form-label">Fecha de Entrada</label>
                  <input type="date" name="entrada" id="edit-fecha" class="form-control" required>
                </div>
                
                <!-- div class="mb-3">
                  <label class="form-label">Fecha Mantenimiento</label>
                  <input type="date" name="mantenimiento" id="edit-fecha" class="form-control" required>
                </div -->
                <h6 class="modal-title">Estado Insumo</h6>
                <select name="estado" class="form-select mb-2">
                    <option value="Buen Estado">Buen Estado</option>
                    <option value="Necesita Revisión">Necesita Revisión</option>
                    <option value="Dañado">Dañado</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<!-- Script: Buscar y ordenar -->
<script>
(() => {
  const searchInput = document.getElementById('tableSearch');
  const sortSelect = document.getElementById('tableSort');
  const tbody = document.querySelector('#TablaInventario tbody');

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
      if (aName === bName) return 0;
      return sortSelect.value === 'asc' ? aName.localeCompare(bName) : bName.localeCompare(aName);
    });
    rows.forEach(r => tbody.appendChild(r));
  });
})();
</script>

</body>
</html>
