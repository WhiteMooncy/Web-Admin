<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php'; // Usa __DIR__ para una ruta relativa más robusta

if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: ../../../frontend/templates/form_login.php');
    exit();
}

$user_id = $_SESSION['ID_Usuario'];

// Consulta para obtener el nombre de usuario y el rol
$stmt = $conn->prepare(
    "SELECT u.username, r.Nombre_Rol 
     FROM usuarios u 
     JOIN roles r ON u.ID_Rol_FK = r.ID_Rol 
     WHERE u.ID_Usuario = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $user_role_name = strtolower($row['Nombre_Rol']);
    // Normaliza 'trabajador' a 'empleado'
    if ($user_role_name === 'trabajador') {
        $user_role_name = 'empleado';
    }
    $_SESSION['username'] = $username;
    $_SESSION['user_role_name'] = $user_role_name;
} else {
    // Si no se encuentra el usuario, forzar logout
    session_destroy();
    header('Location: ../../../frontend/templates/form_login.php');
    exit();
}

$stmt->close();
// $username y $user_role_name quedan disponibles para el resto del sistema
?>