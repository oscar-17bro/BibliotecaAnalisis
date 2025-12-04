<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../libros/fpdf/fpdf.php';

$mes = intval($_GET['mes'] ?? date('n'));
$anio = intval($_GET['anio'] ?? date('Y'));
$guardar = isset($_GET['guardar']);

$conn = obtenerConexion();

$sql = "SELECT c.nombre AS categoria, COUNT(*) AS cantidad
        FROM prestamos p
        JOIN libros l ON l.idLibro = p.libro_id
        LEFT JOIN categorias c ON c.idCategoria = l.categoria_id
        WHERE MONTH(p.fechaPrestamo) = ? AND YEAR(p.fechaPrestamo) = ?
        GROUP BY c.idCategoria
        ORDER BY c.nombre";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $mes, $anio);
$stmt->execute();
$res = $stmt->get_result();
ob_end_clean();

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,utf8_decode("Préstamos por categoría: $mes/$anio"),0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(110,10,utf8_decode('Categoría'),1,0,'C',true);
$pdf->Cell(60,10,'Cantidad',1,0,'C',true);
$pdf->Ln();
$pdf->SetFont('Arial','',12);

$total = 0;
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(110,10,utf8_decode($row['categoria'] ?? '-'),1);
    $pdf->Cell(60,10,intval($row['cantidad']),1,0,'R');
    $pdf->Ln();
    $total += intval($row['cantidad']);
}

if ($total === 0) {
    $pdf->Cell(170,10,utf8_decode('No hay préstamos en el período seleccionado.'),1,1,'C');
} else {
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(110,10,utf8_decode('Total'),1);
    $pdf->Cell(60,10,$total,1,0,'R');
}

if ($guardar) {
    if (!is_dir(__DIR__ . '/../reportes')) mkdir(__DIR__ . '/../reportes');
    if (!is_dir(__DIR__ . '/../reportes/archivos')) mkdir(__DIR__ . '/../reportes/archivos');
    $nombre = 'reporte_prestamos_categoria_'.$anio.'_'.$mes.'_'.date('Ymd_His').'.pdf';
    $rutaRel = 'reportes/archivos/'.$nombre;
    $rutaAbs = __DIR__ . '/../'.$rutaRel;
    $pdf->Output('F', $rutaAbs);

    $conn->query("CREATE TABLE IF NOT EXISTS reportes (
        idReporte INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(100) NOT NULL,
        formato ENUM('PDF','CSV') NOT NULL,
        ruta VARCHAR(255) NOT NULL,
        creado_por INT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $tipo = 'prestamos_por_categoria';
    $formato = 'PDF';
    $ruta = $rutaRel;
    $creado_por = $_SESSION['usuario']['id'] ?? null;
    $ins = $conn->prepare("INSERT INTO reportes (tipo, formato, ruta, creado_por) VALUES (?,?,?,?)");
    $ins->bind_param('sssi', $tipo, $formato, $ruta, $creado_por);
    $ins->execute();

    header('Location: '.URL_BASE.'/reportes/listar.php');
    exit;
}

$pdf->Output('I','reporte_prestamos_categoria.pdf');
?>
