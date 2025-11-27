<?php
require_once __DIR__ . '/../seguridad.php';

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
$conn = obtenerConexion();
$errors = []; $titulo=$autor=$isbn=$editorial=''; $anio=''; $categoria_id=null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $editorial = trim($_POST['editorial'] ?? '');
    $anio = intval($_POST['anio'] ?? 0);
    $categoria_id = $_POST['categoria_id'] ? intval($_POST['categoria_id']) : null;
    if ($titulo === '') $errors[] = 'Título es obligatorio.';
    if ($isbn !== '') {
        $stmt = $conn->prepare('SELECT idLibro FROM libros WHERE isbn = ?'); $stmt->bind_param('s',$isbn); $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'ISBN ya existe.'; $stmt->close();
    }
    $portada_nombre = null;
    if (!empty($_FILES['portada']['name'])) {
        $up = $_FILES['portada']; $allowed = ['image/jpeg','image/png','image/gif'];
        if ($up['error'] === 0 and in_array($up['type'], $allowed)) {
            $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
            $portada_nombre = uniqid('p_').'.'.$ext;
            $dest = __DIR__ . '/../recursos/imagenes/' . $portada_nombre;
            if (!move_uploaded_file($up['tmp_name'], $dest)) $errors[] = 'No se pudo subir la portada.';
        } else $errors[] = 'Portada inválida. Usa JPG/PNG/GIF.';
    }
    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO libros (titulo, autor, isbn, editorial, anio, categoria_id, portada) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssiss', $titulo, $autor, $isbn, $editorial, $anio, $categoria_id, $portada_nombre);
        if ($stmt->execute()) { header('Location: listar.php?msg=Libro creado'); exit; } else $errors[] = 'Error: '.$stmt->error;
    }
}
$cats = $conn->query('SELECT idCategoria, nombre FROM categorias ORDER BY nombre');
?>
<div class="card"><div class="card-body"><h5>Agregar Libro</h5><?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo "<div>$e</div>"; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3"><label class="form-label">Título</label><input class="form-control" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>"></div>
  <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Autor</label><input class="form-control" name="autor" value="<?php echo htmlspecialchars($autor); ?>"></div>
  <div class="col-md-6 mb-3"><label class="form-label">ISBN</label><input class="form-control" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>"></div></div>
  <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Editorial</label><input class="form-control" name="editorial" value="<?php echo htmlspecialchars($editorial); ?>"></div>
  <div class="col-md-3 mb-3"><label class="form-label">Año</label><input class="form-control" name="anio" type="number" value="<?php echo htmlspecialchars($anio); ?>"></div>
  <div class="col-md-3 mb-3"><label class="form-label">Categoría</label><select class="form-select" name="categoria_id"><option value="">--Sin categoría--</option><?php while($c = $cats->fetch_assoc()) echo "<option value='{$c['idCategoria']}'>".htmlspecialchars($c['nombre'])."</option>"; ?></select></div></div>
  <div class="mb-3"><label class="form-label">Portada (JPG/PNG/GIF)</label><input class="form-control" type="file" name="portada" accept="image/*"></div>
  <button class="btn btn-primary">Guardar</button> <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form></div></div>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
