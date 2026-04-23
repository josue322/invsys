<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0">Proveedores</h5>
        <small class="text-muted"><?= $total ?> registrados</small>
    </div>
    <a href="<?= url('proveedores/crear') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Proveedor
    </a>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('proveedores') ?>" class="filter-bar mb-3">
    <div class="form-group flex-grow-1">
        <label>Buscar</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Nombre, RUC o contacto..." value="<?= htmlspecialchars($search) ?>">
        </div>
    </div>
    <div class="form-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary d-block">Filtrar</button>
    </div>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($proveedores)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-truck"></i>
                </div>
                <h5>No hay proveedores</h5>
                <p class="text-muted">Aún no has registrado proveedores o la búsqueda no coincide.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RUC/DNI</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $prov): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($prov->nombre) ?></td>
                            <td><?= htmlspecialchars($prov->ruc_dni ?? '-') ?></td>
                            <td><?= htmlspecialchars($prov->contacto ?? '-') ?></td>
                            <td><?= htmlspecialchars($prov->telefono ?? '-') ?></td>
                            <td><?= htmlspecialchars($prov->email ?? '-') ?></td>
                            <td>
                                <?php if ($prov->activo): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Inactivo</span>
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
