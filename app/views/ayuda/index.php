



<div class="content-wrapper bg-light">
    <!-- Header Banner -->
    <div class="docs-header shadow-sm">
        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2 text-white"><i class="bi bi-journal-text me-3"></i>Centro de Ayuda Oficial</h2>
                    <p class="mb-0 text-white-50 fs-5">Documentación técnica y guías operativas de InvSys Enterprise</p>
                </div>
                <div>
                    <a href="<?= url('ayuda/pdf') ?>" class="btn btn-light btn-lg shadow-sm text-primary fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Descargar PDF
                    </a>
                </div>
            </div>
        </div>
        <!-- Decorative bg -->
        <i class="bi bi-box-seam position-absolute text-white" style="font-size: 15rem; right: -20px; top: -50px; opacity: 0.1;"></i>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="docs-sidebar pe-2">
                <h6 class="text-uppercase text-muted fw-bold mb-3 ps-3" style="font-size: 0.75rem; letter-spacing: 1px;">Índice de Contenido</h6>
                <nav id="docs-navbar">
                    <a href="#intro" class="docs-nav-item active"><i class="bi bi-house-door me-2"></i>1. Introducción</a>
                    <a href="#dashboard" class="docs-nav-item"><i class="bi bi-speedometer2 me-2"></i>2. Dashboard y KPIs</a>
                    <a href="#catalogos" class="docs-nav-item"><i class="bi bi-tags me-2"></i>3. Catálogos Base</a>
                    <a href="#productos" class="docs-nav-item"><i class="bi bi-box me-2"></i>4. Gestión de Productos</a>
                    <a href="#compras" class="docs-nav-item"><i class="bi bi-cart me-2"></i>5. Compras y Proveedores</a>
                    <a href="#movimientos" class="docs-nav-item"><i class="bi bi-arrow-left-right me-2"></i>6. Entradas y Salidas</a>
                    <a href="#auditoria" class="docs-nav-item"><i class="bi bi-upc-scan me-2"></i>7. Conteo y Auditorías</a>
                    <a href="#logistica" class="docs-nav-item"><i class="bi bi-truck me-2"></i>8. Logística Interna</a>
                    <a href="#reportes" class="docs-nav-item"><i class="bi bi-graph-up me-2"></i>9. Análisis y Reportes</a>
                    <a href="#admin" class="docs-nav-item"><i class="bi bi-shield-lock me-2"></i>10. Administración</a>
                    <a href="#faq" class="docs-nav-item"><i class="bi bi-chat-left-text me-2"></i>11. Preguntas Frecuentes</a>
                </nav>

                <div class="mt-5 p-3 bg-white rounded-3 border">
                    <h6 class="fw-bold"><i class="bi bi-life-preserver text-primary me-2"></i>¿Necesitas más ayuda?</h6>
                    <p class="text-muted small mb-3">Si este manual no resuelve tu problema, contacta al equipo técnico.</p>
                    <a href="<?= url('ayuda/soporte') ?>" class="btn btn-outline-primary btn-sm w-100">Abrir Ticket de Soporte</a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            
            <div id="intro" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-3 text-dark">1. Introducción al WMS</h3>
                        <p class="text-muted fs-5 mb-4"><strong>InvSys Enterprise</strong> es un Sistema de Gestión de Almacenes (WMS) de nivel corporativo.</p>
                        <p>Diseñado para mantener un registro inmutable de todas las transacciones de inventario, el sistema garantiza una trazabilidad total desde el momento en que un producto ingresa al almacén hasta que es despachado o consumido.</p>
                        <div class="alert alert-primary mt-4 border-0 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-lightbulb-fill me-2"></i><strong>Tip de Uso:</strong> Utiliza siempre el buscador global en la parte superior para encontrar productos rápidamente por su SKU o Nombre.
                        </div>
                    </div>
                </div>
            </div>

            <div id="dashboard" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">2. Dashboard y KPIs <span class="docs-badge">Principal</span></h3>
                        <p>El Dashboard es el centro de mando. Al iniciar sesión, verás indicadores clave (KPIs) en tiempo real:</p>
                        <ul class="list-group list-group-flush mt-4">
                            <li class="list-group-item px-0 py-3 border-light">
                                <h6 class="fw-bold"><i class="bi bi-box-fill text-primary me-2"></i>Total de Productos</h6>
                                <p class="text-muted mb-0 ms-4">La variedad de SKUs únicos registrados en el sistema, independientemente de sus existencias.</p>
                            </li>
                            <li class="list-group-item px-0 py-3 border-light">
                                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Alertas de Stock Mínimo</h6>
                                <p class="text-muted mb-0 ms-4">Productos que han caído por debajo del umbral de seguridad y requieren reabastecimiento urgente.</p>
                            </li>
                            <li class="list-group-item px-0 py-3 border-light">
                                <h6 class="fw-bold"><i class="bi bi-cash-stack text-success me-2"></i>Valor del Inventario</h6>
                                <p class="text-muted mb-0 ms-4">El capital total invertido, calculado multiplicando el stock actual por el precio unitario de cada producto.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="catalogos" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">3. Catálogos Base</h3>
                        <p>Antes de registrar productos, es vital configurar la estructura lógica de su almacén mediante los catálogos base.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-tags-fill me-2"></i>Categorías</h6>
                                    <p class="text-muted small mb-0">Agrupan los productos para reportes y filtros. Ej: <em>Electrónicos, Perecederos, Herramientas.</em></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones</h6>
                                    <p class="text-muted small mb-0">Espacios físicos en su almacén. Ej: <em>Pasillo 1, Estante A, Almacén Central.</em></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-truck me-2"></i>Proveedores</h6>
                                    <p class="text-muted small mb-0">Entidades que suministran los artículos. Indispensable para las órdenes de compra.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-building me-2"></i>Departamentos</h6>
                                    <p class="text-muted small mb-0">Áreas internas de la empresa (Ej. <em>Mantenimiento, TI</em>) que solicitarán requisiciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="productos" class="docs-section">
                <div class="docs-card border-start border-4 border-primary">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">4. Gestión de Productos</h3>
                        
                        <h5 class="fw-bold mt-4">Creación de un Producto Nuevo</h5>
                        <div class="mt-3">
                            <div class="d-flex align-items-start mb-3">
                                <span class="step-number">1</span>
                                <div><p class="mb-0 text-muted">Vaya a <strong>Productos</strong> y haga clic en <strong>+ Nuevo Producto</strong>.</p></div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <span class="step-number">2</span>
                                <div><p class="mb-0 text-muted">Llene la <strong>Información Básica</strong>: Nombre, SKU (código único), Categoría y Ubicación por defecto.</p></div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <span class="step-number">3</span>
                                <div>
                                    <p class="mb-1 text-muted">Establezca los <strong>Límites y Precios</strong>:</p>
                                    <ul class="text-muted small">
                                        <li><strong>Stock Mínimo:</strong> Si el stock cae por debajo de este número, el sistema emitirá una alerta roja.</li>
                                        <li><strong>Stock Máximo:</strong> Ayuda a prevenir sobre-inventario.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <span class="step-number">4</span>
                                <div>
                                    <p class="mb-0 text-muted"><strong>Control de Lotes (FEFO):</strong> Si el producto tiene caducidad, active "Requiere Gestión por Lotes y Vencimientos". Podrá registrar un lote inicial y el Kardex priorizará las salidas de los lotes más próximos a vencer.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <span class="step-number">5</span>
                                <div>
                                    <p class="mb-0 text-muted"><strong>Vincular Proveedores:</strong> Use la pestaña de Proveedores para enlazarlos, definiendo costo referencial y tiempos de entrega.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="compras" class="docs-section">
                <div class="docs-card border-start border-4 border-success">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">5. Compras y Proveedores</h3>
                        <p class="text-muted">El módulo de Compras formaliza el abastecimiento y genera documentos legales.</p>
                        
                        <h6 class="fw-bold mt-4 text-success"><i class="bi bi-file-earmark-pdf me-2"></i>Generar Órdenes de Compra</h6>
                        <ul class="text-muted small">
                            <li>Vaya a <strong>Compras > Nueva Orden</strong>. Seleccione al Proveedor; el sistema cargará los productos que tiene vinculados.</li>
                            <li>Defina cantidades. El sistema calculará los totales y generará un PDF profesional listo para enviar por email.</li>
                            <li>La orden nace en estado <strong>Borrador</strong>, avanza a <strong>Pendiente</strong> y finaliza en <strong>Recibida</strong> (lo cual inyecta el stock al Kardex automáticamente).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="movimientos" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">6. Movimientos (Entradas y Salidas)</h3>
                        <p class="text-muted">El Kardex del sistema se alimenta exclusivamente de este módulo. <strong>Nunca se debe editar el stock manualmente sin dejar rastro.</strong></p>
                        
                        <h5 class="fw-bold mt-4 text-success"><i class="bi bi-box-arrow-in-right me-2"></i>Registrar Entrada</h5>
                        <p class="text-muted small">Úselo cuando reciba mercancía directa que no proviene de una Orden de Compra.</p>
                        <ol class="text-muted">
                            <li>Vaya a <strong>Movimientos > Entradas</strong>.</li>
                            <li>Busque el producto e ingrese la cantidad.</li>
                            <li>Si el producto maneja lotes, el sistema exigirá el <em>Número de Lote</em> y la <em>Fecha de Vencimiento</em>.</li>
                            <li>Opcionalmente asocie un documento de respaldo (Ej. Factura #1234).</li>
                        </ol>

                        <h5 class="fw-bold mt-4 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Registrar Salida</h5>
                        <p class="text-muted small">Úselo para mermas, consumo directo, o ventas no automatizadas.</p>
                        <p class="text-muted mb-0">Seleccione el producto, la cantidad a retirar y un motivo de salida obligatorio.</p>
                    </div>
                </div>
            </div>

            <div id="auditoria" class="docs-section">
                <div class="docs-card border-start border-4 border-warning">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">7. Auditoría y Conteo Físico</h3>
                        <p>El módulo de Conteo Cíclico le permite conciliar el stock registrado virtualmente en el sistema contra lo que realmente existe físicamente en las estanterías.</p>
                        
                        <div class="mt-4 p-4 bg-light rounded">
                            <h6 class="fw-bold">Flujo de Auditoría:</h6>
                            <div class="d-flex align-items-start mt-3 mb-2">
                                <span class="step-number text-warning bg-warning bg-opacity-10">1</span>
                                <p class="mb-0 text-muted"><strong>Iniciar Sesión:</strong> Vaya a Conteo Físico > Nuevo Conteo. Elija si desea contar todo el almacén, o solo un pasillo (Ubicación) en específico.</p>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <span class="step-number text-warning bg-warning bg-opacity-10">2</span>
                                <p class="mb-0 text-muted"><strong>Ejecución (Escáner):</strong> Los operarios pueden usar el módulo "Escáner" (compatible con lectores de códigos de barras físicos o cámaras) para sumar unidades rápidamente al conteo activo.</p>
                            </div>
                            <div class="d-flex align-items-start">
                                <span class="step-number text-warning bg-warning bg-opacity-10">3</span>
                                <p class="mb-0 text-muted"><strong>Conciliación:</strong> Al cerrar el conteo, el sistema resaltará en rojo los faltantes y en verde los sobrantes. Al hacer clic en "Aplicar Ajustes", el sistema auto-generará transacciones en el Kardex para igualar el sistema a la realidad, bajo el concepto de "Ajuste por Auditoría".</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="logistica" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">8. Logística Interna (Requisiciones y Devoluciones)</h3>
                        <p>Diseñado para empresas donde distintos departamentos (Producción, Mantenimiento) consumen materiales del almacén central.</p>
                        
                        <h6 class="fw-bold mt-4"><i class="bi bi-file-earmark-text text-primary me-2"></i>Crear una Requisición</h6>
                        <ul class="text-muted">
                            <li>En el menú <strong>Requisiciones</strong>, seleccione <em>Nueva Requisición</em>.</li>
                            <li>Seleccione el departamento solicitante y la persona encargada.</li>
                            <li>Añada productos al "carrito" de la requisición.</li>
                            <li>Al procesarla, se genera un PDF imprimible como vale de salida, y el stock es descontado inmediatamente.</li>
                        </ul>
                        
                        <h6 class="fw-bold mt-4"><i class="bi bi-arrow-return-left text-danger me-2"></i>Gestión de Devoluciones</h6>
                        <ul class="text-muted small">
                            <li>Si un departamento devuelve material no utilizado, vaya a <strong>Devoluciones > Nueva Devolución</strong>.</li>
                            <li>Añada la cantidad devuelta y el estado del producto (<strong>Bueno / Dañado</strong>).</li>
                            <li>Si está en buen estado, el stock retorna al Kardex como "Disponible". Si está dañado, se registra como "Merma" automáticamente.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="reportes" class="docs-section">
                <div class="docs-card">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">9. Análisis e Inteligencia (Reportes)</h3>
                        <p>Los reportes son herramientas críticas para la toma de decisiones financieras.</p>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reporte</th>
                                        <th>Propósito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Kardex General</td>
                                        <td class="text-muted">Muestra el "libro mayor" de un producto: todas sus entradas, salidas, fechas y responsables a lo largo del tiempo.</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Análisis ABC</td>
                                        <td class="text-muted">Clasifica el inventario por su valor económico e impacto. <strong>(A)</strong> son productos vitales que requieren auditoría constante, <strong>(C)</strong> son de bajo impacto.</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Rotación</td>
                                        <td class="text-muted">Identifica artículos de alta demanda basándose en la frecuencia de salidas en un rango de fechas.</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Inventario Muerto</td>
                                        <td class="text-muted">Detecta productos que no han tenido ninguna salida en los últimos X meses, ayudando a liquidar stock estancado.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="admin" class="docs-section">
                <div class="docs-card border-start border-4 border-danger">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">10. Administración y Seguridad <span class="docs-badge bg-danger text-white">Solo Admin</span></h3>
                        
                        <p class="text-muted mb-4">El panel de configuración permite gestionar el núcleo del sistema operativo y sus barreras de protección.</p>
                        
                        <h6 class="fw-bold">Usuarios y Permisos</h6>
                        <p class="text-muted small">Cree cuentas y asigne roles (Admin, Almacenista, Auditor). Cada rol restringe el acceso a módulos específicos a través de middlewares de seguridad (RBAC).</p>

                        <h6 class="fw-bold">Copias de Seguridad (Backups)</h6>
                        <p class="text-muted small">Recomendamos ir a <strong>Administración > Backups</strong> al menos una vez por semana para descargar un volcado completo (SQL) de la base de datos y protegerse contra pérdidas de información.</p>

                        <h6 class="fw-bold">Logs de Auditoría (Seguridad)</h6>
                        <p class="text-muted small mb-0">Un registro de eventos 100% inmutable. Todo inicio de sesión, cambio de configuración o intento de acceso denegado quedará registrado con la dirección IP del usuario.</p>
                    </div>
                </div>
            </div>

            <div id="faq" class="docs-section">
                <div class="docs-card border-start border-4 border-info">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-dark">11. Preguntas Frecuentes (FAQ)</h3>
                        <p class="text-muted mb-4">Respuestas rápidas a las dudas más comunes de nuestros usuarios.</p>

                        <div class="accordion accordion-flush border rounded overflow-hidden" id="accordionFAQ">
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        ¿Cómo corrijo un error si me equivoqué al registrar una entrada de stock?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted">
                                        Por cuestiones de auditoría inmutable, <strong>no se puede borrar</strong> ni editar una transacción pasada del Kardex. Para solucionarlo, debe registrar un <strong>Movimiento de Salida</strong> seleccionando el motivo <em>"Ajuste de Inventario / Corrección"</em> por la misma cantidad que ingresó por error.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        ¿Por qué un producto no me aparece en la ventana de impresión masiva de etiquetas?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted">
                                        Verifique que el producto tenga un <strong>SKU válido asignado</strong>. El sistema de códigos de barras (Code128) ignora automáticamente cualquier producto que carezca de un código único para prevenir impresiones en blanco.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        ¿Qué significa cuando el stock aparece en números negativos?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted">
                                        Ocurre cuando se registran salidas (ventas o despachos) <strong>antes</strong> de registrar las entradas correspondientes del proveedor. Significa que tiene mercancía física, pero olvidó ingresarla al sistema. Debe hacer una entrada para regularizarlo a positivo.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        ¿Puedo cambiar la categoría de un producto que ya tiene movimientos en el Kardex?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted">
                                        Sí, puede editar el producto y reasignarlo a otra categoría sin problema. Sin embargo, los reportes pasados filtrados por esa fecha podrían reflejar el cambio estructural.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                        Olvidé mi contraseña o no tengo permisos para ciertos módulos.
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted">
                                        Solo el <strong>Administrador Principal</strong> del sistema puede resetear contraseñas o modificar sus niveles de permiso de seguridad (Roles). Por favor, utilice el formulario de Soporte a la izquierda para solicitar ayuda directa al administrador.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



