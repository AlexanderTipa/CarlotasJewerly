<?php
// 1. Definimos el título y cargamos el header
$pagina_titulo = "Gestión de Pedidos";
require '../includes/db_conexion.php';
require '../includes/header_admin.php'; // Asegúrate de usar el header_admin

// 2. ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// 3. Lógica para obtener todos los pedidos
try {
    // 3.1. Obtener los pedidos principales
    $sql = "SELECT * FROM pedidos ORDER BY fecha_pedido DESC";
    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll();

    // 3.2. Obtener los detalles de todos los pedidos
    $sql_detalles = "SELECT dp.id_pedido, dp.cantidad, a.nombre 
                     FROM detalle_pedidos dp
                     JOIN accesorios a ON dp.id_accesorio = a.id_accesorio";
    $stmt_detalles = $pdo->query($sql_detalles);
    $todos_los_detalles = $stmt_detalles->fetchAll();
    
    // 3.3. Agrupar los detalles por ID de pedido
    $detalles_agrupados = [];
    foreach ($todos_los_detalles as $detalle) {
        $detalles_agrupados[ $detalle['id_pedido'] ][] = $detalle;
    }

} catch (PDOException $e) {
    $error = "Error al cargar los pedidos: " . $e->getMessage();
    $pedidos = [];
    $detalles_agrupados = []; 
}
?>

<div class="admin-header">
    <h1 class="admin-titulo">Historial de Pedidos</h1>
</div>

<?php
// Mensajes de retroalimentación
if (isset($_GET['actualizado'])) echo '<div class="alerta alerta-exito">Pedido actualizado a "En proceso de envío".</div>';
if (isset($_GET['completado'])) echo '<div class="alerta alerta-exito">¡Pedido marcado como completado y archivado!</div>';
if (isset($_GET['error'])) echo '<div class="alerta alerta-error">Hubo un error al actualizar el pedido.</div>';
if (isset($error)) echo '<div class="alerta alerta-error">' . $error . '</div>';
?>

<div class="tabla-responsiva-contenedor">
    <table class="tabla-responsiva tabla-pedidos">
        
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Cliente y Productos</th>
                <th>Total</th>
                <th>Envío</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($pedidos)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">
                        No hay pedidos registrados todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td data-label="ID Pedido:">
                            <strong>#<?php echo $pedido['id_pedido']; ?></strong>
                        </td>
                        
                        <td data-label="Fecha:">
                            <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                        </td>
                        
                        <td data-label="Cliente y Productos:">
                            <strong><?php echo htmlspecialchars($pedido['envio_nombre'] . ' ' . $pedido['envio_apellido']); ?></strong>
                            <br><?php echo htmlspecialchars($pedido['envio_direccion']); ?>
                            <br><?php echo htmlspecialchars($pedido['envio_colonia']); ?>, <?php echo htmlspecialchars($pedido['envio_cp']); ?>
                            <br><?php echo htmlspecialchars($pedido['envio_municipio']); ?>, <?php echo htmlspecialchars($pedido['envio_estado']); ?>
                            <br>Tel: <?php echo htmlspecialchars($pedido['envio_telefono']); ?>
                            
                            <?php if (!empty($pedido['envio_indicaciones'])): ?>
                                <div class="pedido-indicaciones">
                                    <strong>Indicaciones:</strong>
                                    <?php echo htmlspecialchars($pedido['envio_indicaciones']); ?>
                                </div>
                            <?php endif; ?>
                            <hr class="pedido-detalle-separador">
                            <strong class="pedido-detalle-titulo">Productos:</strong>
                            <ul class="pedido-detalle-lista">
                                <?php 
                                if (isset($detalles_agrupados[$pedido['id_pedido']])):
                                    foreach ($detalles_agrupados[$pedido['id_pedido']] as $detalle):
                                ?>
                                    <li>
                                        (<?php echo $detalle['cantidad']; ?>x) 
                                        <?php echo htmlspecialchars($detalle['nombre']); ?>
                                    </li>
                                <?php 
                                    endforeach;
                                else:
                                    echo '<li>Error: No se encontraron productos.</li>';
                                endif;
                                ?>
                            </ul>
                        </td>
                        
                        <td data-label="Total:">
                            $<?php echo number_format($pedido['total'], 2); ?> MXN
                        </td>

                        <td data-label="Envío:">
                            <?php echo htmlspecialchars($pedido['envio_tipo']); ?>
                            <br>$<?php echo number_format($pedido['envio_costo'], 2); ?> MXN
                        </td>
                        
                        <td data-label="Estado:">
                            <span class="estado-pedido estado-<?php echo str_replace(' ', '-', $pedido['estado_pedido']); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $pedido['estado_pedido']))); ?>
                            </span>
                        </td>
                        
                        <td data-label="Acción:">
                            <?php if ($pedido['estado_pedido'] == 'pendiente'): ?>
                                <form action="actualizar_pedido.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                    <button type="submit" class="btn btn-accion-pagado">
                                        Marcar Pagado
                                    </button>
                                </form>
                                
                            <?php elseif ($pedido['estado_pedido'] == 'en proceso de envio'): ?>
                                <form action="completar_pedido.php" method="POST" 
                                      onsubmit="return confirmarCompletar();" 
                                      style="display: inline;">
                                      
                                    <input type="hidden" name="id_pedido" 
                                           value="<?php echo $pedido['id_pedido']; ?>">
                                           
                                    <button type="submit" class="btn btn-accion-completar">
                                        Completado
                                    </button>
                                </form>
                                
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require '../includes/footer_admin.php';
?>