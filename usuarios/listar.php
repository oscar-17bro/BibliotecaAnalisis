<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

$conn = obtenerConexion();
$q = trim($_GET['q'] ?? '');

// 1. DETERMINAR PERMISO:
// La variable $rolUsuarioActual se obtiene de la sesión (cargada por seguridad.php).
$rolUsuarioActual = $_SESSION['usuario']['rol_id'] ?? 0;
// Si el rol es 1 (Admin) o 2 (Bibliotecario), pueden administrar.
$puedeAdministrar = ($rolUsuarioActual <= 2); 

if ($q === '') {
    $stmt = $conn->prepare("SELECT u.idUsuario, u.nombre, u.apellido, u.identificacion, u.correo, r.nombre as rol, u.estado, u.rol_id FROM usuarios u JOIN roles r ON r.idRol = u.rol_id ORDER BY u.nombre");
    $stmt->execute();
} else {
    $like = "%$q%";
    $stmt = $conn->prepare("SELECT u.idUsuario, u.nombre, u.apellido, u.identificacion, u.correo, r.nombre as rol, u.estado, u.rol_id FROM usuarios u JOIN roles r ON r.idRol = u.rol_id WHERE u.nombre LIKE ? OR u.apellido LIKE ? OR u.identificacion = ? ORDER BY u.nombre");
    $stmt->bind_param('sss', $like, $like, $q);
    $stmt->execute();
}
$res = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Usuarios</h2>
 <a class="btn btn-info" href="historial.php">historial</a>
  <a class="btn btn-danger" href="notificaciones.php">Notificacion</a>
  
  <?php if ($puedeAdministrar): // Muestra el botón Crear solo para Roles 1 y 2 ?>
    <a class="btn btn-success" href="crear.php">+ Crear usuario</a>

   
  <?php endif; ?>

</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto"><input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Nombre o identificación"></div>
  <div class="col-auto"><button class="btn btn-primary">Buscar</button> <a href="listar.php" class="btn btn-outline-secondary">Limpiar</a></div>
</form>

<table class="table table-striped table-hover">
<thead class="table-light">
  <tr>
    <th>Nombre</th>
    <th>Identificación</th>
    <th>Correo</th>
    <th>Rol</th>
    <th>Estado</th>
    <?php if ($puedeAdministrar): // Muestra el encabezado "Acciones" solo para Roles 1 y 2 ?>
      <th>Acciones</th>
    <?php endif; ?>
  </tr>
</thead>
<tbody>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
  <td><?php echo htmlspecialchars($row['nombre'].' '.$row['apellido']); ?></td>
  <td><?php echo htmlspecialchars($row['identificacion']); ?></td>
  <td><?php echo htmlspecialchars($row['correo']); ?></td>
  <td><?php echo htmlspecialchars($row['rol']); ?></td>
  <td><?php echo $row['estado'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Suspendido</span>'; ?></td>
  
  <?php if ($puedeAdministrar): // Muestra los botones de Editar/Eliminar solo para Roles 1 y 2 ?>
    <td>
      <!-- Se mantiene la lógica de editar y eliminar -->
      <a class="btn btn-sm btn-primary" href="editar.php?id=<?php echo $row['idUsuario']; ?>">Editar</a> 
      <a class="btn btn-sm btn-danger" href="eliminar.php?id=<?php echo $row['idUsuario']; ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
    </td>
  <?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>