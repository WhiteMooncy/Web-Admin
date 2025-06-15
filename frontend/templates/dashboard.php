<?php
require_once '../../backend/php/conexion/check_role.php';

// A partir de aquí, las variables $user_role_name y $user_role_id ya están disponibles,
// y el usuario ya habrá sido redirigido si no está logueado.
// La conexión $conn también estará abierta y disponible si necesitas hacer más consultas.

// Opcional: Ahora podrías cerrar la conexión si no la usarás más en esta página.
// if (isset($conn)) {
//     $conn->close();
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../frontend/src/css/styleDashboard.css">
    <title>Cafetería | Dashboard</title>
</head>
<body>
    <div class="container-layout">
        <header>
            <h1>Bienvenido al Dashboard</h1>            
            <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($user_role_name)); ?> <?php echo htmlspecialchars($username); ?>!</h6>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../dashboard.php" class="active">Inicio</a></li>

                    <?php if ($user_role_name === 'admin'): // Funciones solo para administradores ?>
                        <li><a href="../templates/admin/manage_users.php">Usuarios</a></li>
                        <li><a href="../templates/admin/orders.php">Pedidos</a></li>
                        <li><a href="../templates/admin/products.php">Productos</a></li>
                        <li><a href="../templates/admin/reports.php">Reportes</a></li>
                        <li><a href="../templates/admin/settings.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <?php if ($user_role_name === 'empleado'): // Funciones para administradores y empleados ?>
                        <li><a href="../templates/admin/orders.php">Pedidos</a></li>
                        <li><a href="../templates/admin/products.php">Productos</a></li>
                        <li><a href="../templates/admin/reports.php">Reportes</a></li>
                        <li><a href="../templates/admin/settings.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <?php if ($user_role_name === 'cliente'): // Funciones solo para clientes ?>
                        <li><a href="../templates/admin/orders.php">Comprar</a></li>
                        <li><a href="../templates/admin/orders.php">Mis Pedidos</a></li>
                        <li><a href="profile.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <li><a href="../../backend/php/conexion/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Contenido Principal</h2>

            <?php if ($user_role_name === 'admin'): ?>
                <p>Eres un **Administrador**. Tienes acceso completo a todas las herramientas de gestión.</p>
            <?php elseif ($user_role_name === 'empleado'): ?>
                <p>Eres un **Empleado**. Puedes gestionar pedidos y ver información relevante para tu trabajo.</p>
                <p>Aquí podrías ver un resumen de los pedidos pendientes.</p>
            <?php elseif ($user_role_name === 'cliente'): ?>
                <p>Hola, **Cliente**. Aquí puedes ver tus pedidos recientes y actualizar tu información.</p>
                <p>¡Explora nuestros productos más populares!</p>
            <?php else: ?>
                <p>Tu rol no está definido o no es reconocido. Por favor, contacta al soporte.</p>
            <?php endif; ?>

            <p>Información general disponible para todos los usuarios con sesión iniciada.</p>

        </main>
        <footer>
            © 2025 - Cafetería
        </footer>
    </div>
</body>
</html>