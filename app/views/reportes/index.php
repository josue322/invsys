<?php
/**
 * @var array $kpis
 * @var array $productosStockBajo
 * @var array $topProductos
 * @var array $productosPorCategoria
 * @var array $categoriaAnalisis
 * @var array $tendenciaMensual
 * @var string $titulo
 */
?>
<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Reportes & Exportación</h5>
        <small class="text-muted">Visualiza y exporta datos de tu inventario</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Export Dropdown Excel -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnExportCSV" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnExportCSV">
                <li><h6 class="dropdown-header"><i class="bi bi-table me-1"></i>Exportar a Excel</h6></li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/inventario/csv') ?>">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Inventario General
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/stock-bajo/csv') ?>">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Stock Bajo / Agotado
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/top-productos/csv') ?>">
                        <i class="bi bi-trophy me-2 text-info"></i>Top Productos Movidos
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/categorias/csv') ?>">
                        <i class="bi bi-pie-chart me-2 text-success"></i>Distribución por Categoría
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/movimientos/csv') ?>">
                        <i class="bi bi-arrow-left-right me-2 text-secondary"></i>Movimientos (Todos)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Export Dropdown PDF -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="btnExportPDF" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnExportPDF">
                <li><h6 class="dropdown-header"><i class="bi bi-printer me-1"></i>Exportar a PDF</h6></li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/inventario/pdf') ?>" target="_blank">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Inventario General
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/stock-bajo/pdf') ?>" target="_blank">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Stock Bajo / Agotado
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url('reportes/exportar/movimientos/pdf') ?>" target="_blank">
                        <i class="bi bi-arrow-left-right me-2 text-secondary"></i>Movimientos (Todos)
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item fw-bold" href="<?= url('reportes/exportar/completo/pdf') ?>" target="_blank">
                        <i class="bi bi-file-earmark-richtext me-2 text-danger"></i>Reporte Completo
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- KPI Summary Cards Grid -->
<div class="row g-3 mb-4" id="kpi-grid">
    <?php
        $bajos = (int)($kpis['saludStock']->bajo ?? 0);
        $agotados = (int)($kpis['saludStock']->agotado ?? 0);
        $totalCritico = $bajos + $agotados;
    ?>
    <!-- Valor Total Inventario -->
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="background: var(--bg-card); border-left: 3px solid #22c55e !important; min-height: 110px;">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; font-family: 'Inter', sans-serif;">VALOR INVENTARIO</span>
                    <h4 class="mb-0 fw-800" style="font-family: 'Inter', sans-serif; font-size: 1.35rem; color: var(--text-primary);"><?= formatMoney($kpis['valorInventario']) ?></h4>
                    <small class="text-muted d-inline-flex align-items-center mt-1" style="font-size: 0.68rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-info-circle me-1"></i> Costo total de adquisición
                    </small>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, rgba(34,197,94,0.12) 0%, rgba(16,185,129,0.18) 100%);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Productos Catálogo -->
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="background: var(--bg-card); border-left: 3px solid #6366f1 !important; min-height: 110px;">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; font-family: 'Inter', sans-serif;">PRODUCTOS ACTIVOS</span>
                    <h4 class="mb-0 fw-800" style="font-family: 'Inter', sans-serif; font-size: 1.35rem; color: #6366f1;"><?= $kpis['totalProductos'] ?></h4>
                    <small class="text-muted d-inline-flex align-items-center mt-1" style="font-size: 0.68rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-info-circle me-1"></i> En catálogo activo
                    </small>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(139,92,246,0.15) 100%);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Crítico -->
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="background: var(--bg-card); border-left: 3px solid <?= $totalCritico > 0 ? '#ef4444' : '#22c55e' ?> !important; min-height: 110px;">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; font-family: 'Inter', sans-serif;">STOCK CRÍTICO</span>
                    <h4 class="mb-0 fw-800" style="font-family: 'Inter', sans-serif; font-size: 1.35rem; color: <?= $totalCritico > 0 ? '#ef4444' : '#22c55e' ?>;"><?= $totalCritico ?></h4>
                    <small class="<?= $totalCritico > 0 ? 'text-warning' : 'text-success' ?> fw-semibold d-inline-flex align-items-center mt-1" style="font-size: 0.68rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.35rem;"></i>
                        <?= $agotados ?> agotados · <?= $bajos ?> bajos
                    </small>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(245,158,11,0.12) 100%);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="<?= $totalCritico > 0 ? '#ef4444' : '#22c55e' ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos de Hoy -->
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="background: var(--bg-card); border-left: 3px solid #06b6d4 !important; min-height: 110px;">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; font-family: 'Inter', sans-serif;">MOVIMIENTOS HOY</span>
                    <h4 class="mb-0 fw-800" style="font-family: 'Inter', sans-serif; font-size: 1.35rem; color: #06b6d4;"><?= $kpis['movimientosHoy'] ?></h4>
                    <small class="text-muted d-inline-flex align-items-center mt-1" style="font-size: 0.68rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-info-circle me-1"></i> Registrados hoy
                    </small>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, rgba(6,182,212,0.1) 0%, rgba(20,184,166,0.15) 100%);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Quick Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/inventario/csv') ?>" class="export-card" id="export-inventario-csv">
            <div class="export-card-icon export-csv">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="export-card-body">
                <h6>Inventario Excel</h6>
                <small>Todos los productos activos con costos, stock y categorías</small>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="export-card-arrow"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/completo/pdf') ?>" target="_blank" class="export-card" id="export-completo-pdf">
            <div class="export-card-icon export-pdf">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M9 15v-2h2a1 1 0 1 0 0-2H9v6"/></svg>
            </div>
            <div class="export-card-body">
                <h6>Reporte Completo PDF</h6>
                <small>Inventario, stock bajo, top productos y categorías</small>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="export-card-arrow"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/movimientos/csv') ?>" class="export-card" id="export-movimientos-csv">
            <div class="export-card-icon export-mov">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="export-card-body">
                <h6>Movimientos Excel</h6>
                <small>Historial completo de entradas, salidas y ajustes</small>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="export-card-arrow"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
    </div>
