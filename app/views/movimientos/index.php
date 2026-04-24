<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Movimientos de Inventario</h5>
        <small class="text-muted"><?= $movimientos['total'] ?> registros</small>
    </div>
    <?php if (hasPermission('movimientos.crear')): ?>
    <a href="<?= url('movimientos/crear') ?>" class="btn btn-primary" id="btn-nuevo-movimiento">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Movimiento
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('movimientos') ?>" class="filter-bar" id="filter-movimientos">
    <div class="form-group">
        <label>Tipo</label>
        <select name="tipo" class="form-select">
            <option value="">Todos</option>
            <option value="entrada" <?= $tipo === 'entrada' ? 'selected' : '' ?>>Entrada</option>
            <option value="salida" <?= $tipo === 'salida' ? 'selected' : '' ?>>Salida</option>
            <option value="ajuste" <?= $tipo === 'ajuste' ? 'selected' : '' ?>>Ajuste</option>
        </select>
    </div>
    <div class="form-group">
        <label>Desde</label>
        <input type="date" name="fecha_desde" class="form-control" value="<?= $fechaDesde ?>">
    </div>
    <div class="form-group">
        <label>Hasta</label>
        <input type="date" name="fecha_hasta" class="form-control" value="<?= $fechaHasta ?>">
    </div>
    <div class="form-group">
        <label>Producto</label>
        <select name="producto" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($productos as $p): ?>
            <option value="<?= $p->id ?>" <?= $productoId == $p->id ? 'selected' : '' ?>><?= htmlspecialchars($p->nombre) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" style="flex:0">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary d-block"><i class="bi bi-search"></i></button>
    </div>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($movimientos['data'])): ?>
            <div class="empty-state" style="padding: 3.5rem 1rem;">
                <div class="empty-state-icon" style="width:90px;height:90px;margin-bottom:1.5rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-arrow-left-right" style="font-size:2rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Sin movimientos</h5>
                <p class="text-muted mb-3" style="max-width:320px;">No se encontraron movimientos con los filtros seleccionados. Pruebe con otro rango de fechas.</p>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabla-movimientos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Stock</th>
                        <th>Lote</th>
                        <th>Origen / Destino</th>
                        <th>Usuario</th>
                        <th>Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos['data'] as $m): ?>
                    <tr>
                        <td><span class="text-muted">#<?= $m->id ?></span></td>
                        <td><?= formatDate($m->created_at) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($m->producto_nombre) ?></strong>
                            <br><small class="text-muted"><?= $m->producto_sku ?></small>
                        </td>
                        <td>
                            <span class="badge badge-tipo-<?= $m->tipo ?> fw-bold">
                                <?= ucfirst($m->tipo) ?>
                            </span>
                        </td>
                        <td class="fw-bold tabular-nums">
                            <?php if ($m->tipo === 'entrada'): ?>
                                <span class="text-success">+<?= $m->cantidad ?></span>
                            <?php elseif ($m->tipo === 'salida'): ?>
                                <span class="text-danger">-<?= $m->cantidad ?></span>
                            <?php else: ?>
                                <span class="text-info"><?= $m->cantidad ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="tabular-nums">
                            <small><?= $m->stock_anterior ?> → <strong><?= $m->stock_nuevo ?></strong></small>
                        </td>
                        <td>
                            <?php if (!empty($m->lote_numero)): ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><i class="bi bi-box-seam me-1"></i><?= htmlspecialchars($m->lote_numero) ?></span>
                            <?php else: ?>
                                <span class="text-muted"><small>-</small></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($m->tipo === 'entrada' && !empty($m->proveedor_nombre)): ?>
                                <small><i class="bi bi-truck me-1 text-muted"></i><?= htmlspecialchars($m->proveedor_nombre) ?></small>
                            <?php elseif ($m->tipo === 'salida' && !empty($m->destino)): ?>
                                <small><i class="bi bi-geo-alt me-1 text-muted"></i><?= htmlspecialchars($m->destino) ?></small>
                            <?php else: ?>
                                <span class="text-muted"><small>-</small></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m->usuario_nombre) ?></td>
                        <td><small><?= htmlspecialchars($m->referencia ?? '-') ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
            $pg = $movimientos;
            $baseUrl = 'movimientos?tipo=' . urlencode($tipo) . '&fecha_desde=' . urlencode($fechaDesde) . '&fecha_hasta=' . urlencode($fechaHasta) . '&producto=' . $productoId;
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
        <?php endif; ?>
    </div>
</div>
