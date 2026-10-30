<?php
/**
 * ============================================================================
 * ARCHIVO: app/controllers/PageController.php
 * ============================================================================
 * Propósito: Controlador de páginas públicas del sitio corporativo.
 *            Gestiona la lógica de las páginas estáticas y dinámicas:
 *            inicio, nosotros, servicios y equipo. Consulta los modelos
 *            correspondientes para obtener datos dinámicos.
 * ============================================================================
 */

require_once CONTROLLERS_PATH . '/Controller.php';

class PageController extends Controller
{
    /**
     * Constructor del controlador de páginas públicas.
     * Carga los modelos necesarios para consultar los datos dinámicos
     * que se muestran en el sitio corporativo.
     */
    public function __construct()
    {
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/TeamMember.php';
        require_once MODELS_PATH . '/Testimonial.php';
    }

    /**
     * Página principal de OntoByte.
     * Obtiene los datos corporativos de la base de datos: servicios,
     * miembros del equipo y testimonios para construir la página home.
     *
     * @return void
     */
    public function home(): void
    {
        /* Instanciar los modelos necesarios para consultar datos */
        $serviceModel    = new Service();
        $teamMemberModel = new TeamMember();
        $testimonialModel = new Testimonial();

        /* Obtener datos dinámicos de la base de datos */
        $services    = $serviceModel->getActiveServices();
        $teamMembers = $teamMemberModel->getActiveMembers();
        $testimonials = $testimonialModel->getActiveTestimonials();

        /* Generar token CSRF para el formulario de contacto embebido en el home */
        $csrfToken = $this->generateCsrfToken();

        /* Renderizar la vista home con todos los datos obtenidos */
        $this->render('pages/home', [
            'services'     => $services,
            'teamMembers'  => $teamMembers,
            'testimonials' => $testimonials,
            'csrfToken'    => $csrfToken,
            'activePage'   => 'home'
        ]);
    }

    /**
     * Página "Nosotros".
     * Presenta la información institucional, misión, visión y valores
     * de la empresa OntoByte.
     *
     * @return void
     */
    public function about(): void
    {
        /* Renderizar la vista about */
        $this->render('pages/about', [
            'teamMembers' => (new TeamMember())->getActiveMembers(),
            'activePage'  => 'about'
        ]);
    }

    /**
     * Página de "Servicios".
     * Muestra el catálogo completo de servicios que ofrece OntoByte,
     * consultados desde el modelo Service.
     *
     * @return void
     */
    public function services(): void
    {
        /* Obtener todos los servicios activos */
        $services = (new Service())->getActiveServices();

        /* Renderizar la vista services */
        $this->render('pages/services', [
            'services'   => $services,
            'activePage' => 'services'
        ]);
    }

    /**
     * Página de "Precios".
     * Presenta los planes de desarrollo, el desglose de servicios
     * y los módulos adicionales con sus tarifas en pesos colombianos.
     * Página estática: los valores se definen directamente en la vista.
     *
     * @return void
     */
    public function prices(): void
    {
        /* Renderizar la vista prices */
        $this->render('pages/prices', [
            'activePage' => 'prices'
        ]);
    }

    /**
     * Página de "Equipo".
     * Presenta a los miembros del equipo de trabajo de OntoByte.
     *
     * @return void
     */
    public function team(): void
    {
        /* Obtener los miembros activos del equipo */
        $teamMembers = (new TeamMember())->getActiveMembers();

        /* Renderizar la vista team */
        $this->render('pages/team', [
            'teamMembers' => $teamMembers,
            'activePage'  => 'team'
        ]);
    }
}