<?php
session_start();
require_once '../../../backend/php/conexion/db.php';
require_once '../../../backend/php/conexion/check_role.php';

// Asume que tienes el ID y el rol del usuario en sesión
$user_id = $_SESSION['ID_Usuario'];
$user_role_name = $_SESSION['user_role_name'] ?? '';
$username = $_SESSION['username'] ?? '';

// Consulta según el rol
if ($user_role_name === 'admin' || $user_role_name === 'empleado' || $user_role_name === 'trabajador') {
    $sql = "SELECT p.ID_Pedido, p.fecha, p.estado, u.username, p.total 
            FROM pedidos p 
            JOIN usuarios u ON p.ID_Usuario_FK = u.ID_Usuario
            ORDER BY p.fecha DESC";
    $result = $conn->query($sql);
} else {
    // Solo pedidos del usuario actual
    $sql = "SELECT p.ID_Pedido, p.fecha, p.estado, u.username, p.total 
            FROM pedidos p 
            JOIN usuarios u ON p.ID_Usuario_FK = u.ID_Usuario
            WHERE u.ID_Usuario = ?
            ORDER BY p.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
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
                <h1>Listado de Pedidos</h1>
                <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($user_role_name)); ?> <?php echo htmlspecialchars($username); ?>! aqui podras ver los pedidos realizados y su estado</h6>
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

                        <?php if ($user_role_name === 'empleado'): // Funciones para administradores y empleados ?>
                            <li><a href="../loged/orders.php" class="active">Pedidos</a></li>
                            <li><a href="../loged/products.php">Productos</a></li>
                            <li><a href="../loged/reports.php">Reportes</a></li>
                            <li><a href="../loged/profile.php">Mi Perfil</a></li>
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
                <div class="pedidos-container">
                    <h1>Listado de Pedidos</h1>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <?php if ($user_role_name === 'admin' || $user_role_name === 'empleado'): ?>
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
                                            elseif ($estado == 'completado') $clase = 'completado';
                                            elseif ($estado == 'cancelado') $clase = 'cancelado';
                                            ?>
                                            <span class="estado <?php echo $clase; ?>">
                                                <?php echo ucfirst($estado); ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                        <?php if ($user_role_name === 'admin' || $user_role_name === 'empleado'): ?>
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
                                                    <button type="submit">Cambiar estado</button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No hay pedidos registrados.</td>
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
        <?php if (isset($_GET['msg'])): ?>
            <script>
                <?php if ($_GET['msg'] === 'actualizado'): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Estado actualizado',
                    confirmButtonColor: '#3085d6'
                });
                <?php elseif ($_GET['msg'] === 'error'): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar el estado',
                    confirmButtonColor: '#d33'
                });
                <?php endif; ?>
                
            </script>
        <?php endif; ?>
    </body>
</html>
<?php
if (isset($result) && $result instanceof mysqli_result) {
    $result->free();
}
$conn->close();
?>