<?php
/**
 * InvSys - Vista Crear Devolución
 */
?>
<div class="d-flex align-items-center mb-4">
    <a href="<?= url('devoluciones') ?>" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <div>
        <h2 class="h3 mb-0 text-body"><i class="bi bi-plus-circle me-2" style="color: var(--primary);"></i>Nueva Devolución</h2>
        <p class="text-muted mb-0">Registrar retorno de inventario desde un departamento</p>
    </div>
</div>

<form method="POST" action="<?= url('devoluciones/crear') ?>" id="formDevolucion">
    <?= csrfField() ?>
    
    <div class="row g-4">
        <!-- Columna Izquierda: Datos Generales -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2">
                    <h6 class="mb-0 fw-bold">Datos Generales</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Departamento <span class="text-danger">*</span></label>
                        <select name="departamento_id" class="form-select" required>
                            <option value="">Seleccione un departamento</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?= $dep->id ?>"><?= htmlspecialchars($dep->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Requisición Origen (Opcional)</label>
                        <select name="requisicion_id" class="form-select">
                            <option value="">Ninguna - Retorno libre</option>
                            <?php foreach ($requisiciones as $req): ?>
                                <option value="<?= $req->id ?>"><?= htmlspecialchars($req->numero_requisicion) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Si la devolución corresponde a un despacho previo.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Notas Adicionales</label>
                        <textarea name="notas" class="form-control" rows="3" placeholder="Motivo general de la devolución..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body bg-light rounded text-center p-4">
                    <i class="bi bi-info-circle text-primary mb-2" style="font-size: 2rem;"></i>
                    <h6 class="fw-bold">Proceso de Retorno</h6>
                    <p class="small text-muted mb-0">
                        Los productos devueltos en estado "Bueno" sumarán stock inmediatamente tras la aprobación. Si el estado es "Dañado", ingresarán al sistema pero deberán ajustarse manualmente como salida.
                    </p>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Productos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h6 class="mb-0 fw-bold">Productos a Devolver</h6>
                </div>
                <div class="card-body p-0">
                    <!-- Buscador de productos -->
                    <div class="p-3 border-bottom bg-light">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="buscadorProductos" class="form-control ps-5" placeholder="Escriba el nombre o SKU del producto para agregarlo a la lista...">
                        </div>
                        <ul id="resultadosBusqueda" class="list-group position-absolute w-100 shadow" style="z-index: 1000; display: none; max-height: 250px; overflow-y: auto;">
                            <!-- Resultados inyectados por JS -->
                        </ul>
                    </div>

                    <!-- Lista de detalles -->
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle" id="tablaDetalles">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%">Producto</th>
                                    <th style="width: 15%">Cantidad</th>
                                    <th style="width: 25%">Estado</th>
                                    <th style="width: 20%">Motivo</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas inyectadas por JS -->
                                <tr id="filaVacia">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-cart-x fs-2 d-block mb-2"></i>
                                        No se han agregado productos a la devolución.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top p-3 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardar">
                        <i class="bi bi-save me-2"></i>Registrar Devolución
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Data bridge de productos para evitar CSP inline script blocking -->
<div id="productosDataBridge" class="d-none" data-productos="<?= htmlspecialchars(json_encode(array_map(function($p) {
    return [
        'id' => $p->id,
        'nombre' => $p->nombre,
        'sku' => $p->sku,
        'stock' => $p->stock
    ];
}, $productos)), ENT_QUOTES, 'UTF-8') ?>"></div>
