<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['reportes'] == 1) {
?>
        <style>
            @media (max-width: 991px) {
                .caja1 {
                    padding-right: 0 !important;
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

                #labelCustom {
                    display: none;
                }

                .listadoregistros {
                    margin-bottom: 0;
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
                                <h1 class="box-title">Reporte de salidas generales</h1>
                                <div class="box-tools pull-right"></div>
                                <div class="panel-body table-responsive listadoregistros" style="overflow: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Fecha Inicial:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Fecha Final:</label>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>N° de documento:</label>
                                        <input type="number" class="form-control" name="documentoBuscar" id="documentoBuscar" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="20" placeholder="Ingrese el N° de documento." required>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Local:</label>
                                        <select id="localBuscar" name="localBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Usuario:</label>
                                        <select id="usuarioBuscar" name="usuarioBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Estado:</label>
                                        <select id="estadoBuscar" name="estadoBuscar" class="form-control selectpicker" data-size="5">
                                            <option value="">- Seleccione -</option>
                                            <option value="activado">ACTIVADO</option>
                                            <option value="desactivado">DESACTIVADO</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Tipo:</label>
                                        <select id="tiposBuscar" name="tiposBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Tipo de movimiento:</label>
                                        <select id="tipoMovimientoBuscar" name="tipoMovimientoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                            <option value="">- Seleccione -</option>
                                            <option value="personal">PERSONAL</option>
                                            <option value="maquinaria">MAQUINARIA</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Maquinaria:</label>
                                        <select id="maquinariaBuscar" name="maquinariaBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Personal autorizado:</label>
                                        <select id="personalAutorizadoBuscar" name="personalAutorizadoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Personal recibido:</label>
                                        <select id="personalRecibidoBuscar" name="personalRecibidoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label id="labelCustom">ㅤ</label>
                                        <div style="display: flex; gap: 10px;">
                                            <button style="width: 100%;" class="btn btn-bcp" onclick="buscar()">Buscar</button>
                                            <button style="height: 32px;" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
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
                                            <th style="white-space: nowrap;">Maquinaria</th>
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
                                            <th>Maquinaria</th>
                                            <th>Autorizado por</th>
                                            <th>Recibido por</th>
                                            <th>Entregado por</th>
                                            <th>Cargo</th>
                                            <th>Estado</th>
                                        </tfoot>
                                    </table>
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
    <script type="text/javascript" src="scripts/reporteSalida.js"></script>
<?php
}
ob_end_flush();
?>