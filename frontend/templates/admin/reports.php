<?php
session_start();
require_once '../../../backend/php/conexion/db.php';

// Ejemplo: obtener cantidad de usuarios y pedidos
$usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc();
$pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Cafetería</title>
    <link rel="stylesheet" href="../../../frontend/src/css/styleDashboard.css">
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
<body>
    <div class="reportes-container">
        <h1>Reportes del Sistema</h1>
        <div class="reporte-card">
            <div>Total de usuarios registrados</div>
            <span><?php echo $usuarios['total']; ?></span>
        </div>
        <div class="reporte-card">
            <div>Total de pedidos realizados</div>
            <span><?php echo $pedidos['total']; ?></span>
        </div>
        <!-- Puedes agregar más reportes aquí -->
    </div>
</body>
</html>