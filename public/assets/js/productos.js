/**
 * InvSys — Productos Page Scripts (create + edit)
 * Image upload, barcode, perecedero toggle, form validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const PAGE_DATA = JSON.parse(document.getElementById('page-data')?.textContent || '{}');
    const isEdit = !!PAGE_DATA.sku;

    // === Perecedero Toggle ===
    const perecederoSwitch = document.getElementById('es_perecedero');
    const perecederoAlert = document.getElementById('perecederoAlert');
    if (perecederoSwitch && perecederoAlert) {
        perecederoSwitch.addEventListener('change', function() {
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
        input.addEventListener('change', function() {
            if (this.files.length) { showPreview(this.files[0]); if (deleteCheck) deleteCheck.checked = false; }
        });
    }

    function showPreview(file) {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 2 * 1024 * 1024) { alert('La imagen excede el tamaño máximo de 2MB.'); return; }
        const reader = new FileReader();
        reader.onload = (e) => {
            const label = isEdit ? `<small class="text-muted mt-2"><i class="bi bi-arrow-repeat me-1"></i>Nueva imagen: ${file.name} (${(file.size/1024).toFixed(0)} KB)</small>` : `<small class="text-muted mt-2">${file.name} (${(file.size/1024).toFixed(0)} KB)</small>`;
            preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover;">${label}`;
        };
        reader.readAsDataURL(file);
    }

    // === Barcode (edit + show only) ===
    if (PAGE_DATA.sku && typeof JsBarcode !== 'undefined') {
        try {
            JsBarcode("#barcode", PAGE_DATA.sku, { format: "CODE128", width: 2, height: 60, displayValue: false, margin: 10 });
        } catch(e) { console.warn('Barcode error:', e); }

        document.getElementById('btnPrintBarcode')?.addEventListener('click', function() {
            const svg = document.getElementById('barcode');
            const nombre = PAGE_DATA.nombre || '';
            const sku = PAGE_DATA.sku || '';
            const w = window.open('', '_blank', 'width=400,height=300');
            w.document.write(`<html><head><title>Etiqueta - ${sku}</title>
            <style>body{font-family:Arial,sans-serif;text-align:center;margin:20px}.label{border:1px dashed #ccc;padding:15px;display:inline-block}.name{font-size:12px;margin-top:5px}.sku{font-size:14px;font-weight:bold;margin-top:3px}@media print{.label{border:none}}</style></head><body>
            <div class="label">${svg.outerHTML}<div class="sku">${sku}</div><div class="name">${nombre}</div></div>
            <script>window.onload=function(){window.print();}<\/script></body></html>`);
            w.document.close();
        });
    }

    // === Price Chart (show page only) ===
    if (PAGE_DATA.precioChart && PAGE_DATA.precioChart.length >= 2 && typeof Chart !== 'undefined') {
        const labels = PAGE_DATA.precioChart.map(d => d.fecha);
        const prices = PAGE_DATA.precioChart.map(d => parseFloat(d.precio));
        const symbol = PAGE_DATA.monedaSimbolo || '$';
        new Chart(document.getElementById('chartPrecio'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Precio', data: prices, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)', borderWidth: 2.5, pointBackgroundColor: '#6366f1', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.3 }] },
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
            precio: { required: true, min: 0, messages: { required: 'El precio es obligatorio' } },
            stock: { min: 0 }, stock_minimo: { min: 0 }
        });
    }
    if (editForm) {
        FormValidator.init('#formEditarProducto', {
            nombre: { required: true, maxlength: 200, messages: { required: 'El nombre del producto es obligatorio' } },
            sku: { required: true, maxlength: 16, pattern: '^[A-Za-z0-9\\-_]+$', messages: { required: 'El SKU es obligatorio', pattern: 'Solo letras, números, guiones y guiones bajos' } },
            precio: { required: true, min: 0, messages: { required: 'El precio es obligatorio' } },
            stock_minimo: { min: 0 }
        });
    }
});
