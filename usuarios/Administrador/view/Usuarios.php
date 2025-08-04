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

// Agregar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'agregar') {
    agregar_usuario($conexion, $_POST['usuario'], $_POST['contrasena'], $_POST['id_lider'], $_POST['id_nivel'], $_POST['estado']);
    header("Location: Usuarios.php?ok=1"); exit;
}

// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'editar') {
    editar_usuario($conexion, $_POST['id'], $_POST['usuario'], $_POST['contrasena'], $_POST['id_lider'], $_POST['id_nivel'], $_POST['estado']);
    header("Location: Usuarios.php?ok=1"); exit;
}

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
    eliminar_usuario($conexion, $_POST['id']);
    header("Location: Usuarios.php?eliminado=1"); exit;
}

// Habilitar/Deshabilitar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'toggle_estado') {
    toggle_estado_usuario($conexion, $_POST['id'], $_POST['nuevo_estado']);
    header("Location: Usuarios.php?cambio_estado=1"); exit;
}

// Consulta con joins
$sql = "SELECT u.ID_USUARIO, u.USUARIO, l.NOMBRE_LI, l.APELLIDO_LI, n.DESCRIP, e.ESTADO_US, u.ESTADO
        FROM usuarios u
        LEFT JOIN lideres l ON u.ID_LIDER = l.ID_LIDER
        LEFT JOIN nivel n ON u.ID_NIVEL = n.ID_NIVEL
        LEFT JOIN estado_usuarios e ON u.ESTADO = e.ID_ESTADO";

