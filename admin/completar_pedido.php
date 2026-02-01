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

// 3. Lógica de Eliminación (Completar Pedido)
if (isset($_POST['id_pedido']) && is_numeric($_POST['id_pedido'])) {
    
    $id_pedido = $_POST['id_pedido'];

    try {
        // --- INICIAR TRANSACCIÓN ---
        $pdo->beginTransaction();

        // 3.1. Borrar primero los "detalles" del pedido (llave foránea)
        $stmt_detalle = $pdo->prepare("DELETE FROM detalle_pedidos WHERE id_pedido = ?");
        $stmt_detalle->execute([$id_pedido]);
        
        // 3.2. Borrar el pedido "principal"
        $stmt_pedido = $pdo->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
        $stmt_pedido->execute([$id_pedido]);

        // 3.3. ¡ÉXITO! Confirmar los cambios
        $pdo->commit();

        // 4. Redirigir con mensaje de éxito
        header('Location: pedidos.php?completado=exito');
        exit;

    } catch (Exception $e) {
        // 5. ¡FALLO! Revertir todo
        $pdo->rollBack();
        header('Location: pedidos.php?error=db_delete');
        exit;
    }
} else {
    // Si no se envió un ID válido
    header('Location: pedidos.php');
    exit;
}
?>