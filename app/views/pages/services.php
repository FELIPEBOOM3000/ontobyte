<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/services.php
 * ============================================================================
 * Propósito: Página de servicios de OntoByte. Muestra el catálogo completo
 *            de servicios consultados desde la base de datos, presentados
 *            en tarjetas con detalles, beneficios y CTA de contacto.
 *
 * Variables disponibles:
 *   $services - Lista de servicios activos (inyectada por el controlador)
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Servicios';
?>

<!-- ============================================================
     ENCABEZADO INTERNO DE LA PÁGINA
     ============================================================ -->
<section class="page-hero">
    <div class="container">
        <span class="section-eyebrow">Servicios profesionales</span>
        <h1 class="page-hero-title">Soluciones completas para tu <span class="text-gradient">transformación digital</span></h1>
        <p class="page-hero-text">
            Desde el desarrollo de software hasta la consultoría estratégica, te acompañamos en cada paso del camino.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: CATÁLOGO DE SERVICIOS
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="services-grid services-grid-lg">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <!-- Tarjeta de servicio con ancla para navegación in-page -->
                    <article class="service-card" id="<?php echo htmlspecialchars($service['slug']); ?>">
                        <div class="service-icon" aria-hidden="true">
                            <i class="<?php echo htmlspecialchars($service['icon_class'] ?? 'fas fa-code'); ?>"></i>
                        </div>

                        <h3 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h3>

                        <!-- Descripción corta del servicio -->
                        <p class="service-description"><?php echo htmlspecialchars($service['short_desc'] ?? ''); ?></p>

                        <!-- Descripción completa del servicio -->
                        <p class="service-fulltext"><?php echo htmlspecialchars($service['description']); ?></p>

                        <!-- CTA individual del servicio -->
                        <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-sm">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i> Solicitar cotización
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Nuestros servicios estarán disponibles próximamente.</p>
            <?php endif; ?>
        </div>

        <!-- Nota aclaratoria sobre atención personalizada -->
        <div class="services-note">
            <i class="fas fa-users" aria-hidden="true"></i>
            <p>
                ¿No encuentras el servicio que necesitas? <a href="<?php echo BASE_URL; ?>/contact">Contáctanos</a>
                y diseñemos juntos una solución a la medida de tu organización.
            </p>
        </div>
    </div>
</section>