<?php
require_once __DIR__ . '/../permiso_bibliotecario.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
require_once __DIR__ . '/../conexion.php';

$conn = obtenerConexion();
$result = $conn->query("SELECT * FROM categorias ORDER BY nombre");
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categorías</h2>
    <?php if ($_SESSION['usuario']['rol_id'] == 1): ?>
        <a class="btn btn-success" href="crear.php">+ Crear categoría</a>
    <?php endif; ?>
</div>
<table class="table table-striped table-hover">
    <thead class="table-light">
        <tr><th>Nombre</th><th>Descripción</th><?php if ($_SESSION['usuario']['rol_id'] == 1): ?><th>Acciones</th><?php endif; ?></tr>
    </thead>
    <tbody>
        <?php while ($c = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['descripcion']) ?></td>
            <?php if ($_SESSION['usuario']['rol_id'] == 1): ?>
            <td>
                <a class="btn btn-sm btn-primary" href="editar.php?id=<?= $c['idCategoria'] ?>">Editar</a>
                <a class="btn btn-sm btn-danger" href="eliminar.php?id=<?= $c['idCategoria'] ?>" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
