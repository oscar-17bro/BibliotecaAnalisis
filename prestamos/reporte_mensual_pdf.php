<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../libros/fpdf/fpdf.php';

$mes = intval($_GET['mes'] ?? date('n'));
$anio = intval($_GET['anio'] ?? date('Y'));
$guardar = isset($_GET['guardar']);

$conn = obtenerConexion();

$stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, l.titulo AS libroTitulo, c.nombre AS categoria
                         FROM prestamos p
                         JOIN usuarios u ON u.idUsuario = p.usuario_id
                         JOIN libros l ON l.idLibro = p.libro_id
                         LEFT JOIN categorias c ON c.idCategoria = l.categoria_id
                         WHERE MONTH(p.fechaPrestamo) = ? AND YEAR(p.fechaPrestamo) = ?
                         ORDER BY p.fechaPrestamo ASC");
$stmt->bind_param('ii', $mes, $anio);
$stmt->execute();
$res = $stmt->get_result();

$total = 0; $activos = 0; $finalizados = 0; $vencidos = 0; $multaTotal = 0;
while ($row = $res->fetch_assoc()) {
    $total++;
    $multaTotal += intval($row['multa']);
    if ($row['estado'] === 'Activo') $activos++;
    elseif ($row['estado'] === 'Finalizado') $finalizados++;
    elseif ($row['estado'] === 'Vencido') $vencidos++;
    $datos[] = $row;
}

ob_end_clean();

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,utf8_decode("Reporte mensual de préstamos: $mes/$anio"),0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,utf8_decode("Total préstamos: $total | Activos: $activos | Finalizados: $finalizados | Vencidos: $vencidos | Multa total: $".number_format($multaTotal)),0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(40,9,'Fecha préstamo',1,0,'C',true);
$pdf->Cell(55,9,utf8_decode('Usuario'),1,0,'C',true);
$pdf->Cell(85,9,utf8_decode('Libro'),1,0,'C',true);
$pdf->Cell(45,9,utf8_decode('Categoría'),1,0,'C',true);
$pdf->Cell(35,9,utf8_decode('Fecha devolución'),1,0,'C',true);
$pdf->Cell(30,9,'Multa',1,0,'C',true);
$pdf->Cell(25,9,'Estado',1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
if (!empty($datos)) {
    foreach ($datos as $d) {
        $pdf->Cell(40,8,$d['fechaPrestamo'],1);
        $pdf->Cell(55,8,utf8_decode($d['usuarioNombre']),1);
        $pdf->Cell(85,8,utf8_decode($d['libroTitulo']),1);
        $pdf->Cell(45,8,utf8_decode($d['categoria'] ?? '-'),1);
        $pdf->Cell(35,8,$d['fechaDevolucion'],1);
        $pdf->Cell(30,8,'$ '.number_format($d['multa']),1,0,'R');
        $pdf->Cell(25,8,utf8_decode($d['estado']),1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(315,10,utf8_decode('No hay préstamos en el período seleccionado.'),1,1,'C');
}

if ($guardar) {
    if (!is_dir(__DIR__ . '/../reportes')) mkdir(__DIR__ . '/../reportes');
    if (!is_dir(__DIR__ . '/../reportes/archivos')) mkdir(__DIR__ . '/../reportes/archivos');
    $nombre = 'reporte_mensual_prestamos_'.$anio.'_'.$mes.'_'.date('Ymd_His').'.pdf';
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

    $tipo = 'prestamos_mensual';
    $formato = 'PDF';
    $ruta = $rutaRel;
    $creado_por = $_SESSION['usuario']['id'] ?? null;
    $ins = $conn->prepare("INSERT INTO reportes (tipo, formato, ruta, creado_por) VALUES (?,?,?,?)");
    $ins->bind_param('sssi', $tipo, $formato, $ruta, $creado_por);
    $ins->execute();

    header('Location: '.URL_BASE.'/reportes/listar.php');
    exit;
}

$pdf->Output('I','reporte_mensual_prestamos.pdf');
?>