</div>

<!-- Exportar Movimientos por Fecha -->
<div class="card mb-4" id="card-export-fecha">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-calendar-event me-2 text-primary"></i>Exportar Movimientos por Fecha
        </h6>
        <span class="badge bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-funnel me-1"></i>Filtro por fecha
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <!-- Modo de filtro -->
            <div class="col-md-3">
                <label for="filtroFechaModo" class="form-label fw-semibold">
                    <i class="bi bi-sliders me-1"></i>Tipo de filtro
                </label>
                <select class="form-select" id="filtroFechaModo">
                    <option value="exacta">Fecha exacta</option>
                    <option value="rango">Rango de fechas</option>
                </select>
            </div>

            <!-- Fecha exacta -->
            <div class="col-md-3" id="filtroFechaExactaGroup">
                <label for="filtroFechaExacta" class="form-label fw-semibold">
                    <i class="bi bi-calendar-date me-1"></i>Fecha
                </label>
                <input type="date" class="form-control" id="filtroFechaExacta" 
                       value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
            </div>

            <!-- Rango: Desde -->
            <div class="col-md-3 d-none" id="filtroFechaDesdeGroup">
                <label for="filtroFechaDesde" class="form-label fw-semibold">
                    <i class="bi bi-calendar-minus me-1"></i>Desde
                </label>
                <input type="date" class="form-control" id="filtroFechaDesde" max="<?= date('Y-m-d') ?>">
            </div>

            <!-- Rango: Hasta -->
            <div class="col-md-3 d-none" id="filtroFechaHastaGroup">
                <label for="filtroFechaHasta" class="form-label fw-semibold">
                    <i class="bi bi-calendar-plus me-1"></i>Hasta
                </label>
                <input type="date" class="form-control" id="filtroFechaHasta" 
                       value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
            </div>

            <!-- Botones de exportar -->
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary flex-fill" id="btnExportFechaCSV" title="Exportar Excel">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                    </button>
                    <button type="button" class="btn btn-primary flex-fill" id="btnExportFechaPDF" title="Exportar PDF">
                        <i class="bi bi-filetype-pdf me-1"></i>PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Selecciona una fecha exacta o un rango de fechas y elige el formato de exportación. 
                Si no hay movimientos en la fecha seleccionada, recibirás un aviso.
            </small>
        </div>
    </div>
</div>

