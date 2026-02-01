<?php
$pagina_titulo = "Catálogo - Carlota's Jewelry";
require 'includes/db_conexion.php';
require 'includes/header.php'; // Usamos el header de cliente

try {
    // ===== SQL ACTUALIZADO =====
    // Ahora solo selecciona productos que sean VISIBLES
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
    $categorias = $stmt_cats->fetchAll();

    $stmt_accs = $pdo->query("SELECT * FROM accesorios WHERE visible = 1 ORDER BY id_categoria, nombre");
    $accesorios_db = $stmt_accs->fetchAll();

    $accesorios_agrupados = [];
    foreach ($accesorios_db as $accesorio) {
        $accesorios_agrupados[ $accesorio['id_categoria'] ][] = $accesorio;
    }

} catch (PDOException $e) {
    echo "<p class='alerta alerta-error'>Error al cargar el catálogo: " . $e->getMessage() . "</p>";
    require 'includes/footer.php';
    exit; 
}
?>

<div class="catalogo-bienvenida">
    <h1 class="catalogo-titulo-principal">Nuestras Colecciones</h1>
    <p>Descubre la pieza perfecta para cada ocasión.</p>
</div>

<?php
if (empty($categorias)): ?>
    <p class='alerta'>No hay categorías de productos para mostrar todavía.</p>
<?php else:
    foreach ($categorias as $categoria): ?>
        
        <section class="catalogo-seccion">
            <h2 class="catalogo-titulo-seccion"><?php echo htmlspecialchars($categoria['nombre']); ?></h2>
            <hr class="separador-seccion">
            
            <?php
            if (!empty($accesorios_agrupados[$categoria['id_categoria']])):
            ?>
                <div class="catalogo-grid">
                    <?php foreach ($accesorios_agrupados[$categoria['id_categoria']] as $producto): ?>
                        
                        <div class="producto-card">
                            <div class="producto-imagen">
                                <img src="uploads/<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                     onerror="this.src='https://via.placeholder.com/300x300.png?text=Carlota%27s+Jewelry';">
                            </div>
                            
                            <div class="producto-info">
                                <h3 class="producto-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="producto-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p class="producto-precio">
                                    $<?php echo number_format($producto['precio'], 2, '.', ','); ?> MXN
                                </p>
                                
                                <?php if ($producto['stock'] > 0): ?>
                                    
                                    <p class="producto-stock">
                                        Disponibles: <?php echo $producto['stock']; ?>
                                    </p>
                                
                                    <?php if (isset($_SESSION['id_usuario'])): ?>
                                        <a href="gestionar_carrito.php?accion=agregar&id=<?php echo $producto['id_accesorio']; ?>" 
                                           class="btn btn-principal">
                                            Agregar al Carrito
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-principal">
                                            Agregar al Carrito
                                        </a>
                                    <?php endif; ?>
                                
                                <?php else: ?>
                                
                                    <p class="producto-stock-agotado">
                                        No disponible por el momento
                                    </p>
                                    <a class="btn btn-principal btn-agotado" disabled>
                                        Agotado
                                    </a>
                                
                                <?php endif; ?>
                                </div>
                        </div> <?php endforeach; // Fin bucle de productos ?>
                </div> <?php
            else:
            ?>
                <p>No hay <?php echo htmlspecialchars($categoria['nombre']); ?> disponibles por el momento.</p>
            <?php
            endif; 
            ?>
        </section>

    <?php endforeach; // Fin bucle de categorías
endif; 
?>

<?php
require 'includes/footer.php';
?>