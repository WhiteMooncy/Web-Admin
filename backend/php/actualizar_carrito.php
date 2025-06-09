<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = intval($_POST['index']);
    $accion = $_POST['accion'];

    if (isset($_SESSION['carrito'][$index])) {
        switch ($accion) {
            case "sumar":
                $_SESSION['carrito'][$index]['cantidad']++;
                break;
            case "restar":
                $_SESSION['carrito'][$index]['cantidad']--;
                if ($_SESSION['carrito'][$index]['cantidad'] <= 0) {
                    unset($_SESSION['carrito'][$index]);
                    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                }
                break;
            case "eliminar":
                unset($_SESSION['carrito'][$index]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                break;
        }
    }
}
?>
