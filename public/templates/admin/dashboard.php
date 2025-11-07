<?php
/**
 * Dashboard Administrativo
 */

define('APP_ROOT', dirname(__DIR__, 2));
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';

// Requiere autenticación
requireAuth();

$currentUser = getCurrentUser();
$username = $currentUser['username'];
$userRole = $currentUser['role'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styleDashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/logout-confirm.js"></script>
    <title><?php echo APP_NAME; ?> | Dashboard</title>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header class="header">
            <h1>Bienvenido al Dashboard</h1>            
            <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($userRole)); ?> <?php echo htmlspecialchars($username); ?>!</h6>
        </header>
        
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/templates/admin/dashboard.php" class="active">Inicio</a></li>
                    
                    <?php if (hasRole('administrador')): ?>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/manage_users.php">Usuarios</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/orders.php">Pedidos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/proveedores.php">Proveedores</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/products.php">Productos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/reports.php">Reportes</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    
                    <?php if (hasRole('empleado')): ?>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/orders.php">Pedidos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/proveedores.php">Proveedores</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/products.php">Productos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/reports.php">Reportes</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    
                    <?php if (hasRole('cliente')): ?>
                        <li><a href="<?php echo BASE_URL; ?>/templates/carta.php">Comprar</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/orders.php">Mis Pedidos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/templates/admin/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    
                    <li><a href="<?php echo BASE_URL; ?>/templates/logout.php" id="logout-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        
        <main>
            <h2>Contenido Principal</h2>
            
            <?php if (hasRole('administrador')): ?>
                <div class="welcome-card">
                    <h3>Panel de Administrador</h3>
                    <p>Tienes acceso completo a todas las herramientas de gestión del sistema.</p>
                    <div class="quick-stats">
                        <div class="stat-box">
                            <h4>Usuarios</h4>
                            <p class="stat-number" id="total-users">-</p>
                        </div>
                        <div class="stat-box">
                            <h4>Pedidos Hoy</h4>
                            <p class="stat-number" id="orders-today">-</p>
                        </div>
                        <div class="stat-box">
                            <h4>Productos</h4>
                            <p class="stat-number" id="total-products">-</p>
                        </div>
                    </div>
                </div>
            <?php elseif (hasRole('empleado')): ?>
                <div class="welcome-card">
                    <h3>Panel de Empleado</h3>
                    <p>Puedes gestionar pedidos, productos y proveedores.</p>
                    <p>Aquí podrías ver un resumen de los pedidos pendientes.</p>
                </div>
            <?php elseif (hasRole('cliente')): ?>
                <div class="welcome-card">
                    <h3>Bienvenido</h3>
                    <p>Aquí puedes ver tus pedidos recientes y actualizar tu información.</p>
                    <p>¡Explora nuestros productos más populares!</p>
                    <a href="<?php echo BASE_URL; ?>/templates/carta.php" class="btn btn-primary">Ver Menú</a>
                </div>
            <?php else: ?>
                <div class="welcome-card alert-warning">
                    <p>Tu rol no está definido o no es reconocido. Por favor, contacta al soporte.</p>
                </div>
            <?php endif; ?>
            
            <div class="info-section">
                <h3>Información del Sistema</h3>
                <p>Versión: <?php echo APP_VERSION; ?></p>
                <p>Entorno: <?php echo ENVIRONMENT; ?></p>
            </div>
        </main>
        
        <footer>
            © <?php echo date('Y'); ?> - <?php echo APP_NAME; ?>
        </footer>
    </div>
    
    <script>
        // Cargar estadísticas si es administrador
        <?php if (hasRole('administrador')): ?>
        fetch('<?php echo BASE_URL; ?>/src/controllers/stats.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total-users').textContent = data.stats.users || '-';
                    document.getElementById('orders-today').textContent = data.stats.ordersToday || '-';
                    document.getElementById('total-products').textContent = data.stats.products || '-';
                }
            })
            .catch(err => console.error('Error loading stats:', err));
        <?php endif; ?>
    </script>
</body>
</html>
