<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Gestión de Productos</h5>
        <small class="text-muted"><?= $productos['total'] ?> productos encontrados</small>
    </div>
    <?php if (hasPermission('productos.crear')): ?>
    <div class="d-flex gap-2">
        <a href="<?= url('productos/importar') ?>" class="btn btn-outline-primary" id="btn-importar-csv">
            <i class="bi bi-file-earmark-arrow-up me-1"></i>Importar CSV
        </a>
        <a href="<?= url('productos/crear') ?>" class="btn btn-primary" id="btn-nuevo-producto">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('productos') ?>" class="filter-bar" id="filter-productos">
    <div class="form-group" style="flex:2">
        <label>Buscar</label>
        <input type="text" name="search" class="form-control" placeholder="Nombre, SKU o descripción..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="form-group">
        <label>Categoría</label>
        <select name="categoria" class="form-select">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat->id ?>" <?= $categoriaId == $cat->id ? 'selected' : '' ?>><?= htmlspecialchars($cat->nombre) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Stock</label>
        <select name="stock" class="form-select">
            <option value="">Todos</option>
            <option value="normal" <?= $stockFilter === 'normal' ? 'selected' : '' ?>>Normal</option>
            <option value="bajo" <?= $stockFilter === 'bajo' ? 'selected' : '' ?>>Bajo</option>
            <option value="agotado" <?= $stockFilter === 'agotado' ? 'selected' : '' ?>>Agotado</option>
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
        <?php if (empty($productos['data'])): ?>
            <div class="empty-state" style="padding: 3.5rem 1rem;">
                <div class="empty-state-icon" style="width:90px;height:90px;margin-bottom:1.5rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-search" style="font-size:2rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Sin resultados</h5>
                <p class="text-muted mb-3" style="max-width:320px;">No encontramos productos con esos criterios. Intente con otro filtro o agregue uno nuevo.</p>
                <?php if (hasPermission('productos.crear')): ?>
                <a href="<?= url('productos/crear') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabla-productos">
                <thead>
                    <tr>
                        <th style="width:60px"></th>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Tipo Logística</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos['data'] as $p): ?>
                    <tr class="<?= !$p->activo ? 'row-inactive' : '' ?>">
                        <td>
                            <img src="<?= productImage($p->imagen ?? null) ?>" 
                                 alt="<?= htmlspecialchars($p->nombre) ?>"
                                 class="product-thumb"
                                 style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                        </td>
                        <td><code class="text-primary fw-bold tabular-nums"><?= $p->sku ?></code></td>
                        <td>
                            <strong><?= htmlspecialchars($p->nombre) ?></strong>
                            <?php if ($p->descripcion): ?>
                            <br><small class="text-muted"><?= truncate(htmlspecialchars($p->descripcion), 60) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p->categoria_nombre ?? 'Sin categoría') ?></td>
                        <td class="fw-bold tabular-nums"><?= formatMoney($p->precio) ?></td>
                        <td class="tabular-nums"><strong><?= number_format($p->stock) ?></strong></td>
                        <td>
                            <?php if (!empty($p->es_perecedero)): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-box-seam me-1"></i>Lotes (WMS)</span>
                            <?php else: ?>
                                <small class="text-muted"><i class="bi bi-box me-1"></i>Estándar</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$p->activo): ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php elseif ($p->stock <= 0): ?>
                                <span class="badge badge-stock-out">Agotado</span>
                            <?php elseif ($p->stock <= $p->stock_minimo): ?>
                                <span class="badge badge-stock-low">Bajo</span>
                            <?php else: ?>
                                <span class="badge badge-stock-ok">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= url("productos/ver/{$p->id}") ?>" class="btn-action" title="Ver detalle" style="color:#6366f1">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <?php if (hasPermission('productos.editar')): ?>
                                <a href="<?= url("productos/editar/{$p->id}") ?>" class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('productos.editar')): ?>
                                <form method="POST" action="<?= url("productos/toggle/{$p->id}") ?>" 
                                      data-confirm='{"title":"¿<?= $p->activo ? 'Desactivar' : 'Activar' ?> producto?","message":"El estado del producto será modificado.","type":"warning","confirmText":"Proceder","icon":"bi-<?= $p->activo ? 'toggle-off' : 'toggle-on' ?>"}'
                                      style="display:inline">
                                    <?= csrfField() ?>
                                    <button type="submit" class="btn-action <?= $p->activo ? 'btn-delete' : 'btn-ok' ?>" title="<?= $p->activo ? 'Desactivar' : 'Activar' ?>" style="<?= !$p->activo ? 'color:#10b981; background:rgba(16,185,129,0.1);' : '' ?>">
                                        <i class="bi bi-<?= $p->activo ? 'toggle-off' : 'toggle-on' ?>"></i>
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
        <?php
            $pg = $productos;
            $baseUrl = 'productos?search=' . urlencode($search) . '&categoria=' . $categoriaId . '&stock=' . $stockFilter;
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
        <?php endif; ?>
    </div>
</div>
