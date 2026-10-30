<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/about.php
 * ============================================================================
 * Propósito: Página "Nosotros" de OntoByte. Presenta la historia, misión,
 *            visión y valores institucionales de la empresa, así como el
 *            equipo que la conforma.
 *
 * Variables disponibles:
 *   $teamMembers - Lista de miembros del equipo (inyectada por el controlador)
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Nosotros';
?>

<!-- ============================================================
     ENCABEZADO INTERNO DE LA PÁGINA
     ============================================================ -->
<section class="page-hero">
    <div class="container">
        <span class="section-eyebrow">Sobre OntoByte</span>
        <h1 class="page-hero-title">Tecnología con <span class="text-gradient">propósito humano</span></h1>
        <p class="page-hero-text">
            Conoce la filosofía y los valores que guían cada línea de código que escribimos.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: Nuestra Historia
     ============================================================ -->
<section class="section">
    <div class="container about-grid">
        <!-- Texto institucional -->
        <article class="about-text">
            <span class="section-eyebrow">Nuestra historia</span>
            <h2 class="section-title">Nacimos del <span class="text-accent">propósito</span>, no solo de la tecnología</h2>
            <p>
                <strong>OntoByte</strong> nace de la convicción de que la tecnología puede y debe
                estar al servicio de la humanidad. Como empresa de base tecnológica y humanista,
                integramos la precisión de la ingeniería de software con la sensibilidad de quienes
                comprenden que detrás de cada sistema hay personas reales.
            </p>
            <p>
                Combinamos la profundidad ontológica de nuestro nombre (del griego <em>ontos</em>,
                "lo que es") con la naturaleza digital del byte, reflejando nuestra visión:
                construir tecnología que entiende, transforma y eleva el ser.
            </p>

            <!-- Indicadores de solidez de la empresa -->
            <div class="about-badges">
                <div class="badge-item"><i class="fas fa-award" aria-hidden="true"></i> Estándares SENA</div>
                <div class="badge-item"><i class="fas fa-code-branch" aria-hidden="true"></i> Arquitectura MVC</div>
                <div class="badge-item"><i class="fas fa-lock" aria-hidden="true"></i> Seguridad Integrada</div>
            </div>
        </article>

        <!-- Bloque de Misión y Visión -->
        <div class="about-cards">
            <article class="mission-card">
                <div class="mission-icon" aria-hidden="true"><i class="fas fa-bullseye"></i></div>
                <h3>Nuestra Misión</h3>
                <p>
                    Impulsar la transformación digital de personas y organizaciones mediante
                    soluciones tecnológicas éticas, innovadoras y de alta calidad que generen
                    valor real y sostenible.
                </p>
            </article>
            <article class="mission-card accent">
                <div class="mission-icon" aria-hidden="true"><i class="fas fa-eye"></i></div>
                <h3>Nuestra Visión</h3>
                <p>
                    Convertirnos para 2030 en la empresa de referencia en Latinoamérica por nuestra
                    capacidad de unir tecnología avanzada con un impacto humano positivo y medible.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: VALORES INSTITUCIONALES
     ============================================================ -->
<section class="section values-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Nuestros principios</span>
            <h2 class="section-title">Valores que nos <span class="text-gradient">definen</span></h2>
        </div>

        <div class="values-grid">
            <article class="value-card">
                <i class="fas fa-balance-scale value-icon" aria-hidden="true"></i>
                <h3>Ética Digital</h3>
                <p>Desarrollamos con integridad, respetando los datos y la privacidad de las personas.</p>
            </article>
            <article class="value-card">
                <i class="fas fa-lightbulb value-icon" aria-hidden="true"></i>
                <h3>Innovación</h3>
                <p>Exploramos constantemente nuevas fronteras tecnológicas para ofrecer soluciones de vanguardia.</p>
            </article>
            <article class="value-card">
                <i class="fas fa-users value-icon" aria-hidden="true"></i>
                <h3>Colaboración</h3>
                <p>Creemos en el poder de los equipos diversos y el trabajo en comunidad con nuestros clientes.</p>
            </article>
            <article class="value-card">
                <i class="fas fa-gem value-icon" aria-hidden="true"></i>
                <h3>Excelencia</h3>
                <p>Buscanos la calidad impecable en cada detalle: desde el diseño hasta el despliegue final.</p>
            </article>
            <article class="value-card">
                <i class="fas fa-hand-holding-heart value-icon" aria-hidden="true"></i>
                <h3>Humanismo</h3>
                <p>Ponemos a las personas en el centro de cada decisión tecnológica que tomamos.</p>
            </article>
            <article class="value-card">
                <i class="fas fa-seedling value-icon" aria-hidden="true"></i>
                <h3>Sostenibilidad</h3>
                <p>Construimos tecnología responsable con el medio ambiente y la sociedad.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: EQUIPO CLAVE (resumen)
     ============================================================ -->
<?php if (!empty($teamMembers)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Los líderes</span>
            <h2 class="section-title">Personas detrás de <span class="text-gradient">OntoByte</span></h2>
        </div>

        <div class="team-grid">
            <?php foreach ($teamMembers as $member): ?>
                <article class="team-card">
                    <div class="team-avatar" aria-hidden="true">
                        <?php echo strtoupper(substr(htmlspecialchars($member['full_name']), 0, 1)); ?>
                    </div>
                    <h3 class="team-name"><?php echo htmlspecialchars($member['full_name']); ?></h3>
                    <p class="team-role"><?php echo htmlspecialchars($member['role']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="text-center" style="margin-top: 2.5rem;">
            <a href="<?php echo BASE_URL; ?>/team" class="btn btn-outline">Ver equipo completo &rarr;</a>
        </div>
    </div>
</section>
<?php endif; ?>