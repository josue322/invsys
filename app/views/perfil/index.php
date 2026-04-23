<!-- Perfil de Usuario -->
<div class="config-page animate-fadeIn">
    <div class="row g-4">

        <!-- ============================================= -->
        <!-- COLUMNA IZQUIERDA: Datos del usuario -->
        <!-- ============================================= -->
        <div class="col-lg-5">

            <!-- Tarjeta de perfil -->
            <div class="config-section">
                <div class="perfil-header-card">
                    <div class="perfil-avatar">
                        <?= userInitials($usuario->nombre) ?>
                    </div>
                    <h4 class="perfil-nombre"><?= htmlspecialchars($usuario->nombre) ?></h4>
                    <p class="perfil-email"><?= htmlspecialchars($usuario->email) ?></p>
                    <span class="badge <?= roleBadgeClass($usuario->rol_nombre) ?> perfil-rol-badge"><?= $usuario->rol_nombre ?></span>
                    <div class="perfil-meta">
                        <div class="perfil-meta-item">
                            <i class="bi bi-calendar3"></i>
                            <span>Registrado: <?= date('d/m/Y', strtotime($usuario->created_at)) ?></span>
                        </div>
                        <?php if (!empty($usuario->ultimo_login)): ?>
                        <div class="perfil-meta-item">
                            <i class="bi bi-clock-history"></i>
                            <span>Último acceso: <?= date('d/m/Y H:i', strtotime($usuario->ultimo_login)) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Editar datos personales -->
            <div class="config-section">
                <div class="config-section-header">
                    <div class="config-section-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <h6 class="config-section-title">Datos personales</h6>
                        <p class="config-section-desc">Edita tu nombre y correo electrónico</p>
                    </div>
                </div>
                <div class="config-section-body">
                    <form method="POST" action="<?= url('perfil/info') ?>" id="formDatos">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre" 
                                   value="<?= htmlspecialchars($usuario->nombre) ?>" 
                                   required minlength="2" placeholder="Tu nombre">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= htmlspecialchars($usuario->email) ?>" 
                                   required placeholder="tu@email.com">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-2"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cambiar contraseña -->
            <div class="config-section">
                <div class="config-section-header">
                    <div class="config-section-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <h6 class="config-section-title">Cambiar contraseña</h6>
                        <p class="config-section-desc">Actualiza tu contraseña de acceso</p>
                    </div>
                </div>
                <div class="config-section-body">
                    <form method="POST" action="<?= url('perfil/password') ?>" id="formPassword">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="current_password" 
                                       required placeholder="••••••••" id="currentPass">
                                <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="currentPass">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password" 
                                       required minlength="8" placeholder="Mínimo 8 caracteres" id="newPass">
                                <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="newPass">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="passStrength"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="confirm_password" 
                                       required minlength="8" placeholder="Repite la contraseña" id="confirmPass">
                                <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="confirmPass">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-danger d-none" id="passMatchError">Las contraseñas no coinciden</small>
                        </div>
                        <button type="submit" class="btn btn-danger w-100" id="btnChangePass">
                            <i class="bi bi-key me-2"></i>Cambiar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- COLUMNA DERECHA: Historial de actividad -->
        <!-- ============================================= -->
        <div class="col-lg-7">
            <div class="config-section">
                <div class="config-section-header">
                    <div class="config-section-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h6 class="config-section-title">Historial de actividad</h6>
                        <p class="config-section-desc">Tus últimas <?= $actividad['total'] ?> acciones en el sistema</p>
                    </div>
                </div>
                <div class="config-section-body p-0">
                    <?php if (empty($actividad['data'])): ?>
                        <div class="empty-state py-5">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 100 100">
                                    <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                    <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                                </svg>
                                <i class="bi bi-clipboard-x text-muted"></i>
                            </div>
                            <h6>Sin actividad reciente</h6>
                            <p class="text-muted mb-0">No hay registros de actividad para mostrar de este usuario.</p>
                        </div>
                    <?php else: ?>
                        <div class="activity-timeline">
                            <?php foreach ($actividad['data'] as $log): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?= match(true) {
                                        str_contains($log->accion, 'login') => 'activity-login',
                                        str_contains($log->accion, 'crear') => 'activity-create',
                                        str_contains($log->accion, 'editar') || str_contains($log->accion, 'actualizar') => 'activity-edit',
                                        str_contains($log->accion, 'eliminar') => 'activity-delete',
                                        str_contains($log->accion, 'password') => 'activity-security',
                                        default => 'activity-default'
                                    } ?>">
                                        <i class="bi <?= match(true) {
                                            str_contains($log->accion, 'login') => 'bi-box-arrow-in-right',
                                            str_contains($log->accion, 'crear') => 'bi-plus-circle',
                                            str_contains($log->accion, 'editar') || str_contains($log->accion, 'actualizar') => 'bi-pencil',
                                            str_contains($log->accion, 'eliminar') => 'bi-trash',
                                            str_contains($log->accion, 'password') => 'bi-key',
                                            default => 'bi-activity'
                                        } ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-action">
                                            <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $log->accion))) ?></strong>
                                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2"><?= ucfirst($log->modulo) ?></span>
                                        </div>
                                        <?php if (!empty($log->detalles)): ?>
                                            <p class="activity-details"><?= htmlspecialchars(mb_strimwidth($log->detalles, 0, 120, '...')) ?></p>
                                        <?php endif; ?>
                                        <small class="activity-time">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($log->created_at)) ?>
                                            <?php if (!empty($log->ip)): ?>
                                                · <i class="bi bi-globe me-1"></i><?= $log->ip ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($actividad['pages'] > 1): ?>
                        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                            <small class="text-muted">Mostrando <?= count($actividad['data']) ?> de <?= $actividad['total'] ?></small>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <?php for ($i = 1; $i <= min($actividad['pages'], 5); $i++): ?>
                                    <li class="page-item <?= $i == $actividad['current'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= url("perfil?page={$i}") ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Toggle password visibility (kept as-is, UI utility)
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // === Validación: Datos personales ===
    FormValidator.init('#formDatos', {
        nombre: { required: true, minlength: 2, messages: { required: 'El nombre es obligatorio' } },
        email:  { required: true, email: true, messages: { required: 'El correo es obligatorio' } }
    });

    // === Validación: Cambio de contraseña ===
    FormValidator.init('#formPassword', {
        current_password: { required: true, messages: { required: 'Ingrese su contraseña actual' } },
        new_password:     { required: true, minlength: 8, messages: { required: 'Ingrese la nueva contraseña' } },
        confirm_password: {
            required: true,
            match: '[name="new_password"]',
            messages: { required: 'Confirme la nueva contraseña', match: 'Las contraseñas no coinciden' }
        }
    });

    // === Indicador de fuerza ===
    FormValidator.passwordStrength('#newPass', '#passStrength');
});
</script>
