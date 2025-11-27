<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$uid = $_SESSION['usuario']['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_todas'])) {
    $u = $conn->prepare("UPDATE notificaciones SET leido = 1 WHERE usuario_id = ?");
    $u->bind_param('i', $uid); $u->execute();
    header("Location: notificaciones.php"); exit;
}
$res = $conn->prepare("SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC");
$res->bind_param('i', $uid); $res->execute(); $rs = $res->get_result();
?>
<div class="card"><div class="card-body">
<h5>Notificaciones</h5>
<form method="post" class="mb-3"><button name="marcar_todas" class="btn btn-sm btn-outline-secondary">Marcar todas como leídas</button></form>
<?php if ($rs->num_rows === 0): ?><div class="alert alert-info">No hay notificaciones.</div><?php else: ?>
  <ul class="list-group"><?php while($n = $rs->fetch_assoc()): ?>
    <li class="list-group-item <?php echo $n['leido'] ? '' : 'list-group-item-warning'; ?>">
      <div><?php echo htmlspecialchars($n['mensaje']); ?></div>
      <small class="text-muted"><?php echo $n['fecha']; ?></small>
    </li>
  <?php endwhile; ?></ul>
<?php endif; ?>
</div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
