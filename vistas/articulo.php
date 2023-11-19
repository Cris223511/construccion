<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';
  if ($_SESSION['almacen'] == 1) {
?>
    <style>
      @media (max-width: 991px) {
        .caja1 {
          padding-right: 0 !important;
        }

        .listadoregistros {
          padding-bottom: 15px !important;
        }

        .caja1 .contenedor {
          display: flex;
          flex-direction: column;
          justify-content: center;
          text-align: center;
          gap: 15px;
        }

        .caja1 .contenedor img {
          width: 25% !important;
        }

        #label {
          display: none;
        }
      }

      @media (max-width: 767px) {
        .botones {
          width: 100% !important;
        }
      }

      tbody td:nth-child(12) {
        white-space: nowrap !important;
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Productos
                  <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <a href="../reportes/rptarticulos.php" target="_blank"><button class="btn btn-secondary" style="color: black !important;"><i class="fa fa-clipboard"></i> Reporte</button></a>
                </h1>
                <div class="box-tools pull-right"></div>
              </div>
              <div class="panel-body table-responsive listadoregistros" style="overflow-x: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Buscar por marca:</label>
                  <select id="idmarcaBuscar" name="idmarcaBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                    <option value="">- Seleccione -</option>
                  </select>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Buscar por categoría:</label>
                  <select id="idcategoriaBuscar" name="idcategoriaBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                    <option value="">- Seleccione -</option>
                  </select>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Buscar por estado:</label>
                  <select id="estadoBuscar" name="estadoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                    <option value="">- Seleccione -</option>
                    <option value="1">Disponible</option>
                    <option value="2">Agotándose</option>
                    <option value="3">Agotado</option>
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
                      <th style="white-space: nowrap;">Agregado por</th>
                      <th>Cargo</th>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th style="width: 20%; min-width: 220px; white-space: nowrap;">Ubicación del local</th>
                      <th>Marca</th>
                      <th style="white-space: nowrap;">C. producto</th>
                      <th style="white-space: nowrap;">C. de barra</th>
                      <th style="white-space: nowrap;">Stock normal</th>
                      <th style="white-space: nowrap;">Stock mínimo</th>
                      <th style="white-space: nowrap;">P. compra</th>
                      <th style="white-space: nowrap;">P. venta</th>
                      <th>Imagen</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Agregado por</th>
                      <th>Cargo</th>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th>Ubicación del local</th>
                      <th>Marca</th>
                      <th>C. producto</th>
                      <th>C. de barra</th>
                      <th>Stock normal</th>
                      <th>Stock mínimo</th>
                      <th>P. compra</th>
                      <th>P. venta</th>
                      <th>Imagen</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-2 col-md-4 col-sm-12 caja1" style="padding-left: 0 !important; padding-right: 20px;">
                    <div class="contenedor" style="background-color: white; border-top: 3px #3686b4 solid; padding: 10px 20px 20px 20px;">
                      <label>Imagen de muestra:</label>
                      <div>
                        <img src="" width="100%" id="imagenmuestra">
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-lg-10 col-md-8 col-sm-12 caja2" style="background-color: white; border-top: 3px #3686b4 solid; padding: 20px;">
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Nombre(*):</label>
                      <input type="hidden" name="idarticulo" id="idarticulo">
                      <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre del producto." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Categoría(*):</label>
                      <select id="idcategoria" name="idcategoria" class="form-control selectpicker" data-live-search="true" required></select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Marca(*):</label>
                      <select id="idmarca" name="idmarca" class="form-control selectpicker" data-live-search="true" required></select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Servicio(*):</label>
                      <select id="idservicio" name="idservicio" class="form-control selectpicker" data-live-search="true" required></select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Local(*):</label>
                      <select id="idlocal" name="idlocal" class="form-control selectpicker idlocal" data-live-search="true" data-size="5" onchange="actualizarRUC()">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>RUC local(*):</label>
                      <input type="number" class="form-control" id="local_ruc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="RUC del local" disabled>
                    </div>
                    <div class="form-group col-lg-12 col-md-12">
                      <label>Descripción:</label>
                      <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="50" placeholder="Ingrese la descripción del producto." autocomplete="off">
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Precio compra(*):</label>
                      <input type="number" class="form-control" name="precio_compra" id="precio_compra" step="any" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" min="0" placeholder="Ingrese el precio de compra." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Precio venta(*):</label>
                      <input type="number" class="form-control" name="precio_venta" id="precio_venta" step="any" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" min="0" placeholder="Ingrese el precio de venta." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Stock(*):</label>
                      <input type="number" class="form-control" name="stock" id="stock" onkeydown="evitarNegativo(event)" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" placeholder="Ingrese el stock." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Stock mínimo(*):</label>
                      <input type="number" class="form-control" name="stock_minimo" id="stock_minimo" onkeydown="evitarNegativo(event)" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="3" min="0" placeholder="Ingrese el stock mínimo." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <label>Código del producto(*):</label>
                      <input type="text" class="form-control" name="codigo_producto" id="codigo_producto" maxlength="10" placeholder="Ingrese el código del producto." required>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                      <div>
                        <label>Código de barra(*):</label>
                        <input type="text" class="form-control" name="codigo" id="codigo" maxlength="13" placeholder="Ingrese el código de barra.">
                      </div>
                      <div style="margin-top: 10px;">
                        <button class="btn btn-bcp" type="button" onclick="generarbarcode(1)">Visualizar</button>
                        <button class="btn btn-info" type="button" onclick="generar()">Generar</button>
                        <button class="btn btn-warning" type="button" onclick="imprimir()">Imprimir</button>
                      </div>
                      <div id="print" style="overflow-y: hidden;">
                        <img id="barcode">
                        <div id="barcode-number"></div>
                      </div>
                    </div>
                    <div class="form-group col-lg-12 col-md-12">
                      <label>Imagen:</label>
                      <input type="file" class="form-control" name="imagen" id="imagen" accept="image/x-png,image/gif,image/jpeg">
                      <input type="hidden" name="imagenactual" id="imagenactual">
                    </div>
                    <div class="form-group col-lg-12 col-md-12" style="display: flex; justify-content: center;">
                      <button class="btn btn-success" type="button" id="btnDetalles1" onclick="frmDetalles(true)"><i class="fa fa-plus"></i> Más detalles</button>
                      <button class="btn btn-danger" type="button" id="btnDetalles2" onclick="frmDetalles(false)"><i class="fa fa-minus"></i> Cerrar</button>
                    </div>
                    <!-- form detalles -->
                    <div id="frmDetalles" class="col-lg-12 col-md-12" style="margin: 0 !important; padding: 0 !important;">
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Talla:</label>
                        <input type="text" class="form-control" name="talla" id="talla" maxlength="5" placeholder="Ingrese la talla del producto." autocomplete="off">
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Color:</label>
                        <input type="text" class="form-control" name="color" id="color" maxlength="30" placeholder="Ingrese el color del producto." autocomplete="off">
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Unidad de medida:</label>
                        <select id="idmedida" name="idmedida" class="form-control selectpicker" data-live-search="true"></select>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Peso:</label>
                        <input type="number" class="form-control" name="peso" id="peso" step="any" onkeydown="evitarNegativo(event)" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" min="0" placeholder="Ingrese el peso.">
                      </div>
                    </div>
                    <!-- end form detalles -->
                  </div>
                  <div class="form-group col-lg-10 col-md-8 col-sm-12 botones" style="background-color: white !important; padding: 10px !important; float: right;">
                    <div style="float: left;">
                      <button class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                      <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                  </div>
                </form>
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
  <script type="text/javascript" src="../public/js/JsBarcode.all.min.js"></script>
  <script type="text/javascript" src="../public/js/jquery.PrintArea.js"></script>
  <script type="text/javascript" src="scripts/articulo19.js"></script>
<?php
}
ob_end_flush();
?>