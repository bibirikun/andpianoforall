<?php
session_start();
?>

<div style="margin-top:20px;">
<?php if (isset($_SESSION['usuario'])): ?>

    <!-- Grabacion -->
    <button id="btn-grabar">🎙️ Grabar</button>
    <button id="btn-parar" disabled>⏹️ Parar</button>

<?php else: ?>
    <p style="color:red;">⚠️ Debes iniciar sesión para poder grabar tus notas.</p>
<?php endif; ?>
</div>

<!-- Piano -->
<div class="piano">
    <div class="white-keys">
        <div class="white" data-note="c3"><span class="key-label">Z</span></div>
        <div class="white" data-note="d3"><span class="key-label">X</span></div>
        <div class="white" data-note="e3"><span class="key-label">C</span></div>
        <div class="white" data-note="f3"><span class="key-label">V</span></div>
        <div class="white" data-note="g3"><span class="key-label">B</span></div>
        <div class="white" data-note="a3"><span class="key-label">N</span></div>
        <div class="white" data-note="b3"><span class="key-label">M</span></div>

        <div class="white" data-note="c4"><span class="key-label">Q</span></div>
        <div class="white" data-note="d4"><span class="key-label">W</span></div>
        <div class="white" data-note="e4"><span class="key-label">E</span></div>
        <div class="white" data-note="f4"><span class="key-label">R</span></div>
        <div class="white" data-note="g4"><span class="key-label">T</span></div>
        <div class="white" data-note="a4"><span class="key-label">Y</span></div>
        <div class="white" data-note="b4"><span class="key-label">U</span></div>
    </div>
    <div class="black-keys">
        <div class="black" data-note="c-3"><span class="key-label black-label">S</span></div>
        <div class="black" data-note="d-3"><span class="key-label black-label">D</span></div>
        <div class="black" data-note="f-3"><span class="key-label black-label">G</span></div>
        <div class="black" data-note="g-3"><span class="key-label black-label">H</span></div>
        <div class="black" data-note="a-3"><span class="key-label black-label">J</span></div>

        <div class="black" data-note="c-4"><span class="key-label black-label">2</span></div>
        <div class="black" data-note="d-4"><span class="key-label black-label">3</span></div>
        <div class="black" data-note="f-4"><span class="key-label black-label">5</span></div>
        <div class="black" data-note="g-4"><span class="key-label black-label">6</span></div>
        <div class="black" data-note="a-4"><span class="key-label black-label">7</span></div>
    </div>
</div>

<?php
$logueado = isset($_SESSION["usuario"]);
?>