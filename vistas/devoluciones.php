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

      .selects .popover {
        z-index: 10000 !important;
        width: 320px !important;
        max-width: max-content !important;
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
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Devolución de materiales
                  <?php if ($_SESSION["cargo"] == "superadmin" || $_SESSION["cargo"] == "admin" || $_SESSION["cargo"] == "encargado") { ?>
                    <a href="../reportes/rptdevoluciones.php" target="_blank">
                      <button class="btn btn-secondary" style="color: black !important;">
                        <i class="fa fa-clipboard"></i> Reporte
                      </button>
                    </a>
                  <?php } ?>
                  &nbsp;<a href="#" data-toggle="popover" data-placement="bottom" title="Información de Estados" data-html="true" data-content="Estas son la lista de las solicitudes que tienen la posibilidad de devolver artículos al almacén (que en este módulo se les considera como devoluciones). Sólo se listan las devoluciones en el que su estado sea pendiente, en curso o finalizado. Si algunas solicitudes no se muestran, es porque su estado está anulado o rechazado. <br><br> A continuación, se detallará la explicación de los estados de las devoluciones: <br><br> <strong>Pendiente:</strong> Indica que recién van a comenzarse a devolver los artículos al almacén (cantidad solicitada a devolver igual a 0). <br><br> <strong>En curso:</strong> Indica que se comenzó a hacer la solicitud de devolución de los artículos al almacén (cantidad solicitada a devolver mayor a 0). <br><br> <strong>Finalizado:</strong> Indica que la devolución se procesó con éxito (es decir, fue aceptado por el receptor del pedido (almacenero) y los artículos fueron devueltos al almacén correctamente)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
                </h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
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

    <!-- Modal 5 -->
    <div class="modal fade" id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">ACEPTAR DEVOLUCIÓN:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario4" id="formulario4" method="POST">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Usuario receptor del pedido(*):</label>
                <input type="hidden" name="iddevolucion" id="iddevolucion3">
                <select id="idalmacenero3" name="idalmacenero" class="form-control selectpicker" data-live-search="true" data-size="5" data-dropup-auto="false" disabled>
                  <option value="">- Seleccione -</option>
                </select>
              </div>
              <div class="row" style="padding-left: 15px; padding-right: 15px;">
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Código(*):</label>
                  <input type="text" class="form-control" name="codigo_pedido" id="codigo_pedido3" maxlength="10" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Teléfono:</label>
                  <input type="number" class="form-control" name="telefono" id="telefono3" maxlength="9" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <label class="label_serie">Nombre o empresa(*):</label>
                  <input type="text" class="form-control" name="empresa" id="empresa3" maxlength="50" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-12 col-md-12">
                  <label>Lugar de destino:</label>
                  <input type="text" class="form-control" name="destino" id="destino3" maxlength="100" placeholder="Sin registrar." disabled>
                </div>
                <hr>
                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 selects">
                  <label class="label_serie">Seleccione una opción(*): &nbsp;<a href="#" data-toggle="popover" data-placement="right" title="Tipos de envío" data-html="true" data-content="Selecciona almenos una de las opciones. Si selecciona la opción de <strong>almacén de origen</strong>, la cantidad a devolver, que es digitada por el emisor del pedido (encargado), se devolverá al almacén en el que te encuentras actualmente, aumentando el stock del producto devuelto; mientras que la opción de <strong>almacén de devolución</strong>, envías el producto a un almacén de productos devueltos, que hace referencia a los productos que están malogrados y no están disponible para ser utilizados en las ventas, compras, proformas y cuotas. (Puedes editar el registro y cambiar la opción en cualquier momento exepto si se selecciona la opción de <strong>almacén de origen</strong>, ya que esta opción envía los artículos al almacén y no se pueden devolver nuevamente)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></label><br>
                  <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="radio" class="opcion" name="opcion" value="1" required> Almacén de origen
                    <input type="radio" class="opcion" name="opcion" value="2" required> Almacén de devolución
                  </div>
                </div>
              </div>

              <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="overflow-x: visible;">
                <div class="table-responsive" style="margin-bottom: 20px;">
                  <table id="detalles3" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                    <thead style="background-color:#A9D0F5">
                      <th>Opciones</th>
                      <th>Artículo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Local</th>
                      <th>Precio venta</th>
                      <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad que solicitaste prestar al receptor del pedido." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que te prestó el receptor del pedido (almacenero)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad a devolver <a href="#" data-toggle="popover" data-placement="top" title="Cantidad a devolver" data-content="Es la cantidad que el emisor del pedido (encargado) solicitó devolver al almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Estado</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar4"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 5 -->

    <!-- Modal 4 -->
    <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">SOLICITAR EVOLUCIÓN:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario3" id="formulario3" method="POST">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Usuario receptor del pedido(*):</label>
                <input type="hidden" name="iddevolucion" id="iddevolucion2">
                <select id="idalmacenero2" name="idalmacenero" class="form-control selectpicker" data-live-search="true" data-size="5" data-dropup-auto="false" disabled>
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
                  <label class="label_serie">Nombre o empresa(*):</label>
                  <input type="text" class="form-control" name="empresa" id="empresa2" maxlength="50" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-12 col-md-12">
                  <label>Lugar de destino:</label>
                  <input type="text" class="form-control" name="destino" id="destino2" maxlength="100" placeholder="Sin registrar." disabled>
                </div>
              </div>

              <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="overflow-x: visible;">
                <div class="table-responsive" style="margin-bottom: 20px;">
                  <table id="detalles2" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                    <thead style="background-color:#A9D0F5">
                      <th>Opciones</th>
                      <th>Artículo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Local</th>
                      <th>Precio venta</th>
                      <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad que solicitaste prestar al receptor del pedido." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que te prestó el receptor del pedido (almacenero)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad solicitada a devolver <a href="#" data-toggle="popover" data-placement="top" title="Cantidad solicitada a devolver" data-content="Es la cantidad que solicitaste devolver al almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Solicitar cantidad a devolver <a href="#" data-toggle="popover" data-placement="top" title="Solicitar cantidad a devolver" data-content="Digita la cantidad que deseas devolver al almacén (no debe superar la cantidad prestada)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Estado</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar3"><i class="fa fa-save"></i> Guardar</button>
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
                <input type="hidden" name="iddevolucion" id="iddevolucion">
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
            <h4 class="modal-title infotitulo">REGISTRO DE DEVOLUCIÓN:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario" id="formulario" method="POST">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Usuario receptor del pedido(*):</label>
                <input type="hidden" name="iddevolucion" id="iddevolucion">
                <select id="idalmacenero" name="idalmacenero" class="form-control selectpicker" data-live-search="true" data-size="5" data-dropup-auto="false" disabled>
                  <option value="">- Seleccione -</option>
                </select>
              </div>
              <div class="row" style="padding-left: 15px; padding-right: 15px;">
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Código(*):</label>
                  <input type="text" class="form-control" name="codigo_pedido" id="codigo_pedido" maxlength="10" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Teléfono:</label>
                  <input type="number" class="form-control" name="telefono" id="telefono" maxlength="9" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
                  <label class="label_serie">Nombre o empresa(*):</label>
                  <input type="text" class="form-control" name="empresa" id="empresa" maxlength="50" placeholder="Sin registrar." disabled>
                </div>
                <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <label>Usuario emisor del pedido(*):</label> <a href="#" data-toggle="popover" data-placement="bottom" title="Usuario emisor del pedido" data-html="true" data-content="Es el usuario que ha solicitado el préstamo de materiales del almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a>
                  <select id="idencargado" class="form-control selectpicker" data-size="5" data-dropup-auto="false" disabled></select>
                </div>
                <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <label>Lugar de destino:</label>
                  <input type="text" class="form-control" name="destino" id="destino" maxlength="100" placeholder="Sin registrar." disabled>
                </div>
              </div>

              <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="overflow-x: visible;">
                <div class="table-responsive" style="margin-bottom: 20px;">
                  <table id="detalles" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important; margin-bottom: 0px;">
                    <thead style="background-color:#A9D0F5">
                      <th>Opciones</th>
                      <th>Artículo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Local</th>
                      <th>Precio venta</th>
                      <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad que solicitaste prestar al receptor del pedido." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que te prestó el receptor del pedido (almacenero)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Cantidad solicitada a devolver <a href="#" data-toggle="popover" data-placement="top" title="Cantidad solicitada a devolver" data-content="Es la cantidad que solicitaste devolver al almacén." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                      <th>Estado</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                <button id="btnCancelar" class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Enviar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 2 -->
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <div id="script"></div>
  <script type="text/javascript" src="scripts/devoluciones17.js"></script>
<?php
}
ob_end_flush();
?>