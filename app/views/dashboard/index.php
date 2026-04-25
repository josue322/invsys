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

<!-- Chart.js Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── KPI Counter Animation ───
    document.querySelectorAll('.kpi-value').forEach(el => {
        const raw = el.textContent.trim();
        // Extract prefix (everything before the first digit)
        const prefixMatch = raw.match(/^[^0-9]*/);
        const prefix = prefixMatch ? prefixMatch[0] : '';
        // Parse only the numeric part AFTER the prefix
        const numPart = raw.substring(prefix.length);
        const numStr = numPart.replace(/[^0-9.]/g, '');
        const target = parseFloat(numStr);
        if (isNaN(target) || target === 0) return;

        const hasDec = numPart.includes('.');
        const duration = 1200;
        let start = null;

        el.textContent = prefix + '0';
        
        function step(ts) {
            if (!start) start = ts;
            const progress = Math.min((ts - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
            const current = eased * target;
            
            if (hasDec) {
                const formatted = current.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const parts = formatted.split('.');
                el.innerHTML = `<span class="kpi-prefix">${prefix}</span>${parts[0]}.<span class="kpi-decimal">${parts[1]}</span>`;
            } else {
                el.innerHTML = `<span class="kpi-prefix">${prefix}</span>${Math.floor(current).toLocaleString('en-US')}`;
            }
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    // ─── Movimientos Chart (Bar with Gradients) ───
    const movData = <?= json_encode($movimientosSemana) ?>;
    const fechas = [...new Set(movData.map(m => m.fecha))];
    const entradas = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'entrada');
        return item ? parseInt(item.cantidad_total) : 0;
    });
    const salidas = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'salida');
        return item ? parseInt(item.cantidad_total) : 0;
    });
    const ajustes = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'ajuste');
        return item ? parseInt(item.cantidad_total) : 0;
    });

    const fechaLabels = fechas.map(f => {
        const d = new Date(f + 'T00:00:00');
        return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
    });

    const ctxMov = document.getElementById('chartMovimientos').getContext('2d');

    // Create gradients for bars
    const gradEntrada = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradEntrada.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
    gradEntrada.addColorStop(1, 'rgba(16, 185, 129, 0.3)');

    const gradSalida = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradSalida.addColorStop(0, 'rgba(239, 68, 68, 0.9)');
    gradSalida.addColorStop(1, 'rgba(239, 68, 68, 0.3)');

    const gradAjuste = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradAjuste.addColorStop(0, 'rgba(6, 182, 212, 0.9)');
    gradAjuste.addColorStop(1, 'rgba(6, 182, 212, 0.3)');

    // Custom tooltip style
    const premiumTooltip = {
        backgroundColor: 'rgba(15, 23, 42, 0.92)',
        titleFont: { weight: '700', size: 13, family: 'Inter' },
        bodyFont: { size: 12, family: 'Inter' },
        padding: { top: 10, right: 14, bottom: 10, left: 14 },
        cornerRadius: 10,
        displayColors: true,
        boxPadding: 4,
        caretSize: 6,
        borderColor: 'rgba(99, 102, 241, 0.2)',
        borderWidth: 1,
    };

    new Chart(ctxMov, {
        type: 'bar',
        data: {
            labels: fechaLabels.length > 0 ? fechaLabels : ['Hoy'],
            datasets: [
                {
                    label: 'Entradas',
                    data: entradas.length > 0 ? entradas : [0],
                    backgroundColor: gradEntrada,
                    hoverBackgroundColor: 'rgba(16, 185, 129, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Salidas',
                    data: salidas.length > 0 ? salidas : [0],
                    backgroundColor: gradSalida,
                    hoverBackgroundColor: 'rgba(239, 68, 68, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Ajustes',
                    data: ajustes.length > 0 ? ajustes : [0],
                    backgroundColor: gradAjuste,
                    hoverBackgroundColor: 'rgba(6, 182, 212, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart',
                delay: (ctx) => ctx.dataIndex * 80
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        font: { size: 12, weight: '500', family: 'Inter' }
                    }
                },
                tooltip: premiumTooltip
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { font: { size: 11, family: 'Inter' }, padding: 8 },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter' }, padding: 4 },
                    border: { display: false }
                }
            }
        }
    });

    // ─── Categorías Chart (Doughnut with Hover Effect) ───
    const catData = <?= json_encode($productosPorCategoria) ?>;
    const catLabels = catData.map(c => c.categoria);
    const catValues = catData.map(c => parseInt(c.total));
    const catColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#f97316'];

    new Chart(document.getElementById('chartCategorias'), {
        type: 'doughnut',
        data: {
            labels: catLabels.length > 0 ? catLabels : ['Sin datos'],
            datasets: [{
                data: catValues.length > 0 ? catValues : [1],
                backgroundColor: catColors,
                borderWidth: 0,
                hoverOffset: 12,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            animation: {
                animateRotate: true,
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, weight: '500', family: 'Inter' }
                    }
                },
                tooltip: premiumTooltip
            }
        }
    });
});
</script>

