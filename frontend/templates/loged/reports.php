<?php
require_once '../../../backend/php/conexion/check_role.php';

// Ejemplo: obtener cantidad de usuarios y pedidos
$usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc();
$pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reportes | Cafetería</title>
        <link rel="stylesheet" href="../../src/css/styleDashboard.css?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../src/js/logout-confirm.js"></script>
        <style>
            .reportes-container {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                padding: 32px 40px;
                margin: 40px auto;
                max-width: 700px;
            }
            .reporte-card {
                background: #f4f6fa;
                border-radius: 8px;
                padding: 24px 32px;
                margin-bottom: 24px;
                box-shadow: 0 1px 4px rgba(17,116,216,0.08);
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 1.2rem;
            }
            .reporte-card span {
                font-weight: bold;
                color: #1174d8;
                font-size: 1.5rem;
            }
            h1 {
                text-align: center;
                color: #1174d8;
                margin-bottom: 32px;
            }
            @media (max-width: 700px) {
                .reportes-container { padding: 16px 8px; }
                .reporte-card { flex-direction: column; align-items: flex-start; }
            }
        </style>
    </head>
    <body id="dash-board">
        <div class="container-layout">
            <header>
                <h1>Reportes del Sistema</h1>            
                <h6>¡Bienvenido <?php echo ucfirst(htmlspecialchars($user_role_name)); ?> <?php echo htmlspecialchars($username); ?>!</h6>
            </header>
            <aside>
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="../loged/dashboard.php">Inicio</a></li>
                        <?php if ($user_role_name === 'admin'): // Funciones solo para administradores ?>
                            <li><a href="../loged/manage_users.php">Usuarios</a></li>
                            <li><a href="../loged/orders.php">Pedidos</a></li>
                            <li><a href="../loged/products.php">Productos</a></li>
                            <li><a href="../loged/reports.php" class="active">Reportes</a></li>
                            <li><a href="../loged/profile.php">Mi Perfil</a></li>
                        <?php endif; ?>
                        <?php if ($user_role_name === 'empleado'): // Funciones para administradores y empleados ?>
                            <li><a href="../loged/orders.php">Pedidos</a></li>
                            <li><a href="../loged/products.php">Productos</a></li>
                            <li><a href="../loged/reports.php" class="active">Reportes</a></li>
                            <li><a href="../loged/profile.php">Mi Perfil</a></li>
                        <?php endif; ?>
                        <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </aside>
            <main>
                <h1>Reportes Generales</h1>
                <div class="reportes-container">
                    <div class="reporte-card">
                    <div>Total de usuarios registrados</div>
                        <span><?php echo $usuarios['total']; ?></span>
                    </div>
                        <div class="reporte-card">
                            <div>Total de pedidos realizados</div>
                            <span><?php echo $pedidos['total']; ?></span>
                        </div>    
                </div>
            </main>
            <footer>
                © 2025 - Cafetería
            </footer>
        </div>
    </body>
</html>