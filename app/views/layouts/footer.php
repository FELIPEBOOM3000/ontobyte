<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/layouts/footer.php
 * ============================================================================
 * Propósito: Pie de página corporativo de OntoByte. Contiene los enlaces
 *            de navegación institucional, los servicios destacados,
 *            los datos de contacto de la empresa y las redes sociales.
 *            Se incluye en todas las páginas del sitio web.
 * ============================================================================
 */
?>
<footer class="site-footer">
    <div class="footer-container">

        <!-- Sección 1: Marca y descripción de la empresa -->
        <div class="footer-brand">
            <!-- Logo corporativo (LOGO_ONTOBYTE de public/img/ con respaldo logo.svg; 48px max por CSS) -->
            <a href="<?php echo BASE_URL; ?>/" class="brand footer-brand-logo">
                <img src="<?php echo IMG_LOGO; ?>" alt="OntoByte Logo"
                     class="brand-logo-img brand-logo-img-lg" loading="lazy" width="48" height="48">
                <span class="brand-text">
                    <span class="brand-name">Onto<em>Byte</em></span>
                </span>
            </a>
            <p class="footer-description">
                Empresa de base tecnológica y humanista. Harmonia entre la precisión del código y el propósito de las personas.
            </p>
            <!-- Redes sociales de la empresa -->
            <div class="footer-socials">
                <a href="#" aria-label="LinkedIn" class="social-link"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
                <a href="#" aria-label="GitHub" class="social-link"><i class="fab fa-github" aria-hidden="true"></i></a>
                <a href="#" aria-label="Twitter" class="social-link"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                <a href="#" aria-label="Instagram" class="social-link"><i class="fab fa-instagram" aria-hidden="true"></i></a>
            </div>
        </div>

        <!-- Sección 2: Links de navegación institucional -->
        <div class="footer-column">
            <h4 class="footer-title">Navegación</h4>
            <ul class="footer-links">
                <li><a href="<?php echo BASE_URL; ?>/"><i class="fas fa-chevron-right" aria-hidden="true"></i> Inicio</a></li>
                <li><a href="<?php echo BASE_URL; ?>/about"><i class="fas fa-chevron-right" aria-hidden="true"></i> Nosotros</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Servicios</a></li>
                <li><a href="<?php echo BASE_URL; ?>/team"><i class="fas fa-chevron-right" aria-hidden="true"></i> Equipo</a></li>
                <li><a href="<?php echo BASE_URL; ?>/contact"><i class="fas fa-chevron-right" aria-hidden="true"></i> Contacto</a></li>
            </ul>
        </div>

        <!-- Sección 3: Servicios destacados -->
        <div class="footer-column">
            <h4 class="footer-title">Servicios</h4>
            <ul class="footer-links">
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Desarrollo Web</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Desarrollo Móvil</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Inteligencia Artificial</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Ciberseguridad</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services"><i class="fas fa-chevron-right" aria-hidden="true"></i> Cloud Computing</a></li>
            </ul>
        </div>

        <!-- Sección 4: Información de contacto -->
        <div class="footer-column">
            <h4 class="footer-title">Contacto</h4>
            <ul class="footer-contact">
                <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Bogotá, Colombia</li>
                <li><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:contacto@ontobyte.com">contacto@ontobyte.com</a></li>
                <li><i class="fas fa-phone-alt" aria-hidden="true"></i> <a href="tel:+573001234567">+57 300 123 4567</a></li>
            </ul>
        </div>
    </div>

    <!-- Barra inferior de derechos de autor y créditos -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - Todos los derechos reservados.</p>
        <p class="footer-credit">Desarrollado con <i class="fas fa-heart" aria-hidden="true"></i> y arquitectura MVC en PHP</p>
    </div>
</footer>