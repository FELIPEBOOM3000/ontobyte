<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/home.php
 * ============================================================================
 * Propósito: Página principal del sitio corporativo de OntoByte.
 *            Plasma de forma fiel y jerárquica el contenido integrado de la
 *            empresa: Hero (filosofía -> software), Origen y Filosofía,
 *            Nosotros (misión, visión y valores), Servicios con propósito,
 *            estadísticas, equipo, testimonios y una sección de contacto
 *            con formulario embebido.
 *
 * Variables disponibles (inyectadas por PageController::home()):
 *   $services     - Lista de servicios activos de la base de datos
 *   $teamMembers  - Lista de miembros activos del equipo
 *   $testimonials - Lista de testimonios activos de clientes
 *   $csrfToken    - Token CSRF para el formulario de contacto embebido
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Inicio';

/* Datos de sesión: permiten personalizar el home para usuarios autenticados */
$isLogged  = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName  = $_SESSION['user_name'] ?? 'Usuario';
$userEmail = $_SESSION['user_email'] ?? '';

/* Cargar el componente de mensajes flash (errores/éxitos de acciones previas) */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     SECCIÓN: HERO / Portada principal
     Título de impacto y subtexto que definen la esencia de OntoByte.
     ============================================================ -->
<section class="hero" id="heroSection">
    <!-- Fondo interactivo sutil: red de nodos (partículas) dibujada en Canvas.
         Es SOLO decoración: pointer-events:none para no bloquear botones/enlaces,
         z-index:0 debajo del contenido, opacity:0.35 y omitido en móviles/CPU baja. -->
    <canvas id="heroNetwork" class="hero-network-bg" aria-hidden="true"></canvas>

    <div class="hero-container">
        <!-- Badge de posicionamiento de la empresa -->
        <span class="hero-badge"><i class="fas fa-infinity" aria-hidden="true"></i> Ontología + Byte = Tecnología con propósito</span>

        <?php if ($isLogged): ?>
        <!-- Saludo personalizado: solo visible con la sesión iniciada -->
        <div class="session-welcome">
            <span class="session-dot" aria-hidden="true"></span>
            Hola de nuevo, <strong><?php echo htmlspecialchars($userName); ?></strong>
            <a href="<?php echo BASE_URL; ?>/dashboard" class="session-link">
                Ir a mi panel <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
        <?php endif; ?>

        <!-- Título de impacto requerido -->
        <h1 class="hero-title">
            Donde la <span class="text-gradient">filosofía</span><br>
            se convierte en <span class="text-accent">software</span>.
        </h1>

        <!-- Subtexto conceptual requerido -->
        <p class="hero-subtitle">
            En la raíz de nuestro nombre habita la <strong>existencia</strong> y la
            <strong>naturaleza del ser</strong>, sumada a la unidad mínima de información digital.
            Somos el puente entre el pensamiento humano y la precisión digital:
            cristalizamos conocimiento en productos digitales eficientes y con propósito.
        </p>

        <!-- Botones de acción principal de la portada (cambian si hay sesión) -->
        <div class="hero-actions">
            <?php if ($isLogged): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Ir a mi panel
                </a>
                <a href="<?php echo BASE_URL; ?>/services" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-cubes" aria-hidden="true"></i> Explora nuestros servicios
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-lg">
                    <i class="fas fa-diamond" aria-hidden="true"></i> Comencemos a cristalizar tu idea
                </a>
                <a href="<?php echo BASE_URL; ?>/services" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-cubes" aria-hidden="true"></i> Explora nuestros servicios
                </a>
            <?php endif; ?>
        </div>

        <!-- Indicadores de confianza debajo del CTAs -->
        <div class="hero-trust">
            <span><i class="fas fa-shield-alt" aria-hidden="true"></i> +120 proyectos entregados</span>
            <span><i class="fas fa-user-friends" aria-hidden="true"></i> +80 clientes satisfechos</span>
            <span><i class="fas fa-award" aria-hidden="true"></i> Valores humanos al centro</span>
        </div>
    </div>

    <!-- Decoración de fondo: cuadrícula cibernética sutil -->
    <div class="hero-grid-bg" aria-hidden="true"></div>
