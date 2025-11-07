<?php
/**
 * Página de Logout
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/config.php';
require_once INCLUDES_PATH . '/session.php';

// Destruir sesión
destroySession();

// Redirigir a la página de login
header('Location: ' . BASE_URL . '/templates/login.php');
exit();
