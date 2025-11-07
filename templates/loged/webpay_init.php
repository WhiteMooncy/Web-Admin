<?php
session_start();
header('Content-Type: application/json');

// Credenciales de test para Webpay Plus (NO USAR EN PRODUCCIÓN)
$commerce_code = '597055555532';
$api_key = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
$transbank_init_url = 'https://webpay3gint.transbank.cl/rswebpaytransaction/api/webpay/v1.2/transactions';

// Obtener datos de la sesión
$transaction_details = $_SESSION['webpay_transaction_details'] ?? null;
$webpay_urls = $_SESSION['webpay_urls'] ?? null;

if (!$transaction_details || !$webpay_urls) {
    echo json_encode(['status' => 'error', 'message' => 'No hay detalles de transacción o URLs de retorno en la sesión.']);
    exit();
}

$amount = $transaction_details['amount'];
$buy_order = $transaction_details['buy_order'];
$session_id = $transaction_details['session_id'];
$return_url = $webpay_urls['return_url'];
$final_url = $webpay_urls['final_url'];

// Preparar los datos para la solicitud a Transbank
$data = [
    'buy_order' => (string)$buy_order,
    'session_id' => (string)$session_id,
    'amount' => (int)$amount, // Asegúrate de que el monto sea un entero
    'return_url' => $return_url,
];

$ch = curl_init($transbank_init_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
    error_log("Error cURL al iniciar Transbank: " . $error_msg);
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión con Transbank: ' . $error_msg]);
    exit();
}
curl_close($ch);

$decoded_response = json_decode($response, true);

if ($http_status === 200 && isset($decoded_response['token']) && isset($decoded_response['url'])) {
    // Guarda el token en sesión para la confirmación
    $_SESSION['webpay_token'] = $decoded_response['token'];
    echo json_encode([
        'status' => 'success',
        'token' => $decoded_response['token'],
        'url' => $decoded_response['url']
    ]);
} else {
    $error_message = 'Respuesta inesperada de Transbank.';
    if (isset($decoded_response['error_message'])) {
        $error_message = $decoded_response['error_message'];
    } elseif (isset($decoded_response['status'])) {
        $error_message = 'Error de Transbank: ' . $decoded_response['status'];
    }
    error_log("Error Transbank Init: HTTP Status " . $http_status . ", Response: " . $response);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}

exit();
?>