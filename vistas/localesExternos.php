<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['perfilu'] == 1 && ($_SESSION['cargo'] == "superadmin" || $_SESSION['cargo'] == "admin")) {
?>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Locales externos <!-- Configuración de locales -->
                  <!-- <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)">
                    <i class="fa fa-plus-circle"></i> Agregar
                  </button> -->
                  <a href="../reportes/rptlocalesexternos.php" target="_blank">
                    <button class="btn btn-secondary" style="color: black !important;">
                      <i class="fa fa-clipboard"></i> Reporte
                    </button>
                  </a>
                </h1>
                <div class="box-tools pull-right">
                </div>
                <div class="panel-body table-responsive listadoregistros" style="overflow-x: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                  <div class="form-group col-lg-5 col-md-5 col-sm-6 col-xs-12">
                    <label>Fecha Inicial:</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                  </div>
                  <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <label>Fecha Final:</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                  </div>
                  <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label id="label">ㅤ</label>
                    <div style="display: flex; gap: 10px;">
                      <button style="width: 80%;" class="btn btn-bcp" onclick="buscar()">Buscar</button>
                      <button style="width: 20%; height: 32px" class="btn btn-success" onclick="listar()"><i class="fa fa-repeat"></i></button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="table-responsive" style="padding: 8px !important; padding: 20px !important; background-color: white;">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                    <thead>
                      <th>Opciones</th>
                      <th>Logo</th>
                      <th>Ubicación del local</th>
                      <th style="white-space: nowrap;">N° RUC</th>
                      <th style="width: 40%; min-width: 280px; white-space: nowrap;">Descripción del local</th>
                      <th style="white-space: nowrap;">Fecha y hora</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Logo</th>
                      <th>Ubicación del local</th>
                      <th>N° RUC</th>
                      <th>Descripción del local</th>
                      <th>Fecha y hora</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" style="height: max-content;" id="formularioregistros">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Local(*):</label>
                    <input type="hidden" name="idlocal" id="idlocal">
                    <input type="text" class="form-control" name="titulo" id="titulo" maxlength="40" placeholder="Ingrese la ubicación del local." autocomplete="off" required>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>RUC(*):</label>
                    <input type="number" class="form-control" name="local_ruc" id="local_ruc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="Ingrese el N° de RUC del local." required>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Imagen:</label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.jfif,.bmp">
                    <input type="hidden" name="imagenactual" id="imagenactual"><br>
                    <img src="" width="150px" id="imagenmuestra">
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Descripción:</label>
                    <textarea type="text" class="form-control" name="descripcion" id="descripcion" maxlength="10000" rows="4" placeholder="Ingrese una descripción."></textarea>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                    <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Trabajadores del local <strong id="local"></strong></h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tbltrabajadores" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
              <thead>
                <TH>USUARIO</TH>
                <TH>CARGO</TH>
                <TH>NOMBRE</TH>
                <TH>DOCUMENTO</TH>
                <th>Número Doc.</th>
                <TH>TELÉFONO</TH>
                <TH>EMAIL</TH>
                <TH>FOTO</TH>
                <TH>ESTADO</TH>
              </THEAD>
              <TBODY>
              </TBODY>
              <TFOOT>
                <TH>USUARIO</TH>
                <TH>CARGO</TH>
                <TH>NOMBRE</TH>
                <TH>DOCUMENTO</TH>
                <th>Número Doc.</th>
                <TH>TELÉFONO</TH>
                <TH>EMAIL</TH>
                <TH>FOTO</TH>
                <TH>ESTADO</TH>
              </tfoot>
            </table>
            </tfoot>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal -->
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/localesExternos2.js"></script>
<?php
}
ob_end_flush();
?>