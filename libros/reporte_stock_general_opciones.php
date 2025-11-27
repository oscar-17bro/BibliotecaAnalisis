<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';

// Solo admin o bibliotecario
$rol = $_SESSION['usuario']['rol_id'] ?? 0;
if ($rol != 1 && $rol != 2) {
    header("Location: ../no_autorizado.php");
    exit;
}
?>

<div class="card">
  <div class="card-body">
    <h3>Generar PDF de Stock General</h3>
    <p>Selecciona qué columnas quieres que salgan en el PDF:</p>

    <form action="reporte_stock_general_pdf.php" method="POST" target="_blank">

      <label><input type="checkbox" name="col_portada" checked> Portada</label><br>
      <label><input type="checkbox" name="col_titulo" checked> Título</label><br>
      <label><input type="checkbox" name="col_autor" checked> Autor</label><br>
      <label><input type="checkbox" name="col_isbn" checked> ISBN</label><br>
      <label><input type="checkbox" name="col_categoria" checked> Categoría</label><br>
      <label><input type="checkbox" name="col_estado" checked> Estado</label><br>

      <button class="btn btn-danger mt-3">Generar PDF</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
