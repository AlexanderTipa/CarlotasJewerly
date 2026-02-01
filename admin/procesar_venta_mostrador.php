<?php
session_start();
require '../includes/db_conexion.php';

// Verificamos que venga del formulario y que haya productos
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SESSION['pos_carrito'])) {
    header('Location: punto_venta.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. ID DEL USUARIO MOSTRADOR
    // Asegúrate de que este '2' sea el ID correcto en tu base de datos
    $id_usuario_mostrador = 2; 
    
    $total = $_POST['total'];

    // 2. OBTENER DATOS DEL CLIENTE
    // Usamos el operador ternario para ver si enviaron datos o usamos los genéricos
    $nombre   = !empty($_POST['cliente_nombre'])   ? trim($_POST['cliente_nombre'])   : 'Venta';
    $apellido = !empty($_POST['cliente_apellido']) ? trim($_POST['cliente_apellido']) : 'Mostrador';
    $telefono = !empty($_POST['cliente_telefono']) ? trim($_POST['cliente_telefono']) : '0000000000';
    
    // 3. CREAR PEDIDO
    // Nota: Aquí especificamos el orden exacto de las columnas para evitar errores
    $sql_pedido = "INSERT INTO pedidos (
        id_usuario, 
        estado_pedido, 
        total, 
        envio_nombre, 
        envio_apellido, 
        envio_telefono, 
        envio_direccion, 
        envio_colonia, 
        envio_cp, 
        envio_estado, 
        envio_municipio, 
        envio_tipo, 
        envio_costo
    ) VALUES (
        ?,              /* id_usuario */
        'completado',   /* estado_pedido */
        ?,              /* total */
        ?,              /* envio_nombre (VARIABLE) */
        ?,              /* envio_apellido (VARIABLE) */
        ?,              /* envio_telefono (VARIABLE) */
        'Tienda Física',/* envio_direccion */
        '-',            /* envio_colonia */
        '00000',        /* envio_cp */
        '-',            /* envio_estado */
        '-',            /* envio_municipio */
        'Entregado en Tienda', /* envio_tipo */
        0               /* envio_costo */
    )";
    
    $stmt = $pdo->prepare($sql_pedido);
    
    // Pasamos las variables en el mismo orden que los signos de interrogación (?)
    $stmt->execute([
        $id_usuario_mostrador, 
        $total,
        $nombre,    // Se guarda en envio_nombre
        $apellido,  // Se guarda en envio_apellido
        $telefono   // Se guarda en envio_telefono
    ]);
    
    $id_pedido = $pdo->lastInsertId();

    // 4. INSERTAR DETALLES Y RESTAR STOCK
    $sql_detalle = "INSERT INTO detalle_pedidos (id_pedido, id_accesorio, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_det = $pdo->prepare($sql_detalle);
    
    $sql_stock = "UPDATE accesorios SET stock = stock - ? WHERE id_accesorio = ?";
    $stmt_stock = $pdo->prepare($sql_stock);

    foreach ($_SESSION['pos_carrito'] as $item) {
        // Guardar detalle
        $stmt_det->execute([$id_pedido, $item['id'], $item['cantidad'], $item['precio']]);
        
        // Restar stock
        $stmt_stock->execute([$item['cantidad'], $item['id']]);
    }

    $pdo->commit();
    
    // Limpiar carrito POS
    $_SESSION['pos_carrito'] = [];
    
    // Redirigir con éxito
    header('Location: pedidos.php?completado=exito'); 

} catch (Exception $e) {
    $pdo->rollBack();
    // Mostrar el error si algo falla
    die("Error al procesar venta: " . $e->getMessage());
}
?>