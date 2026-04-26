<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('usuarios/crear') ?>" id="formCrearUsuario">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label for="rol_id" class="form-label">Rol *</label>
                            <select class="form-select" id="rol_id" name="rol_id" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol->id ?>"><?= htmlspecialchars($rol->nombre) ?> — <?= htmlspecialchars($rol->descripcion ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">Contraseña * (mínimo 8 caracteres)</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div id="passStrengthCreate"></div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



