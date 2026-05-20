<?php
/**
 * InvSys - PdfGenerator
 * 
 * Clase envoltorio sobre FPDF para generar documentos PDF
 * con cabecera y pie de página estandarizados del sistema.
 * Compatible con PHP 8.2+ (sin utf8_decode deprecated).
 */

// Cargar FPDF directamente (versión clásica sin namespace)
require_once ROOT_PATH . '/vendor/setasign/fpdf/fpdf.php';

class PdfGenerator extends FPDF
{
    private string $empresa = 'InvSys Enterprise';
    private string $documentTitle = 'Documento Oficial';

    public function setDocumentTitle(string $title): void
    {
        $this->documentTitle = $title;
    }

    /**
     * Convierte texto UTF-8 a ISO-8859-1 de forma segura (PHP 8.2+ compatible).
     * Reemplaza utf8_decode() que fue deprecado en PHP 8.2.
     *
     * @param string|null $text Texto en UTF-8
     * @return string Texto en ISO-8859-1
     */
    public static function decode(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    // Cabecera de página
    public function Header(): void
    {
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(33, 37, 41);

        $this->Cell(80);
        $this->Cell(30, 10, self::decode($this->documentTitle), 0, 0, 'C');

        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 10, self::decode($this->empresa), 0, 1, 'R');

        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, 22, 200, 22);

        $this->Ln(10);
    }

    // Pie de página
    public function Footer(): void
    {
        $this->SetY(-15);

        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(108, 117, 125);

        $this->Cell(0, 10, 'Impreso el: ' . date('d/m/Y H:i'), 0, 0, 'L');
        $this->Cell(0, 10, self::decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Método para crear tablas básicas
    public function BasicTable($header, $data, $widths, $aligns)
    {
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0);
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);

        // Cabecera
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($widths[$i], 7, self::decode($header[$i]), 1, 0, 'C', true);
        }
        $this->Ln();

        // Datos
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);

        foreach ($data as $row) {
            for ($i = 0; $i < count($row); $i++) {
                $this->Cell($widths[$i], 6, self::decode($row[$i]), 'LRB', 0, $aligns[$i], true);
            }
            $this->Ln();
        }
    }
}
