<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/layouts/navbar.php
 * ============================================================================
 * Propósito: Barra de navegación principal del sitio OntoByte.
 *            Contiene el logo corporativo, los enlaces de navegación
 *            y los botones de autenticación. Es totalmente responsiva:
 *            en móvil se convierte en un menú hamburguesa desplegable.
 *            La variable $activePage permite resaltar la sección actual.
 * ============================================================================
 */

/* Determinar si el usuario está autenticado (variable disponible en sesión) */
$isLogged = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$isAdmin  = $isLogged && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<header class="site-header" id="siteHeader">
    <div class="header-container">

        <!-- Logo corporativo de OntoByte (LOGO_ONTOBYTE de public/img/; respaldo logo.svg)
             FOTO: alt descriptivo + loading lazy para no bloquear el primer render
             y dimensiones "width/height" para evitar saltos de layout (CLS).
             IMG_LOGO se detecta automáticamente en config/config.php. -->
        <a href="<?php echo BASE_URL; ?>/" class="brand">
            <img src="<?php echo IMG_LOGO; ?>" alt="OntoByte Logo"
                 class="brand-logo-img" loading="lazy" width="42" height="42">
            <span class="brand-text">
                <span class="brand-name">Onto<em>Byte</em></span>
                <span class="brand-tagline">Tecnología con propósito</span>
            </span>
        </a>

        <!-- Botón hamburguesa para navegación móvil -->
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Abrir menú de navegación" aria-expanded="false">
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
        </button>

        <!-- Menú de navegación principal -->
        <nav class="main-nav" id="mainNav">
            <ul class="nav-list">
                <li><a href="<?php echo BASE_URL; ?>/" class="nav-link <?php echo (isset($activePage) && $activePage === 'home') ? 'active' : ''; ?>">Inicio</a></li>
                <li><a href="<?php echo BASE_URL; ?>/about" class="nav-link <?php echo (isset($activePage) && $activePage === 'about') ? 'active' : ''; ?>">Nosotros</a></li>
                <li><a href="<?php echo BASE_URL; ?>/services" class="nav-link <?php echo (isset($activePage) && $activePage === 'services') ? 'active' : ''; ?>">Servicios</a></li>
                <li><a href="<?php echo BASE_URL; ?>/prices" class="nav-link <?php echo (isset($activePage) && $activePage === 'prices') ? 'active' : ''; ?>">Precios</a></li>
                <li><a href="<?php echo BASE_URL; ?>/team" class="nav-link <?php echo (isset($activePage) && $activePage === 'team') ? 'active' : ''; ?>">Equipo</a></li>
                <li><a href="<?php echo BASE_URL; ?>/contact" class="nav-link <?php echo (isset($activePage) && $activePage === 'contact') ? 'active' : ''; ?>">Contacto</a></li>
            </ul>

            <!-- Acciones de autenticación dinámicas según sesión -->
            <div class="nav-actions">
                <?php if ($isLogged): ?>
                    <!-- Enlace al panel de bienvenida (dashboard) de cualquier usuario autenticado -->
                    <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-ghost-light"><i class="fas fa-user" aria-hidden="true"></i> Mi Panel</a>
                    <?php if ($isAdmin): ?>
                        <!-- Enlace al panel de administración si es admin -->
                        <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-ghost-light"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> Panel Admin</a>
                    <?php endif; ?>
                    <!-- Nombre del usuario autenticado -->
                    <span class="nav-user"><i class="fas fa-user-circle" aria-hidden="true"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>
                    <!-- Botón para cerrar sesión -->
                    <a href="<?php echo BASE_URL; ?>/logout" class="btn btn-outline-light"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Salir</a>
                <?php else: ?>
                    <!-- Botones de inicio de sesión y registro -->
                    <a href="<?php echo BASE_URL; ?>/login" class="btn btn-ghost-light"><i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar Sesión</a>
                    <a href="<?php echo BASE_URL; ?>/register" class="btn btn-primary"><i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>