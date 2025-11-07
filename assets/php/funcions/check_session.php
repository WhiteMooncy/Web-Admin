<?php
session_start();

header('Content-Type: application/json'); // Indica que la respuesta será JSON

$response = ['logged_in' => false];

// Asume que tienes una variable de sesión como 'user_id' que se establece al iniciar sesión
if (isset($_SESSION['ID_Usuario']) && !empty($_SESSION['ID_Usuario'])) {
    $response['logged_in'] = true;
}

echo json_encode($response);
?>