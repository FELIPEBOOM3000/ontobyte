<?php
/**
 * ============================================================================
 * ARCHIVO: app/controllers/ContactController.php
 * ============================================================================
 * Propósito: Controlador del módulo de contacto. Gestiona la visualización
 *            del formulario de contacto y el procesamiento de los mensajes
 *            que los visitantes envían a la empresa OntoByte.
 *            Implementa validaciones estrictas y protección CSRF.
 * ============================================================================
 */

require_once CONTROLLERS_PATH . '/Controller.php';

class ContactController extends Controller
{
    /** @var Contact Instancia del modelo de contacto */
    private $contactModel;

    /**
     * Constructor del controlador de contacto.
     * Inicializa el modelo de contacto para las operaciones de datos.
     */
    public function __construct()
    {
        require_once MODELS_PATH . '/Contact.php';
        $this->contactModel = new Contact();
    }

    /**
     * Muestra la página de contacto con el formulario corporativo.
     *
     * @return void
     */
    public function index(): void
    {
        /* Obtener lista de servicios para el select del formulario */
        require_once MODELS_PATH . '/Service.php';
        $services = (new Service())->getActiveServices();

        /* Generar token CSRF para el formulario */
        $csrfToken = $this->generateCsrfToken();

        /* Renderizar la vista de contacto */
        $this->render('pages/contact', [
            'services'   => $services,
            'csrfToken'  => $csrfToken,
            'activePage' => 'contact'
        ]);
    }

    /**
     * Procesa el envío del formulario de contacto.
     * Valida el token CSRF, saneza los datos de entrada y almacena
     * el mensaje en la base de datos con sentencias preparadas.
     *
     * @return void
     */
    public function submit(): void
    {
        /* PASO 1: Validar el token CSRF */
        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            $this->redirect('/contact');
        }

        /* PASO 2: Sanitizar y validar cada campo del formulario */
        $fullName = $this->sanitize($_POST['full_name'] ?? '');
        $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone    = isset($_POST['phone']) ? $this->sanitize($_POST['phone']) : '';
        $subject  = $this->sanitize($_POST['subject'] ?? '');
        $message  = $this->sanitize($_POST['message'] ?? '');
        $serviceInterest = $this->sanitize($_POST['service_interest'] ?? '');

        /* --- Validaciones estrictas de cada campo --- */

        /* Nombre completo: mínimo 3 caracteres, solo letras y espacios */
        if (strlen($fullName) < 3 || !preg_match('/^[a-zA-ZáéíóúñÑüÜÁÉÍÓÚ\s\.]+$/', $fullName)) {
            $this->setFlash('error', 'Ingresa tu nombre completo (solo letras, mínimo 3 caracteres).');
            $this->redirect('/contact');
        }

        /* Email válido */
        if (!$email) {
            $this->setFlash('error', 'Por favor ingresa un email válido.');
            $this->redirect('/contact');
        }

        /* Asunto: mínimo 3 caracteres */
        if (strlen($subject) < 3) {
            $this->setFlash('error', 'El asunto debe tener al menos 3 caracteres.');
            $this->redirect('/contact');
        }

        /* Mensaje: mínimo 10 caracteres */
        if (strlen($message) < 10) {
            $this->setFlash('error', 'El mensaje debe contener al menos 10 caracteres.');
            $this->redirect('/contact');
        }

        /* PASO 3: Almacenar el mensaje en la base de datos */
        $contactId = $this->contactModel->create([
            'full_name'        => $fullName,
            'email'            => $email,
            'phone'            => $phone,
            'subject'          => $subject,
            'message'          => $message,
            'service_interest' => $serviceInterest
        ]);

        /* PASO 4: Retroalimentación al usuario */
        if ($contactId) {
            $this->setFlash('success', 'Gracias ' . $fullName . ', tu mensaje fue enviado. Nuestro equipo te contactará pronto.');
        } else {
            $this->setFlash('error', 'No fue posible enviar el mensaje. Por favor intenta de nuevo.');
        }

        $this->redirect('/contact');
    }
}