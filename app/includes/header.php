<div class="sidebar">
    <h2>Menú</h2>

    <!-- 👤 Bloque de usuario -->
    <div class="user-box">
        <?php if (isset($_SESSION["usuario"])): ?>

            <p class="username">
                👤 <?= htmlspecialchars($_SESSION["usuario"]["Nombre"]) ?>
            </p>

            <a href="/public/logout.php" class="btn-logout">Cerrar sesión</a>

        <?php else: ?>

            <a href="/public/login.php">Iniciar sesión</a>
            <a href="/public/registro.php">Registrarse</a>

        <?php endif; ?>
    </div>

    <ul>
        <li><a href="../../public/index.php">Inicio</a></li>
        <li><a href="#">Teoría musical</a></li>
        <li><a href="#">Ajustes</a></li>
    </ul>
</div>
