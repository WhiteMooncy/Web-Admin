<?php
session_start();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = intval($_POST['precio']);

    $existe = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['nombre'] === $nombre) {
            $item['cantidad'] += 1;
            $existe = true;
            break;
        }
    }
    if (!$existe) {
        $_SESSION['carrito'][] = [
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => 1
        ];
    }
}
?>
