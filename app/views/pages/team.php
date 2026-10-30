<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/team.php
 * ============================================================================
 * Propósito: Página del equipo de OntoByte. Presenta de forma ampliada a
 *            todos los miembros de la organización con sus perfiles,
 *            biografías y redes profesionales.
 *
 * Variables disponibles:
 *   $teamMembers - Lista de miembros activos (inyectada por el controlador)
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Nuestro Equipo';
?>

<!-- ============================================================
     ENCABEZADO INTERNO DE LA PÁGINA
     ============================================================ -->
<section class="page-hero">
    <div class="container">
        <span class="section-eyebrow">Talento humano</span>
        <h1 class="page-hero-title">Conoce al <span class="text-gradient">equipo</span> de OntoByte</h1>
        <p class="page-hero-text">
            Profesionales multidisciplinarios unidos por la excelencia técnica y el propósito humano.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: GRID DEL EQUIPO COMPLETO
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="team-grid">
            <?php if (!empty($teamMembers)): ?>
                <?php foreach ($teamMembers as $member): ?>
                    <!-- Tarjeta de miembro del equipo -->
                    <article class="team-card team-card-lg">
                        <!-- Avatar con foto o inicial -->
                        <div class="team-avatar team-avatar-lg" aria-hidden="true">
                            <?php echo strtoupper(substr(htmlspecialchars($member['full_name']), 0, 1)); ?>
                        </div>

                        <!-- Perfil del miembro -->
                        <h3 class="team-name"><?php echo htmlspecialchars($member['full_name']); ?></h3>
                        <p class="team-role"><?php echo htmlspecialchars($member['role']); ?></p>
                        <p class="team-bio"><?php echo htmlspecialchars($member['bio'] ?? ''); ?></p>

                        <!-- Email y redes sociales -->
                        <div class="team-socials">
                            <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" aria-label="Email de <?php echo htmlspecialchars($member['full_name']); ?>">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($member['linkedin_url'])): ?>
                                <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de <?php echo htmlspecialchars($member['full_name']); ?>">
                                    <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($member['github_url'])): ?>
                                <a href="<?php echo htmlspecialchars($member['github_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub de <?php echo htmlspecialchars($member['full_name']); ?>">
                                    <i class="fab fa-github" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Nuestro equipo será presentado próximamente.</p>
            <?php endif; ?>
        </div>

        <!-- CTA de unirse al equipo -->
        <div class="join-team-cta">
            <h3>¿Quieres ser parte de OntoByte?</h3>
            <p>Estamos en constante búsqueda de talento que comparta nuestra visión.</p>
            <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary">Postúlate ahora</a>
        </div>
    </div>
</section>