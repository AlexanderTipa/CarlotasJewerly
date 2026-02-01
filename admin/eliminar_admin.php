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
    header('Location: gestionar_admins.php');
    exit;
}

// 3. Lógica de Eliminación
if (isset($_POST['id_admin']) && is_numeric($_POST['id_admin'])) {
    
    $id_admin_a_eliminar = $_POST['id_admin'];
    
    // --- ¡¡SEGURIDAD VITAL!! ---
    // Un administrador NO PUEDE eliminarse a sí mismo.
    if ($id_admin_a_eliminar == $_SESSION['id_usuario']) {
        // Redirigir con un error
        header('Location: gestionar_admins.php?error=self_delete');
        exit;
    }
    
    try {
        // Procedemos a eliminar (solo si no es él mismo)
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND tipo_usuario = 'admin'");
        $stmt->execute([$id_admin_a_eliminar]);

        header('Location: gestionar_admins.php?eliminado=exito');
        exit;

    } catch (PDOException $e) {
        header('Location: gestionar_admins.php?error=db');
        exit;
    }
} else {
    header('Location: gestionar_admins.php');
    exit;
}
?>