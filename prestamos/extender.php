<?php
require_once __DIR__ . '/../permiso_bibliotecario.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo "<div class='alert alert-danger'>ID inválido</div>"; require_once __DIR__ . '/../plantillas/pie.php'; exit; }
$stmt = $conn->prepare("SELECT p.*, l.titulo FROM prestamos p JOIN libros l ON l.idLibro = p.libro_id WHERE p.idPrestamo = ?");
$stmt->bind_param('i', $id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { echo "<div class='alert alert-danger'>Préstamo no encontrado</div>"; require_once __DIR__ . '/../plantillas/pie.php'; exit; }
$p = $res->fetch_assoc();
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dias = intval($_POST['dias'] ?? 0);
    if ($dias <= 0) $errores[] = "Ingresa una cantidad de días válida.";
    else {
        $nueva = date('Y-m-d', strtotime($p['fechaDevolucion']." +$dias days"));
        $u = $conn->prepare("UPDATE prestamos SET fechaDevolucion = ? WHERE idPrestamo = ?");
        $u->bind_param('si', $nueva, $id);
        if ($u->execute()) {
            $msg = "Se ha extendido el plazo del préstamo del libro '".addslashes($p['titulo'])."' hasta $nueva.";
            $ins = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)"); $ins->bind_param('is', $p['usuario_id'], $msg); $ins->execute();
            header("Location: detalles.php?id=$id&msg=Plazo extendido"); exit;
        } else $errores[] = "No se pudo actualizar: ".$u->error;
    }
}
?>
<div class="card"><div class="card-body"><h5>Extender plazo - Préstamo #<?php echo $p['idPrestamo']; ?></h5><?php if (!empty($errores)): foreach($errores as $e) echo "<div class='alert alert-danger'>$e</div>"; endif; ?>
<form method="post"><div class="mb-3"><label class="form-label">Días a extender</label><input class="form-control" name="dias" type="number" min="1"></div>
<button class="btn btn-primary">Extender</button> <a class="btn btn-secondary" href="detalles.php?id=<?php echo $id; ?>">Cancelar</a>
</form></div></div><?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
