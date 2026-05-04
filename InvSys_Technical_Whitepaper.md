# InvSys WMS - Documento Arquitectónico y Estratégico

## 1. Visión General del Sistema
**InvSys** no es un sistema de Punto de Venta (POS) ni una tienda en línea. Es un **Warehouse Management System (WMS)** de grado corporativo, diseñado exclusivamente para la logística interna, la gestión de la cadena de suministro "puertas adentro" y el control de costos operativos.

El sistema rastrea el flujo físico de los bienes desde que llegan de los proveedores hasta que son consumidos o utilizados por los propios empleados y departamentos de la empresa. La moneda de cambio aquí no es el dinero de una venta, sino la **trazabilidad, el ahorro y la prevención de pérdidas**.

---

## 2. El Cliente Ideal (Sectores Objetivo)
Este sistema resuelve problemas millonarios en industrias donde el inventario es un gasto operativo (consumibles) o materia prima, destacando:

1. **Clínicas y Hospitales:** 
   * *El problema:* Medicinas vencidas y falta de control en insumos médicos.
   * *La solución InvSys:* Gestión estricta de Lotes con metodología FEFO (First Expire, First Out). Trazabilidad exacta de qué insumo fue a Quirófano vs. Pediatría.
2. **Manufactura e Industrias:** 
   * *El problema:* Paros de línea de producción por falta de tornillos o piezas esenciales.
   * *La solución InvSys:* Alertas automáticas de stock mínimo. Transferencias exactas de la Bodega Principal a las Líneas de Ensamblaje.
3. **Hotelería y Restaurantes:** 
   * *El problema:* Robo hormiga de sábanas, alimentos, jabones o licores.
   * *La solución InvSys:* Requisiciones formales donde el departamento de Limpieza o Bar debe solicitar y dejar registro de cada artículo retirado del almacén.
4. **Construcción:** 
   * *El problema:* Pérdida de herramientas costosas o cemento en grandes obras.
   * *La solución InvSys:* Kardex auditable para saber qué ingeniero retiró un taladro y en qué fecha, con opción a Devoluciones cuando la obra termina.
5. **Corporativos e Instituciones:** 
   * *El problema:* Desperdicio de material de oficina y papelería.
   * *La solución InvSys:* Centros de costos organizados por Departamentos (RRHH, Sistemas, Gerencia) para medir quién gasta más.

---

## 3. Arquitectura y Stack Tecnológico
InvSys fue construido bajo el paradigma **MVC (Modelo-Vista-Controlador)** sin depender de frameworks pesados de backend (como Laravel o Symfony), lo que garantiza un consumo de RAM casi nulo, extrema velocidad y fácil mantenimiento en cualquier servidor web estándar.

### Stack Backend (Servidor)
* **Lenguaje:** PHP 8.1+ (Tipado estricto, alta velocidad).
* **Patrón de Diseño:** MVC (Model-View-Controller) creado a la medida.
* **Base de Datos:** MySQL / MariaDB con abstracción a través de **PDO (PHP Data Objects)**.
* **Enrutamiento:** Router personalizado basado en expresiones regulares para URLs limpias (`/productos/editar/5`).

### Stack Frontend (Cliente)
* **Motor de Estilos:** Vanilla CSS + **Bootstrap 5.3.3** (Responsive Design, Mobile-First).
* **Interactividad:** JavaScript Vanilla moderno (ES6+).
* **Iconografía:** Bootstrap Icons.
* **Componentes Externos Estratégicos:**
  * `Chart.js`: Para analítica en tiempo real en el Dashboard.
  * `html5-qrcode`: Motor de escaneo de códigos de barras a través de la cámara nativa del celular o tablet.

---

## 4. Flujo Operativo Core (El "Workflow")

El sistema está diseñado para reflejar el movimiento físico de las cajas en la vida real:

### Fase A: Abastecimiento (El inventario llega)
1. **Órdenes de Compra:** El administrador genera un documento pidiendo insumos al *Proveedor*.
2. **Movimiento de Entrada:** Llega el camión. El bodeguero ingresa el material al sistema, asociándolo a un **Lote** (con fecha de caducidad) y colocándolo en una **Ubicación** física específica (Ej. Pasillo 3).

### Fase B: Distribución Interna (El inventario se mueve)
1. **Transferencias:** El bodeguero necesita reacomodar el almacén. Mueve 100 cajas de la "Bodega Norte" a la "Cámara Frigorífica". El sistema deja registro del movimiento sin alterar el balance contable total.
2. **Requisiciones:** El *Departamento* de Sistemas necesita 10 teclados. Hace la solicitud. El bodeguero aprueba, y el stock baja automáticamente.

### Fase C: Auditoría y Cierre (Control y Prevención)
1. **Conteo Físico (Auditoría Cíclica):** El sistema toma una "foto" del inventario actual. El auditor va con su celular escaneando códigos en la bodega. El sistema resalta en rojo/amarillo las diferencias (faltantes o sobrantes) y ajusta automáticamente.
2. **Kardex:** Libro contable intocable. Muestra el historial inmutable de vida de cada producto (Nació -> Se Murió).

---

## 5. Módulo de Seguridad Avanzada (SecOps)
Para garantizar el uso corporativo, InvSys cuenta con barreras de nivel bancario:
* **Tokens CSRF (Cross-Site Request Forgery):** Cada formulario genera un ticket criptográfico de un solo uso que previene ataques de suplantación.
* **CSP (Content Security Policy):** Políticas de cabecera HTTP estrictas que prohíben la ejecución de scripts "inline" maliciosos (cero riesgo de ataques XSS).
* **Auditoría de Actividad (Log):** Si un usuario modifica un producto o resetea una contraseña, el evento queda grabado en la tabla `logs` (Quién, Qué y Cuándo).
* **Consultas Preparadas (PDO):** Es 100% inmune a inyecciones SQL.

---

## 6. Ventajas Competitivas (PROS)
* **Zero-Dependency (Backend):** No requiere `composer install` de paquetes frágiles. Si PHP funciona en el servidor, InvSys funciona.
* **Modo Oscuro Nativo:** Interfaz diseñada con variables CSS automáticas, reduciendo la fatiga visual de los operarios de almacén que trabajan turnos largos.
* **Búsqueda Externa de APIs:** Si se escanea un código de barras desconocido, el sistema auto-consulta *Open Food Facts* y *UPCitemdb* en milisegundos para intentar autocompletar nombre y fotografía.
* **Extremadamente Rápido:** Al no usar ORMs pesados y utilizar paginación a nivel SQL (`LIMIT` / `OFFSET`), el sistema puede consultar historiales de millones de registros en menos de 50 milisegundos.

---

*Documento generado para fines de inducción técnica, ventas corporativas y capacitación operativa.*
