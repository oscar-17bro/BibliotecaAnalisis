<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../libros/fpdf/fpdf.php';

$guardar = isset($_GET['guardar']);

$conn = obtenerConexion();

$sql = "SELECT u.idUsuario, u.nombre, u.apellido, u.correo, r.nombre AS rol, u.estado
        FROM usuarios u
        JOIN roles r ON r.idRol = u.rol_id
        ORDER BY u.nombre";
$res = $conn->query($sql);

$activos = 0; $inactivos = 0; $datos = [];
while ($u = $res->fetch_assoc()) {
    $datos[] = $u;
    if (intval($u['estado']) === 1) $activos++; else $inactivos++;
}

ob_end_clean();
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,utf8_decode('Reporte de usuarios activos/inactivos'),0,1,'C');
$pdf->Ln(2);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,utf8_decode('Activos: '.$activos.' | Inactivos: '.$inactivos),0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(60,9,utf8_decode('Nombre'),1,0,'C',true);
$pdf->Cell(60,9,'Correo',1,0,'C',true);
$pdf->Cell(40,9,'Rol',1,0,'C',true);
$pdf->Cell(30,9,'Estado',1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
foreach ($datos as $d) {
    $pdf->Cell(60,8,utf8_decode($d['nombre'].' '.$d['apellido']),1);
    $pdf->Cell(60,8,utf8_decode($d['correo']),1);
    $pdf->Cell(40,8,utf8_decode($d['rol']),1);
    $pdf->Cell(30,8, $d['estado'] ? 'Activo' : 'Inactivo',1);
    $pdf->Ln();
}

if ($guardar) {
    if (!is_dir(__DIR__ . '/../reportes')) mkdir(__DIR__ . '/../reportes');
    if (!is_dir(__DIR__ . '/../reportes/archivos')) mkdir(__DIR__ . '/../reportes/archivos');
    $nombre = 'reporte_usuarios_actividad_'.date('Ymd_His').'.pdf';
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

    $tipo = 'usuarios_actividad';
    $formato = 'PDF';
    $ruta = $rutaRel;
    $creado_por = $_SESSION['usuario']['id'] ?? null;
    $ins = $conn->prepare("INSERT INTO reportes (tipo, formato, ruta, creado_por) VALUES (?,?,?,?)");
    $ins->bind_param('sssi', $tipo, $formato, $ruta, $creado_por);
    $ins->execute();

    header('Location: '.URL_BASE.'/reportes/listar.php');
    exit;
}

$pdf->Output('I','reporte_usuarios_actividad.pdf');
?>
