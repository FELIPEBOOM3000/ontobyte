<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/contact.php
 * ============================================================================
 * Propósito: Página de contacto de OntoByte. Contiene el formulario
 *            corporativo con validación en cliente (JS) y en servidor (PHP),
 *            protección con token CSRF y los datos institucionales de
 *            contacto de la empresa.
 *
 * Variables disponibles:
 *   $services  - Lista de servicios para el selector (inyectada por el controlador)
 *   $csrfToken - Token CSRF generado por el controlador
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Contacto';

/* Datos de sesión: personalizan y precargan el formulario */
$isLogged  = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName  = $_SESSION['user_name'] ?? 'Usuario';
$userEmail = $_SESSION['user_email'] ?? '';

/* Cargar mensajes flash (errores de envío previos, etc.) */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     ENCABEZADO INTERNO DE LA PÁGINA
     ============================================================ -->
<section class="page-hero">
    <div class="container">
        <span class="section-eyebrow">Comunícate con nosotros</span>
        <h1 class="page-hero-title">Hablemos sobre tu <span class="text-gradient">proyecto</span></h1>
        <p class="page-hero-text">
            Cuéntanos tu idea y nuestro equipo te asesorará sin costo alguno.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: FORMULARIO + DATOS DE CONTACTO
     ============================================================ -->
<section class="section">
    <div class="container contact-grid">
        <!-- ----------------------------------------------------
             COLUMNA IZQUIERDA: Formulario de contacto corporativo
             ---------------------------------------------------- -->
        <div class="contact-form-wrapper">
            <?php if ($isLogged): ?>
                <!-- Aviso: el usuario ya inició sesión y sus datos vienen precargados -->
                <div class="contact-session-note">
                    <i class="fas fa-user-check" aria-hidden="true"></i>
                    <span>Escribiendo como <strong><?php echo htmlspecialchars($userName); ?></strong> (<?php echo htmlspecialchars($userEmail); ?>).</span>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/contact/submit" method="POST" id="contactForm" class="contact-form" novalidate>
                <!-- Token CSRF oculto: protección contra falsificación de peticiones -->
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <!-- Fila de dos columnas: Nombre y Email -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Nombre completo <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-input"
                               placeholder="Ej: María Fernanda López" minlength="3" maxlength="200"
                               data-validate="name" required
                               value="<?php echo $isLogged ? htmlspecialchars($userName) : ''; ?>">
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="required" aria-hidden="true">*</span></label>
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="tu@email.com" maxlength="255"
                               data-validate="email" required
                               value="<?php echo $isLogged ? htmlspecialchars($userEmail) : ''; ?>">
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                </div>

                <!-- Fila de dos columnas: Teléfono y Servicio de interés -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" class="form-input"
                               placeholder="+57 300 000 0000" maxlength="20" pattern="[+0-9\s\-()]{7,20}"
                               data-validate="phone">
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                    <div class="form-group">
                        <label for="service_interest">Servicio de interés</label>
                        <select id="service_interest" name="service_interest" class="form-input">
                            <option value="">-- Seleccionar servicio --</option>
                            <?php if (!empty($services)): ?>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['title']); ?>">
                                        <?php echo htmlspecialchars($service['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-error" aria-live="polite"></small>
                    </div>
                </div>

                <!-- Campo de Asunto -->
                <div class="form-group">
                    <label for="subject">Asunto <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" id="subject" name="subject" class="form-input"
                           placeholder="Ej: Quiero desarrollar una aplicación web" minlength="3" maxlength="255"
                           data-validate="subject" required>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Campo de Mensaje -->
                <div class="form-group">
                    <label for="message">Mensaje <span class="required" aria-hidden="true">*</span></label>
                    <textarea id="message" name="message" class="form-input form-textarea"
                              placeholder="Cuéntanos los detalles de tu proyecto, requerimientos y objetivos..." minlength="10" maxlength="5000" rows="6"
                              data-validate="message" required></textarea>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Campo de privacidad y consentimiento -->
                <div class="form-group form-consent">
                    <label class="checkbox-label">
                        <input type="checkbox" id="consent" required>
                        <span>Acepto el tratamiento de mis datos personales de acuerdo con la política de privacidad. <span class="required">*</span></span>
                    </label>
                    <small class="form-error" aria-live="polite"></small>
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Enviar mensaje
                </button>
            </form>
        </div>

        <!-- ----------------------------------------------------
             COLUMNA DERECHA: Información de contacto institucional
             ---------------------------------------------------- -->
        <aside class="contact-info">
            <h3 class="contact-info-title">Información de contacto</h3>

            <!-- Tarjeta: Dirección -->
            <div class="info-item">
                <span class="info-icon" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                <div>
                    <h4>Nuestra oficina</h4>
                    <p>Calle 100 No. 12-34, Bogotá D.C., Colombia</p>
                </div>
            </div>

            <!-- Tarjeta: Teléfono -->
            <div class="info-item">
                <span class="info-icon" aria-hidden="true"><i class="fas fa-phone-alt"></i></span>
                <div>
                    <h4>Teléfono</h4>
                    <p><a href="tel:+573001234567">+57 300 123 4567</a></p>
                </div>
            </div>

            <!-- Tarjeta: Email -->
            <div class="info-item">
                <span class="info-icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                <div>
                    <h4>Email</h4>
                    <p><a href="mailto:contacto@ontobyte.com">contacto@ontobyte.com</a></p>
                </div>
            </div>

            <!-- Tarjeta: Horario de atención -->
            <div class="info-item">
                <span class="info-icon" aria-hidden="true"><i class="fas fa-clock"></i></span>
                <div>
                    <h4>Horario de atención</h4>
                    <p>Lunes a Viernes: 8:00 AM - 6:00 PM<br>Sábados: 9:00 AM - 1:00 PM</p>
                </div>
            </div>

            <!-- Redes sociales -->
            <div class="contact-socials">
                <a href="#" class="social-link dark" aria-label="LinkedIn"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
                <a href="#" class="social-link dark" aria-label="GitHub"><i class="fab fa-github" aria-hidden="true"></i></a>
                <a href="#" class="social-link dark" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
            </div>
        </aside>
    </div>
</section>