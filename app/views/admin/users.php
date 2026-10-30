<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/admin/users.php
 * ============================================================================
 * Propósito: Vista de gestión de usuarios registrados en el panel de
 *            administración. Lista a todos los usuarios con su rol, estado,
 *            fecha de registro y acciones (editar / eliminar).
 *
 * Variables disponibles:
 *   $users     - Lista completa de usuarios registrados
 *   $csrfToken - Token CSRF para los formularios de eliminación
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Gestión de Usuarios';

/* Cargar mensajes flash */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     ENCABEZADO DE LA PÁGINA DE GESTIÓN
     ============================================================ -->
<section class="admin-header">
    <div class="container">
        <h1 class="admin-title">
            <i class="fas fa-users" aria-hidden="true"></i>
            Gestión de Usuarios
        </h1>
        <p class="admin-subtitle">
            Consulta quién se ha registrado en el sistema: edita sus datos,
            cambia su rol o estado, o elimínalo si es necesario.
        </p>
    </div>
</section>

<!-- ============================================================
     LISTA DE USUARIOS REGISTRADOS
     ============================================================ -->
<section class="section">
    <div class="container">
        <?php if (!empty($users)): ?>
            <!-- Tabla responsiva de usuarios -->
            <div class="table-responsive">
                <table class="admin-table">
                    <!-- Encabezados de la tabla -->
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <!-- Cuerpo de la tabla: cada usuario es una fila -->
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <!-- Datos del usuario -->
                            <td>
                                <div class="contact-sender">
                                    <span class="contact-avatar" aria-hidden="true">
                                        <?php echo strtoupper(substr(htmlspecialchars($user['first_name']), 0, 1)); ?>
                                    </span>
                                    <div>
                                        <strong>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            <?php if ((int) $user['id'] === (int) ($_SESSION['user_id'] ?? 0)): ?>
                                                <small class="text-muted">(tú)</small>
                                            <?php endif; ?>
                                        </strong><br>
                                        <small><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></a></small>
                                    </div>
                                </div>
                            </td>

                            <!-- Rol del usuario -->
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="status-badge read" title="Administrador"><i class="fas fa-crown" aria-hidden="true"></i> Admin</span>
                                <?php else: ?>
                                    <span class="status-badge read" title="Usuario"><i class="fas fa-user" aria-hidden="true"></i> Usuario</span>
                                <?php endif; ?>
                            </td>

                            <!-- Estado de la cuenta -->
                            <td>
                                <?php if ((int) $user['is_active'] === 1): ?>
                                    <span class="status-badge unread" title="Activo"><i class="fas fa-check-circle" aria-hidden="true"></i> Activo</span>
                                <?php else: ?>
                                    <span class="status-badge inactive" title="Inactivo"><i class="fas fa-ban" aria-hidden="true"></i> Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <!-- Fecha de registro -->
                            <td class="contact-date">
                                <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </td>

                            <!-- Acciones: editar y eliminar -->
                            <td>
                                <div class="admin-user-actions">
                                    <a href="<?php echo BASE_URL; ?>/admin/user-edit?id=<?php echo (int) $user['id']; ?>"
                                       class="btn btn-sm btn-primary" title="Editar usuario">
                                        <i class="fas fa-edit" aria-hidden="true"></i> Editar
                                    </a>

                                    <?php if ((int) $user['id'] !== (int) ($_SESSION['user_id'] ?? 0)): ?>
                                        <form action="<?php echo BASE_URL; ?>/admin/users/delete" method="POST"
                                              class="inline-form"
                                              onsubmit="return confirm('¿Estás seguro de eliminar a <?php echo addslashes(htmlspecialchars($user['first_name'] . ' ' . $user['last_name'])); ?>? Esta acción no se puede deshacer.');">
                                            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                            <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar usuario">
                                                <i class="fas fa-trash-alt" aria-hidden="true"></i> Eliminar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted" title="No puedes eliminarte a ti mismo">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje de estado vacío -->
            <div class="empty-state admin-empty">
                <i class="fas fa-users" aria-hidden="true"></i>
                <h3>No hay usuarios registrados todavía</h3>
                <p>Cuando las personas se registren en el sistema, aparecerán aquí.</p>
            </div>
        <?php endif; ?>
    </div>
</section>