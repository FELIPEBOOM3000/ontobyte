<?php
/**
 * ============================================================================
 * ARCHIVO: public/index.php (Front Controller)
 * ============================================================================
 * Propósito: Punto de entrada único y centralizado de la aplicación OntoByte.
 *            Implementa el patrón Front Controller, donde TODAS las peticiones
 *            HTTP pasan por este archivo. Él se encarga de:
 *            1. Cargar la configuración del sistema.
 *            2. Analizar la URL solicitada.
 *            3. Determinar qué controlador y método ejecutar.
 *            4. Ejecutar la lógica y renderizar la vista correspondiente.
 *
 *            Este enfoque permite:
 *            - URLs amigables y modulares.
 *            - Un único punto de control de seguridad.
 *            - Separación clara entre rutas y lógica de negocio.
 * ============================================================================
 */

/* --------------------------------------------------------------------------
 * PASO 1: Cargar la configuración del sistema
 * Incluye el archivo de configuración que a su vez carga database.php,
 * define constantes e inicia la sesión de forma segura.
 * -------------------------------------------------------------------------- */
require_once dirname(__DIR__) . '/config/config.php';

/* --------------------------------------------------------------------------
 * PASO 2: Obtener la URL solicitada por el usuario
 * Se elimina la cadena de consulta (?param=value) y se decodifican
 * los caracteres especiales de la URL.
 * -------------------------------------------------------------------------- */
$requestUri = $_SERVER['REQUEST_URI'];
$basePath   = parse_url($requestUri, PHP_URL_PATH);     /* Obtener solo la ruta sin parámetros */

/* --------------------------------------------------------------------------
 * PASO 2.1: Normalizar la ruta quitando el prefijo del proyecto
 * Se soportan múltiples escenarios de despliegue:
 *   1. Acceso directo al directorio público: /ontobyte/public/login
 *   2. Acceso vía .htaccess raíz (rewrite):    /ontobyte/login
 *   3. Web root apuntando a public/:           /login
 * Se quita el prefijo del script (SCRIPT_NAME) y, de no coincidir,
 * el prefijo de la URL base (BASE_URL).
 * -------------------------------------------------------------------------- */

