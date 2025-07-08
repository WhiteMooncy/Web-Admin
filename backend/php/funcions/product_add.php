<?php
require_once '../conexion/db.php'; 

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    $productName = $input['name'] ?? '';
    $productCategory = $input['category'] ?? '';
    $productPrice = isset($input['price']) ? floatval($input['price']) : 0.0;
    $productStock = isset($input['stock']) ? intval($input['stock']) : 0;
    $productStatus = ($input['status'] === 'Activo') ? 1 : 0; // Convertir 'Activo'/'Inactivo' a 1/0

    // Validaciones básicas
    if (empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos y deben ser válidos.']);
        exit;
    }

    $sql = "INSERT INTO productos (Producto, Categoria, Precio_Unitario, Stock, Estado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error de preparación de la consulta: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssdis", $productName, $productCategory, $productPrice, $productStock, $productStatus);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar producto: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}

$conn->close();
?>