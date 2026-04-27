<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Nueva Orden de Compra</h4>
        <span class="text-muted">Generar una nueva solicitud de abastecimiento a un proveedor</span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('compras') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="<?= url('compras/store') ?>" id="formOrdenCompra">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
    
    <div class="row g-4">
        <!-- Panel Izquierdo: Datos Generales -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-card-heading text-primary me-2"></i>Datos Generales</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label fw-semibold text-muted small">Proveedor <span class="text-danger">*</span></label>
                        <select name="proveedor_id" id="proveedor_id" class="form-select form-select-sm" required>
                            <option value="">— Seleccionar Proveedor —</option>
                            <?php foreach ($proveedores as $prov): ?>
                                <option value="<?= $prov->id ?>"><?= htmlspecialchars($prov->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_esperada" class="form-label fw-semibold text-muted small">Fecha Esperada de Entrega</label>
                        <input type="date" name="fecha_esperada" id="fecha_esperada" class="form-control form-control-sm" min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label fw-semibold text-muted small">Estado Inicial <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-select form-select-sm" required>
                            <option value="borrador">Borrador (editable después)</option>
                            <option value="pendiente">Pendiente (enviada al proveedor)</option>
                        </select>
                        <small class="text-muted" style="font-size:0.75rem">Nota: Las órdenes "Pendientes" bloquean edición.</small>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold text-muted small">Notas u Observaciones</label>
                        <textarea name="notas" id="notas" class="form-control form-control-sm" rows="3" placeholder="Instrucciones para el proveedor, lugar de entrega, etc."></textarea>
                    </div>
                </div>
            </div>

            <!-- Resumen Total -->
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-4 text-center">
                    <p class="mb-1 text-white-50 fw-semibold text-uppercase" style="letter-spacing:1px;font-size:0.8rem">Total de la Orden</p>
                    <h2 class="mb-0 fw-bold" id="total_orden_display">$ 0.00</h2>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Detalle de Productos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Productos a Pedir</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Fila
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabla-detalles">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 20%;">Precio Unit.</th>
                                    <th style="width: 15%;">Subtotal</th>
                                    <th class="text-end pe-4" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="detalles-body">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="empty-details-state" class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                        <p class="text-muted mb-0">Agregue productos a la orden usando el botón "Agregar Fila".</p>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btn-guardar">
                        <i class="bi bi-save me-2"></i>Guardar Orden
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Data oculta para JS -->
<script type="application/json" id="productos-data">
<?= json_encode($productos) ?>
</script>

<script src="<?= asset('js/compras.js') ?>?v=<?= ASSET_VERSION ?>"></script>
