<?php
// Datos de conexión
$host = "localhost";        // Servidor (localhost si usas XAMPP)
$usuario = "root";          // Usuario por defecto en XAMPP
$contrasena = "";           // Contraseña (vacía por defecto en XAMPP) LabAiep25
$basededatos = "web-admin"; // Cambia esto por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Si todo está bien
//echo "°";
?>