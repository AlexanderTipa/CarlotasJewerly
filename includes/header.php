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
    <title><?php echo isset($pagina_titulo) ? $pagina_titulo . " - Carlota's Jewelry" : "Carlota's Jewelry"; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

    <header class="navbar">
        <div class="navbar-logo">
            <a href="./index.php" class="logo">Carlota's Jewelry</a>
        </div>
        
        <nav class="navbar-links">
            <ul>
                <li><a href="./index.php">Catálogo</a></li>
                <li><a href="./nosotros.php">Nosotros</a></li>
                <li><a href="./carrito.php">Carrito 🛒</a></li>
                
                <?php 
                if (isset($_SESSION['id_usuario'])) {
                    // --- Usuario CON sesión iniciada ---
                    
                    // ===== INICIO DE LA CORRECCIÓN =====
                    // Ya no mostramos el botón de "Panel Admin" aquí.
                    // Solo mostramos el enlace de Salir para todos los logueados.
                    // ===== FIN DE LA CORRECCIÓN =====
                    
                    // (Aquí podrías añadir "Mis Pedidos" si quisieras en el futuro)
                    // echo '<li><a href="/carlotas_jewelry/mis_pedidos.php">Mis Pedidos</a></li>';
                    
                    // Enlace para Cerrar Sesión
                    echo '<li><a href="./logout.php" class="btn">Salir</a></li>';

                } else {
                    // --- Usuario SIN sesión (invitado) ---
                    echo '<li><a href="./login.php" class="btn btn-principal">Login / Registro</a></li>';
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
                <li><a href="./index.php">Catálogo</a></li>
                <li><a href="./nosotros.php">Nosotros</a></li>
                <li><a href="./carrito.php">Carrito 🛒</a></li>
                
                <hr class="mobile-menu-divider">
                
                <?php 
                if (isset($_SESSION['id_usuario'])) {
                    // --- Usuario CON sesión iniciada ---
                    // (Aquí también quitamos el enlace de Admin)
                    echo '<li><a href="./logout.php">Salir</a></li>';
                } else {
                    // --- Usuario SIN sesión (invitado) ---
                    echo '<li><a href="./login.php">Login / Registro</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>

    <main class="container">