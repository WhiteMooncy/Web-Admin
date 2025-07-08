<?php
session_start();

$result = $_SESSION['webpay_result'] ?? null;
$message = 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.';
$icon = 'error'; // Por defecto, si no hay resultado

if ($result) {
    $message = $result['message'];
    $icon = $result['status']; // 'success', 'failure', 'error'

    // Opcional: Log o mostrar más detalles del array $result['details']
    // Para depuración, podrías descomentar la siguiente línea:
    // echo '<pre>' . print_r($result['details'], true) . '</pre>';
}

// Una vez mostrado el resultado, limpiar la sesión de Webpay
unset($_SESSION['webpay_result']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de Pago | MyBuenOscar</title>
    <link rel="stylesheet" href="../src/css/StyleDashboard.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F4E3;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .result-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            margin: 50px auto;
        }
        .result-container h1 {
            margin-top: 0;
            color: #4A235A;
        }
        .result-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .result-icon.success { color: #22c55e; }
        .result-icon.failure { color: #ef4444; }
        .result-icon.error { color: #f97316; }
        .result-message {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .back-button {
            background-color: #4A235A;
            color: #ffffff;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #6a3a7c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar-brand">
            <nav class="navbar">
                <a href="../templates/index.php" class="logo">
                    <img src="../src/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0; justify-content: center;">
                </a>
                <a href="../php/carta.php">Carta</a>
                <a href="../templates/Promos.html">Promos</a>
                <details class="dropdown">
                    <summary>Contactanos</summary>
                    <div class="dropdown-menu">
                        <a href="mailto:MyBuenOscarRestaurant@gmail.com">Correo Electrónico</a>
                        <a href="tel:+56958917375">Teléfono</a>
                        <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                    </div>
                </details>
                <a href="../templates/mi_cuenta.php" id="cart-link">Mi Cuenta</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="result-container">
            <h1>Resultado de tu Compra</h1>
            <div class="result-icon <?php echo htmlspecialchars($icon); ?>">
                <?php
                if ($icon === 'success') {
                    echo '&#10004;'; // Tick mark
                } elseif ($icon === 'failure') {
                    echo '&#10006;'; // Cross mark
                } else {
                    echo '&#9888;'; // Warning sign
                }
                ?>
            </div>
            <p class="result-message"><?php echo htmlspecialchars($message); ?></p>
            <a href="carta.php" class="back-button">Volver al Menú</a>
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
</body>
</html>