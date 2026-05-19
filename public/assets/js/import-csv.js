/**
 * InvSys — CSV Import Preview
 * Validates and previews CSV files before server-side import.
 */
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('csv_file');
    const previewDiv = document.getElementById('csv-preview');
    const previewTable = document.getElementById('preview-table');
    const btnImportar = document.getElementById('btn-importar');
    const btnHint = document.getElementById('btn-hint');
    const validCountEl = document.getElementById('valid-count');
    const errorCountEl = document.getElementById('error-count');
    const totalCountEl = document.getElementById('total-count');
    const previewErrors = document.getElementById('preview-errors');
    const previewNote = document.getElementById('preview-note');

    if (!fileInput || !previewDiv) return;

    var REQUIRED_COLS = ['nombre', 'sku', 'costo', 'stock'];
    var MAX_PREVIEW = 15;

    fileInput.addEventListener('change', function () {
        var file = this.files[0];
        if (!file) {
            previewDiv.style.display = 'none';
            btnImportar.disabled = true;
            btnHint.textContent = 'Selecciona un archivo CSV para continuar';
            return;
        }

        // Validate extension
        if (!file.name.toLowerCase().endsWith('.csv')) {
            previewDiv.style.display = 'none';
            btnImportar.disabled = true;
            btnHint.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Solo se permiten archivos .csv</span>';
            return;
        }

        // Validate size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            previewDiv.style.display = 'none';
            btnImportar.disabled = true;
            btnHint.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>El archivo excede 5MB</span>';
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            var text = e.target.result;

            // Strip BOM if present
            if (text.charCodeAt(0) === 0xFEFF) {
                text = text.slice(1);
            }

            // Filter lines, skip empty and Excel sep= directive
            var lines = text.split(/\r?\n/).filter(function (l) {
                var trimmed = l.trim();
                return trimmed.length > 0 && !/^sep=.$/i.test(trimmed);
            });

            if (lines.length < 2) {
                previewDiv.style.display = 'none';
                btnImportar.disabled = true;
                btnHint.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>El archivo está vacío o solo tiene encabezados</span>';
                return;
            }

            // Auto-detect delimiter (comma vs semicolon)
            var headerLine = lines[0];
            var commaCount = (headerLine.match(/,/g) || []).length;
            var semicolonCount = (headerLine.match(/;/g) || []).length;
            var delim = semicolonCount > commaCount ? ';' : ',';

            // Parse header
            var headers = parseCSVLine(lines[0], delim).map(function (h) {
                return h.toLowerCase().trim().replace(/[\x00-\x1F\x80-\xFF]/g, '');
            });

            // Check required columns
            var missing = REQUIRED_COLS.filter(function (c) {
                return headers.indexOf(c) === -1;
            });

            if (missing.length > 0) {
                previewDiv.style.display = 'none';
                btnImportar.disabled = true;
                btnHint.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Faltan columnas requeridas: ' + missing.join(', ') + '</span>';
                return;
            }

            // Build preview table
            var thead = previewTable.querySelector('thead');
            var tbody = previewTable.querySelector('tbody');
            thead.innerHTML = '';
            tbody.innerHTML = '';

            // Header row
            var trHead = document.createElement('tr');
            var thStatus = document.createElement('th');
            thStatus.textContent = '';
            thStatus.style.width = '32px';
            trHead.appendChild(thStatus);

            for (var hi = 0; hi < headers.length; hi++) {
                var th = document.createElement('th');
                if (REQUIRED_COLS.indexOf(headers[hi]) !== -1) {
                    th.innerHTML = headers[hi] + ' <span class="text-danger">*</span>';
                } else {
                    th.textContent = headers[hi];
                }
                trHead.appendChild(th);
            }
            thead.appendChild(trHead);

            // Data rows
            var totalRows = lines.length - 1;
            var previewRows = Math.min(totalRows, MAX_PREVIEW);
            var validCount = 0;
            var errorCount = 0;
            var seenSkus = {};

            for (var i = 1; i <= previewRows; i++) {
                var values = parseCSVLine(lines[i], delim);
                var tr = document.createElement('tr');
                var rowValid = true;
                var rowErrors = [];

                // Map data
                var rowData = {};
                for (var j = 0; j < headers.length; j++) {
                    rowData[headers[j]] = (values[j] || '').trim();
                }

                // Validate required
                for (var ri = 0; ri < REQUIRED_COLS.length; ri++) {
                    if (!rowData[REQUIRED_COLS[ri]]) {
                        rowValid = false;
                        rowErrors.push(REQUIRED_COLS[ri] + ' vacío');
                    }
                }

                // Validate numeric
                if (rowData.costo && isNaN(parseFloat(rowData.costo))) {
                    rowValid = false;
                    rowErrors.push('costo no numérico');
                }
                if (rowData.stock && isNaN(parseInt(rowData.stock))) {
                    rowValid = false;
                    rowErrors.push('stock no numérico');
                }

                // Check duplicate SKU within file
                if (rowData.sku) {
                    var skuUp = rowData.sku.toUpperCase();
                    if (seenSkus[skuUp]) {
                        rowValid = false;
                        rowErrors.push('SKU duplicado');
                    }
                    seenSkus[skuUp] = true;
                }

                // Status cell
                var tdStatus = document.createElement('td');
                if (rowValid) {
                    tdStatus.innerHTML = '<i class="bi bi-check-circle-fill text-success" style="font-size:0.9rem;"></i>';
                    validCount++;
                } else {
                    tdStatus.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger" title="' + rowErrors.join(', ') + '" style="font-size:0.9rem; cursor:help;"></i>';
                    tr.style.backgroundColor = 'rgba(239,68,68,0.04)';
                    errorCount++;
                }
                tr.appendChild(tdStatus);

                // Value cells
                for (var vi = 0; vi < headers.length; vi++) {
                    var td = document.createElement('td');
                    var val = (values[vi] || '').trim();
                    td.textContent = val || '—';
                    td.style.fontSize = '0.82rem';
                    if (!val && REQUIRED_COLS.indexOf(headers[vi]) !== -1) {
                        td.classList.add('text-danger', 'fw-bold');
                    }
                    tr.appendChild(td);
                }

                tbody.appendChild(tr);
            }

            // Remaining rows estimate
            var remaining = totalRows - previewRows;
            validCount += remaining;

            // Update counters
            validCountEl.textContent = validCount;
            errorCountEl.textContent = errorCount;
            totalCountEl.textContent = totalRows;
            previewErrors.style.display = errorCount > 0 ? '' : 'none';

            if (remaining > 0) {
                previewNote.textContent = 'Mostrando ' + previewRows + ' de ' + totalRows + ' filas. Las ' + remaining + ' restantes se procesarán en el servidor.';
            } else {
                previewNote.textContent = 'Mostrando todas las ' + totalRows + ' filas.';
            }

            previewDiv.style.display = '';
            btnImportar.disabled = false;
            btnHint.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Archivo listo para importar</span>';
        };

        reader.readAsText(file, 'UTF-8');
    });

    /**
     * Simple CSV line parser that handles quoted fields.
     */
    function parseCSVLine(line, separator) {
        var sep = separator || ',';
        var result = [];
        var current = '';
        var inQuotes = false;
        for (var i = 0; i < line.length; i++) {
            var ch = line[i];
            if (inQuotes) {
                if (ch === '"' && line[i + 1] === '"') {
                    current += '"';
                    i++;
                } else if (ch === '"') {
                    inQuotes = false;
                } else {
                    current += ch;
                }
            } else {
                if (ch === '"') {
                    inQuotes = true;
                } else if (ch === sep) {
                    result.push(current);
                    current = '';
                } else {
                    current += ch;
                }
            }
        }
        result.push(current);
        return result;
    }

    // Confirm before submit if errors exist
    var formImport = document.getElementById('form-import');
    if (formImport) {
        formImport.addEventListener('submit', function (e) {
            var errors = parseInt(errorCountEl.textContent) || 0;
            if (errors > 0) {
                if (!confirm('Se detectaron ' + errors + ' filas con posibles errores. El servidor omitirá las filas inválidas. ¿Desea continuar?')) {
                    e.preventDefault();
                }
            }
        });
    }
});
