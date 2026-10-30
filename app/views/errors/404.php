<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/errors/404.php
 * ============================================================================
 * Propósito: Página personalizada de error 404 (Página no encontrada).
 *            Se muestra cuando la URL solicitada no coincide con ninguna
 *            ruta definida en el Front Controller.
 * ============================================================================
 */
$pageTitle = 'Página no encontrada';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - No encontrada | <?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/style.css">
</head>
<body>
    <main class="error-page">
        <!-- Logo de la empresa -->
        <a href="<?php echo BASE_URL; ?>/" class="brand error-brand">
            <span class="brand-logo" aria-hidden="true">O</span>
            <span class="brand-text"><span class="brand-name">Onto<em>Byte</em></span></span>
        </a>

        <!-- Código de error visual marcado con la tipografía de títulos -->
        <div class="error-code">404</div>

        <!-- Mensaje descriptivo -->
        <h1 class="error-title">Ups. La página que buscas no existe.</h1>
        <p class="error-text">
            Tal vez el enlace cambió o la dirección fue escrita incorrectamente.
            Nuestro equipo tecnológico te ayudará a volver al camino correcto.
        </p>

        <!-- Botones de navegación de retorno -->
        <div class="error-actions">
            <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary btn-lg">
                <i class="fas fa-home" aria-hidden="true"></i> Volver al inicio
            </a>
            <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-outline-lg">
                <i class="fas fa-life-ring" aria-hidden="true"></i> Contactar soporte
            </a>
        </div>
    </main>
</body>
</html>