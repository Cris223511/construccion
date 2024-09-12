<?php
ob_start();
if (strlen(session_id()) < 1) {
    session_start();
}

if (!isset($_SESSION["nombre"])) {
    header("Location: ../vistas/login.html");
} else {
    if ($_SESSION['reportes'] == 1) {
        require_once "../modelos/Reporte.php";

        $reporte = new Reporte();

        $idlocalSession = $_SESSION["idlocal"];
        $cargo = $_SESSION["cargo"];

        switch ($_GET["op"]) {

                /* ======================= REPORTE DE ENTRADAS ======================= */

            case 'listarEntradas':
                $parametros = array();

                if ($cargo != "superadmin") {
                    $parametros[] = "e.idlocal = '$idlocalSession'";
                }

                $filtros = array(
                    "param1" => "DATE(e.fecha_hora) BETWEEN '{$_GET["param1"]}' AND '{$_GET["param2"]}'",
                    "param3" => "e.codigo = '{$_GET["param3"]}'",
                    "param4" => "e.idlocal = '{$_GET["param4"]}'",
                    "param5" => "u.idusuario = '{$_GET["param5"]}'",
                    "param6" => "e.estado = '{$_GET["param6"]}'",
                    "param7" => "p.nombre LIKE '%{$_GET["param7"]}%'",
                );

                foreach ($filtros as $param => $condicion) {
                    if (!empty($_GET[$param])) {
                        $parametros[] = $condicion;
                    }
                }

                if (count($parametros) > 0) {
                    $condiciones = "WHERE " . implode(" AND ", $parametros);
                } else {
                    $condiciones = "";
                }

                $rspta = $cargo == "superadmin" ? $reporte->listarEntradas($condiciones) : $reporte->listarEntradasLocal($idlocalSession, $condiciones);

                $data = array();

                $firstIteration = true;
                $totalPrecioCompra = 0;

                while ($reg = $rspta->fetch_object()) {
                    $cargo_detalle = "";

                    switch ($reg->cargo) {
                        case 'superadmin':
                            $cargo_detalle = "Superadministrador";
                            break;
                        case 'admin':
                            $cargo_detalle = "Administrador del local";
                            break;
                        case 'usuario':
                            $cargo_detalle = "Usuario";
                            break;
                        case 'mirador':
                            $cargo_detalle = "Mirador";
                            break;
                        case 'almacenero':
                            $cargo_detalle = "Almacenero";
                            break;
                        case 'encargado':
                            $cargo_detalle = "Encargado del pedido";
                            break;
                        default:
                            break;
                    }

                    $data[] = array(
                        "0" => '<div style="display: flex; justify-content: center;">' .
                            ('<a target="_blank" href="../reportes/exEntrada.php?id=' . $reg->identrada . '"><button class="btn btn-success" style="margin-right: 3px; height: 35px;"><i class="fa fa-file"></i></button></a>') .
                            '</div>',
                        "1" => $reg->fecha,
                        "2" => $reg->local,
                        "3" => "N° " . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
                        "4" => $reg->total_compra,
                        "5" => ($reg->tipo == '' ? 'Sin registrar.' : $reg->tipo),
                        "6" => $reg->proveedor,
                        "7" => $reg->usuario,
                        "8" => $cargo_detalle,
                        "9" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
                            '<span class="label bg-red">Desactivado</span>'
                    );

                    $totalPrecioCompra += $reg->total_compra;
                    $firstIteration = false;
                }

                if (!$firstIteration) {
                    $data[] = array(
                        "0" => "",
                        "1" => "",
                        "2" => "",
                        "3" => "<strong>TOTAL</strong>",
                        "4" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                        "5" => "",
                        "6" => "",
                        "7" => "",
                        "8" => "",
                        "9" => "",
                    );
                }

                $results = array(
                    "sEcho" => 1, //Información para el datatables
                    "iTotalRecords" => count($data), //enviamos el total registros al datatable
                    "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
                    "aaData" => $data
                );
                echo json_encode($results);

                break;

                /* ======================= REPORTE DE SALIDAS ======================= */

            case 'listarSalidas':
                $parametros = array();

                if ($cargo != "superadmin") {
                    $parametros[] = "s.idlocal = '$idlocalSession'";
                }

                $filtros = array(
                    "param1" => "DATE(s.fecha_hora) BETWEEN '{$_GET["param1"]}' AND '{$_GET["param2"]}'",
                    "param3" => "s.codigo = '{$_GET["param3"]}'",
                    "param4" => "s.idlocal = '{$_GET["param4"]}'",
                    "param5" => "u.idusuario = '{$_GET["param5"]}'",
                    "param6" => "s.estado = '{$_GET["param6"]}'",
                    "param7" => "t.titulo LIKE '%{$_GET["param7"]}%'",
                    "param8" => "s.tipo_movimiento LIKE '%{$_GET["param8"]}%'",
                    "param9" => "p.nombre LIKE '%{$_GET["param9"]}%'",
                    "param10" => "pea.nombre LIKE '%{$_GET["param10"]}%'",
                    "param11" => "per.nombre LIKE '%{$_GET["param11"]}%'",
                );

                foreach ($filtros as $param => $condicion) {
                    if (!empty($_GET[$param])) {
                        $parametros[] = $condicion;
                    }
                }

                if (count($parametros) > 0) {
                    $condiciones = "WHERE " . implode(" AND ", $parametros);
                } else {
                    $condiciones = "";
                }

                $rspta = $cargo == "superadmin" ? $reporte->listarSalidas($condiciones) : $reporte->listarSalidasLocal($idlocalSession, $condiciones);

                $data = array();

                $firstIteration = true;
                $totalPrecioCompra = 0;

                while ($reg = $rspta->fetch_object()) {
                    $cargo_detalle = "";

                    switch ($reg->cargo) {
                        case 'superadmin':
                            $cargo_detalle = "Superadministrador";
                            break;
                        case 'admin':
                            $cargo_detalle = "Administrador del local";
                            break;
                        case 'usuario':
                            $cargo_detalle = "Usuario";
                            break;
                        case 'mirador':
                            $cargo_detalle = "Mirador";
                            break;
                        case 'almacenero':
                            $cargo_detalle = "Almacenero";
                            break;
                        case 'encargado':
                            $cargo_detalle = "Encargado del pedido";
                            break;
                        default:
                            break;
                    }

                    $data[] = array(
                        "0" => '<div style="display: flex; justify-content: center;">' .
                            ('<a target="_blank" href="../reportes/exSalida.php?id=' . $reg->idsalida . '"><button class="btn btn-success" style="margin-right: 3px; height: 35px;"><i class="fa fa-file"></i></button></a>') .
                            '</div>',
                        "1" => $reg->fecha,
                        "2" => $reg->local,
                        "3" => 'N° ' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
                        "4" => $reg->total_compra,
                        "5" => ($reg->tipo == '' ? 'Sin registrar.' : $reg->tipo),
                        "6" => ($reg->tipo_movimiento == '' ? 'Sin registrar.' : ucwords($reg->tipo_movimiento)),
                        "7" => ($reg->maquinaria == '' ? 'Sin registrar.' : $reg->maquinaria),
                        "8" => ($reg->autorizado == '' ? 'Sin registrar.' : $reg->autorizado),
                        "9" => ($reg->recibido == '' ? 'Sin registrar.' : $reg->recibido),
                        "10" => $reg->usuario,
                        "11" => $cargo_detalle,
                        "12" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
                            '<span class="label bg-red">Desactivado</span>'
                    );

                    $totalPrecioCompra += $reg->total_compra;
                    $firstIteration = false;
                }

                if (!$firstIteration) {
                    $data[] = array(
                        "0" => "",
                        "1" => "",
                        "2" => "",
                        "3" => "<strong>TOTAL</strong>",
                        "4" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                        "5" => "",
                        "6" => "",
                        "7" => "",
                        "8" => "",
                        "9" => "",
                        "10" => "",
                        "11" => "",
                        "12" => "",
                    );
                }

                $results = array(
                    "sEcho" => 1, //Información para el datatables
                    "iTotalRecords" => count($data), //enviamos el total registros al datatable
                    "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
                    "aaData" => $data
                );
                echo json_encode($results);

                break;
        }
    } else {
        require 'noacceso.php';
    }
}
ob_end_flush();
