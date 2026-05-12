/**
 * InvSys — Productos Page Scripts (create + edit)
 * Image upload, barcode, perecedero toggle, form validation
 */
document.addEventListener('DOMContentLoaded', function () {
    const PAGE_DATA = JSON.parse(document.getElementById('page-data')?.textContent || '{}');
    const isEdit = !!PAGE_DATA.sku;

    // === Perecedero Toggle ===
    const perecederoSwitch = document.getElementById('es_perecedero');
    const perecederoAlert = document.getElementById('perecederoAlert');
    if (perecederoSwitch && perecederoAlert) {
        perecederoSwitch.addEventListener('change', function () {
            if (isEdit) {
                perecederoAlert.style.setProperty('display', this.checked ? 'block' : 'none', 'important');
            } else {
                perecederoAlert.classList.toggle('d-none', !this.checked);
            }
        });
    }

    // === Image Upload ===
    const zone = document.getElementById('imageUploadZone');
    const input = document.getElementById('inputImagen');
    const preview = document.getElementById('imagePreview');
    const deleteCheck = document.getElementById('eliminar_imagen');

    if (zone && input) {
        zone.addEventListener('click', (e) => {
            if (e.target.closest('.form-check')) return;
            input.click();
        });
        zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', (e) => {
            e.preventDefault(); zone.classList.remove('dragover');
            if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; showPreview(e.dataTransfer.files[0]); }
        });
        input.addEventListener('change', function () {
            if (this.files.length) { showPreview(this.files[0]); if (deleteCheck) deleteCheck.checked = false; }
        });
    }

    function showPreview(file) {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 2 * 1024 * 1024) { alert('La imagen excede el tamaño máximo de 2MB.'); return; }
        const reader = new FileReader();
        reader.onload = (e) => {
            const label = isEdit ? `<small class="text-muted mt-2"><i class="bi bi-arrow-repeat me-1"></i>Nueva imagen: ${file.name} (${(file.size / 1024).toFixed(0)} KB)</small>` : `<small class="text-muted mt-2">${file.name} (${(file.size / 1024).toFixed(0)} KB)</small>`;
            preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover;">${label}`;
        };
        reader.readAsDataURL(file);
    }

    // === Barcode (edit + show only) ===
    if (PAGE_DATA.sku && typeof JsBarcode !== 'undefined') {
        const barcodeValue = PAGE_DATA.codigo_barras || PAGE_DATA.sku;
        try {
            JsBarcode("#barcode", barcodeValue, { format: "CODE128", width: 2, height: 60, displayValue: false, margin: 10 });
        } catch (e) { console.warn('Barcode error:', e); }

        document.getElementById('btnPrintBarcode')?.addEventListener('click', function () {
            const svg = document.getElementById('barcode');
            const nombre = PAGE_DATA.nombre || '';
            const sku = PAGE_DATA.sku || '';
            const barcode = barcodeValue;
            const svgHTML = svg.outerHTML;
            const skuLine = barcode !== sku ? `<div class="barcode-val">SKU: ${sku}</div>` : '';

            const w = window.open('', '_blank', 'width=450,height=400');
            w.document.write(`<html><head><title>Etiqueta - ${sku}</title>
            <style>
                body{font-family:Arial,sans-serif;text-align:center;margin:0;padding:20px;background:#f5f5f5}
                .label{border:1px dashed #ccc;padding:20px;display:inline-block;background:#fff;border-radius:8px;margin-bottom:15px}
                .name{font-size:12px;margin-top:5px;color:#333}
                .sku{font-size:14px;font-weight:bold;margin-top:3px}
                .barcode-val{font-size:11px;color:#666;margin-top:2px}
                .actions{display:flex;gap:10px;justify-content:center;margin-top:10px}
                .btn{padding:8px 20px;border:none;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:6px}
                .btn-print{background:#6366f1;color:#fff}
                .btn-print:hover{background:#4f46e5}
                .btn-download{background:#10b981;color:#fff}
                .btn-download:hover{background:#059669}
                @media print{.actions{display:none!important}.label{border:none;box-shadow:none}body{background:#fff;padding:5px}}
            </style></head><body>
            <div class="label" id="labelContent">${svgHTML}<div class="sku">${barcode}</div><div class="name">${nombre}</div>${skuLine}</div>
            <div class="actions">
                <button class="btn btn-print" id="btnPrint">&#128424; Imprimir</button>
                <button class="btn btn-download" id="btnDownload">&#128190; Descargar PNG</button>
            </div>
            </body></html>`);
            w.document.close();

            // Wait for the popup DOM to be ready, then attach event listeners
            w.addEventListener('DOMContentLoaded', attachHandlers);
            // Fallback in case DOMContentLoaded already fired
            setTimeout(() => attachHandlers(), 200);

            let attached = false;
            function attachHandlers() {
                if (attached) return;
                const printBtn = w.document.getElementById('btnPrint');
                const dlBtn = w.document.getElementById('btnDownload');
                if (!printBtn || !dlBtn) return;
                attached = true;

                printBtn.addEventListener('click', function () { w.print(); });

                dlBtn.addEventListener('click', function () {
                    const labelEl = w.document.getElementById('labelContent');
                    const svgEl = labelEl.querySelector('svg');
                    if (!svgEl) return;

                    // Create a canvas to render the label as PNG
                    const canvas = w.document.createElement('canvas');
                    const svgRect = svgEl.getBoundingClientRect();
                    const scale = 2; // retina quality
                    canvas.width = Math.max(svgRect.width, 300) * scale;
                    canvas.height = (svgRect.height + 80) * scale;
                    const ctx = canvas.getContext('2d');
                    ctx.scale(scale, scale);
                    ctx.fillStyle = '#fff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    // Render SVG to canvas
                    const svgData = new XMLSerializer().serializeToString(svgEl);
                    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                    const svgUrl = URL.createObjectURL(svgBlob);
                    const img = new Image();
                    img.onload = function () {
                        const xOffset = (Math.max(svgRect.width, 300) - svgRect.width) / 2;
                        ctx.drawImage(img, xOffset, 10, svgRect.width, svgRect.height);
                        URL.revokeObjectURL(svgUrl);

                        // Draw text below
                        const textY = svgRect.height + 20;
                        ctx.fillStyle = '#000';
                        ctx.font = 'bold 14px Arial';
                        ctx.textAlign = 'center';
                        const centerX = Math.max(svgRect.width, 300) / 2;
                        ctx.fillText(barcode, centerX, textY);
                        ctx.font = '12px Arial';
                        ctx.fillStyle = '#333';
                        ctx.fillText(nombre, centerX, textY + 18);
                        if (barcode !== sku) {
                            ctx.font = '11px Arial';
                            ctx.fillStyle = '#666';
                            ctx.fillText('SKU: ' + sku, centerX, textY + 34);
                        }

                        // Download
                        const a = w.document.createElement('a');
                        a.href = canvas.toDataURL('image/png');
                        a.download = 'etiqueta_' + sku + '.png';
                        a.click();
                    };
                    img.src = svgUrl;
                });
            }
        });
    }

    // === Price Chart (show page only) ===
    if (PAGE_DATA.costoChart && PAGE_DATA.costoChart.length >= 2 && typeof Chart !== 'undefined') {
        const labels = PAGE_DATA.costoChart.map(d => d.fecha);
        const prices = PAGE_DATA.costoChart.map(d => parseFloat(d.costo));
        const symbol = PAGE_DATA.monedaSimbolo || '$';
        new Chart(document.getElementById('chartCosto'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Costo', data: prices, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)', borderWidth: 2.5, pointBackgroundColor: '#6366f1', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.3 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => symbol + ctx.parsed.y.toFixed(2) } } }, scales: { y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: v => symbol + v.toLocaleString() } }, x: { grid: { display: false } } } }
        });
    }

    // === Form Validation ===
    const createForm = document.getElementById('formCrearProducto');
    const editForm = document.getElementById('formEditarProducto');
    if (createForm) {
        FormValidator.init('#formCrearProducto', {
            nombre: { required: true, maxlength: 200, messages: { required: 'El nombre del producto es obligatorio' } },
            sku: { required: true, maxlength: 16, pattern: '^[A-Za-z0-9\\-_]+$', messages: { required: 'El SKU es obligatorio', pattern: 'Solo letras, números, guiones y guiones bajos' } },
            costo: { required: true, min: 0, messages: { required: 'El costo es obligatorio' } },
            stock: { min: 0 }, stock_minimo: { min: 0 }
        });
    }
    if (editForm) {
        FormValidator.init('#formEditarProducto', {
            nombre: { required: true, maxlength: 200, messages: { required: 'El nombre del producto es obligatorio' } },
            sku: { required: true, maxlength: 16, pattern: '^[A-Za-z0-9\\-_]+$', messages: { required: 'El SKU es obligatorio', pattern: 'Solo letras, números, guiones y guiones bajos' } },
            costo: { required: true, min: 0, messages: { required: 'El costo es obligatorio' } },
            stock_minimo: { min: 0 }
        });
    }

    // === Vincular Proveedor (show page only) ===
    const btnVincular = document.getElementById('btnVincularProveedor');
    if (btnVincular && PAGE_DATA.baseUrl) {
        btnVincular.addEventListener('click', async function () {
            const form = document.getElementById('formVincularProveedor');
            const provSelect = document.getElementById('prov_proveedor_id');

            if (!provSelect.value) {
                provSelect.focus();
                provSelect.classList.add('is-invalid');
                return;
            }
            provSelect.classList.remove('is-invalid');

            const formData = new FormData(form);
            btnVincular.disabled = true;
            btnVincular.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Vinculando...';

            try {
                const resp = await fetch(PAGE_DATA.baseUrl + '/productos/proveedor/add', {
                    method: 'POST',
                    body: formData,
                });
                const data = await resp.json();

                if (data.success) {
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    showToast(data.error || 'Error al vincular proveedor', 'error');
                }
            } catch (err) {
                showToast('Error de conexión', 'error');
            } finally {
                btnVincular.disabled = false;
                btnVincular.innerHTML = '<i class="bi bi-link-45deg me-1"></i>Vincular Proveedor';
            }
        });
    }

    // === Desvincular Proveedor ===
    document.querySelectorAll('.btn-desvincular').forEach(btn => {
        btn.addEventListener('click', function () {
            const vinculoId = this.dataset.vinculoId;
            const provNombre = this.closest('tr')?.querySelector('strong')?.textContent || 'este proveedor';
            const self = this;

            ConfirmModal.open({
                title: '¿Desvincular proveedor?',
                message: `El proveedor <strong>${provNombre}</strong> será desvinculado de este producto. Podrá volver a vincularlo en cualquier momento.`,
                confirmText: 'Desvincular',
                cancelText: 'Cancelar',
                type: 'warning',
                icon: 'bi-link-45deg',
            }, async () => {
                self.disabled = true;
                const csrfToken = PAGE_DATA.csrfToken;

                try {
                    const formData = new FormData();
                    formData.append('_csrf_token', csrfToken);
                    formData.append('vinculo_id', vinculoId);

                    const resp = await fetch(PAGE_DATA.baseUrl + '/productos/proveedor/remove', {
                        method: 'POST',
                        body: formData,
                    });
                    const data = await resp.json();

                    if (data.success) {
                        const row = document.getElementById('vinculo-' + vinculoId);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                const counter = document.getElementById('proveedoresCount');
                                if (counter) counter.textContent = parseInt(counter.textContent) - 1;
                                const tbody = document.querySelector('#tablaProveedores tbody');
                                if (tbody && tbody.children.length === 0) {
                                    window.location.reload();
                                }
                            }, 300);
                        }
                        showToast('Proveedor desvinculado correctamente', 'success');
                    } else {
                        showToast(data.error || 'Error al desvincular', 'error');
                        self.disabled = false;
                    }
                } catch (err) {
                    showToast('Error de conexión', 'error');
                    self.disabled = false;
                }
            });
        });
    });

    // Helper: show toast if available
    function showToast(msg, type) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast toast-${type === 'error' ? 'error' : 'success'}`;
        toast.innerHTML = `<i class="bi ${type === 'error' ? 'bi-exclamation-circle' : 'bi-check-circle-fill'} me-2"></i>${msg}`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }
    // === Products Index Filters ===
    const filterForm = document.getElementById('filter-productos');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const filters = {
                search: filterForm.querySelector('[name="search"]').value,
                categoria: filterForm.querySelector('[name="categoria"]').value,
                stock: filterForm.querySelector('[name="stock"]').value
            };
            sessionStorage.setItem('invsys_productos_filters', JSON.stringify(filters));
        });
    }

    // Auto-restore filters if no query params present but we have saved ones
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('search') && !urlParams.has('categoria') && !urlParams.has('stock') && !urlParams.has('page')) {
        const savedStr = sessionStorage.getItem('invsys_productos_filters');
        if (savedStr) {
            const saved = JSON.parse(savedStr);
            let hasFilters = false;
            if (saved.search) { urlParams.set('search', saved.search); hasFilters = true; }
            if (saved.categoria) { urlParams.set('categoria', saved.categoria); hasFilters = true; }
            if (saved.stock) { urlParams.set('stock', saved.stock); hasFilters = true; }
            
            if (hasFilters) {
                // Redirect to apply saved filters
                window.location.search = urlParams.toString();
            }
        }
    }

    // === Mass Print Logic ===
    const selectAll = document.getElementById('selectAllProducts');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const btnPrintSelected = document.getElementById('btn-print-selected');
    const printCount = document.getElementById('print-count');

    function updatePrintButton() {
        if (!btnPrintSelected) return;
        const selected = document.querySelectorAll('.product-checkbox:checked').length;
        if (selected > 0) {
            printCount.textContent = selected;
            btnPrintSelected.classList.remove('d-none');
        } else {
            btnPrintSelected.classList.add('d-none');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updatePrintButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                else if (document.querySelectorAll('.product-checkbox:checked').length === checkboxes.length) selectAll.checked = true;
                updatePrintButton();
            });
        });

        btnPrintSelected?.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) return;
            
            const baseUrl = PAGE_DATA.baseUrl || '';
            const w = window.open(baseUrl + '/productos/imprimir_masivo?ids=' + selectedIds.join(','), '_blank');
            if(w) w.focus();
        });
    }
});
