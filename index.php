<?php
require_once '../../backend/php/conexion/db.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../src/css/styleDashboard.css?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <title>Cafeteria Aconcagua |</title>
    </head>
    <body id="index-page">
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
                            <a href="mailto:Cafeteria.Aconcagua@gmail.com">Correo Electrónico</a>
                            <a href="tel:+56958917375">Teléfono</a>
                            <a href="https://wa.me/56958917375" target="_blank">WhatsApp</a>
                        </div> 
                    </details>
                    <a href="../templates/loged/form_login.php" id="cart-link">Login</a>
                </nav>
            </div>
        </header>
        <main>
            <div id="back-container" class="container">
                <div class="main-content">
                    <div class="container">
                        <h1 class="titulo-bienvenida">¡Bienvenido a Cafeteria Aconcagua!</h1>
                        <p>Nos encontramos en el mejor lugar para disfrutar de un buen cafe.</p>
                    </div>
                    <div class="carousel">
                        <div class="carousel-images">
                            <img src="../../frontend/src/menu/cafe.jpeg" alt="img">
                            <img src="../../frontend/src/menu/pandepollo.jpeg" alt="img">
                            <img src="../../frontend/src/menu/tartademanzana.jpeg" alt="img">
                        </div>
                        <button class="carousel-button prev" onclick="moveSlide(-1)">&#10094;</button>
                        <button class="carousel-button next" onclick="moveSlide(1)">&#10095;</button>
                    </div>
                </div>
                <div class="instagram-embeds-container" style="display: flex; gap: 20px; justify-content: center;">
                    <!-- Aquí publicaciones de Instagram algun update mas adelante-->
                </div>
            </div>
        </main>
        <footer id="index-footer" class="footer">
            <p>Ubicación: Chile <br>Teléfono: +56 9 5891 7375</p>
            <div class="section">
                <div class="social-media">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook">
                        <img src="../../frontend/src/icons/facebook-icon.png" alt="Facebook" style="width: 32px; margin: 0 10px;">
                    </a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                        <img src="../../frontend/src/icons/instagram-icon.png" alt="Instagram" style="width: 32px; margin: 0 10px;">
                    </a>
                    <a href="https://www.twitter.com" target="_blank" aria-label="Twitter">
                        <img src="../../frontend/src/icons/twitter-icon.png" alt="Twitter" style="width: 32px; margin: 0 10px;">
                    </a>
                </div>
            </div>
            <p>© 2025 Cafeteria Aconcagua. Todos los derechos reservados. <br>Desarrollado por los Incervibles</p>
        </footer>
        <script>
            let currentSlide = 0;
            function moveSlide(direction) {
                const slides = document.querySelector('.carousel-images');
                const totalSlides = slides.children.length;
                currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
                slides.style.transform = `translateX(-${currentSlide * 100}%)`;
            }
        </script>
    </body>
</html>
