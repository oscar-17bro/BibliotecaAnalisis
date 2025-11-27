<?php
require_once __DIR__ . '/../seguridad.php';

// Solo admin y bibliotecario
$rol = $_SESSION['usuario']['rol_id'] ?? 0;
if ($rol != 1 && $rol != 2) {
    header("Location: " . URL_BASE . "/no_autorizado.php");
    exit;
}

require_once __DIR__ . '/../plantillas/encabezado.php';
?>

<div class="card">
  <div class="card-body">
    <h4 class="mb-3">Opciones para generar PDF del Stock</h4>

    <form action="reporte_stock_pdf.php" method="post">

      <p>Selecciona qué información quieres que aparezca en el PDF:</p>

      <label class="form-check">
        <input type="checkbox" name="categoria" class="form-check-input" checked>
        <span class="form-check-label">Categoría</span>
      </label>

      <label class="form-check">
        <input type="checkbox" name="total" class="form-check-input" checked>
        <span class="form-check-label">Total de libros</span>
      </label>

      <label class="form-check">
        <input type="checkbox" name="disponibles" class="form-check-input" checked>
        <span class="form-check-label">Libros disponibles</span>
      </label>

      <label class="form-check">
        <input type="checkbox" name="fecha" class="form-check-input">
        <span class="form-check-label">Fecha de generación</span>
      </label>

      <hr>

      <button class="btn btn-primary">Generar PDF</button>
      <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>
