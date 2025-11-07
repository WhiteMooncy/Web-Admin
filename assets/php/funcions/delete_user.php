<?php
require_once '../conexion/db.php'; // Ajusta esta ruta si es necesario
session_start();

// Asegúrate de que la conexión a la base de datos esté disponible
if (!isset($conn) || !$conn instanceof mysqli) {
    header("Location: ../../../frontend/templates/loged/manage_users.php?status=error&message=" . urlencode("Error de conexión a la base de datos."));
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_usuario = (int)$_GET['id'];

    // ¡CAMBIO AQUÍ! Ahora es un UPDATE para inactivar al usuario
    $sql = "UPDATE usuarios SET activo = 0 WHERE ID_Usuario = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_usuario); // 'i' para entero

        if ($stmt->execute()) {
            // Inactivación exitosa
            $message = "Usuario inactivado correctamente.";
            $status = "success";
        } else {
            // Error al ejecutar la inactivación
            $message = "Error al inactivar el usuario: " . $stmt->error;
            $status = "error";
        }
        $stmt->close();
    } else {
        // Error al preparar la consulta
        $message = "Error al preparar la inactivación: " . $conn->error;
        $status = "error";
    }
} else {
    // No se proporcionó un ID de usuario
    $message = "ID de usuario no proporcionado.";
    $status = "error";
}

$conn->close(); // Cerrar la conexión

// Redirige de vuelta a manage_users.php con el mensaje de estado
header("Location: ../../../frontend/templates/loged/manage_users.php?status=" . $status . "&message=" . urlencode($message));
exit();
?>