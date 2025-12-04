<?php
require_once __DIR__ . '/../seguridad.php';
require_once __DIR__ . '/../plantillas/encabezado.php';
?>
<div class="card">
  <div class="card-body">
    <h3>Reporte de usuarios activos/inactivos</h3>
    <p>Genera un listado y resumen de usuarios según su estado. Puedes exportar a CSV o PDF y guardarlo.</p>

    <form class="row g-3" method="GET" action="reporte_actividad_pdf.php" target="_blank">
      <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="guardar" value="1" id="chkGuardarUsr">
          <label class="form-check-label" for="chkGuardarUsr">Guardar reporte</label>
        </div>
      </div>
      <div class="col-12">
        <button class="btn btn-danger">Generar PDF</button>
        <a class="btn btn-secondary" target="_blank" id="btnCSV">Exportar CSV</a>
      </div>
    </form>

    <script>
      const formU = document.querySelector('form');
      const btnCsvU = document.getElementById('btnCSV');
      btnCsvU.addEventListener('click', function(){
        const params = new URLSearchParams(new FormData(formU));
        const url = 'reporte_actividad_csv.php?' + params.toString();
        window.open(url, '_blank');
      });
    </script>
  </div>
</div>

<?php require_once __DIR__ . '/../plantillas/pie.php'; ?>

