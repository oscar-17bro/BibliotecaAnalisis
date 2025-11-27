<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
$rol = $_SESSION['usuario']['rol_id'];
$usuarioSesion = $_SESSION['usuario']['id'];
if ($rol == 1 && $id > 0) { $uid = $id; } else { $uid = $usuarioSesion; }
$stmt = $conn->prepare("SELECT p.*, l.titulo, l.autor FROM prestamos p JOIN libros l ON l.idLibro = p.libro_id WHERE p.usuario_id = ? ORDER BY p.fechaPrestamo DESC");
$stmt->bind_param('i', $uid); $stmt->execute(); $res = $stmt->get_result();
?>
<div class="card"><div class="card-body">
<h5>Historial de préstamos</h5>
<table class="table"><thead><tr><th>Libro</th><th>Prestamo</th><th>Devolución esperada</th><th>Devuelto</th><th>Multa</th><th>Estado</th></tr></thead><tbody>
<?php while($r = $res->fetch_assoc()): ?>
  <tr><td><?php echo htmlspecialchars($r['titulo'].' — '.$r['autor']); ?></td><td><?php echo $r['fechaPrestamo']; ?></td><td><?php echo $r['fechaDevolucion']; ?></td><td><?php echo $r['fechaDevuelto'] ?? '-'; ?></td><td><?php echo ($r['multa']>0) ? ('$ '.number_format($r['multa'])) : '-'; ?></td><td><?php echo $r['estado']; ?></td></tr>
<?php endwhile; ?>
</tbody></table></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
