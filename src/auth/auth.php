<?php
/**
 * Controlador de Autenticación
 * Maneja login, logout y registro de usuarios
 */

define('APP_ROOT', dirname(__DIR__, 2));
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';

/**
 * Procesar login
 */
function processLogin($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare(
        "SELECT u.ID_Usuario, u.username, u.password_hash, u.activo, r.Nombre_Rol 
         FROM usuarios u 
         JOIN roles r ON u.ID_Rol_FK = r.ID_Rol 
         WHERE u.username = ?"
    );
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error en la consulta'];
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verificar si está activo
    if ($user['activo'] != 1) {
        return ['success' => false, 'message' => 'Tu cuenta está inactiva. Contacta al administrador.'];
    }
    
    // Verificar contraseña
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
    }
    
    // Iniciar sesión
    session_regenerate_id(true);
    $_SESSION['ID_Usuario'] = $user['ID_Usuario'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role_name'] = strtolower($user['Nombre_Rol']);
    
    // Normalizar rol 'trabajador' a 'empleado'
    if ($_SESSION['user_role_name'] === 'trabajador') {
        $_SESSION['user_role_name'] = 'empleado';
    }
    
    return ['success' => true, 'message' => 'Login exitoso'];
}

/**
 * Procesar registro
 */
function processRegister($data) {
    global $conn;
    
    // Validar campos requeridos
    $required = ['nombre', 'correo', 'telefono', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => "El campo {$field} es requerido"];
        }
    }
    
    // Validar formato de correo
    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'El formato del correo es inválido'];
    }
    
    // Verificar duplicados
    $stmt = $conn->prepare(
        "SELECT ID_Usuario FROM usuarios WHERE username = ? OR Correo = ? OR Telefono = ?"
    );
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error en la consulta'];
    }
    
    $stmt->bind_param("sss", $data['username'], $data['correo'], $data['telefono']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'El usuario, correo o teléfono ya existen'];
    }
    $stmt->close();
    
    // Insertar nuevo usuario (rol cliente por defecto = 3)
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
    $rolCliente = 3;
    
    $stmt = $conn->prepare(
        "INSERT INTO usuarios (nombre, Correo, Telefono, username, password_hash, ID_Rol_FK, activo) 
         VALUES (?, ?, ?, ?, ?, ?, 1)"
    );
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al preparar el registro'];
    }
    
    $stmt->bind_param(
        "sssssi",
        $data['nombre'],
        $data['correo'],
        $data['telefono'],
        $data['username'],
        $passwordHash,
        $rolCliente
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Registro exitoso. Ahora puedes iniciar sesión.'];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Error al registrar usuario'];
    }
}

/**
 * Procesar logout
 */
function processLogout() {
    destroySession();
    return ['success' => true, 'message' => 'Sesión cerrada correctamente'];
}
