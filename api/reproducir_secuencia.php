<?php
require_once __DIR__ . '/../app/includes/db.php';

header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode([]);
    exit;
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("
    SELECT ID_Nota, TimestampMs, DuracionMs
    FROM Secuencia_Notas
    WHERE ID_Secuencia = ?
    ORDER BY TimestampMs ASC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

$notas = [];

while ($row = $res->fetch_assoc()) {
    $notas[] = [
        "nota"       => intval($row["ID_Nota"]),
        "timestamp"  => intval($row["TimestampMs"]),
        "duracionMs" => intval($row["DuracionMs"])
    ];
}

echo json_encode($notas);
?>