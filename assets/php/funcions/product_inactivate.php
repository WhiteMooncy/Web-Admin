<?php
require_once '../conexion/db.php'; 
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input && isset($input['id'])) {
    $productId = intval($input['id']);
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
        exit;
    }
    $sql = "UPDATE productos SET Estado = 0, Stock = 0 WHERE ID_Producto = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error de preparación de la consulta: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $productId);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Producto inactivado y stock en 0 exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado o ya estaba inactivo/sin stock.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al inactivar producto: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida o ID de producto no proporcionado.']);
}
$conn->close();
?>