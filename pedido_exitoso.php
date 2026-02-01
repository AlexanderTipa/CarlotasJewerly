<?php
// 1. Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. ¡SEGURIDAD!
// Si el usuario no acaba de hacer un pedido, no debe estar aquí.
if (!isset($_SESSION['ultimo_pedido_id'])) {
    header('Location: index.php');
    exit;
}

// 3. Obtener el ID del pedido y limpiarlo de la sesión
$id_pedido = $_SESSION['ultimo_pedido_id'];
unset($_SESSION['ultimo_pedido_id']); // Limpiamos para que no pueda recargar

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Realizado! - Carlota's Jewelry</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-logo">
        <a href="index.php">Carlota's Jewelry</a>
    </div>
    
    <div class="form-container pedido-exitoso">
        <div class="exitoso-icono">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="#2ecc71"/></svg>
        </div>
        
        <h2>¡Gracias por tu pedido!</h2>
        <p>Tu pedido con el <strong>ID #<?php echo $id_pedido; ?></strong> ha sido recibido con éxito.</p>
        
        <hr style="display: block; border-top: 1px solid var(--color-borde); margin: 25px 0;">
        
        <div class="alerta alerta-info" style="text-align: left;">
            <p style="margin: 0; font-size: 0.95rem; line-height: 1.6;">
                Por favor mándanos un Whatsapp al número <b>012 345 6789</b> y con gusto te brindamos el número de cuenta para la transferencia, pago en ventanilla o en un Oxxo. También puedes hacernos una llamada telefónica. ¡Éxito!
            </p>
        </div>
        
        <div class="form-grupo" style="margin-top: 30px; margin-bottom: 0;">
            <a href="index.php" class="btn btn-principal">Seguir Comprando</a>
        </div>
    </div>

</body>
</html>