<!-- Scanner Styles -->
<style>
    .scanner-viewport {
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-radius: 14px;
        overflow: hidden;
        border: 3px solid var(--bs-border-color);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .scanner-viewport.scanning {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        animation: scannerPulse 1.5s ease-in-out infinite;
    }
    .scanner-viewport.detected {
        border-color: #22c55e !important;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.25) !important;
        animation: none;
    }
    @keyframes scannerPulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
        50%      { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.08); }
    }
    .scanner-status {
        text-align: center;
        margin-top: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .scanner-status.scanning { color: #ef4444; }
    .scanner-status.detected { color: #22c55e; }
    .scanner-viewport #reader video { border-radius: 0 !important; }
    /* Hide the library's default shaded region borders */
    #reader #qr-shaded-region { border-color: rgba(255,255,255,0.3) !important; }
</style>

<!-- CSRF Meta for AJAX -->
<meta name="csrf-token" content="<?= $csrfToken ?? '' ?>">

<!-- Scanner Interface -->
<div class="row g-3">
    <!-- Main Scanner Column -->
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
                    <div class="scanner-viewport" id="scannerViewport">
                        <div id="reader"></div>
                    </div>
                    <div class="scanner-status" id="scannerStatus">
                        <i class="bi bi-info-circle me-1"></i>Apunte la cámara al código de barras del producto
                    </div>
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
                    <div class="col-md-3">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">1</span>
                            <small>Haga clic en <strong>Activar Cámara</strong> o escriba el SKU directamente.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">2</span>
                            <small>Apunte al <strong>código de barras</strong> del producto.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">3</span>
                            <small>Escuche el <strong>sonido</strong>: agudo = encontrado, grave = no encontrado.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-primary rounded-pill">4</span>
                            <small>Use <strong>Movimiento Rápido</strong> para registrar entradas/salidas sin salir.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan History Column -->
    <div class="col-lg-4">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de Escaneos</h6>
            </div>
            <div class="card-body p-0" id="scanHistory" style="max-height: 500px; overflow-y: auto;">
                <div class="text-center text-muted py-3">
                    <i class="bi bi-clock-history fs-4 d-block mb-1"></i>
                    <small>Los escaneos aparecerán aquí</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script src="<?= asset('js/escaner.js') ?>?v=<?= ASSET_VERSION ?>"></script>
