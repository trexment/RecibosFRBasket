<?php

require_once(__DIR__ . '/../libraries/tcpdf/tcpdf.php');

function generarReciboDesplazamientoPDF($partidos, $usuario, $desde, $hasta)
{
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Federación Riojana de Baloncesto');
    $pdf->SetTitle('Recibo Desplazamientos');
    $pdf->SetMargins(15, 20, 15);
    $pdf->AddPage();

    // === CABECERA ===
    $logoLeft = __DIR__ . '/../../public/img/Federacion_riojana_baloncesto.png';
    $logoRight = __DIR__ . '/../../public/img/silbato.png';

    if (file_exists($logoLeft)) {
        $pdf->Image($logoLeft, 15, 10, 30);
    }
    if (file_exists($logoRight)) {
        $pdf->Image($logoRight, 165, 10, 15);
    }

    $pdf->Ln(25);
    // Línea superior con los colores de La Rioja
    $pdf->SetLineWidth(0.8);
    $colors = [[200, 0, 0], [255, 255, 255], [0, 120, 0], [255, 210, 0]];
    $x = 15;
    foreach ($colors as $color) {
        [$r, $g, $b] = $color;
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->Line($x, 28, $x + 45, 28);
        $x += 45;
    }

    $pdf->Ln(8);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'RECIBO ARBITRAL - DESPLAZAMIENTOS', 0, 1, 'C');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, "Periodo: " . date('d/m/Y', strtotime($desde)) . " - " . date('d/m/Y', strtotime($hasta)), 0, 1, 'C');
    $pdf->Ln(5);

    // === TABLA ===
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 100, 0);
    $pdf->Cell(15, 7, 'Día', 0, 0, 'C');
    $pdf->Cell(25, 7, 'Cat.', 0, 0, 'L');
    $pdf->Cell(95, 7, 'Equipos', 0, 0, 'L');
    $pdf->Cell(40, 7, 'Importe (€)', 0, 1, 'R');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetDrawColor(0, 150, 0);
    $pdf->SetTextColor(0, 0, 0);
    $total = 0;

    foreach ($partidos as $p) {
        $dia = date('d', strtotime($p['fecha']));
        $importe = (float)$p['importe_desplazamiento'] / 2; // mitad del desplazamiento

        // Abreviar categoría automáticamente
        $cat = strtoupper(trim($p['categoria']));
        $cat = preg_replace('/\s+/', ' ', $cat);
        $catWords = explode(' ', $cat);
        $catAbbr = '';
        foreach ($catWords as $word) {
            if (mb_strlen($word) > 2) {
                $catAbbr .= mb_substr($word, 0, 1);
            }
        }
        $cat = $catAbbr;

        // Limitar longitud equipos
        $maxLen = 25;
        $equipoLocal = (mb_strlen($p['equipo_local']) > $maxLen)
            ? mb_substr($p['equipo_local'], 0, $maxLen - 3) . '...'
            : $p['equipo_local'];
        $equipoVisitante = (mb_strlen($p['equipo_visitante']) > $maxLen)
            ? mb_substr($p['equipo_visitante'], 0, $maxLen - 3) . '...'
            : $p['equipo_visitante'];
        $equipos = "{$equipoLocal} vs {$equipoVisitante}";

        $pdf->Cell(15, 6, $dia, 0, 0, 'C');
        $pdf->Cell(25, 6, $cat, 0, 0, 'L');
        $pdf->Cell(95, 6, $equipos, 0, 0, 'L');
        $pdf->Cell(40, 6, number_format($importe, 2, ',', '.') . " €", 0, 1, 'R');
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());

        $total += $importe;
    }

    $pdf->Ln(5);
    $pdf->SetDrawColor(0, 150, 0);
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());

    // === TOTAL FINAL ===
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(135, 6, 'TOTAL DESPLAZAMIENTOS', 0, 0, 'R');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(45, 6, number_format($total, 2, ',', '.') . " €", 0, 1, 'R');

    $pdf->Ln(8);
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(5);

    // === DATOS PERSONALES ===
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(0, 5,
        "Árbitro: {$usuario['nombre']} {$usuario['apellidos']}\n" .
        "DNI: {$usuario['dni']}   CP: {$usuario['codigo_postal']}\n" .
        "Domicilio: {$usuario['domicilio']}\n" .
        "Cuenta Bancaria: {$usuario['cuenta_bancaria']}",
        0, 'L'
    );

    $pdf->Ln(15);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(0, 6, "Firma del árbitro: ____________________________", 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(0, 6, "Logroño, " . date('d/m/Y'), 0, 1, 'R');

    // === DESCARGA ===
    $fileName = 'Recibo_' . str_replace(' ', '_', $usuario['nombre'] . '_' . $usuario['apellidos']) . '_Desplazamientos.pdf';
    $pdf->Output($fileName, 'D');
}

