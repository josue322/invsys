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
<div class="row g-2 mb-3 align-items-stretch">
    <div class="col-lg-8 d-flex">
        <div class="card h-100 w-100 d-flex flex-column">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Movimientos - Últimos 7 días</h6>
            </div>
            <div class="card-body d-flex flex-column flex-grow-1">
                <div style="position: relative; flex: 1; min-height: 280px;">
                    <canvas id="chartMovimientos"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 d-flex">
        <div class="card h-100 w-100 d-flex flex-column">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Productos por Categoría</h6>
            </div>
            <div class="card-body d-flex flex-column flex-grow-1 p-3">
                <div style="position: relative; flex: 1; min-height: 200px;">
                    <canvas id="chartCategorias"></canvas>
                </div>
                <div id="chartCategoriasLegend" class="mt-auto pt-3" style="border-top: 1px solid rgba(0,0,0,0.06);"></div>
            </div>
        </div>
    </div>
</div>

<!-- Insights Row -->
<div class="row g-2 mb-3 align-items-stretch">
    <!-- Value Trend Chart -->
    <div class="col-lg-8 d-flex">
        <div class="card h-100 w-100 d-flex flex-column">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Tendencia del Valor de Inventario</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">30 días</span>
            </div>
            <div class="card-body d-flex flex-column flex-grow-1">
                <div style="position: relative; flex: 1; min-height: 200px;">
                    <canvas id="chartValorTendencia"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Health + Rotation Stacked -->
    <div class="col-lg-4 d-flex flex-column gap-2">
        <!-- Health Widget -->
        <div class="card flex-fill">
            <div class="card-header py-2">
                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-heart-pulse me-2"></i>Salud del Inventario</h6>
            </div>
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fs-3 fw-bold text-success"><?= $stockHealth->total > 0 ? round(($stockHealth->saludable / $stockHealth->total) * 100) : 0 ?>%</div>
                        <small class="text-muted">Saludable</small>
                    </div>
                    <div class="d-flex gap-3 text-center">
                        <div>
                            <div class="fw-bold text-success"><?= (int) $stockHealth->saludable ?></div>
                            <small class="text-muted" style="font-size:0.7rem;">Normal</small>
                        </div>
                        <div>
                            <div class="fw-bold text-warning"><?= (int) $stockHealth->bajo ?></div>
                            <small class="text-muted" style="font-size:0.7rem;">Bajo</small>
                        </div>
                        <div>
                            <div class="fw-bold text-danger"><?= (int) $stockHealth->agotado ?></div>
                            <small class="text-muted" style="font-size:0.7rem;">Agotado</small>
                        </div>
                    </div>
                </div>
                <?php
                    $pctSano = $stockHealth->total > 0 ? ($stockHealth->saludable / $stockHealth->total) * 100 : 0;
                    $pctBajo = $stockHealth->total > 0 ? ($stockHealth->bajo / $stockHealth->total) * 100 : 0;
                    $pctAgotado = $stockHealth->total > 0 ? ($stockHealth->agotado / $stockHealth->total) * 100 : 0;
                ?>
                <div class="progress" style="height: 10px; border-radius: 8px;">
                    <div class="progress-bar bg-success" style="width: <?= $pctSano ?>%" title="Normal"></div>
                    <div class="progress-bar bg-warning" style="width: <?= $pctBajo ?>%" title="Bajo"></div>
                    <div class="progress-bar bg-danger" style="width: <?= $pctAgotado ?>%" title="Agotado"></div>
                </div>
            </div>
        </div>

        <!-- Rotation Widget -->
        <div class="card flex-fill">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-arrow-repeat me-2"></i>Rotación de Inventario</h6>
                <small class="text-muted" style="font-size:0.65rem;">90 días</small>
            </div>
            <div class="card-body py-3">
                <div class="row g-2 text-center">
                    <div class="col-3">
                        <div class="rounded-3 py-2" style="background: rgba(16,185,129,0.08);">
                            <div class="fw-bold text-success fs-5"><?= (int) $rotacionResumen->alta ?></div>
                            <small class="text-muted" style="font-size:0.65rem;">Alta</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="rounded-3 py-2" style="background: rgba(99,102,241,0.08);">
                            <div class="fw-bold text-primary fs-5"><?= (int) $rotacionResumen->media ?></div>
                            <small class="text-muted" style="font-size:0.65rem;">Media</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="rounded-3 py-2" style="background: rgba(245,158,11,0.08);">
                            <div class="fw-bold text-warning fs-5"><?= (int) $rotacionResumen->baja ?></div>
                            <small class="text-muted" style="font-size:0.65rem;">Baja</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="rounded-3 py-2" style="background: rgba(239,68,68,0.08);">
                            <div class="fw-bold text-danger fs-5"><?= (int) $rotacionResumen->sin_movimiento ?></div>
                            <small class="text-muted" style="font-size:0.65rem;">Nula</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Feed Row -->
