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

      .imagenActual {
        padding-left: 0px;
        padding-right: 20px;
      }

      .imagenInput {
        padding-left: 0px;
        padding-right: 0px;
      }

      @media (max-width: 767px) {

        .imagenInput,
        .imagenActual {
          padding: 0px;
        }

        .imagenActual img {
          width: 50%;
        }

        .imagenActual .marco {
          text-align: center;
        }

        .imagenActual .imgContenido {
          display: flex;
          width: 100%;
          justify-content: center;
        }
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Configuración de portada de acceso</h1>
                <div class="box-tools pull-right">
                </div>
              </div>
            </div>
            <div class="d-flex">
              <div class="col-lg-4 col-sm-6 imagenActual">
                <div class="box" style="border-top: none !important;">
                  <div class="panel-body marco" id="formularioregistros">
                    <label>Imagen actual:</label>
                    <div class="imgContenido">
                      <img src="" width="100%" id="imagenmuestra">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-8 col-sm-6 imagenInput">
                <div class="box" style="border-top: none !important">
                  <div class="panel-body marco" id="formularioregistros">
                    <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>Actualizar imagen(*):</label>
                        <input type="file" class="form-control" name="imagen" id="imagen" accept="image/x-png,image/gif,image/jpeg" required>
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
    </div>
    </section>
    </div>
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/confPortada1.js"></script>
<?php
}
ob_end_flush();
?>