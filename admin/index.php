<?php
// 1. Definimos el título y cargamos el header
$pagina_titulo = "Panel de Administración";
require '../includes/db_conexion.php'; // Salimos de /admin/
require '../includes/header_admin.php';

// 2. ¡SEGURIDAD!
// Verificamos que el usuario esté logueado Y que sea 'admin'
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    // Si no lo es, lo enviamos al login
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// 3. Saludamos al administrador
$nombre_admin = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Admin';

?>

<div class="admin-header">
    <h1 class="admin-titulo">¡Bienvenido, <?php echo $nombre_admin; ?>!</h1>
    <p class="admin-subtitulo">Desde aquí puedes gestionar tu tienda "Carlota's Jewelry".</p>
</div>

<div class="dashboard-grid">

    <a href="accesorios.php" class="dashboard-card">
        <div class="card-icono">
            💍
        </div>
        <div class="card-cuerpo">
            <h3 class="card-titulo">Gestionar Accesorios</h3>
            <p class="card-descripcion">Añade, edita o elimina productos de tu catálogo.</p>
        </div>
    </a>

    <a href="pedidos.php" class="dashboard-card">
        <div class="card-icono">
            📦
        </div>
        <div class="card-cuerpo">
            <h3 class="card-titulo">Ver Pedidos</h3>
            <p class="card-descripcion">Revisa los pedidos pendientes y actualiza su estado.</p>
        </div>
    </a>
    
    <a href="gestionar_admins.php" class="dashboard-card">
        <div class="card-icono">
            🧑‍💼
        </div>
        <div class="card-cuerpo">
            <h3 class="card-titulo">Gestionar Admins</h3>
            <p class="card-descripcion">Añade, edita o elimina las cuentas de otros administradores.</p>
        </div>
    </a>
    
    <a href="punto_venta.php" class="dashboard-card">
        <div class="card-icono">
            🏪
        </div>
        <div class="card-cuerpo">
            <h3 class="card-titulo">Punto de Venta</h3>
            <p class="card-descripcion">Caja registradora para ventas físicas. Escanea y cobra.</p>
        </div>
    </a>
    </div>

<?php
// 4. Incluimos el footer
require '../includes/footer_admin.php';
?>