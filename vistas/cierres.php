<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["cajas"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['cajas'] == 1) {
?>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Cierre de caja
                  <a href="../reportes/rptcajas.php" target="_blank">
                    <button class="btn btn-secondary" style="color: black !important;">
                      <i class="fa fa-clipboard"></i> Reporte
                    </button>
                  </a>
                </h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body table-responsive listadoregistros" style="overflow-x: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Fecha Inicial:</label>
                  <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Fecha Final:</label>
                  <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Buscar por local:</label>
                  <select id="idlocal" name="idlocal" class="form-control selectpicker" data-live-search="true" data-size="5">
                    <option value="">- Seleccione -</option>
                  </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                  <label id="label">ㅤ</label>
                  <div style="display: flex; gap: 10px;">
                    <button style="width: 80%;" class="btn btn-bcp" onclick="buscar()">Buscar</button>
                    <button style="width: 20%; height: 32px" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
                  </div>
                </div>
              </div>
              <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="table-responsive" style="padding: 8px !important; padding: 20px !important; background-color: white;">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                    <thead>
                      <th>Opciones</th>
                      <th>Nombre</th>
                      <th style="width: 20%; min-width: 220px; white-space: nowrap;">Ubicación del local</th>
                      <th style="white-space: nowrap;">Usuario</th>
                      <th>Cargo</th>
                      <th>Monto</th>
                      <th style="white-space: nowrap;">Fecha inicio</th>
                      <th style="white-space: nowrap;">Fecha cierre</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Nombre</th>
                      <th>Ubicación del local</th>
                      <th>Usuario</th>
                      <th>Cargo</th>
                      <th>Monto</th>
                      <th>Fecha inicio</th>
                      <th>Fecha cierre</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/cierres6.js"></script>
<?php
}
ob_end_flush();
?>