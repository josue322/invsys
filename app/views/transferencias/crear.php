<div class="content-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h3 mb-0 text-gray-800"><i class="bi bi-box-arrow-right me-2 text-primary"></i><?= htmlspecialchars($title) ?></h2>
            <p class="text-muted mb-0">Mover todo el stock de un producto a una nueva ubicación</p>
        </div>
        <a href="<?= url('transferencias') ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al historial
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="<?= url('transferencias/store') ?>" method="POST" id="formTransferencia" data-confirm='{"title":"¿Confirmar Transferencia?","message":"Se moverá todo el inventario del producto a la nueva ubicación seleccionada. ¿Deseas continuar?","type":"warning"}'>
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <!-- Alerta Informativa -->
                    <div class="alert border-0 bg-light rounded d-flex align-items-center p-3 mb-4">
                        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                        <div>
                            <p class="mb-0 text-dark"><strong>Nota Operativa:</strong> Al realizar una transferencia, estás moviendo <span class="badge bg-warning text-dark">todo el stock físico</span> del producto seleccionado hacia su nuevo destino.</p>
                        </div>
                    </div>

                    <!-- Selección de Producto -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">1. Seleccionar Producto</label>
                        <select name="producto_id" id="producto_id" class="form-select form-select-lg" required>
                            <option value="">Buscar o seleccionar producto...</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p->id ?>" 
                                        data-stock="<?= $p->stock ?>" 
                                        data-ubicacion-id="<?= $p->ubicacion_id ?>"
                                        data-ubicacion-nombre="<?= htmlspecialchars($p->ubicacion_nombre ?? 'Sin Ubicación Asignada') ?>">
                                    <?= htmlspecialchars($p->sku . ' - ' . $p->nombre) ?> 
                                    (Stock: <?= number_format($p->stock) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor selecciona un producto válido.</div>
                    </div>

                    <!-- Detalles del Producto Seleccionado (Solo lectura) -->
                    <div id="detalleProducto" class="mb-4 p-3 bg-light rounded border border-light d-none">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <span class="d-block text-muted small text-uppercase mb-1">Stock Actual a Mover</span>
                                <span class="fs-4 fw-bold text-primary" id="lblStock">-</span>
                            </div>
                            <div class="col-6">
                                <span class="d-block text-muted small text-uppercase mb-1">Ubicación Origen</span>
                                <span class="fs-5 fw-medium text-dark" id="lblUbicacionOrigen">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nueva Ubicación -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">2. Seleccionar Nueva Ubicación (Destino)</label>
                        <select name="ubicacion_destino_id" id="ubicacion_destino_id" class="form-select" required disabled>
                            <option value="">Seleccione el destino...</option>
                            <?php foreach ($ubicaciones as $u): ?>
                                <option value="<?= $u->id ?>">
                                    <?= htmlspecialchars($u->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione una ubicación de destino.</div>
                        <div id="errorUbicacionMisma" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-triangle me-1"></i> El producto ya se encuentra en esta ubicación. Seleccione un destino diferente.
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Observaciones (Opcional)</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Motivo de la transferencia o notas adicionales..."></textarea>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('transferencias') ?>" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5" id="btnSubmitTransferencia" disabled>
                            <i class="bi bi-box-arrow-in-right me-1"></i> Ejecutar Transferencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


