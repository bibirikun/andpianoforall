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

// 🔐 Comprobar login
if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// 📥 Leer JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["idCancion"])) {
    echo json_encode(["error" => "ID de canción no recibido"]);
    exit;
}

$idCancion = intval($data["idCancion"]);
$idUsuario = $_SESSION["usuario"]["ID_Usuario"];

// 🧠 Borrar SOLO si es del usuario
$stmt = $conn->prepare("
    DELETE FROM Canciones
    WHERE ID_Cancion = ? AND ID_Usuario = ?
");

$stmt->bind_param("ii", $idCancion, $idUsuario);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
