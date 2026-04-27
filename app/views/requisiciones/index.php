<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Requisiciones Internas</h4>
        <span class="text-muted">Gestión de despachos y solicitudes de almacén</span>
    </div>
    
    <?php if (hasPermission('requisiciones.crear')): ?>
        <div class="d-flex gap-2">
            <a href="<?= url('requisiciones/crear') ?>" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nueva Requisición
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('requisiciones') ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1 text-muted small fw-semibold">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="borrador" <?= $filtros['estado'] == 'borrador' ? 'selected' : '' ?>>Borrador</option>
                    <option value="pendiente" <?= $filtros['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="despachada" <?= $filtros['estado'] == 'despachada' ? 'selected' : '' ?>>Despachada</option>
                    <option value="cancelada" <?= $filtros['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 text-muted small fw-semibold">Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 text-muted small fw-semibold">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Requisición #</th>
                        <th>Fecha</th>
                        <th>Departamento</th>
                        <th>Solicitante</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($requisiciones)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-0">No se encontraron requisiciones.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requisiciones as $r): ?>
                            <tr>
                                <td class="ps-4 fw-medium text-primary">
                                    <a href="<?= url('requisiciones/show/' . $r->id) ?>" class="text-decoration-none text-primary">
                                        <?= htmlspecialchars($r->numero_requisicion) ?>
                                    </a>
                                </td>
                                <td><?= formatDate($r->fecha_solicitud) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($r->departamento_nombre) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($r->usuario_nombre) ?></small></td>
                                <td>
                                    <?php
                                        $badge = match($r->estado) {
                                            'borrador' => 'bg-secondary',
                                            'pendiente' => 'bg-warning text-dark',
                                            'despachada' => 'bg-success',
                                            'cancelada' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badge ?> text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <?= $r->estado ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= url('requisiciones/show/' . $r->id) ?>" class="btn btn-sm btn-light btn-icon" title="Ver Detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if ($last_page > 1): ?>
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('requisiciones?page='.($page-1).'&estado='.$filtros['estado'].'&fecha_desde='.$filtros['fecha_desde'].'&fecha_hasta='.$filtros['fecha_hasta']) ?>">Anterior</a>
                </li>
                <?php for($i = 1; $i <= $last_page; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('requisiciones?page='.$i.'&estado='.$filtros['estado'].'&fecha_desde='.$filtros['fecha_desde'].'&fecha_hasta='.$filtros['fecha_hasta']) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $last_page ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('requisiciones?page='.($page+1).'&estado='.$filtros['estado'].'&fecha_desde='.$filtros['fecha_desde'].'&fecha_hasta='.$filtros['fecha_hasta']) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>
