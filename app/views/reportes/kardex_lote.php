<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 print-hide">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>Trazabilidad por Lote</h4>
        <span class="text-muted">Historial completo de movimientos de un lote específico</span>
    </div>
</div>

<div class="row g-4 print-hide">
    <!-- Panel de Filtros -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm sticky-top" style="top: 1rem; z-index: 10;">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">Configurar Trazabilidad</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= url('reportes/kardex-lote') ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">1. Producto Perecedero</label>
                        <select name="producto_id" class="form-select auto-submit-select" required>
                            <option value="">Seleccione un producto...</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p->id ?>" <?= $filtros['producto_id'] == $p->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p->nombre) ?> (<?= $p->sku ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($filtros['producto_id'] > 0): ?>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">2. Seleccionar Lote</label>
                            <select name="lote_id" class="form-select auto-submit-select" required>
                                <option value="">Seleccione un lote...</option>
                                <?php foreach ($lotes as $l): ?>
                                    <option value="<?= $l->id ?>" <?= $filtros['lote_id'] == $l->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($l->numero_lote) ?> 
                                        (Stock: <?= $l->stock_actual ?> - Vence: <?= formatDate($l->fecha_vencimiento) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel de Resultados -->
    <div class="col-lg-9">
        <?php if ($filtros['producto_id'] <= 0 || $filtros['lote_id'] <= 0): ?>
            <div class="card border-0 shadow-sm h-100 min-vh-50">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-5">
                    <div class="empty-state-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-search text-muted fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Seleccione un Lote</h5>
                    <p class="text-muted mb-0 max-w-md">Para visualizar el historial de movimientos (trazabilidad), primero debe seleccionar un producto perecedero y luego un lote específico.</p>
                </div>
            </div>
        <?php elseif ($loteSeleccionado): ?>
            <!-- Resumen del Lote -->
            <div class="card border-0 shadow-sm mb-4 print-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="fw-bold text-primary mb-1">LOTE: <?= htmlspecialchars($loteSeleccionado->numero_lote) ?></h5>
                            <div class="text-muted small">
                                <i class="bi bi-box-seam me-1"></i>Producto ID: <?= $loteSeleccionado->producto_id ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-md-end g-3">
                                <div class="col-6 col-md-4">
                                    <div class="text-muted small fw-semibold text-uppercase mb-1">Estado</div>
                                    <span class="badge <?= $loteSeleccionado->estado === 'activo' ? 'bg-success' : ($loteSeleccionado->estado === 'agotado' ? 'bg-secondary' : 'bg-danger') ?>">
                                        <?= strtoupper($loteSeleccionado->estado) ?>
                                    </span>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="text-muted small fw-semibold text-uppercase mb-1">Vencimiento</div>
                                    <div class="fw-bold <?= strtotime($loteSeleccionado->fecha_vencimiento) < time() ? 'text-danger' : 'text-dark' ?>">
                                        <?= formatDate($loteSeleccionado->fecha_vencimiento) ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small fw-semibold text-uppercase mb-1">Stock Restante</div>
                                    <div class="fw-bolder fs-5 text-primary"><?= $loteSeleccionado->stock_actual ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Trazabilidad -->
            <div class="card border-0 shadow-sm print-card">
                <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Movimientos del Lote</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($movimientosLote)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                            <p class="mb-0">Este lote no tiene movimientos registrados.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Fecha</th>
                                        <th>Tipo</th>
                                        <th>Referencia</th>
                                        <th>Usuario</th>
                                        <th class="text-end">Cant.</th>
                                        <th class="text-end pe-4">Stock Lote</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php 
                                    $runningStock = 0;
                                    foreach ($movimientosLote as $m): 
                                        if ($m->tipo === 'entrada') {
                                            $runningStock += $m->cantidad;
                                            $color = 'text-success';
                                            $icon = 'bi-arrow-down-left-circle';
                                        } else if ($m->tipo === 'salida') {
                                            $runningStock -= $m->cantidad;
                                            $color = 'text-danger';
                                            $icon = 'bi-arrow-up-right-circle';
                                        } else {
                                            $runningStock = $m->stock_nuevo; // Ajuste directo
                                            $color = 'text-warning';
                                            $icon = 'bi-arrow-left-right';
                                        }
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-medium text-dark"><?= date('d/m/Y', strtotime($m->created_at)) ?></div>
                                                <div class="text-muted" style="font-size:0.7rem"><?= date('H:i', strtotime($m->created_at)) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light <?= $color ?> border border-light-subtle rounded-pill">
                                                    <i class="bi <?= $icon ?> me-1"></i><?= ucfirst($m->tipo) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="d-block fw-medium text-dark"><?= htmlspecialchars($m->referencia ?: 'S/N') ?></span>
                                                <?php if ($m->tipo === 'entrada' && $m->proveedor_nombre): ?>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($m->proveedor_nombre) ?>">
                                                        Prov: <?= htmlspecialchars($m->proveedor_nombre) ?>
                                                    </small>
                                                <?php elseif ($m->tipo === 'salida' && $m->departamento_nombre): ?>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($m->departamento_nombre) ?>">
                                                        Depto: <?= htmlspecialchars($m->departamento_nombre) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><small class="text-muted"><?= htmlspecialchars($m->usuario_nombre) ?></small></td>
                                            <td class="text-end fw-bold <?= $color ?>">
                                                <?= $m->tipo === 'entrada' ? '+' : '-' ?><?= $m->cantidad ?>
                                            </td>
                                            <td class="text-end pe-4 fw-bolder text-primary">
                                                <?= $runningStock ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
