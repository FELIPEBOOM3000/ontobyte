<?php
/**
 * ============================================================================
 * ARCHIVO: config/config.php
 * ============================================================================
 * Propósito: Constantes globales, rutas base del sistema y configuración
 *            general de la aplicación OntoByte.
 *            Este archivo se carga una sola vez al iniciar la aplicación y
 *            define todos los parámetros necesarios para su funcionamiento.
 * ============================================================================
 */

/* --------------------------------------------------------------------------
 * INICIO SEGURO DE SESIÓN
 * Verifica que no haya una sesión activa antes de iniciar una nueva para
 * evitar ataques de fijación de sesión.
 * -------------------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    /* Configurar parámetros de seguridad de la sesión */
    session_set_cookie_params([
        'lifetime' => 0,                    /* Cookie de sesión (se cierra al cerrar navegador) */
        'path'     => '/',                  /* Disponible en todo el dominio */
        'domain'   => '',                   /* Dominio actual */
        'secure'   => false,                /* Cambiar a TRUE en producción con HTTPS */
        'httponly' => true,                 /* Impide acceso a la cookie via JavaScript (XSS) */
        'samesite' => 'Lax'                 /* Protección CSRF básica a nivel de cookie */
    ]);
    session_start();

    /* Regenerar el ID de sesión para prevenir session fixation */
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

/* --------------------------------------------------------------------------
 * CONSTANTES DE RUTAS DEL SISTEMA
 * Definen las rutas absolutas a los directorios principales del proyecto.
 * Se utiliza __DIR__ para obtener la ruta del directorio actual (config/)
 * y se sube un nivel para llegar a la raíz del proyecto.
 * -------------------------------------------------------------------------- */
define('ROOT_PATH', dirname(__DIR__));                        /* Raíz del proyecto: ontobyte/ */
define('CONFIG_PATH', ROOT_PATH . '/config');                /* Directorio de configuración */
define('APP_PATH', ROOT_PATH . '/app');                      /* Directorio de la aplicación MVC */
define('CONTROLLERS_PATH', APP_PATH . '/controllers');       /* Directorio de controladores */
define('MODELS_PATH', APP_PATH . '/models');                 /* Directorio de modelos */
define('VIEWS_PATH', APP_PATH . '/views');                   /* Directorio de vistas */
define('PUBLIC_PATH', ROOT_PATH . '/public');                /* Directorio público (raíz web) */
define('DATABASE_PATH', ROOT_PATH . '/database');            /* Directorio de scripts SQL */

/* --------------------------------------------------------------------------
 * CONSTANTES DE URL
 * URLs base que se utilizan para generar enlaces y rutas internas.
 * -------------------------------------------------------------------------- */
define('BASE_URL', '/ontobyte');                              /* URL base del proyecto */
define('PUBLIC_URL', BASE_URL . '/public');                   /* URL del directorio público */
define('CSS_URL', PUBLIC_URL . '/css');                       /* URL de hojas de estilo */
define('JS_URL', PUBLIC_URL . '/js');                         /* URL de scripts JavaScript */
define('IMG_URL', PUBLIC_URL . '/img');                       /* URL de imágenes */

/* --------------------------------------------------------------------------
 * LOGOTIPO OFICIAL DE LA EMPRESA (archivo del cliente en public/img/)
 * El cliente guarda su logo como "LOGO_ONTOBYTE" (extensión .png/.jpg/.webp/
 * .svg, la que use) dentro de la carpeta de imágenes del proyecto.
 * DETECCIÓN AUTOMÁTICA: si el archivo existe en public/img/, se usa; si no,
 * se usa logo.svg (marca vectorial de respaldo) para NUNCA dejar el logo roto.
 * Así se puede sustituir el logo oficial SIN tocar el código.
 * -------------------------------------------------------------------------- */
$__logoCandidates = glob(PUBLIC_PATH . '/img/LOGO_ONTOBYTE.*');
define('IMG_LOGO',
    !empty($__logoCandidates)
        ? IMG_URL . '/' . basename($__logoCandidates[0])
        : IMG_URL . '/logo.svg'
);
unset($__logoCandidates);

/* --------------------------------------------------------------------------
 * CONFIGURACIÓN DE LA APLICACIÓN
 * Parámetros generales del funcionamiento del sistema.
 * -------------------------------------------------------------------------- */
define('APP_NAME', 'OntoByte');                               /* Nombre de la aplicación */
define('APP_VERSION', '1.0.0');                               /* Versión actual del sistema */
define('APP_ENV', 'development');                             /* Entorno: development | production */
define('APP_DEBUG', true);                                    /* Mostrar errores (false en producción) */

/* --------------------------------------------------------------------------
 * CONFIGURACIÓN DE SEGURIDAD
 * Parámetros para proteger contra ataques comunes.
 * -------------------------------------------------------------------------- */
define('CSRF_TOKEN_NAME', 'csrf_token');                      /* Nombre del campo token CSRF */
define('MAX_LOGIN_ATTEMPTS', 5);                              /* Intentos máximos de login */
define('LOCKOUT_TIME', 900);                                  /* Tiempo de bloqueo en segundos (15 min) */
define('PASSWORD_MIN_LENGTH', 8);                             /* Longitud mínima de contraseñas */

/* --------------------------------------------------------------------------
 * CONFIGURACIÓN DE GOOGLE OAUTH 2.0 (Inicio de sesión con Google)
 * Integración mediante Google Identity Services (GIS).
 * IMPORTANTE: Para activar el inicio de sesión con Google debes:
 *   1. Crear un proyecto en https://console.cloud.google.com
 *   2. Configurar la "Pantalla de consentimiento" de OAuth.
 *   3. Crear una credencial OAuth 2.0 de tipo "Aplicación web"
 *      y copiar aquí el Client ID y el Client Secret.
 *   4. Añadir el origen autorizado (ej: http://localhost) y la URI de
 *      redirección (ej: http://localhost/ontobyte/auth/google).
 * Finalizados esos pasos, sustituye los valores vacíos por tus credenciales.
 * -------------------------------------------------------------------------- */
define('GOOGLE_CLIENT_ID', '');                               /* Client ID de tu aplicación Google OAuth */
define('GOOGLE_CLIENT_SECRET', '');                           /* Client Secret de tu aplicación Google */
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google');     /* URI de redirección autorizada */
define('GOOGLE_CERTS_URL', 'https://www.googleapis.com/oauth2/v3/certs'); /* Certificados públicos de firma JWT */

/* --------------------------------------------------------------------------
 * CONFIGURACIÓN DE PAGINACIÓN
 * Valores por defecto para la paginación de registros.
 * -------------------------------------------------------------------------- */
define('RECORDS_PER_PAGE', 10);                               /* Registros por página por defecto */

/* --------------------------------------------------------------------------
 * CONFIGURACIÓN DE ZONA HORARIA
 * Establece la zona horaria del servidor para consistencia en fechas.
 * -------------------------------------------------------------------------- */
date_default_timezone_set('America/Bogota');

/* --------------------------------------------------------------------------
 * AUTOLOAD DE ARCHIVOS NECESARIOS
 * Incluye archivos de configuración y utilidades al iniciar.
 * -------------------------------------------------------------------------- */
require_once CONFIG_PATH . '/database.php';
