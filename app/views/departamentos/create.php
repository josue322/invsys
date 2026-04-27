<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Nuevo Departamento</h4>
        <span class="text-muted">Crear una nueva área o departamento interno</span>
    </div>
    <a href="<?= url('departamentos') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('departamentos/store') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold text-muted small">Nombre del Departamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Mantenimiento, Recursos Humanos...">
                    </div>

                    <div class="mb-3">
                        <label for="responsable" class="form-label fw-semibold text-muted small">Nombre del Responsable</label>
                        <input type="text" class="form-control" id="responsable" name="responsable" placeholder="Ej: Juan Pérez">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="centro_costo" class="form-label fw-semibold text-muted small">Centro de Costo</label>
                            <input type="text" class="form-control" id="centro_costo" name="centro_costo" placeholder="Ej: CC-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-semibold text-muted small">Teléfono / Extensión</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: Ext. 405">
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('departamentos') ?>" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Departamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
