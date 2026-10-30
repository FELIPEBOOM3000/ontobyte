<?php
/**
 * ============================================================================
 * ARCHIVO: app/controllers/AdminController.php
 * ============================================================================
 * Propósito: Controlador del panel de administración. Gestiona el acceso
 *            exclusivo de usuarios con rol 'admin' al área privada del
 *            sistema y la gestión de los mensajes de contacto recibidos.
 *            Todas las rutas protegidas exigen autenticación y rol admin.
 * ============================================================================
 */

require_once CONTROLLERS_PATH . '/Controller.php';

class AdminController extends Controller
{
    /** @var Contact Instancia del modelo de contacto */
    private $contactModel;

    /**
     * Constructor del controlador de administración.
     * Todas las páginas del panel exigen usuario administrador.
     */
    public function __construct()
    {
        require_once MODELS_PATH . '/Contact.php';
        $this->contactModel = new Contact();
    }

    /**
     * Panel principal de administración.
     * Muestra un resumen estadístico del sistema: total de usuarios,
     * mensajes de contacto recibidos y mensajes sin leer.
     * Acceso restringido a administradores.
     *
     * @return void
     */
    public function dashboard(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        /* Obtener estadísticas para el tablero de control */
        require_once MODELS_PATH . '/User.php';
        $totalUsers   = (new User())->count();
        $totalContacts = $this->contactModel->count();
        $unreadContacts = $this->contactModel->countUnread();

        /* Renderizar el panel de administración con las estadísticas */
        $this->render('admin/dashboard', [
            'totalUsers'     => $totalUsers,
            'totalContacts'  => $totalContacts,
            'unreadContacts' => $unreadContacts,
            'activePage'     => 'admin'
        ]);
    }

    /**
     * Lista todos los mensajes de contacto recibidos.
     * Cada mensaje se muestra con su estado (leído, respondido).
     * Acceso restringido a administradores.
     *
     * @return void
     */
    public function contacts(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        /* Obtener todos los mensajes de contacto ordenados por fecha */
        require_once MODELS_PATH . '/Model.php';
        $contacts = $this->executeAllContacts();

        /* Renderizar la vista de gestión de contactos */
        $this->render('admin/contacts', [
            'contacts'   => $contacts,
            'activePage' => 'admin-contacts'
        ]);
    }

    /**
     * Método auxiliar que obtiene todos los contactos ordenados
     * por fecha de recepción (más recientes primero).
     * Utiliza consulta preparada para garantizar seguridad.
     *
     * @return array Lista de mensajes de contacto
     */
    private function executeAllContacts(): array
    {
        require_once MODELS_PATH . '/Model.php';

        /* Obtener la conexión PDO a través del modelo base */
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM mensajes_contacto ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* ================================================================
     * GESTIÓN DE USUARIOS REGISTRADOS (CRUD del administrador)
     * ================================================================ */

    /**
     * Lista todos los usuarios registrados en el sistema.
     * Muestra nombres, email, rol, estado, fecha de registro y acciones
     * (editar / eliminar). Acceso restringido a administradores.
     *
     * @return void
     */
    public function users(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        /* Obtener todos los usuarios ordenados por fecha de registro */
        require_once MODELS_PATH . '/User.php';
        $users = (new User())->all();

        /* Renderizar la vista de gestión de usuarios */
        $this->render('admin/users', [
            'users'      => $users,
            'csrfToken'  => $this->generateCsrfToken(),
            'activePage' => 'admin-users'
        ]);
    }

    /**
     * Muestra el formulario de edición de un usuario específico.
     * Obtiene el usuario por ID vía query string (?id=N).
     * Acceso restringido a administradores.
     *
     * @return void
     */
    public function userEdit(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        require_once MODELS_PATH . '/User.php';
        $user = $id > 0 ? (new User())->find($id) : null;

        if (!$user) {
            $this->setFlash('error', 'El usuario solicitado no existe.');
            $this->redirect('/admin/users');
        }

        /* Renderizar el formulario de edición */
        $this->render('admin/user-edit', [
            'user'       => $user,
            'csrfToken'  => $this->generateCsrfToken(),
            'activePage' => 'admin-users'
        ]);
    }

    /**
     * Procesa la actualización de los datos de un usuario.
     * Valida el token CSRF, sane las entradas y evita que el
     * administrador modifique o desactive su propia cuenta.
     * Acceso restringido a administradores.
     *
     * @return void
     */
    public function updateUser(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        /* Validar token CSRF contra ataques de falsificación de petición */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/admin/users');
        }

        $id = (int) ($_POST['id'] ?? 0);

        require_once MODELS_PATH . '/User.php';
        $userModel = new User();
        $user = $id > 0 ? $userModel->find($id) : null;

        if (!$user) {
            $this->setFlash('error', 'El usuario solicitado no existe.');
            $this->redirect('/admin/users');
        }

        /* Evitar que el admin se desactive o cambie su propio rol */
        if ((int) $user['id'] === (int) ($_SESSION['user_id'] ?? 0)) {
            $this->setFlash('error', 'No puedes cambiar el rol ni desactivar tu propia cuenta.');
            $this->redirect('/admin/users');
        }

        $firstName = $this->sanitize($_POST['first_name'] ?? '');
        $lastName  = $this->sanitize($_POST['last_name'] ?? '');

        if ($firstName === '' || $lastName === '') {
            $this->setFlash('error', 'El nombre y el apellido son obligatorios.');
            $this->redirect('/admin/user-edit?id=' . $user['id']);
        }

        $updated = $userModel->update($user['id'], [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'phone'      => $this->sanitize($_POST['phone'] ?? ''),
            'role'       => $_POST['role'] ?? 'user',
            'is_active'  => $_POST['is_active'] ?? '0',
            'password'   => $_POST['password'] ?? ''
        ]);

        $this->setFlash(
            $updated ? 'success' : 'error',
            $updated
                ? 'El usuario fue actualizado correctamente.'
                : 'No se pudo actualizar el usuario. Intenta de nuevo.'
        );
        $this->redirect('/admin/users');
    }

    /**
     * Elimina un usuario del sistema.
     * Valida el token CSRF y evita que el administrador se elimine a sí mismo.
     * Acceso restringido a administradores.
     *
     * @return void
     */
    public function deleteUser(): void
    {
        /* Proteger la ruta: solo administradores autenticados */
        $this->requireAdmin();

        /* Validar token CSRF */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/admin/users');
        }

        $id = (int) ($_POST['id'] ?? 0);

        require_once MODELS_PATH . '/User.php';
        $userModel = new User();

        /* Evitar que el admin se elimine a sí mismo */
        if ($id > 0 && (int) $id === (int) ($_SESSION['user_id'] ?? 0)) {
            $this->setFlash('error', 'No puedes eliminar tu propia cuenta.');
            $this->redirect('/admin/users');
        }

        $user = $id > 0 ? $userModel->find($id) : null;
        if (!$user) {
            $this->setFlash('error', 'El usuario solicitado no existe.');
            $this->redirect('/admin/users');
        }

        $deleted = $userModel->delete($id);
        $this->setFlash(
            $deleted ? 'success' : 'error',
            $deleted
                ? 'El usuario ' . $user['email'] . ' fue eliminado.'
                : 'No se pudo eliminar el usuario. Intenta de nuevo.'
        );
        $this->redirect('/admin/users');
    }
}