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

$id = $_GET['id'] ?? null;
if (!$id) {
  die("ID no especificado.");
}

// cerrar sesion//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../../../index.php"); // Cambia a la página de login
    exit;
}

// Obtener datos del muchacho
$muchacho = $conexion->query("SELECT * FROM muchachos WHERE ID_MUCHACHO = $id")->fetch_assoc();

// Insertar nuevo dato médico
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
  agregar_dato_medico($conexion, $_POST['id_muchacho'], $_POST['alergias'], $_POST['medicamentos']);
}

// Editar datos existentes
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
  editar_dato_medico($conexion, $_POST['id_med'], $_POST['alergias'], $_POST['medicamentos']);
}

// Eliminar dato médico
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
  eliminar_dato_medico($conexion, $_POST['id_med']);
}

// Obtener todos los registros médicos del muchacho
$registros = $conexion->query("SELECT * FROM datos_med WHERE ID_MUCHACHO = $id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Datos Médicos</title>
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
    <h2 class="text-warning mb-4">Datos Médicos de <?= htmlspecialchars($muchacho['NOMBRE_MUC'] . " " . $muchacho['APELLIDO_MUC']) ?></h2>

    <button class="btn btn-success mb-3" onclick="document.getElementById('formAgregar').classList.toggle('d-none')">
      Agregar nuevo registro
    </button>

    <!-- Formulario oculto para agregar -->
    <div class="card bg-secondary p-3 mb-4 d-none" id="formAgregar">
      <form method="post">
        <input type="hidden" name="accion" value="agregar">
        <input type="hidden" name="id_muchacho" value="<?= $id ?>">
        <div class="mb-3">
          <label>Alergias:</label>
          <textarea name="alergias" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label>Medicamentos:</label>
          <textarea name="medicamentos" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </form>
    </div>

    <!-- Tabla de registros -->
    <input type="text" id="buscarInput" class="form-control mb-2" placeholder="Buscar...">

    <table class="table table-dark table-bordered table-hover">
      <thead class="table-warning">
        <tr>
          <th>Alergias</th>
          <th>Medicamentos</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tablaRegistros">
        <?php while ($row = $registros->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['ALERGIAS'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['MEDICAMENTOS'] ?? '') ?></td>
            <td>
              <!-- Botón Editar -->
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?= $row['ID_DATOS'] ?>">Editar</button>

              <!-- Modal edición -->
              <div class="modal fade" id="editarModal<?= $row['ID_DATOS'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content bg-dark text-light">
                    <form method="post">
                      <input type="hidden" name="accion" value="editar">
                      <input type="hidden" name="id_med" value="<?= $row['ID_DATOS'] ?>">
                      <div class="modal-header">
                        <h5 class="modal-title">Editar Registro Médico</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label>Alergias:</label>
                          <textarea name="alergias" class="form-control" rows="3"><?= htmlspecialchars($row['ALERGIAS'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                          <label>Medicamentos:</label>
                          <textarea name="medicamentos" class="form-control" rows="3"><?= htmlspecialchars($row['MEDICAMENTOS'] ?? '') ?></textarea>
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

              <!-- Formulario eliminar -->
              <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?')">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_med" value="<?= $row['ID_DATOS'] ?>">
                <button class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <button onclick="history.back()" class="btn btn-outline-light mt-4">Volver</button>

  </div>

  <!-- Script búsqueda en tabla -->
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
