<?php
/* funcions.php
 * Conexión + funciones reutilizables
 */

# ---------- CONEXIÓN ----------
$pdo = new PDO(
    "mysql:host=localhost;dbname=bethel1_sys;charset=utf8mb4",
    "root",
    "",
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
);

# ---------- FUNCIONES ----------
/**
 * Inserta un nuevo registro en la tabla muchachos.
 *
 * @param string $nombre        NOMBRE_MUC
 * @param string $apellido      APELLIDO_MUC
 * @param string $departamento  DEPARTAMENTO
 * @param string $fecha_na      FECHA_NA (formato YYYY‑MM‑DD)
 * @return bool                 true si la inserción fue exitosa
 */
/** Muchachos */
 function agregar_muchacho($nombre, $apellido, $departamento, $fecha_na) {
    global $pdo;
    $sql = "INSERT INTO muchachos (NOMBRE_MUC, APELLIDO_MUC, DEPARTAMENTO, FECHA_NA)
            VALUES (:n, :a, :d, :f)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':n' => $nombre,
        ':a' => $apellido,
        ':d' => $departamento,
        ':f' => $fecha_na
    ]);
}

function obtener_todos_los_muchachos(): array {
    global $pdo;
    $sql = "SELECT * FROM muchachos";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function editar_muchacho($id, $nombre, $apellido, $departamento, $fecha_na): bool {
    global $pdo;
    $sql = "UPDATE muchachos SET 
              NOMBRE_MUC = :nombre, 
              APELLIDO_MUC = :apellido, 
              DEPARTAMENTO = :departamento, 
              FECHA_NA = :fecha_na 
            WHERE ID_MUCHACHO = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':departamento' => $departamento,
        ':fecha_na' => $fecha_na,
        ':id' => $id,
    ]);
}

function eliminar_muchacho($id): bool {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM muchachos WHERE ID_MUCHACHO = ?");
    return $stmt->execute([$id]);
}

/** Alergias */
function agregar_dato_medico($conexion, $id_muchacho, $alergias, $medicamentos) {
    $stmt = $conexion->prepare("INSERT INTO datos_med (ID_MUCHACHO, ALERGIAS, MEDICAMENTOS) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $id_muchacho, $alergias, $medicamentos);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error en la preparación: " . $conexion->error);
    }
}

function editar_dato_medico($conexion, $id_med, $alergias, $medicamentos) {
    $stmt = $conexion->prepare("UPDATE datos_med SET ALERGIAS = ?, MEDICAMENTOS = ? WHERE ID_DATOS = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $alergias, $medicamentos, $id_med);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error en la preparación: " . $conexion->error);
    }
}

function eliminar_dato_medico($conexion, $id_med) {
    $stmt = $conexion->prepare("DELETE FROM datos_med WHERE ID_DATOS = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_med);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error en la preparación: " . $conexion->error);
    }
}

/** Premios */
function agregar_premio($conexion, $id_muchacho, $id_premio, $estado, $comentario) {
    $stmt = $conexion->prepare("INSERT INTO premios_ganados (ID_MUCHACHO, ID_PREMIO, ESTADO, COMENTARIO)
                                 VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_muchacho, $id_premio, $estado, $comentario);
    $stmt->execute();
    $stmt->close();
}

function editar_premio($conexion, $id_reg, $estado, $comentario) {
    $stmt = $conexion->prepare("UPDATE premios_ganados SET ESTADO = ?, COMENTARIO = ? WHERE ID_REG = ?");
    $stmt->bind_param("isi", $estado, $comentario, $id_reg);
    $stmt->execute();
    $stmt->close();
}

function eliminar_premio($conexion, $id_reg) {
    $stmt = $conexion->prepare("DELETE FROM premios_ganados WHERE ID_REG = ?");
    $stmt->bind_param("i", $id_reg);
    $stmt->execute();
    $stmt->close();
}

