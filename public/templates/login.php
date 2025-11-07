<?php
/**
 * Página de Login y Registro
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/session.php';
require_once SRC_PATH . '/auth/auth.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    header('Location: ' . BASE_URL . '/templates/admin/dashboard.php');
    exit();
}

$error = '';
$success = '';
$showRegister = false;

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $result = processLogin($_POST['username'], $_POST['password']);
    
    if ($result['success']) {
        header('Location: ' . BASE_URL . '/templates/admin/dashboard.php');
        exit();
    } else {
        $error = $result['message'];
    }
}

// Procesar registro
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['registro'])) {
    $result = processRegister($_POST);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
        $showRegister = true;
    }
}

// Manejar mensajes de sesión
if (isset($_SESSION['register_error'])) {
    $error = $_SESSION['register_error'];
    $showRegister = true;
    unset($_SESSION['register_error']);
}

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if (isset($_SESSION['show_register'])) {
    $showRegister = true;
    unset($_SESSION['show_register']);
}
?>
<!DOCTYPE html>
<html id="login-page" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styleDashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title><?php echo APP_NAME; ?> | Login</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            overflow: auto;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-button:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="login" value="1">
                
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                ¿No tienes cuenta? <a href="#" id="openRegisterModal">Regístrate aquí</a>
            </p>
            
            <p style="text-align: center;">
                <a href="<?php echo BASE_URL; ?>/index.php">← Volver al inicio</a>
            </p>
        </div>
    </div>
    
    <!-- Modal de Registro -->
    <div id="registerModal" class="modal <?php echo $showRegister ? 'active' : ''; ?>">
        <div class="modal-content">
            <span class="close-button" id="closeRegisterModal">&times;</span>
            <h2>Registro de Usuario</h2>
            
            <form method="POST" action="">
                <input type="hidden" name="registro" value="1">
                
                <div class="form-group">
                    <label for="reg_nombre">Nombre Completo:</label>
                    <input type="text" id="reg_nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_correo">Correo Electrónico:</label>
                    <input type="email" id="reg_correo" name="correo" required autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="reg_telefono">Teléfono:</label>
                    <input type="tel" id="reg_telefono" name="telefono" required pattern="[0-9]{9}" 
                           title="Ingresa un número de 9 dígitos">
                </div>
                
                <div class="form-group">
                    <label for="reg_username">Usuario:</label>
                    <input type="text" id="reg_username" name="username" required autocomplete="new-username">
                </div>
                
                <div class="form-group">
                    <label for="reg_password">Contraseña:</label>
                    <input type="password" id="reg_password" name="password" required 
                           autocomplete="new-password" minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const modal = document.getElementById('registerModal');
        const openBtn = document.getElementById('openRegisterModal');
        const closeBtn = document.getElementById('closeRegisterModal');
        
        openBtn.onclick = (e) => {
            e.preventDefault();
            modal.classList.add('active');
        };
        
        closeBtn.onclick = () => {
            modal.classList.remove('active');
        };
        
        window.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        };
    </script>
</body>
</html>
