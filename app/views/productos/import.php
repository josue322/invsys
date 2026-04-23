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

                    <!-- Preview area -->
                    <div id="csv-preview" class="mb-4" style="display:none;">
                        <label class="form-label fw-semibold">Vista previa</label>
                        <div class="table-wrapper" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm mb-0" id="preview-table">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted" id="preview-count"></small>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-importar">
                        <i class="bi bi-cloud-upload me-1"></i>Importar Productos
                    </button>
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
                <ol class="mb-0" style="padding-left: 1.2rem; line-height: 2;">
                    <li>Prepara un archivo CSV con las columnas requeridas</li>
                    <li>La primera fila debe contener los nombres de las columnas</li>
                    <li>Los SKU deben ser únicos (no existir previamente)</li>
                    <li>La columna <code>categoria</code> debe coincidir con categorías existentes</li>
                    <li>Selecciona el archivo y haz clic en <strong>Importar</strong></li>
                </ol>
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
                            <tr><td><code>precio</code></td><td><span class="badge bg-danger">Sí</span></td><td>250.00</td></tr>
                            <tr><td><code>stock</code></td><td><span class="badge bg-danger">Sí</span></td><td>50</td></tr>
                            <tr><td><code>stock_minimo</code></td><td><span class="badge bg-secondary">No</span></td><td>10</td></tr>
                            <tr><td><code>categoria</code></td><td><span class="badge bg-secondary">No</span></td><td>Electrónica</td></tr>
                            <tr><td><code>descripcion</code></td><td><span class="badge bg-secondary">No</span></td><td>Teclado ergonómico</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(evt) {
        const lines = evt.target.result.split('\n').filter(l => l.trim());
        if (lines.length < 2) return;
        
        const preview = document.getElementById('csv-preview');
        const thead = document.querySelector('#preview-table thead');
        const tbody = document.querySelector('#preview-table tbody');
        
        // Header
        const headers = lines[0].split(',').map(h => h.trim().replace(/['"]/g, ''));
        thead.innerHTML = '<tr>' + headers.map(h => '<th>' + h + '</th>').join('') + '</tr>';
        
        // Rows (max 5)
        tbody.innerHTML = '';
        const maxRows = Math.min(lines.length, 6);
        for (let i = 1; i < maxRows; i++) {
            const cols = lines[i].split(',').map(c => c.trim().replace(/['"]/g, ''));
            tbody.innerHTML += '<tr>' + cols.map(c => '<td><small>' + c + '</small></td>').join('') + '</tr>';
        }
        
        document.getElementById('preview-count').textContent = 
            `Mostrando ${maxRows - 1} de ${lines.length - 1} filas`;
        preview.style.display = 'block';
    };
    reader.readAsText(file);
});
</script>
