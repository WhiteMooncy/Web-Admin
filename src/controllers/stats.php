<?php
/**
 * Controlador de Estadísticas
 * Provee datos para el dashboard
 */

define('APP_ROOT', dirname(__DIR__, 2));
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';

header('Content-Type: application/json');

// Requiere autenticación de administrador
if (!hasRole('administrador')) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

$stats = [];

try {
    // Total de usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1");
    $stats['users'] = $result->fetch_assoc()['total'];
    
    // Pedidos de hoy
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha_pedido) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['ordersToday'] = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Total de productos activos
    $result = $conn->query("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
    $stats['products'] = $result->fetch_assoc()['total'];
    
    // Ventas del mes
    $currentMonth = date('Y-m');
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total), 0) as total FROM pedidos WHERE DATE_FORMAT(fecha_pedido, '%Y-%m') = ?");
    $stmt->bind_param("s", $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['monthlySales'] = $result->fetch_assoc()['total'];
    $stmt->close();
    
    echo json_encode(['success' => true, 'stats' => $stats]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar estadísticas']);
}
