<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

$conn = obtenerConexion();
$rol = $_SESSION['usuario']['rol_id'] ?? 0;
if ($rol != 1 && $rol != 2) {
    header("Location: ../no_autorizado.php");
    exit;
}

$conn->query("CREATE TABLE IF NOT EXISTS reportes (
    idReporte INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    formato ENUM('PDF','CSV') NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    creado_por INT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    $stmt = $conn->prepare("SELECT r.*, u.nombre AS autor FROM reportes r LEFT JOIN usuarios u ON u.idUsuario = r.creado_por ORDER BY r.fecha DESC");
    $stmt->execute();
} else {
    $like = "%$q%";
    $stmt = $conn->prepare("SELECT r.*, u.nombre AS autor FROM reportes r LEFT JOIN usuarios u ON u.idUsuario = r.creado_por WHERE r.tipo LIKE ? OR r.formato LIKE ? ORDER BY r.fecha DESC");
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
}
$res = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Reportes guardados</h2>
  <div>
    <a class="btn btn-outline-primary" href="../prestamos/reporte_mensual.php">Préstamos mensuales</a>
    <a class="btn btn-outline-primary" href="../prestamos/reporte_categoria.php">Préstamos por categoría</a>
    <a class="btn btn-outline-primary" href="../usuarios/reporte_actividad.php">Usuarios activos/inactivos</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto"><input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Buscar por tipo o formato"></div>
  <div class="col-auto"><button class="btn btn-primary">Buscar</button> <a href="listar.php" class="btn btn-outline-secondary">Limpiar</a></div>
  <div class="col-12 text-muted">Los archivos se almacenan en la carpeta reportes/archivos.</div>
</form>

<table class="table table-striped table-hover">
  <thead class="table-light">
    <tr>
      <th>Tipo</th>
      <th>Formato</th>
      <th>Archivo</th>
      <th>Autor</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['tipo']); ?></td>
      <td><?php echo htmlspecialchars($r['formato']); ?></td>
      <td>
        <?php $url = URL_BASE . '/' . $r['ruta']; ?>
        <a class="btn btn-sm btn-outline-dark" target="_blank" href="<?php echo $url; ?>">Descargar</a>
      </td>
      <td><?php echo htmlspecialchars($r['autor'] ?? '-'); ?></td>
      <td><?php echo htmlspecialchars($r['fecha']); ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
