<?php
require_once '../../../backend/php/conexion/db.php';
session_start();

// Asegúrate de que la conexión a la base de datos esté disponible
if (!isset($conn) || !$conn instanceof mysqli) {
    die("Error: La conexión a la base de datos no está disponible. Asegúrate de que 'db.php' esté configurado correctamente.");
}

$user_data = null;
$message = '';
$user_id = 0;

// --- Paso 1: Cargar datos del usuario si se proporciona un ID ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $sql = "SELECT ID_Usuario, username, correo, ID_Rol_FK FROM usuarios WHERE ID_Usuario = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
        } else {
            $message = "<p style='color: red;'>Usuario no encontrado.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>Error al preparar la consulta de carga: " . $conn->error . "</p>";
    }
}

// --- Paso 2: Procesar el formulario de actualización si se envía ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_user') {
    $user_id = (int)$_POST['id_usuario']; // ID del usuario que se está editando
    $username = $conn->real_escape_string($_POST['username']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $id_rol_fk = (int)$_POST['id_rol_fk'];
    $new_password = $_POST['new_password']; // Posible nueva contraseña

    // Construye la consulta SQL para actualizar
    $sql_update = "UPDATE usuarios SET username = ?, correo = ?, ID_Rol_FK = ? WHERE ID_Usuario = ?";
    $param_types = "ssii";
    $params = [&$username, &$correo, &$id_rol_fk, &$user_id];

    // Si se proporcionó una nueva contraseña, la hasheamos y la incluimos en la actualización
    if (!empty($new_password)) {
        $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql_update = "UPDATE usuarios SET username = ?, password_hash = ?, correo = ?, ID_Rol_FK = ? WHERE ID_Usuario = ?";
        $param_types = "sssii";
        array_splice($params, 1, 0, [$hashed_new_password]); // Insertar el hash en la posición correcta
    }

    if ($stmt_update = $conn->prepare($sql_update)) {
        // Vincula los parámetros dinámicamente
        // Se usa call_user_func_array para bind_param con un array de referencias
        call_user_func_array([$stmt_update, 'bind_param'], array_merge([$param_types], $params));

        if ($stmt_update->execute()) {
            $message = "<p style='color: green;'>Usuario actualizado correctamente.</p>";
            // Opcional: Redirigir de vuelta a la lista de usuarios después de la edición
            header("Location: manage_users.php?status=success&message=" . urlencode("Usuario actualizado correctamente."));
            exit();
        } else {
            $message = "<p style='color: red;'>Error al actualizar el usuario: " . $stmt_update->error . "</p>";
        }
        $stmt_update->close();
    } else {
        $message = "<p style='color: red;'>Error al preparar la consulta de actualización: " . $conn->error . "</p>";
    }

    // Si hubo un error y no se redirigió, recargar los datos del usuario por si se necesita reintentar
    if ($user_id > 0) {
        $sql = "SELECT ID_Usuario, username, correo, ID_Rol_FK FROM usuarios WHERE ID_Usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Cierra la conexión si no se va a usar más
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-width: 500px; margin: auto; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover { background-color: #0056b3; }
        .message { text-align: center; margin-top: 10px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #6c757d; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Usuario</h2>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <?php if ($user_data): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user_data['ID_Usuario']); ?>">

                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($user_data['correo']); ?>" required>

                <label for="new_password">Nueva Contraseña (dejar vacío para no cambiar):</label>
                <input type="password" id="new_password" name="new_password">

                <label for="id_rol_fk">Rol:</label>
                <select id="id_rol_fk" name="id_rol_fk" required>
                    <option value="1" <?php echo ($user_data['ID_Rol_FK'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo ($user_data['ID_Rol_FK'] == 2) ? 'selected' : ''; ?>>Empleado</option>
                    <option value="3" <?php echo ($user_data['ID_Rol_FK'] == 3) ? 'selected' : ''; ?>>Cliente</option>
                </select>

                <button type="submit">Actualizar Usuario</button>
            </form>
        <?php else: ?>
            <p style='text-align: center; color: red;'>No se pudo cargar la información del usuario para editar.</p>
        <?php endif; ?>
        <a href="manage_users.php" class="back-link">Volver a la Gestión de Usuarios</a>
    </div>

    <?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            title: '<?php echo (strpos($message, 'red') !== false) ? 'Error' : 'Éxito'; ?>',
            html: '<?php echo strip_tags($message); ?>',
            icon: '<?php echo (strpos($message, 'red') !== false) ? 'error' : 'success'; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            <?php if (strpos($message, 'green') !== false && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                window.location.href = 'manage_users.php'; // Redirige después de un éxito en POST
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>