$usuarios = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-dark text-light">
<div class="container my-5">

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

  <h1 class="text-center mb-4" style="color:yellow">Gestión de Usuarios</h1>

  <div class="d-sm-flex align-items-center justify-content-between mb-3">
    <input type="text" id="tableSearch" class="form-control me-2" placeholder="Buscar…" style="max-width: 250px;">
    <select id="tableSort" class="form-select" style="max-width: 200px;">
      <option value="">Ordenar por nombre…</option>
      <option value="asc">A-Z</option>
      <option value="desc">Z-A</option>
    </select>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Usuario</button>
  </div>

  <div class="table-responsive">
    <table class="table table-dark table-hover">
      <thead style="color:yellow">
        <tr>
          <th>Usuario</th>
          <th>Líder</th>
          <th>Nivel</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody style="color:rgb(255,255,0)">
        <?php while ($u = $usuarios->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($u['USUARIO']) ?></td>
            <td><?= htmlspecialchars($u['NOMBRE_LI'] . ' ' . $u['APELLIDO_LI']) ?></td>
            <td><?= htmlspecialchars($u['DESCRIP']) ?></td>
            <td><?= htmlspecialchars($u['ESTADO_US']) ?></td>
            <td>
              <!-- Editar -->
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['ID_USUARIO'] ?>">Editar</button>

              <!-- Eliminar -->
              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $u['ID_USUARIO'] ?>">
                <button class="btn btn-danger btn-sm">Eliminar</button>
              </form>

              <!-- Activar/Desactivar -->
              <form method="post" class="d-inline">
                <input type="hidden" name="accion" value="toggle_estado">
                <input type="hidden" name="id" value="<?= $u['ID_USUARIO'] ?>">
                <input type="hidden" name="nuevo_estado" value="<?= $u['ESTADO'] == 1 ? 2 : 1 ?>">
                <button class="btn btn-warning btn-sm"><?= $u['ESTADO'] == 1 ? 'Deshabilitar' : 'Habilitar' ?></button>
              </form>
            </td>
          </tr>

          <!-- Modal Editar -->
          <div class="modal fade" id="modalEditar<?= $u['ID_USUARIO'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <form method="post" class="modal-content bg-dark text-light">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $u['ID_USUARIO'] ?>">
                <div class="modal-header">
                  <h5 class="modal-title">Editar Usuario</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="text" name="usuario" class="form-control mb-2" value="<?= $u['USUARIO'] ?>" required>
                  <input type="password" name="contrasena" class="form-control mb-2" placeholder="Nueva contraseña">

                  <select name="id_lider" class="form-select mb-2">
                    <option value="">Seleccionar líder</option>
                    <?php
                      $lideres = $conexion->query("SELECT ID_LIDER, NOMBRE_LI, APELLIDO_LI FROM lideres");
                      while ($l = $lideres->fetch_assoc()):
                    ?>
                      <option value="<?= $l['ID_LIDER'] ?>" <?= ($l['ID_LIDER'] == $u['ID_LIDER']) ? 'selected' : '' ?>>
                        <?= $l['NOMBRE_LI'] . ' ' . $l['APELLIDO_LI'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>

                  <select name="id_nivel" class="form-select mb-2">
                    <option value="">Seleccionar nivel</option>
                    <?php
                      $niveles = $conexion->query("SELECT * FROM nivel");
                      while ($n = $niveles->fetch_assoc()):
                    ?>
                      <option value="<?= $n['ID_NIVEL'] ?>" <?= ($n['ID_NIVEL'] == $u['ID_NIVEL']) ? 'selected' : '' ?>>
                        <?= $n['DESCRIP'] ?>
                      </option>
                    <?php endwhile; ?>
                  </select>

                  <select name="estado" class="form-select mb-2">
                    <?php
                      $estados = $conexion->query("SELECT * FROM estado_usuarios");
                      while ($e = $estados->fetch_assoc()):
                    ?>
                      <option value="<?= $e['ID_ESTADO'] ?>" <?= ($e['ID_ESTADO'] == $u['ESTADO']) ? 'selected' : '' ?>>
                        <?= $e['ESTADO_US'] ?>
                      </option>
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

        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="agregar">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="usuario" class="form-control mb-2" placeholder="Usuario" required>
        <input type="password" name="contrasena" class="form-control mb-2" placeholder="Contraseña" required>

        <select name="id_lider" class="form-select mb-2" required>
          <option value="">Seleccionar líder</option>
          <?php
            $lideres = $conexion->query("SELECT ID_LIDER, NOMBRE_LI, APELLIDO_LI FROM lideres");
            while ($l = $lideres->fetch_assoc()):
          ?>
            <option value="<?= $l['ID_LIDER'] ?>"><?= $l['NOMBRE_LI'] . ' ' . $l['APELLIDO_LI'] ?></option>
          <?php endwhile; ?>
        </select>

        <select name="id_nivel" class="form-select mb-2" required>
          <option value="">Seleccionar nivel</option>
          <?php
            $niveles = $conexion->query("SELECT * FROM nivel");
            while ($n = $niveles->fetch_assoc()):
          ?>
            <option value="<?= $n['ID_NIVEL'] ?>"><?= $n['DESCRIP'] ?></option>
          <?php endwhile; ?>
        </select>

        <select name="estado" class="form-select mb-2" required>
          <option value="">Seleccionar estado</option>
          <?php
            $estados = $conexion->query("SELECT * FROM estado_usuarios");
            while ($e = $estados->fetch_assoc()):
          ?>
            <option value="<?= $e['ID_ESTADO'] ?>"><?= $e['ESTADO_US'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Agregar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts para filtros -->
<script>
document.getElementById('tableSearch').addEventListener('input', function () {
  const value = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
  });
});

document.getElementById('tableSort').addEventListener('change', function () {
  const dir = this.value;
  const rows = Array.from(document.querySelectorAll('tbody tr')).filter(r => r.style.display !== 'none');
  rows.sort((a, b) => {
    const textA = a.cells[0].innerText.toLowerCase();
    const textB = b.cells[0].innerText.toLowerCase();
    return dir === 'asc' ? textA.localeCompare(textB) : textB.localeCompare(textA);
  });
  rows.forEach(r => r.parentElement.appendChild(r));
});
</script>
</body>
</html>
