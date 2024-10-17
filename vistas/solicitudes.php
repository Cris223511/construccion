<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require '../config/Conexion.php';
  require 'header.php';

  if ($_SESSION['prestamo'] == 1) {
?>

    <style>
      .popover {
        z-index: 10000 !important;
        width: 200px !important;
      }

      .box-title .popover {
        z-index: 10000 !important;
        width: 300px !important;
        max-width: max-content !important;
        max-height: 500px !important;
        overflow-x: hidden !important;
      }

      table .popover {
        z-index: 10000 !important;
        width: 190px !important;
      }

      @media (max-width: 991px) {
        .label_serie {
          width: 100px !important;
        }
      }

      @media (max-width: 767px) {
        label {
          width: 100px !important;
        }
      }
    </style>

    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12" style="overflow-x: visible !important;">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Solicitud de materiales
                  <?php
                  if (($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado')) {
                  ?>
                    <a data-toggle="modal" href="#myModal2">
                      <button type="button" class="btn btn-bcp" id="btnInsertarArt" onclick="limpiar()">
                        <i class="fa fa-plus-circle"></i> Agregar Solicitud
                      </button>
                    </a>
                  <?php
                  }
                  ?>
                  <?php if ($_SESSION["cargo"] == "superadmin" || $_SESSION["cargo"] == "admin" || $_SESSION["cargo"] == "encargado") { ?>
                    <a href="../reportes/rptsolicitudes.php" target="_blank">
                      <button class="btn btn-secondary" style="color: black !important;">
                        <i class="fa fa-clipboard"></i> Reporte
                      </button>
                    </a>
                  <?php } ?>
                  &nbsp;<a href="#" data-toggle="popover" data-placement="bottom" title="Información de módulo solicitud" data-html="true" data-content="Módulo en el que se solicita productos del almacén para que sean prestados. El <strong>emisor del pedido</strong> (o el encargado) es el quien solicita, el <strong>receptor del pedido</strong> (o el almacenero / despachador) es el quien acepta o no la solicitud del préstamo. Una vez acepte, el stock del producto solicitado se reduce del almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
                </h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body table-responsive" style="margin-bottom: 20px;" id="listadoregistros">
                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                  <thead>
                    <th style="width: 12%;">Opciones</th>
                    <th>Código</th>
                    <th>Fecha pedido</th>
                    <th>Fecha despacho</th>
                    <th>Usuario emisor del pedido</th>
                    <th>Usuario receptor del pedido</th>
                    <th>Empresa</th>
                    <th>Lugar de destino</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>Opciones</th>
                    <th>Código</th>
                    <th>Fecha pedido</th>
                    <th>Fecha despacho</th>
                    <th>Usuario emisor del pedido</th>
                    <th>Usuario receptor del pedido</th>
                    <th>Empresa</th>
                    <th>Lugar de destino</th>
                    <th>Telefono</th>
                    <th>Estado</th>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Modal 4 -->
    <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">ACEPTAR SOLICITUD:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario3" id="formulario3" method="POST">
              <?php if (($_SESSION['cargo'] == 'superadmin')) { ?>
                <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <label>Usuario emisor del pedido(*):</label> <a href="#" data-toggle="popover" data-placement="bottom" title="Usuario emisor del pedido" data-html="true" data-content="Es el usuario que ha solicitado el préstamo de materiales del almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
                  <select id="idencargado" class="form-control selectpicker" data-size="5" data-dropup-auto="false" disabled></select>
                </div>
              <?php } ?>
              <div class="<?php echo ($_SESSION['cargo'] == 'superadmin') ? 'form-group col-lg-6 col-md-6 col-sm-12 col-xs-12' : 'form-group col-lg-12 col-md-12 col-sm-12 col-xs-12'; ?>">
                <label>Usuario receptor del pedido(*):</label> <?php if (($_SESSION['cargo'] == 'superadmin')) { ?><a href="#" data-toggle="popover" data-placement="bottom" title="Usuario receptor del pedido" data-html="true" data-content="Selecciona al usuario que aceptará el préstamo de los productos al solicitante (el emisor del pedido)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a><?php } ?>
                <input type="hidden" name="idsolicitud" id="idsolicitud2">
                <select id="idalmacenero2" name="receptor" class="form-control selectpicker" data-size="5" data-dropup-auto="false" <?php echo ($_SESSION['cargo'] != 'superadmin') ? 'disabled' : 'required'; ?>>
                  <option value="">- Sin registrar -</option>
                </select>
              </div>

              <div class="row" style="padding-left: 15px; padding-right: 15px;">
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Código(*):</label>
                  <input type="text" class="form-control" name="codigo_pedido" id="codigo_pedido2" maxlength="10" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Teléfono:</label>
                  <input type="number" class="form-control" name="telefono" id="telefono2" maxlength="9" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <label class="label_serie">Nombre o empresa:</label>
                  <input type="text" class="form-control" name="empresa" id="empresa2" maxlength="50" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-12 col-md-12">
                  <label>Lugar de destino:</label>
                  <input type="text" class="form-control" name="destino" id="destino2" maxlength="100" placeholder="Sin registrar." disabled>
                </div>
              </div>

              <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="overflow-x: visible !important;">
                <div class="table-responsive" style="margin-bottom: 20px;">
                  <table id="detalles2" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                    <thead style="background-color:#A9D0F5">
                      <th>Opciones</th>
                      <th>Artículo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Local</th>
                      <th>Precio compra</th>
                      <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el receptor de pedido prestó." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad a Prestar <a href="#" data-toggle="popover" data-placement="top" title="Cantidad a Prestar" data-content="Digita la cantidad que deseas prestar al emisor de pedido (no debe superar la cantidad solicitada)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Estado</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar3"><i class="fa fa-save"></i> Guardar</button>
                <!-- <button class="btn btn-info" type="button" id="btnProbar" onclick="probarDatos()"><i class="fa fa-bug"></i> Probar</button> -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 4 -->

    <!-- Modal 3 -->
    <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">COMENTARIOS:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario2" id="formulario2" method="POST">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Comentario(*):</label>
                <input type="hidden" name="idsolicitud" id="idsolicitud">
                <textarea type="text" class="form-control" style="resize: none;" name="comentario" id="comentario" maxlength="200" rows="4" placeholder="Sin registrar."></textarea>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar2"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 3 -->

    <!-- Modal 2 -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">REGISTRO DE SOLICITUD:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario" id="formulario" method="POST">
              <input type="hidden" name="idsolicitud" id="idsolicitud">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 despachador">
                <label>Usuario receptor del pedido(*):</label>
                <select id="idalmacenero" name="idalmacenero" class="form-control selectpicker" data-size="5" disabled>
                  <option value="">- Sin registrar -</option>
                </select>
              </div>
              <div class="row" style="padding-left: 15px; padding-right: 15px;">
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Código(*):</label>
                  <input type="text" class="form-control" name="codigo_pedido" id="codigo_pedido" oninput="onlyNumbersAndMaxLenght(this)" onblur="formatearNumero(this)" maxlength="20" onpaste="false" ondrop="false" placeholder="Ingrese el código correlativo." required>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Teléfono:</label>
                  <input type="number" class="form-control" name="telefono" id="telefono" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="9" placeholder="Ingrese el número telefónico.">
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <label class="label_serie">Nombre o empresa:</label>
                  <input type="text" class="form-control" name="empresa" id="empresa" maxlength="50" placeholder="Ingrese la empresa.">
                </div>
                <?php if (($_SESSION['cargo'] == 'superadmin')) { ?>
                  <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label>Usuario emisor del pedido(*):</label> <a href="#" data-toggle="popover" data-placement="top" title="Usuario emisor del pedido" data-html="true" data-content="Selecciona al usuario que va a solicitar prestado los productos del almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
                    <select name="emisor" id="emisor" class="form-control selectpicker" data-size="5" data-dropup-auto="false" required></select>
                  </div>
                  <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label>Lugar de destino:</label>
                    <input type="text" class="form-control" name="destino" id="destino" maxlength="100" placeholder="Ingrese el lugar de destino.">
                  </div>
                <?php } else { ?>
                  <div class="form-group col-lg-12 col-md-12">
                    <label>Lugar de destino:</label>
                    <input type="text" class="form-control" name="destino" id="destino" maxlength="100" placeholder="Ingrese el lugar de destino.">
                  </div>
                <?php } ?>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float: left;">
                <a data-toggle="modal" href="#myModal">
                  <button id="btnAgregarArt" type="button" class="btn btn-bcp"> <span class="fa fa-plus"></span> Agregar artículo</button>
                </a>
              </div>

              <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="overflow-x: visible !important;">
                <div class="table-responsive" style="margin-bottom: 20px;">
                  <table id="detalles" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                    <thead style="background-color:#A9D0F5">
                      <th>Opciones</th>
                      <th>Artículo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Local</th>
                      <th>Precio compra</th>
                      <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el receptor de pedido prestó." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Estado</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 2 -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 95% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">SELECCIONE UN PRODUCTO</h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
              <thead>
                <th>Opciones</th>
                <th>Imagen</th>
                <th style="width: 20%; min-width: 150px;">Nombre</th>
                <th style="white-space: nowrap;">U. medida</th>
                <th style="width: 20%; min-width: 300px;">Descripción</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th style="white-space: nowrap;">Ubicación del local</th>
                <th style="white-space: nowrap;">Stock normal</th>
                <th style="white-space: nowrap;">Stock mínimo</th>
                <th style="white-space: nowrap;">P. Compra</th>
                <!-- <th style="white-space: nowrap;">P. Compra Mayor</th> -->
                <th style="white-space: nowrap;">C. producto</th>
                <th style="white-space: nowrap;">C. de barra</th>
                <th style="width: 20%; min-width: 200px;">Talla</th>
                <th style="width: 20%; min-width: 200px;">Color</th>
                <th>Peso</th>
                <th style="white-space: nowrap;">Fecha emisión</th>
                <th style="white-space: nowrap;">Fecha vencimiento</th>
                <th style="width: 20%; min-width: 200px;">Nota 1</th>
                <th style="width: 20%; min-width: 200px;">Nota 2</th>
                <th style="width: 20%; min-width: 200px;">Nota 3 (IMEI)</th>
                <th style="width: 20%; min-width: 200px;">Nota 4 (Serial)</th>
                <th style="white-space: nowrap;">Agregado por</th>
                <th>Cargo</th>
                <th style="white-space: nowrap;">Fecha y hora</th>
                <th>Estado</th>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <th>Opciones</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>U. medida</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Ubicación del local</th>
                <th>Stock normal</th>
                <th>Stock mínimo</th>
                <th>P. Compra</th>
                <!-- <th>P. Compra Mayor</th> -->
                <th>C. producto</th>
                <th>C. de barra</th>
                <th>Talla</th>
                <th>Color</th>
                <th>Peso</th>
                <th>Fecha emisión</th>
                <th>Fecha vencimiento</th>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Nota 3 (IMEI)</th>
                <th>Nota 4 (Serial)</th>
                <th>Agregado por</th>
                <th>Cargo</th>
                <th>Fecha y hora</th>
                <th>Estado</th>
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
  <div id="script"></div>
  <script type="text/javascript" src="scripts/solicitudes34.js"></script>
<?php
}
ob_end_flush();
?>