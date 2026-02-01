<?php
// 1. Iniciar sesión y conexión
session_start();
require '../includes/db_conexion.php';

// 2. ¡SEGURIDAD!
// Solo administradores pueden estar aquí y solo vía POST
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: pedidos.php');
    exit;
}

// 3. Lógica de Actualización de Estado
if (isset($_POST['id_pedido']) && is_numeric($_POST['id_pedido'])) {
    
    $id_pedido = $_POST['id_pedido'];
    // El nuevo estado que queremos establecer
    $nuevo_estado = 'en proceso de envio'; 

    try {
        // Actualizamos solo el pedido que coincida Y que esté 'pendiente'
        $stmt = $pdo->prepare("UPDATE pedidos SET estado_pedido = ? WHERE id_pedido = ? AND estado_pedido = 'pendiente'");
        $stmt->execute([$nuevo_estado, $id_pedido]);

        // 4. Redirigir con mensaje de éxito
        header('Location: pedidos.php?actualizado=exito');
        exit;

    } catch (PDOException $e) {
        // Manejar error
        header('Location: pedidos.php?error=db');
        exit;
    }
} else {
    // Si no se envió un ID válido
    header('Location: pedidos.php');
    exit;
}
?>