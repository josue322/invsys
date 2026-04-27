<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Inventario Muerto</h5>
        <small class="text-muted">Productos con stock pero sin movimiento — capital retenido</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
    </div>
</div>

<!-- Filtro de período -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/analisis/muertos') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="dias" class="form-label fw-semibold">
                    <i class="bi bi-calendar-range me-1"></i>Sin movimiento en
                </label>
                <select class="form-select" name="dias" id="dias">
                    <option value="30" <?= $dias == 30 ? 'selected' : '' ?>>30 días</option>
                    <option value="60" <?= $dias == 60 ? 'selected' : '' ?>>60 días</option>
                    <option value="90" <?= $dias == 90 ? 'selected' : '' ?>>90 días</option>
                    <option value="180" <?= $dias == 180 ? 'selected' : '' ?>>6 meses</option>
                    <option value="365" <?= $dias == 365 ? 'selected' : '' ?>>1 año</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Analizar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-start border-4 border-danger">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Productos Sin Movimiento</small>
                    <span class="fs-2 fw-bold text-danger"><?= count($items) ?></span>
                </div>
                <i class="bi bi-exclamation-diamond text-danger" style="font-size:2.5rem;opacity:0.3"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-start border-4 border-warning">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Capital Retenido</small>
                    <span class="fs-2 fw-bold text-warning"><?= formatMoney($valorRetenido) ?></span>
                </div>
                <i class="bi bi-piggy-bank text-warning" style="font-size:2.5rem;opacity:0.3"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-moon-stars me-2"></i>Productos sin movimiento (<?= $dias ?> días)</h6>
        <span class="badge bg-danger"><?= count($items) ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($items)): ?>
            <div class="empty-state py-5">
                <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-check-circle" style="font-size:1.6rem;"></i>
                </div>
                <h6>¡Excelente!</h6>
                <p class="text-muted mb-0" style="font-size:0.8rem">
                    Todos los productos con stock han tenido movimiento en los últimos <?= $dias ?> días
                </p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Valor Retenido</th>
                            <th class="text-center">Último Mov.</th>
                            <th class="text-center">Días sin Mov.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><code class="text-primary"><?= $item->sku ?></code></td>
                            <td><strong><?= htmlspecialchars($item->nombre) ?></strong></td>
                            <td><small class="text-muted"><?= htmlspecialchars($item->categoria_nombre ?? '—') ?></small></td>
                            <td class="text-end"><?= $item->stock ?></td>
                            <td class="text-end fw-semibold text-warning"><?= formatMoney($item->valor_retenido) ?></td>
                            <td class="text-center">
                                <small><?= $item->ultimo_movimiento ? formatDate($item->ultimo_movimiento, false) : 'Nunca' ?></small>
                            </td>
                            <td class="text-center">
                                <?php
                                    $diasSin = (int) ($item->dias_sin_movimiento ?? 999);
                                    $alertClass = $diasSin >= 180 ? 'text-danger fw-bold' : ($diasSin >= 90 ? 'text-warning fw-bold' : '');
                                ?>
                                <span class="<?= $alertClass ?>"><?= $diasSin >= 999 ? '+999' : $diasSin ?></span>
                                <small class="text-muted"> días</small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
