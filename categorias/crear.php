<?php
require_once __DIR__ . '/../permiso_admin.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$errores = [];
$nombre = $descripcion = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    if ($nombre === "") $errores[] = "El nombre es obligatorio.";
    $stmt = $conn->prepare("SELECT idCategoria FROM categorias WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errores[] = "Ya existe una categoría con ese nombre.";
    if (empty($errores)) {
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);
        $stmt->execute();
        header("Location: listar.php");
        exit;
    }
}
?>
<div class="card"><div class="card-body">
<h4>Crear Categoría</h4>
<?php if ($errores): ?><div class="alert alert-danger"><?php foreach ($errores as $e) echo "<div>$e</div>"; ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>"></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea></div>
    <button class="btn btn-primary">Guardar</button> <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
