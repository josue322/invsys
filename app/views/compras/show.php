<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 print-hide">
    <div>
        <h4 class="fw-bold mb-1">Orden de Compra: <?= htmlspecialchars($orden->numero_orden) ?></h4>
        <span class="text-muted">Detalles y recepción de mercancía</span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('compras') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Main content -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm print-card">
            <div class="card-body p-4 p-md-5">
                <!-- Encabezado de OC (tipo factura) -->
                <div class="row mb-5 pb-4 border-bottom">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <h2 class="fw-bolder text-primary mb-1">ÓRDEN DE COMPRA</h2>
                        <p class="text-muted mb-0"># <?= htmlspecialchars($orden->numero_orden) ?></p>

                        <?php
                        $badge = match ($orden->estado) {
                            'borrador' => 'bg-secondary',
                            'pendiente' => 'bg-warning text-dark',
                            'recibida' => 'bg-success',
                            'cancelada' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        ?>
                        <span class="badge <?= $badge ?> text-uppercase mt-3 px-3 py-2" style="letter-spacing: 1px;">
                            Estado: <?= $orden->estado ?>
                        </span>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h6 class="fw-bold text-muted text-uppercase small mb-2">Detalles de Emisión</h6>
                        <table class="table table-sm table-borderless m-0 float-sm-end" style="width: auto;">
                            <tr>
                                <th class="text-muted pe-3 text-start fw-normal">Fecha:</th>
                                <td class="text-end fw-medium"><?= formatDate($orden->fecha_emision) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted pe-3 text-start fw-normal">Entrega:</th>
                                <td class="text-end fw-medium">
                                    <?= $orden->fecha_esperada ? formatDate($orden->fecha_esperada) : 'No definida' ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted pe-3 text-start fw-normal">Emitida por:</th>
                                <td class="text-end fw-medium"><?= htmlspecialchars($orden->usuario_nombre) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Info Proveedor -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Proveedor</h6>
                        <div class="bg-light p-4 rounded-3">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($orden->proveedor_nombre) ?></h5>
                            <p class="text-muted mb-0">RUC/NIT: <?= htmlspecialchars($orden->proveedor_documento) ?></p>
                            <?php if ($orden->proveedor_email): ?>
                                <p class="text-muted mb-0 mt-1"><i
                                        class="bi bi-envelope me-2"></i><?= htmlspecialchars($orden->proveedor_email) ?></p>
                            <?php endif; ?>
                            <?php if ($orden->proveedor_telefono): ?>
                                <p class="text-muted mb-0 mt-1"><i
                                        class="bi bi-telephone me-2"></i><?= htmlspecialchars($orden->proveedor_telefono) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <h6 class="fw-bold text-muted text-uppercase small mb-3">Detalle de Productos</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">SKU</th>
                                <th>Producto</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio U.</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orden->detalles as $det): ?>
                                <tr>
                                    <td class="ps-3"><code class="text-primary"><?= $det->sku ?></code></td>
                                    <td>
                                        <span
                                            class="fw-medium d-block"><?= htmlspecialchars($det->producto_nombre) ?></span>
                                        <?php if ($det->es_perecedero): ?>
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle mt-1"
                                                style="font-size:0.65rem">Perecedero</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end"><?= $det->cantidad ?> <small
                                            class="text-muted"><?= $det->unidad_medida ?></small></td>
                                    <td class="text-end"><?= formatMoney($det->precio_unitario) ?></td>
                                    <td class="text-end pe-3 fw-medium"><?= formatMoney($det->subtotal) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-3">TOTAL ORDEN</td>
                                <td class="text-end pe-3 fw-bolder fs-5 text-primary py-3">
                                    <?= formatMoney($orden->total) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if ($orden->notas): ?>
                    <div class="mb-0">
                        <h6 class="fw-bold text-muted text-uppercase small mb-2">Notas</h6>
                        <p class="text-muted mb-0 p-3 bg-light rounded-3"><?= nl2br(htmlspecialchars($orden->notas)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel Lateral: Acciones -->
    <div class="col-lg-4 print-hide">
        <?php if ($orden->estado === 'borrador' || $orden->estado === 'pendiente'): ?>
            <div class="card border-0 shadow-sm border-top border-primary border-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-box-arrow-in-down me-2 text-primary"></i>Recibir Mercancía</h5>
                    <p class="text-muted small mb-4">Al recibir esta orden, el inventario se actualizará automáticamente y
                        se registrarán las entradas correspondientes en el Kardex.</p>

                    <form method="POST" action="<?= url('compras/recibir/' . $orden->id) ?>" id="formRecibir">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                        <?php
                        $hasPerecederos = false;
                        foreach ($orden->detalles as $det) {
                            if ($det->es_perecedero) {
                                $hasPerecederos = true;
                                break;
                            }
                        }

                        if ($hasPerecederos):
                            ?>
                            <div class="alert alert-warning border-warning-subtle small px-3 py-2 mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Esta orden contiene productos perecederos.
                                Ingrese los datos de lote.
                            </div>

                            <?php foreach ($orden->detalles as $det): ?>
                                <?php if ($det->es_perecedero): ?>
                                    <div class="bg-light p-3 rounded-3 mb-3 border">
                                        <div class="fw-bold mb-2 text-primary" style="font-size:0.85rem">
                                            <?= htmlspecialchars($det->producto_nombre) ?> (Cant: <?= $det->cantidad ?>)
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label text-muted" style="font-size:0.75rem">Núm. Lote *</label>
                                                <input type="text" name="lotes[<?= $det->id ?>]" class="form-control form-control-sm"
                                                    required placeholder="LOTE-XXX">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted" style="font-size:0.75rem">Vencimiento *</label>
                                                <input type="date" name="vencimientos[<?= $det->id ?>]"
                                                    class="form-control form-control-sm" required min="<?= date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary w-100 fw-medium"
                            onclick="return confirm('¿Está seguro de recibir esta orden? Se actualizará el inventario permanentemente.')">
                            <i class="bi bi-check2-circle me-1"></i>Marcar como Recibida
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm border-top border-danger border-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-x-circle me-2"></i>Cancelar Orden</h5>
                    <p class="text-muted small mb-3">Si la orden ya no es requerida o el proveedor no puede despachar.</p>
                    <form method="POST" action="<?= url('compras/cancelar/' . $orden->id) ?>">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                            onclick="return confirm('¿Está seguro de cancelar esta orden? Esta acción no se puede deshacer.')">
                            Cancelar Orden
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Estado Recibida o Cancelada -->
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <?php if ($orden->estado === 'recibida'): ?>
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-check-lg" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Orden Completada</h5>
                        <p class="text-muted mb-0">El inventario fue ingresado exitosamente.</p>
                    <?php else: ?>
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-x-lg" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Orden Cancelada</h5>
                        <p class="text-muted mb-0">Esta orden no tiene efectos en el inventario.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @media print {
        .print-hide {
            display: none !important;
        }

        .print-card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        body {
            background-color: #fff !important;
        }
    }
</style>