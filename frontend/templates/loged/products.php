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
    <title>Cafetería | Administración de Productos</title>
</head>
<body id="dash-board">
    <div class="container-layout">
        <header>
            <h1>Gestión de Productos</h1>
        </header>
        <aside>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../loged/dashboard.php">Inicio</a></li>

                    <?php if ($user_role_name === 'admin'): // Funciones solo para administradores ?>
                        <li><a href="../loged/manage_users.php">Usuarios</a></li>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/products.php" class="active">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>

                    <?php if ($user_role_name === 'empleado'): // Funciones para administradores y empleados ?>
                        <li><a href="../loged/orders.php">Pedidos</a></li>
                        <li><a href="../loged/products.php" class="active">Productos</a></li>
                        <li><a href="../loged/reports.php">Reportes</a></li>
                        <li><a href="../loged/profile.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="../../../backend/php/conexion/logout.php" id="logout-link">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <input type="text" class="form-control" id="searchProducts" placeholder="Buscar productos..." style="margin-bottom: 16px; max-width: 350px;">
            <button class="btn-add-product" onclick="showProductModal()">Agregar Producto</button>
            <div class="alert alert-warning" id="lowStockAlert" style="display: none; margin: 16px 0;">
                Alerta: <span id="lowStockCount">0</span> productos tienen stock bajo.
            </div>
            <div class="data-table-card">
                <table class="users-table" id="stockTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio (CLP)</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT ID_Producto, Producto, Categoria, Precio_Unitario, Stock, Estado FROM productos";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0):
                            while($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_Producto']); ?></td>
                                <td><?php echo htmlspecialchars($row['Producto']); ?></td>
                                <td><?php echo htmlspecialchars($row['Categoria']); ?></td>
                                <td><?php echo number_format($row['Precio_Unitario']); ?></td>
                                <td><?php echo htmlspecialchars($row['Stock']); ?></td>
                                <td>
                                    <?php echo $row['Estado'] ? 'Activo' : 'Inactivo'; ?>
                                </td>
                                <td>
                                    <!-- Aquí puedes poner botones para editar/eliminar con JS o formularios -->
                                    <button class="btn-edit" onclick="editProduct(<?php echo $row['ID_Producto']; ?>)">Editar</button>
                                    <button class="btn-delete" onclick="deleteProduct(<?php echo $row['ID_Producto']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="7">No hay productos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <footer>
            © 2025 - Administración de Productos | Cafetería
        </footer>
    </div>
    <div class="modal" id="productModal">
        <div class="modal-dialog">
            <form class="modal-content" id="productForm" onsubmit="saveProduct(event)">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Producto</h5>
                    <button type="button" class="btn-close" onclick="closeProductModal()" aria-label="Cerrar">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="productId">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Producto</label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="productCategory" required>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Precio (CLP)</label>
                        <input type="number" class="form-control" id="productPrice" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="productStock" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="productStatus" class="form-label">Estado</label>
                        <select class="form-select" id="productStatus" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-success">Guardar</button>
                    <button type="button" class="btn-secondary" onclick="closeProductModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function saveProduct(event) {
            event.preventDefault();
            const productData = {
                name: document.getElementById('productName').value,
                category: document.getElementById('productCategory').value,
                price: parseFloat(document.getElementById('productPrice').value),
                stock: parseInt(document.getElementById('productStock').value),
                status: document.getElementById('productStatus').value
            };
            fetch('../../../backend/php/funcions/product_add.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Producto agregado', 'Se ha guardado correctamente', 'success');
                    document.getElementById('productForm').reset();
                    closeProductModal();
                    location.reload(); // recargar la tabla
                } else {
                    Swal.fire('Error', data.message || 'No se pudo guardar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema con la conexión', 'error');
            });
        }
    </script>
    <script src="../../src/js/find_products.js"></script>
</body>
</html>