<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../templates/login.php");
    exit();
}
?>

<h1>Bienvenido, 
    <?php echo $_SESSION['username']; ?>!</h1>
<a href="logout.php">Cerrar sesión</a>