/* Prefijo del directorio del script (ej: /ontobyte/public) */
$scriptDir = rtrim((string) dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');

/* Lista priorizada de prefijos a eliminar de la URL */
$prefixesToStrip = array_unique(array_filter([
    $scriptDir,                      /* /ontobyte/public (acceso directo) */
    PUBLIC_URL,                      /* /ontobyte/public (formato constante) */
    BASE_URL                         /* /ontobyte (vía rewrite de raíz) */
]));

foreach ($prefixesToStrip as $prefix) {
    /* Solo considerar prefijos no vacíos y distintos de la raíz */
    if ($prefix === '' || $prefix === '/' || $prefix === '\\') {
        continue;
    }

    /* Si la URL actual comienza con este prefijo en un límite de ruta */
    if (strpos($basePath, $prefix) === 0) {
        $basePath = substr($basePath, strlen($prefix));
        break;
    }
}

/* Limpiar la ruta: eliminar barras extras al inicio y final */
$basePath = '/' . trim($basePath, '/');

/* Si la ruta está vacía, asignar la ruta raíz */
if (empty($basePath) || $basePath === '/') {
    $basePath = '/';
}

/* --------------------------------------------------------------------------
 * PASO 3: Determinar el método HTTP de la petición
 * Se utiliza para distinguir rutas GET de rutas POST (envío de formularios).
 * -------------------------------------------------------------------------- */
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* --------------------------------------------------------------------------
 * PASO 4: Definir el sistema de rutas (Router)
 * Array asociativo que mapea rutas URL => [Controlador, Método].
 * Cada ruta define qué controlador y método se ejecutan al acceder a ella.
 * Las rutas POST se almacenan en un sub-array separado para manejar el
 * envío de formularios de forma segura y diferenciada.
 * -------------------------------------------------------------------------- */

/* Rutas de tipo GET: muestran contenido (formularios y páginas) */
$getRoutes = [
    /* --- Rutas públicas --- */
    '/'                 => ['PageController', 'home'],          /* Página principal / Inicio */
    '/home'             => ['PageController', 'home'],          /* Alias de la página principal */
    '/about'            => ['PageController', 'about'],         /* Página "Nosotros" */
    '/services'         => ['PageController', 'services'],      /* Página de servicios */
    '/team'             => ['PageController', 'team'],          /* Página del equipo */
    '/prices'           => ['PageController', 'prices'],        /* Página de planes y precios */

    /* --- Rutas de autenticación --- */
    '/login'            => ['AuthController', 'login'],         /* Formulario de inicio de sesión */
    '/register'         => ['AuthController', 'register'],      /* Formulario de registro */
    '/logout'           => ['AuthController', 'logout'],        /* Cerrar sesión */
    '/dashboard'        => ['AuthController', 'dashboard'],     /* Panel de bienvenida (protegido por sesión) */

    /* --- Rutas de contacto --- */
    '/contact'          => ['ContactController', 'index'],      /* Página/formulario de contacto */

    /* --- Rutas del panel de administración --- */
    '/admin'            => ['AdminController', 'dashboard'],    /* Panel de administración */
    '/admin/contacts'   => ['AdminController', 'contacts'],     /* Gestión de mensajes de contacto */
    '/admin/users'      => ['AdminController', 'users'],        /* Gestión de usuarios registrados */
    '/admin/user-edit'  => ['AdminController', 'userEdit'],     /* Formulario de edición de usuario */
];

/* Rutas de tipo POST: procesan el envío de formularios */
$postRoutes = [
    '/login'            => ['AuthController', 'authenticate'],  /* Procesar inicio de sesión */
    '/register'         => ['AuthController', 'store'],         /* Procesar registro de usuario */
    '/contact/submit'   => ['ContactController', 'submit'],     /* Procesar formulario de contacto */
    '/auth/google'      => ['AuthController', 'googleCallback'],/* Procesar credencial de Google OAuth 2.0 */
    '/admin/users/update' => ['AdminController', 'updateUser'], /* Guardar cambios de un usuario */
    '/admin/users/delete' => ['AdminController', 'deleteUser'], /* Eliminar un usuario */
];

/* Seleccionar el array de rutas según el método HTTP */
$routes = ($requestMethod === 'POST') ? $postRoutes : $getRoutes;

/* --------------------------------------------------------------------------
 * PASO 5: Resolver la ruta solicitada
 * Busca la ruta actual en el array de rutas definidas.
 * Si no existe, muestra una página 404 (no encontrado).
 * -------------------------------------------------------------------------- */
if (array_key_exists($basePath, $routes)) {
    /* La ruta fue encontrada: obtener controlador y método */
    $controllerName = $routes[$basePath][0];
    $methodName     = $routes[$basePath][1];
} else {
    /* Ruta no encontrada: mostrar página 404 */
    http_response_code(404);
    require_once VIEWS_PATH . '/errors/404.php';
    exit;
}

/* --------------------------------------------------------------------------
 * PASO 6: Cargar y ejecutar el controlador
 * Se construye la ruta al archivo del controlador, se verifica que exista,
 * se instancia el controlador y se llama al método correspondiente.
 * -------------------------------------------------------------------------- */
$controllerFile = CONTROLLERS_PATH . '/' . $controllerName . '.php';

/* Verificar que el archivo del controlador exista */
if (!file_exists($controllerFile)) {
    http_response_code(500);
    if (APP_DEBUG) {
        die("Error: Controlador no encontrado - {$controllerName}");
    }
    die('Error interno del servidor.');
}

/* Incluir el archivo del controlador */
require_once $controllerFile;

/* Verificar que la clase del controlador exista */
if (!class_exists($controllerName)) {
    http_response_code(500);
    if (APP_DEBUG) {
        die("Error: Clase no encontrada - {$controllerName}");
    }
    die('Error interno del servidor.');
}

/* Verificar que el método exista en el controlador */
if (!method_exists($controllerName, $methodName)) {
    http_response_code(404);
    if (APP_DEBUG) {
        die("Error: Método no encontrado - {$controllerName}::{$methodName}");
    }
    die('Página no encontrada.');
}

/* --------------------------------------------------------------------------
 * PASO 7: Ejecutar el controlador con Dependency Injection simple
 * Se crea una instancia del controlador y se ejecuta el método solicitado.
 * Se pasa la variable $basePath como parámetro por si el controlador
 * necesita información adicional de la ruta.
 * -------------------------------------------------------------------------- */
$controller = new $controllerName();
$controller->$methodName();
