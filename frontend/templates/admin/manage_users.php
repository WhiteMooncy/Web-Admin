<?php
require_once '../../../backend/php/conexion/db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../frontend/src/css/styleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Cafetería | Administración de Usuarios</title>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header>
            <h1>Administración de Usuarios</h1>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../dashboard.php">Inicio</a></li>
                    <li><a href="../admin/manage_users.php" class="active">Usuarios</a></li>
                    <li><a href="../admin/orders.php">Pedidos</a></li>
                    <li><a href="../admin/products.php">Productos</a></li>
                    <li><a href="../admin/reports.php">Reportes</a></li>
                    <li><a href="../admin/settings.php">Perfil</a></li>
                    <li><a href="../../../backend/php/conexion/logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Gestión de Usuarios</h2>
            <button class="btn-add-user" onclick="location.href='add_user.php'">Agregar Usuario</button>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Validar que $conn esté disponible de db.php
                    if (isset($conn) && $conn instanceof mysqli) {
                        $sql = "SELECT ID_Usuario, username, correo, ID_Rol_FK FROM usuarios";
                        $result = $conn->query($sql);

                        if ($result) { // Verificar si la consulta fue exitosa
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['ID_Usuario']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['ID_Rol_FK']) . "</td>";
                                    echo "<td>";
                                    // Los botones de editar y eliminar deberían enviar a scripts PHP con el ID del usuario
                                    echo "<button class='btn-edit' onclick=\"location.href='edit_user.php?id=" . htmlspecialchars($row['ID_Usuario']) . "'\">Editar</button>";
                                    echo "<button class='btn-delete' onclick=\"if(confirm('¿Estás seguro de que quieres eliminar a este usuario?')) location.href='delete_user.php?id=" . htmlspecialchars($row['ID_Usuario']) . "'\">Eliminar</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Error al cargar usuarios: " . $conn->error . "</td></tr>";
                        }
                        $conn->close(); // Cerrar la conexión después de usarla
                    } else {
                        echo "<tr><td colspan='5'>Error: La conexión a la base de datos no está disponible.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
        <footer>
            © 2025 - Administración de Usuarios | Cafetería
        </footer>
    </div>
</body>
</html>