/** Lideres */
function agregar_lider($conexion, $nombre, $apellido, $departamento, $nivel) {
    $stmt = $conexion->prepare("INSERT INTO lideres (NOMBRE_LI, APELLIDO_LI, ID_DEPARTAMENTO, NIVEL) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param("ssii", $nombre, $apellido, $departamento, $nivel);
    $stmt->execute();
    $stmt->close();
}


function editar_lider($conexion, $id, $nombre, $apellido, $departamento, $nivel) {
    $stmt = $conexion->prepare("UPDATE lideres SET NOMBRE_LI=?, APELLIDO_LI=?, ID_DEPARTAMENTO=?, NIVEL=? WHERE ID_LIDER=?");
    $stmt->bind_param("ssiii", $nombre, $apellido, $departamento, $nivel, $id);
    $stmt->execute();
    $stmt->close();
}

function eliminar_lider($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM lideres WHERE ID_LIDER=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

/** Niveles Usuarios */
function agregar_nivelUs($conexion, $cargo) {
    $stmt = $conexion->prepare("INSERT INTO nivel (DESCRIP) VALUES (?)");
    
    if (!$stmt) {
        die("Error en prepare (agregar_nivelUs): " . $conexion->error);
    }

    $stmt->bind_param("s", $cargo);
    $stmt->execute();
    $stmt->close();
}


function editar_nivelUs($conexion, $id, $cargo) {
    $stmt = $conexion->prepare("UPDATE nivel SET DESCRIP = ? WHERE ID_NIVEL = ?");
    
    if (!$stmt) {
        die("Error en prepare (editar_nivelUs): " . $conexion->error);
    }

    $stmt->bind_param("si", $cargo, $id);
    $stmt->execute();
    $stmt->close();
}


function eliminar_nivelUs($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM nivel WHERE ID_NIVEL = ?");
    
    if (!$stmt) {
        die("Error en prepare (eliminar_nivelUs): " . $conexion->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

/** Usuarios */
function agregar_usuario($conexion, $usuario, $contrasena, $id_lider, $id_nivel, $estado) {
    $stmt = $conexion->prepare("INSERT INTO usuarios (USUARIO, CONTRASENA, ID_LIDER, ID_NIVEL, ESTADO)
                                 VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en prepare (agregar_usuario): " . $conexion->error);
    }
    $stmt->bind_param("ssiii", $usuario, $contrasena, $id_lider, $id_nivel, $estado);
    $stmt->execute();
    $stmt->close();
}

function editar_usuario($conexion, $id, $usuario, $contrasena, $id_lider, $id_nivel, $estado) {
    if (!empty($contrasena)) {
        $stmt = $conexion->prepare("UPDATE usuarios SET USUARIO = ?, CONTRASENA = ?, ID_LIDER = ?, ID_NIVEL = ?, ESTADO = ? WHERE ID_USUARIO = ?");
        $stmt->bind_param("ssiiii", $usuario, $contrasena, $id_lider, $id_nivel, $estado, $id);
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET USUARIO = ?, ID_LIDER = ?, ID_NIVEL = ?, ESTADO = ? WHERE ID_USUARIO = ?");
        $stmt->bind_param("siiii", $usuario, $id_lider, $id_nivel, $estado, $id);
    }

    if (!$stmt) {
        die("Error en prepare (editar_usuario): " . $conexion->error);
    }

    $stmt->execute();
    $stmt->close();
}

function eliminar_usuario($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE ID_USUARIO = ?");
    if (!$stmt) {
        die("Error en prepare (eliminar_usuario): " . $conexion->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function toggle_estado_usuario($conexion, $id, $nuevo_estado) {
    $stmt = $conexion->prepare("UPDATE usuarios SET ESTADO = ? WHERE ID_USUARIO = ?");
    if (!$stmt) {
        die("Error en prepare (toggle_estado_usuario): " . $conexion->error);
    }
    $stmt->bind_param("ii", $nuevo_estado, $id);
    $stmt->execute();
    $stmt->close();
}

/** Inventario/insumos */
// AGREGAR INSUMO
function agregar_insumo($nombre, $descripcion, $entrada, $mantenimiento, $estado, $conexion) {
    $stmt = $conexion->prepare("INSERT INTO inventario (NOMBRE, DESCRIPCION, ENTRADA, MANTENIMIENTO, ESTADO) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $nombre, $descripcion, $entrada, $mantenimiento, $estado);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error al preparar INSERT: " . $conexion->error);
    }
}

// EDITAR INSUMO
function editar_insumo($id, $nombre, $descripcion, $entrada, $mantenimiento, $estado, $conexion) {
    $stmt = $conexion->prepare("UPDATE inventario SET NOMBRE=?, DESCRIPCION=?, ENTRADA=?, MANTENIMIENTO=?, ESTADO=? WHERE ID_INSUMO=?");
    if ($stmt) {
        $stmt->bind_param("sssssi", $nombre, $descripcion, $entrada, $mantenimiento, $estado, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error al preparar UPDATE: " . $conexion->error);
    }
}

// ELIMINAR INSUMO
function eliminar_insumo($id, $conexion) {
    $stmt = $conexion->prepare("DELETE FROM inventario WHERE ID_INSUMO=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error al preparar DELETE: " . $conexion->error);
    }
}

/** Salida de insumos */
//AGREGAR
function agregar_salida_insumo($conexion, $id_insumo, $fecha_salida, $fecha_vuelta, $encargado, $estado) {
    $stmt = $conexion->prepare("INSERT INTO salida_insumos (ID_INSUMO, FECHA_SALIDA, FECHA_VUELTA, ENCARGADO, ESTADO) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_insumo, $fecha_salida, $fecha_vuelta, $encargado, $estado);
    $stmt->execute();
    $stmt->close();
}

//EDITAR
function editar_salida_insumo($conexion, $id_ins_sal, $fecha_salida, $fecha_vuelta, $encargado, $estado) {
    $stmt = $conexion->prepare("UPDATE salida_insumos SET FECHA_SALIDA = ?, FECHA_VUELTA = ?, ENCARGADO = ?, ESTADO = ? WHERE ID_INS_SAL = ?");
    $stmt->bind_param("ssssi", $fecha_salida, $fecha_vuelta, $encargado, $estado, $id_ins_sal);
    $stmt->execute();
    $stmt->close();
}

//ELIMINAR
function eliminar_salida_insumo($conexion, $id_ins_sal) {
    $stmt = $conexion->prepare("DELETE FROM salida_insumos WHERE ID_INS_SAL = ?");
    $stmt->bind_param("i", $id_ins_sal);
    $stmt->execute();
    $stmt->close();
}

// AGREGAR PREMIO
function agregar_premio_tabla($conexion, $nombre_pre, $id_departamento, $senda, $requisitos) {
    $stmt = $conexion->prepare("INSERT INTO premios (NOMBRE_PRE, ID_DEPARTAMENTO, SENDA, REQUISITOS) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }
    $stmt->bind_param("siss", $nombre_pre, $id_departamento, $senda, $requisitos);
    $stmt->execute();
    $stmt->close();
}

// EDITAR PREMIO
function editar_premio_tabla($conexion, $id_premio, $nombre_pre, $id_departamento, $senda, $requisitos) {
    $stmt = $conexion->prepare("UPDATE premios SET NOMBRE_PRE = ?, ID_DEPARTAMENTO = ?, SENDA = ?, REQUISITOS = ? WHERE ID_PREMIO = ?");
    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }
    $stmt->bind_param("sissi", $nombre_pre, $id_departamento, $senda, $requisitos, $id_premio);
    $stmt->execute();
    $stmt->close();
}

// ELIMINAR PREMIO
function eliminar_premio_tabla($conexion, $id_premio) {
    $stmt = $conexion->prepare("DELETE FROM premios WHERE ID_PREMIO = ?");
    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }
    $stmt->bind_param("i", $id_premio);
    $stmt->execute();
    $stmt->close();
}

//EVENTOS
function agregar_evento($conexion, $nombre, $fecha, $lugar, $estado) {
    $stmt = $conexion->prepare("INSERT INTO eventos (NOMBRE_EVENTO, FECHA, LUGAR, ESTADO) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $fecha, $lugar, $estado);
    $stmt->execute();
    $stmt->close();
}

function editar_evento($conexion, $id, $nombre, $fecha, $lugar, $estado) {
    $stmt = $conexion->prepare("UPDATE eventos SET NOMBRE_EVENTO = ?, FECHA = ?, LUGAR = ?, ESTADO = ? WHERE ID_EVENTO = ?");
    $stmt->bind_param("ssssi", $nombre, $fecha, $lugar, $estado, $id);
    $stmt->execute();
    $stmt->close();
}

function eliminar_evento($conexion, $id) {
    $stmt = $conexion->prepare("DELETE FROM eventos WHERE ID_EVENTO = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

/** ASISTENTES A EVENTOS **/

// AGREGAR ASISTENTE
function agregar_asistente_evento($conexion, $id_evento, $id_lider, $id_muchacho, $nombre_otro, $primer_abono, $segundo_abono) {
    $stmt = $conexion->prepare("INSERT INTO asistentes_evento 
        (ID_EVENTO, ID_LIDER, ID_MUCHACHO, NOMBRE_OTRO, PRIMER_ABONO, SEGUNDO_ABONO) 
        VALUES (?, ?, ?, ?, ?, ?)");

    // Convertir valores vacíos a NULL
    $id_lider = !empty($id_lider) ? $id_lider : null;
    $id_muchacho = !empty($id_muchacho) ? $id_muchacho : null;
    $nombre_otro = !empty($nombre_otro) ? $nombre_otro : null;

    // Como bind_param no acepta NULL directamente, usamos 's' para el string y 'i' para INT
    $stmt->bind_param("iiisdd", 
        $id_evento, 
        $id_lider, 
        $id_muchacho, 
        $nombre_otro, 
        $primer_abono, 
        $segundo_abono
    );
    $stmt->execute();
    $stmt->close();
}

// EDITAR SOLO ABONOS CON RESTRICCIONES
function editar_abonos_asistente($conexion, $id_asistente, $primer_abono, $segundo_abono) {
    // Obtener valores actuales
    $stmt = $conexion->prepare("SELECT PRIMER_ABONO, SEGUNDO_ABONO FROM asistentes_evento WHERE ID_ASISTENTE_EVENTO = ?");
    $stmt->bind_param("i", $id_asistente);
    $stmt->execute();
    $stmt->bind_result($abono1_actual, $abono2_actual);
    $stmt->fetch();
    $stmt->close();

    // Si el PRIMER_ABONO ya tiene un valor mayor a 0, mantenerlo intacto
    if ($abono1_actual > 0) {
        $primer_abono = $abono1_actual;
    }

    // Si el SEGUNDO_ABONO ya tiene un valor mayor a 0, mantenerlo intacto
    if ($abono2_actual > 0) {
        $segundo_abono = $abono2_actual;
    }

    // Actualizar SOLO los abonos permitidos
    $stmt = $conexion->prepare("UPDATE asistentes_evento SET PRIMER_ABONO = ?, SEGUNDO_ABONO = ? WHERE ID_ASISTENTE_EVENTO = ?");
    $stmt->bind_param("ddi", $primer_abono, $segundo_abono, $id_asistente);
    $stmt->execute();
    $stmt->close();
}

// ELIMINAR ASISTENTE
function eliminar_asistente_evento($conexion, $id_asistente) {
    $stmt = $conexion->prepare("DELETE FROM asistentes_evento WHERE ID_ASISTENTE_EVENTO = ?");
    $stmt->bind_param("i", $id_asistente);
    $stmt->execute();
    $stmt->close();
}
