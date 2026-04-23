<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card animate-fadeIn">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-800 mb-0"><i class="bi bi-arrow-left-right me-2"></i>Registrar Movimiento</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('movimientos/crear') ?>" id="formCrearMovimiento">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="producto_id" class="form-label">Producto *</label>
                            <select class="form-select" id="producto_id" name="producto_id" required>
                                <option value="">Seleccione un producto...</option>
                                <?php foreach ($productos as $p): ?>
                                <option value="<?= $p->id ?>" data-stock="<?= $p->stock ?>" data-perecedero="<?= $p->es_perecedero ? '1' : '0' ?>">
                                    <?= htmlspecialchars($p->nombre) ?> (<?= $p->sku ?>) — Stock: <?= $p->stock ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select fw-bold text-primary" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="entrada">📥 Entrada</option>
                                <option value="salida">📤 Salida</option>
                                <option value="ajuste">🔧 Ajuste</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Stock Actual</label>
                            <input type="text" class="form-control bg-light" id="stockActual" disabled value="-">
                        </div>

                        <!-- Campos Dinámicos -->
                        <div class="col-md-6 d-none animate-fadeIn" id="proveedorWrapper">
                            <label for="proveedor_id" class="form-label">Proveedor</label>
                            <select class="form-select" id="proveedor_id" name="proveedor_id">
                                <option value="">Seleccione proveedor...</option>
                                <?php foreach ($proveedores as $prov): ?>
                                <option value="<?= $prov->id ?>"><?= htmlspecialchars($prov->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 d-none animate-fadeIn" id="destinoWrapper">
                            <label for="destino" class="form-label">Destino (Área/Cliente)</label>
                            <input type="text" class="form-control" id="destino" name="destino" placeholder="Ej: Producción, Cliente A">
                        </div>

                        <div class="col-md-6" id="referenciaWrapper">
                            <label for="referencia" class="form-label">Referencia Documental</label>
                            <input type="text" class="form-control" id="referencia" name="referencia" placeholder="OC-2025-XXX / REQ-123">
                        </div>

                        <!-- Panel de Lotes -->
                        <div class="col-12 d-none animate-fadeIn" id="lotesWrapper">
                            <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 shadow-sm mt-1">
                                <div class="card-body py-3">
                                    <h6 class="card-title fw-bold text-primary mb-3"><i class="bi bi-box-seam me-2"></i>Gestión de Lotes de Vencimiento</h6>
                                    
                                    <div id="lotesEntradaUI" class="d-none">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-primary">Número de Lote *</label>
                                                <input type="text" class="form-control border-primary" id="numero_lote" name="numero_lote" placeholder="Identificador del lote">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-primary">Fecha de Vencimiento *</label>
                                                <input type="date" class="form-control border-primary" id="fecha_vencimiento" name="fecha_vencimiento">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="lotesSalidaUI" class="d-none">
                                        <div class="alert alert-primary bg-white mb-0 py-2">
                                            <i class="bi bi-info-circle me-2"></i>El sistema descontará las unidades automáticamente de los lotes más próximos a expirar (FEFO).
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('movimientos') ?>" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="btnRegistrarMovimiento">Registrar Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('producto_id');
    const tipoSelect = document.getElementById('tipo');
    
    // UI Elements
    const stockActual = document.getElementById('stockActual');
    const proveedorWrapper = document.getElementById('proveedorWrapper');
    const destinoWrapper = document.getElementById('destinoWrapper');
    const lotesWrapper = document.getElementById('lotesWrapper');
    const lotesEntradaUI = document.getElementById('lotesEntradaUI');
    const lotesSalidaUI = document.getElementById('lotesSalidaUI');
    
    // Inputs Lote
    const inputLote = document.getElementById('numero_lote');
    const inputVencimiento = document.getElementById('fecha_vencimiento');

    function updateFormUI() {
        const tipo = tipoSelect.value;
        const option = productoSelect.options[productoSelect.selectedIndex];
        
        const isPerecedero = option && option.dataset.perecedero === '1';
        
        // Stock Update
        stockActual.value = option ? (option.dataset.stock ?? '-') : '-';

        // Reset display
        proveedorWrapper.classList.add('d-none');
        destinoWrapper.classList.add('d-none');
        lotesWrapper.classList.add('d-none');
        lotesEntradaUI.classList.add('d-none');
        lotesSalidaUI.classList.add('d-none');
        
        inputLote.required = false;
        inputVencimiento.required = false;

        // Contextual UI
        if (tipo === 'entrada') {
            proveedorWrapper.classList.remove('d-none');
            
            if (isPerecedero) {
                lotesWrapper.classList.remove('d-none');
                lotesEntradaUI.classList.remove('d-none');
                inputLote.required = true;
                inputVencimiento.required = true;
            }
        } 
        else if (tipo === 'salida') {
            destinoWrapper.classList.remove('d-none');
            
            if (isPerecedero) {
                lotesWrapper.classList.remove('d-none');
                lotesSalidaUI.classList.remove('d-none');
            }
        }
    }

    productoSelect.addEventListener('change', updateFormUI);
    tipoSelect.addEventListener('change', updateFormUI);

    // Initial load
    updateFormUI();

    // === Form Validation ===
    FormValidator.init('#formCrearMovimiento', {
        producto_id: { required: true, messages: { required: 'Seleccione un producto' } },
        tipo:        { required: true, messages: { required: 'Seleccione el tipo de movimiento' } },
        cantidad:    {
            required: true,
            min: 1,
            messages: { required: 'La cantidad es obligatoria' },
            custom(value, field) {
                const tipo = document.getElementById('tipo').value;
                const option = productoSelect.options[productoSelect.selectedIndex];
                const stock = parseInt(option?.dataset?.stock ?? 0);

                if (tipo === 'salida' && parseInt(value) > stock) {
                    return `La cantidad (${value}) supera el stock actual (${stock})`;
                }
                return true;
            }
        }
    });
});
</script>
