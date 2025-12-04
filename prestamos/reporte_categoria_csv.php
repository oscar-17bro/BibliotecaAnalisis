<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';

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

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

if ($guardar) {
    if (!is_dir(__DIR__ . '/../reportes')) mkdir(__DIR__ . '/../reportes');
    if (!is_dir(__DIR__ . '/../reportes/archivos')) mkdir(__DIR__ . '/../reportes/archivos');
    $nombre = 'reporte_prestamos_categoria_'.$anio.'_'.$mes.'_'.date('Ymd_His').'.csv';
    $rutaRel = 'reportes/archivos/'.$nombre;
    $rutaAbs = __DIR__ . '/../'.$rutaRel;

    $f = fopen($rutaAbs, 'w');
    fputcsv($f, ['Categoria','Cantidad']);
    foreach ($rows as $d) {
        fputcsv($f, [ $d['categoria'], $d['cantidad'] ]);
    }
    fclose($f);

    $conn->query("CREATE TABLE IF NOT EXISTS reportes (
        idReporte INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(100) NOT NULL,
        formato ENUM('PDF','CSV') NOT NULL,
        ruta VARCHAR(255) NOT NULL,
        creado_por INT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $tipo = 'prestamos_por_categoria';
    $formato = 'CSV';
    $ruta = $rutaRel;
    $creado_por = $_SESSION['usuario']['id'] ?? null;
    $ins = $conn->prepare("INSERT INTO reportes (tipo, formato, ruta, creado_por) VALUES (?,?,?,?)");
    $ins->bind_param('sssi', $tipo, $formato, $ruta, $creado_por);
    $ins->execute();

    header('Location: '.URL_BASE.'/reportes/listar.php');
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_prestamos_categoria.csv');
$output = fopen('php://output', 'w');
fputcsv($output, ['Categoria','Cantidad']);
foreach ($rows as $d) {
    fputcsv($output, [ $d['categoria'], $d['cantidad'] ]);
}
fclose($output);
?>
