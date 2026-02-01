<?php
// 1. Iniciar sesión y conexión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db_conexion.php';

// 2. ¡SEGURIDAD!
// 2.1. Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}
// 2.2. ¿Está logueado?
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?origen=pagar');
    exit;
}
// 2.3. ¿El carrito está vacío?
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
if (empty($carrito)) {
    header('Location: carrito.php');
    exit;
}
// 2.4. ¿El usuario envió todos los datos?
$campos_requeridos = [
    'nombre', 'apellido', 'direccion', 'colonia', 'cp', 
    'estado', 'municipio', 'telefono', 'opcion_envio', 'metodo_pago'
];
foreach ($campos_requeridos as $campo) {
    if (empty($_POST[$campo])) {
        // Si falta un campo, redirigir con error
        header('Location: pagar.php?error=campos');
        exit;
    }
}

// 3. --- VALIDACIÓN DE COSTOS (Lado del Servidor) ---
$subtotal_servidor = 0;
foreach ($carrito as $item) {
    $subtotal_servidor += $item['precio'] * $item['cantidad'];
}

$costo_envio_servidor = 0;
$tipo_envio = $_POST['opcion_envio'];

if ($tipo_envio == 'MexPost México 2-3 Semanas') {
    $costo_envio_servidor = 75.00;
} elseif ($tipo_envio == 'Fedex México 2 a 5 Días Hábiles') {
    $costo_envio_servidor = 280.00;
} else {
    header('Location: pagar.php?error=envio');
    exit;
}

$total_servidor = $subtotal_servidor + $costo_envio_servidor;

// 4. --- INICIAR TRANSACCIÓN DE BASE DE DATOS ---
try {
    $pdo->beginTransaction();

    // 4.1. Recolectar TODOS los datos
    $id_usuario = $_SESSION['id_usuario'];
    
    $envio_nombre = $_POST['nombre'];
    $envio_apellido = $_POST['apellido'];
    $envio_direccion = $_POST['direccion'];
    $envio_colonia = $_POST['colonia'];
    $envio_cp = $_POST['cp'];
    $envio_num_interior = $_POST['num_interior'] ?? null;
    $envio_estado = $_POST['estado'];
    $envio_municipio = $_POST['municipio'];
    $envio_telefono = $_POST['telefono'];
    $envio_indicaciones = $_POST['indicaciones'] ?? null;

    // 4.2. Insertar el Pedido Principal (Tabla 'pedidos')
    $sql_pedido = "INSERT INTO pedidos (
                        id_usuario, estado_pedido, total, 
                        envio_nombre, envio_apellido, envio_direccion, envio_colonia, envio_cp, 
                        envio_num_interior, envio_estado, envio_municipio, envio_telefono, 
                        envio_indicaciones, envio_tipo, envio_costo
                    ) VALUES (
                        ?, 'pendiente', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";
    
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([
        $id_usuario, $total_servidor,
        $envio_nombre, $envio_apellido, $envio_direccion, $envio_colonia, $envio_cp,
        $envio_num_interior, $envio_estado, $envio_municipio, $envio_telefono,
        $envio_indicaciones, $tipo_envio, $costo_envio_servidor
    ]);
    
    // 4.3. Obtener el ID del pedido que acabamos de crear
    $id_pedido_nuevo = $pdo->lastInsertId();

    // 4.4. Insertar los Productos (Tabla 'detalle_pedidos') Y ACTUALIZAR STOCK
    $sql_detalle = "INSERT INTO detalle_pedidos (id_pedido, id_accesorio, cantidad, precio_unitario) 
                    VALUES (?, ?, ?, ?)";
    $stmt_detalle = $pdo->prepare($sql_detalle);
    
    // ===== PREPARAMOS LA CONSULTA PARA ACTUALIZAR EL STOCK =====
    $stmt_stock = $pdo->prepare("UPDATE accesorios SET stock = stock - ? WHERE id_accesorio = ?");
    
    foreach ($carrito as $id_accesorio => $item) {
        // 4.4.1. Insertar el detalle del pedido
        $stmt_detalle->execute([
            $id_pedido_nuevo,
            $id_accesorio,
            $item['cantidad'],
            $item['precio']
        ]);
        
        // ===== 4.4.2. (¡NUEVO!) Actualizar el stock del producto =====
        $stmt_stock->execute([
            $item['cantidad'], // Cantidad a restar
            $id_accesorio      // ID del producto a actualizar
        ]);
    }
    
    // 4.5. ¡ÉXITO! Confirmar todos los cambios en la BD
    $pdo->commit();

    // 5. Limpiar el carrito y guardar el ID del pedido
    unset($_SESSION['carrito']);
    $_SESSION['ultimo_pedido_id'] = $id_pedido_nuevo;

    // 6. Redirigir a la página de éxito
    header('Location: pedido_exitoso.php');
    exit;

} catch (Exception $e) {
    // 7. ¡FALLO! Revertir todos los cambios
    $pdo->rollBack();
    
    // Redirigir de vuelta a pagar con un error
    // (Podríamos pasar el mensaje de error para depurar: $e->getMessage())
    header('Location: pagar.php?error=db');
    exit;
}
?>