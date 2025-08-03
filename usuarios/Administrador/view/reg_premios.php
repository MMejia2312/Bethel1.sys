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

// Validar ID
if (!isset($_GET['id'])) {
    echo "ID de muchacho no proporcionado.";
    exit;
}

$id_muchacho = intval($_GET['id']);

// Manejar acciones POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === "agregar") {
            agregar_premio($conexion, $id_muchacho, $_POST['id_premio'], $_POST['estado'], $_POST['comentario']);
        } elseif ($_POST['accion'] === "editar") {
            editar_premio($conexion, $_POST['id_reg'], $_POST['estado'], $_POST['comentario']);
        } elseif ($_POST['accion'] === "eliminar") {
            eliminar_premio($conexion, $_POST['id_reg']);
        }
    }
}

// Obtener registros de premios
$premios = $conexion->query("
    SELECT pg.ID_REG, p.NOMBRE_PRE, ep.DESCRIPCION AS ESTADO_DESC, pg.FECHA_COMPLETADO, pg.COMENTARIO, ep.ID_ESTADO
    FROM premios_ganados pg
    JOIN premios p ON pg.ID_PREMIO = p.ID_PREMIO
    JOIN estado_premio ep ON pg.ESTADO = ep.ID_ESTADO
    WHERE pg.ID_MUCHACHO = $id_muchacho
");

$lista_premios = $conexion->query("SELECT ID_PREMIO, NOMBRE_PRE FROM premios");
$estados = $conexion->query("SELECT ID_ESTADO, DESCRIPCION FROM estado_premio");

?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Premios del Muchacho</title>
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


<div class="container py-5">
  <h2 class="mb-4 text-warning">Premios Ganados</h2>

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
          <option value="asc">Premios A → Z</option>
        <option value="desc">Premios Z → A</option>
      </select>
    </div>

  <!-- Botón para mostrar el modal -->
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregarPremio">Agregar Premio</button>

  <!-- Modal Agregar -->
  <div class="modal fade" id="modalAgregarPremio" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-dark text-light">
        <form method="post">
          <input type="hidden" name="accion" value="agregar">
          <div class="modal-header">
            <h5 class="modal-title">Agregar Premio</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Premio</label>
              <select name="id_premio" class="form-select" required>
                <?php while ($p = $lista_premios->fetch_assoc()): ?>
                  <option value="<?= $p['ID_PREMIO'] ?>"><?= $p['NOMBRE_PRE'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select" required>
                <?php while ($e = $estados->fetch_assoc()): ?>
                  <option value="<?= $e['ID_ESTADO'] ?>"><?= $e['DESCRIPCION'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Comentario</label>
              <textarea name="comentario" class="form-control" rows="3" maxlength="800"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tabla de premios -->
   <table id="tablaPremios" class="table table-dark table-striped">
    <thead class="table-warning text-dark">
      <tr>
        <th>Premio</th>
        <th>Estado</th>
        <th>Fecha Completado</th>
        <th>Comentario</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $premios->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['NOMBRE_PRE']) ?></td>
          <td><?= $row['ESTADO_DESC'] ?></td>
          <td><?= $row['FECHA_COMPLETADO'] ?: '-' ?></td>
          <td><?= htmlspecialchars($row['COMENTARIO']) ?></td>
          <td>
            <!-- Botón Editar -->
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['ID_REG'] ?>">Editar</button>

            <!-- Modal de edición -->
            <div class="modal fade" id="modalEditar<?= $row['ID_REG'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">
                  <form method="post">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id_reg" value="<?= $row['ID_REG'] ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Editar Premio</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                          <?php
                            $estados2 = $conexion->query("SELECT ID_ESTADO, DESCRIPCION FROM estado_premio");
                            while ($e = $estados2->fetch_assoc()):
                          ?>
                            <option value="<?= $e['ID_ESTADO'] ?>" <?= ($e['ID_ESTADO'] == $row['ID_ESTADO']) ? 'selected' : '' ?>>
                              <?= $e['DESCRIPCION'] ?>
                            </option>
                          <?php endwhile; ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="3"><?= htmlspecialchars($row['COMENTARIO']) ?></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-primary" type="submit">Guardar</button>
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Formulario Eliminar -->
            <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este premio?');">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="id_reg" value="<?= $row['ID_REG'] ?>">
              <button class="btn btn-sm btn-danger">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="javascript:history.back()" class="btn btn-secondary mt-4">Volver</a>
</div>



<script>
(() => {
  /* ------------- Referencias ------------- */
  const searchInput = document.getElementById('tableSearch');
  const sortSelect  = document.getElementById('tableSort');
  const tbody = document.querySelector('#tablaPremios tbody');


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
      const aName = a.cells[0].innerText.trim().toLowerCase();
       const bName = b.cells[0].innerText.trim().toLowerCase();
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


</body>
</html>
