<?php
/**
 * Redireccionamiento al nuevo index.php en public/
 * Este archivo mantiene la compatibilidad con la URL antigua
 */

// Redirigir a la nueva ubicación
header('Location: public/index.php');
exit();
?>
