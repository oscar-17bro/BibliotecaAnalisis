<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
?>
<div class="card">
  <div class="card-body">
    <h3>Reporte de préstamos por categoría</h3>
    <p>Selecciona el mes y año para ver cuántos préstamos se realizaron por categoría.</p>

    <form class="row g-3" method="GET" action="reporte_categoria_pdf.php" target="_blank">
      <div class="col-md-3">
        <label class="form-label">Mes</label>
        <select class="form-select" name="mes" required>
          <?php for($m=1;$m<=12;$m++): ?>
            <option value="<?= $m ?>" <?= (date('n')==$m)?'selected':''; ?>><?= $m ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Año</label>
        <input class="form-control" type="number" name="anio" value="<?= date('Y') ?>" min="2000" max="2100" required>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="guardar" value="1" id="chkGuardarCat">
          <label class="form-check-label" for="chkGuardarCat">Guardar reporte</label>
        </div>
      </div>
      <div class="col-12">
        <button class="btn btn-danger">Generar PDF</button>
        <a class="btn btn-secondary" target="_blank" id="btnCSV">Exportar CSV</a>
      </div>
    </form>

    <script>
      const formCat = document.querySelector('form');
      const btnCsvCat = document.getElementById('btnCSV');
      btnCsvCat.addEventListener('click', function(){
        const params = new URLSearchParams(new FormData(formCat));
        const url = 'reporte_categoria_csv.php?' + params.toString();
        window.open(url, '_blank');
      });
    </script>
  </div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>

