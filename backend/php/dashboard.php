<?php
session_start();
echo "<h1>¡Bienvenido al Dashboard!</h1>";
if (isset($_SESSION['usuario'])) {
    echo "<p>Usuario: " . htmlspecialchars($_SESSION['usuario']) . "</p>";
} else {
    echo "<p>No hay usuario en sesión.</p>";
}
?>