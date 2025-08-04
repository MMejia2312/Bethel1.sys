<?php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) {
    $_SESSION['error'] = "Debes iniciar sesión primero.";
    header("Location: ../../../index.php");  
    exit();
}
include '../../../includes/coneccion.php';

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

// Obtener eventos
$consulta_eventos = $conexion->query("SELECT * FROM eventos");

// Función para obtener resumen de asistentes
function obtener_resumen_evento($conexion, $id_evento) {
    $stmt = $conexion->prepare("SELECT 
        COUNT(*) AS total_asistentes, 
        SUM(PRIMER_ABONO + SEGUNDO_ABONO) AS total_abonos
        FROM asistentes_evento 
        WHERE ID_EVENTO = ?");
    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $stmt->bind_result($total_asistentes, $total_abonos);
    $stmt->fetch();
    $stmt->close();
    return [
        'total_asistentes' => $total_asistentes ?? 0,
        'total_abonos' => $total_abonos ?? 0
    ];
}

// Procesar evento como completado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completar_evento'])) {
    $id_evento = $_POST['id_evento'];
    $total_asistentes = $_POST['total_asistentes'];
    $total_abonos = $_POST['total_abonos'];

    // Actualizar estado del evento
    $stmt = $conexion->prepare("UPDATE eventos SET ESTADO = 'Completado' WHERE ID_EVENTO = ?");
    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $stmt->close();

    header("Location: resumenEventos.php?ok=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Eventos</title>
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

<div class="container my-5">
    <h1 class="mb-4 text-center"><span style="color:rgba(255,255,0,.9)">Resumen de Eventos</span></h1>

    <div class="table-responsive">
        <table class="table table-dark text-center">
            <thead style="color:rgb(255,255,0)">
                <tr>
                    <th>Evento</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total Asistentes</th>
                    <th>Total Abonos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($evento = $consulta_eventos->fetch_assoc()): ?>
                    <?php $resumen = obtener_resumen_evento($conexion, $evento['ID_EVENTO']); ?>
                    <tr>
                        <td><?= htmlspecialchars($evento['NOMBRE_EVENTO']) ?></td>
                        <td><?= htmlspecialchars($evento['FECHA'] ?? '') ?></td>
                        <td><?= htmlspecialchars($evento['ESTADO']) ?></td>
                        <td><?= $resumen['total_asistentes'] ?></td>
                        <td>$<?= number_format($resumen['total_abonos'], 2) ?></td>
                        <td>
                            <?php if ($evento['ESTADO'] !== 'Completado'): ?>
                                <button class="btn btn-success btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalCompletar"
                                        data-id="<?= $evento['ID_EVENTO'] ?>"
                                        data-asistentes="<?= $resumen['total_asistentes'] ?>"
                                        data-abonos="<?= $resumen['total_abonos'] ?>">
                                    Completar
                                </button>
                            <?php else: ?>
                                <span class="text-success">Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Completar Evento -->
<div class="modal fade" id="modalCompletar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content bg-dark text-light">
      <div class="modal-header">
        <h5 class="modal-title">Completar Evento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_evento" id="modal-id-evento">
        <input type="hidden" name="total_asistentes" id="modal-total-asistentes">
        <input type="hidden" name="total_abonos" id="modal-total-abonos">
        <p>¿Deseas marcar como completado este evento?</p>
        <p><strong>Total Asistentes:</strong> <span id="view-asistentes"></span></p>
        <p><strong>Total Abonos:</strong> $<span id="view-abonos"></span></p>
      </div>
      <div class="modal-footer">
        <button type="submit" name="completar_evento" class="btn btn-success">Completar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('modalCompletar').addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    document.getElementById('modal-id-evento').value = btn.dataset.id;
    document.getElementById('modal-total-asistentes').value = btn.dataset.asistentes;
    document.getElementById('modal-total-abonos').value = btn.dataset.abonos;
    document.getElementById('view-asistentes').innerText = btn.dataset.asistentes;
    document.getElementById('view-abonos').innerText = parseFloat(btn.dataset.abonos).toFixed(2);
});
</script>

</body>
</html>
