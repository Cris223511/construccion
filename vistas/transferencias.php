<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['transferencias'] == 1) {
?>
    <style>
      .botonArt {
        margin-top: 25px;
        margin-bottom: 0;
      }

      #label2 {
        display: block;
      }

      #form_codigo_barra {
        margin-top: 0;
        margin-bottom: 15px;
      }

      @media (max-width: 767px) {
        .botonArt {
          margin-top: 15px;
          margin-bottom: 0;
        }

        #form_codigo_barra {
          margin-top: 15px;
          margin-bottom: 25px;
        }

        #label2 {
          display: none;
        }
      }

      input[id="cantidad[]"] {
        width: 150px;
      }
    </style>

    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Transferencias
                  <?php if ($_SESSION["cargo"] != "mirador") { ?>
                    <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)">
                      <i class="fa fa-plus-circle"></i> Nueva transferencia
                    </button>
                  <?php } ?>
                  <a href="../reportes/rpttransferencias.php" target="_blank">
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
                      <th style="white-space: nowrap;">Fecha y hora</th>
                      <th style="white-space: nowrap;">N° de documento</th>
                      <th>Cantidad transferida</th>
                      <th>Local de origen</th>
                      <th>Local de destino</th>
                      <th style="width: 20%; min-width: 200px;">Comentario</th>
                      <th style="white-space: nowrap;">Agregado por</th>
                      <th>Cargo</th>
                      <!-- <th>Estado</th> -->
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Fecha y hora</th>
                      <th>N° de documento</th>
                      <th>Cantidad transferida</th>
                      <th>Local de origen</th>
                      <th>Local de destino</th>
                      <th>Comentario</th>
                      <th>Agregado por</th>
                      <th>Cargo</th>
                      <!-- <th>Estado</th> -->
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white; border-top: 3px #002a8e solid; padding: 20px;">
                    <div class="form-group col-lg-4 col-md-4 col-md-12">
                      <label>Local de origen(*):</label>
                      <select id="origen" name="origen" class="form-control selectpicker" data-live-search="true" data-size="5" onchange="validarLugarDestino(); validarListarArticulos(this.value);" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-md-12">
                      <label>Local de destino(*):</label>
                      <select id="destino" name="destino" class="form-control selectpicker" data-live-search="true" data-size="5" onchange="validarLugarDestino()" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-md-12">
                      <label>N° de documento:</label>
                      <input type="text" class="form-control" name="codigo" id="codigo" maxlength="20" placeholder="Ingrese el N° de documento de la transferencia." required oninput="convertirMayus()">
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-md-12" id="destino_input">
                      <label>Lugar de destino(*):</label>
                      <input type="text" class="form-control" name="lugar_destino" id="lugar_destino" maxlength="100" placeholder="Ingrese el lugar de destino.">
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-md-12">
                      <label>Comentario:</label>
                      <textarea class="form-control" name="comentario" id="comentario" maxlength="10000" cols="30" rows="5" placeholder="Ingrese un comentario." style="resize: none;"></textarea>
                    </div>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white !important; padding: 20px !important;">
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-6 botonArt" id="botonArt">
                      <a data-toggle="modal" href="#myModal">
                        <button id="btnAgregarArt" type="button" class="btn btn-bcp"> <span class="fa fa-plus"></span> Agregar Productos</button>
                      </a>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-6" id="form_codigo_barra">
                      <label>Buscar por código de barra: <a data-toggle="popover" data-placement="top" title="Buscar por código de barra" data-content="Sólo se listan los productos que no están en stock." style="color: #002a8e; cursor: pointer;"><i class="fa fa-question-circle"></i></a></label>
                      <select id="idproducto" name="idproducto" class="form-control selectpicker" data-size="6" data-live-search="true" onchange="llenarTabla()">
                        <option value="">Busca un producto.</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 table-responsive" style="border: 0px solid transparent !important; margin-top: 10px">
                      <table id="detalles" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                        <thead style="background-color:#A9D0F5">
                          <th>Opciones</th>
                          <th>Imagen</th>
                          <th>Artículo</th>
                          <th>Local</th>
                          <th>Categoría</th>
                          <th>Marca</th>
                          <th style="white-space: nowrap;">Código de producto</th>
                          <th style="white-space: nowrap;">Código de barra</th>
                          <th>Stock</th>
                          <th style="white-space: nowrap;">Stock mínimo</th>
                          <th>Cantidad a transferir</th>
                          <th>Precio compra</th>
                          <th style="white-space: nowrap;">Unidad de medida</th>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white !important; padding: 10px !important;">
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
            <h4 class="modal-title">SELECCIONE UN ARTÍCULO</h4>
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
  <script type="text/javascript" src="scripts/transferencias.js"></script>
<?php
}
ob_end_flush();
?>