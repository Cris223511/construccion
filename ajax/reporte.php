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

                $lastIdEntrada = null;
                $firstIteration = true;
                $totalPrecioCompra = 0;

                while ($reg = $rspta->fetch_object()) {
                    if (!$firstIteration && $reg->identrada != $lastIdEntrada) {
                        $data[] = array(
                            "0" => "",
                            "1" => "",
                            "2" => "",
                            "3" => "",
                            "4" => "",
                            "5" => "<strong>TOTAL</strong>",
                            "6" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                            "7" => "",
                            "8" => "",
                            "9" => "",
                            "10" => "",
                        );

                        $data[] = array_fill(0, 11, '');

                        $totalPrecioCompra = 0;
                    }

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
                            $cargo_detalle = "Usuario mirador";
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
                        "0" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
                                    <img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
                                </a>',
                        "1" => $reg->nombre,
                        "2" => $reg->codigo_producto,
                        "3" => $reg->local,
                        "4" => $reg->cantidad,
                        "5" => $reg->stock,
                        "6" => $reg->precio_compra,
                        "7" => "N° " . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
                        "8" => $reg->usuario,
                        "9" => $cargo_detalle,
                        "10" => $reg->fecha,
                    );

                    $lastIdEntrada = $reg->identrada;
                    $totalPrecioCompra += $reg->precio_compra;

                    $firstIteration = false;
                }

                if (!$firstIteration) {
                    $data[] = array(
                        "0" => "",
                        "1" => "",
                        "2" => "",
                        "3" => "",
                        "4" => "",
                        "5" => "<strong>TOTAL</strong>",
                        "6" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                        "7" => "",
                        "8" => "",
                        "9" => "",
                        "10" => "",
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
                    $parametros[] = "e.idlocal = '$idlocalSession'";
                }

                $filtros = array(
                    "param1" => "DATE(e.fecha_hora) BETWEEN '{$_GET["param1"]}' AND '{$_GET["param2"]}'",
                    "param3" => "e.codigo = '{$_GET["param3"]}'",
                    "param4" => "e.idlocal = '{$_GET["param4"]}'",
                    "param5" => "u.idusuario = '{$_GET["param5"]}'",
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

                $lastIdSalida = null;
                $firstIteration = true;
                $totalPrecioCompra = 0;

                while ($reg = $rspta->fetch_object()) {
                    if (!$firstIteration && $reg->idsalida != $lastIdSalida) {
                        $data[] = array(
                            "0" => "",
                            "1" => "",
                            "2" => "",
                            "3" => "",
                            "4" => "",
                            "5" => "<strong>TOTAL</strong>",
                            "6" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                            "7" => "",
                            "8" => "",
                            "9" => "",
                            "10" => "",
                        );

                        $data[] = array_fill(0, 11, '');

                        $totalPrecioCompra = 0;
                    }

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
                            $cargo_detalle = "Usuario mirador";
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
                        "0" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
                                        <img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
                                    </a>',
                        "1" => $reg->nombre,
                        "2" => $reg->codigo_producto,
                        "3" => $reg->local,
                        "4" => $reg->cantidad,
                        "5" => $reg->stock,
                        "6" => $reg->precio_compra,
                        "7" => "N° " . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
                        "8" => $reg->usuario,
                        "9" => $cargo_detalle,
                        "10" => $reg->fecha,
                    );

                    $lastIdSalida = $reg->idsalida;
                    $totalPrecioCompra += $reg->precio_compra;

                    $firstIteration = false;
                }

                if (!$firstIteration) {
                    $data[] = array(
                        "0" => "",
                        "1" => "",
                        "2" => "",
                        "3" => "",
                        "4" => "",
                        "5" => "<strong>TOTAL</strong>",
                        "6" => "<strong>" . number_format($totalPrecioCompra, 2) . "</strong>",
                        "7" => "",
                        "8" => "",
                        "9" => "",
                        "10" => "",
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

                /* ======================= REPORTE DE COMPARACIONES ======================= */

            case 'listarComparaciones':
                $parametros = array();

                if ($cargo != "superadmin") {
                    $parametros[] = "e.idlocal = '$idlocalSession' AND s.idlocal = '$idlocalSession'";
                }

                $filtros = array(
                    "param1" => "DATE(e.fecha_hora) BETWEEN '{$_GET["param1"]}' AND '{$_GET["param2"]}' AND DATE(s.fecha_hora) BETWEEN '{$_GET["param1"]}' AND '{$_GET["param2"]}'",
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

                $rspta = $cargo == "superadmin" ? $reporte->listarCombinaciones($condiciones) : $reporte->listarCombinacionesLocal($idlocalSession, $condiciones);

                $data = array();

                while ($reg = $rspta->fetch_object()) {
                    $data[] = array(
                        "0" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
                                    <img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
                                </a>',
                        "1" => $reg->nombre,
                        "2" => $reg->codigo_producto,
                        "3" => $reg->cantidad_entrada,
                        "4" => $reg->cantidad_salida,
                        "5" => $reg->stock,
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
