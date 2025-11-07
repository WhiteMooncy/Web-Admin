<?php
/**
 * Página de Inicio - Cafetería Aconcagua
 */

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styleDashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo ASSETS_URL; ?>/images/icons/icon_cafe.png" type="image/png">
    <title><?php echo APP_NAME; ?> | Inicio</title>
</head>
<body id="index-page">
    <header>
        <div class="navbar-brand">
            <nav class="navbar">
                <a href="<?php echo BASE_URL; ?>/index.php">
                    <img src="<?php echo ASSETS_URL; ?>/images/icons/icon_cafe.png" alt="Logo" style="width: 32px; height: 32px; padding: 0;">  
                </a>
                <a href="<?php echo BASE_URL; ?>/templates/carta.php">Carta</a>
                <details class="dropdown">
                    <summary>Contáctanos</summary>
                    <div class="dropdown-menu">
                        <a href="mailto:Cafeteria.Aconcagua@gmail.com">Correo Electrónico</a>
                        <a href="tel:+56958917375">Teléfono</a>
                        <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                    </div> 
                </details>
                <?php if (isAuthenticated()): ?>
                    <a href="<?php echo BASE_URL; ?>/templates/admin/dashboard.php">Panel</a>
                    <a href="<?php echo BASE_URL; ?>/templates/logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/templates/login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main>
        <div id="back-container" class="container">
            <div class="main-content">
                <div class="container">
                    <h1 class="titulo-bienvenida">¡Bienvenido a Cafetería Aconcagua!</h1>
                    <p>Nos encontramos en el mejor lugar para disfrutar de un buen café.</p>
                </div>
                
                <div class="carousel">
                    <div class="carousel-images">
                        <img src="<?php echo ASSETS_URL; ?>/images/menu/Capuccino.jpeg" alt="Capuccino">
                        <img src="<?php echo ASSETS_URL; ?>/images/menu/SandwichDePollo.jpeg" alt="Sandwich">
                        <img src="<?php echo ASSETS_URL; ?>/images/menu/TartaDeManzana.jpeg" alt="Tarta">
                    </div>
                    <button class="carousel-button prev" onclick="moveSlide(-1)">&#10094;</button>
                    <button class="carousel-button next" onclick="moveSlide(1)">&#10095;</button>
                </div>
            </div>
            
            <div class="instagram-embeds-container" style="display: flex; gap: 20px; justify-content: center;">
                <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/DCwF5ktxo_Q/" 
                    data-instgrm-version="14" style="max-width:540px; min-width:326px; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">
                </blockquote>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
        <p>Versión <?php echo APP_VERSION; ?></p>
    </footer>
    
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-images img');
        const totalSlides = slides.length;
        
        function showSlide(index) {
            if (index >= totalSlides) currentSlide = 0;
            if (index < 0) currentSlide = totalSlides - 1;
            
            const offset = -currentSlide * 100;
            document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
        }
        
        function moveSlide(direction) {
            currentSlide += direction;
            showSlide(currentSlide);
        }
        
        // Auto-slide cada 5 segundos
        setInterval(() => {
            currentSlide++;
            showSlide(currentSlide);
        }, 5000);
        
        showSlide(currentSlide);
    </script>
    
    <script async src="//www.instagram.com/embed.js"></script>
</body>
</html>
