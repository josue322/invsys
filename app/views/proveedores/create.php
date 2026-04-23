<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-800 mb-0">Registrar Nuevo Proveedor</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('proveedores/store') ?>" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre o Razón Social <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">RUC / DNI</label>
                            <input type="text" name="ruc_dni" class="form-control">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Contacto</label>
                            <input type="text" name="contacto" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('proveedores') ?>" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Registrar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
