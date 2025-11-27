<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';

$conn = obtenerConexion();
$rol = $_SESSION['usuario']['rol_id'] ?? 0;
$usuario_id_sesion = $_SESSION['usuario']['id'] ?? 0;
$q = trim($_GET['q'] ?? '');
if ($rol == 3) {
    if ($q === '') {
        $stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, l.titulo AS libroTitulo FROM prestamos p JOIN usuarios u ON u.idUsuario = p.usuario_id JOIN libros l ON l.idLibro = p.libro_id WHERE p.usuario_id = ? ORDER BY p.fechaPrestamo DESC");
        $stmt->bind_param('i', $usuario_id_sesion);
    } else {
        $like = "%$q%";
        $stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, l.titulo AS libroTitulo FROM prestamos p JOIN usuarios u ON u.idUsuario = p.usuario_id JOIN libros l ON l.idLibro = p.libro_id WHERE p.usuario_id = ? AND (l.titulo LIKE ? OR u.nombre LIKE ?) ORDER BY p.fechaPrestamo DESC");
        $stmt->bind_param('iss', $usuario_id_sesion, $like, $like);
    }
} else {
    if ($q === '') {
        $stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, l.titulo AS libroTitulo FROM prestamos p JOIN usuarios u ON u.idUsuario = p.usuario_id JOIN libros l ON l.idLibro = p.libro_id ORDER BY p.fechaPrestamo DESC");
    } else {
        $like = "%$q%";
        $stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, l.titulo AS libroTitulo FROM prestamos p JOIN usuarios u ON u.idUsuario = p.usuario_id JOIN libros l ON l.idLibro = p.libro_id WHERE l.titulo LIKE ? OR u.nombre LIKE ? OR l.isbn = ? ORDER BY p.fechaPrestamo DESC");
        $stmt->bind_param('sss', $like, $like, $q);
    }
}
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Préstamos</h2>
  <?php if ($rol == 1 || $rol == 2): ?>
    <a class="btn btn-success" href="crear.php">+ Prestar libro</a>
    <a class="btn btn-info" href="cron.php">+ generar notificacion</a>
  <?php endif; ?>

</div>
<form class="row g-2 mb-3" method="get">
  <div class="col-auto"><input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Buscar por título, usuario o ISBN"></div>
  <div class="col-auto"><button class="btn btn-primary">Buscar</button> <a href="listar.php" class="btn btn-outline-secondary">Limpiar</a></div>
</form>
<table class="table table-hover"><thead class="table-light"><tr><th>Usuario</th><th>Libro</th><th>Fecha préstamo</th><th>Fecha devolución</th><th>Fecha devuelto</th><th>Multa</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
<?php while($p = $res->fetch_assoc()): ?>
  <tr>
    <td><?php echo htmlspecialchars($p['usuarioNombre']); ?></td>
    <td><?php echo htmlspecialchars($p['libroTitulo']); ?></td>
    <td><?php echo htmlspecialchars($p['fechaPrestamo']); ?></td>
    <td><?php echo htmlspecialchars($p['fechaDevolucion']); ?></td>
    <td><?php echo htmlspecialchars($p['fechaDevuelto'] ?? '-'); ?></td>
    <td><?php echo ($p['multa']>0) ? ('$ '.number_format($p['multa'])) : '-'; ?></td>
    <td><?php echo htmlspecialchars($p['estado']); ?></td>
    <td>
      <div class="card-actions">
        <a class="btn btn-sm btn-info" href="detalles.php?id=<?php echo $p['idPrestamo']; ?>">Detalles</a>
        <?php if (($rol==1 || $rol==2) && $p['estado'] == 'Activo'): ?>
          <a class="btn btn-sm btn-success" href="devolver.php?id=<?php echo $p['idPrestamo']; ?>" onclick="return confirm('Marcar como devuelto?')">Devolver</a>
         <a class="btn btn-sm btn-warning" href="extender.php?id=<?php echo $p['idPrestamo']; ?>" onclick="return confirm('extender prestamo??')">extender</a>
          <?php endif; ?>
        <?php if ($rol==1): ?>
          <a class="btn btn-sm btn-danger" href="eliminar.php?id=<?php echo $p['idPrestamo']; ?>" onclick="return confirm('Eliminar préstamo?')">Eliminar</a>
        <?php endif; ?>
      </div>
    </td>
  </tr>
<?php endwhile; ?>
</tbody></table>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
