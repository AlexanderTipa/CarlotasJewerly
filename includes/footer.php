</main> <footer class="site-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Carlota's Jewelry</h4>
                    <p>El lugar favorito de las girls.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Navegación</h4>
                    <ul>
                        <li><a href="./index.php">Catálogo</a></li>
                        <li><a href="./nosotros.php">Nosotros</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Síguenos</h4>
                    <a href="https://www.instagram.com/carlotasjewelry_/">Instagram</a> | 
                    <a href="https://www.facebook.com/profile.php?id=61552801936909">Facebook</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Carlota's Jewelry. Todos los derechos reservados.</p>
            </div>
        </footer>

        <script src="./assets/js/main.js"></script>
        
        <?php
        // Un pequeño truco: si estamos en una página de admin,
        // podríamos cargar también el script de admin.
        if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            echo '<script src="./assets/js/admin.js"></script>';
        }
        ?>

    </body> </html> ```

