<?php
// 1. Definimos el título y cargamos el header
$pagina_titulo = "Nosotros";
require 'includes/db_conexion.php'; 
require 'includes/header.php'; // Esto ya inicia la sesión
?>

<section class="about-hero-section">
    <div class="about-hero-grid">
        <div class="about-hero-text">
            <h1 class="about-hero-title">Nuestra Historia</h1>
            <p class="about-hero-subtitle">El corazón detrás de cada pieza.</p>
            
            <p>
                Fundada en 2020 con un sueño y una pasión por el detalle, Carlota's Jewelry nació del deseo de <strong>ofrecer</strong> joyería fina que no solo adorne, sino que también cuente una historia.
            </p>
            <p>
                Comenzamos como una pequeña tienda en línea, <strong>seleccionando cuidadosamente cada pieza</strong> de artesanos y diseñadores, inspirándonos en la belleza de lo cotidiano y en la elegancia atemporal. Hoy, mantenemos esa misma dedicación en cada joya que <strong>elegimos para ti</strong>.
            </p>
            </div>
        <div class="about-hero-image">
            <img src="https://via.placeholder.com/600x700.png?text=Foto+de+Carlota's+Jewelry" 
                 alt="Tienda de Carlota's Jewelry">
        </div>
    </div>
</section>

<section class="about-mv-section">
    <div class="about-grid">
    
        <div class="about-card">
            <div class="about-card-icon">🎯</div>
            <h3 class="about-card-title">Misión</h3>
            <p>
                Ofrecer joyería de alta calidad con diseños únicos y atemporales, creando piezas que celebren los momentos especiales de nuestros clientes.
            </p>
        </div>
        
        <div class="about-card">
            <div class="about-card-icon">👁️</div>
            <h3 class="about-card-title">Visión</h3>
            <p>
                Ser la marca de joyería en línea líder en México, reconocida por nuestra calidad curada y nuestro compromiso con la elegancia en los detalles.
            </p>
        </div>

    </div>
</section>

<section class="about-values-section">
    <h2 class="section-title">Nuestros Valores</h2>
    
    <div class="values-grid">
        
        <div class="value-card">
            <div class="value-icon">🌟</div>
            <h4 class="value-title">Calidad</h4>
            <p>Cada pieza es inspeccionada rigurosamente para asegurar la perfección.</p>
        </div>
        
        <div class="value-card">
            <div class="value-icon">❤️</div>
            <h4 class="value-title">Pasión</h4>
            <p>Amamos la joyería y esa pasión se refleja en nuestra selección.</p>
        </div>
        
        <div class="value-card">
            <div class="value-icon">🤝</div>
            <h4 class="value-title">Integridad</h4>
            <p>Somos honestos y transparentes en cada una de nuestras interacciones.</p>
        </div>
        
        <div class="value-card">
            <div class="value-icon">✨</div>
            <h4 class="value-title">Elegancia</h4>
            <p>Creemos en la belleza de la simplicidad y el diseño atemporal.</p>
        </div>
        
        <div class="value-card">
            <div class="value-icon">😊</div>
            <h4 class="value-title">Compromiso</h4>
            <p>Nos dedicamos por completo a la felicidad de nuestros clientes.</p>
        </div>

    </div>
</section>


<?php
// 2. Incluimos el footer
require 'includes/footer.php';
?>