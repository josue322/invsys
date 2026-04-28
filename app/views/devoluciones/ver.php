<?php
/**
 * InvSys - Vista Detalle de Devolución
 */
$esPendiente = $devolucion->estado === 'pendiente';
$esAprobada = $devolucion->estado === 'aprobada';
$esRechazada = $devolucion->estado === 'rechazada';

$badgeColor = match($devolucion->estado) {
    'aprobada' => 'success',
    'rechazada' => 'danger',
    default => 'warning'
};
?>

<!-- Cabecera de Impresión (Oculta en pantalla) -->
<div class="d-none d-print-block mb-4">
    <div class="d-flex justify-content-between align-items-center border-bottom border-dark pb-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-0">InvSys WMS</h2>
            <p class="text-muted small mb-0">Comprobante de Devolución Interna</p>
        </div>
        <div class="text-end">
            <h3 class="h5 fw-bold mb-0">N° <?= htmlspecialchars($devolucion->numero_devolucion) ?></h3>
            <p class="text-muted small mb-0">Impreso: <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 d-print-none">
    <div>
        <h4 class="fw-bold mb-1">
            Devolución: <?= htmlspecialchars($devolucion->numero_devolucion) ?>
        </h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge bg-<?= $badgeColor ?> text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <?= $devolucion->estado ?>
            </span>
            <span class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>Solicitada: <?= formatDate($devolucion->fecha_solicitud, false) ?>
            </span>
            <?php if ($devolucion->fecha_procesamiento): ?>
                <span class="text-muted small ms-2">
                    <i class="bi bi-check2-circle me-1"></i>Procesada: <?= date('d/m/Y H:i', strtotime($devolucion->fecha_procesamiento)) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <button type="button" id="btnPrint" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <a href="<?= url('devoluciones') ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Panel de Información -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-building text-primary me-2"></i>Datos del Origen</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal small">Departamento</dt>
                    <dd class="col-sm-7 fw-semibold"><?= htmlspecialchars($devolucion->departamento_nombre) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal small">Registrado por</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($devolucion->usuario_nombre) ?></dd>

                    <?php if ($devolucion->numero_requisicion): ?>
                    <dt class="col-sm-5 text-muted fw-normal small">Req. Origen</dt>
                    <dd class="col-sm-7">
                        <a href="<?= url("requisiciones/show/{$devolucion->requisicion_id}") ?>" class="badge bg-light text-dark border text-decoration-none">
                            <?= htmlspecialchars($devolucion->numero_requisicion) ?>
                        </a>
                    </dd>
                    <?php endif; ?>
                </dl>
                <?php if (!empty($devolucion->notas)): ?>
                    <hr class="my-3">
                    <p class="small text-muted mb-1 fw-semibold">Notas / Motivo General:</p>
                    <p class="small mb-0 p-2 bg-light rounded border border-light-subtle"><?= nl2br(htmlspecialchars($devolucion->notas)) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($esPendiente && hasPermission('devoluciones.aprobar')): ?>
            <div class="card border-warning shadow-sm border-opacity-50 d-print-none">
                <div class="card-body p-3">
                    <h6 class="text-warning-emphasis fw-bold"><i class="bi bi-shield-check me-2"></i>Gestión de Devolución</h6>
                    <p class="small text-muted mb-3">Revise el estado físico reportado antes de aprobar. El stock de los productos "Buenos" aumentará de inmediato.</p>
                    
                    <form method="POST" action="<?= url("devoluciones/aprobar/{$devolucion->id}") ?>" class="mb-2" data-native-confirm="¿Está seguro de APROBAR esta devolución? El stock aumentará automáticamente.">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-success btn-sm w-100 shadow-sm fw-medium">
                            <i class="bi bi-check-circle me-1"></i> Aprobar y Reingresar Stock
                        </button>
                    </form>

                    <form method="POST" action="<?= url("devoluciones/rechazar/{$devolucion->id}") ?>" data-native-confirm="¿Está seguro de RECHAZAR esta devolución? No se registrará ningún reingreso.">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-x-circle me-1"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Detalles de Productos -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Productos Devueltos</h6>
                <span class="badge bg-secondary rounded-pill d-print-none"><?= count($detalles) ?> items</span>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4" style="width: 40%">Producto</th>
                                <th class="text-center" style="width: 15%">Cantidad</th>
                                <th style="width: 20%">Estado Físico</th>
                                <th style="width: 25%">Motivo Específico</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $det): 
                                $esMalo = $det->estado_producto === 'dañado';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($det->producto_nombre) ?></div>
                                    <div class="text-muted small font-monospace">SKU: <?= $det->producto_sku ?></div>
                                    <?php if ($det->numero_lote): ?>
                                        <span class="badge border text-body-secondary mt-1">Lote: <?= htmlspecialchars($det->numero_lote) ?></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center fw-bold">
                                    <?= $det->cantidad ?>
                                </td>
                                
                                <td>
                                    <?php if ($esMalo): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Dañado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Bueno</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="text-muted small"><?= htmlspecialchars($det->motivo) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if ($esPendiente): ?>
            <div class="card-footer bg-light border-top-0 py-3 d-print-none">
                <div class="d-flex align-items-center text-muted small">
                    <i class="bi bi-info-circle-fill text-warning me-2 fs-5"></i>
                    <span><strong>Nota contable:</strong> Los productos marcados como "Dañados" requerirán un Ajuste de Salida manual posterior a su aprobación para cuadrar el inventario físico.</span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Firmas para impresión -->
        <div class="row mt-5 pt-5 d-none d-print-flex text-center">
            <div class="col-6">
                <div class="border-top border-dark mx-4 pt-2">
                    <p class="fw-bold mb-0">Entregué Conforme</p>
                    <p class="small text-muted"><?= htmlspecialchars($devolucion->departamento_nombre) ?></p>
                </div>
            </div>
            <div class="col-6">
                <div class="border-top border-dark mx-4 pt-2">
                    <p class="fw-bold mb-0">Recibí Conforme</p>
                    <p class="small text-muted">Almacén Central</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: #fff !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge { border: 1px solid #6c757d !important; color: #000 !important; }
    .bg-danger-subtle, .bg-success-subtle, .bg-secondary, .bg-warning, .bg-success, .bg-danger {
        background-color: transparent !important;
        color: #000 !important;
    }
}
</style>
