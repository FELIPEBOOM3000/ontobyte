<?php
/**
 * ============================================================================
 * ARCHIVO: app/controllers/Controller.php
 * ============================================================================
 * Propósito: Clase base del patrón MVC de la cual heredan todos los
 *            controladores del sistema. Proporciona funcionalidad común:
 *            renderizado de vistas, protección CSRF, validación y
 *            sanitización de entradas, mensajes flash y control de sesión.
 * ============================================================================
 */

class Controller
{
    /** @var array Datos compartidos que se pasan a todas las vistas */
    protected $viewData = [];

    /**
     * Renderiza una vista dentro del layout principal de la aplicación.
     * Los datos del controlador se extraen como variables locales del
     * archivo de la vista (estructura limpia del patrón MVC).
     *
     * @param string $view Nombre de la vista (ej: 'home', 'auth/login')
     * @param array  $data Datos que se inyectan a la vista (clave => valor)
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        /* Fusionar datos de la vista con los datos del controlador */
        $data = array_merge($this->viewData, $data);

        /* Extraer las claves como variables accesibles en la vista */
        extract($data);

        /* Definir la ruta completa del archivo de la vista */
        $viewFile = VIEWS_PATH . '/' . $view . '.php';

        /* Verificar que la vista exista */
        if (!file_exists($viewFile)) {
            if (APP_DEBUG) {
                throw new Exception("Error: Vista no encontrada - {$view}");
            }
            http_response_code(500);
            exit('Error interno del servidor.');
        }

        /* Incluir el layout principal que a su vez incluye la vista específica */
        require_once VIEWS_PATH . '/layouts/main.php';
    }

    /**
     * Redirige al usuario a una URL interna de la aplicación.
     * Utiliza encabezado HTTP 302 (Found) y termina la ejecución.
     *
     * @param string $path Ruta interna (ej: '/login')
     * @return void
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /**
     * Genera (o reutiliza) el token CSRF de la sesión actual.
     * Los tokens CSRF protegen los formularios contra ataques de
     * Cross-Site Request Forgery.
     *
     * @return string Token CSRF aleatorio de la sesión
     */
    protected function generateCsrfToken(): string
    {
        /* Si no existe token en la sesión, generarlo de forma segura */
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Valida el token CSRF enviado por POST contra el token de sesión.
     * Comparación con hash_equals() para evitar ataques por timing.
     *
     * @return bool True si el token es válido, false en caso contrario
     */
    protected function validateCsrfToken(): bool
    {
        /* Verificar que se envió el token en la solicitud */
        $sentToken = $_POST[CSRF_TOKEN_NAME] ?? '';

        /* Verificar que exista un token en la sesión */
        $sessionToken = $_SESSION[CSRF_TOKEN_NAME] ?? '';

        /* Comparación segura de tokens (constante de tiempo) */
        return !empty($sessionToken) && hash_equals($sessionToken, $sentToken);
    }

    /**
     * Sanitiza un valor de entrada eliminando etiquetas HTML y caracteres
     * peligrosos. Protege contra ataques XSS.
     *
     * @param string $value Valor a limpiar
     * @return string Valor saneado
     */
    protected function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Establece un mensaje flash en la sesión para mostrarlo una sola vez.
     * Estos mensajes se usan para retroalimentar al usuario después de
     * una redirección.
     *
     * @param string $type Tipo de mensaje: 'success' | 'error' | 'info' | 'warning'
     * @param string $message Contenido del mensaje
     * @return void
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash_messages'][] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    /**
     * Verifica si un usuario está autenticado en el sistema.
     *
     * @return bool True si el usuario inició sesión
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    /**
     * Verifica si la sesión activa es de un administrador.
     * Se utiliza para proteger rutas del panel de administración.
     *
     * @return bool True si el usuario autenticado es administrador
     */
    protected function isAdmin(): bool
    {
        return $this->isAuthenticated()
            && isset($_SESSION['user_role'])
            && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Redirige a la página de login si el usuario no está autenticado.
     * Protección de rutas privadas del sistema.
     *
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->setFlash('warning', 'Debes iniciar sesión para acceder a esta sección.');
            $this->redirect('/login');
        }
    }

    /**
     * Redirige al panel de administración si el usuario no es admin.
     * Protección de rutas de administración.
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'No tienes permisos para acceder a esta sección.');
            $this->redirect('/');
        }
    }
}