<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0">Ubicaciones de Almacén</h5>
        <small class="text-muted"><?= $total ?> ubicaciones registradas</small>
    </div>
    <a href="<?= url('ubicaciones/crear') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nueva Ubicación
    </a>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($ubicaciones)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h5>No hay ubicaciones</h5>
                <p class="text-muted">Empieza creando zonas o estantes para organizar tu inventario.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ubicaciones as $ub): ?>
                        <tr>
                            <td class="fw-bold"><i class="bi bi-geo text-muted me-2"></i><?= htmlspecialchars($ub->nombre) ?></td>
                            <td><?= htmlspecialchars($ub->descripcion ?? '-') ?></td>
                            <td>
                                <?php if ($ub->activa): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Inactiva</span>
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
