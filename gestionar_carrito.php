<?php
// 1. Iniciar la sesión
session_start();

// 2. Incluir la conexión a la BD
require 'includes/db_conexion.php';

// 3. ¡SEGURIDAD! Verificar que el usuario esté logueado.
// (Según tu requisito, un usuario no logueado no debería poder llegar aquí)
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?error=necesita_login');
    exit;
}

// 4. Inicializar el carrito si no existe
// $_SESSION['carrito'] será un array asociativo 
// donde la "llave" (key) es el ID del producto.
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// 5. Obtener la acción y el ID del producto (desde la URL)
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Convertir a entero por seguridad

// 6. --- LÓGICA PRINCIPAL (Switch de Acciones) ---

if ($id_producto > 0) { // Solo proceder si tenemos un ID válido
    
    switch ($accion) {
        
        // --- CASO 1: AÑADIR PRODUCTO ---
        case 'agregar':
            try {
                // 1. Buscar el producto en la BD para verificar existencia y precio
                $stmt = $pdo->prepare("SELECT * FROM accesorios WHERE id_accesorio = ?");
                $stmt->execute([$id_producto]);
                $producto = $stmt->fetch();
                
                if ($producto) {
                    // 2. Si el producto existe, añadirlo al carrito
                    
                    // 2.1. Si el producto YA está en el carrito, solo aumentamos la cantidad
                    if (isset($_SESSION['carrito'][$id_producto])) {
                        
                        $_SESSION['carrito'][$id_producto]['cantidad']++;
                        
                    } else {
                        // 2.2. Si es un producto nuevo, lo añadimos
                        $_SESSION['carrito'][$id_producto] = [
                            'id' => $producto['id_accesorio'],
                            'nombre' => $producto['nombre'],
                            'precio' => $producto['precio'],
                            'imagen' => $producto['imagen_url'],
                            'cantidad' => 1 // Cantidad inicial
                        ];
                    }
                    
                } else {
                    // Producto no encontrado en la BD (error)
                    // (Podríamos añadir un mensaje de error aquí)
                }

            } catch (PDOException $e) {
                // Error de base de datos
                // (Podríamos añadir un mensaje de error aquí)
            }
            break;
            
        // --- CASO 2: ELIMINAR PRODUCTO (Lo usará carrito.php) ---
        case 'eliminar':
            if (isset($_SESSION['carrito'][$id_producto])) {
                unset($_SESSION['carrito'][$id_producto]);
            }
            break;

        // --- CASO 3: RESTAR CANTIDAD (Lo usará carrito.php) ---
        case 'restar':
             if (isset($_SESSION['carrito'][$id_producto])) {
                $_SESSION['carrito'][$id_producto]['cantidad']--;
                
                // Si la cantidad llega a 0, eliminar el producto
                if ($_SESSION['carrito'][$id_producto]['cantidad'] <= 0) {
                    unset($_SESSION['carrito'][$id_producto]);
                }
            }
            break;

        // (Podríamos añadir 'actualizar' si quisiéramos un input de cantidad)
    }
}

// --- CASO 4: VACIAR TODO EL CARRITO (Lo usará carrito.php) ---
if ($accion == 'limpiar') {
    $_SESSION['carrito'] = [];
}

// 7. --- REDIRECCIÓN ---
// Redirigir al usuario de vuelta a la página de donde vino
// Si vino de 'gestionar_carrito.php' (error), lo mandamos al index.
$url_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
if (strpos($url_anterior, 'gestionar_carrito.php') !== false) {
    $url_anterior = 'index.php';
}

header('Location: ' . $url_anterior);
exit;
?>