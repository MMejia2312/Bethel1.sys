<?php
include '../../../includes/coneccion.php';
include '../../../includes/funcions.php';

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

$id = $_GET['id'] ?? null;
if (!$id) {
  die("ID no especificado.");
}

// Obtener datos del insumo
$insumo = $conexion->query("SELECT * FROM inventario WHERE ID_INSUMO = $id")->fetch_assoc();

// Agregar salida
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

  $id_insumo = $_POST['ID_INSUMO'] ?? $id; // usa POST si está disponible
  agregar_salida_insumo($conexion, $_POST['ID_INSUMO'], $_POST['FECHA_SALIDA'], $_POST['FECHA_VUELTA'], $_POST['ENCARGADO'], $_POST['ESTADO']);
  header("Location: salida_insumos.php?id=$id_insumo");
  exit;
}


// Editar salida
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
  editar_salida_insumo($conexion, $_POST['ID_INS_SAL'], $_POST['FECHA_SALIDA'], $_POST['FECHA_VUELTA'],  $_POST['ENCARGADO'], $_POST['ESTADO']);
  header("Location: salida_insumos.php?id=$id");
  exit;
}

// Eliminar salida
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
  eliminar_salida_insumo($conexion, $_POST['ID_INS_SAL']);
  header("Location: salida_insumos.php?id=$id");
  exit;
}

// Obtener todos los registros
$registros = $conexion->query("SELECT s.*, i.NOMBRE FROM salida_insumos s 
                                JOIN inventario i ON s.ID_INSUMO = i.ID_INSUMO
                                WHERE s.ID_INSUMO = $id");
?>

<!-- HTML igual que antes, sin cambios -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Salida de Insumos</title>
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


  <div class="container my-5">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Salida del registro #<?= htmlspecialchars($insumo['ID_INSUMO'] . ", " . $insumo['NOMBRE']) ?></span></h1>
    
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Salida Insumos</button>
    <input type="hidden" name="ID_INSUMO" value="<?= $id ?>">


    <input type="text" id="buscarInput" class="form-control mb-2" placeholder="Buscar...">

    <div class="table-responsive">
      <table class="table bg-dark">
        <thead style="color:rgb(255,255,0)">
          <tr>
            <th># Registro</th>
            <th>Nombre</th>
            <th>Fecha de Salida</th>
            <th>Fecha de Reingreso</th>
            <th>Encargado</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tablaRegistros">
          <?php while ($row = $registros->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['ID_INS_SAL'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['NOMBRE'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['FECHA_SALIDA'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['FECHA_VUELTA'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ENCARGADO'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['ESTADO'] ?? '') ?></td>
              <td>
                <!-- Botón Editar -->
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                        data-id_ins_sal="<?= $row['ID_INS_SAL'] ?>"
                        data-fecha_salida="<?= htmlspecialchars($row['FECHA_SALIDA']) ?>"
                        data-fecha_vuelta="<?= htmlspecialchars($row['FECHA_VUELTA']) ?>"
                        data-encargado="<?= $row['ENCARGADO'] ?>"
                        data-estado="<?= $row['ESTADO'] ?>">
                  Editar
                </button>

                <!-- Formulario eliminar -->
                <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?')">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="ID_INS_SAL" value="<?= $row['ID_INS_SAL'] ?>">
                  <button class="btn btn-danger btn-sm">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <button onclick="history.back()" class="btn btn-outline-light mt-4">Volver</button>
  </div>

  <!-- Modal Agregar -->
  <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="post" class="modal-content bg-dark text-light">
        <input type="hidden" name="accion" value="agregar">
        <input type="hidden" name="ID_INSUMO" value="<?= htmlspecialchars($id) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Salida de Insumos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Fecha de Salida</label>
            <input type="date" name="FECHA_SALIDA" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Fecha de Reingreso</label>
            <input type="date" name="FECHA_VUELTA" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">Encargado</label>
            <input type="text" name="ENCARGADO" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Estado</label>
            <select name="ESTADO" class="form-select">
              <option value="Buen Estado">Buen Estado</option>
              <option value="Necesita Revisión">Necesita Revisión</option>
              <option value="Dañado">Dañado</option>
            </select>
          </div>
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
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-light">
        <form method="post">
          <input type="hidden" name="accion" value="editar">
          <input type="hidden" name="ID_INS_SAL" id="edit-id">
          <div class="modal-header">
            <h5 class="modal-title">Editar Insumo</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="date" name="FECHA_SALIDA" id="edit-fecha-salida" class="form-control mb-2" required>
            <input type="date" name="FECHA_VUELTA" id="edit-fecha-vuelta" class="form-control mb-2">
            <input type="text" name="ENCARGADO" id="edit-encargado" class="form-control mb-2" placeholder="Encargado" required>
            <select name="ESTADO" id="edit-estado" class="form-select mb-2">
              <option value="Buen Estado">Buen Estado</option>
              <option value="Necesita Revisión">Necesita Revisión</option>
              <option value="Dañado">Dañado</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Script para rellenar el modal editar -->
  <script>
    document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
      const btn = e.relatedTarget;
      document.getElementById('edit-id').value = btn.dataset.id_ins_sal;
      document.getElementById('edit-fecha-salida').value = btn.dataset.fecha_salida;
      document.getElementById('edit-fecha-vuelta').value = btn.dataset.fecha_vuelta;
      document.getElementById('edit-encargado').value = btn.dataset.encargado;
      document.getElementById('edit-estado').value = btn.dataset.estado;
    });
  </script>

  <!-- Script búsqueda -->
  <script>
    const input = document.getElementById("buscarInput");
    const filas = document.querySelectorAll("#tablaRegistros tr");

    input.addEventListener("input", () => {
      const texto = input.value.toLowerCase();
      filas.forEach(fila => {
        const visible = Array.from(fila.children).some(td =>
          td.textContent.toLowerCase().includes(texto)
        );
        fila.style.display = visible ? "" : "none";
      });
    });
  </script>
</body>
</html>
