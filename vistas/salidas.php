<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['salidas'] == 1) {
?>
    <style>
      .botonArt {
        margin-top: 15px;
      }

      @media (max-width: 991px) {
        .botonArt {
          display: flex;
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
                <h1 class="box-title">Salidas
                  <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)">
                    <i class="fa fa-plus-circle"></i> Agregar
                  </button>
                  <a href="../reportes/rptsalidas.php" target="_blank">
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
                      <th style="white-space: nowrap;">Agregado por</th>
                      <th>Cargo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Tipo</th>
                      <th style="white-space: nowrap;">Código</th>
                      <th style="white-space: nowrap;">Fecha y hora</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Agregado por</th>
                      <th>Cargo</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th>Tipo</th>
                      <th>Código</th>
                      <th>Fecha y hora</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white; border-top: 3px #3686b4 solid; padding: 20px;">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                      <label>Categoría(*):</label>
                      <select id="idcategoria" name="idcategoria" class="form-control selectpicker" data-live-search="true" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Marca(*):</label>
                      <select id="idmarca" name="idmarca" class="form-control selectpicker" data-live-search="true" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Tipo(*):</label>
                      <select id="idtipo" name="idtipo" class="form-control selectpicker" data-live-search="true" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Medida / peso(*):</label>
                      <input type="number" class="form-control" name="peso" id="peso" step="any" onkeydown="evitarNegativo(event)" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" min="0" placeholder="Ingrese la medida o peso.">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Unidad de medida / peso(*):</label>
                      <select id="idmedida" name="idmedida" class="form-control selectpicker" data-live-search="true">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Ubicación(*):</label>
                      <input type="text" class="form-control" name="ubicacion" id="ubicacion" maxlength="50" placeholder="Ingrese la ubicación." autocomplete="off">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Código(*):</label>
                      <input type="text" class="form-control" name="codigo" id="codigo" maxlength="10" placeholder="Ingrese el código de la salida." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Tipo de movimiento(*):</label>
                      <select id="tipo_movimiento" class="form-control selectpicker" onchange="evaluarMetodo()" required>
                        <option value="">- Seleccione -</option>
                        <option value="personal">Personal</option>
                        <option value="maquinaria">Maquinaria</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12" id="selectPersonal">
                      <label>Personal(*):</label>
                      <select id="idpersonal" name="idpersonal" class="form-control selectpicker" data-live-search="true" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12" id="selectMaquinaria">
                      <label>Maquinaria(*):</label>
                      <select id="idmaquinaria" name="idmaquinaria" class="form-control selectpicker" data-live-search="true" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-md-12">
                      <label>Descripción:</label>
                      <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="50" placeholder="Ingrese una descripción." autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white !important; padding: 20px !important;">
                    <div class="form-group col-lg-6 col-md-6 col-md-12 botonArt" id="botonArt">
                      <a data-toggle="modal" href="#myModal">
                        <button id="btnAgregarArt" type="button" class="btn btn-secondary" style="color: black !important"> <span class="fa fa-plus"></span> Agregar Artículos</button>
                      </a>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12" id="form_codigo_barra">
                      <label>Buscar por código de barra: <a href="#" data-toggle="popover" data-placement="top" title="Buscar por código de barra" data-content="Sólo se listan los productos que no están en stock." style="color: #418bb7"><i class="fa fa-question-circle"></i></a></label>
                      <select id="idproducto" name="idproducto" class="form-control selectpicker" data-size="6" data-live-search="true" onchange="llenarTabla()">
                        <option value="">Busca un producto.</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12 table-responsive" style="border: 0px solid transparent !important; margin-top: 10px">
                      <table id="detalles" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                        <thead style="background-color:#A9D0F5">
                          <th>Opciones</th>
                          <th>Artículo</th>
                          <th>Cantidad</th>
                          <th>Código de barra</th>
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
      <div class="modal-dialog" style="width: 85% !important;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Seleccione un artículo</h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
              <thead>
                <th>OPCIONES</th>
                <th style="white-space: nowrap;">AGREGADO POR</th>
                <th>CARGO</th>
                <th>NOMBRE</th>
                <th>CATEGORÍA</th>
                <th style="width: 20%; min-width: 220px; white-space: nowrap;">UBICACIÓN DEL LOCAL</th>
                <th>MARCA</th>
                <th style="white-space: nowrap;">C. PRODUCTO</th>
                <th style="white-space: nowrap;">C. DE BARRA</th>
                <th style="white-space: nowrap;">STOCK NORMAL</th>
                <th style="white-space: nowrap;">STOCK MÍNIMO</th>
                <th>IMAGEN</th>
                <th style="white-space: nowrap;">FECHA Y HORA</th>
                <th>ESTADO</th>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <TH>OPCIONES</TH>
                <TH>AGREGADO POR</TH>
                <TH>CARGO</TH>
                <TH>NOMBRE</TH>
                <TH>CATEGORÍA</TH>
                <TH>UBICACIÓN DEL LOCAL</TH>
                <TH>MARCA</TH>
                <TH>C. PRODUCTO</TH>
                <TH>C. DE BARRA</TH>
                <TH>STOCK NORMAL</TH>
                <TH>STOCK MÍNIMO</TH>
                <TH>IMAGEN</TH>
                <TH>FECHA Y HORA</TH>
                <TH>ESTADO</TH>
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
  <script type="text/javascript" src="scripts/salidas6.js"></script>
<?php
}
ob_end_flush();
?>