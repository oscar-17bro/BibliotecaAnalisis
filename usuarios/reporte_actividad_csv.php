<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';

$guardar = isset($_GET['guardar']);

$conn = obtenerConexion();
$sql = "SELECT u.idUsuario, u.nombre, u.apellido, u.correo, r.nombre AS rol, u.estado
        FROM usuarios u
        JOIN roles r ON r.idRol = u.rol_id
        ORDER BY u.nombre";
$res = $conn->query($sql);

$rows = [];
$activos = 0; $inactivos = 0;
while ($u = $res->fetch_assoc()) {
    $rows[] = $u;
    if (intval($u['estado']) === 1) $activos++; else $inactivos++;
}

if ($guardar) {
    if (!is_dir(__DIR__ . '/../reportes')) mkdir(__DIR__ . '/../reportes');
    if (!is_dir(__DIR__ . '/../reportes/archivos')) mkdir(__DIR__ . '/../reportes/archivos');
    $nombre = 'reporte_usuarios_actividad_'.date('Ymd_His').'.csv';
    $rutaRel = 'reportes/archivos/'.$nombre;
    $rutaAbs = __DIR__ . '/../'.$rutaRel;

    $f = fopen($rutaAbs, 'w');
    fputcsv($f, ['Nombre','Correo','Rol','Estado']);
    foreach ($rows as $d) {
        fputcsv($f, [
            $d['nombre'].' '.$d['apellido'],
            $d['correo'],
            $d['rol'],
            intval($d['estado'])===1 ? 'Activo' : 'Inactivo'
        ]);
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

    $tipo = 'usuarios_actividad';
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
header('Content-Disposition: attachment; filename=reporte_usuarios_actividad.csv');
$output = fopen('php://output', 'w');
fputcsv($output, ['Nombre','Correo','Rol','Estado']);
foreach ($rows as $d) {
    fputcsv($output, [
        $d['nombre'].' '.$d['apellido'],
        $d['correo'],
        $d['rol'],
        intval($d['estado'])===1 ? 'Activo' : 'Inactivo'
    ]);
}
fclose($output);
?>
