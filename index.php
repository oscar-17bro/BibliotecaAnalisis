<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/plantillas/encabezado.php';
$conn = obtenerConexion();
$q = trim($_GET['q'] ?? '');
$cat = intval($_GET['categoria'] ?? 0);
$disp = trim($_GET['disponibilidad'] ?? '');
$cats = $conn->query("SELECT idCategoria, nombre FROM categorias ORDER BY nombre");
$where = [];
$params = [];
$types = '';
if ($q !== '') {
  $where[] = "(l.titulo LIKE ? OR l.autor LIKE ? OR l.isbn = ?)";
  $like = "%$q%";
  $params[] = $like; $params[] = $like; $params[] = $q; $types .= 'sss';
}
if ($cat > 0) {
  $where[] = "l.categoria_id = ?";
  $params[] = $cat; $types .= 'i';
}
if ($disp !== '') {
  $where[] = "l.estado = ?";
  $params[] = $disp; $types .= 's';
}
$sql = "SELECT l.idLibro, l.titulo, l.autor, l.isbn, l.estado, l.portada, c.nombre as categoria FROM libros l LEFT JOIN categorias c ON c.idCategoria = l.categoria_id";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY l.titulo";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$stats = $conn->query("SELECT COUNT(*) as total, SUM(estado='Disponible') as disponibles, SUM(estado='Prestado') as prestados FROM libros")->fetch_assoc();
?>
<div class="py-5 bg-light mb-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-7">
        <h1 class="display-5 fw-bold">Bibliogestor</h1>
        <p class="lead">Busca libros por título, autor, categoría y disponibilidad. Para gestionar préstamos, inicia sesión.</p>
        <a class="btn btn-primary btn-lg" href="<?php echo URL_BASE; ?>/usuarios/login.php">Iniciar sesión</a>
      </div>
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="row text-center">
              <div class="col-4">
                <div class="h5 mb-0"><?php echo intval($stats['total'] ?? 0); ?></div>
                <div class="text-muted">Libros</div>
              </div>
              <div class="col-4">
                <div class="h5 mb-0"><?php echo intval($stats['disponibles'] ?? 0); ?></div>
                <div class="text-muted">Disponibles</div>
              </div>
              <div class="col-4">
                <div class="h5 mb-0"><?php echo intval($stats['prestados'] ?? 0); ?></div>
                <div class="text-muted">Prestados</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

<div class="container">
  <div class="card mb-4">
    <div class="card-body">
      <form class="row g-3" method="get">
        <div class="col-md-4">
          <input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Título, autor o ISBN">
        </div>
        <div class="col-md-3">
          <select class="form-select" name="categoria">
            <option value="0">Todas las categorías</option>
            <?php while($c = $cats->fetch_assoc()): ?>
              <option value="<?php echo $c['idCategoria']; ?>" <?php echo ($cat==$c['idCategoria'])?'selected':''; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" name="disponibilidad">
            <option value="">Todas</option>
            <option value="Disponible" <?php echo ($disp==='Disponible')?'selected':''; ?>>Disponible</option>
            <option value="Prestado" <?php echo ($disp==='Prestado')?'selected':''; ?>>Prestado</option>
          </select>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary">Buscar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <?php if ($res->num_rows === 0): ?>
      <div class="col-12"><div class="alert alert-info">Sin resultados</div></div>
    <?php endif; ?>
    <?php while($r = $res->fetch_assoc()): ?>
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm">
          <?php if ($r['portada']): ?>
            <img src="<?php echo URL_BASE.'/recursos/imagenes/'.htmlspecialchars($r['portada']); ?>" class="card-img-top" style="height:220px;object-fit:cover;">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-1"><?php echo htmlspecialchars($r['titulo']); ?></h6>
            <div class="text-muted mb-2"><?php echo htmlspecialchars($r['autor']); ?></div>
            <div class="small mb-2">Categoría: <?php echo htmlspecialchars($r['categoria'] ?? '-'); ?></div>
            <span class="badge <?php echo ($r['estado']==='Disponible')?'bg-success':'bg-secondary'; ?> align-self-start"><?php echo htmlspecialchars($r['estado']); ?></span>
            <div class="mt-auto">
              <a class="btn btn-outline-primary w-100 mt-2" href="<?php echo URL_BASE; ?>/usuarios/login.php">Inicia sesión para gestionar</a>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<?php require_once __DIR__ . '/plantillas/pie.php'; ?>
