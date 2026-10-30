/**
 * ============================================================================
 * ARCHIVO: public/js/google-auth.js
 * ============================================================================
 * Propósito: Lógica de integración con Google Identity Services (GIS)
 *            para el inicio de sesión con Google (OAuth 2.0).
 *
 * Flujo implementado:
 *   1. El usuario hace clic en el botón "Continuar con Google".
 *   2. GIS muestra el selector de cuentas de Google.
 *   3. Al elegir cuenta, GIS invoca la función global
 *      handleGoogleCredential(response) con un token ID (JWT) firmado.
 *   4. Este script envía el token al servidor (POST /auth/google) junto
 *      con el token CSRF de la sesión.
 *   5. El controlador AuthController::googleCallback() verifica la firma
 *      y las reclamaciones del token, y valida la identidad.
 *   6. Ante una redirección del servidor, el navegador sigue a la
 *      página final (dashboard o admin).
 * ============================================================================
 */

/**
 * Callback global invocado por Google Identity Services.
 * Recibe la respuesta del selector de cuentas con el token ID.
 * @param {Object} response - Objeto con la propiedad 'credential' (JWT)
 */
function handleGoogleCredential(response) {
    /* Obtener el contenedor que GIS inserta en la página */
    const container = document.getElementById('g_id_onload');

    /* Validar que la respuesta contenga la credencial (token ID) */
    if (!response || !response.credential) {
        alert('No se pudo obtener la credencial de Google. Intenta de nuevo.');
        return;
    }

    /* Extraer de los data-attributes la URL base y el token CSRF */
    const baseUrl = container ? container.dataset.baseUrl || '' : '';
    const csrf    = container ? container.dataset.csrf    || '' : '';

    if (!baseUrl || !csrf) {
        alert('Configuración incompleta de Google OAuth.');
        return;
    }

    /* Construir el cuerpo de la petición (form-urlencoded) */
    const body = new URLSearchParams();
    body.append('credential', response.credential);
    body.append('csrf_token', csrf);

    /* Bloquear temporalmente el botón para evitar doble envío */
    const signInButton = document.querySelector('.g_id_signin');
    if (signInButton) {
        signInButton.style.pointerEvents = 'none';
        signInButton.style.opacity = '0.6';
    }

    /* Enviar el token al controlador del servidor */
    fetch(baseUrl + '/auth/google', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
        credentials: 'same-origin'
    })
    .then((res) => {
        /* Si el servidor redirigió (302), seguir a la página final */
        if (res.redirected) {
            window.location.href = res.url;
            return null;
        }
        return res.text();
    })
    .then((text) => {
        /* No hay texto si hubo redirección (el flujo continúa al navegador) */
        if (!text) return;

        /* Intentar extraer un mensaje flash de la respuesta para mostrarlo */
        const doc = new DOMParser().parseFromString(text, 'text/html');
        const flash = doc.querySelector('.flash-message');
        if (flash) {
            alert(flash.textContent.trim());
        }
        window.location.href = baseUrl + '/login';
    })
    .catch(() => {
        alert('Error de conexión con el servidor. Verifica tu internet e inténtalo de nuevo.');
    })
    .finally(() => {
        /* Restaurar el botón para permitir nuevos intentos */
        if (signInButton) {
            signInButton.style.pointerEvents = '';
            signInButton.style.opacity = '';
        }
    });
}