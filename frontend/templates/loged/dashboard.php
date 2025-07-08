<?php
require_once '../../../backend/php/conexion/check_role.php';
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
    <title>Cafetería | Dashboard</title>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header class="header">
            <h1>Bienvenido al Dashboard</h1>            
            <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($user_role_name)); ?> <?php echo htmlspecialchars($username); ?>!</h6>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../loged/dashboard.php" class="active">Inicio</a></li>
                    <?php if ($user_role_name === 'admin'): ?>
                        <li><a href="../loged/manage_users.php">Usuarios</a></li>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/products.php">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <?php if ($user_role_name === 'empleado'): ?>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/products.php">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <?php if ($user_role_name === 'cliente'): ?>
                        <li><a href="../carta.php">Comprar</a></li>
                        <li><a href="../loged/orders.php">Mis Pedidos</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
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