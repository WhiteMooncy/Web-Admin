<?php
session_start();
require_once '../conexion/db.php';

// Validar que el usuario esté autenticado
if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: ../conexion/login.php');
    exit;
}

// Recibe los datos del formulario
$nombre    = $_POST['nombre'] ?? '';
$apellido  = $_POST['apellido'] ?? '';
$username  = $_POST['username'] ?? '';
$telefono  = $_POST['telefono'] ?? '';
$correo    = $_POST['correo'] ?? '';
$usuario_id = $_SESSION['ID_Usuario'];

// Actualiza en la base de datos
$sql = "UPDATE usuarios SET 
            nombre = ?, 
            apellido = ?, 
            username = ?, 
            Telefono = ?, 
            Correo = ?
        WHERE ID_Usuario = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en prepare: " . $conn->error);
}
$stmt->bind_param("sssssi", $nombre, $apellido, $username, $telefono, $correo, $usuario_id);

if ($stmt->execute()) {
    // Actualiza los datos en la sesión
    $_SESSION['nombre'] = $nombre;
    $_SESSION['apellido'] = $apellido;
    $_SESSION['username'] = $username;
    $_SESSION['telefono'] = $telefono;
    $_SESSION['correo'] = $correo;

    header('Location: ../../../frontend/templates/loged/profile.php?success=1');
    exit;
} else {
    header('Location: ../../../frontend/templates/loged/profile.php?error=1');
    exit;
}
?>