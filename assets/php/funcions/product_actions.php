<?php
require_once '../conexion/db.php'; // Asegúrate de que la conexión esté disponible
session_start(); // Necesario si usas sesiones para roles o autenticación

header('Content-Type: application/json'); // La respuesta siempre será JSON

// Asegúrate de que la conexión a la base de datos esté disponible
if (!isset($conn) || !$conn instanceof mysqli) {
    echo json_encode(['success' => false, 'message' => 'Error: Conexión a la base de datos no disponible.']);
    exit();
}

// Inicializar $action de forma segura
$action = '';
// Primero, intentar obtener 'action' de la URL (para GET requests como get_product)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// Leer los datos JSON si es una petición POST
$input = json_decode(file_get_contents('php://input'), true);

// Si es una petición POST y la acción no se encontró en la URL, buscarla en el cuerpo JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input && isset($input['action'])) {
    $action = $input['action'];
}

switch ($action) {
    case 'add_product':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
            $productName = $conn->real_escape_string($input['name'] ?? '');
            $productCategory = $conn->real_escape_string($input['category'] ?? '');
            $productPrice = isset($input['price']) ? (float)$input['price'] : -1; // Usar -1 para indicar error si no está
            $productStock = isset($input['stock']) ? (int)$input['stock'] : -1;
            // Convertir el estado de string ('Activo'/'Inactivo') a TINYINT (1/0) si tu DB lo espera así
            $productStatus = ($input['status'] === 'Activo') ? 1 : 0;

            if (empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0) {
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
                    $product = $result->fetch_assoc();
                    // Asegurarse de que el campo 'Estado' sea un booleano o string 'Activo'/'Inactivo' si tu JS lo espera así,
                    // aunque en el modal ya lo transformamos para el select. Aquí lo dejamos como viene de DB (0/1 o string).
                    echo json_encode(['success' => true, 'product' => $product]);
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
            $productName = $conn->real_escape_string($input['name'] ?? '');
            $productCategory = $conn->real_escape_string($input['category'] ?? '');
            $productPrice = isset($input['price']) ? (float)$input['price'] : -1;
            $productStock = isset($input['stock']) ? (int)$input['stock'] : -1;
            $productStatus = ($input['status'] === 'Activo') ? 1 : 0;

            if ($productId <= 0 || empty($productName) || empty($productCategory) || $productPrice < 0 || $productStock < 0) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios y deben ser válidos para actualizar.']);
                break;
            }

            $sql = "UPDATE productos SET Producto = ?, Categoria = ?, Precio_Unitario = ?, Stock = ?, Estado = ? WHERE ID_Producto = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssdisi", $productName, $productCategory, $productPrice, $productStock, $productStatus, $productId);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente.']);
                    } else {
                        echo json_encode(['success' => true, 'message' => 'No se realizaron cambios en el producto.']);
                    }
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

    case 'inactivate_product': // Nuevo caso para inactivar
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input && isset($input['id'])) {
            $productId = (int)$input['id'];

            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de producto inválido para inactivar.']);
                break;
            }

            // Actualizar el estado a 'Inactivo' (o 0) y el stock a 0
            $sql = "UPDATE productos SET Estado = 0, Stock = 0 WHERE ID_Producto = ?"; // Asumiendo 0 para 'Inactivo'
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $productId);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Producto inactivado y stock marcado sin existencias correctamente.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Producto no encontrado o ya estaba inactivo/sin stock.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al inactivar el producto: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de inactivar: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido o ID de producto no especificado para inactivar.']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida o no especificada.']);
        break;
}

$conn->close();
?>