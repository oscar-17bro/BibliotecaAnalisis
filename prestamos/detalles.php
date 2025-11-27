<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo "<div class='alert alert-danger'>ID inválido</div>"; require_once __DIR__.'/../plantillas/pie.php'; exit; }
$stmt = $conn->prepare("SELECT p.*, u.nombre AS usuarioNombre, u.apellido AS usuarioApellido, l.titulo AS libroTitulo, l.autor AS libroAutor FROM prestamos p JOIN usuarios u ON u.idUsuario = p.usuario_id JOIN libros l ON l.idLibro = p.libro_id WHERE p.idPrestamo = ?");
$stmt->bind_param('i', $id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { echo "<div class='alert alert-danger'>Préstamo no encontrado</div>"; require_once __DIR__.'/../plantillas/pie.php'; exit; }
$p = $res->fetch_assoc(); $stmt->close();
?>
<div class="card mb-3"><div class="card-body">
<h5 class="card-title">Detalle de préstamo #<?php echo $p['idPrestamo']; ?></h5>
<p><strong>Usuario:</strong> <?php echo htmlspecialchars($p['usuarioNombre'].' '.$p['usuarioApellido']); ?></p>
<p><strong>Libro:</strong> <?php echo htmlspecialchars($p['libroTitulo'].' — '.$p['libroAutor']); ?></p>
<p><strong>Fecha préstamo:</strong> <?php echo $p['fechaPrestamo']; ?></p>
<p><strong>Fecha devolución esperada:</strong> <?php echo $p['fechaDevolucion']; ?></p>
<p><strong>Fecha devuelto:</strong> <?php echo $p['fechaDevuelto'] ?? '-'; ?></p>
<p><strong>Multa:</strong> <?php echo ($p['multa']>0) ? ('$ '.number_format($p['multa'])) : '-'; ?></p>
<p><strong>Estado:</strong> <?php echo $p['estado']; ?></p>
<a class="btn btn-secondary" href="listar.php">Volver</a>
</div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
