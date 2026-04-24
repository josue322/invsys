<!-- Scanner Interface -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card animate-fadeIn">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-upc-scan me-2"></i>Escáner de Códigos</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary" id="btnStartScan">
                        <i class="bi bi-camera-fill me-1"></i>Activar Cámara
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-none" id="btnStopScan">
                        <i class="bi bi-stop-fill me-1"></i>Detener
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Manual input -->
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-upc"></i></span>
                    <input type="text" class="form-control" id="manualCode" 
                           placeholder="Ingrese el SKU manualmente o escanee con la cámara..." 
                           autocomplete="off" autofocus>
                    <button class="btn btn-primary" id="btnManualSearch">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                </div>

                <!-- Camera view -->
                <div id="scanner-container" class="d-none">
                    <div id="reader" style="width:100%;max-width:500px;margin:0 auto;border-radius:12px;overflow:hidden;"></div>
                    <small class="text-muted d-block text-center mt-2">
                        <i class="bi bi-info-circle me-1"></i>Apunte la cámara al código de barras del producto
                    </small>
                </div>

                <!-- Result -->
                <div id="scanResult" class="d-none mt-3">
                    <!-- Filled by JS -->
                </div>

                <!-- No result -->
                <div id="scanEmpty" class="d-none mt-3">
                    <div class="alert alert-warning d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <strong>Producto no encontrado</strong>
                            <p class="mb-0" id="scanEmptyMsg"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card mt-3 border-0" style="background: var(--bs-tertiary-bg);">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-2"></i>¿Cómo usar?</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">1</span>
                            <small>Haga clic en <strong>Activar Cámara</strong> o escriba el SKU directamente.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">2</span>
                            <small>Apunte al <strong>código de barras</strong> del producto.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">3</span>
                            <small>Acceda al producto o registre un <strong>movimiento rápido</strong>.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE = document.querySelector('meta[name="base-url"]')?.content || '/invsys/public';
    const btnStart = document.getElementById('btnStartScan');
    const btnStop = document.getElementById('btnStopScan');
    const container = document.getElementById('scanner-container');
    const resultDiv = document.getElementById('scanResult');
    const emptyDiv = document.getElementById('scanEmpty');
    const emptyMsg = document.getElementById('scanEmptyMsg');
    const manualInput = document.getElementById('manualCode');
    const btnManual = document.getElementById('btnManualSearch');

    let scanner = null;
    let isScanning = false;
    let lastScanned = '';

    // ─── Manual Search ───
    btnManual.addEventListener('click', () => searchProduct(manualInput.value.trim()));
    manualInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchProduct(manualInput.value.trim());
        }
    });

    // ─── Camera Scanner ───
    btnStart.addEventListener('click', startScanner);
    btnStop.addEventListener('click', stopScanner);

    function startScanner() {
        container.classList.remove('d-none');
        btnStart.classList.add('d-none');
        btnStop.classList.remove('d-none');

        scanner = new Html5Qrcode("reader");
        isScanning = true;

        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 280, height: 150 }, aspectRatio: 1.5 },
            onScanSuccess,
            () => {} // ignore errors
        ).catch(err => {
            console.error('Camera error:', err);
            showToast('No se pudo acceder a la cámara. Verifique los permisos.', 'error');
            stopScanner();
        });
    }

    function stopScanner() {
        if (scanner && isScanning) {
            scanner.stop().then(() => {
                scanner.clear();
            }).catch(() => {});
        }
        isScanning = false;
        container.classList.add('d-none');
        btnStart.classList.remove('d-none');
        btnStop.classList.add('d-none');
    }

    function onScanSuccess(decodedText) {
        // Prevent duplicate scans
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;

        // Vibrate on mobile
        if (navigator.vibrate) navigator.vibrate(100);

        manualInput.value = decodedText;
        searchProduct(decodedText);

        // Reset after 3 seconds to allow re-scan
        setTimeout(() => { lastScanned = ''; }, 3000);
    }

    function searchProduct(code) {
        if (!code) return;

        resultDiv.classList.add('d-none');
        emptyDiv.classList.add('d-none');

        fetch(`${BASE}/escaner/buscar/${encodeURIComponent(code)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.found) {
                showProduct(data.product);
            } else if (data.multiple) {
                showMultiple(data.results);
            } else {
                emptyMsg.textContent = data.error || 'Producto no encontrado';
                emptyDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            showToast('Error al buscar el producto', 'error');
        });
    }

    function showProduct(p) {
        const stockClass = p.stock <= 0 ? 'text-danger' : (p.stock <= 5 ? 'text-warning' : 'text-success');
        resultDiv.innerHTML = `
            <div class="card border-primary border-opacity-25 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">${p.nombre}</h5>
                            <span class="badge bg-body-secondary text-body">${p.sku}</span>
                            <span class="badge bg-body-secondary text-body">${p.unidad_medida}</span>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-800 ${stockClass}">${p.stock}</div>
                            <small class="text-muted">Stock actual</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="${p.urlEditar}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i>Ver Producto
                        </a>
                        <a href="${p.urlMovimiento}" class="btn btn-primary">
                            <i class="bi bi-arrow-left-right me-1"></i>Registrar Movimiento
                        </a>
                    </div>
                </div>
            </div>`;
        resultDiv.classList.remove('d-none');
    }

    function showMultiple(results) {
        let html = '<div class="alert alert-info mb-2"><i class="bi bi-info-circle me-1"></i>Se encontraron múltiples coincidencias:</div>';
        html += '<div class="list-group">';
        results.forEach(p => {
            html += `
                <a href="${p.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${p.nombre}</strong>
                        <span class="badge bg-body-secondary text-body ms-2">${p.sku}</span>
                    </div>
                    <span class="badge bg-primary rounded-pill">Stock: ${p.stock}</span>
                </a>`;
        });
        html += '</div>';
        resultDiv.innerHTML = html;
        resultDiv.classList.remove('d-none');
    }
});
</script>
