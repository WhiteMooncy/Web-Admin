<?php
require_once '../../../backend/php/conexion/db.php';
session_start();

// --- Procesar registro desde el modal ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['registro'])) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validar duplicados
    $stmt = $conn->prepare("SELECT ID_Usuario FROM usuarios WHERE username=? OR Correo=? OR Telefono=?");
    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $correo, $telefono);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "El usuario, correo o teléfono ya existen.";
        $_SESSION['show_register'] = true;
    } else {
        // Insertar usuario (rol cliente, asume ID_Rol_FK=3)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $rol_cliente = 3;
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, Correo, Telefono, username, password_hash, ID_Rol_FK, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
        if (!$stmt) {
            die("Error en prepare (insert): " . $conn->error);
        }
        $stmt->bind_param("sssssi", $nombre, $correo, $telefono, $username, $hash, $rol_cliente);
        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            $_SESSION['register_error'] = "Error al registrar usuario.";
            $_SESSION['show_register'] = true;
        }
    }
    $stmt->close();
    header("Location: form_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html id="login-page" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
    <title>Iniciar Sesión</title>
    <style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000; 
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6); 
        padding-top: 60px;
        animation: fadeIn 0.3s forwards; 
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; 
        padding: 30px; 
        border: 1px solid #888;
        width: 80%; 
        max-width: 550px; 
        border-radius: 10px; 
        box-shadow: 0 8px 20px rgba(0,0,0,0.4); 
        position: relative;
        animation: slideInTop 0.4s forwards;
    }
    .close-button {
        color: #aaa;
        float: right;
        font-size: 30px; 
        font-weight: bold;
        line-height: 1; 
    }
    .close-button:hover,
    .close-button:focus {
        color: #333; 
        text-decoration: none;
        cursor: pointer;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideInTop { from { transform: translateY(-60px); } to { transform: translateY(0); } }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            <img src="../../src/icons/icon_cafe.png" alt="Logo"> Inicio
        </a>
    </nav>
    <div class="login-container" id="loginContainer">
        <h2>Iniciar Sesión</h2>
        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<p style="color: red; text-align: center;">' . $_SESSION['login_error'] . '</p>';
            unset($_SESSION['login_error']); 
        }
        if (isset($_SESSION['register_success'])) {
            echo '<p style="color: green; text-align: center;">' . $_SESSION['register_success'] . '</p>';
            unset($_SESSION['register_success']);
        }
        ?>
        <form method="post" action="../../../backend/php/conexion/login.php">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" name="username" id="username" required autocomplete="username">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p class="register-link">
            ¿No tienes cuenta? <a href="#" onclick="abrirModalRegistro();return false;">regístrate aquí</a>
        </p>
    </div>

    <!-- Modal de registro oculto -->
    <div id="modalRegistro" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="cerrarModalRegistro()">&times;</span>
            <h2>Registro de Usuario</h2>
            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<p style="color: red; text-align: center;">' . $_SESSION['register_error'] . '</p>';
                unset($_SESSION['register_error']);
            }
            ?>
            <form method="post" action="form_login.php" autocomplete="off">
                <input type="hidden" name="registro" value="1">
                <label for="reg_nombre">Nombre completo:</label>
                <input type="text" name="nombre" id="reg_nombre" required minlength="3" maxlength="80">
                <label for="reg_correo">Correo:</label>
                <input type="email" name="correo" id="reg_correo" required maxlength="100">
                <label for="reg_telefono">Teléfono:</label>
                <input type="text" name="telefono" id="reg_telefono" required minlength="7" maxlength="15" pattern="^[0-9]+$" title="Solo números">
                <label for="reg_username">Nombre de Usuario:</label>
                <input type="text" name="username" id="reg_username" required minlength="4" maxlength="30">
                <label for="reg_password">Contraseña:</label>
                <input type="password" name="password" id="reg_password" required minlength="6">
                <button type="submit">Registrarse</button>
            </form>
        </div>
    </div>

    <script>
    function abrirModalRegistro() {
        document.getElementById('modalRegistro').style.display = "block";
        document.getElementById('loginContainer').style.display = "none";
    }
    function cerrarModalRegistro() {
        document.getElementById('modalRegistro').style.display = "none";
        document.getElementById('loginContainer').style.display = "block";
    }
    window.onclick = function(event) {
        var modal = document.getElementById('modalRegistro');
        if (event.target == modal) {
            cerrarModalRegistro();
        }
    }
    // Mostrar el modal si hubo error de registro
    <?php if (isset($_SESSION['show_register'])): ?>
        abrirModalRegistro();
        <?php unset($_SESSION['show_register']); ?>
    <?php endif; ?>
    </script>
</body>
</html>