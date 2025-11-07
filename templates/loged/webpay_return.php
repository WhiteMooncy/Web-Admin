<?php
session_start();

// Credenciales de test para Webpay Plus (NO USAR EN PRODUCCIÓN)
$commerce_code = '597055555532';
$api_key = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';

// Transbank nos envía el token de vuelta como POST o GET, dependiendo de la configuración
$token_ws = $_POST['token_ws'] ?? $_GET['token_ws'] ?? null;

if (!$token_ws) {
    // No hay token, algo salió mal o es un acceso directo
    $_SESSION['webpay_result'] = [
        'status' => 'error',
        'message' => 'No se recibió el token de Webpay.',
    ];
    header('Location: webpay_final.php');
    exit();
}

// URL de confirmación de Transbank
$transbank_confirm_url = "https://webpay3gint.transbank.cl/rswebpaytransaction/api/webpay/v1.2/transactions/{$token_ws}";

$ch = curl_init($transbank_confirm_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PUT, true); // La confirmación es una solicitud PUT para Transbank
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Tbk-Api-Key-Id: ' . $commerce_code,
    'Tbk-Api-Key-Secret: ' . $api_key,
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    error_log("Error cURL al confirmar Transbank: " . $error_msg);
    $_SESSION['webpay_result'] = [
        'status' => 'error',
        'message' => 'Error de conexión al confirmar pago: ' . $error_msg,
    ];
    header('Location: webpay_final.php');
    exit();
}
curl_close($ch);

$decoded_response = json_decode($response, true);

// Limpiar el carrito después de la confirmación (independiente del resultado para este ejemplo)
// En un sistema real, solo limpiarías si la transacción es exitosa y la orden se ha guardado correctamente.
unset($_SESSION['carrito']); 
unset($_SESSION['webpay_transaction_details']);
unset($_SESSION['webpay_token']);
unset($_SESSION['webpay_urls']); // También limpia las URLs para futuras transacciones

// Guardar el resultado en sesión para mostrarlo en webpay_final.php
if ($http_status === 200 && isset($decoded_response['status'])) {
    if ($decoded_response['status'] === 'AUTHORIZED' || $decoded_response['response_code'] === 0) { // 'AUTHORIZED' o response_code 0 indican éxito
        $_SESSION['webpay_result'] = [
            'status' => 'success',
            'message' => '¡Tu pago ha sido exitoso!',
            'details' => $decoded_response,
        ];
        // Aquí es donde deberías:
        // 1. Guardar la orden en tu base de datos con todos los detalles de la transacción.
        // 2. Marcar la orden como pagada.
        // 3. Enviar correos de confirmación.
    } else {
        $_SESSION['webpay_result'] = [
            'status' => 'failure',
            'message' => 'El pago fue rechazado o falló: ' . ($decoded_response['response_code'] ?? 'N/A') . ' - ' . ($decoded_response['status'] ?? 'N/A'),
            'details' => $decoded_response,
        ];
    }
} else {
    $_SESSION['webpay_result'] = [
        'status' => 'error',
        'message' => 'Error al procesar la confirmación de pago. Respuesta inesperada de Transbank.',
        'details' => $decoded_response,
    ];
}

// Redirige a la página final de resultados
header('Location: webpay_final.php');
exit();
?>