<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/layouts/main.php
 * ============================================================================
 * Propósito: Layout principal del sitio. Define la estructura HTML común
 *            del documento y orquesta la inclusión del header, la vista
 *            específica de cada página (a través de la variable $view)
 *            y el footer. Todas las páginas comparten esta estructura.
 *
 * Implementación con buffer de salida:
 *   1. Se captura primero el contenido de la vista solicitada (ob_start).
 *   2. De esta forma, las variables definidas dentro de la vista
 *      (como $pageTitle) quedan disponibles ANTES de renderizar <head>,
 *      permitiendo títulos dinámicos por página.
 *
 * Variables disponibles (inyectadas por el controlador):
 *   $view       - Nombre de la vista a incluir (ej: 'pages/home')
 *   $activePage - Página activa para resaltar el menú (opcional)
 * ============================================================================
 */

/* --------------------------------------------------------------------------
 * PASO 1: Capturar el contenido de la vista solicitada
 * Se inicia el buffer y se incluye la vista. Las variables definidas en
 * la vista persisten en el ámbito actual tras el require.
 * -------------------------------------------------------------------------- */
ob_start();
require_once VIEWS_PATH . '/' . $view . '.php';
$viewContent = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OntoByte - Empresa de base tecnológica y humanista. Desarrollamos software de alto impacto con propósito y ética.">
    <meta name="keywords" content="OntoByte, desarrollo web, desarrollo móvil, inteligencia artificial, ciberseguridad, cloud computing">
    <meta name="author" content="OntoByte">

    <!-- Título dinámico de la página (definido en la vista o por defecto) -->
    <title><?php echo isset($pageTitle) && $pageTitle !== '' ? htmlspecialchars($pageTitle) : APP_NAME; ?> | <?php echo APP_NAME; ?></title>

    <!-- Ícono de la aplicación (favicon) creado con el logo oficial SVG
         Ruta absoluta relativa al Front Controller: /ontobyte/public/img/favicon.svg
         Sustituir public/img/favicon.svg por un .ico/.png si se prefiere formato ráster. -->
    <link rel="icon" type="image/svg+xml" href="<?php echo IMG_URL; ?>/favicon.svg">

    <!-- Hojas de estilo (Google Fonts + CSS principal + íconos Font Awesome) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/style.css">
</head>
<body>

    <!-- Barra de navegación principal -->
    <?php require_once VIEWS_PATH . '/layouts/navbar.php'; ?>

    <!-- Contenido principal de la página (contenido capturado de la vista) -->
    <main class="main-content">
        <?php echo $viewContent; ?>
    </main>

    <!-- Pie de página corporativo -->
    <?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>

    <!-- Scripts JavaScript (menú móvil, validaciones client-side, contadores) -->
    <script src="<?php echo JS_URL; ?>/main.js"></script>
</body>
</html>