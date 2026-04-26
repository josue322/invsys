<!-- Dashboard KPI Cards -->
<div class="row g-2 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
            <div class="kpi-value"><?= number_format($totalProductos) ?></div>
            <div class="kpi-label">Productos Activos</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="kpi-value"><?= formatMoney($valorInventario) ?></div>
            <div class="kpi-label">Valor del Inventario</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card kpi-warning">
            <div class="kpi-icon"><i class="bi bi-bell"></i></div>
            <div class="kpi-value"><?= number_format($alertasActivas) ?></div>
            <div class="kpi-label">Alertas Activas</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card kpi-danger">
            <div class="kpi-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="kpi-value"><?= number_format($movimientosHoy) ?></div>
            <div class="kpi-label">Movimientos Hoy</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-2 mb-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Movimientos - Últimos 7 días</h6>
            </div>
            <div class="card-body">
                <canvas id="chartMovimientos" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Productos por Categoría</h6>
            </div>
            <div class="card-body">
                <canvas id="chartCategorias" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="row g-2">
    <!-- Productos Stock Bajo -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-0 py-3 px-4">
                <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Productos con Stock Bajo</h6>
                <span class="badge bg-warning text-dark"><?= count($productosStockBajo) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($productosStockBajo)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                            <svg viewBox="0 0 100 100">
                                <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                            </svg>
                            <i class="bi bi-check-circle" style="font-size:1.6rem;"></i>
                        </div>
                        <h6>Todo en orden</h6>
                        <p class="text-muted mb-0">No hay productos con stock bajo</p>
                    </div>
                <?php else: ?>
                <div class="table-wrapper">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($productosStockBajo, 0, 5) as $p): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($p->nombre) ?></strong>
                                    <br><small class="text-muted"><?= $p->sku ?></small>
                                </td>
                                <td class="tabular-nums"><strong><?= $p->stock ?></strong></td>
                                <td class="tabular-nums"><?= $p->stock_minimo ?></td>
                                <td>
                                    <?php if ($p->stock <= 0): ?>
                                        <span class="badge badge-stock-out">Agotado</span>
                                    <?php else: ?>
                                        <span class="badge badge-stock-low">Bajo</span>
                                    <?php endif; ?>
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

    <!-- Top Productos -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-bottom-0 py-3 px-4">
                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Productos Más Movidos</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topProductos)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                            <svg viewBox="0 0 100 100">
                                <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                            </svg>
                            <i class="bi bi-inbox" style="font-size:1.6rem;"></i>
                        </div>
                        <h6>Sin datos</h6>
                        <p class="text-muted mb-0">Aún no hay movimientos registrados</p>
                    </div>
                <?php else: ?>
                <div class="table-wrapper">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Entradas</th>
                                <th>Salidas</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProductos as $i => $tp): ?>
                            <tr>
                                <td><span class="badge bg-primary rounded-pill"><?= $i + 1 ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($tp->nombre) ?></strong>
                                    <br><small class="text-muted"><?= $tp->sku ?></small>
                                </td>
                                <td class="tabular-nums"><span class="text-success fw-bold">+<?= $tp->total_entradas ?></span></td>
                                <td class="tabular-nums"><span class="text-danger fw-bold">-<?= $tp->total_salidas ?></span></td>
                                <td class="tabular-nums"><strong><?= $tp->total_movimientos ?></strong></td>
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

<!-- Productos Próximos a Vencer -->
<?php if (!empty($productosProximosVencer)): ?>
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-0 py-3 px-4">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-danger"></i>Productos Próximos a Vencer</h6>
                <span class="badge bg-danger"><?= count($productosProximosVencer) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-wrapper">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Fecha y Riesgo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($productosProximosVencer, 0, 8) as $pv): 
                                $diasRestantes = (int) floor((strtotime($pv->fecha_vencimiento) - strtotime('today')) / 86400);
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($pv->nombre) ?></strong>
                                    <br><small class="text-muted"><?= $pv->sku ?> <span class="badge border border-secondary border-opacity-25 text-secondary ms-1 shadow-sm"><i class="bi bi-box-seam me-1"></i><?= $pv->numero_lote ?></span></small>
                                </td>
                                <td><small><?= htmlspecialchars($pv->categoria_nombre ?? '-') ?></small></td>
                                <td><strong><?= $pv->lote_stock ?></strong></td>
                                <td style="min-width:180px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-bold tabular-nums"><i class="bi bi-calendar-event me-1"></i><?= formatDate($pv->fecha_vencimiento, false) ?></small>
                                        <small class="fw-bold" style="font-size:0.7rem; color: <?= $diasRestantes < 0 ? '#ef4444' : ($diasRestantes <= 15 ? '#f59e0b' : 'currentColor') ?>"><?= $diasRestantes < 0 ? 'VENCIDO' : "$diasRestantes DÍAS" ?></small>
                                    </div>
                                    <?php 
                                        $maxDays = 60; 
                                        if ($diasRestantes < 0) {
                                            $percent = 100;
                                            $colorClass = 'bg-danger';
                                        } else {
                                            $percent = max(10, min(100, (($maxDays - $diasRestantes) / $maxDays) * 100));
                                            $colorClass = $diasRestantes <= 15 ? 'bg-warning' : 'bg-primary opacity-75';
                                        }
                                    ?>
                                    <div class="progress burn-down-progress">
                                        <div class="progress-bar <?= $colorClass ?> progress-bar-striped <?= $diasRestantes < 0 ? 'progress-bar-animated' : '' ?>" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js Data Bridge + External Script -->
<script id="page-data" type="application/json"><?= json_encode([
    'movimientos' => $movimientosSemana,
    'categorias'  => $productosPorCategoria,
]) ?></script>
<script src="<?= asset('js/dashboard.js') ?>?v=<?= ASSET_VERSION ?>"></script>