</section>

<?php if ($isLogged): ?>
<!-- ============================================================
     SECCIÓN: ZONA DE MIEMBROS (solo con sesión iniciada)
     Acceso rápido a las descargas y herramientas exclusivas.
     ============================================================ -->
<section class="section member-home-section">
    <div class="container">
        <div class="member-home-card">
            <div class="member-home-text">
                <span class="section-eyebrow">Área privada</span>
                <h2 class="section-title">Tu zona de <span class="text-gradient">miembros</span></h2>
                <p>
                    Descarga el catálogo de servicios, la plantilla de brief y revisa las
                    novedades de OntoByte. Todo desde tu cuenta.
                </p>
            </div>
            <div class="member-home-actions">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-primary">
                    <i class="fas fa-door-open" aria-hidden="true"></i> Entrar a mi zona
                </a>
                <a href="<?php echo BASE_URL; ?>/downloads/Catalogo-Servicios-Precios-OntoByte.docx" class="btn btn-outline-light" download>
                    <i class="fas fa-file-arrow-down" aria-hidden="true"></i> Descargar catálogo
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     SECCIÓN: ORIGEN Y FILOSOFÍA
     Presenta el porqué del nombre y las tres columnas conceptuales:
     Ontología, Byte y Ontobyte.
     ============================================================ -->
<section class="section features-section">
    <div class="container">
        <!-- Encabezado de sección -->
        <div class="section-header">
            <span class="section-eyebrow">Nuestro origen</span>
            <h2 class="section-title">¿Por qué <span class="text-gradient">Ontobyte</span>?</h2>
            <p class="section-description">
                Nuestro nombre es una <strong>síntesis conceptual</strong>: la fusión entre
                ontología e informática. Tres columnas sostienen esa idea.
            </p>
        </div>

        <!-- Grid de tres tarjetas conceptuales (Ontología / Byte / Ontobyte) -->
        <div class="features-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><i class="fas fa-brain"></i></div>
                <h3>Ontología</h3>
                <p>
                    Comprender el ser, la experiencia y el valor detrás de cada byte:
                    la filosofía como punto de partida de todo lo que construimos.
                </p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><i class="fas fa-microchip"></i></div>
                <h3>Byte</h3>
                <p>
                    Transformar el saber en soluciones tecnológicas concretas:
                    la unidad mínima de información digital al servicio de las personas.
                </p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><i class="fas fa-link"></i></div>
                <h3>Ontobyte</h3>
                <p>
                    El puente entre el pensamiento humano y la precisión digital:
                    donde la filosofía se convierte en software.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: NOSOTROS (Metas, Misión, Visión y Valores)
     Misión y visión corporativas + grid interactivo de 4 valores.
     ============================================================ -->
