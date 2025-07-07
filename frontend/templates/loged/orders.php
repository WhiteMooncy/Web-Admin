<?php
session_start();
require_once '../../../backend/php/conexion/db.php'; // Asume que db.php define $conn
require_once '../../../backend/php/conexion/check_role.php'; // Asume que check_role.php define $user_role_name, $user_id, $username

// Asume que tienes el ID y el rol del usuario en sesión
$user_id = $_SESSION['ID_Usuario'] ?? null; // Usar null coalesce para evitar warnings si no está set
$user_role_name = $_SESSION['user_role_name'] ?? '';
$username = $_SESSION['username'] ?? '';

// --- Lógica para determinar el orden de la tabla ---
// Por defecto, ordena por fecha descendente (más reciente)
$orderBy = isset($_GET['order_by']) ? $_GET['order_by'] : 'p.fecha DESC';

// Lista blanca de opciones de ordenamiento permitidas para prevenir inyección SQL
// Asegúrate de que los nombres de las columnas coincidan con tu base de datos
$allowedOrderBy = [
    'p.ID_Pedido ASC', 'p.ID_Pedido DESC',
    'p.fecha ASC', 'p.fecha DESC',
    'p.estado ASC', 'p.estado DESC',
    'u.username ASC', 'u.username DESC',
    'p.total ASC', 'p.total DESC'
];

// Valida que la opción de ordenamiento recibida esté en la lista blanca
if (!in_array($orderBy, $allowedOrderBy)) {
    $orderBy = 'p.fecha DESC'; // Si no es válida, usa el orden por defecto
}

// Consulta según el rol, aplicando el orden dinámico
if ($user_role_name === 'admin' || $user_role_name === 'empleado' || $user_role_name === 'trabajador') {
    $sql = "SELECT p.ID_Pedido, p.fecha, p.estado, u.username, p.total 
            FROM pedidos p 
            JOIN usuarios u ON p.ID_Usuario_FK = u.ID_Usuario
            ORDER BY $orderBy";
    $result = $conn->query($sql);
} else {
    // Solo pedidos del usuario actual
    $sql = "SELECT p.ID_Pedido, p.fecha, p.estado, u.username, p.total 
            FROM pedidos p 
            JOIN usuarios u ON p.ID_Usuario_FK = u.ID_Usuario
            WHERE u.ID_Usuario = ?
            ORDER BY $orderBy"; // Aplicar orden aquí también
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = false; // Indicar que hubo un error en la preparación
        error_log("Error al preparar la consulta de pedidos para cliente: " . $conn->error);
    }
}

