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

    const viewport = document.getElementById('scannerViewport');
    const statusEl = document.getElementById('scannerStatus');

    // ─── Beep sound for detection ───
    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = 1200;
            gain.gain.value = 0.3;
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch(e) {}
    }

    function setScannerState(state) {
        viewport.classList.remove('scanning', 'detected');
        statusEl.classList.remove('scanning', 'detected');

        if (state === 'scanning') {
            viewport.classList.add('scanning');
            statusEl.classList.add('scanning');
            statusEl.innerHTML = '<i class="bi bi-broadcast me-1"></i>Escaneando... apunte al código de barras';
        } else if (state === 'detected') {
            viewport.classList.add('detected');
            statusEl.classList.add('detected');
            statusEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>¡Código detectado!';
        } else {
            statusEl.innerHTML = '<i class="bi bi-info-circle me-1"></i>Apunte la cámara al código de barras del producto';
        }
    }

    async function startScanner() {
        container.classList.remove('d-none');
        btnStart.classList.add('d-none');
        btnStop.classList.remove('d-none');

        // Optimized for speed: higher FPS, larger scan area, experimental features
        const scanConfig = {
            fps: 20,
            qrbox: { width: 300, height: 160 },
            aspectRatio: 1.5,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true },
        };

        // Limit formats for faster detection
        const supportedFormats = [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.QR_CODE,
        ];

        try {
            const devices = await Html5Qrcode.getCameras();
            console.log('Cameras found:', devices);

            if (!devices || devices.length === 0) {
                throw new Error('No se detectaron cámaras en este dispositivo.');
            }

            let selectedDevice = devices[0];
            for (const d of devices) {
                const label = (d.label || '').toLowerCase();
                if (label.includes('back') || label.includes('rear') || label.includes('environment') || label.includes('trasera')) {
                    selectedDevice = d;
                    break;
                }
            }
            console.log('Using camera:', selectedDevice.label || selectedDevice.id);

            scanner = new Html5Qrcode("reader", { formatsToSupport: supportedFormats });
            isScanning = true;
            setScannerState('scanning');

            await scanner.start(
                selectedDevice.id,
                scanConfig,
                onScanSuccess,
                () => {}
            );

        } catch (err) {
            console.error('Scanner error:', err);

            let msg = 'No se pudo acceder a la cámara.';
            const errStr = String(err?.message || err).toLowerCase();

            if (errStr.includes('permission') || errStr.includes('notallowed')) {
                msg = 'Permiso de cámara denegado. Haga clic en el ícono de cámara en la barra del navegador y permita el acceso.';
            } else if (errStr.includes('no se detectaron') || errStr.includes('no cameras')) {
                msg = 'No se detectaron cámaras en este dispositivo.';
            } else if (errStr.includes('notreadable') || errStr.includes('could not start')) {
                msg = 'La cámara está en uso por otra aplicación. Ciérrela e intente de nuevo.';
            } else if (errStr.includes('insecure') || errStr.includes('secure context')) {
                msg = 'La cámara requiere HTTPS. Acceda desde https:// o http://localhost.';
            }

            showToast(msg, 'error');
            stopScanner();
        }
    }

    function stopScanner() {
        if (scanner && isScanning) {
            scanner.stop().then(() => {
                scanner.clear();
            }).catch(() => {});
        }
        isScanning = false;
        setScannerState('idle');
        container.classList.add('d-none');
        btnStart.classList.remove('d-none');
        btnStop.classList.add('d-none');
    }

    function onScanSuccess(decodedText) {
        // Prevent duplicate scans
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;

        // Visual + audio + haptic feedback
        setScannerState('detected');
        playBeep();
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

        manualInput.value = decodedText;
        searchProduct(decodedText);

        // Reset border back to scanning after 2 seconds
        setTimeout(() => {
            if (isScanning) setScannerState('scanning');
            lastScanned = '';
        }, 2500);
    }

    function searchProduct(code) {
        if (!code) return;

        resultDiv.classList.add('d-none');
        emptyDiv.classList.add('d-none');
        resultDiv.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="text-muted">Buscando producto...</span>
            </div>`;
        resultDiv.classList.remove('d-none');

        fetch(`${BASE}/escaner/buscar/${encodeURIComponent(code)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.found) {
                showProduct(data.product);
            } else if (data.multiple) {
                showMultiple(data.results);
            } else if (data.notInSystem) {
                showNotInSystem(data);
            } else {
                emptyMsg.textContent = data.error || 'Producto no encontrado';
                resultDiv.classList.add('d-none');
                emptyDiv.classList.remove('d-none');
            }
        })
        .catch(() => {
            resultDiv.classList.add('d-none');
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
                            <h5 class="fw-bold mb-1">${escapeHtml(p.nombre)}</h5>
                            <span class="badge bg-body-secondary text-body">${escapeHtml(p.sku)}</span>
                            <span class="badge bg-body-secondary text-body">${escapeHtml(p.unidad_medida)}</span>
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
                        <strong>${escapeHtml(p.nombre)}</strong>
                        <span class="badge bg-body-secondary text-body ms-2">${escapeHtml(p.sku)}</span>
                    </div>
                    <span class="badge bg-primary rounded-pill">Stock: ${p.stock}</span>
                </a>`;
        });
        html += '</div>';
        resultDiv.innerHTML = html;
        resultDiv.classList.remove('d-none');
    }

    function showNotInSystem(data) {
        const codigo = data.codigo || '';
        const canCreate = data.canCreate || false;
        const lookup = data.lookup || null;
        const createUrl = data.createUrl || '';

        let html = `
            <div class="card border-warning border-opacity-50 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-bottom-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Producto no registrado</h6>
                            <small class="text-muted">El código <code class="text-primary fw-bold">${escapeHtml(codigo)}</code> no existe en el sistema</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">`;

        // Si se encontró info externa
        if (lookup) {
            html += `
                    <div class="alert alert-success py-2 mb-3 d-flex align-items-start gap-2">
                        <i class="bi bi-cloud-check fs-5 mt-1"></i>
                        <div>
                            <strong>Información encontrada</strong>
                            <small class="d-block text-body-secondary">Fuente: ${escapeHtml(lookup.fuente)}</small>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">`;

            // Imagen del producto (si existe)
            if (lookup.imagen_url) {
                html += `
                        <div class="col-auto">
                            <img src="${escapeHtml(lookup.imagen_url)}" alt="Producto" 
                                 style="width:80px;height:80px;object-fit:contain;border-radius:8px;border:1px solid var(--bs-border-color);"
                                 onerror="this.style.display='none'">
                        </div>`;
            }

            html += `
                        <div class="col">
                            <table class="table table-sm table-borderless mb-0">`;

            if (lookup.nombre) {
                html += `<tr><td class="text-muted" style="width:100px">Nombre</td><td class="fw-bold">${escapeHtml(lookup.nombre)}</td></tr>`;
            }
            if (lookup.marca) {
                html += `<tr><td class="text-muted">Marca</td><td>${escapeHtml(lookup.marca)}</td></tr>`;
            }
            if (lookup.descripcion) {
                html += `<tr><td class="text-muted">Info</td><td><small>${escapeHtml(lookup.descripcion)}</small></td></tr>`;
            }
            if (lookup.categoria) {
                html += `<tr><td class="text-muted">Categoría</td><td><small>${escapeHtml(lookup.categoria.substring(0, 100))}</small></td></tr>`;
            }

            html += `      </table>
                        </div>
                    </div>`;
        } else {
            html += `
                    <div class="alert alert-secondary py-2 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-cloud-slash fs-5"></i>
                        <small>No se encontró información externa para este código. Puede registrarlo manualmente.</small>
                    </div>`;
        }

        // Botones de acción
        if (canCreate) {
            const params = new URLSearchParams({ sku: codigo, from_scanner: '1' });
            if (lookup?.nombre) params.set('nombre', lookup.nombre);
            if (lookup?.descripcion) params.set('descripcion', lookup.descripcion);

            html += `
                    <div class="d-flex flex-wrap gap-2">
                        <a href="${createUrl}?${params.toString()}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Registrar Producto
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="retryLookup('${escapeHtml(codigo)}')">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reintentar búsqueda externa
                        </button>
                    </div>`;
        } else {
            html += `
                    <div class="alert alert-danger py-2 mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-lock-fill"></i>
                        <small>No tiene permisos para crear productos. Contacte al administrador.</small>
                    </div>`;
        }

        html += `
                </div>
            </div>`;

        resultDiv.innerHTML = html;
        resultDiv.classList.remove('d-none');
    }

    // Reintentar búsqueda en APIs externas
    window.retryLookup = function(codigo) {
        resultDiv.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="text-muted">Consultando bases de datos externas...</span>
            </div>`;

        fetch(`${BASE}/escaner/lookup/${encodeURIComponent(codigo)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            showNotInSystem({
                codigo,
                canCreate: true,
                createUrl: `${BASE}/productos/crear`,
                lookup: data.found ? data.lookup : null,
            });
            if (data.found) {
                showToast('Se encontró información del producto', 'success');
            } else {
                showToast('No se encontró información externa', 'warning');
            }
        })
        .catch(() => {
            showToast('Error al consultar APIs externas', 'error');
        });
    };

    // Utilidad para escapar HTML
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
