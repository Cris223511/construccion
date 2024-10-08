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

            td {
                height: 30.84px !important;
            }
        </style>
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Reporte de comparaciones de entradas y salidas</h1>
                                <div class="box-tools pull-right"></div>
                                <div class="panel-body table-responsive listadoregistros" style="overflow: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Fecha Inicial:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label>Fecha Final:</label>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
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
                                            <th style="white-space: nowrap;">Imagen</th>
                                            <th>Nombre producto</th>
                                            <th>Código producto</th>
                                            <th>Cantidad entrada</th>
                                            <th>Cantidad salida</th>
                                            <th>Stock actual</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Imagen</th>
                                            <th>Nombre producto</th>
                                            <th>Código producto</th>
                                            <th>Cantidad entrada</th>
                                            <th>Cantidad salida</th>
                                            <th>Stock actual</th>
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
    <script type="text/javascript" src="scripts/reporteComparacion.js"></script>
<?php
}
ob_end_flush();
?>