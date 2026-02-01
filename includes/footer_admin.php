</main> <footer class="site-footer">
            <div class="footer-content">
                
                <div class="footer-section">
                    <h4>Carlota's Jewelry</h4>
                    <p>Panel de Administración</p>
                </div>
                
                <div class="footer-section">
                    <h4>Navegación</h4>
                    <ul>
                        <li><a href="../admin/index.php">Inicio (Dashboard)</a></li>
                        <li><a href="../admin/accesorios.php">Gestionar Accesorios</a></li>
                        <li><a href="../admin/pedidos.php">Ver Pedidos</a></li>
                        <li><a href="../admin/punto_venta.php">Punto de Venta (POS)</a></li>
                        <li><a href="../admin/gestionar_admins.php">Gestionar Admins</a></li>
                    </ul>
                </div>

            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Carlota's Jewelry. Todos los derechos reservados.</p>
            </div>
        </footer>

        <script src="../assets/js/main.js"></script>
        
        <?php
        if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            echo '<script src="../assets/js/admin.js"></script>';
        }
        ?>

    </body> 
</html>