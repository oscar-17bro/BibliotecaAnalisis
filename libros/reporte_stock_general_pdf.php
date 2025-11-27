<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../libros/fpdf/fpdf.php';

// Recibir columnas seleccionadas
$col_portada   = isset($_POST['col_portada']);
$col_titulo    = isset($_POST['col_titulo']);
$col_autor     = isset($_POST['col_autor']);
$col_isbn      = isset($_POST['col_isbn']);
$col_categoria = isset($_POST['col_categoria']);
$col_estado    = isset($_POST['col_estado']);

$conn = obtenerConexion();

// Obtener todos los libros con categoría
$sql = "SELECT l.titulo, l.autor, l.isbn, l.estado, l.portada, c.nombre AS categoria
        FROM libros l
        LEFT JOIN categorias c ON c.idCategoria = l.categoria_id
        ORDER BY l.titulo";

$res = $conn->query($sql);

// Evitar errores por salidas antes del PDF
ob_end_clean();

$pdf = new FPDF('L', 'mm', 'A4'); // Horizontal para más columnas
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Stock de Libros'), 0, 1, 'C');
$pdf->Ln(5);

// === ENCABEZADOS ===
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 200, 200);

if ($col_portada)   $pdf->Cell(30, 10, "Portada", 1, 0, 'C', true);
if ($col_titulo)    $pdf->Cell(70, 10, utf8_decode("Título"), 1, 0, 'C', true);
if ($col_autor)     $pdf->Cell(50, 10, utf8_decode("Autor"), 1, 0, 'C', true);
if ($col_isbn)      $pdf->Cell(35, 10, "ISBN", 1, 0, 'C', true);
if ($col_categoria) $pdf->Cell(40, 10, "Categoría", 1, 0, 'C', true);
if ($col_estado)    $pdf->Cell(30, 10, "Estado", 1, 0, 'C', true);

$pdf->Ln();

// === CONTENIDO ===
$pdf->SetFont('Arial', '', 11);

while ($l = $res->fetch_assoc()) {

    if ($col_portada) {
        if ($l['portada']) {
            $pdf->Cell(30, 20, $pdf->Image(
                __DIR__ . '/../recursos/imagenes/' . $l['portada'], 
                $pdf->GetX()+5, 
                $pdf->GetY()+3,
                15,
                15
            ), 1);
        } else {
            $pdf->Cell(30, 20, "-", 1);
        }
    }

    if ($col_titulo)    $pdf->Cell(70, 20, utf8_decode($l['titulo']), 1);
    if ($col_autor)     $pdf->Cell(50, 20, utf8_decode($l['autor']), 1);
    if ($col_isbn)      $pdf->Cell(35, 20, $l['isbn'], 1);
    if ($col_categoria) $pdf->Cell(40, 20, utf8_decode($l['categoria']), 1);
    if ($col_estado)    $pdf->Cell(30, 20, utf8_decode($l['estado']), 1);

    $pdf->Ln();
}

$pdf->Output("I", "reporte_stock_general.pdf");
?>
