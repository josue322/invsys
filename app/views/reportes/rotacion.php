<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Rotación de Inventario</h5>
        <small class="text-muted">Índice de rotación y velocidad de salida por producto</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url("reportes/analisis/rotacion/csv?dias={$dias}") ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
        </a>
        <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
    </div>
</div>

<!-- Filtro de período -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/analisis/rotacion') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="dias" class="form-label fw-semibold">
                    <i class="bi bi-calendar-range me-1"></i>Período de análisis
                </label>
                <select class="form-select" name="dias" id="dias">
                    <option value="30" <?= $dias == 30 ? 'selected' : '' ?>>Últimos 30 días</option>
                    <option value="60" <?= $dias == 60 ? 'selected' : '' ?>>Últimos 60 días</option>
                    <option value="90" <?= $dias == 90 ? 'selected' : '' ?>>Últimos 90 días</option>
                    <option value="180" <?= $dias == 180 ? 'selected' : '' ?>>Últimos 6 meses</option>
                    <option value="365" <?= $dias == 365 ? 'selected' : '' ?>>Último año</option>
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

<!-- Leyenda -->
<div class="d-flex gap-3 mb-3 flex-wrap">
    <small><span class="badge bg-success">Alta ≥ 3.0</span> Se mueve rápido</small>
    <small><span class="badge bg-warning text-dark">Media 1.0–2.9</span> Normal</small>
    <small><span class="badge bg-danger">Baja 0.01–0.9</span> Lento</small>
    <small><span class="badge bg-secondary">Nula 0</span> Sin salidas</small>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Rotación (<?= $dias ?> días)</h6>
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
                    <i class="bi bi-arrow-repeat" style="font-size:1.6rem;"></i>
                </div>
                <h6>Sin datos</h6>
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
                            <th class="text-end">Entradas</th>
                            <th class="text-end">Salidas</th>
                            <th class="text-center">Índice</th>
                            <th class="text-center">Velocidad</th>
                            <th class="text-end">Días Inv.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><code class="text-primary"><?= $item->sku ?></code></td>
                            <td><strong><?= htmlspecialchars($item->nombre) ?></strong></td>
                            <td><small class="text-muted"><?= htmlspecialchars($item->categoria) ?></small></td>
                            <td class="text-end"><?= $item->stock ?></td>
                            <td class="text-end text-success">+<?= $item->entradas ?></td>
                            <td class="text-end text-danger">-<?= $item->salidas ?></td>
                            <td class="text-center fw-bold"><?= $item->rotacion ?></td>
                            <td class="text-center">
                                <?php
                                    $velBadge = match($item->velocidad) {
                                        'alta' => 'bg-success',
                                        'media' => 'bg-warning text-dark',
                                        'baja' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <span class="badge <?= $velBadge ?>"><?= ucfirst($item->velocidad) ?></span>
                            </td>
                            <td class="text-end">
                                <?php if ($item->dias_inventario >= 999): ?>
                                    <span class="text-danger fw-bold">+999</span>
                                <?php else: ?>
                                    <?= $item->dias_inventario ?> <small class="text-muted">días</small>
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
