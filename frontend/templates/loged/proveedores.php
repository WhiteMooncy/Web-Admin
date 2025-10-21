<?php
require_once '../../../backend/php/conexion/check_role.php';
require_once '../../../backend/php/conexion/db.php';

// --- Crear / Editar proveedor ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $id = $_POST['ID_Proveedor'] ?? null;
    $nombre = $_POST['Nombre'];
    $correo = $_POST['Correo'];
    $telefono = $_POST['Telefono'];
    $direccion = $_POST['Direccion'];

    // Verificar si el teléfono ya existe
    if ($_POST['action'] === 'add_proveedor') {
        $stmt = $conn->prepare("SELECT ID_Proveedor FROM proveedores WHERE Telefono = ?");
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $status = "error";
            $message = "El número de teléfono ya está registrado.";
            header("Location: proveedores.php?status=$status&message=" . urlencode($message));
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO proveedores (Nombre, Correo, Telefono, Direccion, Estado) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $nombre, $correo, $telefono, $direccion);
        $stmt->execute();
        $stmt->close();
        $status = "success";
        $message = "Proveedor añadido correctamente.";
    } elseif ($_POST['action'] === 'edit_proveedor' && $id) {
        $stmt = $conn->prepare("SELECT ID_Proveedor FROM proveedores WHERE Telefono = ? AND ID_Proveedor != ?");
        $stmt->bind_param("si", $telefono, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $status = "error";
            $message = "El número de teléfono ya está registrado por otro proveedor.";
            header("Location: proveedores.php?status=$status&message=" . urlencode($message));
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("UPDATE proveedores SET Nombre=?, Correo=?, Telefono=?, Direccion=? WHERE ID_Proveedor=?");
        $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $direccion, $id);
        $stmt->execute();
        $stmt->close();
        $status = "success";
        $message = "Proveedor actualizado correctamente.";
    }
    header("Location: proveedores.php?status=$status&message=" . urlencode($message));
    exit;
}

// --- Inactivar proveedor ---
if (isset($_GET['inactivate'])) {
    $id = intval($_GET['inactivate']);
    $stmt = $conn->prepare("UPDATE proveedores SET Estado=1 WHERE ID_Proveedor=?");
    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $status = "success";
    $message = "Proveedor inactivado correctamente.";
    header("Location: proveedores.php?status=$status&message=" . urlencode($message));
    exit;
}

// --- Activar proveedor ---
if (isset($_GET['activate'])) {
    $id = intval($_GET['activate']);
    $stmt = $conn->prepare("UPDATE proveedores SET Estado=0 WHERE ID_Proveedor=?");
    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $status = "success";
    $message = "Proveedor activado correctamente.";
    header("Location: proveedores.php?status=$status&message=" . urlencode($message));
    exit;
}

// --- Cargar datos para edición (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] == 'load_edit_data' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    $sql = "SELECT ID_Proveedor, Nombre, Correo, Telefono, Direccion FROM proveedores WHERE ID_Proveedor = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $proveedor = $result->fetch_assoc();
            echo json_encode(['status' => 'success', 'proveedor' => $proveedor]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Proveedor no encontrado.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta.']);
    }
    exit;
}