<section class="section section-alt about-cards-section">
    <div class="container">
        <!-- Encabezado de sección -->
        <div class="section-header">
            <span class="section-eyebrow">Nuestra esencia</span>
            <h2 class="section-title">
                Nuestra misión, visión y los valores<br>que guían cada
                <span class="text-gradient">byte</span> que creamos.
            </h2>
        </div>

        <!-- Misión y Visión en tarjetas -->
        <div class="about-cards">
            <article class="mission-card">
                <div class="mission-icon" aria-hidden="true"><i class="fas fa-bullseye"></i></div>
                <h3>Misión</h3>
                <p>
                    Diseñar y mejorar páginas web funcionales y creativas. Después de dos
                    años de formación, buscamos posicionar nuestra propuesta como una de
                    las mejores, con esfuerzo y valores humanos al centro.
                </p>
            </article>

            <article class="mission-card accent">
                <div class="mission-icon" aria-hidden="true"><i class="fas fa-eye"></i></div>
                <h3>Visión</h3>
                <p>
                    Aspiramos a ser una empresa líder en páginas web con propósito,
                    impulsando el bienestar y la educación, resolviendo desafíos reales
                    y generando un alto impacto visual.
                </p>
            </article>
        </div>

        <!-- Grid interactivo de valores corporativos con íconos -->
        <div class="values-grid values-grid-4">
            <article class="value-card">
                <div class="value-icon" aria-hidden="true"><i class="fas fa-eye"></i></div>
                <h3>Transparencia</h3>
                <p>Claridad absoluta en cada proceso.</p>
            </article>

            <article class="value-card">
                <div class="value-icon" aria-hidden="true"><i class="fas fa-lightbulb"></i></div>
                <h3>Innovación Consciente</h3>
                <p>Tecnología con propósito y significado.</p>
            </article>

            <article class="value-card">
                <div class="value-icon" aria-hidden="true"><i class="fas fa-hand-pointer"></i></div>
                <h3>Experiencia Amigable</h3>
                <p>Interfaces intuitivas y centradas en el usuario.</p>
            </article>

            <article class="value-card">
                <div class="value-icon" aria-hidden="true"><i class="fas fa-handshake"></i></div>
                <h3>Compromiso</h3>
                <p>Acompañamiento dedicado desde la primera idea hasta la entrega final.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: SERVICIOS CON PROPÓSITO (Lo que hacemos)
     Tres servicios destacados con sus textos fieles.
     ============================================================ -->
<section class="section services-section">
    <div class="container">
        <!-- Encabezado de sección -->
        <div class="section-header">
            <span class="section-eyebrow">Lo que hacemos</span>
            <h2 class="section-title">Servicios con <span class="text-gradient">propósito</span></h2>
            <p class="section-description">
                Convertimos ideas en experiencias digitales que generan alto impacto visual
                y resuelven desafíos reales.
            </p>
        </div>

        <!-- Grid de los tres servicios con propósito -->
        <div class="services-grid">
            <article class="service-card">
                <div class="service-icon" aria-hidden="true"><i class="fas fa-palette"></i></div>
                <h3 class="service-title">Diseño Web Creativo</h3>
                <p class="service-description">
                    Interfaces modernas, elegantes y funcionales que reflejan la esencia de cada marca.
                </p>
                <a href="<?php echo BASE_URL; ?>/contact" class="service-link">Solicitar este servicio &rarr;</a>
            </article>

            <article class="service-card">
                <div class="service-icon" aria-hidden="true"><i class="fas fa-code"></i></div>
                <h3 class="service-title">Desarrollo Web</h3>
                <p class="service-description">
                    Soluciones tecnológicas concretas, eficientes y escalables construidas byte a byte.
                </p>
                <a href="<?php echo BASE_URL; ?>/contact" class="service-link">Solicitar este servicio &rarr;</a>
            </article>

            <article class="service-card">
                <div class="service-icon" aria-hidden="true"><i class="fas fa-handshake"></i></div>
                <h3 class="service-title">Acompañamiento Total</h3>
                <p class="service-description">
                    Desde la primera idea hasta la entrega final, dedicados al bienestar y educación de tu proyecto.
                </p>
                <a href="<?php echo BASE_URL; ?>/contact" class="service-link">Solicitar este servicio &rarr;</a>
            </article>
        </div>

        <!-- Aviso con el resto del catálogo dinámico de servicios -->
        <p class="services-note">
            <i class="fas fa-arrow-right" aria-hidden="true"></i>
            Descubre todos los servicios de nuestro catálogo en
            <a href="<?php echo BASE_URL; ?>/services">nuestra página de servicios</a>.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: Estadísticas / Métricas destacadas
     Datos cuantitativos de solidez y resultados de la empresa.
     ============================================================ -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <!-- Cada métrica utiliza contador animado (JS) -->
            <div class="stat-item">
                <span class="stat-value" data-counter="2">0</span>
                <span class="stat-suffix">+</span>
                <p class="stat-label">Años de formación</p>
            </div>
            <div class="stat-item">
                <span class="stat-value" data-counter="120">0</span>
                <span class="stat-suffix">+</span>
                <p class="stat-label">Proyectos entregados</p>
            </div>
            <div class="stat-item">
                <span class="stat-value" data-counter="80">0</span>
                <span class="stat-suffix">+</span>
                <p class="stat-label">Clientes satisfechos</p>
            </div>
            <div class="stat-item">
                <span class="stat-value" data-counter="15">0</span>
                <span class="stat-suffix">+</span>
                <p class="stat-label">Especialistas en tecnología</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: Nuestro Equipo
     Tarjetas de los miembros del equipo consultados de la BD.
     ============================================================ -->
