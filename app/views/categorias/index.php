<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Gestión de Categorías</h5>
        <small class="text-muted">
            <?= $categorias['total'] ?> categorías en total · <?= $totalActivas ?> activas
        </small>
    </div>
    <?php if (hasPermission('categorias.crear')): ?>
    <a href="<?= url('categorias/crear') ?>" class="btn btn-primary" id="btn-nueva-categoria">
        <i class="bi bi-plus-lg me-1"></i>Nueva Categoría
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('categorias') ?>" class="filter-bar" id="filter-categorias">
    <div class="form-group" style="flex:2">
        <label>Buscar</label>
        <input type="text" name="search" class="form-control" placeholder="Nombre o descripción..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="form-group">
        <label>Estado</label>
        <select name="estado" class="form-select">
            <option value="">Todas</option>
            <option value="activa" <?= $status === 'activa' ? 'selected' : '' ?>>Activas</option>
            <option value="inactiva" <?= $status === 'inactiva' ? 'selected' : '' ?>>Inactivas</option>
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
        <?php if (empty($categorias['data'])): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-tags"></i>
                </div>
                <h5>No se encontraron categorías</h5>
                <p class="text-muted">Ajuste los filtros o cree una nueva categoría</p>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabla-categorias">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Creada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias['data'] as $cat): ?>
                    <tr>
                        <td><code class="text-primary fw-bold">#<?= $cat->id ?></code></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="categoria-icon">
                                    <i class="bi bi-tag-fill"></i>
                                </div>
                                <strong><?= htmlspecialchars($cat->nombre) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($cat->descripcion): ?>
                                <small class="text-muted"><?= truncate(htmlspecialchars($cat->descripcion), 80) ?></small>
                            <?php else: ?>
                                <small class="text-muted fst-italic">Sin descripción</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:0.85rem">
                                <?= (int) ($cat->productos_activos ?? 0) ?>
                            </span>
                            <?php if (($cat->total_productos ?? 0) > ($cat->productos_activos ?? 0)): ?>
                                <small class="text-muted ms-1">(<?= $cat->total_productos ?> total)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($cat->activa): ?>
                                <span class="badge badge-stock-ok">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-stock-out">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= formatDate($cat->created_at, 'd/m/Y') ?></small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if (hasPermission('categorias.editar')): ?>
                                <a href="<?= url("categorias/editar/{$cat->id}") ?>" class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Toggle activa/inactiva -->
                                <form method="POST" action="<?= url("categorias/toggle/{$cat->id}") ?>" style="display:inline"
                                      data-confirm='<?= json_encode([
                                          "title" => ($cat->activa ? "¿Desactivar" : "¿Activar") . " categoría?",
                                          "message" => ($cat->activa
                                              ? "La categoría <strong>" . htmlspecialchars($cat->nombre, ENT_QUOTES) . "</strong> será desactivada. Los productos asociados no serán afectados."
                                              : "La categoría <strong>" . htmlspecialchars($cat->nombre, ENT_QUOTES) . "</strong> será activada nuevamente."),
                                          "type" => "warning",
                                          "confirmText" => $cat->activa ? "Sí, desactivar" : "Sí, activar",
                                          "icon" => $cat->activa ? "bi-toggle-off" : "bi-toggle-on"
                                      ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn-action <?= $cat->activa ? 'btn-toggle-off' : 'btn-toggle-on' ?>" 
                                            title="<?= $cat->activa ? 'Desactivar' : 'Activar' ?>">
                                        <i class="bi <?= $cat->activa ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <?php if (hasPermission('categorias.eliminar')): ?>
                                <form method="POST" action="<?= url("categorias/eliminar/{$cat->id}") ?>" style="display:inline"
                                      data-confirm='<?= json_encode([
                                          "title" => "¿Eliminar categoría?",
                                          "message" => "La categoría <strong>" . htmlspecialchars($cat->nombre, ENT_QUOTES) . "</strong> será eliminada permanentemente.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
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
        <?php if ($categorias['pages'] > 1): ?>
        <div class="d-flex justify-content-between align-items-center px-3 py-3">
            <small class="text-muted">
                Mostrando <?= count($categorias['data']) ?> de <?= $categorias['total'] ?> registros
            </small>
            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $categorias['pages']; $i++): ?>
                    <li class="page-item <?= $i == $categorias['current'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url("categorias?page={$i}&search=" . urlencode($search) . "&estado={$status}") ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
