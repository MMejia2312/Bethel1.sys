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
