

<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1 fw-bold"><i class="bi bi-headset text-primary me-2"></i>Soporte Técnico</h4>
                    <p class="text-muted mb-0">Contacta directamente al desarrollador del sistema</p>
                </div>
                <div>
                    <a href="<?= url('ayuda') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver al Manual
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= url('ayuda/soporte') ?>" method="POST" id="form-soporte" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="categoria" class="form-label fw-bold">¿En qué podemos ayudarte?</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="" disabled selected>Selecciona una categoría...</option>
                                    <option value="Error Crítico">Error Crítico (El sistema no funciona, pantalla en blanco)</option>
                                    <option value="Error Funcional">Error Funcional (No puedo registrar entrada, error en stock)</option>
                                    <option value="Duda de Uso">Duda de Uso (No sé cómo utilizar un módulo)</option>
                                    <option value="Sugerencia / Mejora">Sugerencia / Mejora (Me gustaría una nueva función)</option>
                                    <option value="Otro">Otro problema</option>
                                </select>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label for="telefono" class="form-label fw-bold">Número de Teléfono (Contacto)</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. +1 234 567 890" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="asunto" class="form-label fw-bold">Asunto breve</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Ej. No puedo registrar una entrada de lote" maxlength="150" required>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold">Descripción detallada</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="6" placeholder="Describe el problema paso a paso. ¿Qué estabas intentando hacer? ¿Qué error apareció en pantalla?" required></textarea>
                            <div class="form-text">Entre más detalles nos des, más rápido podremos ayudarte.</div>
                        </div>

                        <div class="mb-4">
                            <label for="captura" class="form-label fw-bold">Captura de Pantalla (Opcional)</label>
                            <input class="form-control" type="file" id="captura" name="captura" accept="image/jpeg, image/png, image/webp">
                            <div class="form-text text-muted">Formatos soportados: JPG, PNG, WEBP. Peso máximo: 2MB.</div>
                            <div id="file-error" class="text-danger mt-1 fw-bold d-none small"></div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg" id="btn-enviar-soporte">
                                <i class="bi bi-send-fill me-2"></i>Enviar Ticket de Soporte
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3 text-muted small">
                    <i class="bi bi-shield-check me-1"></i>Tus datos de usuario se adjuntarán automáticamente.
                </div>
            </div>
        </div>
    </div>
</div>



