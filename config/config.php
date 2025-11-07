<?php
/**
 * Archivo de Configuración Principal
 * Define constantes y rutas del proyecto
 */

// Prevenir acceso directo
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Configuración de entorno
define('ENVIRONMENT', getenv('APP_ENV') ?: 'development');
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Rutas del proyecto
define('CONFIG_PATH', APP_ROOT . '/config');
define('INCLUDES_PATH', APP_ROOT . '/includes');
define('SRC_PATH', APP_ROOT . '/src');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');
define('TEMPLATES_PATH', PUBLIC_PATH . '/templates');

// URLs base (ajustar según tu configuración de XAMPP)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$projectFolder = '/tareas-con-xampp/Web-Admin/public';

define('BASE_URL', $protocol . '://' . $host . $projectFolder);
define('ASSETS_URL', BASE_URL . '/assets');

// Configuración de la aplicación
define('APP_NAME', 'Cafetería Admin');
define('APP_VERSION', '2.0.0');

// Configuración de sesión
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'cafeteria_session');

// Configuración de seguridad
ini_set('display_errors', DEBUG_MODE ? '1' : '0');
error_reporting(DEBUG_MODE ? E_ALL : E_ERROR);

// Configuración de zona horaria
date_default_timezone_set('America/Santiago');

// Autoload básico para includes
spl_autoload_register(function ($class) {
    $file = SRC_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