<div class="row g-2 mb-3 align-items-stretch">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2 text-primary"></i>Actividad Reciente</h6>
                <a href="<?= url('seguridad') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-clock-history me-1"></i>Ver todo
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($actividadReciente)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                            <svg viewBox="0 0 100 100">
                                <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                            </svg>
                            <i class="bi bi-clock" style="font-size:1.6rem;"></i>
                        </div>
                        <h6>Sin actividad</h6>
                        <p class="text-muted mb-0">No se han registrado acciones aún</p>
                    </div>
                <?php else: ?>
                <div class="activity-feed px-4 py-3">
                    <?php
                    // Map actions to icons and colors
                    $actionMap = [
                        'crear'           => ['bi-plus-circle-fill', '#10b981', 'Creó'],
                        'editar'          => ['bi-pencil-fill', '#6366f1', 'Editó'],
                        'eliminar'        => ['bi-trash-fill', '#ef4444', 'Eliminó'],
                        'toggle'          => ['bi-toggle-on', '#f59e0b', 'Cambió estado'],
                        'login'           => ['bi-box-arrow-in-right', '#3b82f6', 'Inició sesión'],
                        'logout'          => ['bi-box-arrow-right', '#6b7280', 'Cerró sesión'],
                        'importar_csv'    => ['bi-file-earmark-arrow-up', '#8b5cf6', 'Importó CSV'],
                        'exportar_csv'    => ['bi-file-earmark-arrow-down', '#06b6d4', 'Exportó CSV'],
                        'entrada'         => ['bi-arrow-down-circle-fill', '#10b981', 'Entrada'],
                        'salida'          => ['bi-arrow-up-circle-fill', '#ef4444', 'Salida'],
                        'ajuste'          => ['bi-sliders', '#f59e0b', 'Ajuste'],
                        'crear_movimiento'=> ['bi-arrow-left-right', '#6366f1', 'Movimiento'],
                    ];
                    ?>
                    <?php foreach ($actividadReciente as $log):
                        // Find matching action
                        $icon = 'bi-circle-fill';
                        $color = '#6b7280';
                        $label = ucfirst($log->accion);
                        foreach ($actionMap as $key => $val) {
                            if (stripos($log->accion, $key) !== false) {
                                $icon = $val[0];
                                $color = $val[1];
                                $label = $val[2];
                                break;
                            }
                        }

                        // Format module name
                        $modulo = ucfirst(str_replace('_', ' ', $log->modulo));
                    ?>
                    <div class="activity-item d-flex align-items-start gap-3 py-2" style="border-bottom: 1px solid rgba(0,0,0,0.04);">
                        <div class="activity-icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" 
                             style="width:36px; height:36px; background: <?= $color ?>12; color: <?= $color ?>;">
                            <i class="bi <?= $icon ?>" style="font-size: 0.85rem;"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="min-width:0;">
                                    <strong style="font-size: 0.82rem;"><?= htmlspecialchars($log->usuario_nombre) ?></strong>
                                    <span class="text-muted" style="font-size: 0.82rem;">— <?= $label ?></span>
                                    <span class="badge bg-light text-secondary ms-1" style="font-size: 0.65rem; font-weight: 500;"><?= $modulo ?></span>
                                </div>
                                <small class="text-muted flex-shrink-0 ms-2 tabular-nums" style="font-size: 0.7rem;" title="<?= $log->created_at ?>" data-timestamp="<?= $log->created_at ?>">
                                    <?= date('H:i', strtotime($log->created_at)) ?>
                                </small>
                            </div>
                            <?php if (!empty($log->detalles)): ?>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.75rem; max-width: 600px;">
                                <?= htmlspecialchars(strip_tags($log->detalles)) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="row g-2 mb-3">
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
    'movimientos'      => $movimientosSemana,
    'categorias'       => $productosPorCategoria,
    'valorTendencia'   => $valorTendencia,
    'valorActual'      => $valorInventario,
]) ?></script>
<script src="<?= asset('js/dashboard.js') ?>?v=<?= ASSET_VERSION ?>"></script>


