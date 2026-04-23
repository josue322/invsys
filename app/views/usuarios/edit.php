<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Editar Usuario</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url("usuarios/editar/{$usuario->id}") ?>" id="formEditarUsuario">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   value="<?= htmlspecialchars($usuario->nombre) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?= htmlspecialchars($usuario->email) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="rol_id" class="form-label">Rol *</label>
                            <select class="form-select" id="rol_id" name="rol_id" required>
                                <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol->id ?>" <?= $usuario->rol_id == $rol->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol->nombre) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="1" <?= $usuario->activo ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= !$usuario->activo ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <button type="button" class="btn btn-outline-warning d-block w-100" data-bs-toggle="modal" data-bs-target="#modalResetPass">
                                <i class="bi bi-key me-1"></i>Restablecer contraseña
                            </button>
                            <small class="text-muted">El usuario podrá cambiarla desde su perfil</small>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Restablecer Contraseña -->
<div class="modal fade" id="modalResetPass" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-key me-2"></i>Restablecer Contraseña</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url("usuarios/editar/{$usuario->id}") ?>" id="formResetPass">
                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="reset_password" value="1">
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <strong>¿Estás seguro?</strong><br>
                            Se generará una nueva contraseña temporal para <strong><?= htmlspecialchars($usuario->nombre) ?></strong>.
                            El usuario deberá cambiarla desde su perfil.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña temporal</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="new_password" id="tempPass" 
                                   value="Temp<?= rand(1000, 9999) ?>!" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="copyTempPass()">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <small class="text-muted">Comparte esta contraseña temporal con el usuario de forma segura</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-key me-1"></i>Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyTempPass() {
    const input = document.getElementById('tempPass');
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = input.nextElementSibling;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
        setTimeout(() => { btn.innerHTML = originalHTML; }, 2000);
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    FormValidator.init('#formEditarUsuario', {
        nombre: { required: true, messages: { required: 'El nombre es obligatorio' } },
        email:  { required: true, email: true, messages: { required: 'El correo es obligatorio' } },
        rol_id: { required: true, messages: { required: 'Seleccione un rol' } }
    });
});
</script>
