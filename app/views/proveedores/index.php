<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0">Proveedores</h5>
        <small class="text-muted"><?= $total ?> registrados</small>
    </div>
    <?php if (hasPermission('proveedores.crear')): ?>
    <a href="<?= url('proveedores/crear') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Proveedor
    </a>
    <?php endif; ?>
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
    <div class="form-group" style="flex:0">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary d-block"><i class="bi bi-search"></i></button>
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
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RUC/DNI</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
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
                                    <span class="badge badge-stock-ok">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-stock-out">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (hasPermission('proveedores.editar')): ?>
                                    <a href="<?= url("proveedores/editar/{$prov->id}") ?>" class="btn-action btn-edit" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <!-- Toggle activo/inactivo -->
                                    <form method="POST" action="<?= url("proveedores/toggle/{$prov->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => ($prov->activo ? "¿Desactivar" : "¿Activar") . " proveedor?",
                                              "message" => ($prov->activo
                                                  ? "El proveedor <strong>" . htmlspecialchars($prov->nombre, ENT_QUOTES) . "</strong> será desactivado."
                                                  : "El proveedor <strong>" . htmlspecialchars($prov->nombre, ENT_QUOTES) . "</strong> será activado nuevamente."),
                                              "type" => "warning",
                                              "confirmText" => $prov->activo ? "Sí, desactivar" : "Sí, activar",
                                              "icon" => $prov->activo ? "bi-toggle-off" : "bi-toggle-on"
                                          ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action <?= $prov->activo ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
                                                title="<?= $prov->activo ? 'Desactivar' : 'Activar' ?>">
                                            <i class="bi <?= $prov->activo ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if (hasPermission('proveedores.eliminar')): ?>
                                    <form method="POST" action="<?= url("proveedores/eliminar/{$prov->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => "¿Eliminar proveedor?",
                                              "message" => "El proveedor <strong>" . htmlspecialchars($prov->nombre, ENT_QUOTES) . "</strong> será desactivado.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
                                              "type" => "danger",
                                              "confirmText" => "Sí, eliminar",
                                              "icon" => "bi-trash-fill"
                                          ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action btn-delete" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
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

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-between align-items-center px-3 py-3">
            <small class="text-muted">
                Mostrando <?= count($proveedores) ?> de <?= $total ?> registros
            </small>
            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url("proveedores?page={$i}&search=" . urlencode($search)) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
