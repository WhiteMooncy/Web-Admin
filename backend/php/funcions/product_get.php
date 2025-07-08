<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../conexion/db.php'; 
header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
        exit;
    }
    $sql = "SELECT ID_Producto, Producto, Categoria, Precio_Unitario, Stock, Estado FROM productos WHERE ID_Producto = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error de preparación de la consulta: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado.']);
}
$conn->close();
?>