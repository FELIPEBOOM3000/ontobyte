<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/auth/register.php
 * ============================================================================
 * Propósito: Vista del formulario de registro de nuevos usuarios de OntoByte.
 *            Implementa protección CSRF, validación estricta de entradas en
 *            cliente y en servidor (contraseña segura, coincidencia de
 *            contraseñas y formato de email) y un diseño centrado en el
 *            usuario.
 *
 * Variables disponibles:
 *   $csrfToken - Token CSRF generado por el controlador
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Crear Cuenta';

/* Cargar mensajes flash (errores de validación de registros previos) */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     SECCIÓN: FORMULARIO DE REGISTRO
     ============================================================ -->
<section class="auth-section">
    <div class="auth-card auth-card-lg">
        <!-- Encabezado del formulario -->
        <div class="auth-header">
            <span class="auth-logo" aria-hidden="true">O</span>
            <h1 class="auth-title">Crea tu cuenta</h1>
            <p class="auth-subtitle">Únete a la comunidad OntoByte y transforma tu visión digital</p>
        </div>

        <!-- Formulario de registro -->
        <form action="<?php echo BASE_URL; ?>/register" method="POST" id="registerForm" class="auth-form" novalidate>
            <!-- Token CSRF oculto: protección contra ataques CSRF -->
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <!-- Fila de dos columnas: Nombre y Apellido -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Nombre <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input type="text" id="first_name" name="first_name" class="form-input"
                               placeholder="Tu nombre" minlength="2" maxlength="100"
                               data-validate="name" required>
                    </div>
                    <small class="form-error" aria-live="polite"></small>
                </div>
                <div class="form-group">
                    <label for="last_name">Apellido <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input type="text" id="last_name" name="last_name" class="form-input"
                               placeholder="Tus apellidos" minlength="2" maxlength="100"
                               data-validate="name" required>
                    </div>
                    <small class="form-error" aria-live="polite"></small>
                </div>
            </div>

            <!-- Fila de dos columnas: Email y Teléfono -->
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Correo electrónico <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="tu@email.com" maxlength="255"
                               data-validate="email" required>
                    </div>
                    <small class="form-error" aria-live="polite"></small>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono <span class="optional-label">(opcional)</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <input type="tel" id="phone" name="phone" class="form-input"
                               placeholder="+57 300 000 0000" maxlength="20"
                               data-validate="phone">
                    </div>
                    <small class="form-error" aria-live="polite"></small>
                </div>
            </div>

            <!-- Fila de dos columnas: Contraseña y Confirmación -->
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Contraseña <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Mínimo 8 caracteres" minlength="8"
                               data-validate="password" required autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <!-- Indicador de fortaleza de la contraseña -->
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <small class="form-hint">Debe incluir mayúsculas, minúsculas y números.</small>
                    <small class="form-error" aria-live="polite"></small>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirmar contraseña <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                               placeholder="Repite tu contraseña" minlength="8" required autocomplete="new-password">
                    </div>
                    <small class="form-error" aria-live="polite"></small>
                </div>
            </div>

            <!-- Términos y condiciones -->
            <div class="form-group form-consent">
                <label class="checkbox-label">
                    <input type="checkbox" id="terms" required>
                    <span>Acepto los <a href="#" class="text-link">Términos y condiciones</a> y la
                    <a href="#" class="text-link">Política de privacidad</a> de <?php echo APP_NAME; ?>. <span class="required" aria-hidden="true">*</span></span>
                </label>
                <small class="form-error" aria-live="polite"></small>
            </div>

            <!-- Botón de envío -->
            <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                <i class="fas fa-user-plus" aria-hidden="true"></i> Crear mi cuenta
            </button>
        </form>

        <!-- Pie del formulario: enlace a iniciar sesión -->
        <div class="auth-footer">
            <p>
                ¿Ya tienes una cuenta?
                <a href="<?php echo BASE_URL; ?>/login">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</section>