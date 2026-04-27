<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Nueva Requisición</h4>
        <span class="text-muted">Crear una solicitud de salida de inventario</span>
    </div>
    <a href="<?= url('requisiciones') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<form method="POST" action="<?= url('requisiciones/store') ?>" id="form-requisicion">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
    
    <div class="row">
        <!-- Panel Izquierdo: Datos Generales -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Datos de la Solicitud</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="departamento_id" class="form-label fw-semibold text-muted small">Departamento Solicitante <span class="text-danger">*</span></label>
                        <select name="departamento_id" id="departamento_id" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= $d->id ?>"><?= htmlspecialchars($d->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_solicitud" class="form-label fw-semibold text-muted small">Fecha Solicitud</label>
                        <input type="date" name="fecha_solicitud" id="fecha_solicitud" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label fw-semibold text-muted small">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="borrador">Borrador (Solo guardar)</option>
                            <option value="pendiente">Pendiente (Lista para despachar)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold text-muted small">Notas o Motivo</label>
                        <textarea name="notas" id="notas" class="form-control" rows="3" placeholder="Ej: Materiales para mantenimiento preventivo del mes."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Productos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Productos Solicitados</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-row">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Producto
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabla-productos">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th style="width: 50%;">Producto</th>
                                    <th style="width: 25%;">Stock Disp.</th>
                                    <th style="width: 20%;">Cant. Solicitada</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Fila inicial -->
                                <tr>
                                    <td>
                                        <select name="producto_id[]" class="form-select select-producto" required>
                                            <option value="">— Seleccionar —</option>
                                            <?php foreach ($productos as $p): ?>
                                                <option value="<?= $p->id ?>" data-stock="<?= $p->stock ?>" data-unidad="<?= htmlspecialchars($p->unidad_medida) ?>">
                                                    <?= htmlspecialchars($p->nombre) ?> (SKU: <?= htmlspecialchars($p->sku) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="stock-disp text-muted fw-semibold">0</span> <small class="unidad-disp text-muted"></small>
                                    </td>
                                    <td>
                                        <input type="number" name="cantidad[]" class="form-control input-qty" min="1" required>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-check-lg me-1"></i>Guardar Requisición
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#tabla-productos tbody');
    const btnAdd = document.getElementById('btn-add-row');

    // Manejar cambio de producto (mostrar stock)
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-producto')) {
            const option = e.target.options[e.target.selectedIndex];
            const tr = e.target.closest('tr');
            const stockDisp = tr.querySelector('.stock-disp');
            const unidadDisp = tr.querySelector('.unidad-disp');
            const inputQty = tr.querySelector('.input-qty');

            if (option.value) {
                const stock = option.getAttribute('data-stock');
                stockDisp.textContent = stock;
                unidadDisp.textContent = option.getAttribute('data-unidad');
                inputQty.max = stock; // No dejar pedir más de lo que hay
            } else {
                stockDisp.textContent = '0';
                unidadDisp.textContent = '';
                inputQty.max = '';
            }
        }
    });

    // Validar cantidad máxima al escribir
    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-qty')) {
            const max = parseInt(e.target.max);
            const val = parseInt(e.target.value);
            if (val > max) {
                e.target.value = max;
                alert('La cantidad solicitada supera el stock disponible (' + max + ').');
            }
        }
    });

    // Agregar fila
    btnAdd.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        
        // Limpiar valores
        newRow.querySelector('.select-producto').value = '';
        newRow.querySelector('.input-qty').value = '';
        newRow.querySelector('.stock-disp').textContent = '0';
        newRow.querySelector('.unidad-disp').textContent = '';
        
        tableBody.appendChild(newRow);
    });

    // Eliminar fila
    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            if (tableBody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Debe haber al menos un producto en la requisición.');
            }
        }
    });
});
</script>
