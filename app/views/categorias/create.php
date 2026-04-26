<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva Categoría</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('categorias/crear') ?>" id="formCrearCategoria">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100"
                                   placeholder="Ej: Electrónica, Oficina, Herramientas...">
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                      placeholder="Descripción opcional de qué tipo de productos agrupa esta categoría..."></textarea>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('categorias') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnGuardarCategoria">
                            <i class="bi bi-check-lg me-1"></i>Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



