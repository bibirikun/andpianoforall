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

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario    = trim($_POST['usuario']);
    $correo     = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($usuario) || empty($contrasena)) {
        $error = "⚠️ Todos los campos obligatorios deben completarse.";
    }
    elseif (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ El correo electrónico no es válido.";
    } 
    
    else {

        // 1️⃣ Comprobar si el usuario ya existe
        $stmt = $conn->prepare("SELECT ID_Usuario FROM Usuarios WHERE Usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "❌ Este nombre de usuario ya está en uso.";
        } else {

            // 2️⃣ Crear usuario
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmtInsert = $conn->prepare("
                INSERT INTO Usuarios (Usuario, Correo, Contrasena)
                VALUES (?, ?, ?)
            ");
            $stmtInsert->bind_param("sss", $usuario, $correo, $hash);

            if ($stmtInsert->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "❌ Error al crear el usuario.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="/public/assets/css/estilos.css">
</head>
<body>

<div class="registro-container">
    <h2>Registro de usuario</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="registro.php" method="POST">
        <label for="usuario">Nombre de usuario:</label>
        <input type="text" name="usuario" id="usuario" required>

        <label for="correo">Correo electrónico (opcional):</label>
        <input type="email" name="correo" id="correo">

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <button type="submit">Registrarse</button>
    </form>
</div>

</body>
</html>
