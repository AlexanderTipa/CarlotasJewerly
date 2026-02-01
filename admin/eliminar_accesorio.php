<?php
// 1. Iniciar sesión y conexión
session_start();
require '../includes/db_conexion.php';

// 2. ¡SEGURIDAD!
// Solo administradores pueden estar aquí y solo vía POST
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location:../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Si no es POST, no hacer nada y redirigir
    header('Location: accesorios.php');
    exit;
}

// 3. Lógica de Eliminación
if (isset($_POST['id_accesorio']) && is_numeric($_POST['id_accesorio'])) {
    $id_accesorio = $_POST['id_accesorio'];

    try {
        // --- Paso A: Borrar la imagen del servidor ---
        // (Para no guardar basura en la carpeta 'uploads')
        
        // 1. Obtener el nombre de la imagen
        $stmt_img = $pdo->prepare("SELECT imagen_url FROM accesorios WHERE id_accesorio = ?");
        $stmt_img->execute([$id_accesorio]);
        $accesorio = $stmt_img->fetch();
        
        if ($accesorio && !empty($accesorio['imagen_url'])) {
            $ruta_imagen = 'uploads/' . $accesorio['imagen_url'];
            
            // 2. Borrar el archivo si existe
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // --- Paso B: Borrar el registro de la Base de Datos ---
        $stmt_delete = $pdo->prepare("DELETE FROM accesorios WHERE id_accesorio = ?");
        $stmt_delete->execute([$id_accesorio]);

        // 4. Redirigir con mensaje de éxito
        header('Location: accesorios.php?eliminado=exito');
        exit;

    } catch (PDOException $e) {
        // Manejar error
        header('Location: accesorios.php?error=db');
        exit;
    }
} else {
    // Si no se envió un ID válido
    header('Location: accesorios.php');
    exit;
}
?>