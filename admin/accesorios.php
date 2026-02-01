<?php
$pagina_titulo = "Gestionar Accesorios";
require '../includes/db_conexion.php';
require '../includes/header_admin.php';

// ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// Lógica para obtener todos los accesorios
try {
    // El admin ve TODO (incluyendo ocultos), por eso no usamos WHERE visible = 1
    $sql = "SELECT a.*, c.nombre as nombre_categoria 
            FROM accesorios a
            LEFT JOIN categorias c ON a.id_categoria = c.id_categoria
            ORDER BY a.id_accesorio DESC"; 
    
    $stmt = $pdo->query($sql);
    $accesorios = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Error al cargar los accesorios: " . $e->getMessage();
    $accesorios = [];
}
?>

<div class="admin-header">
    <h1 class="admin-titulo">Catálogo de Accesorios</h1>
    <a href="manejar_accesorio.php" class="btn btn-principal">Añadir Nuevo Accesorio</a>
</div>

<?php
if (isset($_GET['creado'])) echo '<div class="alerta alerta-exito">Accesorio añadido con éxito.</div>';
if (isset($_GET['actualizado'])) echo '<div class="alerta alerta-exito">Accesorio actualizado con éxito.</div>';
if (isset($_GET['eliminado'])) echo '<div class="alerta alerta-exito">Accesorio eliminado con éxito.</div>';
if (isset($error)) echo '<div class="alerta alerta-error">' . $error . '</div>';
?>

<div class="tabla-responsiva-contenedor">
    <table class="tabla-responsiva">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th> <th>Estado</th> <th>Acciones</th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($accesorios)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">
                        No hay accesorios añadidos todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($accesorios as $accesorio): ?>
                    <tr>
                        <td data-label="Imagen:">
                            <img src="../uploads/<?php echo htmlspecialchars($accesorio['imagen_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($accesorio['nombre']); ?>"
                                 class="tabla-imagen-producto"
                                 onerror="this.src='https://via.placeholder.com/100x100.png?text=Sin+Imagen';">
                        </td>
                        
                        <td data-label="Nombre:">
                            <?php echo htmlspecialchars($accesorio['nombre']); ?>
                        </td>
                        
                        <td data-label="Categoría:">
                            <?php echo htmlspecialchars($accesorio['nombre_categoria']); ?>
                        </td>
                        
                        <td data-label="Precio:">
                            $<?php echo number_format($accesorio['precio'], 2); ?> MXN
                        </td>
                        
                        <td data-label="Stock:">
                            <?php if ($accesorio['stock'] > 0): ?>
                                <strong><?php echo $accesorio['stock']; ?></strong>
                            <?php else: ?>
                                <span class="estado-pendiente" style="padding: 5px 8px; font-size: 0.75rem;">Agotado</span>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Estado:">
                            <?php if ($accesorio['visible'] == 1): ?>
                                <span class="estado-completado">Visible</span>
                            <?php else: ?>
                                <span class="estado-oculto">Oculto</span>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Acciones:">
                            <div class="tabla-acciones">
                                <a href="manejar_accesorio.php?id=<?php echo $accesorio['id_accesorio']; ?>" 
                                   class="btn btn-accion-editar">
                                   Editar
                                </a>
                                
                                <form action="eliminar_accesorio.php" method="POST" 
                                      onsubmit="return confirmarEliminar();" 
                                      style="display: inline;">
                                      
                                    <input type="hidden" name="id_accesorio" 
                                           value="<?php echo $accesorio['id_accesorio']; ?>">
                                           
                                    <button type="submit" class="btn btn-accion-eliminar">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
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