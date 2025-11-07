<?php
/**
 * Configuración de Base de Datos
 * Gestiona la conexión a MySQL
 */

// Prevenir acceso directo
if (!defined('APP_ROOT')) {
    die('Acceso denegado');
}

// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'web-admin');
define('DB_CHARSET', 'utf8mb4');

// Crear conexión global
$conn = null;

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset(DB_CHARSET);
    
    if (DEBUG_MODE) {
        error_log("Conexión a base de datos establecida correctamente");
    }
    
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Error de base de datos: " . $e->getMessage());
    } else {
        die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
    }
}

/**
 * Función helper para cerrar la conexión
 */
function closeConnection() {
    global $conn;
    if ($conn && $conn instanceof mysqli) {
        $conn->close();
    }
}

// Registrar cierre automático al finalizar el script
register_shutdown_function('closeConnection');
