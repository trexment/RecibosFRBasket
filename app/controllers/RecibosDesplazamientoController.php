<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../libraries/tcpdf/tcpdf.php';

class RecibosController
{
    public function generar()
    {
        session_start();
        $usuario = $_SESSION['usuario'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM partidos WHERE fecha BETWEEN :desde AND :hasta");
        $stmt->execute([
            ':desde' => $desde,
            ':hasta' => $hasta
        ]);
        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new TCPDF();
        $pdf->AddPage();

        $pdf->Image(__DIR__ . '/../../public/images/logo_federacion.png', 15, 10, 30);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(50, 10);
        $pdf->Cell(0, 10, 'FEDERACIÓN RIOJANA DE BALONCESTO', 0, 1);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(50, 16);
        $pdf->Cell(0, 10, 'C/ LOPEZ DE HARO, 14 BAJO · 26006 LOGROÑO (LA RIOJA)', 0, 1);

        $pdf->SetXY(15, 30);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 10, 'TEMPORADA 2024/25 - DESPLAZAMIENTOS', 0, 1);

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(25, 7, 'Fecha', 1);
        $pdf->Cell(25, 7, 'Jornada', 1);
        $pdf->Cell(60, 7, 'Equipos', 1);
        $pdf->Cell(40, 7, 'Categoría', 1);
        $pdf->Cell(30, 7, 'Desplazamiento', 1);
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);
        $total = 0;
        foreach ($partidos as $p) {
            if ($p['importe_desplazamiento'] > 0) {
                $equipos = $p['equipo_local'] . ' - ' . $p['equipo_visitante'];
                $importe = $p['importe_desplazamiento'] / 2;
                $pdf->Cell(25, 7, $p['fecha'], 1);
                $pdf->Cell(25, 7, $p['jornada'], 1);
                $pdf->Cell(60, 7, $equipos, 1);
                $pdf->Cell(40, 7, $p['categoria'], 1);
                $pdf->Cell(30, 7, number_format($importe, 2) . ' €', 1);
                $pdf->Ln();
                $total += $importe;
            }
        }

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(150, 7, 'TOTAL DESPLAZAMIENTOS', 1);
        $pdf->Cell(30, 7, number_format($total, 2) . ' €', 1);
        $pdf->Ln(10);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 5, "Nombre: {$usuario['nombre']} {$usuario['apellidos']}
DNI: {$usuario['dni']}
Domicilio: {$usuario['domicilio']}
C.P.: {$usuario['codigo_postal']}
Cuenta: {$usuario['cuenta_bancaria']}", 0);

        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'RECIBÍ en Logroño, a ' . date('d/m/Y'), 0, 1);

        $pdf->Output('recibo_desplazamientos.pdf', 'I');
    }
}