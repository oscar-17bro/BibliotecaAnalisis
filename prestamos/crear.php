<?php
require_once __DIR__ . '/../permiso_bibliotecario.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$errors = [];
$usuario_id = '';
$libro_id = '';
$usuarios = $conn->query("SELECT idUsuario, nombre, apellido FROM usuarios WHERE estado = 1 ORDER BY nombre");
$libros = $conn->query("SELECT idLibro, titulo, estado FROM libros ORDER BY titulo");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = intval($_POST['usuario_id'] ?? 0);
    $libro_id = intval($_POST['libro_id'] ?? 0);
    if ($usuario_id <= 0) $errors[] = "Selecciona un usuario.";
    if ($libro_id <= 0) $errors[] = "Selecciona un libro.";
    $stmt = $conn->prepare("SELECT estado FROM libros WHERE idLibro = ?");
    $stmt->bind_param('i',$libro_id); $stmt->execute(); $st = $stmt->get_result();
    if ($st->num_rows === 0) $errors[] = "Libro no encontrado."; else { $row = $st->fetch_assoc(); if ($row['estado'] === 'Prestado') $errors[] = "El libro ya está prestado."; }
    $stmt->close();
    if (empty($errors)) {
        $cfg = $conn->query("SELECT diasMaximoPrestamo FROM configuracion LIMIT 1")->fetch_assoc();
        $dias = intval($cfg['diasMaximoPrestamo'] ?? 14);
        $fechaPrestamo = date('Y-m-d');
        $fechaDevolucion = date('Y-m-d', strtotime("+$dias days"));
        $stmt = $conn->prepare("INSERT INTO prestamos (usuario_id, libro_id, fechaPrestamo, fechaDevolucion, estado) VALUES (?, ?, ?, ?, 'Activo')");
        $stmt->bind_param('iiss', $usuario_id, $libro_id, $fechaPrestamo, $fechaDevolucion);
        if ($stmt->execute()) {
            $u2 = $conn->prepare("UPDATE libros SET estado = 'Prestado' WHERE idLibro = ?");
            $u2->bind_param('i',$libro_id); $u2->execute(); $u2->close();
            header("Location: listar.php?msg=Préstamo registrado"); exit;
        } else { $errors[] = "Error guardando préstamo: " . $stmt->error; }
    }
}
?>
<div class="card"><div class="card-body"><h5>Registrar Préstamo</h5><?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>$e</div>"; ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">Usuario</label><select name="usuario_id" class="form-select"><option value="">--Seleccionar--</option><?php while($u = $usuarios->fetch_assoc()): ?><option value="<?php echo $u['idUsuario']; ?>" <?php echo ($u['idUsuario']==$usuario_id)?'selected':''; ?>><?php echo htmlspecialchars($u['nombre'].' '.$u['apellido']); ?></option><?php endwhile; ?></select></div>
  <div class="mb-3"><label class="form-label">Libro</label><select name="libro_id" class="form-select"><option value="">--Seleccionar--</option><?php while($l = $libros->fetch_assoc()): ?><option value="<?php echo $l['idLibro']; ?>" <?php echo ($l['idLibro']==$libro_id)?'selected':''; ?>><?php echo htmlspecialchars($l['titulo'].' ('.$l['estado'].')'); ?></option><?php endwhile; ?></select></div>
  <button class="btn btn-primary">Registrar préstamo</button> <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
