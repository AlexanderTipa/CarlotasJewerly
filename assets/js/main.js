// Espera a que todo el HTML esté cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {

    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const body = document.body;

    // Si el botón de hamburguesa existe en la página...
    if (hamburgerBtn && mobileMenu) {
        
        // Añade un "escuchador de clics" al botón
        hamburgerBtn.addEventListener('click', () => {
            
            // Alterna la clase 'is-active' en el botón (para animarlo a 'X')
            hamburgerBtn.classList.toggle('is-active');
            
            // Alterna la clase 'is-active' en el menú (para mostrarlo/ocultarlo)
            mobileMenu.classList.toggle('is-active');
            
            // Alterna la clase 'no-scroll' en el body (para evitar que la página se mueva detrás)
            body.classList.toggle('no-scroll');
            
            // Actualiza los atributos ARIA para accesibilidad
            const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';
            hamburgerBtn.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.setAttribute('aria-hidden', isExpanded);
        });
    }

});