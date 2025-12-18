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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Piano online</title>
    <link rel="stylesheet" href="/public/assets/css/estilos.css">
</head>
<body>

    <div class="main-container">
        <?php include_once __DIR__ . '/../app/includes/header.php'; ?>
        <?php include_once __DIR__ . '/../app/templates/piano.php'; ?>

        <?php
        if (isset($_SESSION["usuario"])) {

            $idUsuario = $_SESSION["usuario"]["ID_Usuario"];

            $stmt = $conn->prepare("
                SELECT ID_Cancion, Titulo, ID_Secuencia
                FROM Canciones
                WHERE ID_Usuario = ?
                ORDER BY ID_Cancion DESC
                LIMIT 5
            ");

            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        ?>
        
        <div class="song-list">
            <h2>🎵 Tus últimas canciones</h2>

            <?php if ($result->num_rows === 0): ?>
                <p>No has grabado ninguna canción todavía.</p>
            <?php else: ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                
                <li class="song-item">
                    <span class="song-title"><?= htmlspecialchars($row["Titulo"]) ?></span>

                    <div class="song-actions">
                        <button onclick="reproducirSecuencia(<?= $row['ID_Secuencia'] ?>)">
                            ▶ Reproducir
                        </button>

                        <button class="btn-borrar"
                            onclick="borrarCancion(<?= $row['ID_Cancion'] ?>, this)">
                            🗑️ Borrar
                        </button>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php endif; ?>
        </div>

        <?php
        } // FIN DEL IF
        ?>

        <?php include_once __DIR__ . '/../app/includes/footer.php'; ?>
    </div>

    <script>
        const usuarioLogueado = <?= isset($_SESSION["usuario"]) ? "true" : "false" ?>;
    </script>

    <script src="/public/assets/js/piano.js?v=<?= time() ?>"></script>
</body>
</html>