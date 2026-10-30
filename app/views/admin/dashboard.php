<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/admin/dashboard.php
 * ============================================================================
 * Propósito: Panel principal de administración de OntoByte.
 *            Muestra estadísticas generales del sistema: total de usuarios,
 *            mensajes de contacto recibidos y mensajes sin leer, además
 *            de acceso a la gestión de contactos.
 *
 * Variables disponibles:
 *   $totalUsers     - Cantidad total de usuarios registrados
 *   $totalContacts  - Cantidad total de mensajes de contacto
 *   $unreadContacts - Cantidad de mensajes sin leer
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Panel de Administración';

/* Cargar mensajes flash */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     ENCABEZADO DEL PANEL ADMINISTRATIVO
     ============================================================ -->
<section class="admin-header">
    <div class="container">
        <h1 class="admin-title">
            <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
            Panel de Administración
        </h1>
        <p class="admin-subtitle">
            Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrador'); ?>.
            Aquí puedes monitorear la actividad del sistema.
        </p>
    </div>
</section>

<!-- ============================================================
     GRID DE TARJETAS ESTADÍSTICAS
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="admin-stats-grid">

            <!-- Tarjeta estadística: Total de usuarios -->
            <article class="admin-stat-card">
                <div class="admin-stat-icon blue" aria-hidden="true"><i class="fas fa-users"></i></div>
                <div class="admin-stat-info">
                    <span class="admin-stat-value"><?php echo (int) $totalUsers; ?></span>
                    <span class="admin-stat-label">Usuarios registrados</span>
                </div>
            </article>

            <!-- Tarjeta estadística: Mensajes totales -->
            <article class="admin-stat-card">
                <div class="admin-stat-icon green" aria-hidden="true"><i class="fas fa-envelope-open-text"></i></div>
                <div class="admin-stat-info">
                    <span class="admin-stat-value"><?php echo (int) $totalContacts; ?></span>
                    <span class="admin-stat-label">Mensajes recibidos</span>
                </div>
            </article>

            <!-- Tarjeta estadística: Mensajes sin leer -->
            <article class="admin-stat-card">
                <div class="admin-stat-icon amber" aria-hidden="true"><i class="fas fa-envelope"></i></div>
                <div class="admin-stat-info">
                    <span class="admin-stat-value"><?php echo (int) $unreadContacts; ?></span>
                    <span class="admin-stat-label">Mensajes sin leer</span>
                </div>
            </article>
        </div>

        <!-- Acciones rápidas del panel -->
        <div class="admin-actions">
            <h2 class="admin-section-title">Acciones rápidas</h2>

            <div class="admin-action-grid">
                <!-- Acción: Gestionar usuarios -->
                <a href="<?php echo BASE_URL; ?>/admin/users" class="admin-action-card">
                    <i class="fas fa-users" aria-hidden="true"></i>
                    <div>
                        <h3>Gestionar usuarios</h3>
                        <p>Consulta quién se ha registrado, edita o elimina cuentas.</p>
                    </div>
                    <i class="fas fa-chevron-right admin-action-arrow" aria-hidden="true"></i>
                </a>

                <!-- Acción: Gestionar mensajes -->
                <a href="<?php echo BASE_URL; ?>/admin/contacts" class="admin-action-card">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <div>
                        <h3>Gestionar mensajes</h3>
                        <p>Revisa y responde los mensajes de contacto recibidos.</p>
                    </div>
                    <i class="fas fa-chevron-right admin-action-arrow" aria-hidden="true"></i>
                </a>

                <!-- Acción: Volver a la página principal -->
                <a href="<?php echo BASE_URL; ?>/" class="admin-action-card">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    <div>
                        <h3>Ver sitio web</h3>
                        <p>Visita la página pública corporativa de OntoByte.</p>
                    </div>
                    <i class="fas fa-chevron-right admin-action-arrow" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>