<section class="section team-section">
    <div class="container">
        <!-- Encabezado de sección -->
        <div class="section-header">
            <span class="section-eyebrow">Detrás del código</span>
            <h2 class="section-title">El <span class="text-gradient">equipo</span> que lo hace posible</h2>
            <p class="section-description">
                Dos años de formación en el SENA fueron el punto de partida para convertirnos en futuros líderes visionarios de la tecnología.
            </p>
        </div>

        <!-- Grid de tarjetas del equipo -->
        <div class="team-grid">
            <?php if (!empty($teamMembers)): ?>
                <?php foreach ($teamMembers as $member): ?>
                    <article class="team-card">
                        <!-- Avatar inicial del miembro -->
                        <div class="team-avatar" aria-hidden="true">
                            <?php echo strtoupper(substr(htmlspecialchars($member['full_name']), 0, 1)); ?>
                        </div>

                        <!-- Nombre y cargo del miembro -->
                        <h3 class="team-name"><?php echo htmlspecialchars($member['full_name']); ?></h3>
                        <p class="team-role"><?php echo htmlspecialchars($member['role']); ?></p>

                        <!-- Biografía breve -->
                        <p class="team-bio"><?php echo htmlspecialchars($member['bio'] ?? ''); ?></p>

                        <!-- Redes sociales profesionales del miembro -->
                        <div class="team-socials">
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
                <!-- Mensaje de respaldo si el equipo está vacío -->
                <p class="empty-state">Nuestro equipo será presentado próximamente.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: Testimonios / Prueba social
     Opiniones de clientes reales con calificación de estrellas.
     ============================================================ -->
<section class="section testimonials-section">
    <div class="container">
        <!-- Encabezado de sección -->
        <div class="section-header">
            <span class="section-eyebrow">Voces de nuestros clientes</span>
            <h2 class="section-title">Lo que <span class="text-gradient">dicen</span> de nosotros</h2>
            <p class="section-description">
                La confianza de quienes ya transformaron su negocio con OntoByte.
            </p>
        </div>

        <!-- Carrusel de testimonios -->
        <div class="testimonials-grid">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $testimonial): ?>
                    <figure class="testimonial-card">
                        <!-- Calificación con estrellas -->
                        <div class="testimonial-rating" aria-label="Calificación <?php echo (int) $testimonial['rating']; ?> de 5 estrellas">
                            <?php
                            /* Generar estrellas llenas y vacías según la calificación */
                            $rating = (int) ($testimonial['rating'] ?? 5);
                            for ($star = 1; $star <= 5; $star++) {
                                if ($star <= $rating) {
                                    echo '<i class="fas fa-star" aria-hidden="true"></i>';
                                } else {
                                    echo '<i class="far fa-star" aria-hidden="true"></i>';
                                }
                            }
                            ?>
                        </div>

                        <!-- Contenido del testimonio -->
                        <blockquote class="testimonial-content">
                            "<?php echo htmlspecialchars($testimonial['content']); ?>"
                        </blockquote>

                        <!-- Autor del testimonio -->
                        <figcaption class="testimonial-author">
                            <div class="testimonial-avatar" aria-hidden="true">
                                <?php echo strtoupper(substr(htmlspecialchars($testimonial['client_name']), 0, 1)); ?>
                            </div>
                            <div>
                                <span class="testimonial-name"><?php echo htmlspecialchars($testimonial['client_name']); ?></span>
                                <span class="testimonial-role"><?php echo htmlspecialchars($testimonial['client_role'] ?? ''); ?></span>
                            </div>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mensaje de respaldo si no hay testimonios -->
                <p class="empty-state">Próximamente conocerás las experiencias de nuestros clientes.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: CONTACTO EMBEBIDO EN EL HOME
     Título requerido + formulario validado por JS conectado al
     controlador PHP (/contact/submit) y respaldado por la BD.
     ============================================================ -->
