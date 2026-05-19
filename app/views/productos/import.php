<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="<?= url('productos') ?>" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Volver a Productos
        </a>
        <h5 class="fw-800 mb-0">Importar Productos</h5>
        <small class="text-muted">Carga masiva desde archivo CSV</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-upload me-2"></i>Subir Archivo CSV</h6>
            </div>
            <div class="card-body">
                <form action="<?= url('productos/importar') ?>" method="POST" enctype="multipart/form-data" id="form-import">
                    <?= csrfField() ?>

                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold">Archivo CSV</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" 
                               accept=".csv" required>
                        <div class="form-text">Tamaño máximo: 5MB. Solo archivos .csv</div>
                    </div>

                    <!-- Preview area (populated by JS) -->
                    <div id="csv-preview" class="mb-4" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">
                                <i class="bi bi-eye me-1"></i>Vista Previa
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" id="preview-valid">
                                    <i class="bi bi-check-circle me-1"></i><span id="valid-count">0</span> válidas
                                </span>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" id="preview-errors" style="display:none;">
                                    <i class="bi bi-exclamation-circle me-1"></i><span id="error-count">0</span> con error
                                </span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary" id="preview-total">
                                    <span id="total-count">0</span> filas
                                </span>
                            </div>
                        </div>
                        <div class="table-wrapper" style="max-height: 350px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px;">
                            <table class="table table-sm mb-0" id="preview-table">
                                <thead style="position: sticky; top: 0; z-index: 1;"></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted mt-1 d-block" id="preview-note"></small>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-importar" disabled>
                        <i class="bi bi-cloud-upload me-1"></i>Importar Productos
                    </button>
                    <small class="text-muted ms-2" id="btn-hint">Selecciona un archivo CSV para continuar</small>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- Instrucciones -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2"></i>Instrucciones</h6>
            </div>
            <div class="card-body">
                <ol class="mb-3" style="padding-left: 1.2rem; line-height: 2;">
                    <li>Descarga la <strong>plantilla CSV</strong> con el formato correcto</li>
                    <li>Completa los datos — la primera fila son los encabezados</li>
                    <li>Los SKU deben ser únicos (no existir previamente)</li>
                    <li>La columna <code>categoria</code> debe coincidir con categorías existentes</li>
                    <li>Revisa la <strong>vista previa</strong> y confirma con <strong>Importar</strong></li>
                </ol>
                <a href="<?= url('productos/exportar') ?>" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-download me-1"></i>Descargar Plantilla CSV
                </a>
            </div>
        </div>

        <!-- Formato -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Formato del CSV</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-wrapper">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Columna</th>
                                <th>Requerida</th>
                                <th>Ejemplo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><code>nombre</code></td><td><span class="badge bg-danger">Sí</span></td><td>Teclado USB</td></tr>
                            <tr><td><code>sku</code></td><td><span class="badge bg-danger">Sí</span></td><td>TEC-001</td></tr>
                            <tr><td><code>costo</code></td><td><span class="badge bg-danger">Sí</span></td><td>250.00</td></tr>
                            <tr><td><code>stock</code></td><td><span class="badge bg-danger">Sí</span></td><td>50</td></tr>
                            <tr><td><code>stock_minimo</code></td><td><span class="badge bg-secondary">No</span></td><td>10</td></tr>
                            <tr><td><code>categoria</code></td><td><span class="badge bg-secondary">No</span></td><td>Electrónica</td></tr>
                            <tr><td><code>descripcion</code></td><td><span class="badge bg-secondary">No</span></td><td>Teclado ergonómico</td></tr>
                            <tr><td><code>unidad_medida</code></td><td><span class="badge bg-secondary">No</span></td><td>Unidad</td></tr>
                            <tr><td><code>codigo_barras</code></td><td><span class="badge bg-secondary">No</span></td><td>7501234567890</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/import-csv.js') ?>?v=<?= filemtime(PUBLIC_PATH . '/assets/js/import-csv.js') ?>"></script>

