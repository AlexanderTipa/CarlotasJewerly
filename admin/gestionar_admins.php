<?php
$pagina_titulo = "Gestionar Administradores";
require '../includes/db_conexion.php';
require '../includes/header_admin.php';

// ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// Lógica para obtener todos los admins
try {
    $stmt = $pdo->query("SELECT id_usuario, nombre, apellido, correo FROM usuarios WHERE tipo_usuario = 'admin' ORDER BY nombre");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al cargar administradores: " . $e->getMessage();
    $admins = [];
}
?>

<div class="admin-header">
    <h1 class="admin-titulo">Administradores</h1>
    <a href="manejar_admin.php" class="btn btn-principal">Añadir Nuevo Admin</a>
</div>

<?php
// Mensajes de retroalimentación
if (isset($_GET['creado'])) echo '<div class="alerta alerta-exito">Administrador creado con éxito.</div>';
if (isset($_GET['actualizado'])) echo '<div class="alerta alerta-exito">Administrador actualizado con éxito.</div>';
if (isset($_GET['eliminado'])) echo '<div class="alerta alerta-exito">Administrador eliminado con éxito.</div>';
if (isset($_GET['error']) && $_GET['error'] == 'self_delete') {
    echo '<div class="alerta alerta-error">Error: No puedes eliminar tu propia cuenta.</div>';
}
if (isset($error)) echo '<div class="alerta alerta-error">' . $error . '</div>';
?>

<div class="tabla-responsiva-contenedor">
    <table class="tabla-responsiva">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($admins)): ?>
                <tr><td colspan="4" style="text-align:center;">No hay administradores.</td></tr>
            <?php else: ?>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td data-label="ID:"><?php echo $admin['id_usuario']; ?></td>
                        <td data-label="Nombre:"><?php echo htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']); ?></td>
                        <td data-label="Correo:"><?php echo htmlspecialchars($admin['correo']); ?></td>
                        <td data-label="Acciones:">
                            <div class="tabla-acciones">
                                <a href="manejar_admin.php?id=<?php echo $admin['id_usuario']; ?>" class="btn btn-accion-editar">
                                   Editar
                                </a>
                                
                                <?php 
                                // ¡SEGURIDAD! No mostrar el botón de eliminar para el usuario actual
                                if ($admin['id_usuario'] != $_SESSION['id_usuario']): 
                                ?>
                                <form action="eliminar_admin.php" method="POST" 
                                      onsubmit="return confirmarEliminarAdmin();" 
                                      style="display: inline;">
                                      
                                    <input type="hidden" name="id_admin" value="<?php echo $admin['id_usuario']; ?>">
                                    <button type="submit" class="btn btn-accion-eliminar">
                                        Eliminar
                                    </button>
                                </form>
                                <?php else: ?>
                                    <span class="btn-deshabilitado">Eliminar</span>
                                <?php endif; ?>
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