// Manejo de mensajes SweetAlert desde la URL (si vienen de cambiar_estado_pedido.php u otra acción)
$alert_status = '';
$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'actualizado') {
        $alert_status = 'success';
        $message = 'Estado del pedido actualizado correctamente.';
    } elseif ($_GET['msg'] === 'error') {
        $alert_status = 'error';
        $message = 'Error al actualizar el estado del pedido.';
    }
    // Opcional: limpiar el parámetro msg de la URL para que no aparezca en recargas
    // Esto se hará en JavaScript
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | Cafetería</title>
    <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../src/js/logout-confirm.js"></script>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header>
            <h1>Pedidos</h1>
            <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($user_role_name)); ?> <?php echo htmlspecialchars($username); ?>! aquí podrás ver los pedidos realizados y su estado</h6>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../loged/dashboard.php">Inicio</a></li>

                    <?php if ($user_role_name === 'admin'): // Funciones solo para administradores ?>
                        <li><a href="../loged/manage_users.php">Usuarios</a></li>
                        <li><a href="../loged/orders.php" class="active">Pedidos</a></li>
                        <li><a href="../loged/products.php">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <?php if ($user_role_name === 'empleado'): ?>
                        <?php if ($user_role_name !== 'administrador'): // Evita duplicar si ya es admin ?>
                            <li><a href="../loged/orders.php" class="active">Pedidos</a></li>
                            <li><a href="../loged/products.php">Productos</a></li>
                            <li><a href="../loged/reports.php">Reportes</a></li>
                            <li><a href="../loged/profile.php">Mi Perfil</a></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($user_role_name === 'cliente'): // Funciones solo para clientes ?>
                        <li><a href="../carta.php">Comprar</a></li>
                        <li><a href="../loged/orders.php" class="active">Mis Pedidos</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h1>Listado de Pedidos</h1>
            <div class="sort-container">
                <label for="sortOrders">Ordenar por:</label>
                <select id="sortOrders" onchange="sortOrdersTable()">
                    <option value="p.fecha DESC">Fecha (Más reciente)</option>
                    <option value="p.fecha ASC">Fecha (Más antiguo)</option>
                    <option value="p.ID_Pedido ASC">ID Pedido (Ascendente)</option>
                    <option value="p.ID_Pedido DESC">ID Pedido (Descendente)</option>
                    <option value="p.estado ASC">Estado (A-Z)</option>
                    <option value="p.estado DESC">Estado (Z-A)</option>
                    <option value="u.username ASC">Usuario (A-Z)</option>
                    <option value="u.username DESC">Usuario (Z-A)</option>
                    <option value="p.total ASC">Total (Menor a Mayor)</option>
                    <option value="p.total DESC">Total (Mayor a Menor)</option>
                </select>
            </div>
            <!-- Fin del desplegable para ordenar -->

            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <?php if ($user_role_name === 'administrador' || $user_role_name === 'empleado' || $user_role_name === 'trabajador'): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_Pedido']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                <td>
                                    <?php
                                    $estado = strtolower($row['estado']);
                                    $clase = '';
                                    if ($estado == 'pendiente') $clase = 'pendiente';
                                    elseif ($estado == 'en preparacion') $clase = 'en preparacion';
                                    elseif ($estado == 'listo') $clase = 'listo';
                                    elseif ($estado == 'entregado') $clase = 'entregado';
                                    elseif ($estado == 'cancelado') $clase = 'cancelado';
                                    ?>
                                    <span class="estado <?php echo $clase; ?>">
                                        <?php echo ucfirst($estado); ?>
                                    </span>
                                </td>
                                <td>$<?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                <?php if ($user_role_name === 'admin' || $user_role_name === 'empleado' || $user_role_name === 'trabajador'): ?>
                                    <td>
                                        <form action="../../../backend/php/funcions/cambiar_estado_pedido.php" method="POST">
                                            <input type="hidden" name="ID_Pedido" value="<?php echo htmlspecialchars($row['ID_Pedido']); ?>">
                                            <select class="estado" name="estado">
                                                <option value="pendiente" <?php if($estado == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                                                <option value="en preparacion" <?php if($estado == 'en preparacion') echo 'selected'; ?>>En preparación</option>
                                                <option value="listo" <?php if($estado == 'listo') echo 'selected'; ?>>Listo</option>
                                                <option value="entregado" <?php if($estado == 'entregado') echo 'selected'; ?>>Entregado</option>
                                                <option value="cancelado" <?php if($estado == 'cancelado') echo 'selected'; ?>>Cancelado</option>
                                            </select>
                                            <button class="btn_cambio_estado" type="submit">Cambiar estado</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($user_role_name === 'administrador' || $user_role_name === 'empleado' || $user_role_name === 'trabajador') ? '6' : '5'; ?>">
                                No hay pedidos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
        <footer>
            © 2025 - Cafetería
        </footer>
    </div>

    <script>
        // --- Función para ordenar la tabla de pedidos ---
        function sortOrdersTable() {
            const orderBy = document.getElementById('sortOrders').value;
            // Obtener los parámetros actuales de la URL, excepto 'msg'
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('msg'); // Eliminar el mensaje de SweetAlert si existe

            // Construir la nueva URL con el parámetro 'order_by'
            urlParams.set('order_by', orderBy);
            window.location.href = `orders.php?${urlParams.toString()}`;
        }

        // --- Script para seleccionar la opción de ordenamiento actual al cargar la página ---
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const currentOrderBy = urlParams.get('order_by');
            if (currentOrderBy) {
                document.getElementById('sortOrders').value = currentOrderBy;
            }

            // --- Mostrar SweetAlert si hay mensajes en la URL después de la redirección ---
            const msg = urlParams.get('msg');
            if (msg) {
                let icon = '';
                let title = '';
                let text = '';

                if (msg === 'actualizado') {
                    icon = 'success';
                    title = '¡Éxito!';
                    text = 'Estado del pedido actualizado correctamente.';
                } else if (msg === 'error') {
                    icon = 'error';
                    title = 'Error';
                    text = 'Error al actualizar el estado del pedido.';
                }

                if (icon && title && text) {
                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: text,
                        confirmButtonColor: (icon === 'success' ? '#3085d6' : '#d33')
                    }).then(() => {
                        // Limpiar el parámetro 'msg' de la URL después de mostrar la alerta
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.delete('msg');
                        history.replaceState({}, document.title, newUrl.toString());
                    });
                }
            }
        });
    </script>
</body>
</html>
<?php
// Asegurarse de liberar el resultado y cerrar la conexión al final del script
if (isset($result) && $result instanceof mysqli_result) {
    $result->free();
}
if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
    $conn->close();
}
?>
