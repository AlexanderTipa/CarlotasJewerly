<?php
// 1. Definimos el título y cargamos el header
$pagina_titulo = "Carrito de Compras";
require 'includes/db_conexion.php'; 
require 'includes/header.php'; // Esto ya inicia la sesión

// 2. ¡SEGURIDAD!
// Esta página es solo para usuarios logueados.
// Si un invitado llega aquí, lo mandamos a login.
if (!isset($_SESSION['id_usuario'])) {
    // Guardamos 'carrito' en la URL para saber a dónde redirigir después del login
    header('Location: login.php?origen=carrito');
    exit;
}

// 3. Lógica del Carrito
// Asignar el carrito (o un array vacío si no existe) a una variable
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$subtotal_general = 0; // Inicializar el subtotal total

?>

<div class="admin-header"> <h1 class="admin-titulo">Tu Carrito de Compras</h1>
</div>

<?php if (empty($carrito)): ?>
    
    <div class="alerta alerta-info" style="text-align:center; padding: 30px;">
        <p style="font-size: 1.1rem; margin: 0 0 20px 0;">Tu carrito está vacío.</p>
        <a href="index.php" class="btn btn-principal btn-pagar"> Ver Catálogo
        </a>
    </div>

<?php else: ?>

    <div class="tabla-responsiva-contenedor">
        <table class="tabla-responsiva tabla-carrito">
            
            <thead>
                <tr>
                    <th colspan="2">Producto</th>
                    <th>Precio Unit.</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Quitar</th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach ($carrito as $id_producto => $item): ?>
                    <?php
                        // Calcular subtotal por item
                        $subtotal_item = $item['precio'] * $item['cantidad'];
                        // Sumar al subtotal general
                        $subtotal_general += $subtotal_item;
                    ?>
                    <tr>
                        <td data-label="Imagen:">
                            <img src="uploads/<?php echo htmlspecialchars($item['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                 class="tabla-imagen-producto" 
                                 onerror="this.src='https://via.placeholder.com/100x100.png?text=Sin+Imagen';">
                        </td>
                        
                        <td data-label="Producto:">
                            <span class="tabla-link-producto"><?php echo htmlspecialchars($item['nombre']); ?></span>
                        </td>
                        
                        <td data-label="Precio:">
                            $<?php echo number_format($item['precio'], 2); ?>
                        </td>
                        
                        <td data-label="Cantidad:">
                            <div class="control-cantidad">
                                <a href="gestionar_carrito.php?accion=restar&id=<?php echo $id_producto; ?>" class="btn-cantidad" aria-label="Restar uno">-</a>
                                <span class="cantidad-numero"><?php echo $item['cantidad']; ?></span>
                                <a href="gestionar_carrito.php?accion=agregar&id=<?php echo $id_producto; ?>" class="btn-cantidad" aria-label="Sumar uno">+</a>
                            </div>
                        </td>
                        
                        <td data-label="Subtotal:">
                            <strong>$<?php echo number_format($subtotal_item, 2); ?></strong>
                        </td>
                        
                        <td data-label="Quitar:">
                            <a href="gestionar_carrito.php?accion=eliminar&id=<?php echo $id_producto; ?>" class="btn-eliminar-item" aria-label="Eliminar producto">
                                &times; </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> <div class="carrito-summary-contenedor">
        
        <div class="carrito-acciones">
            <a href="index.php" class="btn btn-accion-editar" style="background: var(--color-texto-secundario); border-color: var(--color-texto-secundario);">
                &larr; Seguir Comprando
            </a>
            <a href="gestionar_carrito.php?accion=limpiar" class="btn btn-accion-eliminar">
                Limpiar Carrito
            </a>
        </div>

        <div class="carrito-totales">
            <div class="total-fila">
                <span class="total-label">Subtotal:</span>
                <span class="total-valor">$<?php echo number_format($subtotal_general, 2); ?> MXN</span>
            </div>
            
            <div class="total-fila total-general">
                <span class="total-label">Total:</span>
                <span class="total-valor">$<?php echo number_format($subtotal_general, 2); ?> MXN</span>
            </div>
            
            <p class="total-aviso">El costo de envío se calculará en el siguiente paso.</p>
            
            <a href="pagar.php" class="btn btn-principal btn-pagar">
                Proceder al Pago
            </a>
        </div>
        
    </div>

<?php endif; ?>

<?php
// 4. Incluimos el footer
require 'includes/footer.php';
?>