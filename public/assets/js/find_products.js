// Este archivo es para la funcionalidad de búsqueda de productos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchProducts'); // ID del input de búsqueda en products.php
    const stockTable = document.getElementById('stockTable');     // ID de la tabla de productos en products.php
    const tableBody = stockTable ? stockTable.querySelector('tbody') : null;

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase(); // Texto de búsqueda en minúsculas
            const rows = tableBody.querySelectorAll('tr');      // Todas las filas de la tabla

            rows.forEach(row => {
                let rowText = '';
                // Asume que quieres buscar en las columnas 1 (Producto) y 2 (Categoría), y quizás 3 (Precio) y 4 (Stock), 5 (Estado)
                // Ajusta los índices [1], [2], etc., según las columnas donde quieras buscar.
                // Recuerda que row.cells[0] es la columna ID, row.cells[1] es Producto, etc.
                // La columna 6 es 'Acciones', que probablemente no quieres buscar.
                rowText += row.cells[1].textContent.toLowerCase() + ' '; // Producto
                rowText += row.cells[2].textContent.toLowerCase() + ' '; // Categoría
                // Puedes añadir más si quieres buscar en precio, stock, estado:
                rowText += row.cells[3].textContent.toLowerCase() + ' '; // Precio
                rowText += row.cells[4].textContent.toLowerCase() + ' '; // Stock
                rowText += row.cells[5].textContent.toLowerCase() + ' '; // Estado

                if (rowText.includes(searchTerm)) {
                    row.style.display = ''; // Mostrar fila
                } else {
                    row.style.display = 'none'; // Ocultar fila
                }
            });
        });
    }

    // --- Lógica para la Alerta de Stock Bajo (si aplica a productos.php) ---
    function checkLowStock() {
        const rows = tableBody ? tableBody.querySelectorAll('tr') : [];
        let lowStockCount = 0;
        const lowStockThreshold = 5; // Define tu umbral de stock bajo aquí

        rows.forEach(row => {
            // Asumiendo que la columna de Stock es la 4ª (índice 4)
            const stockCell = row.cells[4];
            if (stockCell) {
                const stock = parseInt(stockCell.textContent);
                if (!isNaN(stock) && stock <= lowStockThreshold) {
                    lowStockCount++;
                    row.classList.add('low-stock-row'); // Añadir una clase CSS para destacar
                } else {
                    row.classList.remove('low-stock-row');
                }
            }
        });

        const lowStockAlert = document.getElementById('lowStockAlert');
        const lowStockCountSpan = document.getElementById('lowStockCount');

        if (lowStockAlert && lowStockCountSpan) {
            if (lowStockCount > 0) {
                lowStockCountSpan.textContent = lowStockCount;
                lowStockAlert.style.display = 'block';
            } else {
                lowStockAlert.style.display = 'none';
            }
        }
    }

    // Llama a la función de verificación de stock al cargar la página
    checkLowStock();

    // Las funciones `showProductModal`, `closeProductModal`, `saveProduct`, `editProduct`, `deleteProduct`
    // también deberían estar en este archivo (o un archivo separado para modales de productos)
    // si las estás llamando desde buttons directamente en products.php.
    // Incluyo las definiciones de esas funciones que te di en la respuesta anterior:

    // --- Funciones para el modal de productos ---
    function showProductModal(productId = null) {
        const modal = document.getElementById('productModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('productForm');
        form.reset(); 

        if (productId) {
            modalTitle.textContent = 'Editar Producto';
            document.getElementById('productId').value = productId;
            fetch(`../../../backend/php/productos/get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('productName').value = data.product.Producto;
                        document.getElementById('productCategory').value = data.product.Categoria;
                        document.getElementById('productPrice').value = data.product.Precio_Unitario;
                        document.getElementById('productStock').value = data.product.Stock;
                        document.getElementById('productStatus').value = data.product.Estado == 1 ? 'Activo' : 'Inactivo';
                    } else {
                        Swal.fire('Error', 'No se pudieron cargar los datos del producto.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al cargar producto:', error);
                    Swal.fire('Error', 'Hubo un problema al cargar los datos del producto para editar.', 'error');
                });
        } else {
            modalTitle.textContent = 'Agregar Producto';
            document.getElementById('productId').value = ''; 
        }
        modal.style.display = 'block';
    }

    function closeProductModal() {
        const modal = document.getElementById('productModal');
        modal.style.display = 'none';
    }

    function saveProduct(event) {
        event.preventDefault();

        const productId = document.getElementById('productId').value;
        const productName = document.getElementById('productName').value;
        const productCategory = document.getElementById('productCategory').value;
        const productPrice = document.getElementById('productPrice').value;
        const productStock = document.getElementById('productStock').value;
        const productStatus = document.getElementById('productStatus').value === 'Activo' ? 1 : 0; 

        const formData = new FormData();
        formData.append('ID_Producto', productId); 
        formData.append('Producto', productName);
        formData.append('Categoria', productCategory);
        formData.append('Precio_Unitario', productPrice);
        formData.append('Stock', productStock);
        formData.append('Estado', productStatus);

        const url = productId ? '../../../backend/php/productos/update_product.php' : '../../../backend/php/productos/add_product.php';

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema al guardar el producto.', 'error');
        });
    }

    function editProduct(productId) {
        showProductModal(productId); 
    }

    function deleteProduct(productId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`../../../backend/php/productos/delete_product.php?id=${productId}`, {
                    method: 'GET' 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El producto ha sido eliminado.',
                            'success'
                        ).then(() => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar:', error);
                    Swal.fire(
                        'Error',
                        'Hubo un problema al eliminar el producto.',
                        'error'
                    );
                });
            }
        });
    }

});