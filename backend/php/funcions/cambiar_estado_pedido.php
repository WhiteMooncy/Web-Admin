<?php
session_start();
include __DIR__ . '/../conexion/db.php';

// Usa el nombre de rol correcto según tu sistema
$rol = $_SESSION['user_role_name'] ?? null;

if ($rol === 'admin' || $rol === 'empleado') {
    // Usa los nombres de campo correctos del formulario
    if (isset($_POST['ID_Pedido'], $_POST['estado'])) {
        $pedido_id = $_POST['ID_Pedido'];
        $nuevo_estado = $_POST['estado'];
        $estados_permitidos = ['cancelado', 'en preparacion', 'pendiente', 'listo', 'entregado'];

        if (in_array($nuevo_estado, $estados_permitidos)) {
            $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE ID_Pedido = ?");
            $stmt->bind_param("si", $nuevo_estado, $pedido_id);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: ../../../frontend/templates/admin/orders.php?msg=actualizado");
                exit;
            } else {
                $stmt->close();
                header("Location: ../../../frontend/templates/admin/orders.php?msg=error");
                exit;
            }
        } else {
            header("Location: ../../../frontend/templates/admin/orders.php?msg=estado_no_permitido");
            exit;
        }
    } else {
        header("Location: ../../../frontend/templates/admin/orders.php?msg=datos_incompletos");
        exit;
    }
} else {
    header("Location: ../../../frontend/templates/admin/orders.php?msg=no_autorizado");
    exit;
}
?>