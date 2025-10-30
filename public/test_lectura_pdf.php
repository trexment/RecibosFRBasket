<?php
require_once __DIR__ . '/../app/helpers/PdfHelper.php';
PdfHelper::register();

use Smalot\PdfParser\Parser;

$pdfFile = __DIR__ . '/../app/uploads/test_designacion.pdf'; // pon aquí la ruta a tu PDF real

if (!file_exists($pdfFile)) {
    die("❌ No se encontró el archivo: $pdfFile");
}

$parser = new Parser();
$pdf = $parser->parseFile($pdfFile);
$text = $pdf->getText();

// Mostrar texto procesado
echo "<pre style='font-family:monospace; white-space:pre-wrap;'>";
echo htmlspecialchars($text);
echo "</pre>";
