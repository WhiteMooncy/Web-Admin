<?php
require_once '../conexion/db.php'; 
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    $productId = isset($input['id']) ? intval($input['id']) : 0;
    $productName = $input['name'] ?? '';
    $productCategory = $input['category'] ?? '';
    $productPrice = isset($input['price']) ? floatval($input['price']) : 0.0;
    $productStock = isset($input['stock']) ? intval($input['stock']) : 0;
    if ($productStock > 0) {
        $productStatus = 1; 
    } else {
        $productStatus = ($input['status'] === 'Activo') ? 1 : 0;
    }
    if ($productId <= 0 || empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos para la actualización.']);
        exit;
    }
    $sql = "UPDATE productos SET Producto = ?, Categoria = ?, Precio_Unitario = ?, Stock = ?, Estado = ? WHERE ID_Producto = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error de preparación de la consulta: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssdisi", $productName, $productCategory, $productPrice, $productStock, $productStatus, $productId);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No se realizaron cambios en el producto (los datos eran los mismos o no se encontró el ID).']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar producto: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}
$conn->close();
?>