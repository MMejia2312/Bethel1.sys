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



$id_evento = $_GET['id'] ?? null;

if (!$id_evento) {
    die("ID de evento no especificado.");
}

// cerrar sesion//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../../../index.php"); // Cambia a la página de login
    exit;
}

$evento = $conexion->query("SELECT * FROM eventos WHERE ID_EVENTO = $id_evento")->fetch_assoc();
if (!$evento) {
    die("Evento no encontrado.");
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $id_lider = !empty($_POST['id_lider']) ? $_POST['id_lider'] : null;
        $id_muchacho = !empty($_POST['id_muchacho']) ? $_POST['id_muchacho'] : null;
        $nombre_otro = !empty($_POST['nombre_otro']) ? $_POST['nombre_otro'] : null;
        $primer_abono = $_POST['primer_abono'] ?? 0;
        $segundo_abono = $_POST['segundo_abono'] ?? 0;

        agregar_asistente_evento($conexion, $id_evento, $id_lider, $id_muchacho, $nombre_otro, $primer_abono, $segundo_abono);
        header("Location: asistentesEventos.php?id=$id_evento");
        exit;
    }

    if ($accion === 'editar_abono') {
        $id_asistente = $_POST['id_asistente'];
        $primer_abono = $_POST['primer_abono'] ?? 0;
        $segundo_abono = $_POST['segundo_abono'] ?? 0;

        editar_abonos_asistente($conexion, $id_asistente, $primer_abono, $segundo_abono);
        header("Location: asistentesEventos.php?id=$id_evento");
        exit;
    }

    if ($accion === 'eliminar') {
        $id_asistente = $_POST['id_asistente'];
        eliminar_asistente_evento($conexion, $id_asistente);
        header("Location: asistentesEventos.php?id=$id_evento");
        exit;
    }
}

$asistentes = $conexion->query("SELECT ae.*, l.NOMBRE_LI, m.NOMBRE_MUC FROM asistentes_evento ae
                                 LEFT JOIN lideres l ON ae.ID_LIDER = l.ID_LIDER
                                 LEFT JOIN muchachos m ON ae.ID_MUCHACHO = m.ID_MUCHACHO
                                 WHERE ae.ID_EVENTO = $id_evento");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistentes a <?= htmlspecialchars($evento['NOMBRE_EVENTO']) ?></title>
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

<div class="container mt-5">
    <h1 class="mb-4">Asistentes al Evento: <?= htmlspecialchars($evento['NOMBRE_EVENTO']) ?></h1>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Asistente</button>

    <div class="table-responsive">
        <table class="table table-dark">
            <thead style="color:rgb(255,255,0)">
                <tr>
                    <th>Líder</th>
                    <th>Muchacho</th>
                    <th>Otro</th>
                    <th>Primer Abono</th>
                    <th>Segundo Abono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $asistentes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['NOMBRE_LI'] ?? '-' ?></td>
                    <td><?= $row['NOMBRE_MUC'] ?? '-' ?></td>
                    <td><?= htmlspecialchars($row['NOMBRE_OTRO'] ?? '-') ?></td>
                    <td><?= $row['PRIMER_ABONO'] ?></td>
                    <td><?= $row['SEGUNDO_ABONO'] ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                                data-id="<?= $row['ID_ASISTENTE_EVENTO'] ?>"
                                data-primer_abono="<?= $row['PRIMER_ABONO'] ?>"
                                data-segundo_abono="<?= $row['SEGUNDO_ABONO'] ?>">
                          Editar
                        </button>

                        <form method="post" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este asistente?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_asistente" value="<?= $row['ID_ASISTENTE_EVENTO'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <button onclick="history.back()" class="btn btn-outline-light mt-4">Volver</button>
</div>

<!-- Modal Agregar Asistente -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content bg-dark text-light">
            <input type="hidden" name="accion" value="agregar">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Asistente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Líder</label>
                    <select name="id_lider" id="id_lider" class="form-select">
                        <option value="">Seleccionar líder</option>
                        <?php $lideres = $conexion->query("SELECT * FROM lideres");
                        while ($l = $lideres->fetch_assoc()): ?>
                            <option value="<?= $l['ID_LIDER'] ?>"><?= htmlspecialchars($l['NOMBRE_LI'] . ' ' . $l['APELLIDO_LI']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Muchacho</label>
                    <select name="id_muchacho" id="id_muchacho" class="form-select">
                        <option value="">Seleccionar muchacho</option>
                        <?php $muchachos = $conexion->query("SELECT * FROM muchachos");
                        while ($m = $muchachos->fetch_assoc()): ?>
                            <option value="<?= $m['ID_MUCHACHO'] ?>"><?= htmlspecialchars($m['NOMBRE_MUC'] . ' ' . $m['APELLIDO_MUC']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Otro Asistente</label>
                    <input type="text" name="nombre_otro" id="nombre_otro" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Primer Abono</label>
                    <input type="number" name="primer_abono" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Segundo Abono</label>
                    <input type="number" name="segundo_abono" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Abonos -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content bg-dark text-light">
      <input type="hidden" name="accion" value="editar_abono">
      <input type="hidden" name="id_asistente" id="edit-id-asistente">
      <div class="modal-header">
        <h5 class="modal-title">Editar Abonos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Primer Abono</label>
          <input type="number" step="0.01" name="primer_abono" id="edit-primer-abono" class="form-control" readonly>
        </div>
        <div class="mb-2">
          <label class="form-label">Segundo Abono</label>
          <input type="number" step="0.01" name="segundo_abono" id="edit-segundo-abono" class="form-control">
        </div>
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
    document.getElementById('edit-id-asistente').value = btn.dataset.id;
    document.getElementById('edit-primer-abono').value = btn.dataset.primer_abono;
    document.getElementById('edit-segundo-abono').value = btn.dataset.segundo_abono;

    if (parseFloat(btn.dataset.segundo_abono) > 0) {
        document.getElementById('edit-segundo-abono').setAttribute('readonly', 'readonly');
    } else {
        document.getElementById('edit-segundo-abono').removeAttribute('readonly');
    }
});

function toggleInputs() {
    const idLider = document.getElementById('id_lider');
    const idMuchacho = document.getElementById('id_muchacho');
    const nombreOtro = document.getElementById('nombre_otro');

    idLider.addEventListener('change', () => {
        if (idLider.value) {
            idMuchacho.disabled = true;
            nombreOtro.disabled = true;
        } else {
            idMuchacho.disabled = false;
            nombreOtro.disabled = false;
        }
    });

    idMuchacho.addEventListener('change', () => {
        if (idMuchacho.value) {
            idLider.disabled = true;
            nombreOtro.disabled = true;
        } else {
            idLider.disabled = false;
            nombreOtro.disabled = false;
        }
    });

    nombreOtro.addEventListener('input', () => {
        if (nombreOtro.value.trim() !== '') {
            idLider.disabled = true;
            idMuchacho.disabled = true;
        } else {
            idLider.disabled = false;
            idMuchacho.disabled = false;
        }
    });
}

window.addEventListener('load', toggleInputs);
</script>

</body>
</html>
