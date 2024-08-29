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

      #label2 {
        display: block;
      }

      @media (max-width: 991px) {
        .botonArt {
          display: flex;
          justify-content: center;
        }
      }

      @media (max-width: 767px) {
        #label2 {
          display: none;
        }
      }

      tbody td,
      tfoot tr th {
        white-space: nowrap !important;
      }
    </style>

    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Salidas
                  <?php if ($_SESSION["cargo"] != "mirador") { ?>
                    <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)">
                      <i class="fa fa-plus-circle"></i> Nueva salida
                    </button>
                  <?php } ?>
                  <a href="articulo.php">
                    <button class="btn btn-warning" id="btnInsertarArt">
                      <i class="fa fa-sign-in"></i> Ver productos
                    </button>
                  </a>
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
                      <th style="white-space: nowrap;">Fecha y hora</th>
                      <th>Ubicación del local</th>
                      <th style="white-space: nowrap;">N° de documento</th>
                      <th>Total compra</th>
                      <th>Tipo</th>
                      <th>Tipo Movimiento</th>
                      <th style="white-space: nowrap;">Autorizado por</th>
                      <th style="white-space: nowrap;">Recibido por</th>
                      <th style="white-space: nowrap;">Entregado por</th>
                      <th>Cargo</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Fecha y hora</th>
                      <th>Ubicación del local</th>
                      <th>N° de documento</th>
                      <th>Total compra</th>
                      <th>Tipo</th>
                      <th>Tipo Movimiento</th>
                      <th>Autorizado por</th>
                      <th>Recibido por</th>
                      <th>Entregado por</th>
                      <th>Cargo</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white; border-top: 3px #002a8e solid; padding: 20px;">
                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                      <label>Ubicación del producto:</label>
                      <input type="text" class="form-control" name="ubicacion" id="ubicacion" maxlength="50" placeholder="Ingrese la ubicación del producto." autocomplete="off">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                      <label>Fecha y hora(*):</label>
                      <input type="datetime-local" class="form-control" id="fecha_hora" readonly>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>Local(*):</label>
                      <select id="idlocal" name="idlocal" class="form-control selectpicker idlocal" data-live-search="true" data-size="5" onchange="actualizarRUC(); actualizarPersonales(this.value);" required>
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-md-12">
                      <label>RUC local(*):</label>
                      <input type="number" class="form-control" id="local_ruc" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="RUC del local" disabled>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                      <label>Tipo:</label>
                      <select id="idtipo" name="idtipo" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                      <label>N° de documento(*):</label>
                      <input type="text" class="form-control codigo" id="cod_1" maxlength="5" placeholder="Inicial del N° de documento de la salida." required oninput="onlyLetters()" onblur="convertirMayus()">
                    </div>
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                      <label id="label2">ㅤㅤ</label>
                      <input type="text" class="form-control codigo" id="cod_2" maxlength="5" placeholder="N° de documento de la salida." oninput="onlyNumbersAndMaxLenght(this)" onblur="formatearNumero(this)" required />

                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                      <label>Tipo de movimiento:</label>
                      <select id="tipo_movimiento" name="tipo_movimiento" class="form-control selectpicker" onchange="evaluarMetodo()">
                        <option value="">- Seleccione -</option>
                        <option value="personal">Personal</option>
                        <option value="maquinaria">Maquinaria</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-12" id="selectMaquinaria">
                      <label>Maquinaria:</label>
                      <select id="idmaquinaria" name="idmaquinaria" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-12 selectPersonal">
                      <label>Autorizado por:</label>
                      <select id="idautorizado" name="idautorizado" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="0">- Seleccione -</option>
                      </select>
                    </div>
                    <!-- <div class="form-group col-lg-6 col-md-6 col-sm-12 selectPersonal">
                      <label>Entregado por:</label>
                      <select id="identregado" name="identregado" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="0">- Seleccione -</option>
                      </select>
                    </div> -->
                    <div class="form-group col-lg-6 col-md-6 col-sm-12 selectPersonal">
                      <label>Recibido por:</label>
                      <select id="idrecibido" name="idrecibido" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="0">- Seleccione -</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-md-12">
                      <label>Descripción:</label>
                      <textarea class="form-control" name="descripcion" id="descripcion" maxlength="10000" cols="30" rows="5" placeholder="Ingrese una descripción." style="resize: none;"></textarea>
                    </div>
                  </div>
                  <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white !important; padding: 20px !important;">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12 botonArt" id="botonArt">
                      <a data-toggle="modal" href="#myModal">
                        <button id="btnAgregarArt" type="button" class="btn btn-secondary" style="color: black !important"> <span class="fa fa-plus"></span> Agregar Productos</button>
                      </a>
                    </div>
                    <div class="form-group col-lg-3 col-md-6 col-sm-6">
                      <label>Impuesto:</label>
                      <select name="impuesto" id="impuesto" class="form-control selectpicker" onchange="modificarSubototales();" required>
                        <option value="0">0</option>
                        <option value="18">18</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-3 col-md-6 col-sm-6" id="form_codigo_barra">
                      <label>Buscar por código de barra: <a data-toggle="popover" data-placement="top" title="Buscar por código de barra" data-content="Sólo se listan los productos que no están en stock." style="color: #418bb7; cursor: pointer;"><i class="fa fa-question-circle"></i></a></label>
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
                          <th>Categoría</th>
                          <th>Marca</th>
                          <th style="white-space: nowrap;">Código de producto</th>
                          <th style="white-space: nowrap;">Código de barra</th>
                          <th>Cantidad</th>
                          <th>Precio compra</th>
                          <th style="white-space: nowrap;">Unidad de medida</th>
                          <th>Stock</th>
                          <th style="white-space: nowrap;">Stock mínimo</th>
                          <th>Subtotal</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>IGV</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                              <h4 id="igv">S/. 0.00</h4><input type="hidden" name="total_igv" id="total_igv">
                            </th>
                          </tr>
                          <tr>
                            <th>TOTAL</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                              <h4 id="total">S/. 0.00</h4><input type="hidden" name="total_compra" id="total_compra">
                            </th>
                          </tr>
                        </tfoot>
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
            <h4 class="modal-title">Seleccione un artículo</h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
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
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal -->

    <!-- Modal 3 -->
    <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: visible;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">NO SE ENCONTRÓ AL PERSONAL, ¿DESEA AGREGAR UNO NUEVO?:</h4>
          </div>
          <div class="panel-body">
            <form name="formSunat" id="formSunat" method="POST">
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 20px;">
                <div style="display: flex;">
                  <input type="number" class="form-control" name="sunat" id="sunat" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="Buscar personal por DNI o RUC a la SUNAT." required>
                  <button class="btn btn-bcp" type="submit" id="btnSunat">Buscar</button>
                </div>
              </div>
            </form>
            <form name="formulario2" id="formulario2" method="POST">
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Tipo Documento(*):</label>
                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" onchange="changeValue(this);" required disabled>
                  <option value="">- Seleccione -</option>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CEDULA">CEDULA</option>
                </select>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Número(*):</label>
                <input type="number" class="form-control" name="num_documento" id="num_documento" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" placeholder="Ingrese el N° de documento." required disabled>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Nombre(*):</label>
                <input type="hidden" name="idpersonal" id="idpersonal2">
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="40" placeholder="Ingrese el nombre del personal." autocomplete="off" required disabled>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingrese la dirección." maxlength="40" disabled>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Teléfono:</label>
                <input type="number" class="form-control" name="telefono" id="telefono" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="9" placeholder="Ingrese el teléfono." disabled>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Ingrese el correo electrónico." disabled>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Local(*):</label>
                <select id="idlocal2" name="idlocal" class="form-control selectpicker idlocal" data-live-search="true" data-size="5" onchange="actualizarRUC2()" required disabled>
                  <option value="">- Seleccione -</option>
                </select>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>RUC local(*):</label>
                <input type="number" class="form-control" id="local_ruc2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="RUC del local." disabled>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Descripción:</label>
                <input type="text" class="form-control" name="descripcion" id="descripcion2" maxlength="10000" placeholder="Ingrese la descripción del personal." autocomplete="off" disabled>
              </div>

              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 0 !important; padding: 0 !important;">
                <button class="btn btn-warning" type="button" data-dismiss="modal" onclick="limpiarModalClientes();"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardarCliente" disabled><i class="fa fa-save"></i> Guardar</button>
                <button class="btn btn-success" type="button" data-dismiss="modal" onclick="agregarClienteManual();"><i class="fa fa-sign-in"></i> Agregar Personal Manual</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 3 -->

    <!-- Modal 4 -->
    <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="width: 85% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: visible;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title infotitulo">CREAR NUEVO PERSONAL:</h4>
          </div>
          <div class="panel-body">
            <form name="formulario3" id="formulario3" method="POST">
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Tipo Documento(*):</label>
                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento2" onchange="changeValue(this);" required>
                  <option value="">- Seleccione -</option>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CEDULA">CEDULA</option>
                </select>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Número(*):</label>
                <input type="number" class="form-control" name="num_documento" id="num_documento2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" placeholder="Ingrese el N° de documento." required>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Nombre(*):</label>
                <input type="hidden" name="idpersonal" id="idpersonal3">
                <input type="text" class="form-control" name="nombre" id="nombre2" maxlength="40" placeholder="Ingrese el nombre del personal." autocomplete="off" required>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion2" placeholder="Ingrese la dirección." maxlength="40">
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Teléfono:</label>
                <input type="number" class="form-control" name="telefono" id="telefono2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="9" placeholder="Ingrese el teléfono.">
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" id="email2" maxlength="50" placeholder="Ingrese el correo electrónico.">
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Local(*):</label>
                <select id="idlocal3" name="idlocal" class="form-control selectpicker idlocal" data-live-search="true" data-size="5" onchange="actualizarRUC3()" required>
                  <option value="">- Seleccione -</option>
                </select>
              </div>
              <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>RUC local(*):</label>
                <input type="number" class="form-control" id="local_ruc3" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="11" placeholder="RUC del local." disabled>
              </div>
              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Descripción:</label>
                <input type="text" class="form-control" name="descripcion" id="descripcion3" maxlength="10000" placeholder="Ingrese la descripción del personal." autocomplete="off">
              </div>

              <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 0 !important; padding: 0 !important;">
                <button class="btn btn-warning" type="button" data-dismiss="modal" onclick="limpiarModalClientes2();"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                <button class="btn btn-bcp" type="submit" id="btnGuardarCliente2"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin modal 4 -->

    <!-- Form maquinarias -->
    <form name="formularioMaquinaria" id="formularioMaquinaria" method="POST" style="display: none;">
      <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Nombre(*):</label>
        <input type="hidden" name="idmaquinaria" id="idmaquinaria2">
        <input type="text" class="form-control" name="titulo" id="titulo2" maxlength="50" placeholder="Nombre de la maquinaria." required>
      </div>
      <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label>Descripción:</label>
        <input type="text" class="form-control" name="descripcion" id="descripcion2" maxlength="10000" placeholder="Descripción">
      </div>
    </form>
    <!-- Fin form maquinarias -->

    <!-- Form tipos -->
    <form name="formularioTipo" id="formularioTipo" method="POST" style="display: none;">
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Marca(*):</label>
        <input type="hidden" name="idtipo" id="idtipo3">
        <input type="text" class="form-control" name="titulo" id="titulo3" maxlength="50" placeholder="Nombre del tipo." required>
      </div>
      <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label>Descripción:</label>
        <textarea type="text" class="form-control" name="descripcion" id="descripcion3" maxlength="10000" rows="4" placeholder="Descripción"></textarea>
      </div>
    </form>
    <!-- Fin form tipos -->
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>
  <script type="text/javascript" src="scripts/salidas25.js"></script>
<?php
}
ob_end_flush();
?>