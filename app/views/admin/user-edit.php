<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/admin/user-edit.php
 * ============================================================================
 * Propósito: Vista del formulario de edición de un usuario registrado.
 *            Permite al administrador corregir nombres, teléfono, rol,
 *            estado de la cuenta y (opcionalmente) asignar una contraseña
 *            nueva. Incluye protección CSRF y escapado de salida (XSS).
 *
 * Variables disponibles:
 *   $user      - Registro completo del usuario a editar
 *   $csrfToken - Token CSRF para el formulario
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Editar Usuario';

/* Cargar mensajes flash */
require_once VIEWS_PATH . '/components/flash.php';
?>

<!-- ============================================================
     ENCABEZADO DE LA PÁGINA DE EDICIÓN
     ============================================================ -->
<section class="admin-header">
    <div class="container">
        <h1 class="admin-title">
            <i class="fas fa-user-edit" aria-hidden="true"></i>
            Editar Usuario
        </h1>
        <p class="admin-subtitle">
            Actualiza los datos de
            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
            (<?php echo htmlspecialchars($user['email']); ?>).
        </p>
    </div>
</section>

<!-- ============================================================
     FORMULARIO DE EDICIÓN DE USUARIO
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="auth-card">
            <form action="<?php echo BASE_URL; ?>/admin/users/update" method="POST" class="auth-form" novalidate>
                <!-- Token CSRF oculto: protección contra ataques CSRF -->
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <!-- ID del usuario a actualizar -->
                <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">

                <!-- Nombre del usuario -->
                <div class="form-group">
                    <label for="first_name">Nombre <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input type="text" id="first_name" name="first_name" class="form-input"
                               value="<?php echo htmlspecialchars($user['first_name']); ?>"
                               maxlength="100" data-validate="name" required>
                    </div>
                </div>

                <!-- Apellido del usuario -->
                <div class="form-group">
                    <label for="last_name">Apellido <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input type="text" id="last_name" name="last_name" class="form-input"
                               value="<?php echo htmlspecialchars($user['last_name']); ?>"
                               maxlength="100" data-validate="name" required>
                    </div>
                </div>

                <!-- Correo (solo lectura: es la identidad de acceso) -->
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <input type="email" id="email" class="form-input"
                               value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    <small class="form-hint">El correo es la identidad del usuario y no puede modificarse.</small>
                </div>

                <!-- Teléfono del usuario -->
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <input type="tel" id="phone" name="phone" class="form-input"
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                               maxlength="20" data-validate="phone" placeholder="Opcional">
                    </div>
                </div>

                <!-- Nueva contraseña (opcional) -->
                <div class="form-group">
                    <label for="password">Nueva contraseña <span class="text-muted">(opcional)</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Déjala vacía si no quieres cambiarla"
                               autocomplete="new-password" data-validate="password">
                    </div>
                    <small class="form-hint">Mínimo 8 caracteres, con mayúscula, minúscula y número.</small>
                </div>

                <!-- Rol del usuario -->
                <div class="form-group">
                    <label for="role">Rol <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                        <select id="role" name="role" class="form-input">
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>

                <!-- Estado de la cuenta -->
                <div class="form-group">
                    <label for="is_active">Estado de la cuenta <span class="required" aria-hidden="true">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-check" aria-hidden="true"></i>
                        <select id="is_active" name="is_active" class="form-input">
                            <option value="1" <?php echo ((int) $user['is_active'] === 1) ? 'selected' : ''; ?>>Activo — puede iniciar sesión</option>
                            <option value="0" <?php echo ((int) $user['is_active'] === 0) ? 'selected' : ''; ?>>Inactivo — login bloqueado</option>
                        </select>
                    </div>
                </div>

                <!-- Acciones del formulario -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-save" aria-hidden="true"></i> Guardar cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>/admin/users" class="btn btn-outline btn-block">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al listado
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>