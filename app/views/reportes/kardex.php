<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Kardex de Inventario</h5>
        <small class="text-muted">Historial de movimientos con saldo corrido por producto</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/kardex') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="producto_id" class="form-label fw-semibold">
                    <i class="bi bi-box-seam me-1"></i>Producto *
                </label>
                <select class="form-select" name="producto_id" id="producto_id" required>
                    <option value="">— Seleccionar producto —</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p->id ?>" <?= $filtros['producto_id'] == $p->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p->sku . ' — ' . $p->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="desde" class="form-label fw-semibold">
                    <i class="bi bi-calendar-minus me-1"></i>Desde
                </label>
                <input type="date" class="form-control" name="desde" id="desde" 
                       value="<?= htmlspecialchars($filtros['desde'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <label for="hasta" class="form-label fw-semibold">
                    <i class="bi bi-calendar-plus me-1"></i>Hasta
                </label>
                <input type="date" class="form-control" name="hasta" id="hasta" 
                       value="<?= htmlspecialchars($filtros['hasta'] ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="valorizado" 
                           name="valorizado" value="1" <?= $filtros['valorizado'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="valorizado">
                        <i class="bi bi-currency-dollar me-1"></i>Valorizado
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Consultar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($productoSeleccionado && $kardexData): ?>

<!-- Resumen del producto -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <small class="text-muted d-block">Producto</small>
                <strong class="fs-6"><?= htmlspecialchars($productoSeleccionado->nombre) ?></strong>
                <div><code class="text-primary"><?= $productoSeleccionado->sku ?></code></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <small class="text-muted d-block">Saldo Inicial</small>
                <span class="fs-3 fw-bold text-info"><?= $kardexData['saldo_inicial'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <small class="text-muted d-block">Entradas / Salidas</small>
                <span class="fs-5 fw-bold text-success">+<?= $kardexData['total_entradas'] ?></span>
                <span class="mx-1 text-muted">/</span>
                <span class="fs-5 fw-bold text-danger">-<?= $kardexData['total_salidas'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <small class="text-muted d-block">Saldo Final</small>
                <span class="fs-3 fw-bold <?= $kardexData['saldo_final'] <= 0 ? 'text-danger' : 'text-primary' ?>">
                    <?= $kardexData['saldo_final'] ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Acciones de exportación -->
<div class="d-flex justify-content-end gap-2 mb-3">
    <?php
        $exportParams = http_build_query([
            'producto_id' => $filtros['producto_id'],
            'desde' => $filtros['desde'],
            'hasta' => $filtros['hasta'],
            'valorizado' => $filtros['valorizado'] ? '1' : '0',
        ]);
    ?>
    <a href="<?= url("reportes/kardex/exportar/csv?{$exportParams}") ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
    </a>
</div>

<!-- Tabla Kardex -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-journal-text me-2"></i>Kardex — <?= htmlspecialchars($productoSeleccionado->nombre) ?>
        </h6>
        <span class="badge bg-primary"><?= count($kardexData['movimientos']) ?> movimientos</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($kardexData['movimientos'])): ?>
            <div class="empty-state py-5">
                <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-journal-x" style="font-size:1.6rem;"></i>
                </div>
                <h6>Sin movimientos</h6>
                <p class="text-muted mb-0" style="font-size:0.8rem">No hay movimientos registrados para el período seleccionado</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Referencia</th>
                            <th class="text-end">Entrada</th>
                            <th class="text-end">Salida</th>
                            <th class="text-end fw-bold">Saldo</th>
                            <?php if ($filtros['valorizado']): ?>
                                <th class="text-end">V. Entrada</th>
                                <th class="text-end">V. Salida</th>
                                <th class="text-end fw-bold">V. Saldo</th>
                            <?php endif; ?>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fila saldo inicial -->
                        <tr class="bg-secondary bg-opacity-10">
                            <td colspan="5" class="text-end fst-italic">Saldo Inicial →</td>
                            <td class="text-end fw-bold"><?= $kardexData['saldo_inicial'] ?></td>
                            <?php if ($filtros['valorizado']): ?>
                                <td colspan="2"></td>
                                <td class="text-end fw-bold">
                                    <?= formatMoney($kardexData['saldo_inicial'] * ($kardexData['precio_unitario'] ?? 0)) ?>
                                </td>
                            <?php endif; ?>
                            <td></td>
                        </tr>

                        <?php foreach ($kardexData['movimientos'] as $m): ?>
                        <tr>
                            <td><small><?= formatDate($m->fecha) ?></small></td>
                            <td>
                                <?php
                                    $badgeClass = match($m->tipo) {
                                        'entrada' => 'bg-success',
                                        'salida' => 'bg-danger',
                                        'ajuste' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>" style="font-size:0.7rem"><?= ucfirst($m->tipo) ?></span>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($m->referencia) ?></small></td>
                            <td class="text-end <?= $m->entrada > 0 ? 'text-success fw-semibold' : '' ?>">
                                <?= $m->entrada > 0 ? '+' . $m->entrada : '' ?>
                            </td>
                            <td class="text-end <?= $m->salida > 0 ? 'text-danger fw-semibold' : '' ?>">
                                <?= $m->salida > 0 ? '-' . $m->salida : '' ?>
                            </td>
                            <td class="text-end fw-bold"><?= $m->saldo ?></td>
                            <?php if ($filtros['valorizado']): ?>
                                <td class="text-end text-success"><?= $m->valor_entrada > 0 ? formatMoney($m->valor_entrada) : '' ?></td>
                                <td class="text-end text-danger"><?= $m->valor_salida > 0 ? formatMoney($m->valor_salida) : '' ?></td>
                                <td class="text-end fw-bold"><?= formatMoney($m->valor_saldo) ?></td>
                            <?php endif; ?>
                            <td><small><?= htmlspecialchars($m->usuario) ?></small></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Fila saldo final -->
                        <tr class="bg-primary bg-opacity-10">
                            <td colspan="3" class="text-end fw-bold">TOTALES</td>
                            <td class="text-end text-success fw-bold">+<?= $kardexData['total_entradas'] ?></td>
                            <td class="text-end text-danger fw-bold">-<?= $kardexData['total_salidas'] ?></td>
                            <td class="text-end fw-bold fs-6"><?= $kardexData['saldo_final'] ?></td>
                            <?php if ($filtros['valorizado']): ?>
                                <td class="text-end text-success fw-bold"><?= formatMoney($kardexData['valor_total_entradas'] ?? 0) ?></td>
                                <td class="text-end text-danger fw-bold"><?= formatMoney($kardexData['valor_total_salidas'] ?? 0) ?></td>
                                <td class="text-end fw-bold fs-6"><?= formatMoney($kardexData['saldo_final'] * ($kardexData['precio_unitario'] ?? 0)) ?></td>
                            <?php endif; ?>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php elseif (!$productoSeleccionado && $filtros['producto_id'] > 0): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>Producto no encontrado.
    </div>
<?php else: ?>
    <!-- Estado inicial: sin producto seleccionado -->
    <div class="card">
        <div class="card-body py-5 text-center">
            <div class="empty-state">
                <div class="empty-state-icon" style="width:80px;height:80px;margin: 0 auto 1.5rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-journal-text" style="font-size:2rem;"></i>
                </div>
                <h5>Seleccione un Producto</h5>
                <p class="text-muted" style="max-width:400px;margin:0 auto;">
                    Elija un producto en el filtro de arriba para ver su Kardex con el historial
                    completo de entradas, salidas y saldo corrido.
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>