<section class="home-contact-section">
    <div class="container home-contact-grid">
        <!-- --------------------------------------------------------
             COLUMNA IZQUIERDA: Título y propuesta de valor
             -------------------------------------------------------- -->
        <div class="home-contact-intro">
            <span class="section-eyebrow">Trabajemos juntos</span>
            <h2 class="home-contact-title">
                Hablemos: Comencemos a <span class="text-gradient">cristalizar</span> tu idea
            </h2>
            <p class="home-contact-text">
                Cuéntanos tu proyecto y nuestro equipo te asesorará sin costo alguno.
                Desde la primera idea hasta la entrega final, dedicamos todo nuestro
                esfuerzo y valores humanos al centro.
            </p>
            <ul class="home-contact-list">
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Asesoría personalizada sin costo</li>
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Respuesta en menos de 24 horas</li>
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Acompañamiento total del proyecto</li>
            </ul>
        </div>

        <!-- --------------------------------------------------------
             COLUMNA DERECHA: Formulario de contacto (validado por JS)
             -------------------------------------------------------- -->
        <div class="contact-form-wrapper">
            <form action="<?php echo BASE_URL; ?>/contact/submit" method="POST" id="contactFormHome" class="contact-form" novalidate>
                <!-- Token CSRF oculto: protección contra falsificación de peticiones -->
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <!-- Fila de dos columnas: Nombre y Correo -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="home_full_name">Nombre <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="home_full_name" name="full_name" class="form-input"
                               placeholder="Ej: María Fernanda López" minlength="3" maxlength="200"
                               data-validate="name" required
                               value="<?php echo $isLogged ? htmlspecialchars($userName) : ''; ?>">
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                    <div class="form-group">
                        <label for="home_email">Correo <span class="required" aria-hidden="true">*</span></label>
                        <input type="email" id="home_email" name="email" class="form-input"
                               placeholder="tu@email.com" maxlength="255"
                               data-validate="email" required
                               value="<?php echo $isLogged ? htmlspecialchars($userEmail) : ''; ?>">
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                </div>

                <!-- Campo de Asunto -->
                <div class="form-group">
                    <label for="home_subject">Asunto <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" id="home_subject" name="subject" class="form-input"
                           placeholder="Ej: Quiero desarrollar una aplicación web" minlength="3" maxlength="255"
                           data-validate="subject" required>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Campo de Mensaje -->
                <div class="form-group">
                    <label for="home_message">Mensaje <span class="required" aria-hidden="true">*</span></label>
                    <textarea id="home_message" name="message" class="form-input form-textarea"
                              placeholder="Cuéntanos los detalles de tu idea, requerimientos y objetivos..." minlength="10" maxlength="5000" rows="5"
                              data-validate="message" required></textarea>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Campo de consentimiento -->
                <div class="form-group form-consent">
                    <label class="checkbox-label">
                        <input type="checkbox" id="home_consent" required>
                        <span>Acepto el tratamiento de mis datos personales de acuerdo con la política de privacidad. <span class="required">*</span></span>
                    </label>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Comencemos a cristalizar tu idea
                </button>
            </form>
        </div>
    </div>
</section>