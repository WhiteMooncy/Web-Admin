<?php
session_start();
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
// Asumiendo que 'IS_Usuario' es la variable de sesión que estableces cuando un usuario inicia sesión.
$user_logged_in = isset($_SESSION['IS_Usuario']); 

$login_url = '../templates/loged/form_login.php';
$checkout_url = '../templates/loged/checkout.php'; // Asegúrate de que esta ruta sea correcta
$mi_cuenta_url = '../templates/mi_cuenta.php'; // Define la URL de 'Mi Cuenta'
$logout_url = '../backend/php/funcions/logout.php'; // Define la URL de cierre de sesión
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Pedir | MyBuenOscar</title>
        <link rel="stylesheet" href="../src/css/StyleDashboard.css?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <style>
            /* ----------------- Productos ----------------- */
            .item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #EEE8C9;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                padding: 15px;
                overflow: hidden;
            }

            .item img {
                width: 180px;
                height: 180px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
            }

            .item img:hover {
                transform: scale(1.05);
            }

            .item-info {
                flex: 1;
                margin-right: 15px;
            }

            .item-info h3 {
                font-size: 1.5rem;
                margin: 0 0 10px;
                color: #333;
            }

            .item-info p {
                font-size: 1rem;
                color: #666;
                margin: 0 0 10px;
            }

            .item-info .price {
                font-size: 1.2rem;
                font-weight: bold;
                color: #4A235A;
            }

            /* ----------------- Carrito ----------------- */
            .cart-item {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
            }

            .cart-item-image {
                width: 50px;
                height: 50px;
                border-radius: 5px;
            }

            .cart-item-details {
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .cart-item-name {
                font-size: 14px;
                font-weight: bold;
                margin: 0;
                margin-bottom: 5px;
            }

            .cart-item-price {
                font-size: 14px;
                font-weight: bold;
                color: #4A235A;
                margin-left: auto;
            }

            .quantity-container {
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .buton-cart-min, .buton-cart-max {
                color: #333;
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .buton-cart-min:hover, .buton-cart-max:hover {
                background-color: #ddd;
                transform: scale(1.1);
            }

            #cantidad {
                font-size: 14px;
                font-weight: bold;
            }

            #items-carrito {
                max-height: none;
                overflow: visible;
            }

            .cart-sidebar {
                position: fixed;
                top: calc(7% + 10px);
                right: 0;
                width: 25%;
                height: auto;
                max-height: 80%;
                background-color: rgba(255, 255, 255, 0.9);
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
                padding: 20px;
                overflow-y: auto;
                z-index: 1000;
                border-radius: 10px 0 0 10px;
            }

            .main-container {
                margin-right: 25%;
            }

            .cart-summary {
                margin-top: 20px;
                text-align: center;
            }

            .cart-continue-btn {
                display: block;
                margin: 20px auto;
                width: 80%;
                background-color: #22c55e;
                color: #ffffff;
                border: none;
                border-radius: 25px;
                padding: 10px 20px;
                font-size: 1.2rem;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .cart-continue-btn:hover {
                background-color: #16a34a;
                transform: scale(1.05);
            }

            .cart-continue-btn:active {
                background-color: #15803d;
                transform: scale(0.95);
            }
        </style>
    </head>
    <body id="index-page">
        <header>
            <div class="navbar-brand">
                <nav class="navbar">
                    <a href="../templates/index.php" class="logo">
                        <img src="../src/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0; justify-content: center;">
                    </a>
                    <a href="../php/carta.php">Carta</a>
                    <a href="../templates/Promos.html">Promos</a>
                    <details class="dropdown">
                        <summary>Contáctanos</summary>
                        <div class="dropdown-menu">
                            <a href="mailto:MyBuenOscarRestaurant@gmail.com">Correo Electrónico</a>
                            <a href="tel:+56958917375">Teléfono</a>
                            <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                        </div>
                    </details>
                    <?php if ($user_logged_in): ?>
                        <a href="<?php echo $mi_cuenta_url; ?>" id="my-account-link">Mi Cuenta</a>
                        <a href="<?php echo $logout_url; ?>" id="logout-link">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="<?php echo $login_url; ?>" id="login-link">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main>
            <div class="main-container">
                <div id="tag-menu" class="container">
                    <h1>Menú</h1>
                </div>
                <div class="menu-columns">
                    <div class="container">
                        <div id="tag-especiales" class="container">
                            <h1>Especiales</h1>
                        </div>
                        <div id="plato-semana" class="section">
                            <div class="menu-grid">
                                <div class="item">
                                    <div class="item-info">
                                        <h3>El Mateo</h3>
                                        <p>Delicioso pollo cthulu para 4 personas.</p>
                                        <span class="price">$12.000</span>
                                        <button class="button" onclick="agregarCarrito('El Mateo', 12000, 'platos', 'elmateo')">Añadir al carrito</button>
                                    </div>
                                    <img src="../src/menu/platos/elmateo.png" alt="Plato El Mateo">
                                </div>
                            </div>
                        </div>
                        <div id="tag-platos" class="container">
                            <h1>Platos</h1>
                        </div>
                        <div id="menu" class="section">
                            <div class="menu-grid">
                                <div class="item">
                                    <div class="item-info">
                                        <h3>Foreskin Oreos</h3>
                                        <p>Ricas oreos cubiertas de prepucio.</p>
                                        <span class="price">$10.000</span>
                                        <button class="button" onclick="agregarCarrito('foreskin Oreos', 10000, 'platos', 'foreskinoreos')">Añadir al carrito</button>
                                    </div>
                                    <img src="../src/menu/platos/foreskinoreos.png" alt="oreos">
                                </div>
                            </div>
                        </div>
                        <div id="tag-bebidas" class="container">
                            <h1>Bebidas</h1>
                        </div>
                        <div id="menu" class="section">
                            <div class="menu-grid">
                                <div class="item">
                                    <div class="item-info">
                                        <h3>Fanta de uva</h3>
                                        <span class="price">$2.000</span>
                                        <button class="button" onclick="agregarCarrito('Fanta de uva', 2000, 'bebidas', 'fanta-de-uva')">Añadir al carrito</button>
                                    </div>
                                    <img src="../src/menu/bebidas/fanta-de-uva.png" alt="Fanta de uva">
                                </div>
                            </div>
                        </div>
                    </div>
                    <aside class="cart-sidebar" id="cart">
                        <section>
                            <h2>Tu Carrito (<span id="cart-count"><?php echo isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?></span>)</h2>
                            <div class="cart-location">
                                <label for="location">¿Dónde quieres pedir?</label>
                                <select id="location">
                                    <option value="">Selecciona una ubicación</option>
                                    <option value="local1">Local 1</option>
                                    <option value="local2">Local 2</option>
                                </select>
                            </div>
                        </section>
                        <div id="items-carrito">
                            <?php
                            $total = 0;
                            foreach ($_SESSION['carrito'] as $index => $item) {
                                $imagen = isset($item['imagen']) && !empty($item['imagen']) ? $item['imagen'] : '../src/menu/default-image.png';
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $total += $subtotal;
                                echo "
                                <div class='container'>
                                    <div class='cart-item'>
                                        <img src='{$imagen}' alt='{$item['nombre']}' class='cart-item-image'>
                                        <div class='cart-item-details'>
                                            <p class='cart-item-name'>{$item['nombre']}</p>
                                            <p class='cart-item-price' id='subtotal-{$index}'>\$" . number_format($subtotal) . "</p>
                                            <div class='quantity-container'>
                                                <button class='buton-cart-min' onclick='actualizarCantidad($index, \"restar\")'>-</button>
                                                <span id='cantidad-{$index}'>{$item['cantidad']}</span>
                                                <button class='buton-cart-max' onclick='actualizarCantidad($index, \"sumar\")'>+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            }
                            ?>
                        </div>
                        <div class="cart-summary">
                            <p>Subtotal: <span class="cart-subtotal">$<?php echo number_format($total); ?></span></p>
                            <button class="cart-continue-btn" onclick="continuarCompra()">Continuar</button>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
        <footer class="footer" style="padding: 10px; font-size: 12px; text-align: center;">
            <p>Ubicación: Chile <br>Teléfono: +56 9 5891 7375</p>
            <div class="section" style="margin: 10px 0;">
                <div>
                    <p class="social-media">
                        <a href="https://www.facebook.com" target="_blank" aria-label="Facebook">
                            <img src="../src/icons/facebook-icon.png" alt="Facebook" style="width: 24px; margin: 0 5px;">
                        </a>
                        <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                            <img src="../src/icons/instagram-icon.png" alt="Instagram" style="width: 24px; margin: 0 5px;">
                        </a>
                        <a href="https://www.twitter.com" target="_blank" aria-label="Twitter">
                            <img src="../src/icons/twitter-icon.png" alt="Twitter" style="width: 24px; margin: 0 5px;">
                        </a>
                    </p>
                </div>
            </div>
            <p>©2025 Cafeteria Aconcagua. Todos los derechos reservados. <br>Desarrollado por los Incervibles</p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script>
            const LOGIN_URL = '<?php echo $login_url; ?>';
            const CHECKOUT_URL = '<?php echo $checkout_url; ?>';
            function agregarCarrito(nombre, precio, type, img) {
                var src = '../src/menu/' + type + '/' + img + '.png';
                fetch('../../backend/php/funcions/add_cart.php', {
                    method: 'POST',
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `nombre=${encodeURIComponent(nombre)}&precio=${precio}&src=${encodeURIComponent(src)}`
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Error al añadir al carrito:', response.statusText);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error('Hubo un problema con la operación fetch para agregar al carrito:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No se pudo añadir el producto al carrito. Inténtalo de nuevo.',
                        confirmButtonText: 'Entendido'
                    });
                });
            }
            function actualizarCantidad(index, accion) {
                fetch('../../backend/php/funcions/update_cart.php', {
                    method: 'POST',
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `index=${index}&accion=${accion}`
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Error al actualizar cantidad:', response.statusText);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error('Hubo un problema con la operación fetch para actualizar la cantidad:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de actualización',
                        text: 'No se pudo actualizar la cantidad. Inténtalo de nuevo.',
                        confirmButtonText: 'Ok'
                    });
                });
            }
            // --- Lógica para continuarCompra con SweetAlert2 ---
            function continuarCompra() {
                const cartCount = parseInt(document.getElementById('cart-count').innerText);
                if (cartCount === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Carrito Vacío',
                        text: 'Tu carrito está vacío. ¡Añade algunos productos antes de continuar!',
                        confirmButtonText: 'Ok'
                    });
                    return; 
                }
                fetch('../../backend/php/funcions/check_session.php') 
                    .then(response => response.json()) 
                    .then(data => {
                        if (data.logged_in) {
                            window.location.href = CHECKOUT_URL;
                        } else {
                            Swal.fire({
                                title: 'Iniciar Sesión Necesario',
                                text: 'Para continuar con tu compra, debes iniciar sesión.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ir a Iniciar Sesión',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = LOGIN_URL; 
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar la sesión:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Conexión',
                            text: 'Ocurrió un error al verificar tu sesión. Por favor, inténtalo de nuevo.',
                            confirmButtonText: 'Entendido'
                        });
                    });
            }
        </script>
    </body>
</html>