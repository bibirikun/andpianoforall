<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',      // importante
    'secure' => true,    // HTTPS
    'httponly' => true,
    'samesite' => 'Lax', // CLAVE
]);
session_start();
require_once __DIR__ . '/../app/includes/db.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

$idSecuencia = intval($data["idSecuencia"]);
$noteName    = $data["nota"];         // "c3", "g-3", etc.
$posicion    = intval($data["posicion"]);
$timestamp   = intval($data["timestamp"]);
$duracion    = intval($data["duracionMs"]);

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

/* Buscar ID_Nota en la tabla Notas */
$stmtFind = $conn->prepare("SELECT ID_Nota FROM Notas WHERE Nombre = ?");
$stmtFind->bind_param("s", $noteName);
$stmtFind->execute();
$res = $stmtFind->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["error" => "Nota no existe: $noteName"]);
    exit;
}

$row = $res->fetch_assoc();
$idNota = intval($row["ID_Nota"]);
$stmtFind->close();

/* 2) Insertar nota completa (con duración REAL */
$stmt = $conn->prepare("
    INSERT INTO Secuencia_Notas 
        (ID_Secuencia, ID_Nota, Posicion, TimestampMs, DuracionMs)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("iiiii", 
    $idSecuencia, 
    $idNota, 
    $posicion, 
    $timestamp, 
    $duracion
);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
