<?php
require_once '../../../backend/php/conexion/db.php';
session_start();
if (!isset($conn) || !$conn instanceof mysqli) {
    error_log("Error: La conexión a la base de datos no está disponible en manage_users.php. Asegúrate de que 'db.php' esté configurado correctamente.");
    $connection_error = "Error crítico: No se pudo establecer conexión con la base de datos.";
    $message = $connection_error; 
    $alert_status = "error";   
    $conn = null; 
}

$message = ''; 
$alert_status = ''; 
$user_data_edit = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_user') {
    if ($conn) {
        $username = trim($_POST['username']);
        $password = $_POST['password']; 
        $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
        $id_rol_fk = (int)$_POST['id_rol_fk'];
        if (empty($username) || empty($password) || empty($correo) || empty($id_rol_fk)) {
            $message = "Por favor, rellena todos los campos obligatorios.";
            $alert_status = "error";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { 
            $message = "El formato del correo electrónico no es válido.";
            $alert_status = "error";
        } elseif ($id_rol_fk < 1 || $id_rol_fk > 3) { //  roles válidos: 1=Admin, 2=Empleado, 3=Cliente
            $message = "Rol de usuario inválido.";
            $alert_status = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
            $check_sql = "SELECT ID_Usuario FROM usuarios WHERE username = ? OR correo = ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param("ss", $username, $correo);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                if ($check_result->num_rows > 0) {
                    $message = "El nombre de usuario o correo electrónico ya existen.";
                    $alert_status = "error";
                } else {
                    $sql_insert = "INSERT INTO usuarios (username, password_hash, correo, ID_Rol_FK, activo) VALUES (?, ?, ?, ?, 1)";
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $stmt_insert->bind_param("sssi", $username, $hashed_password, $correo, $id_rol_fk);
                        if ($stmt_insert->execute()) {
                            $message = "¡Usuario agregado correctamente!";
                            $alert_status = "success";
                            error_log("Usuario '$username' agregado exitosamente."); // Depuración de éxito
                        } else {
                            $message = "Error al agregar el usuario: " . $stmt_insert->error;
                            $alert_status = "error";
                            error_log("Error al ejecutar INSERT: " . $stmt_insert->error); // Depuración de error
                        }
                        $stmt_insert->close();
                    } else {
                        $message = "Error al preparar la consulta de inserción: " . $conn->error;
                        $alert_status = "error";
                        error_log("Error al preparar INSERT SQL: " . $conn->error); // Depuración de error
                    }
                }
                $check_stmt->close();
            } else {
                $message = "Error al preparar la consulta de verificación de existencia: " . $conn->error;
                $alert_status = "error";
                error_log("Error al preparar CHECK: " . $conn->error); // Depuración de error
            }
        }
    } else {
        $message = "Error: No se pudo agregar el usuario debido a un problema de conexión con la base de datos.";
        $alert_status = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . $alert_status . "&message=" . urlencode($message));
    exit(); 
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_user') {
    if ($conn) {
        $user_id_edit_form = (int)$_POST['id_usuario'];
        $username_edit = trim($_POST['username']);
        $correo_edit = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
        $id_rol_fk_edit = (int)$_POST['id_rol_fk'];
        $new_password_edit = $_POST['new_password']; // Texto plano
        // Validaciones
        if (empty($username_edit) || empty($correo_edit) || empty($id_rol_fk_edit) || empty($user_id_edit_form)) {
            $message = "Por favor, rellena todos los campos obligatorios para la edición.";
            $alert_status = "error";
        } elseif (!filter_var($correo_edit, FILTER_VALIDATE_EMAIL)) {
            $message = "El formato del correo electrónico no es válido.";
            $alert_status = "error";
        } elseif ($id_rol_fk_edit < 1 || $id_rol_fk_edit > 3) {
            $message = "Rol de usuario inválido.";
            $alert_status = "error";
        } else {
            // Verificar si el username o correo ya existen para otro usuario (excepto el actual)
            $check_sql = "SELECT ID_Usuario FROM usuarios WHERE (username = ? OR correo = ?) AND ID_Usuario != ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param("ssi", $username_edit, $correo_edit, $user_id_edit_form);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                if ($check_result->num_rows > 0) {
                    $message = "El nombre de usuario o correo electrónico ya existen para otro usuario.";
                    $alert_status = "error";
                } else {
                    // Consulta SQL para actualizar dinámicamente
                    $sql_update_parts = ["username = ?", "correo = ?", "ID_Rol_FK = ?"];
                    $param_types = "ssi"; 
                    $params = [&$username_edit, &$correo_edit, &$id_rol_fk_edit];
                    if (!empty($new_password_edit)) {
                        $hashed_new_password_edit = password_hash($new_password_edit, PASSWORD_DEFAULT);
                        array_splice($sql_update_parts, 1, 0, "password_hash = ?"); 
                        $param_types = "s" . $param_types; 
                        array_splice($params, 1, 0, [$hashed_new_password_edit]);
                    }
                    $sql_update = "UPDATE usuarios SET " . implode(", ", $sql_update_parts) . " WHERE ID_Usuario = ?";
                    $param_types .= "i"; 
                    $params[] = &$user_id_edit_form; 
                    if ($stmt_update = $conn->prepare($sql_update)) {
                        $bind_args = array($param_types);
                        foreach ($params as $key => $value) {
                            $bind_args[] = &$params[$key];
                        }
                        call_user_func_array([$stmt_update, 'bind_param'], $bind_args);
                        if ($stmt_update->execute()) {
                            $message = "Usuario actualizado correctamente.";
                            $alert_status = "success";
                        } else {
                            $message = "Error al actualizar el usuario: " . $stmt_update->error;
                            $alert_status = "error";
                            error_log("Error SQL al actualizar usuario: " . $stmt_update->error);
                        }
                        $stmt_update->close();
                    } else {
                        $message = "Error al preparar la consulta de actualización: " . $conn->error;
                        $alert_status = "error";
                        error_log("Error al preparar UPDATE: " . $conn->error);
                    }
                }
                $check_stmt->close();
            } else {
                $message = "Error al preparar la consulta de verificación de existencia para edición: " . $conn->error;
                $alert_status = "error";
                error_log("Error al preparar CHECK para UPDATE: " . $conn->error);
            }
        }
    } else {
        $message = "Error: No se pudo actualizar el usuario debido a un problema de conexión con la base de datos.";
        $alert_status = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . $alert_status . "&message=" . urlencode($message));
    exit();
}
// --- Lógica para Inactivar Usuario (GET) ---
if (isset($_GET['action']) && $_GET['action'] == 'deactivate_user' && isset($_GET['id'])) {
    if ($conn) {
        $user_id_deactivate = (int)$_GET['id'];
        $sql_deactivate = "UPDATE usuarios SET activo = 0 WHERE ID_Usuario = ?";
        if ($stmt_deactivate = $conn->prepare($sql_deactivate)) {
            $stmt_deactivate->bind_param("i", $user_id_deactivate);
            if ($stmt_deactivate->execute()) {
                $message = "Usuario inactivado correctamente.";
                $alert_status = "success";
            } else {
                $message = "Error al inactivar el usuario: " . $stmt_deactivate->error;
                $alert_status = "error";
                error_log("Error SQL al inactivar usuario: " . $stmt_deactivate->error);
            }
            $stmt_deactivate->close();
        } else {
            $message = "Error al preparar la inactivación: " . $conn->error;
            $alert_status = "error";
            error_log("Error al preparar DEACTIVATE: " . $conn->error);
        }
    } else {
        $message = "Error de conexión al intentar inactivar el usuario.";
        $alert_status = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . $alert_status . "&message=" . urlencode($message));
    exit();
}
// --- Lógica para Reactivar Usuario (GET) ---
if (isset($_GET['action']) && $_GET['action'] == 'activate_user' && isset($_GET['id'])) {
    if ($conn) {
        $user_id_activate = (int)$_GET['id'];
        $sql_activate = "UPDATE usuarios SET activo = 1 WHERE ID_Usuario = ?";
        if ($stmt_activate = $conn->prepare($sql_activate)) {
            $stmt_activate->bind_param("i", $user_id_activate);
            if ($stmt_activate->execute()) {
                $message = "Usuario reactivado correctamente.";
                $alert_status = "success";
            } else {
                $message = "Error al reactivar el usuario: " . $stmt_activate->error;
                $alert_status = "error";
                error_log("Error SQL al reactivar usuario: " . $stmt_activate->error);
            }
            $stmt_activate->close();
        } else {
            $message = "Error al preparar la reactivación: " . $conn->error;
            $alert_status = "error";
            error_log("Error al preparar ACTIVATE: " . $conn->error);
        }
    } else {
        $message = "Error de conexión al intentar reactivar el usuario.";
        $alert_status = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . $alert_status . "&message=" . urlencode($message));
    exit();
}
// --- Lógica para Cargar Datos de Edición (GET, para fetch via JS) ---
// Este bloque es especial porque responde JSON y luego termina la ejecución.
if (isset($_GET['action']) && $_GET['action'] == 'load_edit_data' && isset($_GET['id'])) {
    header('Content-Type: application/json'); // Indicar que la respuesta es JSON
    if ($conn) {
        $id_usuario_to_edit = (int)$_GET['id'];
        $sql = "SELECT ID_Usuario, username, correo, ID_Rol_FK FROM usuarios WHERE ID_Usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id_usuario_to_edit);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user_data_edit = $result->fetch_assoc();
                echo json_encode(['status' => 'success', 'user' => $user_data_edit]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al preparar la carga de datos: ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error de conexión al intentar cargar datos.']);
    }
    if ($conn) $conn->close();
    exit(); 
}
if (isset($_GET['status']) && isset($_GET['message'])) {
    $alert_status = htmlspecialchars($_GET['status']);
    $message = htmlspecialchars($_GET['message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../src/js/logout-confirm.js"></script>
    <title>Cafetería | Administración de Usuarios</title>
    <style>
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Posición fija para cubrir toda la pantalla */
            z-index: 1000; /* Por encima de todo lo demás */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Habilitar scroll si el contenido es demasiado largo */
            background-color: rgba(0,0,0,0.6); /* Fondo semi-transparente más oscuro */
            padding-top: 60px; /* Espacio para el contenido */
            animation: fadeIn 0.3s forwards; /* Animación de aparición */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% desde la parte superior y centrado horizontalmente */
            padding: 30px; /* Más padding */
            border: 1px solid #888;
            width: 80%; /* Ancho del modal */
            max-width: 550px; /* Ancho máximo un poco más grande */
            border-radius: 10px; /* Bordes más redondeados */
            box-shadow: 0 8px 20px rgba(0,0,0,0.4); /* Sombra más pronunciada */
            position: relative;
            animation: slideInTop 0.4s forwards; /* Animación de entrada */
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 30px; /* Más grande */
            font-weight: bold;
            line-height: 1; /* Ajuste para centrado vertical */
        }
        .close-button:hover,
        .close-button:focus {
            color: #333; /* Color más oscuro al pasar el mouse */
            text-decoration: none;
            cursor: pointer;
        }
        /* Estilos del formulario dentro del modal */
        .modal-content h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px; /* Más espacio */
        }
        .modal-content label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600; /* Más énfasis */
            color: #555;
        }
        .modal-content input[type="text"],
        .modal-content input[type="password"],
        .modal-content input[type="email"],
        .modal-content select {
            width: calc(100% - 20px); /* Ajuste de ancho */
            padding: 12px; /* Más padding */
            margin-bottom: 20px; /* Más espacio */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .modal-content button[type="submit"] {
            background-color: #28a745; /* Verde Bootstrap */
            color: white;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px; /* Texto más grande */
            width: 100%;
            transition: background-color 0.2s ease; /* Transición suave */
        }
        .modal-content button[type="submit"]:hover {
            background-color: #218838; /* Verde más oscuro al pasar el mouse */
        }
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInTop {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        /* Ajuste para los botones de acción en la tabla */
        .users-table .btn-edit,
        .users-table .btn-delete,
        .users-table .btn-activate { /* Nuevo estilo para reactivar */
            padding: 5px 10px;
            font-size: 14px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        .users-table .btn-edit {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .users-table .btn-edit:hover {
            background-color: #0056b3;
        }
        .users-table .btn-delete {
            background-color: #dc3545; /* Rojo para inactivar */
            color: white;
            border: none;
        }
        .users-table .btn-delete:hover {
            background-color: #c82333;
        }
        .users-table .btn-activate { /* Estilo para botón de reactivar */
            background-color: #ffc107; /* Amarillo/Naranja */
            color: #333;
            border: none;
        }
        .users-table .btn-activate:hover {
            background-color: #e0a800;
        }
        .inactive-user {
            background-color: #f8f9fa; /* Fondo ligeramente gris para usuarios inactivos */
            color: #6c757d; /* Texto atenuado */
            font-style: italic;
        }
    </style>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header>
            <h1>Administración de Usuarios</h1>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../loged/dashboard.php">Inicio</a></li>
                    <li><a href="../loged/manage_users.php" class="active">Usuarios</a></li>
                    <li><a href="../loged/orders.php">Pedidos</a></li>
                    <li><a href="../loged/products.php">Productos</a></li>
                    <li><a href="../loged/reports.php">Reportes</a></li>
                    <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Gestión de Usuarios</h2>
            <button class="btn-add-user" id="openAddUserModal">Agregar Usuario</button>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($conn) && $conn instanceof mysqli) {
                        $sql = "SELECT ID_Usuario, username, correo, ID_Rol_FK, activo FROM usuarios ORDER BY ID_Rol_FK ASC, activo DESC, username ASC";
                        $result = $conn->query($sql);
                        if ($result) {
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $row_class = ($row['activo'] == 0) ? 'inactive-user' : ''; 
                                    echo "<tr class='" . $row_class . "'>";
                                    echo "<td>" . htmlspecialchars($row['ID_Usuario']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                                    echo "<td>";
                                    if ($row['ID_Rol_FK'] == 1) {
                                        echo 'Administrador';
                                    } elseif ($row['ID_Rol_FK'] == 2) {
                                        echo 'Empleado';
                                    } else { 
                                        echo 'Cliente';
                                    }
                                    echo "</td>";
                                    echo "<td>";
                                    echo ($row['activo'] == 1) ? 'Activo' : 'Inactivo';
                                    echo "</td>";
                                    echo "<td>";
                                    if ($row['activo'] == 1) {
                                        echo "<button class='btn-edit' onclick=\"showEditUserModal(" . htmlspecialchars($row['ID_Usuario']) . ")\">Editar</button>";
                                        echo "<button class='btn-delete' onclick=\"deactivateUser(" . htmlspecialchars($row['ID_Usuario']) . ")\">Inactivar</button>";
                                    } else {
                                        echo "<button class='btn-activate' onclick=\"activateUser(" . htmlspecialchars($row['ID_Usuario']) . ")\">Reactivar</button>";
                                        echo "<button class='btn-edit' onclick=\"showEditUserModal(" . htmlspecialchars($row['ID_Usuario']) . ")\" style='margin-left: 5px;'>Editar</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No hay usuarios registrados.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Error al cargar usuarios: " . $conn->error . "</td></tr>";
                            error_log("Error al cargar usuarios en la tabla: " . $conn->error);
                        }
                    } else {
                        echo "<tr><td colspan='6'>" . (isset($connection_error) ? $connection_error : 'Error: La conexión a la base de datos no está disponible.') . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
        <footer>
            © 2025 - Administración de Usuarios | Cafetería
        </footer>
    </div>
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeAddUserModal">&times;</span>
            <h2>Agregar Nuevo Usuario</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="add_user">
                <label for="username_modal">Nombre de Usuario:</label>
                <input type="text" id="username_modal" name="username" required autocomplete="new-username">
                <label for="password_modal">Contraseña:</label>
                <input type="password" id="password_modal" name="password" required autocomplete="new-password">
                <label for="correo_modal">Correo Electrónico:</label>
                <input type="email" id="correo_modal" name="correo" required autocomplete="email">
                <label for="id_rol_fk_modal">Rol:</label>
                <select id="id_rol_fk_modal" name="id_rol_fk" required autocomplete="organization-title">
                    <option value="">Selecciona un rol</option>
                    <option value="1">Administrador</option>
                    <option value="2">Empleado</option>
                    <option value="3">Cliente</option>
                </select>
                <button type="submit">Agregar Usuario</button>
            </form>
        </div>
    </div>
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditUserModal">&times;</span>
            <h2>Editar Usuario</h2>
            <form id="editUserForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" id="edit_id_usuario" name="id_usuario" value="">
                <label for="edit_username">Nombre de Usuario:</label>
                <input type="text" id="edit_username" name="username" required autocomplete="username">
                <label for="edit_correo">Correo Electrónico:</label>
                <input type="email" id="edit_correo" name="correo" required autocomplete="email">
                <label for="edit_new_password">Nueva Contraseña (dejar vacío para no cambiar):</label>
                <input type="password" id="edit_new_password" name="new_password" autocomplete="new-password">
                <label for="edit_id_rol_fk">Rol:</label>
                <select id="edit_id_rol_fk" name="id_rol_fk" required autocomplete="organization-title">
                    <option value="1">Administrador</option>
                    <option value="2">Empleado</option>
                    <option value="3">Cliente</option>
                </select>
                <button type="submit">Actualizar Usuario</button>
            </form>
        </div>
    </div>
    <script>
        var addUserModal = document.getElementById("addUserModal");
        var openAddUserBtn = document.getElementById("openAddUserModal");
        var closeAddUserSpan = document.getElementById("closeAddUserModal");
        openAddUserBtn.onclick = function() {
            addUserModal.style.display = "block";
        }
        closeAddUserSpan.onclick = function() {
            addUserModal.style.display = "none";
        }
        var editUserModal = document.getElementById("editUserModal");
        var closeEditUserSpan = document.getElementById("closeEditUserModal");
        closeEditUserSpan.onclick = function() {
            editUserModal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == addUserModal) {
                addUserModal.style.display = "none";
            }
            if (event.target == editUserModal) {
                editUserModal.style.display = "none";
            }
        }
        function showEditUserModal(userId) {
            Swal.fire({
                title: '¿Deseas editar este usuario?',
                text: "Se abrirá el formulario de edición.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, editar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?action=load_edit_data&id=' + userId)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                document.getElementById('edit_id_usuario').value = data.user.ID_Usuario;
                                document.getElementById('edit_username').value = data.user.username;
                                document.getElementById('edit_correo').value = data.user.correo;
                                document.getElementById('edit_id_rol_fk').value = data.user.ID_Rol_FK;
                                document.getElementById('edit_new_password').value = ''; // Limpiar campo de contraseña

                                editUserModal.style.display = "block";
                            } else {
                                Swal.fire('Error', data.message || 'No se pudieron cargar los datos del usuario.', 'error');
                            }
                        })
                        .catch(e => {
                            console.error('Error al cargar datos del usuario:', e);
                            Swal.fire('Error de Conexión', 'No se pudo cargar la información del usuario debido a un problema de red o servidor.', 'error');
                        });
                }
            });
        }
        function deactivateUser(userId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡El usuario será inactivado! Podrás reactivarlo más tarde.", 
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, inactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?action=deactivate_user&id=' + userId;
                }
            });
        }
        function activateUser(userId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El usuario será reactivado y volverá a tener acceso.", // Texto modificado
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, reactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?action=activate_user&id=' + userId;
                }
            });
        }
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');
            if (status && message) {
                const newUrl = window.location.origin + window.location.pathname + window.location.hash;
                history.replaceState({}, document.title, newUrl);
                Swal.fire({
                    icon: status,
                    title: (status === 'success' ? 'Éxito' : 'Error'),
                    text: decodeURIComponent(message),
                    confirmButtonText: 'Aceptar'
                });
            }
        };
    </script>
</body>
</html>