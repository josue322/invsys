<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Catálogo de Departamentos</h4>
        <span class="text-muted">Áreas internas que realizan requisiciones</span>
    </div>
    
    <?php if (hasPermission('departamentos.crear')): ?>
        <div class="d-flex gap-2">
            <a href="<?= url('departamentos/create') ?>" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Departamento
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('departamentos') ?>" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Buscar</button>
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
                        <th class="ps-4">ID</th>
                        <th>Nombre del Departamento</th>
                        <th>Responsable</th>
                        <th>C. Costos</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <?php if (hasPermission('departamentos.crear') || hasPermission('departamentos.eliminar')): ?>
                            <th class="text-end pe-4">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($departamentos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-3"></i>
                                    <p class="mb-0">No se encontraron departamentos.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departamentos as $d): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $d->id ?></td>
                                <td class="fw-medium"><?= htmlspecialchars($d->nombre) ?></td>
                                <td><?= htmlspecialchars($d->responsable ?: '-') ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d->centro_costo ?: 'N/A') ?></span></td>
                                <td><?= htmlspecialchars($d->telefono ?: '-') ?></td>
                                <td>
                                    <?php if ($d->activo): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (hasPermission('departamentos.crear') || hasPermission('departamentos.eliminar')): ?>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <?php if (hasPermission('departamentos.crear')): ?>
                                                <a href="<?= url('departamentos/edit/' . $d->id) ?>" class="btn-action btn-edit" title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>

                                                <!-- Toggle activa/inactiva -->
                                                <form method="POST" action="<?= url("departamentos/toggle/{$d->id}") ?>" style="display:inline"
                                                      data-confirm='<?= json_encode([
                                                          "title" => ($d->activo ? "¿Desactivar" : "¿Activar") . " departamento?",
                                                          "message" => ($d->activo
                                                              ? "El departamento <strong>" . htmlspecialchars($d->nombre, ENT_QUOTES) . "</strong> será desactivado."
                                                              : "El departamento <strong>" . htmlspecialchars($d->nombre, ENT_QUOTES) . "</strong> será activado nuevamente."),
                                                          "type" => "warning",
                                                          "confirmText" => $d->activo ? "Sí, desactivar" : "Sí, activar",
                                                          "icon" => $d->activo ? "bi-toggle-off" : "bi-toggle-on"
                                                      ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                                    <?= csrfField() ?>
                                                    <button type="submit" class="btn-action <?= $d->activo ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
                                                            title="<?= $d->activo ? 'Desactivar' : 'Activar' ?>">
                                                        <i class="bi <?= $d->activo ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if (hasPermission('departamentos.eliminar')): ?>
                                                <form method="POST" action="<?= url("departamentos/eliminar/{$d->id}") ?>" style="display:inline"
                                                      data-confirm='<?= json_encode([
                                                          "title" => "¿Eliminar departamento?",
                                                          "message" => "El departamento <strong>" . htmlspecialchars($d->nombre, ENT_QUOTES) . "</strong> será eliminado permanentemente.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
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
                                <?php endif; ?>
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
                    <a class="page-link" href="<?= url('departamentos?page='.($page-1).'&search='.urlencode($search)) ?>">Anterior</a>
                </li>
                <?php for($i = 1; $i <= $last_page; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('departamentos?page='.$i.'&search='.urlencode($search)) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $last_page ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('departamentos?page='.($page+1).'&search='.urlencode($search)) ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>
