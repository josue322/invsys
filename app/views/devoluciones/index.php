<?php
/**
 * InvSys - Vista de Listado de Devoluciones
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-body"><i class="bi bi-arrow-return-left me-2"
                style="color: var(--primary);"></i>Devoluciones</h2>
        <p class="text-muted mb-0">Logística inversa de departamentos al almacén</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (hasPermission('devoluciones.crear')): ?>
            <a href="<?= url('devoluciones/crear') ?>" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nueva Devolución
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('devoluciones') ?>" class="row g-2 align-items-center">
            <div class="col-md-5 col-sm-12">
                <div class="search-pill w-100">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                        placeholder="Buscar por número o departamento...">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="estado" class="form-select border-light-subtle bg-light">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?= ($estado ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="aprobada" <?= ($estado ?? '') === 'aprobada' ? 'selected' : '' ?>>Aprobada</option>
                    <option value="rechazada" <?= ($estado ?? '') === 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
            </div>
            <?php if (!empty($search) || !empty($estado)): ?>
                <div class="col-md-2 col-sm-12">
                    <a href="<?= url('devoluciones') ?>"
                        class="btn btn-link text-muted w-100 text-decoration-none">Limpiar</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Listado -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($devoluciones['data'])): ?>
            <div class="empty-state py-5 text-center">
                <div class="empty-state-icon mx-auto mb-3"
                    style="width: 64px; height: 64px; background: rgba(100,116,139,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-inbox text-muted" style="font-size: 1.8rem;"></i>
                </div>
                <h6 class="fw-bold">No hay devoluciones registradas</h6>
                <p class="text-muted mb-0">Las devoluciones aparecerán aquí.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table mb-0 table-hover align-middle">
                    <thead>
                        <tr>
                            <th>N° Devolución</th>
                            <th>Departamento</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Registrado por</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($devoluciones['data'] as $dev): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($dev->numero_devolucion) ?></strong>
                                    <?php if ($dev->numero_requisicion): ?>
                                        <br><small class="text-muted">Req: <?= htmlspecialchars($dev->numero_requisicion) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center me-2"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <span class="fw-medium"><?= htmlspecialchars($dev->departamento_nombre) ?></span>
                                    </div>
                                </td>
                                <td class="tabular-nums text-muted">
                                    <?= formatDate($dev->created_at) ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match ($dev->estado) {
                                        'aprobada' => 'bg-success-subtle text-success',
                                        'rechazada' => 'bg-danger-subtle text-danger',
                                        default => 'bg-warning-subtle text-warning'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> border border-0 px-2 py-1">
                                        <?= ucfirst($dev->estado) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><i
                                            class="bi bi-person me-1"></i><?= htmlspecialchars($dev->usuario_nombre) ?></small>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= url("devoluciones/ver/{$dev->id}") ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            style="color: var(--primary); border-color: var(--primary);" title="Ver Detalle">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <?php if (hasPermission('devoluciones.eliminar')): ?>
                                            <form method="POST" action="<?= url('devoluciones/destroy/' . $dev->id) ?>"
                                                data-confirm='{"title":"¿Eliminar devolución permanentemente?","message":"Esta acción eliminará la devolución y todos sus detalles. Esta acción no se puede deshacer.","type":"danger","confirmText":"Sí, eliminar","icon":"bi-trash"}'
                                                style="display:inline">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar Devolución">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<!-- Paginación -->
<?php if (!empty($devoluciones['data']) && $devoluciones['pages'] > 1): ?>
    <div class="mt-4">
        <?php
        $pg = $devoluciones;
        $baseUrl = 'devoluciones?search=' . urlencode($search) . '&estado=' . urlencode($estado);
        include APP_PATH . '/views/layouts/_pagination.php';
        ?>
    </div>
<?php endif; ?>