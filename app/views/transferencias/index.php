<div class="content-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h3 mb-0 text-gray-800"><i
                class="bi bi-arrow-left-right me-2 text-primary"></i><?= htmlspecialchars($title) ?></h2>
        <p class="text-muted mb-0">Historial de movimientos entre ubicaciones</p>
    </div>
    <div>
        <a href="<?= url('transferencias/crear') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nueva Transferencia
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="<?= url('transferencias') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted small">Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($fechaDesde) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($fechaHasta) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small">Producto</label>
                <select name="producto_id" class="form-select form-select-sm">
                    <option value="">Todos los productos</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p->id ?>" <?= $productoId == $p->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p->sku . ' - ' . $p->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4">Fecha</th>
                        <th>Referencia</th>
                        <th>Producto</th>
                        <th>Cantidad Movida</th>
                        <th>Detalle de Transferencia</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movimientos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inboxes display-4 text-light mb-3 d-block"></i>
                                No se encontraron transferencias
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movimientos as $m): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium"><?= date('d/m/Y', strtotime($m->created_at)) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($m->created_at)) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($m->referencia) ?></span>
                                </td>
                                <td>
                                    <a href="<?= url("productos/ver/{$m->producto_id}") ?>" class="text-decoration-none text-dark hover-primary">
                                        <div class="fw-medium text-dark"><strong><?= htmlspecialchars($m->producto_nombre) ?></strong></div>
                                    </a>
                                    <small class="text-muted font-monospace"><?= htmlspecialchars($m->producto_sku) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2" style="font-size:0.85rem">
                                        <?= number_format($m->cantidad) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="d-block text-muted"><?= htmlspecialchars($m->observaciones) ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <?= strtoupper(substr($m->usuario_nombre, 0, 1)) ?>
                                        </div>
                                        <span
                                            class="small fw-medium text-dark"><?= htmlspecialchars($m->usuario_nombre) ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pages > 1): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <nav aria-label="Navegación de páginas">
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=<?= $current - 1 ?>&fecha_desde=<?= urlencode($fechaDesde) ?>&fecha_hasta=<?= urlencode($fechaHasta) ?>&producto_id=<?= $productoId ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= $i ?>&fecha_desde=<?= urlencode($fechaDesde) ?>&fecha_hasta=<?= urlencode($fechaHasta) ?>&producto_id=<?= $productoId ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $current >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=<?= $current + 1 ?>&fecha_desde=<?= urlencode($fechaDesde) ?>&fecha_hasta=<?= urlencode($fechaHasta) ?>&producto_id=<?= $productoId ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>