<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/auth/login.php
 * ============================================================================
 * Propósito: Vista del formulario de inicio de sesión del sistema OntoByte.
 *            Implementa protección CSRF, validación de entradas en cliente
 *            y en servidor, y un diseño centrado en la experiencia del usuario.
 *
 * Variables disponibles:
 *   $csrfToken - Token CSRF generado por el controlador
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Iniciar Sesión';

/* Cargar mensajes flash (errores de autenticación, etc.) */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     SECCIÓN: FORMULARIO DE INICIO DE SESIÓN
     ============================================================ -->
<section class="auth-section">
    <div class="auth-card">
        <!-- Encabezado del formulario -->
        <div class="auth-header">
            <span class="auth-logo" aria-hidden="true">O</span>
            <h1 class="auth-title">Bienvenido de nuevo</h1>
            <p class="auth-subtitle">Inicia sesión para acceder a tu cuenta de OntoByte</p>
        </div>

        <!-- Formulario de inicio de sesión -->
        <form action="<?php echo BASE_URL; ?>/login" method="POST" id="loginForm" class="auth-form" novalidate>
            <!-- Token CSRF oculto: protección contra ataques CSRF -->
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <!-- Campo de correo electrónico -->
            <div class="form-group">
                <label for="email">Correo electrónico <span class="required" aria-hidden="true">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <input type="email" id="email" name="email" class="form-input"
                           placeholder="tu@email.com" maxlength="255" autocomplete="email"
                           data-validate="email" required>
                </div>
                <small class="form-error" aria-live="polite"></small>
            </div>

            <!-- Campo de contraseña -->
            <div class="form-group">
                <label for="password">Contraseña <span class="required" aria-hidden="true">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Tu contraseña" autocomplete="current-password"
                           data-validate="password" required>
                    <!-- Botón para mostrar/ocultar la contraseña -->
                    <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contraseña">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <small class="form-error" aria-live="polite"></small>
            </div>

            <!-- Recordar sesión -->
            <div class="form-group form-consent">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Recordarme en este dispositivo</span>
                </label>
            </div>

            <!-- Botón de envío -->
            <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar Sesión
            </button>
        </form>

        <!-- ============================================================
             DIVISOR Y BOTÓN DE GOOGLE OAUTH 2.0
             Integración con Google Identity Services (GIS).
             El bloque solo se renderiza si GOOGLE_CLIENT_ID está
             configurado en config/config.php; si no, se muestra una
             nota informativa (el código queda preparado y funcional).
             ============================================================ -->
        <?php if (GOOGLE_CLIENT_ID !== ''): ?>
            <div class="auth-divider"><span>o continúa con</span></div>

            <!-- Contenedor de Google Identity Services: recibe el token ID -->
            <!-- data-callback referencia a handleGoogleCredential() de google-auth.js -->
            <div id="g_id_onload"
                 data-client_id="<?php echo htmlspecialchars(GOOGLE_CLIENT_ID); ?>"
                 data-callback="handleGoogleCredential"
                 data-auto_prompt="true"
                 data-base-url="<?php echo BASE_URL; ?>"
                 data-csrf="<?php echo htmlspecialchars($csrfToken); ?>"></div>

            <!-- Botón oficial "Continuar con Google" renderizado por GIS -->
            <div class="google-signin-wrap">
                <div class="g_id_signin"
                     data-type="standard"
                     data-size="large"
                     data-theme="outline"
                     data-text="continue_with"
                     data-shape="pill"
                     data-logo_alignment="left"></div>
            </div>

            <!-- Scripts de Google Identity Services y de la lógica del botón -->
            <script src="https://accounts.google.com/gsi/client" async defer></script>
            <script src="<?php echo JS_URL; ?>/google-auth.js" defer></script>
        <?php else: ?>
            <p class="google-notice">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                La autenticación con Google estará disponible cuando se configure
                <code>GOOGLE_CLIENT_ID</code> en <code>config/config.php</code>.
            </p>
        <?php endif; ?>

        <!-- Pie del formulario: enlaces de registro -->
        <div class="auth-footer">
            <p>
                ¿Aún no tienes cuenta?
                <a href="<?php echo BASE_URL; ?>/register">Regístrate gratuitamente</a>
            </p>
        </div>
    </div>
</section>