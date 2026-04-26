<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Ubicación</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url("ubicaciones/editar/{$ubicacion->id}") ?>" id="formEditarUbicacion">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre de la Ubicación *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100"
                                   value="<?= htmlspecialchars($ubicacion->nombre) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="activa" class="form-label">Estado</label>
                            <select class="form-select" id="activa" name="activa">
                                <option value="1" <?= $ubicacion->activa ? 'selected' : '' ?>>Activa</option>
                                <option value="0" <?= !$ubicacion->activa ? 'selected' : '' ?>>Inactiva</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" maxlength="255"
                                   value="<?= htmlspecialchars($ubicacion->descripcion ?? '') ?>"
                                   placeholder="Opcional: Tipo de productos almacenados aquí">
                        </div>

                        <!-- Info de la ubicación -->
                        <div class="col-12">
                            <div class="card bg-body-secondary border-0">
                                <div class="card-body py-2 px-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">ID</small>
                                            <strong>#<?= $ubicacion->id ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Fecha de Creación</small>
                                            <strong><?= formatDate($ubicacion->created_at, false) ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Última Modificación</small>
                                            <strong><?= $ubicacion->updated_at ? formatDate($ubicacion->updated_at) : '—' ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('ubicaciones') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnActualizarUbicacion">
                            <i class="bi bi-check-lg me-1"></i>Actualizar Ubicación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



