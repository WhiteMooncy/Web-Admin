<?php
session_start();
require_once '../../../backend/php/conexion/db.php';
require_once '../../../backend/php/conexion/check_role.php';
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$usuario_id = $_SESSION['ID_Usuario'] ?? 0;
$sql = "SELECT username, Correo, Telefono, ID_Rol_FK, nombre, apellido FROM usuarios WHERE ID_Usuario = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en prepare: " . $conn->error);
}
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($username, $correo, $telefono, $id_rol_fk, $nombre, $apellido);
$stmt->fetch();
$stmt->close();

// Si tienes una tabla de roles, puedes obtener el nombre del rol así:
$rol = '';
if ($id_rol_fk) {
    $sql_rol = "SELECT Nombre_Rol FROM roles WHERE ID_Rol = ?";
    $stmt_rol = $conn->prepare($sql_rol);
    $stmt_rol->bind_param("i", $id_rol_fk);
    $stmt_rol->execute();
    $stmt_rol->bind_result($rol);
    $stmt_rol->fetch();
    $stmt_rol->close();
}
$foto = '../../src/icons/profile.jpg';
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
      <header>
        <h2>Mi Perfil</h2>            
      </header>
      <aside>
        <nav class="sidebar-nav">
          <ul>
            <li><a href="../loged/dashboard.php">Inicio</a></li>
              <?php if ($user_role_name === 'admin'): // Funciones solo para administradores ?>
                <li><a href="../loged/manage_users.php">Usuarios</a></li>
                <li><a href="../loged/orders.php">Pedidos</a></li>
                <li><a href="../loged/products.php">Productos</a></li>
                <li><a href="../loged/reports.php">Reportes</a></li>
                <li><a href="../loged/profile.php" class="active">Mi Perfil</a></li>
              <?php endif; ?>
              <?php if ($user_role_name === 'empleado'): // Funciones para administradores y empleados ?>
                <li><a href="../loged/orders.php">Pedidos</a></li>
                <li><a href="../loged/products.php">Productos</a></li>
                <li><a href="../loged/reports.php">Reportes</a></li>
                <li><a href="../loged/profile.php" class="active">Mi Perfil</a></li>
              <?php endif; ?>
              <?php if ($user_role_name === 'cliente'): // Funciones solo para clientes ?>
                <li><a href="../carta.php">Comprar</a></li>
                <li><a href="../loged/orders.php">Mis Pedidos</a></li>
                <li><a href="../loged/profile.php" class="active">Mi Perfil</a></li>
              <?php endif; ?>
            <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
          </ul>
        </nav>
      </aside>
      <main>
        <section class="profile-card" style="max-width:350px;margin:auto;padding:2rem;border-radius:10px;box-shadow:0 2px 8px #ccc;text-align:center;">
          <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin-bottom:1rem;">
          <div id="profile-data">
            <h2 id="username"><?php echo htmlspecialchars($username); ?></h2>
            <p id="nombre">Nombre: <?php echo htmlspecialchars($nombre); ?></p>
            <p id="apellido">Apellido: <?php echo htmlspecialchars($apellido); ?></p>
            <p id="rol">Rol: <?php echo htmlspecialchars($rol); ?></p>
            <p id="telefono">Teléfono: <?php echo htmlspecialchars($telefono); ?></p>
            <p id="correo">Correo: <?php echo htmlspecialchars($correo); ?></p>
          </div>
            <form id="edit-form" style="display:none;" method="POST" action="../../../backend/php/funcions/save_pf.php">
              <input type="hidden" name="usuario_id" value="<?php echo isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0; ?>">
              <input type="text" id="edit-username" name="username" value="<?php echo htmlspecialchars($username); ?>" required placeholder="Usuario ENSI" readonly><br>
              <input type="text" id="edit-nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required placeholder="Nombre"><br>
              <input type="text" id="edit-apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required placeholder="Apellido"><br>
              <!-- El campo de rol solo se muestra como texto, no editable -->
              <input type="hidden" name="rol" value="<?php echo htmlspecialchars($rol); ?>">
              <input type="tel" id="edit-telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required placeholder="Teléfono"><br>
              <input type="email" id="edit-correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required placeholder="Correo"><br>
              <button type="submit">Guardar</button>
            </form>
              <button id="edit-btn" onclick="toggleEdit();return false;">Editar</button>
        </section>
        <script>
          function toggleEdit() {
            const data = document.getElementById('profile-data');
            const form = document.getElementById('edit-form');
            const btn = document.getElementById('edit-btn');
            if (form.style.display === 'none') {
              // Mostrar formulario para editar
              form.style.display = 'block';
              data.style.display = 'none';
              btn.textContent = 'Cancelar';
            } else {
              // Ocultar formulario y mostrar datos
              form.style.display = 'none';
              data.style.display = 'block';
              btn.textContent = 'Editar';
            }
          }
        </script>
      </main>
      <footer>
        © 2025 - Cafetería
      </footer>
    </div>
  </body>
</html>