<!-- Reportes -->
<div class="row g-4">
    <!-- Tendencia de Movimientos -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Tendencia Mensual de Movimientos</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary">Últimos 6 meses</span>
                    <a href="<?= url('reportes/exportar/movimientos/csv') ?>" class="btn-export-inline" title="Exportar Excel">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="chartReporteTendencia"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis Analítico por Categoría (Barras / Radar) -->
    <div class="col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3" style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Análisis por Categoría</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Toggle buttons for Bars and Radar -->
                    <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de Gráfico" style="background: var(--bs-tertiary-bg); padding: 2px; border-radius: 8px;">
                        <button type="button" class="btn btn-sm btn-primary active py-1 px-3" id="btnChartBars" style="border-radius: 6px; font-size: 0.72rem; font-weight: 600; border: none; box-shadow: none;">
                            <i class="bi bi-bar-chart-fill me-1"></i>Valor
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary text-muted py-1 px-3" id="btnChartRadar" style="border-radius: 6px; font-size: 0.72rem; font-weight: 600; border: none; box-shadow: none;">
                            <i class="bi bi-radar me-1"></i>Perfil
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="chartCategoryContainer" style="position: relative; height: 240px; width: 100%;">
                    <!-- Canvas elements, shown/hidden dynamically via JS -->
                    <canvas id="chartCategoryBars" style="width: 100%; height: 100%;"></canvas>
                    <canvas id="chartCategoryRadar" class="d-none" style="width: 100%; height: 100%;"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top" style="border-color: rgba(148, 163, 184, 0.08) !important;">
                    <div class="row text-center g-2" style="font-size: 0.78rem;">
                        <div class="col-6 border-end" style="border-color: rgba(148, 163, 184, 0.08) !important;">
                            <span class="text-muted d-block mb-1">Categoría de Mayor Valor</span>
                            <strong id="cat-major-value" class="text-primary" style="font-family: 'Inter', sans-serif;">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Stock Total Acumulado</span>
                            <strong id="cat-total-stock" style="font-family: 'Inter', sans-serif; color: var(--text-primary);">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 10 Productos Más Movidos</h6>
                <a href="<?= url('reportes/exportar/top-productos/csv') ?>" class="btn-export-inline" title="Exportar Excel">
                    <i class="bi bi-download"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topProductos)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width:64px;height:64px;margin-bottom:1rem;">
                            <svg viewBox="0 0 100 100">
                                <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                                <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                            </svg>
                            <i class="bi bi-graph-down" style="font-size:1.6rem;"></i>
                        </div>
                        <h6>Insuficientes Datos</h6>
                        <p class="text-muted mb-0" style="font-size:0.75rem">Se requiere más tiempo para evaluar</p>
                    </div>
                <?php else: ?>
                <div class="table-wrapper">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>#</th><th>Producto</th><th>Entradas</th><th>Salidas</th><th>Total</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProductos as $i => $tp): ?>
                            <tr>
                                <td><span class="badge bg-primary rounded-pill"><?= $i + 1 ?></span></td>
                                <td><strong><?= htmlspecialchars($tp->nombre) ?></strong><br><small class="text-muted"><?= $tp->sku ?></small></td>
                                <td class="text-success fw-bold">+<?= $tp->total_entradas ?></td>
                                <td class="text-danger fw-bold">-<?= $tp->total_salidas ?></td>
                                <td><strong><?= $tp->total_movimientos ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Productos con Stock Bajo o Agotado</h6>
                    <span class="badge bg-warning text-dark"><?= count($productosStockBajo) ?></span>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('reportes/exportar/stock-bajo/csv') ?>" class="btn-export-inline" title="Exportar Excel">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </a>
                    <a href="<?= url('reportes/exportar/stock-bajo/pdf') ?>" class="btn-export-inline" target="_blank" title="Exportar PDF">
                        <i class="bi bi-filetype-pdf"></i>
                    </a>
                </div>
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
                        <h5>Todos los productos tienen stock normal</h5>
                    </div>
                <?php else: ?>
                <div class="table-wrapper">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>SKU</th><th>Producto</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Estado</th><th>Valor</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosStockBajo as $p): ?>
                            <tr>
                                <td><code class="text-primary"><?= $p->sku ?></code></td>
                                <td><strong><?= htmlspecialchars($p->nombre) ?></strong></td>
                                <td><?= htmlspecialchars($p->categoria_nombre ?? '-') ?></td>
                                <td class="fw-bold"><?= $p->stock ?></td>
                                <td><?= $p->stock_minimo ?></td>
                                <td>
                                    <?php if ($p->stock <= 0): ?>
                                        <span class="badge badge-stock-out">Agotado</span>
                                    <?php else: ?>
                                        <span class="badge badge-stock-low">Bajo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatMoney($p->costo * $p->stock) ?></td>
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
<?php
$reportData = [
    'categorias'        => $productosPorCategoria ?? [],
    'categoriaAnalisis' => $categoriaAnalisis ?? [],
    'tendenciaMensual'  => $tendenciaMensual ?? [],
    'exportCsvUrl'      => url('reportes/exportar/movimientos/csv'),
    'exportPdfUrl'      => url('reportes/exportar/movimientos/pdf'),
];
?>
<script id="page-data" type="application/json"><?= json_encode($reportData) ?></script>
<script src="<?= asset('js/reportes.js') ?>?v=<?= filemtime(PUBLIC_PATH . '/assets/js/reportes.js') ?>"></script>


