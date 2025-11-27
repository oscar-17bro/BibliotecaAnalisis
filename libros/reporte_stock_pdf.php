<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../libros/fpdf/fpdf.php';

$rol = $_SESSION['usuario']['rol_id'] ?? 0;
if ($rol != 1 && $rol != 2) {
    exit("Acceso denegado.");
}

$conn = obtenerConexion();

$sql = "SELECT 
          c.nombre AS categoria,
          COUNT(l.idLibro) AS total,
          SUM(l.estado='Disponible') AS disponibles
        FROM categorias c
        LEFT JOIN libros l ON l.categoria_id = c.idCategoria
        GROUP BY c.idCategoria";

$res = $conn->query($sql);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Stock de Libros', 0, 1, 'C');
$pdf->Ln(5);

// Fecha
if (isset($_POST['fecha'])) {
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Fecha: ' . date("Y-m-d H:i"), 0, 1);
}

$pdf->Ln(5);

// Encabezado dinámico
$pdf->SetFont('Arial', 'B', 12);

if (isset($_POST['categoria']))     $pdf->Cell(70, 10, 'Categoria', 1);
if (isset($_POST['total']))         $pdf->Cell(40, 10, 'Total', 1);
if (isset($_POST['disponibles']))   $pdf->Cell(40, 10, 'Disponibles', 1);

$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 12);

while ($row = $res->fetch_assoc()) {
    if (isset($_POST['categoria']))     $pdf->Cell(70, 10, utf8_decode($row['categoria']), 1);
    if (isset($_POST['total']))         $pdf->Cell(40, 10, $row['total'], 1);
    if (isset($_POST['disponibles']))   $pdf->Cell(40, 10, $row['disponibles'], 1);

    $pdf->Ln();
}

$pdf->Output();
exit;
