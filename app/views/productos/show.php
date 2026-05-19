<!-- Product Detail Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="<?= url('productos') ?>" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Volver a Productos
        </a>
        <h5 class="fw-800 mb-0"><?= htmlspecialchars($producto->nombre) ?></h5>
        <small class="text-muted">SKU: <?= htmlspecialchars($producto->sku) ?></small>
    </div>
    <div class="d-flex gap-2">
        <?php if (hasPermission('productos.editar')): ?>
            <a href="<?= url("productos/editar/{$producto->id}") ?>" class="btn btn-primary" id="btn-editar-producto">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">
    <!-- Columna Izquierda: Info del Producto -->
    <div class="col-lg-5">
        <!-- Imagen -->
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <img src="<?= productImage($producto->imagen ?? null) ?>"
                    alt="<?= htmlspecialchars($producto->nombre) ?>" class="rounded"
                    style="max-width: 200px; max-height: 200px; object-fit: cover;">
            </div>
        </div>

        <!-- Código de Barras -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-upc me-2"></i>Código de Barras</h6>
                <button class="btn btn-sm btn-outline-primary" id="btnPrintBarcode">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
            </div>
            <div class="card-body text-center">
                <svg id="barcode"></svg>
                <div class="mt-2">
                    <strong><?= htmlspecialchars(!empty($producto->codigo_barras) ? $producto->codigo_barras : $producto->sku) ?></strong>
                    <br><small class="text-muted"><?= htmlspecialchars($producto->nombre) ?></small>
                </div>
            </div>
        </div>

        <!-- Datos del Producto -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Información General</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:40%">Nombre</td>
                            <td><strong><?= htmlspecialchars($producto->nombre) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">SKU</td>
                            <td><code><?= htmlspecialchars($producto->sku) ?></code></td>
                        </tr>
                        <?php if (!empty($producto->codigo_barras)): ?>
                            <tr>
                                <td class="text-muted"><i class="bi bi-upc-scan me-1"></i>Cód. Barras</td>
                                <td><code><?= htmlspecialchars($producto->codigo_barras) ?></code></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Categoría</td>
                            <td><?= htmlspecialchars($producto->categoria_nombre ?? 'Sin categoría') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Costo Unitario</td>
                            <td><strong class="text-success"><?= formatMoney($producto->costo) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Descripción</td>
                            <td><?= $producto->descripcion ? htmlspecialchars($producto->descripcion) : '<span class="text-muted">—</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado</td>
                            <td><small><?= formatDate($producto->created_at) ?></small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-boxes me-2"></i>Stock</h6>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-4">
                        <div
                            class="fs-3 fw-800 <?= $producto->stock <= 0 ? 'text-danger' : ($producto->stock <= $producto->stock_minimo ? 'text-warning' : 'text-success') ?>">
                            <?= $producto->stock ?>
                        </div>
                        <small class="text-muted">Stock Actual</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-3 fw-800"><?= $producto->stock_minimo ?></div>
                        <small class="text-muted">Stock Mínimo</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-3 fw-800"><?= formatMoney($producto->costo * $producto->stock) ?></div>
                        <small class="text-muted">Valor Total</small>
                    </div>
                </div>

                <?php
                $stockPercentage = $producto->stock_minimo > 0
                    ? min(100, ($producto->stock / ($producto->stock_minimo * 3)) * 100)
                    : 100;
                $barClass = $producto->stock <= 0 ? 'bg-danger' : ($producto->stock <= $producto->stock_minimo ? 'bg-warning' : 'bg-success');
                ?>
                <div class="progress mt-3" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar <?= $barClass ?>"
                        style="width: <?= $stockPercentage ?>%; border-radius: 4px;"></div>
                </div>
                <div class="mt-2">
                    <?php if ($producto->stock <= 0): ?>
                        <span class="badge badge-stock-out">Agotado</span>
                    <?php elseif ($producto->stock <= $producto->stock_minimo): ?>
                        <span class="badge badge-stock-low">Stock Bajo</span>
                    <?php else: ?>
                        <span class="badge badge-stock-ok">Normal</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Columna Derecha: Historial de Movimientos -->
    <div class="col-lg-7">
        <!-- Vencimiento (si aplica) -->
        <?php if (!empty($producto->es_perecedero)): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Gestión de Lotes</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($lotes)): ?>
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-info-circle mb-2 d-block fs-4"></i>
                            No hay lotes registrados para este producto.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Lote</th>
                                        <th>Vencimiento</th>
                                        <th>Stock Actual</th>
                                        <th>Estado</th>
                                        <?php if (hasPermission('productos.editar')): ?>
                                            <th class="text-end pe-3">Acciones</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lotes as $lote): ?>
                                        <tr>
                                            <td class="ps-3 fw-medium">#<?= htmlspecialchars($lote->numero_lote) ?></td>
                                            <td>
                                                <?php if ($lote->fecha_vencimiento): ?>
                                                    <?php
                                                    $isExpired = strtotime($lote->fecha_vencimiento) < time();
                                                    $isExpiring = strtotime($lote->fecha_vencimiento) < strtotime('+30 days');
                                                    $badgeClass = $isExpired ? 'bg-danger' : ($isExpiring ? 'bg-warning text-dark' : 'bg-success bg-opacity-10 text-success');
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <i
                                                            class="bi bi-calendar-event me-1"></i><?= formatDate($lote->fecha_vencimiento) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?= $lote->stock_actual ?></span>
                                            </td>
                                            <td>
                                                <?php if ($lote->estado === 'disponible'): ?>
                                                    <span class="text-success"><i
                                                            class="bi bi-check-circle-fill me-1"></i>Disponible</span>
                                                <?php elseif ($lote->estado === 'agotado'): ?>
                                                    <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>
                                                <?php else: ?>
                                                    <span class="text-secondary"><?= ucfirst($lote->estado) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (hasPermission('productos.editar')): ?>
                                                <td class="text-end pe-3">
                                                    <button class="btn-action btn-edit btn-sm btn-editar-lote"
                                                        title="Editar Vencimiento" data-lote-id="<?= $lote->id ?>"
                                                        data-lote-numero="<?= htmlspecialchars($lote->numero_lote) ?>"
                                                        data-lote-fecha="<?= $lote->fecha_vencimiento ?>">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modal Editar Lote -->
            <div class="modal fade" id="modalEditarLote" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold"><i class="bi bi-calendar-check me-2 text-primary"></i>Editar
                                Vencimiento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="<?= url('productos/updateLote') ?>">
                            <div class="modal-body">
                                <?= csrfField() ?>
                                <input type="hidden" name="producto_id" value="<?= $producto->id ?>">
                                <input type="hidden" name="lote_id" id="edit_lote_id">

                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Número de Lote</label>
                                    <div class="fw-bold fs-6" id="edit_lote_numero">LOTE-000</div>
                                </div>
                                <div class="mb-2">
                                    <label for="edit_fecha_vencimiento" class="form-label">Nueva Fecha de Vencimiento
                                        *</label>
                                    <input type="date" class="form-control" id="edit_fecha_vencimiento"
                                        name="fecha_vencimiento" required>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary px-4">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Números de Serie (si aplica) -->
        <?php if (!empty($producto->requiere_serie)): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-upc me-2 text-info"></i>Números de Serie</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($seriales)): ?>
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-upc-scan mb-2 d-block fs-4 text-secondary"></i>
                            No hay números de serie registrados.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase sticky-top">
                                    <tr>
                                        <th class="ps-3">Serial</th>
                                        <th>Estado</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seriales as $serie): ?>
                                        <tr>
                                            <td class="ps-3"><code
                                                    class="fw-bold fs-6"><?= htmlspecialchars($serie->numero_serie) ?></code></td>
                                            <td>
                                                <?php
                                                $badgeClass = match ($serie->estado) {
                                                    'disponible' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                                    'asignado' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                                                    'en_reparacion' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                                    'dado_de_baja' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                                    default => 'bg-light text-dark'
                                                };
                                                $iconClass = match ($serie->estado) {
                                                    'disponible' => 'bi-check-circle-fill',
                                                    'asignado' => 'bi-arrow-right-circle-fill',
                                                    'en_reparacion' => 'bi-tools',
                                                    'dado_de_baja' => 'bi-x-circle-fill',
                                                    default => 'bi-circle'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><i
                                                        class="bi <?= $iconClass ?> me-1"></i><?= ucfirst(str_replace('_', ' ', $serie->estado)) ?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted d-block" title="<?= formatDate($serie->fecha_entrada) ?>">
                                                    <?= $serie->entrada_ref ?: 'Sin ref.' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($serie->estado === 'asignado' && $serie->movimiento_salida_id): ?>
                                                    <small class="text-muted d-block" title="<?= formatDate($serie->fecha_salida) ?>">
                                                        <?= $serie->salida_ref ?: 'Sin ref.' ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
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
        <?php endif; ?>

        <!-- Proveedores Vinculados -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-truck me-2"></i>Proveedores</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary" id="proveedoresCount"><?= count($proveedores ?? []) ?></span>
                    <?php if (hasPermission('productos.editar')): ?>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#modalVincularProveedor" id="btnAbrirModalProv">
                            <i class="bi bi-plus-lg me-1"></i>Vincular
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0" id="proveedoresBody">
                <?php if (empty($proveedores)): ?>
                    <div class="text-center py-4" id="proveedoresEmpty">
                        <i class="bi bi-truck text-muted" style="font-size:2rem"></i>
                        <p class="text-muted mb-0 mt-2"><small>Sin proveedores vinculados.<br>Use el botón "Vincular" para
                                agregar uno.</small></p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table table-sm mb-0" id="tablaProveedores">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Cód. Prov.</th>
                                    <th>Costo</th>
                                    <th>Entrega</th>
                                    <?php if (hasPermission('productos.editar')): ?>
                                        <th style="width:60px"></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proveedores as $prov): ?>
                                    <tr id="vinculo-<?= $prov->id ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($prov->proveedor_nombre) ?></strong>
                                            <?php if ($prov->es_preferido): ?>
                                                <span class="badge text-bg-warning ms-1" style="font-size:0.65rem"><i
                                                        class="bi bi-star-fill me-1"></i>Preferido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small
                                                class="text-muted"><?= htmlspecialchars($prov->codigo_proveedor ?? '—') ?></small>
                                        </td>
                                        <td><?= $prov->costo ? formatMoney($prov->costo) : '—' ?></td>
                                        <td><?= $prov->tiempo_entrega_dias ? $prov->tiempo_entrega_dias . ' días' : '—' ?></td>
                                        <?php if (hasPermission('productos.editar')): ?>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger btn-desvincular"
                                                    data-vinculo-id="<?= $prov->id ?>" title="Desvincular">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Vincular Proveedor -->
        <?php if (hasPermission('productos.editar')): ?>
            <div class="modal fade" id="modalVincularProveedor" tabindex="-1" aria-labelledby="modalVincularLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold" id="modalVincularLabel"><i
                                    class="bi bi-link-45deg me-2"></i>Vincular Proveedor</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formVincularProveedor">
                                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="producto_id" value="<?= $producto->id ?>">

                                <div class="mb-3">
                                    <label for="prov_proveedor_id" class="form-label">Proveedor *</label>
                                    <select class="form-select" id="prov_proveedor_id" name="proveedor_id" required>
                                        <option value="">— Seleccionar proveedor —</option>
                                        <?php foreach ($todosProveedores as $tp): ?>
                                            <option value="<?= $tp->id ?>"><?= htmlspecialchars($tp->nombre) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="prov_codigo" class="form-label">Código del Proveedor</label>
                                        <input type="text" class="form-control" id="prov_codigo" name="codigo_proveedor"
                                            placeholder="Ej: HP-PB450" maxlength="50">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prov_precio" class="form-label">Costo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="prov_precio" name="costo"
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prov_entrega" class="form-label">Tiempo de Entrega</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="prov_entrega"
                                                name="tiempo_entrega_dias" min="0">
                                            <span class="input-group-text">días</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="prov_preferido"
                                                name="es_preferido" value="1">
                                            <label class="form-check-label" for="prov_preferido"><i
                                                    class="bi bi-star-fill text-warning me-1"></i>Proveedor
                                                Preferido</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="prov_notas" class="form-label">Notas</label>
                                        <textarea class="form-control" id="prov_notas" name="notas" rows="2"
                                            placeholder="Observaciones opcionales..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnVincularProveedor">
                                <i class="bi bi-link-45deg me-1"></i>Vincular Proveedor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de Movimientos</h6>
                <span class="badge bg-primary"><?= count($movimientos) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($movimientos)): ?>
                    <div class="empty-state py-5">
                        <i class="bi bi-inbox"></i>
                        <h6>Sin movimientos</h6>
                        <p class="text-muted mb-0">Este producto aún no tiene movimientos registrados.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table mb-0" id="tabla-historial">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Observaciones</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos as $m): ?>
                                    <tr>
                                        <td><small><?= formatDate($m->created_at) ?></small></td>
                                        <td>
                                            <?php
                                            $tipoClass = match ($m->tipo) {
                                                'entrada' => 'badge-stock-ok',
                                                'salida' => 'badge-stock-out',
                                                'ajuste' => 'bg-info',
                                                default => 'bg-secondary',
                                            };
                                            $tipoIcon = match ($m->tipo) {
                                                'entrada' => 'bi-arrow-down-circle',
                                                'salida' => 'bi-arrow-up-circle',
                                                'ajuste' => 'bi-gear',
                                                default => 'bi-circle',
                                            };
                                            ?>
                                            <span class="badge <?= $tipoClass ?>">
                                                <i class="bi <?= $tipoIcon ?> me-1"></i><?= ucfirst($m->tipo) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong
                                                class="<?= $m->tipo === 'entrada' ? 'text-success' : ($m->tipo === 'salida' ? 'text-danger' : '') ?>">
                                                <?= $m->tipo === 'entrada' ? '+' : ($m->tipo === 'salida' ? '-' : '') ?>
                                                <?= $m->cantidad ?>
                                            </strong>
                                        </td>
                                        <td><small
                                                class="text-muted"><?= htmlspecialchars(truncate($m->observaciones ?? '—', 40)) ?></small>
                                        </td>
                                        <td><small><?= htmlspecialchars($m->usuario_nombre) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Historial de Costos -->
