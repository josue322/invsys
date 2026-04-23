<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-800 mb-0">Registrar Ubicación</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('ubicaciones/crear') ?>" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Ubicación (ej. Pasillo A) <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Opcional: Tipo de productos almacenados aquí">
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('ubicaciones') ?>" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Ubicación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
