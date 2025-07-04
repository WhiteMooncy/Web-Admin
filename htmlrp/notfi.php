<?php
header('Content-Type: text/html; charset=utf-8');

// Aquí podrías conectar a una base de datos (MySQL con XAMPP)
// y recuperar las notificaciones reales.
// Por ahora, generaremos algunas notificaciones de ejemplo.

$notifications = [
    [
        'type' => 'message',
        'title' => 'Nuevo Mensaje de Soporte',
        'message' => 'Tienes una nueva respuesta a tu ticket #12345.',
        'time' => 'Hace 5 minutos',
        'read' => false
    ],
    [
        'type' => 'offer',
        'title' => '¡Oferta Especial!',
        'message' => 'Descuento del 20% en todos los productos por tiempo limitado.',
        'time' => 'Hace 2 horas',
        'read' => true
    ],
    [
        'type' => 'friend_request',
        'title' => 'Nueva Solicitud de Amistad',
        'message' => 'Juan Pérez te ha enviado una solicitud de amistad.',
        'time' => 'Ayer',
        'read' => false
    ]
];

// Comienza a generar el HTML de las notificaciones
foreach ($notifications as $notification) {
    $read_class = $notification['read'] ? 'read' : 'unread';
    $icon_class = '';
    switch ($notification['type']) {
        case 'message':
            $icon_class = 'fas fa-bell';
            break;
        case 'offer':
            $icon_class = 'fas fa-star';
            break;
        case 'friend_request':
            $icon_class = 'fas fa-user-plus';
            break;
    }
    ?>
    <div class="notification <?php echo $read_class; ?>">
        <div class="notification-icon">
            <i class="<?php echo $icon_class; ?>"></i>
        </div>
        <div class="notification-content">
            <p class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></p>
            <p class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></p>
            <span class="notification-time"><?php echo htmlspecialchars($notification['time']); ?></span>
        </div>
        <?php if (!$notification['read']) { ?>
            <button class="mark-as-read" title="Marcar como leído">✓</button>
        <?php } ?>
    </div>
    <?php
}

if (empty($notifications)) {
    echo '<div class="no-notifications"><p>No tienes notificaciones nuevas.</p></div>';
}
?>