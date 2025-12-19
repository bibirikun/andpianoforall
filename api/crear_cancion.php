<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/../app/includes/db.php';

header("Content-Type: application/json");

// 🔐 Comprobar login
if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// 📥 Leer JSON recibido
$input = json_decode(file_get_contents("php://input"), true);

if (
    !$input ||
    !isset($input["titulo"]) ||
    !isset($input["idSecuencia"])
) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$titulo = trim($input["titulo"]);
$idSecuencia = intval($input["idSecuencia"]);

$idUsuario = intval($_SESSION["usuario"]["ID_Usuario"]);

// 🧠 Comprobar si el usuario ya tiene una canción con ese título
$stmtCheck = $conn->prepare("
    SELECT 1 
    FROM Canciones 
    WHERE Titulo = ? AND ID_Usuario = ?
");
$stmtCheck->bind_param("si", $titulo, $idUsuario);
$stmtCheck->execute();
$res = $stmtCheck->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["error" => "Ya tienes una canción con ese título"]);
    exit;
}
$stmtCheck->close();

// 💾 Insertar canción
$stmt = $conn->prepare("
    INSERT INTO Canciones (Titulo, ID_Secuencia, ID_Usuario)
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "sii",
    $titulo,
    $idSecuencia,
    $idUsuario
);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
