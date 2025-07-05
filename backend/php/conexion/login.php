<?php
session_start(); // Inicia la sesión al principio del script.
require_once 'db.php'; // Ruta a tu archivo de conexión a la base de datos.

// Verificar si se han enviado datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanear y validar la entrada del usuario
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    // Verificar que ambas entradas existan y no estén vacías
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Por favor, complete ambos campos.";
        header("Location: ../../../frontend/templates/loged/form_login.php");
        exit;
    }

    // Consulta SQL para obtener el usuario y el nombre del rol
    $sql = "SELECT u.ID_Usuario, u.username, u.password_hash, u.ID_Rol_FK, r.Nombre_Rol AS rol_nombre
            FROM usuarios u
            JOIN roles r ON u.ID_Rol_FK = r.ID_Rol
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña usando password_verify
        if (password_verify($password, $user['password_hash'])) {
            // Guardar datos en la sesión
            $_SESSION['ID_Usuario'] = $user['ID_Usuario'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['ID_Rol_FK'] = $user['ID_Rol_FK'];
            $_SESSION['user_role_name'] = strtolower($user['rol_nombre']); // Siempre en minúsculas

            // Redirigir al dashboard
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
    // Si no se accedió por POST, redirigir al formulario de login
    header("Location: ../../../frontend/templates/loged/form_login.php");
    exit;
}

$conn->close(); // Cierra la conexión a la base de datos al final.
?>