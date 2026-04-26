<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Proveedor</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url("proveedores/editar/{$proveedor->id}") ?>" id="formEditarProveedor">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre o Razón Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="150"
                                   value="<?= htmlspecialchars($proveedor->nombre) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="ruc_dni" class="form-label">RUC / DNI</label>
                            <input type="text" class="form-control" id="ruc_dni" name="ruc_dni" maxlength="20"
                                   value="<?= htmlspecialchars($proveedor->ruc_dni ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="contacto" class="form-label">Nombre del Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" maxlength="100"
                                   value="<?= htmlspecialchars($proveedor->contacto ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="50"
                                   value="<?= htmlspecialchars($proveedor->telefono ?? '') ?>">
                        </div>

                        <div class="col-md-8">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="150"
                                   value="<?= htmlspecialchars($proveedor->email ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="1" <?= $proveedor->activo ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= !$proveedor->activo ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"><?= htmlspecialchars($proveedor->direccion ?? '') ?></textarea>
                        </div>

                        <!-- Info del proveedor -->
                        <div class="col-12">
                            <div class="card bg-body-secondary border-0">
                                <div class="card-body py-2 px-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">ID</small>
                                            <strong>#<?= $proveedor->id ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Fecha de Creación</small>
                                            <strong><?= formatDate($proveedor->created_at, false) ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Última Modificación</small>
                                            <strong><?= $proveedor->updated_at ? formatDate($proveedor->updated_at) : '—' ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('proveedores') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnActualizarProveedor">
                            <i class="bi bi-check-lg me-1"></i>Actualizar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



