<?php
/**
 * InvSys - ExportService
 * 
 * Servicio de exportación de datos a CSV y PDF.
 * Genera archivos descargables directamente, sin dependencias externas.
 */

class ExportService
{
    /**
     * Exportar datos como CSV y enviarlos al navegador.
     *
     * @param string $filename  Nombre del archivo (sin extensión)
     * @param array  $headers   Encabezados de columnas
     * @param array  $rows      Filas de datos (arrays asociativos o indexados)
     * @param array  $keys      Claves para extraer de cada fila (si son objetos/asoc.)
     */
    public function exportCSV(string $filename, array $headers, array $rows, array $keys = []): void
    {
        $filename = $this->sanitizeFilename($filename) . '_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM para que Excel reconozca UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir encabezados
        fputcsv($output, $headers);

        // Escribir filas
        foreach ($rows as $row) {
            $rowData = [];
            if (!empty($keys)) {
                foreach ($keys as $key) {
                    $value = is_object($row) ? ($row->$key ?? '') : ($row[$key] ?? '');
                    $rowData[] = $value;
                }
            } else {
                $rowData = is_object($row) ? (array) $row : $row;
            }
            fputcsv($output, $rowData);
        }

        fclose($output);
        exit;
    }

    /**
     * Exportar datos como PDF y enviarlos al navegador.
     * Genera un PDF simple usando HTML nativo convertido con el navegador,
     * implementado como HTML con CSS @media print para impresión directa.
     * 
     * Para un PDF real sin librerías externas, generamos un HTML estilizado
     * con cabecera Content-Type que fuerza la descarga/impresión.
     *
     * @param string $title     Título del reporte
     * @param string $filename  Nombre del archivo
     * @param array  $sections  Secciones del reporte, cada una con 'title', 'headers', 'rows', 'keys'
     * @param array  $meta      Metadatos adicionales (fecha, usuario, etc.)
     */
    public function exportPDF(string $title, string $filename, array $sections, array $meta = []): void
    {
        $filename = $this->sanitizeFilename($filename) . '_' . date('Y-m-d_His') . '.pdf';
        $systemName = systemName();
        $fechaGeneracion = date('d/m/Y H:i:s');
        $usuario = $meta['usuario'] ?? 'Sistema';

        // Generar HTML del reporte
        $html = $this->buildPDFHtml($title, $systemName, $fechaGeneracion, $usuario, $sections, $meta);

        // Intentar usar DomPDF si está disponible
        $dompdfPath = ROOT_PATH . '/vendor/dompdf/autoload.inc.php';
        if (file_exists($dompdfPath)) {
            require_once $dompdfPath;
            $dompdf = new \Dompdf\Dompdf(['defaultFont' => 'sans-serif']);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }

        // Fallback: servir como HTML imprimible con auto-print
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    /**
     * Construir el HTML completo para el reporte PDF/imprimible.
     */
    private function buildPDFHtml(
        string $title, 
        string $systemName, 
        string $fecha, 
        string $usuario, 
        array $sections,
        array $meta
    ): string {
        $sectionsHtml = '';
        foreach ($sections as $section) {
            $sectionsHtml .= $this->buildSectionHtml($section);
        }

        $summaryHtml = '';
        if (!empty($meta['summary'])) {
            $summaryHtml = '<div class="summary-cards">';
            foreach ($meta['summary'] as $card) {
                $summaryHtml .= sprintf(
                    '<div class="summary-card"><div class="summary-value">%s</div><div class="summary-label">%s</div></div>',
                    htmlspecialchars($card['value']),
                    htmlspecialchars($card['label'])
                );
            }
            $summaryHtml .= '</div>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{$title} - {$systemName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #fff;
            font-size: 11px;
            line-height: 1.5;
            padding: 0;
        }
        .report-container { max-width: 1100px; margin: 0 auto; padding: 30px 40px; }

        /* Header */
        .report-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            border-bottom: 3px solid #6366f1; padding-bottom: 16px; margin-bottom: 24px;
        }
        .report-header h1 {
            font-size: 22px; font-weight: 800; color: #6366f1; margin: 0;
        }
        .report-header .subtitle {
            font-size: 13px; color: #64748b; margin-top: 4px;
        }
        .report-meta { text-align: right; font-size: 10px; color: #64748b; }
        .report-meta strong { color: #334155; }

        /* Summary Cards */
        .summary-cards {
            display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;
        }
        .summary-card {
            flex: 1; min-width: 140px; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 14px 18px; text-align: center;
        }
        .summary-value { font-size: 20px; font-weight: 800; color: #6366f1; }
        .summary-label { font-size: 10px; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Section */
        .section { margin-bottom: 28px; }
        .section-title {
            font-size: 14px; font-weight: 700; color: #334155;
            padding: 8px 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 10px;
        }
        .section-title i { margin-right: 6px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        thead th {
            background: #6366f1; color: #fff; padding: 8px 10px;
            text-align: left; font-weight: 600; text-transform: uppercase;
            font-size: 9.5px; letter-spacing: 0.5px;
        }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eef2ff; }

        /* Badge styles for inline status */
        .badge-ok { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 10px; }
        .badge-warn { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 10px; }
        .badge-danger { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 10px; }
        .text-success { color: #16a34a; font-weight: 700; }
        .text-danger { color: #dc2626; font-weight: 700; }
        .text-bold { font-weight: 700; }
        .text-mono { font-family: 'Courier New', monospace; font-size: 10px; color: #6366f1; }

        /* Footer */
        .report-footer {
            border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 32px;
            display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8;
        }

        /* Print styles */
        @media print {
            body { padding: 0; }
            .report-container { max-width: 100%; padding: 15px 20px; }
            .no-print { display: none !important; }
            thead th { background: #6366f1 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr:nth-child(even) { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .summary-card { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* Print toolbar (only visible in browser fallback) */
        .print-toolbar {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff; padding: 14px 24px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .print-toolbar span { font-weight: 600; font-size: 13px; }
        .print-toolbar button {
            background: #fff; color: #6366f1; border: none; padding: 8px 20px;
            border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 12px;
            transition: all 0.15s ease;
        }
        .print-toolbar button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="print-toolbar no-print">
        <span>📊 {$title} — Vista previa del reporte</span>
        <div>
            <button onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
        </div>
    </div>

    <div class="report-container">
        <div class="report-header">
            <div>
                <h1>{$title}</h1>
                <div class="subtitle">{$systemName} — Reporte generado automáticamente</div>
            </div>
            <div class="report-meta">
                <div><strong>Fecha:</strong> {$fecha}</div>
                <div><strong>Generado por:</strong> {$usuario}</div>
                <div><strong>Sistema:</strong> {$systemName}</div>
            </div>
        </div>

        {$summaryHtml}
        {$sectionsHtml}

        <div class="report-footer">
            <span>{$systemName} © {$this->currentYear()} — Reporte confidencial</span>
            <span>Generado el {$fecha}</span>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Construir el HTML de una sección de tabla.
     */
    private function buildSectionHtml(array $section): string
    {
        $title = htmlspecialchars($section['title'] ?? 'Datos');
        $headers = $section['headers'] ?? [];
        $rows = $section['rows'] ?? [];
        $keys = $section['keys'] ?? [];
        $formatters = $section['formatters'] ?? [];

        if (empty($rows)) {
            return "<div class=\"section\"><h3 class=\"section-title\">{$title}</h3><p style=\"color:#94a3b8;padding:12px 0;\">Sin datos para mostrar.</p></div>";
        }

        $html = "<div class=\"section\"><h3 class=\"section-title\">{$title}</h3>";
        $html .= '<table><thead><tr>';

        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $index => $row) {
            $html .= '<tr>';
            foreach ($keys as $ki => $key) {
                $value = is_object($row) ? ($row->$key ?? '') : ($row[$key] ?? '');
                
                // Apply formatter if exists
                if (isset($formatters[$key]) && is_callable($formatters[$key])) {
                    $html .= '<td>' . $formatters[$key]($value, $row, $index) . '</td>';
                } else {
                    $html .= '<td>' . htmlspecialchars((string) $value) . '</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Sanitizar nombre de archivo.
     */
    private function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    }

    /**
     * Obtener el año actual.
     */
    private function currentYear(): string
    {
        return date('Y');
    }
}
