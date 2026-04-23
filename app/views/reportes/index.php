<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Reportes & Exportación</h5>
        <small class="text-muted">Visualiza y exporta datos de tu inventario</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Export Dropdown CSV -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnExportCSV" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnExportCSV">
                <li><h6 class="dropdown-header"><i class="bi bi-table me-1"></i>Exportar a CSV</h6></li>
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

<!-- Export Quick Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/inventario/csv') ?>" class="export-card" id="export-inventario-csv">
            <div class="export-card-icon export-csv">
                <i class="bi bi-filetype-csv"></i>
            </div>
            <div class="export-card-body">
                <h6>Inventario CSV</h6>
                <small>Todos los productos activos con precios, stock y categorías</small>
            </div>
            <i class="bi bi-download export-card-arrow"></i>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/completo/pdf') ?>" target="_blank" class="export-card" id="export-completo-pdf">
            <div class="export-card-icon export-pdf">
                <i class="bi bi-filetype-pdf"></i>
            </div>
            <div class="export-card-body">
                <h6>Reporte Completo PDF</h6>
                <small>Inventario, stock bajo, top productos y categorías</small>
            </div>
            <i class="bi bi-box-arrow-up-right export-card-arrow"></i>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('reportes/exportar/movimientos/csv') ?>" class="export-card" id="export-movimientos-csv">
            <div class="export-card-icon export-mov">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div class="export-card-body">
                <h6>Movimientos CSV</h6>
                <small>Historial completo de entradas, salidas y ajustes</small>
            </div>
            <i class="bi bi-download export-card-arrow"></i>
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
                    <button type="button" class="btn btn-outline-primary flex-fill" id="btnExportFechaCSV" title="Exportar CSV">
                        <i class="bi bi-filetype-csv me-1"></i>CSV
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
    <!-- Resumen de Stock -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Distribución por Categoría</h6>
                <a href="<?= url('reportes/exportar/categorias/csv') ?>" class="btn-export-inline" title="Exportar CSV">
                    <i class="bi bi-download"></i>
                </a>
            </div>
            <div class="card-body">
                <canvas id="chartReporteCategorias" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 10 Productos Más Movidos</h6>
                <a href="<?= url('reportes/exportar/top-productos/csv') ?>" class="btn-export-inline" title="Exportar CSV">
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
                    <a href="<?= url('reportes/exportar/stock-bajo/csv') ?>" class="btn-export-inline" title="Exportar CSV">
                        <i class="bi bi-filetype-csv"></i>
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
                                <td><?= formatMoney($p->precio * $p->stock) ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Gráfico de categorías ===
    const catData = <?= json_encode($productosPorCategoria) ?>;
    const catLabels = catData.map(c => c.categoria);
    const catValues = catData.map(c => parseInt(c.total));
    const catColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#f97316', '#14b8a6'];

    if (catLabels.length > 0) {
        new Chart(document.getElementById('chartReporteCategorias'), {
            type: 'polarArea',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catColors.map(c => c + 'cc'),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // === Filtro de fecha para movimientos ===
    const modoSelect = document.getElementById('filtroFechaModo');
    const exactaGroup = document.getElementById('filtroFechaExactaGroup');
    const desdeGroup = document.getElementById('filtroFechaDesdeGroup');
    const hastaGroup = document.getElementById('filtroFechaHastaGroup');
    const inputExacta = document.getElementById('filtroFechaExacta');
    const inputDesde = document.getElementById('filtroFechaDesde');
    const inputHasta = document.getElementById('filtroFechaHasta');
    const btnCSV = document.getElementById('btnExportFechaCSV');
    const btnPDF = document.getElementById('btnExportFechaPDF');
    const baseCSV = '<?= url('reportes/exportar/movimientos/csv') ?>';
    const basePDF = '<?= url('reportes/exportar/movimientos/pdf') ?>';

    // Toggle entre fecha exacta y rango
    modoSelect.addEventListener('change', function() {
        if (this.value === 'exacta') {
            exactaGroup.classList.remove('d-none');
            desdeGroup.classList.add('d-none');
            hastaGroup.classList.add('d-none');
        } else {
            exactaGroup.classList.add('d-none');
            desdeGroup.classList.remove('d-none');
            hastaGroup.classList.remove('d-none');
        }
    });

    // Construir URL con parámetros de fecha
    function buildUrl(base) {
        const modo = modoSelect.value;
        if (modo === 'exacta') {
            const fecha = inputExacta.value;
            if (!fecha) {
                alert('Por favor, selecciona una fecha.');
                return null;
            }
            return base + '?fecha=' + fecha;
        } else {
            const desde = inputDesde.value;
            const hasta = inputHasta.value;
            if (!desde && !hasta) {
                alert('Por favor, selecciona al menos una fecha del rango.');
                return null;
            }
            let params = [];
            if (desde) params.push('fecha_desde=' + desde);
            if (hasta) params.push('fecha_hasta=' + hasta);
            return base + '?' + params.join('&');
        }
    }

    btnCSV.addEventListener('click', function() {
        const url = buildUrl(baseCSV);
        if (url) window.location.href = url;
    });

    btnPDF.addEventListener('click', function() {
        const url = buildUrl(basePDF);
        if (url) window.open(url, '_blank');
    });
});
</script>

