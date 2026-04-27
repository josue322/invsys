<!-- Configuración del Sistema - Vista organizada por secciones -->
<div class="config-page animate-fadeIn">

    <form method="POST" action="<?= url('configuracion') ?>" id="formConfig" enctype="multipart/form-data">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

        <?php
            // Pre-cargar valores de configuración en un array asociativo
            $cfg = [];
            foreach ($configs as $c) {
                $cfg[$c->clave] = $c->valor ?? '';
            }
        ?>

        <div class="row g-4">

            <!-- ============================================= -->
            <!-- COLUMNA IZQUIERDA -->
            <!-- ============================================= -->
            <div class="col-lg-6">

                <!-- Identidad del Sistema -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: var(--primary-light); color: var(--primary);">
                            <i class="bi bi-brush"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Identidad del sistema</h6>
                            <p class="config-section-desc">Personaliza el nombre, logo y color de tu sistema</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <!-- Logo + Nombre en fila -->
                        <div class="identity-row">
                            <div class="logo-preview-wrapper">
                                <?php $logoPath = $cfg['logo'] ?? ''; ?>
                                <?php if ($logoPath && file_exists(PUBLIC_PATH . '/assets/img/' . $logoPath)): ?>
                                    <img src="<?= asset('img/' . htmlspecialchars($logoPath)) ?>" alt="Logo" class="logo-preview-img" id="logoPreview">
                                <?php else: ?>
                                    <div class="logo-preview-placeholder" id="logoPreview">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="identity-fields">
                                <label class="form-label">Nombre del sistema</label>
                                <input type="text" class="form-control" name="config[nombre_sistema]"
                                       value="<?= htmlspecialchars($cfg['nombre_sistema'] ?? 'InvSys') ?>" placeholder="Ej: InvSys">
                            </div>
                        </div>


                        <!-- Subir logo personalizado -->
                        <div class="config-item mt-3">
                            <label class="form-label">Subir logo personalizado</label>
                            <div class="logo-upload-area" id="logoUploadArea">
                                <input type="file" name="logo" id="logoInput" accept=".png,.jpg,.jpeg,.svg,.webp" hidden>
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>PNG, JPG o SVG — máx. 200×60px</span>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('logoInput').click()">
                                    Seleccionar archivo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empresa y Regionalización -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Empresa y regionalización</h6>
                            <p class="config-section-desc">Moneda, zona horaria y formato de fechas</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Moneda</label>
                                <select class="form-select" name="config[moneda_codigo]">
                                    <?php $mc = $cfg['moneda_codigo'] ?? 'MXN'; ?>
                                    <option value="MXN" <?= $mc === 'MXN' ? 'selected' : '' ?>>MXN — Peso mexicano</option>
                                    <option value="USD" <?= $mc === 'USD' ? 'selected' : '' ?>>USD — Dólar americano</option>
                                    <option value="EUR" <?= $mc === 'EUR' ? 'selected' : '' ?>>EUR — Euro</option>
                                    <option value="PEN" <?= $mc === 'PEN' ? 'selected' : '' ?>>PEN — Sol peruano</option>
                                    <option value="COP" <?= $mc === 'COP' ? 'selected' : '' ?>>COP — Peso colombiano</option>
                                    <option value="ARS" <?= $mc === 'ARS' ? 'selected' : '' ?>>ARS — Peso argentino</option>
                                    <option value="CLP" <?= $mc === 'CLP' ? 'selected' : '' ?>>CLP — Peso chileno</option>
                                    <option value="BRL" <?= $mc === 'BRL' ? 'selected' : '' ?>>BRL — Real brasileño</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Símbolo de moneda</label>
                                <input type="text" class="form-control" name="config[moneda_simbolo]"
                                       value="<?= htmlspecialchars($cfg['moneda_simbolo'] ?? '$') ?>" maxlength="5">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Zona horaria</label>
                                <select class="form-select" name="config[zona_horaria]">
                                    <?php $tz = $cfg['zona_horaria'] ?? 'America/Lima'; ?>
                                    <option value="America/Mexico_City" <?= $tz === 'America/Mexico_City' ? 'selected' : '' ?>>América/México (UTC-6)</option>
                                    <option value="America/Lima" <?= $tz === 'America/Lima' ? 'selected' : '' ?>>América/Lima (UTC-5)</option>
                                    <option value="America/Bogota" <?= $tz === 'America/Bogota' ? 'selected' : '' ?>>América/Bogotá (UTC-5)</option>
                                    <option value="America/New_York" <?= $tz === 'America/New_York' ? 'selected' : '' ?>>América/New York (UTC-5)</option>
                                    <option value="America/Argentina/Buenos_Aires" <?= $tz === 'America/Argentina/Buenos_Aires' ? 'selected' : '' ?>>América/Buenos Aires (UTC-3)</option>
                                    <option value="America/Sao_Paulo" <?= $tz === 'America/Sao_Paulo' ? 'selected' : '' ?>>América/São Paulo (UTC-3)</option>
                                    <option value="Europe/Madrid" <?= $tz === 'Europe/Madrid' ? 'selected' : '' ?>>Europa/Madrid (UTC+1)</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Formato de fecha</label>
                                <select class="form-select" name="config[formato_fecha]">
                                    <?php $df = $cfg['formato_fecha'] ?? 'DD/MM/YYYY'; ?>
                                    <option value="DD/MM/YYYY" <?= $df === 'DD/MM/YYYY' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                    <option value="MM/DD/YYYY" <?= $df === 'MM/DD/YYYY' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                    <option value="YYYY-MM-DD" <?= $df === 'YYYY-MM-DD' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventario y Operación -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Inventario y operación</h6>
                            <p class="config-section-desc">Stock mínimo, paginación y comportamiento del inventario</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Stock mínimo global</label>
                                <input type="number" class="form-control" name="config[stock_minimo_global]"
                                       value="<?= htmlspecialchars($cfg['stock_minimo_global'] ?? '5') ?>" min="0">
                                <small class="text-muted">Valor por defecto para nuevos productos</small>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Registros por página
                                    <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Valor por defecto global. Cada usuario puede ajustar temporalmente la cantidad de registros desde el selector en cada listado."></i>
                                </label>
                                <select class="form-select" name="config[registros_por_pagina]">
                                    <?php $rp = $cfg['registros_por_pagina'] ?? '15'; ?>
                                    <option value="10" <?= $rp === '10' ? 'selected' : '' ?>>10 registros</option>
                                    <option value="15" <?= $rp === '15' ? 'selected' : '' ?>>15 registros</option>
                                    <option value="25" <?= $rp === '25' ? 'selected' : '' ?>>25 registros</option>
                                    <option value="50" <?= $rp === '50' ? 'selected' : '' ?>>50 registros</option>
                                    <option value="100" <?= $rp === '100' ? 'selected' : '' ?>>100 registros</option>
                                </select>
                            </div>
                        </div>

                        <!-- Toggle: Permitir stock negativo -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Permitir stock negativo</span>
                                <span class="config-toggle-desc">Permitir salidas aún si el stock es insuficiente</span>
                            </div>
                            <div class="toggle-switch" data-config="permitir_stock_negativo" data-checked="<?= ($cfg['permitir_stock_negativo'] ?? '0') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[permitir_stock_negativo]" value="<?= ($cfg['permitir_stock_negativo'] ?? '0') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['permitir_stock_negativo'] ?? '0') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Toggle: Reorden automático -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Reorden automático</span>
                                <span class="config-toggle-desc">Generar alerta de reorden al alcanzar stock mínimo</span>
                            </div>
                            <div class="toggle-switch" data-config="reorden_automatico" data-checked="<?= ($cfg['reorden_automatico'] ?? '1') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[reorden_automatico]" value="<?= ($cfg['reorden_automatico'] ?? '1') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['reorden_automatico'] ?? '1') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================= -->
            <!-- COLUMNA DERECHA -->
            <!-- ============================================= -->
            <div class="col-lg-6">

                <!-- Interfaz de Usuario -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <i class="bi bi-palette"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Interfaz de usuario</h6>
                            <p class="config-section-desc">Tema, densidad visual y animaciones</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <!-- Toggle: Modo Oscuro -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Modo oscuro</span>
                                <span class="config-toggle-desc">Tema oscuro en toda la interfaz</span>
                            </div>
                            <div class="toggle-switch" data-config="tema_defecto" data-on="dark" data-off="light" data-checked="<?= ($cfg['tema_defecto'] ?? 'light') === 'dark' ? '1' : '0' ?>">
                                <input type="hidden" name="config[tema_defecto]" value="<?= $cfg['tema_defecto'] ?? 'light' ?>">
                                <span class="toggle-track <?= ($cfg['tema_defecto'] ?? 'light') === 'dark' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Toggle: Sidebar Colapsable -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Sidebar colapsable</span>
                                <span class="config-toggle-desc">Contraer menú lateral automáticamente</span>
                            </div>
                            <div class="toggle-switch" data-config="sidebar_colapsable" data-checked="<?= ($cfg['sidebar_colapsable'] ?? '1') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[sidebar_colapsable]" value="<?= ($cfg['sidebar_colapsable'] ?? '1') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['sidebar_colapsable'] ?? '1') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Toggle: Densidad compacta -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Densidad compacta</span>
                                <span class="config-toggle-desc">Reducir espacio entre filas y elementos</span>
                            </div>
                            <div class="toggle-switch" data-config="densidad_compacta" data-checked="<?= ($cfg['densidad_compacta'] ?? '0') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[densidad_compacta]" value="<?= ($cfg['densidad_compacta'] ?? '0') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['densidad_compacta'] ?? '0') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Toggle: Animaciones -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Animaciones</span>
                                <span class="config-toggle-desc">Transiciones suaves de UI</span>
                            </div>
                            <div class="toggle-switch" data-config="animaciones" data-checked="<?= ($cfg['animaciones'] ?? '1') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[animaciones]" value="<?= ($cfg['animaciones'] ?? '1') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['animaciones'] ?? '1') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notificaciones -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Notificaciones</h6>
                            <p class="config-section-desc">Alertas por correo y configuraciones de aviso</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <!-- Toggle: Alertas por correo -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Alertas por correo</span>
                                <span class="config-toggle-desc">Stock mínimo y vencimientos</span>
                            </div>
                            <div class="toggle-switch" data-config="alertas_email" data-checked="<?= ($cfg['alertas_email'] ?? '0') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[alertas_email]" value="<?= ($cfg['alertas_email'] ?? '0') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['alertas_email'] ?? '0') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Toggle: Alertas de seguridad -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Alertas de seguridad</span>
                                <span class="config-toggle-desc">Accesos fallidos, cambios de permisos</span>
                            </div>
                            <div class="toggle-switch" data-config="alertas_seguridad" data-checked="<?= ($cfg['alertas_seguridad'] ?? '1') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[alertas_seguridad]" value="<?= ($cfg['alertas_seguridad'] ?? '1') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['alertas_seguridad'] ?? '1') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Seguridad</h6>
                            <p class="config-section-desc">Control de acceso, sesiones y auditoría</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Máx. intentos de login</label>
                                <input type="number" class="form-control" name="config[intentos_login_max]"
                                       value="<?= htmlspecialchars($cfg['intentos_login_max'] ?? '5') ?>" min="1" max="20">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Bloqueo por intentos (min)</label>
                                <input type="number" class="form-control" name="config[tiempo_bloqueo_minutos]"
                                       value="<?= htmlspecialchars($cfg['tiempo_bloqueo_minutos'] ?? '15') ?>" min="1" max="120">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Duración de sesión (seg)</label>
                                <input type="number" class="form-control" name="config[session_lifetime]"
                                       value="<?= htmlspecialchars($cfg['session_lifetime'] ?? '3600') ?>" min="300">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Retención de logs</label>
                                <select class="form-select" name="config[retencion_logs]">
                                    <?php $rl = $cfg['retencion_logs'] ?? '90'; ?>
                                    <option value="30" <?= $rl === '30' ? 'selected' : '' ?>>30 días</option>
                                    <option value="60" <?= $rl === '60' ? 'selected' : '' ?>>60 días</option>
                                    <option value="90" <?= $rl === '90' ? 'selected' : '' ?>>90 días</option>
                                    <option value="180" <?= $rl === '180' ? 'selected' : '' ?>>180 días</option>
                                    <option value="365" <?= $rl === '365' ? 'selected' : '' ?>>365 días</option>
                                </select>
                            </div>
                        </div>

                        <!-- Toggle: Permitir registro público -->
                        <div class="config-toggle-item">
                            <div>
                                <span class="config-toggle-label">Permitir registro público</span>
                                <span class="config-toggle-desc">Los usuarios pueden crear su propia cuenta desde la página de login</span>
                            </div>
                            <div class="toggle-switch" data-config="permitir_registro" data-checked="<?= ($cfg['permitir_registro'] ?? '0') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[permitir_registro]" value="<?= ($cfg['permitir_registro'] ?? '0') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['permitir_registro'] ?? '0') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Rol asignado al registro público (visible solo si el registro está activo) -->
                        <div class="config-item mt-3" id="rolRegistroWrapper" style="display: <?= ($cfg['permitir_registro'] ?? '0') === '1' ? 'block' : 'none' ?>;">
                            <label class="form-label"><i class="bi bi-person-badge me-1"></i>Rol para usuarios registrados</label>
                            <select class="form-select" name="config[rol_registro_publico]">
                                <?php
                                    $rolRegistro = $cfg['rol_registro_publico'] ?? '3';
                                    foreach ($roles as $rol):
                                ?>
                                <option value="<?= $rol->id ?>" <?= (string)$rol->id === $rolRegistro ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol->nombre) ?> — <?= htmlspecialchars($rol->descripcion ?? '') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Rol asignado automáticamente a nuevos usuarios que se registren de forma pública</small>
                        </div>
                    </div>
                </div>

                <!-- Correo Electrónico (SMTP) -->
                <div class="config-section">
                    <div class="config-section-header">
                        <div class="config-section-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-envelope-at"></i>
                        </div>
                        <div>
                            <h6 class="config-section-title">Correo electrónico (SMTP)</h6>
                            <p class="config-section-desc">Configuración del servidor de correo saliente</p>
                        </div>
                    </div>

                    <div class="config-section-body">
                        <!-- Toggle: Usar SMTP personalizado -->
                        <div class="config-toggle-item mb-3">
                            <div>
                                <span class="config-toggle-label">Usar servidor SMTP</span>
                                <span class="config-toggle-desc">Activar para usar un servidor SMTP en vez del mail() nativo de PHP</span>
                            </div>
                            <div class="toggle-switch" data-config="smtp_activo" data-checked="<?= ($cfg['smtp_activo'] ?? '0') === '1' ? '1' : '0' ?>">
                                <input type="hidden" name="config[smtp_activo]" value="<?= ($cfg['smtp_activo'] ?? '0') === '1' ? '1' : '0' ?>">
                                <span class="toggle-track <?= ($cfg['smtp_activo'] ?? '0') === '1' ? 'active' : '' ?>">
                                    <span class="toggle-thumb"></span>
                                </span>
                            </div>
                        </div>

                        <div id="smtpFields">
                            <div class="row g-3 mb-3">
                                <div class="col-sm-8">
                                    <label class="form-label">Servidor SMTP</label>
                                    <input type="text" class="form-control" name="config[smtp_host]"
                                           value="<?= htmlspecialchars($cfg['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Puerto</label>
                                    <select class="form-select" name="config[smtp_port]">
                                        <?php $sp = $cfg['smtp_port'] ?? '587'; ?>
                                        <option value="25" <?= $sp === '25' ? 'selected' : '' ?>>25 (Sin cifrado)</option>
                                        <option value="465" <?= $sp === '465' ? 'selected' : '' ?>>465 (SSL)</option>
                                        <option value="587" <?= $sp === '587' ? 'selected' : '' ?>>587 (TLS)</option>
                                        <option value="2525" <?= $sp === '2525' ? 'selected' : '' ?>>2525 (Alternativo)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Encriptación</label>
                                    <select class="form-select" name="config[smtp_encryption]">
                                        <?php $se = $cfg['smtp_encryption'] ?? 'tls'; ?>
                                        <option value="tls" <?= $se === 'tls' ? 'selected' : '' ?>>TLS (Recomendado)</option>
                                        <option value="ssl" <?= $se === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="none" <?= $se === 'none' ? 'selected' : '' ?>>Sin encriptación</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Autenticación</label>
                                    <select class="form-select" name="config[smtp_auth]">
                                        <?php $sa = $cfg['smtp_auth'] ?? '1'; ?>
                                        <option value="1" <?= $sa === '1' ? 'selected' : '' ?>>Sí (usuario/contraseña)</option>
                                        <option value="0" <?= $sa === '0' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Usuario SMTP</label>
                                    <input type="text" class="form-control" name="config[smtp_username]"
                                           value="<?= htmlspecialchars($cfg['smtp_username'] ?? '') ?>" placeholder="tu@gmail.com" autocomplete="off">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Contraseña SMTP</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="config[smtp_password]" id="smtpPass"
                                               value="<?= htmlspecialchars($cfg['smtp_password'] ?? '') ?>" placeholder="••••••••" autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="smtpPass">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Para Gmail usa una "Contraseña de aplicación"</small>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Email remitente</label>
                                    <input type="email" class="form-control" name="config[mail_from_address]"
                                           value="<?= htmlspecialchars($cfg['mail_from_address'] ?? '') ?>" placeholder="noreply@miempresa.com">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Nombre remitente</label>
                                    <input type="text" class="form-control" name="config[mail_from_name]"
                                           value="<?= htmlspecialchars($cfg['mail_from_name'] ?? '') ?>" placeholder="Mi Empresa">
                                </div>
                            </div>

                            <!-- Botón de prueba -->
                            <div class="d-flex align-items-center gap-2 mt-3 p-3 rounded" style="background: var(--bg-secondary);">
                                <i class="bi bi-send text-warning"></i>
                                <small class="text-muted flex-grow-1">Guarda primero, luego envía un correo de prueba para verificar la configuración.</small>
                                <button type="button" class="btn btn-sm btn-outline-warning" id="btnTestMail">
                                    <i class="bi bi-envelope-check me-1"></i>Probar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Botón guardar fijo -->
        <div class="config-save-bar">
            <button type="submit" class="btn btn-primary btn-lg" id="btnGuardarConfig">
                <i class="bi bi-check-lg me-2"></i>Guardar configuración
            </button>
        </div>
    </form>
</div>


<script id="page-data" type="application/json"><?= json_encode([
    'testMailUrl' => url('configuracion/test-mail'),
    'csrfToken'   => $csrfToken,
]) ?></script>
<script src="<?= asset('js/configuracion.js') ?>?v=<?= ASSET_VERSION ?>"></script>

