<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Editar Departamento</h4>
        <span class="text-muted">Actualizar datos de: <?= htmlspecialchars($departamento->nombre) ?></span>
    </div>
    <a href="<?= url('departamentos') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('departamentos/update/' . $departamento->id) ?>">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold text-muted small">Nombre del Departamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= htmlspecialchars($departamento->nombre) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="responsable" class="form-label fw-semibold text-muted small">Nombre del Responsable</label>
                        <input type="text" class="form-control" id="responsable" name="responsable" value="<?= htmlspecialchars($departamento->responsable) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="centro_costo" class="form-label fw-semibold text-muted small">Centro de Costo</label>
                            <input type="text" class="form-control" id="centro_costo" name="centro_costo" value="<?= htmlspecialchars($departamento->centro_costo) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-semibold text-muted small">Teléfono / Extensión</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($departamento->telefono) ?>">
                        </div>
                    </div>

                    <div class="mb-3 border rounded p-3 bg-light">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" <?= $departamento->activo ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="activo">Departamento Activo</label>
                        </div>
                        <small class="text-muted d-block mt-1">Si está inactivo, no podrá ser seleccionado para nuevas requisiciones.</small>
                    </div>

                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('departamentos') ?>" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
