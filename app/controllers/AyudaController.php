<?php
/**
 * InvSys - AyudaController
 * 
 * Controlador para el Centro de Ayuda y Soporte
 */

class AyudaController extends Controller
{
    /**
     * Mostrar el Centro de Ayuda (Manual de Usuario Web)
     */
    public function index()
    {
        $data = [
            'titulo' => 'Centro de Ayuda - Manual',
        ];

        $this->view('ayuda/index', $data);
    }

    /**
     * Mostrar el formulario de contacto a Soporte
     */
    public function soporte()
    {
        $data = [
            'titulo' => 'Contacto a Soporte Técnico',
        ];

        $this->view('ayuda/soporte', $data);
    }

    /**
     * Procesar y enviar el ticket de soporte al desarrollador
     */
    public function enviarTicket()
    {
        // Validar CSRF
        if (!$this->validateCSRF()) {
            $this->redirect('ayuda/soporte?error=' . urlencode('Token de seguridad inválido. Por favor, intenta de nuevo.'));
            return;
        }

        $categoria = trim($_POST['categoria'] ?? '');
        $asunto = trim($_POST['asunto'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? 'No proporcionado');

        if (empty($categoria) || empty($asunto) || empty($descripcion)) {
            $this->redirect('ayuda/soporte?error=' . urlencode('Todos los campos marcados como obligatorios deben ser llenados.'));
            return;
        }

        // Manejo de la captura (opcional)
        $imgTag = '';
        if (isset($_FILES['captura']) && $_FILES['captura']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['captura'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            // Validar tamaño
            if ($file['size'] > $maxSize) {
                $this->redirect('ayuda/soporte?error=' . urlencode('La imagen seleccionada supera el límite de 2MB.'));
                return;
            }

            // Validar MIME type real
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedTypes)) {
                $this->redirect('ayuda/soporte?error=' . urlencode('Formato de imagen no permitido. Solo JPG, PNG o WEBP.'));
                return;
            }

            // Convertir a Base64 para incrustar en el correo (no se guarda en servidor)
            $imageData = base64_encode(file_get_contents($file['tmp_name']));
            $src = 'data:' . $mime . ';base64,' . $imageData;
            $imgTag = "<div style='margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px;'>
                <h4 style='color: #475569; margin-bottom: 10px;'>Captura Adjunta:</h4>
                <img src='{$src}' alt='Captura de pantalla' style='max-width: 100%; height: auto; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>
            </div>";
        }

        // Obtener usuario actual usando el helper global
        $currentUser = currentUser();

        $userNombre = $currentUser['nombre'] ?? 'Usuario Desconocido';
        $userEmail = $currentUser['email'] ?? 'No registrado';
        $userRol = $currentUser['rol_nombre'] ?? 'No definido';

        // Destinatario: el correo del desarrollador
        $developerEmail = 'josuexd123lc@gmail.com';

        // Configurar título del correo
        $mailSubject = "[SOPORTE INVSYS] {$categoria} - {$asunto}";

        // Construir cuerpo del correo en HTML
        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 650px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #ffffff;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #4f46e5; margin: 0;'>InvSys Enterprise</h2>
                <p style='color: #64748b; margin: 5px 0 0;'>Nuevo Ticket de Soporte Técnico</p>
            </div>
            
            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4f46e5;'>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px; color: #334155;'>
                    <tr><td style='padding: 4px 0;'><strong>De:</strong></td><td>{$userNombre} ({$userEmail})</td></tr>
                    <tr><td style='padding: 4px 0;'><strong>Rol:</strong></td><td>{$userRol}</td></tr>
                    <tr><td style='padding: 4px 0;'><strong>Teléfono:</strong></td><td>{$telefono}</td></tr>
                    <tr><td style='padding: 4px 0;'><strong>Categoría:</strong></td><td><span style='background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-weight:bold;'>{$categoria}</span></td></tr>
                </table>
            </div>
            
            <h3 style='color: #1e293b; font-size: 16px; margin-bottom: 10px;'>Asunto: {$asunto}</h3>
            
            <div style='background-color: #f1f5f9; padding: 15px; border-radius: 6px; white-space: pre-wrap; color: #334155; line-height: 1.6; font-size: 14px;'>{$descripcion}</div>
            
            {$imgTag}
            
            <hr style='margin-top: 30px; border: none; border-top: 1px solid #e2e8f0;'>
            <p style='font-size: 12px; color: #94a3b8; text-align: center;'>Este ticket fue generado de forma automática. Al responder, te comunicarás directamente con el usuario afectado.</p>
        </div>
        ";

        // Enviar el correo usando el MailService
        try {
            $mailService = MailService::getInstance();
            $enviado = $mailService->send($developerEmail, $mailSubject, $htmlBody);

            if ($enviado) {
                // Registrar log
                SecurityService::getInstance()->logAction(currentUserId(), 'SOPORTE_TICKET', 'Ayuda', "El usuario ha enviado un ticket de soporte: {$asunto}");

                $this->redirect('ayuda/soporte?success=' . urlencode('¡Tu mensaje ha sido enviado al desarrollador! Te contactaremos pronto.'));
            } else {
                $this->redirect('ayuda/soporte?error=' . urlencode('Hubo un problema al enviar tu mensaje. Por favor, verifica la conexión o intenta más tarde.'));
            }
        } catch (Exception $e) {
            $this->redirect('ayuda/soporte?error=' . urlencode('Error del sistema al enviar el correo: ' . $e->getMessage()));
        }
    }

