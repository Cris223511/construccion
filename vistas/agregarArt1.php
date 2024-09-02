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
      .caja1 .contenedor {
        text-align: center;
      }

      @media (max-width: 991px) {
        .caja1 {
          padding-left: 0 !important;
          padding-right: 0 !important;
        }

        .listadoregistros,
        .listadoregistros2 {
          padding-bottom: 15px !important;
        }

        .contenedor_articulos {
          display: flex;
          flex-direction: column-reverse !important;
        }

        .caja1 .contenedor {
          display: flex;
          flex-direction: column;
          justify-content: center;
          text-align: center;
          gap: 5px;
          margin-bottom: 0;
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

        .listadoregistros {
          padding-bottom: 0 !important;
        }
      }

      tbody td {
        white-space: nowrap !important;
      }

      #camera video {
        width: 250px;
        height: auto;
      }

      #camera canvas.drawingBuffer {
        width: 250px;
        height: auto;
        position: absolute;
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Agregar productos
                  <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <a href="../reportes/rptarticulos.php" target="_blank">
                    <button class="btn btn-secondary" style="color: black !important;">
                      <i class="fa fa-clipboard"></i> Reporte
                    </button>
                  </a>
                  <a href="entradas.php">
                    <button class="btn btn-danger" id="btnInsertarArt">
                      <i class="fa fa-arrow-circle-left"></i> Volver
                    </button>
                  </a>
                </h1>
                <div class="box-tools pull-right"></div>
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
                    <button style="width: 80%;" class="btn btn-bcp" onclick="buscar(0)">Buscar por fecha</button>
                    <button style="width: 20%; height: 32px" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
                  </div>
                </div>
              </div>
              <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="table-responsive listadoregistros2" style="overflow-x: visible; display: inline-block; width: 100%; padding-top: 20px !important; background-color: white; height: max-content;">
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
                      <button style="width: 80%;" class="btn btn-bcp" onclick="buscar(1)">Buscar</button>
                      <button style="width: 20%; height: 32px" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-top: 0 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="table-responsive" style="padding: 20px !important; background-color: white;">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                    <thead>
                      <th>Opciones</th>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th style="white-space: nowrap;">U. medida</th>
                      <th style="width: 20%; min-width: 300px;">Descripción</th>
                      <th>Categoría</th>
                      <th>Marca</th>
                      <th style="white-space: nowrap;">Ubicación del local</th>
                      <th style="white-space: nowrap;">Stock normal</th>
                      <th style="white-space: nowrap;">Stock mínimo</th>
                      <th style="white-space: nowrap;">P. Compra</th>
                      <th style="white-space: nowrap;">P. Compra Mayor</th>
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
                      <th>P. Compra Mayor</th>
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
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                  <div class="contenedor_articulos">
                    <div class="form-group col-lg-10 col-md-8 col-sm-12 caja2" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 20px;">
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Nombre(*):</label>
                        <input type="hidden" name="idarticulo" id="idarticulo">
                        <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre del producto." required>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Unidad de medida(*):</label>
                        <select id="idmedida" name="idmedida" class="form-control selectpicker" data-live-search="true" data-size="5" required>
                          <option value="">- Seleccione -</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Categoría:</label>
                        <select id="idcategoria" name="idcategoria" class="form-control selectpicker" data-live-search="true" data-size="5">
                          <option value="">- Seleccione -</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Marca:</label>
                        <select id="idmarca" name="idmarca" class="form-control selectpicker" data-live-search="true" data-size="5">
                          <option value="">- Seleccione -</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Local(*):</label>
                        <select id="idlocal" name="idlocal" class="form-control selectpicker idlocal" data-live-search="true" data-size="5" onchange="actualizarRUC()" required>
                          <option value="">- Seleccione -</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>RUC local(*):</label>
                        <input type="number" class="form-control" id="local_ruc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="RUC del local" disabled>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Precio compra:</label>
                        <input type="number" class="form-control" name="precio_compra" id="precio_compra" step="any" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0" placeholder="Ingrese el precio de compra.">
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Precio compra al mayor:</label>
                        <input type="number" class="form-control" name="precio_compra_mayor" id="precio_compra_mayor" step="any" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0" placeholder="Ingrese el precio de compra al mayor.">
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Stock(*):</label>
                        <input type="number" class="form-control" name="stock" id="stock" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0.1" placeholder="Ingrese el stock." required>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Stock mínimo(*):</label>
                        <input type="number" class="form-control" name="stock_minimo" id="stock_minimo" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" step="any" min="0.1" placeholder="Ingrese el stock mínimo." required>
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Imagen:</label>
                        <input type="file" class="form-control" name="imagen" id="imagen" accept="image/x-png,image/gif,image/jpeg">
                        <input type="hidden" name="imagenactual" id="imagenactual">
                      </div>
                      <div class="form-group col-lg-6 col-md-12">
                        <label>Código(*):</label>
                        <input type="text" class="form-control" name="codigo_producto" id="codigo_producto" maxlength="10" placeholder="Ingrese el código del producto." oninput="convertirMayus(this)" required>
                      </div>
                      <div class="form-group col-lg-12 col-md-12">
                        <label>Descripción:</label>
                        <textarea type="text" class="form-control" name="descripcion" id="descripcion" maxlength="10000" rows="4" placeholder="Ingrese la descripción del producto."></textarea>
                      </div>
                      <div class="form-group col-lg-12 col-md-12" style="display: flex; justify-content: center;">
                        <button class="btn btn-success" type="button" id="btnDetalles1" onclick="frmDetalles(true)"><i class="fa fa-plus"></i> Más detalles</button>
                        <button class="btn btn-danger" type="button" id="btnDetalles2" onclick="frmDetalles(false)"><i class="fa fa-minus"></i> Cerrar</button>
                      </div>
                      <!-- form detalles -->
                      <div id="frmDetalles" class="col-lg-12 col-md-12" style="margin: 0 !important; padding: 0 !important;">
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Talla:</label>
                          <textarea type="text" class="form-control" name="talla" id="talla" maxlength="10000" rows="4" placeholder="Ingrese la talla del producto."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Color:</label>
                          <textarea type="text" class="form-control" name="color" id="color" maxlength="10000" rows="4" placeholder="Ingrese el color del producto."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Peso:</label>
                          <input type="number" class="form-control" name="peso" id="peso" step="any" onkeydown="evitarNegativo(event)" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="6" min="0" placeholder="Ingrese el peso.">
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Casillero:</label>
                          <input type="text" class="form-control" name="casillero" id="casillero" maxlength="100" placeholder="Ingrese la ubicación del casillero." oninput="convertirMayus(this)">
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Fecha Emisión:</label>
                          <input type="date" class="form-control" name="fecha_emision" id="fecha_emision">
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Fecha Vencimiento:</label>
                          <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento">
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Nota 1:</label>
                          <textarea type="text" class="form-control" name="nota_1" id="nota_1" maxlength="10000" rows="4" placeholder="Ingrese la nota 1."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Nota 2:</label>
                          <textarea type="text" class="form-control" name="nota_2" id="nota_2" maxlength="10000" rows="4" placeholder="Ingrese la nota 2."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Nota 3 (IMEI):</label>
                          <textarea type="text" class="form-control" name="imei" id="imei" maxlength="10000" rows="4" placeholder="Ingrese el IMEI."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12">
                          <label>Nota 4 (Serial):</label>
                          <textarea type="text" class="form-control" name="serial" id="serial" maxlength="10000" rows="4" placeholder="Ingrese el serial."></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-12 col-sm-12">
                          <div>
                            <label>Código de barra:</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" maxlength="13" placeholder="Ingrese el código de barra.">
                          </div>
                          <div style="margin-top: 10px; display: flex; gap: 5px; flex-wrap: wrap;">
                            <button class="btn btn-info" type="button" onclick="generar()">Generar</button>
                            <button class="btn btn-warning" type="button" onclick="imprimir()">Imprimir</button>
                            <button class="btn btn-danger" type="button" onclick="borrar()">Borrar</button>
                            <button class="btn btn-success btn1" type="button" onclick="escanear()">Escanear</button>
                            <button class="btn btn-danger btn2" type="button" onclick="detenerEscaneo()">Detener</button>
                          </div>
                          <div id="print" style="overflow-y: hidden;">
                            <img id="barcode">
                          </div>
                        </div>
                        <div class="form-group col-lg-6 col-md-12 col-sm-12">
                          <div style="display: flex; justify-content: start;">
                            <div id="camera"></div>
                          </div>
                        </div>
                      </div>
                      <!-- end form detalles -->
                    </div>
                    <div class="form-group col-lg-2 col-md-4 col-sm-12 caja1" style="padding-right: 0 !important; padding-left: 20px;">
                      <div class="contenedor" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 10px 20px 20px 20px;">
                        <label>Imagen de muestra:</label>
                        <div>
                          <img src="" width="100%" id="imagenmuestra" style="display: none;">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-lg-10 col-md-8 col-sm-12 botones" style="background-color: white !important; padding: 10px !important; float: left;">
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

    <!-- Form categoría -->
    <form name="formularioCategoria" id="formularioCategoria" method="POST" style="display: none;">
      <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Nombre(*):</label>
        <input type="hidden" name="idcategoria" id="idcategoria2">
        <input type="text" class="form-control" name="titulo" id="titulo2" maxlength="50" placeholder="Nombre" required>
      </div>
      <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Descripción:</label>
        <input type="text" class="form-control" name="descripcion" id="descripcion2" maxlength="10000" placeholder="Descripción">
      </div>
    </form>
    <!-- Fin form categoría -->

    <!-- Form marcas -->
    <form name="formularioMarcas" id="formularioMarcas" method="POST" style="display: none;">
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Marca:</label>
        <input type="hidden" name="idmarca" id="idmarca3">
        <input type="text" class="form-control" name="titulo" id="titulo3" maxlength="50" placeholder="Nombre de la marca" required>
      </div>
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Descripción:</label>
        <textarea type="text" class="form-control" name="descripcion" id="descripcion3" maxlength="10000" rows="4" placeholder="Descripción"></textarea>
      </div>
    </form>
    <!-- Fin form marcas -->

    <!-- Form medidas -->
    <form name="formularioMedidas" id="formularioMedidas" method="POST" style="display: none;">
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Medida(*):</label>
        <input type="hidden" name="idmedida" id="idmedida4">
        <input type="text" class="form-control" name="titulo" id="titulo4" maxlength="50" placeholder="Nombre de la medida" required>
      </div>
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Descripción:</label>
        <textarea type="text" class="form-control" name="descripcion" id="descripcion4" maxlength="10000" rows="4" placeholder="Descripción"></textarea>
      </div>
    </form>
    <!-- Fin form medidas -->
  <?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
  ?>
  <script type="text/javascript" src="../public/js/JsBarcode.all.min.js"></script>
  <script type="text/javascript" src="../public/js/jquery.PrintArea.js"></script>
  <script type="text/javascript" src="scripts/agregarArt1.js"></script>
<?php
}
ob_end_flush();
?>