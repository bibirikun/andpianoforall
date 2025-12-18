<?php
session_set_cookie_params([
    'path' => '/',
]);
session_start();
require_once __DIR__ . '/../app/includes/db.php';

header("Content-Type: application/json");

// Comprobar login
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$usuario = $_SESSION['usuario']['Nombre']; // ahora sí existe

$stmt = $conn->prepare("INSERT INTO Secuencias (Usuario) VALUES (?)");
$stmt->bind_param("s", $usuario);

if (!$stmt->execute()) {
    echo json_encode(["error" => $stmt->error]);
    exit;
}

echo json_encode(["idSecuencia" => $conn->insert_id]);
exit;
?>