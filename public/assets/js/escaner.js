document.addEventListener('DOMContentLoaded', function() {
    const BASE = (document.querySelector('meta[name="base-url"]')?.content || '/invsys/public').replace(/\/+$/, '');
    const btnStart = document.getElementById('btnStartScan');
    const btnStop = document.getElementById('btnStopScan');
    const container = document.getElementById('scanner-container');
    const resultDiv = document.getElementById('scanResult');
    const emptyDiv = document.getElementById('scanEmpty');
    const emptyMsg = document.getElementById('scanEmptyMsg');
    const manualInput = document.getElementById('manualCode');
    const btnManual = document.getElementById('btnManualSearch');

    // ─── State Persistence ───
    let scanner = null;
    let isScanning = false;
    let lastScanned = '';
    let isPaused = false;

    // Load history from sessionStorage
    const savedHistory = sessionStorage.getItem('invsys_scan_history');
    const scanHistory = savedHistory ? JSON.parse(savedHistory) : [];
    const MAX_HISTORY = 10;

    function saveState(html = null) {
        sessionStorage.setItem('invsys_scan_history', JSON.stringify(scanHistory));
        if (html !== null) {
            sessionStorage.setItem('invsys_scan_result', html);
        }
    }

    function addToHistory(code, product) {
        if (scanHistory.length > 0 && scanHistory[0].code === code) return;

        scanHistory.unshift({
            code,
            nombre: product?.nombre || null,
            found: !!product,
            time: new Date().toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
            url: product?.urlEditar || null,
            stock: product?.stock ?? null,
        });

        if (scanHistory.length > MAX_HISTORY) scanHistory.pop();
        saveState();
        renderHistory();
    }

    function renderHistory() {
        const historyDiv = document.getElementById('scanHistory');
        if (!historyDiv) return;

        if (scanHistory.length === 0) {
            historyDiv.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-clock-history fs-4 d-block mb-1"></i><small>Los escaneos aparecerán aquí</small></div>';
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        scanHistory.forEach((item, i) => {
            const icon = item.found
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-x-circle-fill text-danger"></i>';
            const nameOrCode = item.nombre ? escapeHtml(item.nombre) : `<code>${escapeHtml(item.code)}</code>`;
            const stockBadge = item.stock !== null ? `<span class="badge ${item.stock <= 0 ? 'bg-danger' : 'bg-success'} bg-opacity-75 ms-1">${item.stock}</span>` : '';

            html += `<div class="list-group-item px-2 py-2 d-flex align-items-center gap-2 ${i === 0 ? 'border-start border-3 border-primary' : ''}" style="font-size:0.82rem;">
                ${icon}
                <div class="flex-grow-1 text-truncate">
                    <div class="text-truncate fw-medium">${nameOrCode}${stockBadge}</div>
                    <small class="text-muted">${escapeHtml(item.code)} · ${item.time}</small>
                </div>
                ${item.url ? `<a href="${item.url}" class="btn btn-sm btn-outline-primary py-0 px-1" title="Ver"><i class="bi bi-eye"></i></a>` : ''}
            </div>`;
        });
        html += '</div>';
        historyDiv.innerHTML = html;
    }

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

    // ─── Differentiated Sounds ───
    function playBeep(type) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            gain.gain.value = 0.3;

            if (type === 'success') {
                // Two rising tones
                osc.frequency.value = 800;
                osc.start();
                osc.frequency.setValueAtTime(1200, ctx.currentTime + 0.1);
                osc.stop(ctx.currentTime + 0.2);
            } else if (type === 'warning') {
                // Flat low tone
                osc.frequency.value = 400;
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } else {
                // Error: two low beeps
                osc.frequency.value = 300;
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
                setTimeout(() => {
                    try {
                        const ctx2 = new (window.AudioContext || window.webkitAudioContext)();
                        const osc2 = ctx2.createOscillator();
                        const g2 = ctx2.createGain();
                        osc2.connect(g2); g2.connect(ctx2.destination);
                        osc2.type = 'sine'; osc2.frequency.value = 250; g2.gain.value = 0.3;
                        osc2.start(); osc2.stop(ctx2.currentTime + 0.15);
                    } catch(e2){}
                }, 200);
            }
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
        isPaused = false;
        lastScanned = '';
        setScannerState('idle');
        container.classList.add('d-none');
        btnStart.classList.remove('d-none');
        btnStop.classList.add('d-none');
    }

    function onScanSuccess(decodedText) {
        if (isPaused) return;

        // Prevent duplicate scans
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;
        isPaused = true;

        // Visual feedback
        setScannerState('detected');
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

        manualInput.value = decodedText;
        searchProduct(decodedText);
    }

    function resumeScanning() {
        isPaused = false;
        lastScanned = '';
        if (isScanning) setScannerState('scanning');
        resultDiv.classList.add('d-none');
        emptyDiv.classList.add('d-none');
        manualInput.value = '';
        sessionStorage.removeItem('invsys_scan_result');
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

        // Pausar escáner para evitar peticiones repetidas simultáneas
        isPaused = true;

        fetch(`${BASE}/escaner/buscar/${encodeURIComponent(code)}?_t=${Date.now()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP ${r.status} ${r.statusText}`);
            }
            return r.text();
        })
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("JSON parse error. Response was:", text);
                throw new Error("Respuesta no es JSON válido (puede ser redirección a Login o error PHP)");
            }

            if (data.found) {
                playBeep('success');
                addToHistory(code, data.product);
                showProduct(data.product);
            } else if (data.multiple) {
                playBeep('warning');
                data.results.forEach(r => addToHistory(code, { nombre: r.nombre, urlEditar: r.url, stock: r.stock }));
                showMultiple(data.results);
            } else if (data.notInSystem) {
                playBeep('error');
                addToHistory(code, null);
                showNotInSystem(data);
            } else {
                playBeep('error');
                addToHistory(code, null);
                emptyMsg.innerHTML = `${escapeHtml(data.error || 'Producto no encontrado')}<br>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-resume-scan mt-2">
                        <i class="bi bi-camera me-1"></i>Escanear de Nuevo
                    </button>`;
                resultDiv.classList.add('d-none');
                emptyDiv.classList.remove('d-none');
                saveState(null); // Clear saved result HTML
            }
        })
        .catch(err => {
            resultDiv.classList.add('d-none');
            showToast('Error al buscar producto: ' + err.message, 'error');
            // Reanudar escaneo automáticamente después de 3.5 segundos en caso de error de red
            setTimeout(() => {
                isPaused = false;
                lastScanned = '';
                if (isScanning) setScannerState('scanning');
            }, 3500);
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
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="${p.urlEditar}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i>Ver Producto
                        </a>
                        <a href="${p.urlMovimiento}" class="btn btn-primary">
                            <i class="bi bi-arrow-left-right me-1"></i>Registrar Movimiento
                        </a>
                        <button type="button" class="btn btn-success btn-toggle-quick-move" data-id="${p.id}" data-nombre="${escapeHtml(p.nombre)}" data-stock="${p.stock}">
                            <i class="bi bi-lightning-fill me-1"></i>Movimiento Rápido
                        </button>
                        <button type="button" class="btn btn-secondary btn-resume-scan">
                            <i class="bi bi-camera me-1"></i>Escanear de Nuevo
                        </button>
                    </div>

                    <!-- Quick Movement Form (hidden by default) -->
                    <div id="quickMoveForm-${p.id}" class="d-none mt-3 p-3 rounded-3" style="background: var(--bs-tertiary-bg);">
                        <h6 class="fw-bold mb-3"><i class="bi bi-lightning-fill text-warning me-1"></i>Movimiento Rápido</h6>
                        <div class="row g-2 align-items-end">
                            <div class="col-auto">
                                <label class="form-label small mb-1">Tipo</label>
                                <select class="form-select form-select-sm" id="qmTipo-${p.id}">
                                    <option value="entrada">📥 Entrada</option>
                                    <option value="salida">📤 Salida</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label small mb-1">Cantidad</label>
                                <input type="number" class="form-control form-control-sm" id="qmCantidad-${p.id}" min="1" value="1" style="width:80px">
                            </div>
                            <div class="col">
                                <label class="form-label small mb-1">Referencia (opcional)</label>
                                <input type="text" class="form-control form-control-sm" id="qmRef-${p.id}" placeholder="Ej: OC-001, Despacho...">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-success btn-submit-quick-move" data-id="${p.id}">
                                    <i class="bi bi-check-lg me-1"></i>Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        resultDiv.classList.remove('d-none');
        saveState(resultDiv.innerHTML); // Save current view state
    }

    // Event Delegation para botones dinámicos (soluciona error CSP inline onclick)
    document.addEventListener('click', function(e) {
        // Resume scan
        const btnResume = e.target.closest('.btn-resume-scan');
        if (btnResume) {
            resumeScanning();
            return;
        }

        // Toggle Quick Movement
        const btnToggle = e.target.closest('.btn-toggle-quick-move');
        if (btnToggle) {
            const productId = btnToggle.getAttribute('data-id');
            const form = document.getElementById(`quickMoveForm-${productId}`);
            if (form) {
                form.classList.toggle('d-none');
                if (!form.classList.contains('d-none')) {
                    document.getElementById(`qmCantidad-${productId}`)?.focus();
                }
            }
            return;
        }

        // Submit Quick Movement
        const btnSubmit = e.target.closest('.btn-submit-quick-move');
        if (btnSubmit) {
            const productId = btnSubmit.getAttribute('data-id');
            submitQuickMove(productId);
            return;
        }

        // Retry Lookup
        const btnRetry = e.target.closest('.btn-retry-lookup');
        if (btnRetry) {
            const codigo = btnRetry.getAttribute('data-codigo');
            retryLookup(codigo);
            return;
        }
    });

    // Submit Quick Movement via AJAX
    function submitQuickMove(productId) {
        const tipo = document.getElementById(`qmTipo-${productId}`).value;
        const cantidad = parseInt(document.getElementById(`qmCantidad-${productId}`).value);
        const referencia = document.getElementById(`qmRef-${productId}`).value.trim();

        if (!cantidad || cantidad <= 0) {
            showToast('La cantidad debe ser mayor a 0', 'error');
            return;
        }

        // Get CSRF token
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.content : '';

        const formData = new FormData();
        formData.append('_csrf_token', csrfToken);
        formData.append('producto_id', productId);
        formData.append('tipo', tipo);
        formData.append('cantidad', cantidad);
        formData.append('referencia', referencia || `Mov. rápido desde escáner`);

        fetch(`${BASE}/movimientos/rapido`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                playBeep('success');
                showToast(`✅ ${tipo === 'entrada' ? 'Entrada' : 'Salida'} de ${cantidad} registrada. Nuevo stock: ${data.newStock}`, 'success');
                // Update stock display
                const form = document.getElementById(`quickMoveForm-${productId}`);
                if (form) form.classList.add('d-none');
                // Re-search to refresh data
                searchProduct(manualInput.value.trim());
            } else {
                showToast(data.error || 'Error al registrar movimiento', 'error');
            }
        })
        .catch(() => {
            showToast('Error de conexión al registrar movimiento', 'error');
        });
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
        html += `
            <div class="d-flex gap-2 flex-wrap mt-3">
                <button type="button" class="btn btn-secondary btn-resume-scan w-100">
                    <i class="bi bi-camera me-1"></i>Escanear de Nuevo
                </button>
            </div>`;
        resultDiv.innerHTML = html;
        resultDiv.classList.remove('d-none');
        saveState(html);
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
            if (lookup?.imagen_url) params.set('imagen_url', lookup.imagen_url);

            html += `
                    <div class="d-flex flex-wrap gap-2">
                        <a href="${createUrl}?${params.toString()}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Registrar Producto
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-retry-lookup" data-codigo="${escapeHtml(codigo)}">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reintentar búsqueda externa
                        </button>
                        <button type="button" class="btn btn-secondary btn-resume-scan">
                            <i class="bi bi-camera me-1"></i>Escanear de Nuevo
                        </button>
                    </div>`;
        } else {
            html += `
                    <div class="alert alert-danger py-2 mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-lock-fill"></i>
                        <small>No tiene permisos para crear productos. Contacte al administrador.</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-secondary btn-resume-scan w-100">
                            <i class="bi bi-camera me-1"></i>Escanear de Nuevo
                        </button>
                    </div>`;
        }

        html += `
                </div>
            </div>`;

        resultDiv.innerHTML = html;
        resultDiv.classList.remove('d-none');
        saveState(html);
    }

    // Reintentar búsqueda en APIs externas
    function retryLookup(codigo) {
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

    // Restore state on load
    function restoreState() {
        const savedResultHTML = sessionStorage.getItem('invsys_scan_result');
        if (savedResultHTML) {
            resultDiv.innerHTML = savedResultHTML;
            resultDiv.classList.remove('d-none');
        }
    }

    // Initialize
    renderHistory();
    restoreState();
});
