<?php
session_start();
require_once 'db.php';

$usuario = $_POST['username'];
$clave = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "SELECT * FROM usuarios WHERE usuario = ? AND clave = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $clave);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $_SESSION['username'] = $usuario;
    echo "Inicio de sesión correcto. Bienvenido " . htmlspecialchars($usuario);
    header("Location: dashboard.php");
    exit;
} else {
    echo "Usuario o contraseña incorrectos.";
}

$conn->close();
?>