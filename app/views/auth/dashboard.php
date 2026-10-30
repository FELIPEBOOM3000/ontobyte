<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/auth/dashboard.php
 * ============================================================================
 * Propósito: Zona de Miembros de OntoByte (vista protegida por sesión).
 *            Muestra la tarjeta de membresía del usuario, accesos rápidos,
 *            descargas exclusivas y novedades de la empresa.
 *
 * Variables disponibles (inyectadas por AuthController::dashboard()):
 *   $user      - Datos del miembro: name, email, role, created_at,
 *                last_login, is_active
 *   $csrfToken - Token CSRF de la sesión
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Mi Zona';

/* Cargar mensajes flash (éxitos de login, etc.) */
require_once VIEWS_PATH . '/components/flash.php';

/* Determinar si el usuario es administrador */
$isAdmin = ($user['role'] ?? 'user') === 'admin';

/* Formatear fechas legibles (si existen) */
$memberSince = !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '—';
$lastAccess  = !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : '—';
$isActive    = !empty($user['is_active']);

/* Tamaño legible de cada archivo descargable */
$catalogFile = PUBLIC_PATH . '/downloads/Catalogo-Servicios-Precios-OntoByte.docx';
$briefFile   = PUBLIC_PATH . '/downloads/Plantilla-Brief-Proyecto-OntoByte.docx';
$sizeLabel = static function (string $path): string {
    if (!is_file($path)) {
        return 'DOCX';
    }
    $kb = (int) round(filesize($path) / 1024);
    return $kb . ' KB · DOCX';
};
?>

<!-- ============================================================
     TARJETA DE MEMBRESÍA DEL USUARIO
     ============================================================ -->
