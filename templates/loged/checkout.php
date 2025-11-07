<?php
session_start();
if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: ../templates/login.php');
    exit();
}
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: carta.php');
    exit();
}
$total_compra = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_compra += $item['precio'] * $item['cantidad'];
}
$buy_order = 'ORDEN-' . uniqid(); // Ejemplo simple, idealmente de tu DB
$session_id = 'SESION-' . uniqid(); // Ejemplo simple
$_SESSION['webpay_transaction_details'] = [
    'buy_order' => $buy_order,
    'session_id' => $session_id,
    'amount' => $total_compra,
];
// URLs para la interacción con Transbank (relativas a tu dominio)
// Asegúrate de que estas rutas sean correctas desde donde se ejecutará Transbank.
// Por ejemplo, si tu dominio es https://tutienda.cl, la return_url debe ser https://tutienda.cl/php/webpay_return.php
// IMPORTANTE: Para entornos de desarrollo (localhost), las URLs de retorno deben ser accesibles desde el exterior,
// lo que puede requerir herramientas como ngrok.
// Para pruebas en localhost sin ngrok, puedes usar una URL como:
// $return_url = 'http://localhost/tareas-con-xampp/Web-Admin/php/webpay_return.php';
// $final_url = 'http://localhost/tareas-con-xampp/Web-Admin/php/webpay_final.php';
// Pero en un entorno real, deben ser rutas absolutas de tu dominio.
// Definimos las URLs de retorno y final
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = "{$protocol}://{$host}/tareas-con-xampp/Web-Admin/php/"; // Ajusta esta parte si tu proyecto no está en esa subcarpeta

$return_url = $base_url . 'webpay_return.php';
$final_url = $base_url . 'webpay_final.php';
// Guarda las URLs en sesión para usarlas en webpay_init.php si no las pasas directamente
$_SESSION['webpay_urls'] = [
    'return_url' => $return_url,
    'final_url' => $final_url,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout | MyBuenOscar</title>
    <link rel="stylesheet" href="../../src/css/StyleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Estilos básicos para checkout, puedes expandirlos */
        .checkout-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4A235A;
            margin-bottom: 30px;
        }
        .order-summary {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
        .order-summary h2 {
            color: #666;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item-name {
            font-weight: 500;
        }
        .order-total {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            color: #4A235A;
            margin-top: 20px;
        }
        .payment-button-container {
            text-align: center;
            margin-top: 40px;
        }
        .pay-button {
            background-color: #22c55e;
            color: #ffffff;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .pay-button:hover {
            background-color: #16a34a;
            transform: scale(1.05);
        }
        .pay-button:active {
            background-color: #15803d;
            transform: scale(0.98);
        }
    </style>
</head>
<body id="index-page">
    <header>
        <div class="navbar-brand">
            <nav class="navbar">
                <a href="../templates/index.php" class="logo">
                    <img src="../../src/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0; justify-content: center;">
                </a>
                <a href="../carta.php">Carta</a>
                <details class="dropdown">
                    <summary>Contactanos</summary>
                    <div class="dropdown-menu">
                        <a href="mailto:MyBuenOscarRestaurant@gmail.com">Correo Electrónico</a>
                        <a href="tel:+56958917375">Teléfono</a>
                        <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                    </div>
                </details>
                <a href="../loged/profile.php" id="cart-link">Mi Cuenta</a>
            </nav>
        </div>
    </header>
    <div class="checkout-container">
        <h1>Confirmar tu Pedido</h1>
        <div class="order-summary">
            <h2>Resumen del Carrito</h2>
            <?php if (!empty($_SESSION['carrito'])): ?>
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div class="order-item">
                        <span class="order-item-name"><?php echo htmlspecialchars($item['nombre']); ?> (x<?php echo $item['cantidad']; ?>)</span>
                        <span class="order-item-price">$<?php echo number_format($item['precio'] * $item['cantidad']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tu carrito está vacío.</p>
            <?php endif; ?>
            <div class="order-total">
                Total: $<?php echo number_format($total_compra); ?>
            </div>
        </div>
        <div class="payment-button-container">
            <button class="pay-button" onclick="iniciarPagoWebpay()">Pagar con Webpay Plus</button>
        </div>
    </div>
    <footer class="footer" style="padding: 10px; font-size: 12px; text-align: center;">
        <p>Ubicación: Chile <br>Teléfono: +56 9 5891 7375</p>
        <div class="section" style="margin: 10px 0;">
            <div>
                <p class="social-media">
                    <a href="https://www.facebook.com/?locale=es_LA" target="_blank" aria-label="Facebook">
                        <img src="../../src/icons/facebook-icon.png" alt="Facebook" style="width: 24px; margin: 0 5px;">
                    </a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                        <img src="../../src/icons/instagram-icon.png" alt="Instagram" style="width: 24px; margin: 0 5px;">
                    </a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter">
                        <img src="../../src/icons/twitter-icon.png" alt="Twitter" style="width: 24px; margin: 0 5px;">
                    </a>
                </p>
            </div>
        </div>
        <p>©2025 Cafeteria Aconcagua. Todos los derechos reservados. <br>Desarrollado por los Incervibles</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function iniciarPagoWebpay() {
            Swal.fire({
                title: 'Iniciando Pago...',
                text: 'Serás redirigido a la pasarela de pago de Transbank.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            fetch('webpay_init.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                // No es necesario enviar el total, buy_order, etc.,
                // ya que ya están en la sesión PHP y webpay_init.php los leerá.
                // Puedes enviar un JSON vacío si el método es POST
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Crea un formulario oculto para la redirección POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = data.url;
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'token_ws';
                    tokenInput.value = data.token;
                    form.appendChild(tokenInput);
                    document.body.appendChild(form);
                    form.submit(); // Envía el formulario para redirigir a Transbank
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al iniciar pago',
                        text: data.message || 'No se pudo iniciar la transacción con Transbank. Por favor, inténtalo de nuevo.',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Hubo un problema de red. Por favor, inténtalo de nuevo.',
                    confirmButtonText: 'Entendido'
                });
            });
        }
    </script>
</body>
</html>