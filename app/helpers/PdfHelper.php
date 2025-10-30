<?php
require_once __DIR__ . '/../libraries/tcpdf/tcpdf.php';

class PdfHelper
{
    /**
     * 🔹 Genera un recibo PDF de arbitraje
     */
    public static function generarReciboPDF($partidos, $usuario, $tipo = 'partido', $retencion = 0)
    {
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Federación Riojana de Baloncesto');
        $pdf->SetTitle('Recibo Arbitral');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

        // Encabezado FRB
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'FEDERACIÓN RIOJANA DE BALONCESTO', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 5, "TESORERÍA
CIF G26030088
C/ Rodejón, 28, 26003 – Logroño
Tlfno: 941.287.502
Email: administracion@frbaloncesto.com", 0, 'C');

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'RECIBO DE ARBITRAJE', 0, 1, 'C');
        $pdf->Ln(2);

        // Tabla encabezado
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(25, 6, 'FECHA', 1);
        $pdf->Cell(5, 6, 'J', 1);
        $pdf->Cell(95, 6, 'ENCUENTRO', 1, 0, 'C');
        $pdf->Cell(35, 6, 'CATEGORÍA', 1);
        $pdf->Cell(20, 6, 'IMPORTE (€)', 1);
        $pdf->Ln();

        $total = 0;
        foreach ($partidos as $p) {
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(25, 6, $p['fecha'], 1);
            $pdf->Cell(5, 6, $p['jornada'], 1);
            $pdf->Cell(95, 6, $p['equipo_local'] . ' vs ' . $p['equipo_visitante'], 1, 0, 'C');
            $pdf->Cell(35, 6, $p['categoria'], 1);
            $pdf->Cell(20, 6, number_format((float)$p['importe'], 2), 1, 1, 'R');
            $total += (float)$p['importe'];
        }

        // Totales
        $pdf->Ln(4);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(140, 6, 'Total bruto', 0, 0, 'R');
        $pdf->Cell(30, 6, number_format($total, 2) . ' €', 1, 1, 'R');

        $retencion_importe = ($retencion > 0) ? round($total * ($retencion / 100), 2) : 0;
        if ($retencion > 0) {
            $pdf->Cell(140, 6, "Retención IRPF ({$retencion}%)", 0, 0, 'R');
            $pdf->Cell(30, 6, '- ' . number_format($retencion_importe, 2) . ' €', 1, 1, 'R');
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(140, 6, 'Total líquido a percibir', 0, 0, 'R');
        $pdf->Cell(30, 6, number_format($total - $retencion_importe, 2) . ' €', 1, 1, 'R');

        // Datos del árbitro
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "D./Dña. {$usuario['nombre']} {$usuario['apellidos']}
Domicilio: {$usuario['domicilio']} - {$usuario['codigo_postal']}
NIF: {$usuario['dni']}
IBAN: {$usuario['cuenta_bancaria']}", 0, 'L');

        $pdf->Ln(15);
        $pdf->MultiCell(0, 6, "RECIBÍ conforme la cantidad anteriormente reflejada, en concepto de prestación arbitral en la competición organizada por la FRB, en Logroño a " . date('d/m/Y'), 0, 'L');
        $pdf->Ln(15);
        $pdf->Cell(0, 6, 'Fdo.: ___________________________________', 0, 1, 'L');

        return $pdf;
    }

    /**
     * 🔹 Registra el autoload para Smalot\PdfParser
     */
    public static function register()
    {
        $baseDir = __DIR__ . '/../libraries/'; // apunta al directorio principal de librerías

        spl_autoload_register(function ($class) use ($baseDir) {
            $prefix = 'Smalot\\PdfParser\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . 'Smalot/PdfParser/' . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
            } else {
                throw new \Exception("⚠️ Librería Smalot/PdfParser no encontrada. Asegúrate de tenerla en app/libraries/Smalot/PdfParser/");
            }
        });
    }
}
