<?php
session_start();
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$user_logged_in = isset($_SESSION['IS_Usuario']);
$login_url = '../templates/loged/form_login.php';
$checkout_url = '../templates/loged/checkout.php'; 
$mi_cuenta_url = '../templates/mi_cuenta.php'; 
$logout_url = '../backend/php/funcions/logout.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Pedir | MyBuenOscar</title>
        <link rel="stylesheet" href="../src/css/StyleDashboard.css?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    </head>
    <body id="cart-page" style="margin:0; padding: 4%;">
        <header>
            <div class="navbar-brand">
                <nav class="navbar">
                    <a href="../templates/index.php" class="logo">
                        <img src="../src/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0; justify-content: center;">
                    </a>
                    <a href="../php/carta.php">Carta</a>
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
        <main> <div class="content-wrapper"> <div class="main-container">
                    <div id="tag-menu" class="container">
                        <h1>Menú</h1>
                    </div>
                    <div class="menu-columns">
                        <div class="container">
                            <div id="tag-especiales" class="container">
                                <h1>Café</h1>
                            </div>
                            <div id="plato-semana" class="section">
                                <div class="menu-grid">
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Expresso</h3>
                                            <span class="price">$3.400</span>
                                            <button class="button" onclick="agregarCarrito('Expresso', 3400, 'bebestibles', 'expresso')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/Expresso.jpeg" alt="expresso">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Capuccino</h3>
                                            <span class="price">$3.800</span>
                                            <button class="button" onclick="agregarCarrito('Capuccino', 3800, 'bebestibles', 'capuccino')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu//Capuccino.jpeg" alt="capuccino">
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
                                            <h3>Té Verde</h3>
                                            <span class="price">$2.500</span>
                                            <button class="button" onclick="agregarCarrito('Té Verde', 2500, 'bebestibles', 'te_verde')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/TéVerde.jpeg" alt="Té Verde">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Jugo Natural</h3>
                                            <span class="price">$3.000</span>
                                            <button class="button" onclick="agregarCarrito('Jugo Natural', 3000, 'bebestibles', 'jugo_natural')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/JugoNatural.jpeg" alt="Jugo Natural">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Agua</h3>
                                            <span class="price">$1.500</span>
                                            <button class="button" onclick="agregarCarrito('Agua', 1500, 'bebestibles', 'agua')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/agua.jpeg" alt="Agua">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Agua con Gas</h3>
                                            <span class="price">$1.800</span>
                                            <button class="button" onclick="agregarCarrito('Agua con Gas', 1800, 'bebestibles', 'agua_con_gas')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/AguaConGas.jpeg" alt="Agua con Gas">
                                    </div>
                                </div>
                            </div>
                            <div id="tag-platos" class="container">
                                <h1>Comidas</h1>
                            </div>
                            <div id="menu" class="section">
                                <div class="menu-grid">
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Sandwich de Pollo</h3>
                                            <span class="price">$5.500</span>
                                            <button class="button" onclick="agregarCarrito('Sandwich de Pollo', 5500, 'comidas', 'sandwich_pollo')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/SandwichDePollo.jpeg" alt="Sandwich de Pollo">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Croissant</h3>
                                            <span class="price">$2.800</span>
                                            <button class="button" onclick="agregarCarrito('Croissant', 2800, 'comidas', 'croissant')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/Croissant.jpeg" alt="Croissant">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Ensalada César</h3>
                                            <span class="price">$6.200</span>
                                            <button class="button" onclick="agregarCarrito('Ensalada César', 6200, 'comidas', 'ensalada_cesar')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/EnsaladaCésar.jpeg" alt="Ensalada César">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Empanada</h3>
                                            <span class="price">$2.000</span>
                                            <button class="button" onclick="agregarCarrito('Empanada', 2000, 'comidas', 'empanada')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/Empanada.jpeg" alt="Empanada">
                                    </div>
                                </div>
                            </div>
                            <div id="tag-postres" class="container">
                                <h1>Postres</h1>
                            </div>
                            <div id="menu" class="section">
                                <div class="menu-grid">
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Brownie</h3>
                                            <span class="price">$3.500</span>
                                            <button class="button" onclick="agregarCarrito('Brownie', 3500, 'postres', 'brownie')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/Brownie.jpeg" alt="Brownie">
                                    </div>
                                    <div class="item">
                                        <div class="item-info">
                                            <h3>Tarta de Manzana</h3>
                                            <span class="price">$4.000</span>
                                            <button class="button" onclick="agregarCarrito('Tarta de Manzana', 4000, 'postres', 'tarta_manzana')">Añadir al carrito</button>
                                        </div>
                                        <img src="../src/menu/TartaDeManzana.jpeg" alt="Tarta de Manzana">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <aside class="cart-sidebar" id="cart">
                    <section>
                        <h2>Tu Carrito (<span id="cart-count"><?php echo isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?></span>)</h2>
                        <div class="cart-location">
                            <label for="location">¿Modalidad?</label>
                            <select id="location">
                                <option value="">Selecciona una Forma de retiro</option>
                                <option value="local1">Para llevar</option>
                                <option value="local2">Para Servir</option>
                            </select>
                        </div>
                    </section>
                    <div id="items-carrito">
                        <?php
                        $total = 0;
                        foreach ($_SESSION['carrito'] as $index => $item) {
                            $imagen = isset($item['imagen']) && !empty($item['imagen']) ? $item['imagen'] : '../src/menu/default-image.jpeg';
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
                var src = '../src/menu/' + nombre.replace(/\s/g, '') + '.jpeg';
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