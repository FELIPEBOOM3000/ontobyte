<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/admin/contacts.php
 * ============================================================================
 * Propósito: Vista de gestión de mensajes de contacto en el panel de
 *            administración. Lista todos los mensajes recibidos con su
 *            estado (leído / respondido), remitente y fecha. Provee
 *            acceso al detalle y filtros por estado.
 *
 * Variables disponibles:
 *   $contacts - Lista completa de mensajes de contacto ordenados por fecha
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Gestión de Contactos';

/* Cargar mensajes flash */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     ENCABEZADO DE LA PÁGINA DE GESTIÓN
     ============================================================ -->
<section class="admin-header">
    <div class="container">
        <h1 class="admin-title">
            <i class="fas fa-inbox" aria-hidden="true"></i>
            Gestión de Mensajes
        </h1>
        <p class="admin-subtitle">
            Todos los mensajes enviados a través del formulario de contacto del sitio.
        </p>
    </div>
</section>

<!-- ============================================================
     LISTA DE MENSAJES DE CONTACTO
     ============================================================ -->
<section class="section">
    <div class="container">
        <?php if (!empty($contacts)): ?>
            <!-- Tabla responsiva de mensajes -->
            <div class="table-responsive">
                <table class="admin-table">
                    <!-- Encabezados de la tabla -->
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Remitente</th>
                            <th>Asunto</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <!-- Cuerpo de la tabla: cada mensaje es una fila -->
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                        <tr class="<?php echo ((int) $contact['is_read'] === 0) ? 'unread-row' : ''; ?>">
                            <!-- Indicador de estado (leído / no leído) -->
                            <td class="contact-status">
                                <?php if ((int) $contact['is_read'] === 0): ?>
                                    <span class="status-badge unread" title="No leído"><i class="fas fa-circle" aria-hidden="true"></i> Nuevo</span>
                                <?php elseif ((int) $contact['is_replied'] === 1): ?>
                                    <span class="status-badge replied" title="Respondido"><i class="fas fa-check-double" aria-hidden="true"></i> Respondido</span>
                                <?php else: ?>
                                    <span class="status-badge read" title="Leído"><i class="fas fa-check" aria-hidden="true"></i> Leído</span>
                                <?php endif; ?>
                            </td>

                            <!-- Datos del remitente -->
                            <td>
                                <div class="contact-sender">
                                    <span class="contact-avatar" aria-hidden="true">
                                        <?php echo strtoupper(substr(htmlspecialchars($contact['full_name']), 0, 1)); ?>
                                    </span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($contact['full_name']); ?></strong><br>
                                        <small><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></small>
                                    </div>
                                </div>
                            </td>

                            <!-- Asunto del mensaje -->
                            <td class="contact-subject"><?php echo htmlspecialchars($contact['subject']); ?></td>

                            <!-- Servicio de interés -->
                            <td>
                                <?php echo $contact['service_interest'] ? htmlspecialchars($contact['service_interest']) : '<em class="text-muted">No especificado</em>'; ?>
                            </td>

                            <!-- Fecha de recepción formateada -->
                            <td class="contact-date">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                <?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje de estado vacío -->
            <div class="empty-state admin-empty">
                <i class="fas fa-inbox" aria-hidden="true"></i>
                <h3>No hay mensajes de contacto todavía</h3>
                <p>Cuando los visitantes del sitio envíen un mensaje, aparecerá aquí.</p>
            </div>
        <?php endif; ?>
    </div>
</section>