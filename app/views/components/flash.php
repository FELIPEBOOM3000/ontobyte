<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/components/flash.php
 * ============================================================================
 * Propósito: Componente reutilizable para mostrar mensajes flash.
 *            Estos mensajes se muestran una sola vez después de una
 *            redirección (PRG: Post-Redirect-Get) y luego se eliminan
 *            de la sesión, evitando que se muestren repetidamente.
 *
 * Estilos: success (verde), error (rojo), warning (ámbar), info (azul).
 * ============================================================================
 */

/* Verificar si existen mensajes flash pendientes de mostrar */
if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
    <!-- Contenedor de mensajes flash -->
    <div class="flash-messages" aria-live="polite">
        <?php foreach ($_SESSION['flash_messages'] as $flash): ?>
            <!-- Mensaje individual según su tipo -->
            <div class="flash-message flash-<?php echo htmlspecialchars($flash['type']); ?>" role="alert">
                <!-- Ícono según el tipo de mensaje -->
                <?php if ($flash['type'] === 'success'): ?>
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                <?php elseif ($flash['type'] === 'error'): ?>
                    <i class="fas fa-times-circle" aria-hidden="true"></i>
                <?php elseif ($flash['type'] === 'warning'): ?>
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                <?php endif; ?>

                <p><?php echo htmlspecialchars($flash['message']); ?></p>

                <!-- Botón para cerrar manualmente el mensaje -->
                <button type="button" class="flash-close" aria-label="Cerrar mensaje">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    /* Eliminar los mensajes flash después de mostrarlos (una sola vez) */
    unset($_SESSION['flash_messages']);
endif;
?>