<?php if (!empty($costoHistorial)): ?>
    <div class="row g-3 mt-1">
        <!-- Gráfico de evolución de costo -->
        <?php if (count($costoChartData) >= 2): ?>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Evolución del Costo</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="chartCosto" height="220"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabla de cambios de costo -->
        <div class="col-lg-<?= count($costoChartData) >= 2 ? '7' : '12' ?>">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-currency-dollar me-2"></i>Historial de Costos</h6>
                    <span class="badge bg-primary"><?= count($costoHistorial) ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-wrapper">
                        <table class="table mb-0" id="tabla-costos">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Anterior</th>
                                    <th>Nuevo</th>
                                    <th>Cambio</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($costoHistorial as $ph):
                                    $diff = $ph->costo_nuevo - $ph->costo_anterior;
                                    $pct = $ph->costo_anterior > 0
                                        ? round(($diff / $ph->costo_anterior) * 100, 1)
                                        : 0;
                                    ?>
                                    <tr>
                                        <td><small><?= formatDate($ph->created_at) ?></small></td>
                                        <td><small class="text-muted"><?= formatMoney($ph->costo_anterior) ?></small></td>
                                        <td><strong><?= formatMoney($ph->costo_nuevo) ?></strong></td>
                                        <td>
                                            <?php if ($diff > 0): ?>
                                                <span class="badge" style="background:rgba(239,68,68,0.1);color:#dc2626;">
                                                    <i class="bi bi-arrow-up me-1"></i>+<?= formatMoney($diff) ?> (<?= $pct ?>%)
                                                </span>
                                            <?php elseif ($diff < 0): ?>
                                                <span class="badge" style="background:rgba(16,185,129,0.1);color:#059669;">
                                                    <i class="bi bi-arrow-down me-1"></i><?= formatMoney($diff) ?> (<?= $pct ?>%)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Sin cambio</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?= htmlspecialchars($ph->usuario_nombre ?? '—') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script id="page-data" type="application/json"><?= json_encode([
    'sku' => $producto->sku,
    'codigo_barras' => $producto->codigo_barras ?? null,
    'nombre' => $producto->nombre,
    'productoId' => $producto->id,
    'csrfToken' => $csrfToken ?? '',
    'baseUrl' => rtrim(BASE_URL, '/'),
    'costoChart' => $costoChartData ?? [],
    'monedaSimbolo' => Config::get('moneda_simbolo', '$'),
]) ?></script>
<script src="<?= asset('js/productos.js') ?>?v=<?= ASSET_VERSION ?>"></script>