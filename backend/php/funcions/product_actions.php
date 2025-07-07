<?php
require_once '../../../backend/php/conexion/db.php'; // Asegúrate de que la conexión esté disponible
session_start(); // Necesario si usas sesiones para roles o autenticación

header('Content-Type: application/json'); // La respuesta siempre será JSON

// Asegúrate de que la conexión a la base de datos esté disponible
if (!isset($conn) || !$conn instanceof mysqli) {
    echo json_encode(['success' => false, 'message' => 'Error: Conexión a la base de datos no disponible.']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Leer los datos JSON si es una petición POST
$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'add_product':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
            $productName = $conn->real_escape_string($input['name']);
            $productCategory = $conn->real_escape_string($input['category']);
            $productPrice = (float)$input['price'];
            $productStock = (int)$input['stock'];
            $productStatus = $conn->real_escape_string($input['status']);

            if (empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0 || empty($productStatus)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios y deben ser válidos.']);
                break;
            }

            $sql = "INSERT INTO productos (Producto, Categoria, Precio_Unitario, Stock, Estado) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssdis", $productName, $productCategory, $productPrice, $productStock, $productStatus);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Producto agregado correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al agregar el producto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de agregar: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido o datos incompletos para agregar.']);
        }
        break;

    case 'get_product':
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $productId = (int)$_GET['id'];
            $sql = "SELECT ID_Producto, Producto, Categoria, Precio_Unitario, Stock, Estado FROM productos WHERE ID_Producto = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    echo json_encode(['success' => true, 'product' => $result->fetch_assoc()]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de obtener producto: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de producto no especificado.']);
        }
        break;

    case 'update_product':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
            $productId = (int)$input['id'];
            $productName = $conn->real_escape_string($input['name']);
            $productCategory = $conn->real_escape_string($input['category']);
            $productPrice = (float)$input['price'];
            $productStock = (int)$input['stock'];
            $productStatus = $conn->real_escape_string($input['status']);

            if (empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0 || empty($productStatus)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios y deben ser válidos.']);
                break;
            }

            $sql = "UPDATE productos SET Producto = ?, Categoria = ?, Precio_Unitario = ?, Stock = ?, Estado = ? WHERE ID_Producto = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssdisi", $productName, $productCategory, $productPrice, $productStock, $productStatus, $productId);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de actualizar: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido o datos incompletos para actualizar.']);
        }
        break;

    case 'delete_product': // Considera cambiar a 'inactivate_product' para no borrar permanentemente
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input && isset($input['id'])) {
            $productId = (int)$input['id'];
            // Opcional: Cambiar a UPDATE productos SET Estado = 'Inactivo' WHERE ID_Producto = ?
            $sql = "DELETE FROM productos WHERE ID_Producto = ?"; 
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $productId);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminar: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de producto no especificado para eliminar.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        break;
}

$conn->close();
?>