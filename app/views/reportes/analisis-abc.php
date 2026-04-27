<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Análisis ABC (Pareto)</h5>
        <small class="text-muted">Clasificación de productos por valor de inventario — A: 80%, B: 15%, C: 5%</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('reportes/analisis/abc/csv') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
        </a>
        <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
    </div>
</div>

<!-- Resumen por Clase -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-start border-4 border-danger">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Clase A — Alto Valor</small>
                        <span class="fs-3 fw-bold text-danger"><?= $totals['count_A'] ?? 0 ?></span>
                        <small class="text-muted"> productos</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6">A</span>
                        <div class="mt-1"><small class="text-muted"><?= formatMoney($totals['A'] ?? 0) ?></small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-warning">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Clase B — Valor Medio</small>
                        <span class="fs-3 fw-bold text-warning"><?= $totals['count_B'] ?? 0 ?></span>
                        <small class="text-muted"> productos</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 fs-6">B</span>
                        <div class="mt-1"><small class="text-muted"><?= formatMoney($totals['B'] ?? 0) ?></small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-info">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Clase C — Bajo Valor</small>
                        <span class="fs-3 fw-bold text-info"><?= $totals['count_C'] ?? 0 ?></span>
                        <small class="text-muted"> productos</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 fs-6">C</span>
                        <div class="mt-1"><small class="text-muted"><?= formatMoney($totals['C'] ?? 0) ?></small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-primary">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Valor Total</small>
                        <span class="fs-4 fw-bold text-primary"><?= formatMoney($totals['total'] ?? 0) ?></span>
                    </div>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6">
                            <?= count($items) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico + Tabla -->
<div class="row g-4">
    <!-- Gráfico de distribución -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Distribución por Valor</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartABC" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2"></i>Detalle por Producto</h6>
                <span class="badge bg-primary"><?= count($items) ?> productos</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($items)): ?>
                    <div class="empty-state py-5">
                        <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                            <svg viewBox="0 0 100 100">
                                <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                            </svg>
                            <i class="bi bi-bar-chart" style="font-size:1.6rem;"></i>
                        </div>
                        <h6>Sin datos</h6>
                        <p class="text-muted mb-0" style="font-size:0.8rem">No hay productos con stock para analizar</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper" style="max-height:500px;overflow-y:auto;">
                        <table class="table table-sm mb-0">
                            <thead style="position:sticky;top:0;z-index:1;">
                                <tr>
                                    <th style="width:50px">Clase</th>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-end">% Total</th>
                                    <th class="text-end">% Acum.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?php
                                            $claseBg = match($item->clase) {
                                                'A' => 'bg-danger',
                                                'B' => 'bg-warning text-dark',
                                                'C' => 'bg-info',
                                                default => 'bg-secondary',
                                            };
                                        ?>
                                        <span class="badge <?= $claseBg ?> fw-bold"><?= $item->clase ?></span>
                                    </td>
                                    <td><code class="text-primary"><?= $item->sku ?></code></td>
                                    <td>
                                        <strong><?= htmlspecialchars($item->nombre) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($item->categoria) ?></small>
                                    </td>
                                    <td class="text-end"><?= $item->stock ?></td>
                                    <td class="text-end"><?= formatMoney($item->precio) ?></td>
                                    <td class="text-end fw-semibold"><?= formatMoney($item->valor) ?></td>
                                    <td class="text-end"><?= $item->porcentaje ?>%</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:50px;height:6px;">
                                                <div class="progress-bar <?= $claseBg ?>" style="width:<?= min($item->porcentaje_acumulado, 100) ?>%"></div>
                                            </div>
                                            <small><?= $item->porcentaje_acumulado ?>%</small>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script id="page-data" type="application/json"><?= json_encode([
    'totals' => $totals,
]) ?></script>
<script src="<?= asset('js/reportes.js') ?>?v=<?= ASSET_VERSION ?>"></script>
