<?php
require_once __DIR__ . '/../permiso_admin.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';
$conn = obtenerConexion();
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM categorias WHERE idCategoria = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { echo "<div class='alert alert-danger'>Categoría no encontrada</div>"; require_once __DIR__ . '/../plantillas/pie.php'; exit; }
$categoria = $res->fetch_assoc();
$errores = []; $nombre = $categoria['nombre']; $descripcion = $categoria['descripcion'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']); $descripcion = trim($_POST['descripcion']);
    if ($nombre === "") $errores[] = "El nombre es obligatorio.";
    $stmt = $conn->prepare("SELECT idCategoria FROM categorias WHERE nombre = ? AND idCategoria != ?");
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) $errores[] = "Ya existe otra categoría con ese nombre.";
    if (empty($errores)) {
        $stmt = $conn->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE idCategoria = ?");
        $stmt->bind_param("ssi", $nombre, $descripcion, $id); $stmt->execute();
        header("Location: listar.php"); exit;
    }
}
?>
<div class="card"><div class="card-body"><h4>Editar Categoría</h4>
<?php if ($errores): ?><div class="alert alert-danger"><?php foreach ($errores as $e) echo "<div>$e</div>"; ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>"></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea></div>
    <button class="btn btn-primary">Actualizar</button> <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
