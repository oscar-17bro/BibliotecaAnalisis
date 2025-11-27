<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

$conn = obtenerConexion();
$q = trim($_GET['q'] ?? '');

// 1. PERMISOS (igual que en tu código de usuarios):
$rolUsuarioActual = $_SESSION['usuario']['rol_id'] ?? 0;
// Rol 1 o 2 pueden administrar, rol 3 NO
$puedeAdministrar = ($rolUsuarioActual <= 2);

if ($q === '') {
    $stmt = $conn->prepare("SELECT l.idLibro, l.titulo, l.autor, l.isbn, l.estado, l.portada, c.nombre as categoria 
                            FROM libros l LEFT JOIN categorias c ON c.idCategoria = l.categoria_id 
                            ORDER BY l.titulo");
    $stmt->execute();
} else {
    $like = "%$q%";
    $stmt = $conn->prepare("SELECT l.idLibro, l.titulo, l.autor, l.isbn, l.estado, l.portada, c.nombre as categoria 
                            FROM libros l LEFT JOIN categorias c ON c.idCategoria = l.categoria_id 
                            WHERE l.titulo LIKE ? OR l.autor LIKE ? OR l.isbn = ? 
                            ORDER BY l.titulo");
    $stmt->bind_param('sss', $like, $like, $q);
    $stmt->execute();
}
$res = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Libros</h2>

  <?php if ($puedeAdministrar): ?>
      <a class="btn btn-success" href="crear.php">+ Agregar libro</a>
      <a class="btn btn-info ms-2" href="reporte_stock.php">Generar stock  categoria</a>
      <a class="btn btn-warning ms-2" href="reporte_stock_general_opciones.php">Stock General PDF</a>

  <?php endif; ?>

</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
      <input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Título, autor o ISBN">
  </div>
  <div class="col-auto">
      <button class="btn btn-primary">Buscar</button>
      <a href="listar.php" class="btn btn-outline-secondary">Limpiar</a>
  </div>
</form>

<table class="table table-hover">
<thead class="table-light">
<tr>
  <th>Portada</th>
  <th>Título</th>
  <th>Autor</th>
  <th>ISBN</th>
  <th>Categoría</th>
  <th>Estado</th>

  <?php if ($puedeAdministrar): ?>
      <th>Acciones</th>
  <?php endif; ?>

</tr>
</thead>
<tbody>

<?php while($r = $res->fetch_assoc()): ?>
<tr>
  <td>
    <?php if ($r['portada']): ?>
        <img src="<?php echo URL_BASE.'/recursos/imagenes/'.htmlspecialchars($r['portada']); ?>" style="max-width:70px;max-height:90px;object-fit:cover;" class="rounded">
    <?php else: ?>
        <span class="text-muted">-</span>
    <?php endif; ?>
  </td>

  <td><?php echo htmlspecialchars($r['titulo']); ?></td>
  <td><?php echo htmlspecialchars($r['autor']); ?></td>
  <td><?php echo htmlspecialchars($r['isbn']); ?></td>
  <td><?php echo htmlspecialchars($r['categoria']); ?></td>
  <td><?php echo htmlspecialchars($r['estado']); ?></td>

  <?php if ($puedeAdministrar): ?>
  <td>
      <a class="btn btn-sm btn-primary" href="editar.php?id=<?php echo $r['idLibro']; ?>">Editar</a>
      <a class="btn btn-sm btn-danger" href="eliminar.php?id=<?php echo $r['idLibro']; ?>" onclick="return confirm('¿Eliminar libro?')">Eliminar</a>
     

    </td>
  <?php endif; ?>

</tr>
<?php endwhile; ?>

</tbody>
</table>


<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
