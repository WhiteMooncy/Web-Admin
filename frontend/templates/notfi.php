<?php
header('Content-Type: text/html; charset=utf-8');

// Simulación de notificaciones (puedes conectar a tu BD aquí)
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Notificaciones - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f4f6fa;
            margin: 0;
            padding: 0;
        }
        .admin-panel {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px #0002;
            padding: 32px 28px;
        }
        .panel-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #212f3d;
            margin-bottom: 24px;
            text-align: center;
            letter-spacing: 1px;
        }
        .notification {
            display: flex;
            align-items: flex-start;
            background: #f9f6f2;
            border-radius: 10px;
            margin-bottom: 18px;
            padding: 18px 16px;
            box-shadow: 0 2px 8px #0001;
            border-left: 5px solid #e0a96d;
            position: relative;
            transition: background 0.2s;
        }
        .notification.unread {
            background: #fffbe6;
            border-left-color: #f6c700;
        }
        .notification.read {
            opacity: 0.7;
        }
        .notification-icon {
            margin-right: 18px;
            font-size: 1.6rem;
            color: #e0a96d;
            min-width: 32px;
            text-align: center;
        }
        .notification-content {
            flex: 1;
        }
        .notification-title {
            font-weight: 600;
            margin: 0 0 4px 0;
            color: #212f3d;
            font-size: 1.1rem;
        }
        .notification-message {
            margin: 0 0 8px 0;
            color: #555;
            font-size: 1rem;
        }
        .notification-time {
            font-size: 0.9rem;
            color: #888;
        }
        .mark-as-read {
            background: #388e3c;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 1.1rem;
            cursor: pointer;
            position: absolute;
            top: 18px;
            right: 18px;
            transition: background 0.2s;
        }
        .mark-as-read:hover {
            background: #2e7031;
        }
        .no-notifications {
            text-align: center;
            color: #888;
            padding: 32px 0;
        }
    </style>
</head>
<body>
    <div class="admin-panel">
        <div class="panel-title">
            <i class="fas fa-bell"></i> Notificaciones del Administrador
        </div>
        <?php
        if (!empty($notifications)) {
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
        } else {
            echo '<div class="no-notifications"><p>No tienes notificaciones nuevas.</p></div>';
        }
        ?>
    </div>
</body>
</html>