<section class="section member-zone">
    <div class="container">

        <div class="member-hero">
            <div class="member-hero-main">
                <span class="member-avatar" aria-hidden="true">
                    <?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?>
                </span>
                <div>
                    <span class="section-eyebrow">Zona de miembros</span>
                    <h1 class="member-title">Hola, <?php echo htmlspecialchars($user['name']); ?></h1>
                    <div class="member-badges">
                        <span class="member-badge <?php echo $isAdmin ? 'admin' : 'user'; ?>">
                            <i class="fas <?php echo $isAdmin ? 'fa-crown' : 'fa-user'; ?>" aria-hidden="true"></i>
                            <?php echo $isAdmin ? 'Administrador' : 'Miembro'; ?>
                        </span>
                        <span class="member-badge <?php echo $isActive ? 'active' : 'warn'; ?>">
                            <i class="fas <?php echo $isActive ? 'fa-check-circle' : 'fa-ban'; ?>" aria-hidden="true"></i>
                            <?php echo $isActive ? 'Cuenta activa' : 'Cuenta inactiva'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="member-meta">
                <div class="member-meta-item">
                    <small>Correo</small>
                    <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                </div>
                <div class="member-meta-item">
                    <small>Miembro desde</small>
                    <strong><?php echo $memberSince; ?></strong>
                </div>
                <div class="member-meta-item">
                    <small>Último acceso</small>
                    <strong><?php echo $lastAccess; ?></strong>
                </div>
            </div>

            <div class="member-hero-actions">
                <?php if ($isAdmin): ?>
                    <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-accent">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Panel Admin
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/services" class="btn btn-primary">
                    <i class="fas fa-cubes" aria-hidden="true"></i> Ver servicios
                </a>
                <a href="<?php echo BASE_URL; ?>/logout" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión
                </a>
            </div>
        </div>

        <!-- ====================================================
             ACCESOS RÁPIDOS
             ==================================================== -->
        <div class="section-header member-section-head">
            <span class="section-eyebrow">Dentro de tu cuenta</span>
            <h2 class="section-title">Accesos <span class="text-gradient">rápidos</span></h2>
        </div>

        <div class="member-quick-grid">
            <a href="<?php echo BASE_URL; ?>/contact" class="member-quick-card">
                <span class="member-quick-icon"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
                <h3>Hablar con el equipo</h3>
                <p>Cuéntanos tu idea; tus datos ya vienen precargados.</p>
                <span class="member-quick-link">Abrir contacto <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </a>

            <a href="<?php echo BASE_URL; ?>/prices" class="member-quick-card">
                <span class="member-quick-icon"><i class="fas fa-tags" aria-hidden="true"></i></span>
                <h3>Planes y precios</h3>
                <p>Consulta el detalle de cada plan y módulo adicional.</p>
                <span class="member-quick-link">Ver precios <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </a>

            <a href="<?php echo BASE_URL; ?>/downloads/Catalogo-Servicios-Precios-OntoByte.docx" class="member-quick-card" download>
                <span class="member-quick-icon"><i class="fas fa-file-arrow-down" aria-hidden="true"></i></span>
                <h3>Catálogo en Word</h3>
                <p>Descarga el catálogo completo de servicios y precios.</p>
                <span class="member-quick-link">Descargar <i class="fas fa-download" aria-hidden="true"></i></span>
            </a>

            <?php if ($isAdmin): ?>
                <a href="<?php echo BASE_URL; ?>/admin/users" class="member-quick-card">
                    <span class="member-quick-icon"><i class="fas fa-users" aria-hidden="true"></i></span>
                    <h3>Gestionar usuarios</h3>
                    <p>Administra las cuentas registradas en el sistema.</p>
                    <span class="member-quick-link">Abrir panel <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/about" class="member-quick-card">
                    <span class="member-quick-icon"><i class="fas fa-lightbulb" aria-hidden="true"></i></span>
                    <h3>Conoce OntoByte</h3>
                    <p>Nuestra filosofía, misión, visión y valores.</p>
                    <span class="member-quick-link">Conocer más <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                </a>
            <?php endif; ?>
        </div>

        <!-- ====================================================
             DESCARGAS EXCLUSIVAS
             ==================================================== -->
        <div class="section-header member-section-head">
            <span class="section-eyebrow">Solo para miembros</span>
            <h2 class="section-title">Descargas <span class="text-gradient">exclusivas</span></h2>
        </div>

        <div class="member-downloads">
            <div class="download-card">
                <span class="download-icon"><i class="fas fa-file-lines" aria-hidden="true"></i></span>
                <div class="download-info">
                    <h3>Catálogo de Servicios y Precios</h3>
                    <p>Planes, servicios, módulos y complementos con tarifas en pesos colombianos.</p>
                    <span class="download-meta"><?php echo $sizeLabel($catalogFile); ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>/downloads/Catalogo-Servicios-Precios-OntoByte.docx" class="btn btn-primary btn-sm" download>
                    <i class="fas fa-download" aria-hidden="true"></i> Descargar
                </a>
            </div>

            <div class="download-card">
                <span class="download-icon"><i class="fas fa-file-pen" aria-hidden="true"></i></span>
                <div class="download-info">
                    <h3>Plantilla de Brief de Proyecto</h3>
                    <p>Formato guía para contarnos tu idea y recibir una cotización exacta sin costo.</p>
                    <span class="download-meta"><?php echo $sizeLabel($briefFile); ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>/downloads/Plantilla-Brief-Proyecto-OntoByte.docx" class="btn btn-primary btn-sm" download>
                    <i class="fas fa-download" aria-hidden="true"></i> Descargar
                </a>
            </div>
        </div>

        <!-- ====================================================
             NOVEDADES DE LA EMPRESA
             ==================================================== -->
        <div class="section-header member-section-head">
            <span class="section-eyebrow">Lo último</span>
            <h2 class="section-title">Novedades <span class="text-gradient">OntoByte</span></h2>
        </div>

        <div class="member-news">
            <div class="news-item">
                <span class="news-dot" aria-hidden="true"></span>
                <div class="news-body">
                    <span class="news-date">Hoy</span>
                    <h3>Zona de miembros habilitada</h3>
                    <p>Ya puedes acceder a descargas exclusivas, tu brief de proyecto y novedades desde tu cuenta.</p>
                </div>
            </div>
            <div class="news-item">
                <span class="news-dot" aria-hidden="true"></span>
                <div class="news-body">
                    <span class="news-date">Esta semana</span>
                    <h3>Nuevo catálogo de precios</h3>
                    <p>Publicamos los planes, módulos y complementos con tarifas en pesos colombianos.</p>
                </div>
            </div>
            <div class="news-item">
                <span class="news-dot" aria-hidden="true"></span>
                <div class="news-body">
                    <span class="news-date">Siempre</span>
                    <h3>Acompañamiento total</h3>
                    <p>Desde la primera idea hasta la entrega final, con garantía de 30 días de ajustes.</p>
                </div>
            </div>
        </div>

        <div class="member-foot">
            <p>
                <i class="fas fa-lock" aria-hidden="true"></i>
                Tu sesión es segura y cifrada. No compartas tus credenciales con nadie.
            </p>
        </div>
    </div>
</section>