<?php
require_once '../../../backend/php/conexion/db.php';
session_start(); 
?>
<!DOCTYPE html>
<html id="login-page" lang="es" >
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
        <title>Iniciar Sesión</title>
    </head>
    <body>
        <nav class="navbar">
            <a href="index.php" class="navbar-brand">
                <img src="../../src/icons/icon_cafe.png" alt="Logo"> Inicio
            </a>
        </nav>
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<p style="color: red; text-align: center;">' . $_SESSION['login_error'] . '</p>';
                unset($_SESSION['login_error']); 
            }
            ?>
            <form method="post" action="../../../backend/php/conexion/login.php">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" name="username" id="username" required autocomplete="username">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Iniciar Sesión</button>
                <p class="register-link">¿No tienes cuenta? requistrate Aqui</p>
            </form>
        </div>
    </body>
</html>