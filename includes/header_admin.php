<?php
// 1. Iniciamos la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pagina_titulo) ? $pagina_titulo . " - Admin" : "Admin Panel"; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="navbar">
        <div class="navbar-logo">
            <a href="../admin/index.php" class="logo">Carlota's Jewelry (Admin)</a>
        </div>
        
        <nav class="navbar-links">
            <ul>
                <li><a href="../admin/index.php">Inicio</a></li>
                <li><a href="../admin/accesorios.php">Accesorios</a></li>
                <li><a href="../admin/pedidos.php">Pedidos</a></li>
                <li><a href="../admin/punto_venta.php">Punto de Venta</a></li>
                <li><a href="../admin/gestionar_admins.php">Admins</a></li>
                
                <?php 
                if (isset($_SESSION['id_usuario'])) {
                    echo '<li><a href="../logout.php" class="btn btn-principal">Salir</a></li>';
                }
                ?>
            </ul>
        </nav>
        
        <button id="hamburger-btn" class="hamburger-btn" aria-label="Abrir menú" aria-expanded="false">
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>
        
    </header>

    <div id="mobile-menu" class="mobile-menu" aria-hidden="true">
        <nav>
            <ul>
                <li><a href="../admin/index.php">Inicio</a></li>
                <li><a href="../admin/accesorios.php">Accesorios</a></li>
                <li><a href="../admin/pedidos.php">Pedidos</a></li>
                <li><a href="../admin/punto_venta.php">Punto de Venta</a></li>
                <li><a href="../admin/gestionar_admins.php">Admins</a></li>
                
                <hr class="mobile-menu-divider">
                
                <?php 
                if (isset($_SESSION['id_usuario'])) {
                    echo '<li><a href="../logout.php">Salir</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>

    <main class="container">