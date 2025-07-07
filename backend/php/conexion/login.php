<?php
session_start();
require_once 'db.php';

if (!isset($conn) || $conn->connect_error) {
    error_log("Error de conexión a la base de datos en login.php: " . ($conn->connect_error ?? "La variable \$conn no fue establecida."));
    $_SESSION['login_error'] = "Problema con la conexión a la base de datos. Por favor, inténtelo más tarde.";
    header("Location: ../../../frontend/templates/loged/form_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW); 

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Por favor, complete ambos campos.";
        header("Location: ../../../frontend/templates/loged/form_login.php");
        exit;
    }

    $sql = "SELECT u.ID_Usuario, u.username, u.password_hash, u.ID_Rol_FK, u.activo, r.Nombre_Rol AS rol_nombre
            FROM usuarios u
            JOIN roles r ON u.ID_Rol_FK = r.ID_Rol
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Error al preparar la consulta de login: " . $conn->error);
        $_SESSION['login_error'] = "Error interno del servidor. Por favor, inténtelo de nuevo más tarde.";
        header("Location: ../../../frontend/templates/loged/form_login.php");
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // --- Líneas de depuración para la BD ---
        error_log("Usuario encontrado en DB: " . print_r($user, true));
        error_log("Hash de contraseña desde DB: " . $user['password_hash']);
        error_log("Resultado de password_verify: " . (password_verify($password, $user['password_hash']) ? 'TRUE' : 'FALSE'));
        // --- Fin de las líneas de depuración para la BD ---

        if ($user['activo'] == 0) {
            $_SESSION['login_error'] = "Tu cuenta está inactiva. Por favor, contacta al administrador.";
            header("Location: ../../../frontend/templates/loged/form_login.php");
            exit;
        }
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['ID_Usuario'] = $user['ID_Usuario'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['ID_Rol_FK'] = $user['ID_Rol_FK'];
            $_SESSION['user_role_name'] = strtolower($user['rol_nombre']);

            header("Location: ../../../frontend/templates/loged/dashboard.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
            header("Location: ../../../frontend/templates/loged/form_login.php");
            exit; 
        }
    } else {
        $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
        header("Location: ../../../frontend/templates/loged/form_login.php");
        exit;
    }
    $stmt->close(); 
} else {
    header("Location: ../../../frontend/templates/loged/form_login.php");
    exit; 
}
$conn->close(); 
?>