$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6); 
            padding-top: 60px;
            animation: fadeIn 0.3s forwards; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 30px; 
            border: 1px solid #888;
            width: 80%; 
            max-width: 550px; 
            border-radius: 10px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.4); 
            position: relative;
            animation: slideInTop 0.4s forwards;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 30px; 
            font-weight: bold;
            line-height: 1; 
        }
        .close-button:hover,
        .close-button:focus {
            color: #333; 
            text-decoration: none;
            cursor: pointer;
        }
        .modal-content h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px; 
        }
        .modal-content label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600; 
            color: #555;
        }
        .modal-content input[type="text"],
        .modal-content input[type="password"],
        .modal-content input[type="email"],
        .modal-content select {
            width: calc(100% - 20px); 
            padding: 12px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .modal-content button[type="submit"] {
            background-color: #28a745; 
            color: white;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.2s ease; 
        }
        .modal-content button[type="submit"]:hover {
            background-color: #218838; 
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideInTop { from { transform: translateY(-60px); } to { transform: translateY(0); } }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../src/js/logout-confirm.js"></script>
    <title>Cafetería | Proveedores</title>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header class="header">
            <h1>Proveedores</h1>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../loged/dashboard.php">Inicio</a></li>
                    <?php if ($user_role_name === 'admin'): ?>
                        <li><a href="../loged/manage_users.php">Usuarios</a></li>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/proveedores.php" class="active">Proveedores</a></li>
                        <li><a href="../loged/products.php">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <?php if ($user_role_name === 'empleado'): ?>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/proveedores.php" class="active">Proveedores</a></li>
                        <li><a href="../loged/products.php">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Administración de Proveedores</h2>
            <?php if ($user_role_name === 'admin'): ?>
                <button class="btn-add" onclick="abrirModalAgregar()">+ Añadir Proveedor</button>
            <?php endif; ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <?php if ($user_role_name === 'admin'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT ID_Proveedor, Nombre, Correo, Telefono, Direccion, Estado FROM proveedores";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ID_Proveedor']) ?></td>
                            <td><?= htmlspecialchars($row['Nombre']) ?></td>
                            <td><?= htmlspecialchars($row['Correo']) ?></td>
                            <td><?= htmlspecialchars($row['Telefono']) ?></td>
                            <td><?= htmlspecialchars($row['Direccion']) ?></td>
                            <td><?= $row['Estado'] == 0 ? 'Activo' : 'Inactivo' ?></td>
                            <?php if ($user_role_name === 'admin'): ?>
                                <td class="actions">
                                    <button class="btn-edit" onclick="abrirModalEditar(<?= $row['ID_Proveedor'] ?>)">Editar</button>
                                    <?php if ($row['Estado'] == 0): ?>
                                        <button class="btn-delete" onclick="inactivarProveedor(<?= $row['ID_Proveedor'] ?>)">Inactivar</button>
                                    <?php else: ?>
                                        <button class="btn-activate" onclick="activarProveedor(<?= $row['ID_Proveedor'] ?>)">Activar</button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="<?= ($user_role_name === 'admin') ? 7 : 6 ?>">No hay proveedores registrados</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <footer>
            © 2025 - Cafetería
        </footer>
    </div>

    <!-- Modal HTML -->
    <div id="proveedorModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="cerrarModal()">&times;</span>
            <h2 id="modalTitle">Añadir Proveedor</h2>
            <form id="proveedorForm" method="POST" onsubmit="return validarProveedorForm();">
                <input type="hidden" name="ID_Proveedor" id="modalID_Proveedor">
                <input type="hidden" name="action" id="modalAction" value="add_proveedor">
                <label for="modalNombre">Nombre</label>
                <input type="text" name="Nombre" id="modalNombre" required minlength="3" maxlength="80" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios">
                <label for="modalCorreo">Correo</label>
                <input type="email" name="Correo" id="modalCorreo" required maxlength="100" pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Correo válido">
                <label for="modalTelefono">Teléfono</label>
                <input type="text" name="Telefono" id="modalTelefono" required minlength="7" maxlength="9" pattern="^[0-9]{7,9}$" title="Solo números (7 a 9 dígitos)">
                <label for="modalDireccion">Dirección</label>
                <input type="text" name="Direccion" id="modalDireccion" required minlength="5" maxlength="120">
                <button type="submit" id="modalSubmitBtn">Guardar</button>
            </form>
        </div>
    </div>

    <script>
    function abrirModalAgregar() {
        document.getElementById('proveedorForm').reset();
        document.getElementById('modalTitle').innerText = "Añadir Proveedor";
        document.getElementById('modalAction').value = "add_proveedor";
        document.getElementById('modalID_Proveedor').value = "";
        document.getElementById('proveedorModal').style.display = "block";
    }
    function abrirModalEditar(id) {
        fetch('proveedores.php?action=load_edit_data&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const p = data.proveedor;
                    document.getElementById('modalTitle').innerText = "Editar Proveedor";
                    document.getElementById('modalAction').value = "edit_proveedor";
                    document.getElementById('modalID_Proveedor').value = p.ID_Proveedor;
                    document.getElementById('modalNombre').value = p.Nombre;
                    document.getElementById('modalCorreo').value = p.Correo;
                    document.getElementById('modalTelefono').value = p.Telefono;
                    document.getElementById('modalDireccion').value = p.Direccion;
                    document.getElementById('proveedorModal').style.display = "block";
                }
            });
    }
    function cerrarModal() {
        document.getElementById('proveedorModal').style.display = "none";
    }
    window.onclick = function(event) {
        const modal = document.getElementById('proveedorModal');
        if (event.target == modal) {
            cerrarModal();
        }
    }
    </script>

    <script>
    function inactivarProveedor(id) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Este proveedor será marcado como inactivo.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, inactivar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "proveedores.php?inactivate=" + id;
            }
        });
    }
    function activarProveedor(id) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Este proveedor será reactivado.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, activar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "proveedores.php?activate=" + id;
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

    <script>
    function validarProveedorForm() {
        const nombre = document.getElementById('modalNombre').value.trim();
        const correo = document.getElementById('modalCorreo').value.trim();
        const telefono = document.getElementById('modalTelefono').value.trim();
        const direccion = document.getElementById('modalDireccion').value.trim();

        if (nombre.length < 3 || nombre.length > 80 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) {
            Swal.fire('Error', 'El nombre debe tener entre 3 y 80 letras y solo puede contener letras y espacios.', 'error');
            return false;
        }
        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(correo) || correo.length > 100) {
            Swal.fire('Error', 'Ingrese un correo válido (máx 100 caracteres).', 'error');
            return false;
        }
        if (telefono.length < 7 || telefono.length > 9 || !/^[0-9]+$/.test(telefono)) {
            Swal.fire('Error', 'El teléfono debe tener entre 7 y 9 dígitos y solo puede contener números.', 'error');
            return false;
        }
        if (direccion.length < 5 || direccion.length > 120) {
            Swal.fire('Error', 'La dirección debe tener entre 5 y 120 caracteres.', 'error');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
