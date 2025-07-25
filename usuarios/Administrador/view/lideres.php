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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'agregar') {
        agregar_lider($conexion, $_POST['nombre'], $_POST['apellido'], $_POST['departamento'], $_POST['nivel']);
    } elseif ($_POST['accion'] === 'editar') {
        editar_lider($conexion, $_POST['id'], $_POST['nombre'], $_POST['apellido'], $_POST['departamento'], $_POST['nivel']);
    } elseif ($_POST['accion'] === 'eliminar') {
        eliminar_lider($conexion, $_POST['id']);
    }
}

$consulta = "SELECT l.*, d.NOMBRE_DEP, n.DESCRIP
             FROM lideres l
             LEFT JOIN departamento d ON l.ID_DEPARTAMENTO = d.ID_DEPARTAMENTO
             LEFT JOIN nivel n ON l.NIVEL = n.ID_NIVEL";

$resultado = $conexion->query($consulta);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Líderes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container py-5">

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
  <div class="container" id="TablaLideres">
    <h1 class="my-5"><span style="color:rgba(255,255,0,.9)">Registro de Lideres</span></h1>

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
    <!-- Botón agregar líder -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Líder</button>
    </div>
    <!-- Tabla de líderes -->
    <table class="table table-dark table-hover">
        <thead class="text-warning">
        <tr>
            
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Departamento</th>
            <th>Nivel</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($l = $resultado->fetch_assoc()): ?>
            <tr>
                
                <td><?= $l['NOMBRE_LI'] ?></td>
                <td><?= $l['APELLIDO_LI'] ?></td>
                <td><?= $l['NOMBRE_DEP'] ?? 'Sin asignar' ?></td>
                <td><?= $l['DESCRIP'] ?? 'Sin nivel' ?></td>
                <td>
                    <!-- a href="alergias.php?id=<?= $l['ID_LIDER'] ?>" class="btn btn-warning btn-sm">Alergias</a -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $l['ID_LIDER'] ?>">Editar</button>
                    <form method="post" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este líder?');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= $l['ID_LIDER'] ?>">
                        <button class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>

            <!-- Modal editar -->
           <!-- Modal para editar líder -->
<div class="modal fade" id="modalEditar<?= $l['ID_LIDER'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id" value="<?= $l['ID_LIDER'] ?>">

      <div class="modal-header">
        <h5 class="modal-title">Editar Líder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" value="<?= $l['NOMBRE_LI'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Apellido</label>
          <input type="text" name="apellido" class="form-control" value="<?= $l['APELLIDO_LI'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Departamento</label>
          <select name="departamento" class="form-select">
            <option value="">Sin departamento</option>
            <?php
              $deps = $conexion->query("SELECT * FROM departamento");
              while ($d = $deps->fetch_assoc()):
            ?>
              <option value="<?= $d['ID_DEPARTAMENTO'] ?>" <?= ($l['ID_DEPARTAMENTO'] == $d['ID_DEPARTAMENTO']) ? 'selected' : '' ?>>
                <?= $d['NOMBRE_DEP'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Nivel</label>
          <select name="nivel" class="form-select">
            <option value="">Sin nivel</option>
            <?php
              $niveles = $conexion->query("SELECT * FROM nivel");
              while ($n = $niveles->fetch_assoc()):
            ?>
              <option value="<?= $n['ID_NIVEL'] ?>" <?= ($l['NIVEL'] == $n['ID_NIVEL']) ? 'selected' : '' ?>>
                <?= $n['DESCRIP'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content bg-dark text-light">
            <input type="hidden" name="accion" value="agregar">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Líder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
                <input type="text" name="apellido" class="form-control mb-2" placeholder="Apellido" required>

                <select name="departamento" class="form-select mb-2">
                    <option value="">Sin departamento</option>
                    <?php
                    $deps = $conexion->query("SELECT * FROM departamento");
                    while ($d = $deps->fetch_assoc()):
                    ?>
                        <option value="<?= $d['ID_DEPARTAMENTO'] ?>"><?= $d['NOMBRE_DEP'] ?></option>
                    <?php endwhile; ?>
                </select>

                <select name="nivel" class="form-select mb-2">
                    <option value="">Sin nivel</option>
                    <?php
                    $niv = $conexion->query("SELECT * FROM nivel");
                    while ($n = $niv->fetch_assoc()):
                    ?>
                        <option value="<?= $n['ID_NIVEL'] ?>"><?= $n['DESCRIP'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
  /* ------------- Referencias ------------- */
  const searchInput = document.getElementById('tableSearch');
  const sortSelect  = document.getElementById('tableSort');
  const tbody       = document.querySelector('#TablaLideres tbody');

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

