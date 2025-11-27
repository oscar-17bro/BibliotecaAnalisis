<?php
require_once __DIR__ . '/../seguridad.php';

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
$conn = obtenerConexion();
$errors = [];
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo '<div class="alert alert-danger">ID inválido</div>'; require_once __DIR__.'/../plantillas/pie.php'; exit; }
$stmt = $conn->prepare('SELECT * FROM libros WHERE idLibro = ?'); $stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { echo '<div class="alert alert-danger">Libro no encontrado</div>'; require_once __DIR__.'/../plantillas/pie.php'; exit; }
$libro = $res->fetch_assoc(); $stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $autor = trim($_POST['autor'] ?? ''); $isbn = trim($_POST['isbn'] ?? '');
    $editorial = trim($_POST['editorial'] ?? ''); $anio = intval($_POST['anio'] ?? 0); $categoria_id = $_POST['categoria_id'] ? intval($_POST['categoria_id']) : null;
    $estado = $_POST['estado'] ?? 'Disponible';
    if ($titulo === '') $errors[] = 'Título es obligatorio.';
    if ($isbn !== '') {
        $stmt = $conn->prepare('SELECT idLibro FROM libros WHERE isbn = ? AND idLibro != ?'); $stmt->bind_param('si', $isbn, $id); $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'ISBN ya registrado por otro libro.'; $stmt->close();
    }
    $portada_nombre = $libro['portada'];
    if (!empty($_FILES['portada']['name'])) {
        $up = $_FILES['portada']; $allowed = ['image/jpeg','image/png','image/gif'];
        if ($up['error'] === 0 and in_array($up['type'], $allowed)) {
            $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
            $portada_nombre = uniqid('p_').'.'.$ext;
            $dest = __DIR__ . '/../recursos/imagenes/' . $portada_nombre;
            if (!move_uploaded_file($up['tmp_name'], $dest)) $errors[] = 'No se pudo subir la portada.'; else {
                if ($libro['portada'] && file_exists(__DIR__ . '/../recursos/imagenes/'.$libro['portada'])) @unlink(__DIR__ . '/../recursos/imagenes/'.$libro['portada']);
            }
        } else $errors[] = 'Portada inválida. Usa JPG/PNG/GIF.';
    }
    if (empty($errors)) {
        $stmt = $conn->prepare('UPDATE libros SET titulo=?, autor=?, isbn=?, editorial=?, anio=?, categoria_id=?, estado=?, portada=? WHERE idLibro=?');
        $stmt->bind_param('ssssisssi', $titulo, $autor, $isbn, $editorial, $anio, $categoria_id, $estado, $portada_nombre, $id);
        if ($stmt->execute()) { header('Location: listar.php?msg=Libro actualizado'); exit; } else $errors[] = 'Error actualizando: '.$stmt->error;
    }
} else {
    $titulo = $libro['titulo']; $autor = $libro['autor']; $isbn = $libro['isbn']; $editorial = $libro['editorial'];
    $anio = $libro['anio']; $categoria_id = $libro['categoria_id']; $estado = $libro['estado']; $portada_nombre = $libro['portada'];
}
$cats = $conn->query('SELECT idCategoria, nombre FROM categorias ORDER BY nombre');
?>
<div class="card"><div class="card-body"><h5>Editar Libro</h5><?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>$e</div>"; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3"><label class="form-label">Título</label><input class="form-control" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>"></div>
  <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Autor</label><input class="form-control" name="autor" value="<?php echo htmlspecialchars($autor); ?>"></div>
  <div class="col-md-6 mb-3"><label class="form-label">ISBN</label><input class="form-control" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>"></div></div>
  <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Editorial</label><input class="form-control" name="editorial" value="<?php echo htmlspecialchars($editorial); ?>"></div>
  <div class="col-md-3 mb-3"><label class="form-label">Año</label><input class="form-control" name="anio" type="number" value="<?php echo htmlspecialchars($anio); ?>"></div>
  <div class="col-md-3 mb-3"><label class="form-label">Categoría</label><select class="form-select" name="categoria_id"><option value="">--Sin categoría--</option><?php while($c = $cats->fetch_assoc()) echo "<option value='{$c['idCategoria']}' ".(($categoria_id==$c['idCategoria'])?'selected':'').">".htmlspecialchars($c['nombre'])."</option>"; ?></select></div></div>
  <div class="mb-3"><?php if ($portada_nombre): ?><div class="mb-2"><img src="<?php echo URL_BASE.'/recursos/imagenes/'.htmlspecialchars($portada_nombre); ?>" style="max-width:80px;max-height:100px;object-fit:cover;" class="rounded"></div><?php endif; ?><label class="form-label">Cambiar portada (opcional)</label><input class="form-control" type="file" name="portada" accept="image/*"></div>
  <div class="mb-3"><label class="form-label">Estado</label><select class="form-select" name="estado"><option value="Disponible" <?php echo ($estado=='Disponible')?'selected':''; ?>>Disponible</option><option value="Prestado" <?php echo ($estado=='Prestado')?'selected':''; ?>>Prestado</option></select></div>
  <button class="btn btn-primary">Actualizar</button> <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
