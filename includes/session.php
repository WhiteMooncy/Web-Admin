<?php
/**
 * Gestión de Sesiones
 * Maneja inicio de sesión, verificación y cierre de sesión
 */

// Prevenir acceso directo
if (!defined('APP_ROOT')) {
    die('Acceso denegado');
}

/**
 * Iniciar sesión segura
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        
        // Configuración segura de sesión
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        
        session_start();
        
        // Regenerar ID de sesión periódicamente
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['ID_Usuario']) && !empty($_SESSION['ID_Usuario']);
}

/**
 * Requerir autenticación (redirige si no está logueado)
 */
function requireAuth($redirectUrl = '/templates/login.php') {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . $redirectUrl);
        exit();
    }
}

/**
 * Verificar rol del usuario
 */
function hasRole($roleName) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = strtolower($_SESSION['user_role_name'] ?? '');
    return $userRole === strtolower($roleName);
}

/**
 * Requerir rol específico
 */
function requireRole($roleName, $redirectUrl = '/templates/login.php') {
    if (!hasRole($roleName) && !hasRole('administrador')) {
        header('Location: ' . BASE_URL . $redirectUrl);
        exit();
    }
}

/**
 * Destruir sesión completamente
 */
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

/**
 * Obtener información del usuario actual
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['ID_Usuario'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['user_role_name'] ?? '',
    ];
}

// Iniciar sesión automáticamente
startSecureSession();
