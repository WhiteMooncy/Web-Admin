<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../frontend/src/css/Style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Comprar</title>
</head>
<header>
    <div class="navbar-brand">
        <nav class="navbar">
            <a href="../templates/index.php">
                <img src="../../frontend/src/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0; justify-content: center;">  
            </a>
            <a href="../templates/carta.php">Carta</a>
            <details class="dropdown">
                <summary>Contactanos</summary>
                <div class="dropdown-menu">
                    <a href="mailto:MyBuenOscarRestaurant@gmail.com">Correo Electrónico</a>
                    <a href="tel:+56958917375">Teléfono</a>
                    <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                </div>
            </details>
            <a href="../../backend/templates/form_login.php" id="cart-link">
                Login
            </a>
        </nav>
    </div>
</header>
<body style="background-image: url(../../frontend/src/img/background_Cafeteria.png);">
    <div class="main-container">
        <div class="menu-columns">
            <div class="container">
                <div id="tag-especiales" class="container">
                    <h1>Del Dia</h1>
                </div>
                <div id="plato-semana" class="section">
                    <div class="menu-grid">
                        <div class="item">
                            <div class="item-info">
                                <h3>etc</h3>
                                <p>descripcion.</p>
                                <span class="price">$2.000</span>
                                <!--php-->
                                <button class="button" onclick="agregarCarrito('El Mateo', 12000, 'platos', 'elmateo')">Añadir al carrito</button>
                            </div>
                            <img src="../../frontend/src/img/thumb.jpg" alt="etc">
                        </div>
                </div>
                <div id="tag-platos" class="container">
                    <h1>Cafes</h1>
                </div>
                <div id="menu" class="section">
                    <div class="menu-grid"> 
                        <div class="item">
                            <div class="item-info">
                                <h3>etc</h3>
                                <p>descripcion.</p>
                                <span class="price">$2.000</span>
                                <form action="../backend/php/agregar_carrito.php" method="post">
                                    <input type="text" name="id" hidden>
                                    <input type="hidden" name="nombre" value="etc">
                                    <input type="hidden" name="precio" value="2000">
                                    <input type="hidden" name="cantidad" value="1" min="1">
                                    <button class="button" type="submit">
                                        <h2>Añadir al carro</h2>
                                    </button>
                                </form>
                            </div>
                            <img src="../../frontend/src/img/thumb.jpg" alt="etc">
                        </div> 
                    </div>
                </div>
                <div id="tag-bebidas" class="container">
                    <h1>etc</h1>
                </div>
                <div id="menu" class="section" >
                    <div class="menu-grid">
                        <div class="item">
                            <div class="item-info">
                                <h3>etc</h3>
                                <p>descripcion</p>
                                <span class="price">$2.000</span>
                                <button class="button" onclick="agregarCarrito('El Mateo', 12000, 'platos', 'elmateo')">Añadir al carrito</button>
                            </div>
                            <img src="../../frontend/src/img/thumb.jpg" alt="etc">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Aside -->
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
</body>
<footer class="footer">
    <p>Ubicación: Chile <br>Teléfono: +56 9 5891 7375</p>
    <div class="section">
        <div>
            <p class="social-media">
                <a href="https://www.facebook.com" target="_blank" aria-label="Facebook">
                    <img src="../src/icons/facebook-icon.png" alt="Facebook" style="width: 32px; margin: 0 10px;">
                </a>
                <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                    <img src="../src/icons/instagram-icon.png" alt="Instagram" style="width: 32px; margin: 0 10px;">
                </a>
                <a href="https://www.twitter.com" target="_blank" aria-label="Twitter">
                    <img src="../src/icons/twitter-icon.png" alt="Twitter" style="width: 32px; margin: 0 10px;">
                </a>
            </p>
        </div>
    </div>
    <p>©2025 Restaurant MyBuenOscar. Todos los derechos reservados. <br>Desarrollado por los Incervibles</p>
</footer>
</html>