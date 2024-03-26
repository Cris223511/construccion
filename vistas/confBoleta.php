<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['perfilu'] == 1) {
?>
    <style>
      .marco {
        background-color: white;
        border-top: 3px #3686b4 solid;
      }
    </style>

    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Configuración de boletas</h1>
                <div class="box-tools pull-right">
                </div>
              </div>
            </div>
            <div class="d-flex">
              <div class="box" style="border-top: none !important">
                <div class="panel-body marco" id="formularioregistros">
                  <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <label>Empresa(*):</label>
                      <input type="hidden" name="idreporte" id="idreporte">
                      <input type="text" class="form-control" name="titulo" id="titulo" maxlength="30" placeholder="Ingrese el nombre de la empresa." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>RUC(*):</label>
                      <input type="number" class="form-control" name="ruc" id="ruc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="Ingrese el RUC." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Dirección:</label>
                      <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingrese la dirección." maxlength="50">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Teléfono:</label>
                      <input type="number" class="form-control" name="telefono" id="telefono" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="9" placeholder="Ingrese el teléfono.">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Email:</label>
                      <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Ingrese el email.">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Logo:</label>
                      <input type="file" class="form-control" name="imagen" id="imagen" accept="image/x-png,image/gif,image/jpeg">
                      <input type="hidden" name="imagenactual" id="imagenactual"><br>
                      <img src="" width="150px" height="150px" id="imagenmuestra">
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 0;">
                      <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                  </form>
                </div>
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
  <script type="text/javascript" src="scripts/confBoleta.js"></script>
<?php
}
ob_end_flush();
?>