    /**
     * Descargar el manual en PDF con diseño Premium, Gráficos y FAQ
     */
    public function descargarPdf()
    {
        require_once APP_PATH . '/helpers/PdfGenerator.php';

        $pdf = new PdfGenerator();
        $pdf->AliasNbPages();
        $pdf->setDocumentTitle('Manual de Usuario Oficial');
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 25);

        // --- PORTADA ---
        $pdf->AddPage();

        // Fondo decorativo en la portada
        $pdf->SetFillColor(79, 70, 229); // Primary color (Indigo)
        $pdf->Rect(0, 0, 210, 60, 'F');

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 28);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, PdfGenerator::decode('INVSYS ENTERPRISE'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(200, 200, 255);
        $pdf->Cell(0, 10, PdfGenerator::decode('Sistema de Gestión de Almacenes (WMS)'), 0, 1, 'C');

        $pdf->Ln(40);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->Cell(0, 15, PdfGenerator::decode('Manual de Usuario Oficial'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(108, 117, 125);
        $pdf->Cell(0, 10, PdfGenerator::decode('Versión 2.0 - Generado el: ' . date('d/m/Y')), 0, 1, 'C');

        // --- GRAFICO: FLUJO DE INVENTARIO ---
        $pdf->Ln(25);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(79, 70, 229);
        $pdf->Cell(0, 10, PdfGenerator::decode('Diagrama: Ciclo de Vida del Inventario'), 0, 1, 'C');
        $pdf->Ln(5);

        // Dibujo de diagrama de flujo con rectángulos y líneas
        // Cálculo: 4 cajas × 35mm + 3 gaps × 13mm = 179mm (cabe en 180mm)
        $startX = 16;
        $startY = $pdf->GetY();
        $boxW = 35;
        $boxH = 14;
        $gap = 13;

        $pasos = ['Proveedor', 'Entrada', 'Kardex (Stock)', 'Salida / Despacho'];
        $pdf->SetFont('Arial', 'B', 9);

        for ($i = 0; $i < count($pasos); $i++) {
            // Caja
            $pdf->SetFillColor(238, 242, 255); // Indigo muy claro
            $pdf->SetDrawColor(79, 70, 229);
            $pdf->SetLineWidth(0.5);
            $pdf->Rect($startX, $startY, $boxW, $boxH, 'DF');

            // Texto centrado en la caja
            $pdf->SetTextColor(55, 48, 163);
            $pdf->SetXY($startX, $startY + 4);
            $pdf->Cell($boxW, 7, PdfGenerator::decode($pasos[$i]), 0, 0, 'C');

            // Flecha al siguiente paso
            if ($i < count($pasos) - 1) {
                $pdf->SetDrawColor(156, 163, 175); // Gris
                $pdf->SetLineWidth(0.8);
                // Linea
                $pdf->Line($startX + $boxW, $startY + ($boxH / 2), $startX + $boxW + $gap, $startY + ($boxH / 2));
                // Punta de flecha
                $pdf->Line($startX + $boxW + $gap - 2, $startY + ($boxH / 2) - 2, $startX + $boxW + $gap, $startY + ($boxH / 2));
                $pdf->Line($startX + $boxW + $gap - 2, $startY + ($boxH / 2) + 2, $startX + $boxW + $gap, $startY + ($boxH / 2));
            }

            $startX += $boxW + $gap;
        }
        $pdf->Ln(25);

        // --- CONTENIDO DEL MANUAL ---
        $secciones = [
            '1. Introducción al WMS' => "InvSys Enterprise es un Sistema de Gestión de Almacenes (WMS) de nivel corporativo.\nDiseñado para mantener un registro inmutable de todas las transacciones de inventario, el sistema garantiza una trazabilidad total desde el momento en que un producto ingresa al almacén hasta que es despachado o consumido.",

            '2. Dashboard y KPIs' => "El Dashboard es el centro de mando. Al iniciar sesión, verás indicadores clave (KPIs) en tiempo real:\n\n- Total de Productos: Variedad de SKUs únicos registrados.\n- Alertas de Stock: Productos por debajo del umbral de seguridad.\n- Valor del Inventario: Capital total invertido (Stock * Precio unitario).",

            '3. Catálogos Base' => "Es vital configurar la estructura lógica de su almacén:\n\n- Categorías: Agrupan los productos (Ej: Electrónicos, Perecederos).\n- Ubicaciones: Espacios físicos (Ej: Pasillo 1, Estante A).\n- Proveedores: Entidades que suministran artículos.\n- Departamentos: Áreas internas que solicitarán requisiciones.",

            '4. Gestión de Productos (FEFO)' => "Paso a paso para crear un producto:\n1. Vaya a Productos > + Nuevo Producto.\n2. Llene la Información Básica (Nombre, SKU, Categoría).\n3. Establezca Stock Mínimo y Máximo.\n4. Control de Lotes (FEFO): Active si el producto tiene caducidad. Exigirá Lote y Fecha de Vencimiento obligatorios.\n5. Vinculación a Proveedores: Establece un catálogo de costos y tiempos de entrega.",

            '5. Abastecimiento y Compras' => "Módulo para reabastecimiento formal:\n- Órdenes de Compra: Seleccione un Proveedor, añada productos y genere un PDF formal.\n- Estados: Cambian automáticamente de Borrador a Recibida al completarse.\n[TIP] El costo de compra actualiza automáticamente el Valor de Inventario.",

            '6. Entradas y Salidas' => "[WARNING] NUNCA se debe editar el stock manualmente sin dejar rastro.\n\n- Entrada: Úselo cuando reciba mercancía. Si maneja lotes, el sistema exigirá Fecha de Vencimiento.\n- Salida: Úselo para mermas o consumo directo con motivo obligatorio.",

            '7. Conteo y Auditoría' => "Para conciliar el stock virtual vs físico:\n1. Iniciar Nuevo Conteo (Filtre por categoría o ubicación).\n2. Ejecución: Use el Escáner para sumar unidades rápidamente.\n3. Conciliación: El sistema resaltará faltantes/sobrantes. Al Aplicar Ajustes, el Kardex se actualiza con un 'Ajuste por Auditoría'.",

            '8. Logística Interna (Requisiciones y Devoluciones)' => "Para empresas donde departamentos consumen del almacén central:\n- Requisiciones: Seleccione departamento y añada productos. Al procesar, el stock se descuenta inmediatamente y se genera un vale PDF.\n- Devoluciones: Reingreso de mercancía por un departamento. Pasa por un control de calidad (Bueno/Dañado) que determina si el stock vuelve a estar Disponible o se retira como Merma.",

            '9. Análisis y Reportes' => "- Kardex General: Libro mayor de entradas y salidas de un producto.\n- Análisis ABC: Clasifica inventario por impacto financiero.\n- Rotación: Artículos de alta demanda.\n- Inventario Muerto: Detecta productos estancados.",

            '10. Administración (Seguridad)' => "Módulo exclusivo para Administradores.\n\n- Usuarios: Cree cuentas y asigne roles (RBAC).\n- Backups: Descargue copias de la base de datos SQL regularmente.\n- Logs de Auditoría: Registro inmutable de inicios de sesión y configuración."
        ];

        $pdf->AddPage();

        foreach ($secciones as $titulo => $contenido) {
            // Título de Sección con caja de color
            $pdf->SetFillColor(238, 242, 255); // Indigo muy claro
            $pdf->SetDrawColor(79, 70, 229); // Borde indigo
            $pdf->SetLineWidth(0.5);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetTextColor(55, 48, 163); // Texto indigo oscuro

            $pdf->Cell(0, 12, '  ' . PdfGenerator::decode($titulo), 'L', 1, 'L', true);
            $pdf->Ln(4);

            $pdf->SetFont('Arial', '', 11);
            $pdf->SetTextColor(71, 85, 105); // Slate 600

            $lineas = explode("\n", $contenido);
            foreach ($lineas as $linea) {
                if (trim($linea) === '') {
                    $pdf->Ln(2);
                    continue;
                }

                // Procesamiento de Alertas Visuales
                if (str_starts_with(trim($linea), '[WARNING]')) {
                    $texto = str_replace('[WARNING]', '', trim($linea));
                    $pdf->SetFillColor(254, 242, 242); // Red muy claro
                    $pdf->SetDrawColor(220, 38, 38); // Red oscuro
                    $pdf->SetTextColor(153, 27, 27);
                    $pdf->SetX(15);
                    $pdf->MultiCell(0, 8, ' Advertencia:' . PdfGenerator::decode($texto), 'L', 'L', true);
                    $pdf->SetTextColor(71, 85, 105); // Restaurar color normal
                    continue;
                }

                if (str_starts_with(trim($linea), '[TIP]')) {
                    $texto = str_replace('[TIP]', '', trim($linea));
                    $pdf->SetFillColor(240, 253, 244); // Green muy claro
                    $pdf->SetDrawColor(22, 163, 74); // Green oscuro
                    $pdf->SetTextColor(22, 101, 52);
                    $pdf->SetX(15);
                    $pdf->MultiCell(0, 8, ' Tip de uso:' . PdfGenerator::decode($texto), 'L', 'L', true);
                    $pdf->SetTextColor(71, 85, 105); // Restaurar color normal
                    continue;
                }

                // Bullets
                if (str_starts_with(trim($linea), '-') || preg_match('/^[0-9]+\./', trim($linea))) {
                    $pdf->SetX(20);
                    $pdf->MultiCell(0, 7, PdfGenerator::decode(trim($linea)));
                } else {
                    $pdf->SetX(15);
                    $pdf->MultiCell(0, 7, PdfGenerator::decode(trim($linea)));
                }
            }
            $pdf->Ln(8);
        }

        // --- SECCIÓN FAQ ---
        $pdf->AddPage();

        $pdf->SetFillColor(238, 242, 255);
        $pdf->SetDrawColor(14, 165, 233); // Cyan
        $pdf->SetLineWidth(0.5);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(3, 105, 161);
        $pdf->Cell(0, 14, '  ' . PdfGenerator::decode('10. Preguntas Frecuentes (FAQ)'), 'L', 1, 'L', true);
        $pdf->Ln(8);

        $faqs = [
            '¿Cómo corrijo un error si me equivoqué al registrar una entrada?' => 'Por cuestiones de auditoría inmutable, no se puede borrar ni editar una transacción pasada del Kardex. Para solucionarlo, debe registrar un Movimiento de Salida seleccionando el motivo "Ajuste de Inventario / Corrección" por la misma cantidad que ingresó por error.',

            '¿Por qué un producto no me aparece para imprimir etiquetas?' => 'Verifique que el producto tenga un SKU válido asignado. El sistema de códigos de barras ignora automáticamente cualquier producto que carezca de un código único para prevenir impresiones erróneas.',

            '¿Qué significa cuando el stock aparece en números negativos?' => 'Ocurre cuando se registran salidas (ventas o despachos) antes de registrar las entradas correspondientes del proveedor. Debe hacer una entrada para regularizarlo a positivo.',

            '¿Puedo cambiar la categoría de un producto que ya tiene movimientos?' => 'Sí, puede editar el producto y reasignarlo a otra categoría sin problema. Sin embargo, los reportes pasados filtrados por esa fecha podrían reflejar el cambio estructural.',

            'Olvidé mi contraseña o no tengo permisos para módulos' => 'Solo el Administrador Principal del sistema puede resetear contraseñas o modificar sus niveles de permiso de seguridad (Roles).'
        ];

        foreach ($faqs as $pregunta => $respuesta) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(30, 41, 59); // Slate 800
            $pdf->SetX(15);
            $pdf->MultiCell(0, 7, 'Q: ' . PdfGenerator::decode($pregunta));

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(100, 116, 139); // Slate 500
            $pdf->SetX(20);
            $pdf->MultiCell(0, 6, 'R: ' . PdfGenerator::decode($respuesta));
            $pdf->Ln(6);
        }

        $pdf->Output('D', 'Manual_Usuario_InvSys_Pro.pdf');
    }
}
