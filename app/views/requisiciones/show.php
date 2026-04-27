<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">
            Requisición: <?= htmlspecialchars($requisicion->numero_requisicion) ?>
        </h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <?php
                $badge = match($requisicion->estado) {
                    'borrador' => 'bg-secondary',
                    'pendiente' => 'bg-warning text-dark',
                    'despachada' => 'bg-success',
                    'cancelada' => 'bg-danger',
                    default => 'bg-secondary',
                };
            ?>
            <span class="badge <?= $badge ?> text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <?= $requisicion->estado ?>
            </span>
            <span class="text-muted small">
                <i class="bi bi-clock me-1"></i>Solicitada: <?= formatDate($requisicion->fecha_solicitud) ?>
            </span>
            <?php if ($requisicion->fecha_despacho): ?>
                <span class="text-muted small ms-2">
                    <i class="bi bi-check-circle me-1"></i>Despachada: <?= date('d/m/Y H:i', strtotime($requisicion->fecha_despacho)) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <a href="<?= url('requisiciones') ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Panel de Información -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-building text-primary me-2"></i>Datos del Solicitante</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal small">Departamento</dt>
                    <dd class="col-sm-7 fw-semibold"><?= htmlspecialchars($requisicion->departamento_nombre) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal small">Centro Costo</dt>
                    <dd class="col-sm-7"><span class="badge bg-light text-dark border"><?= htmlspecialchars($requisicion->centro_costo ?: 'N/A') ?></span></dd>

                    <dt class="col-sm-5 text-muted fw-normal small">Solicitado por</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($requisicion->usuario_nombre) ?></dd>
                </dl>
                <?php if ($requisicion->notas): ?>
                    <hr class="my-3">
                    <p class="small text-muted mb-1 fw-semibold">Notas:</p>
                    <p class="small mb-0 p-2 bg-light rounded border border-light-subtle"><?= nl2br(htmlspecialchars($requisicion->notas)) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($requisicion->estado === 'pendiente' && hasPermission('requisiciones.despachar')): ?>
            <div class="card border-danger shadow-sm border-opacity-25">
                <div class="card-body p-3">
                    <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Zona de Peligro</h6>
                    <p class="small text-muted mb-3">Si esta requisición no procede, puede cancelarla. Esta acción no se puede deshacer.</p>
                    <form method="POST" action="<?= url('requisiciones/cancelar/' . $requisicion->id) ?>" onsubmit="return confirm('¿Está seguro de cancelar esta requisición?');">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-x-circle me-1"></i>Cancelar Requisición
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Panel de Productos y Despacho -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Detalle de Productos</h6>
            </div>
            
            <form method="POST" action="<?= url('requisiciones/despachar/' . $requisicion->id) ?>" id="form-despacho">
                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Solicitado</th>
                                    <?php if ($requisicion->estado === 'despachada'): ?>
                                        <th class="text-center">Despachado</th>
                                    <?php endif; ?>
                                    
                                    <?php if ($requisicion->estado === 'pendiente'): ?>
                                        <th>Cant. a Despachar</th>
                                        <th>Lote (si aplica)</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requisicion->detalles as $det): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($det->producto_nombre) ?></div>
                                            <div class="text-muted small">SKU: <?= htmlspecialchars($det->sku) ?></div>
                                            <?php if ($det->es_perecedero): ?>
                                                <span class="badge bg-warning text-dark" style="font-size:0.6rem">WMS Lotes</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="text-center fw-medium">
                                            <?= $det->cantidad_solicitada ?> <span class="small text-muted"><?= htmlspecialchars($det->unidad_medida) ?></span>
                                        </td>
                                        
                                        <?php if ($requisicion->estado === 'despachada'): ?>
                                            <td class="text-center fw-bold text-success">
                                                <?= $det->cantidad_despachada ?> <span class="small text-muted"><?= htmlspecialchars($det->unidad_medida) ?></span>
                                            </td>
                                        <?php endif; ?>

                                        <?php if ($requisicion->estado === 'pendiente'): ?>
                                            <td>
                                                <input type="number" name="despachar[<?= $det->id ?>]" class="form-control form-control-sm" value="<?= $det->cantidad_solicitada ?>" min="0" required>
                                            </td>
                                            <td>
                                                <?php if ($det->es_perecedero): ?>
                                                    <?php $lotes = $lotesDisponibles[$det->producto_id] ?? []; ?>
                                                    <select name="lotes[<?= $det->id ?>]" class="form-select form-select-sm border-warning" required>
                                                        <option value="">— Seleccionar Lote —</option>
                                                        <?php foreach ($lotes as $lote): ?>
                                                            <option value="<?= $lote->id ?>">
                                                                Lote: <?= htmlspecialchars($lote->numero_lote) ?> (Disp: <?= $lote->stock_actual ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if ($requisicion->estado === 'pendiente' && hasPermission('requisiciones.despachar')): ?>
                    <div class="card-footer bg-transparent border-top-0 py-3 text-end">
                        <button type="submit" class="btn btn-success px-4 shadow-sm" onclick="return confirm('¿Confirmar despacho? Esto restará el inventario del almacén.');">
                            <i class="bi bi-box-arrow-right me-1"></i>Confirmar Despacho
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
