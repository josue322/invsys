<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0">Ubicaciones de Almacén</h5>
        <small class="text-muted"><?= $total ?> ubicaciones registradas</small>
    </div>
    <?php if (hasPermission('ubicaciones.crear')): ?>
    <a href="<?= url('ubicaciones/crear') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nueva Ubicación
    </a>
    <?php endif; ?>
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
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ubicaciones as $ub): ?>
                        <tr>
                            <td class="fw-bold"><i class="bi bi-geo text-muted me-2"></i><?= htmlspecialchars($ub->nombre) ?></td>
                            <td><?= htmlspecialchars($ub->descripcion ?? '-') ?></td>
                            <td>
                                <?php if ($ub->activa): ?>
                                    <span class="badge badge-stock-ok">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-stock-out">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (hasPermission('ubicaciones.editar')): ?>
                                    <a href="<?= url("ubicaciones/editar/{$ub->id}") ?>" class="btn-action btn-edit" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <!-- Toggle activa/inactiva -->
                                    <form method="POST" action="<?= url("ubicaciones/toggle/{$ub->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => ($ub->activa ? "¿Desactivar" : "¿Activar") . " ubicación?",
                                              "message" => ($ub->activa
                                                  ? "La ubicación <strong>" . htmlspecialchars($ub->nombre, ENT_QUOTES) . "</strong> será desactivada."
                                                  : "La ubicación <strong>" . htmlspecialchars($ub->nombre, ENT_QUOTES) . "</strong> será activada nuevamente."),
                                              "type" => "warning",
                                              "confirmText" => $ub->activa ? "Sí, desactivar" : "Sí, activar",
                                              "icon" => $ub->activa ? "bi-toggle-off" : "bi-toggle-on"
                                          ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action <?= $ub->activa ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
                                                title="<?= $ub->activa ? 'Desactivar' : 'Activar' ?>">
                                            <i class="bi <?= $ub->activa ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if (hasPermission('ubicaciones.eliminar')): ?>
                                    <form method="POST" action="<?= url("ubicaciones/eliminar/{$ub->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => "¿Eliminar ubicación?",
                                              "message" => "La ubicación <strong>" . htmlspecialchars($ub->nombre, ENT_QUOTES) . "</strong> será desactivada.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
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
                Mostrando <?= count($ubicaciones) ?> de <?= $total ?> registros
            </small>
            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url("ubicaciones?page={$i}&search=" . urlencode($search)) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
