<?php
require_once '../../../backend/php/conexion/db.php';
require_once '../../../backend/php/conexion/check_role.php';
if (!isset($conn) || !$conn instanceof mysqli) {
    die("Error crítico: No se pudo establecer conexión con la base de datos.");
}
$queryPedidosMes = $conn->query("
    SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total
    FROM pedidos
    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes
    ORDER BY mes ASC
");
$meses = [];
$totales = [];
if ($queryPedidosMes) {
    while ($row = $queryPedidosMes->fetch_assoc()) {
        $meses[] = $row['mes'];
        $totales[] = $row['total'];
    }
} else {
    error_log("Error en la consulta de Pedidos por Mes: " . $conn->error);
}
// Total de usuarios
$queryUsuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$totalUsuarios = ($queryUsuarios && $queryUsuarios->num_rows > 0) ? $queryUsuarios->fetch_assoc()['total'] : 0;
// Total de pedidos
$queryPedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos");
$totalPedidos = ($queryPedidos && $queryPedidos->num_rows > 0) ? $queryPedidos->fetch_assoc()['total'] : 0;
// Ingresos totales
$queryIngresos = $conn->query("SELECT SUM(total) as total FROM pedidos");
$ingresosTotales = ($queryIngresos && $queryIngresos->num_rows > 0) ? $queryIngresos->fetch_assoc()['total'] : 0;
// Ticket promedio
$ticketPromedio = $totalPedidos > 0 ? round($ingresosTotales / $totalPedidos, 0) : 0;
// Producto más vendido
$queryMasVendido = $conn->query("SELECT
    p.Producto,
        SUM(dp.cantidad) AS total_vendido
    FROM
        productos p
    JOIN
        detalle_pedidos dp ON p.ID_Producto = dp.ID_Producto_FK
    GROUP BY
        p.Producto
    ORDER BY
        total_vendido DESC
    LIMIT 1;");
$productoMasVendido = ($queryMasVendido && $queryMasVendido->num_rows > 0) ? $queryMasVendido->fetch_assoc()['Producto'] : 'N/A';
if (!$queryMasVendido) {
    error_log("Error en la consulta de Producto más vendido (columna 'vendidos' podría no existir): " . $conn->error);
    $productoMasVendido = 'Error de consulta';
}
// Producto con menor stock
$queryMenorStock = $conn->query("SELECT Producto, Stock FROM productos ORDER BY Stock ASC LIMIT 1");
$productoStock = ($queryMenorStock && $queryMenorStock->num_rows > 0) ? $queryMenorStock->fetch_assoc() : null;
$productoBajoStock = $productoStock['Producto'] ?? 'N/A';
$stockBajo = $productoStock['Stock'] ?? 0;
if (!$queryMenorStock) {
    error_log("Error en la consulta de Producto con menor stock: " . $conn->error);
    $productoBajoStock = 'Error de consulta';
    $stockBajo = 'N/A';
}
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
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
        body {
            background-color: #f5f6fa;
            font-family: 'Poppins', sans-serif; /* Usar la fuente Poppins */
        }
        .container {
            max-width: 1200px; /* Limitar el ancho del contenedor principal */
        }
        .report-card {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            transition: transform 0.2s ease-in-out; /* Efecto hover */
        }
        .report-card:hover {
            transform: translateY(-5px); /* Eleva la tarjeta al pasar el mouse */
        }
        .report-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 10px;
        }
        .report-value {
            font-size: 2.5rem; /* Un poco más grande */
            font-weight: bold;
            color: #0d6efd; /* Azul de Bootstrap */
            display: block; /* Asegura que ocupe su propia línea */
            margin-top: 5px;
        }
        .section-title {
            font-size: 1.8rem; /* Más grande */
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 25px; /* Más espacio */
            border-bottom: 3px solid #0d6efd; /* Borde más grueso */
            padding-bottom: 8px; /* Más padding */
            color: #0d6efd;
            text-align: center; /* Centrar el título de la sección */
        }
        .text-center {
            text-align: center;
        }
        .mb-5 {
            margin-bottom: 3rem !important;
        }
        .mt-5 {
            margin-top: 3rem !important;
        }
        canvas {
            max-width: 100%; /* Asegura que el gráfico sea responsivo */
            height: auto;
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
                <div class="container mt-5">
                    <h1 class="text-center text-primary mb-5">📊 Reportes del Sistema</h1>

                    <!-- Reportes Generales -->
                    <div class="section-title">Reportes Generales</div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3"><div class="report-card text-center">
                            <div class="report-title">Total de usuarios registrados</div>
                            <div class="report-value"><?= htmlspecialchars($totalUsuarios) ?></div>
                        </div></div>
                        <div class="col-md-6 col-lg-3"><div class="report-card text-center">
                            <div class="report-title">Total de pedidos realizados</div>
                            <div class="report-value"><?= htmlspecialchars($totalPedidos) ?></div>
                        </div></div>
                        <div class="col-md-6 col-lg-3"><div class="report-card text-center">
                            <div class="report-title">Ingresos totales</div>
                            <div class="report-value">$<?= number_format($ingresosTotales, 0, ',', '.') ?></div>
                        </div></div>
                        <div class="col-md-6 col-lg-3"><div class="report-card text-center">
                            <div class="report-title">Ticket promedio</div>
                            <div class="report-value">$<?= number_format($ticketPromedio, 0, ',', '.') ?></div>
                        </div></div>
                    </div>

                    <!-- Productos -->
                    <div class="section-title">Productos</div>
                    <div class="row">
                        <div class="col-md-6"><div class="report-card text-center">
                            <div class="report-title">Producto más vendido</div>
                            <div class="report-value"><?= htmlspecialchars($productoMasVendido) ?></div>
                        </div></div>
                        <div class="col-md-6"><div class="report-card text-center">
                            <div class="report-title">Producto con stock más bajo</div>
                            <div class="report-value"><?= htmlspecialchars($productoBajoStock) ?> (<?= htmlspecialchars($stockBajo) ?> unidades)</div>
                        </div></div>
                    </div>

                    <!-- Gráfico de Pedidos por Mes -->
                    <div class="section-title">Pedidos por Mes (Últimos 6 Meses)</div>
                    <div class="report-card">
                        <canvas id="graficoPedidos"></canvas>
                    </div>
                </div>               
            </main>
            <footer>
                © 2025 - Cafetería
            </footer>
        </div>
    </body>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('graficoPedidos').getContext('2d');
            const graficoPedidos = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($meses) ?>,
                    datasets: [{
                        label: 'Número de Pedidos',
                        data: <?= json_encode($totales) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)', /* Un azul con algo de transparencia */
                        borderColor: '#0d6efd',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, /* Permite controlar el tamaño del canvas con CSS */
                    plugins: {
                        title: {
                            display: true,
                            text: 'Pedidos Realizados por Mes',
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: '#333'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 /* Asegura números enteros para el conteo de pedidos */
                            },
                            title: {
                                display: true,
                                text: 'Cantidad de Pedidos',
                                color: '#555'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes',
                                color: '#555'
                            }
                        }
                    }
                }
            });

            // Ajustar el tamaño del canvas para que sea responsivo
            function resizeCanvas() {
                const canvasContainer = document.querySelector('.report-card');
                const canvas = document.getElementById('graficoPedidos');
                if (canvasContainer && canvas) {
                    canvas.style.width = '100%';
                    canvas.style.height = '400px'; // Altura fija o ajusta según necesidad
                    graficoPedidos.resize(); // Forzar redibujado de Chart.js
                }
            }

            // Llamar al redimensionamiento al cargar y al cambiar el tamaño de la ventana
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
        });
    </script>
</html>