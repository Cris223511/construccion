<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start();
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html");
} else {
	if ($_SESSION['prestamo'] == 1) {
		require_once "../modelos/Consultas.php";

		$consulta = new Consultas();

		$iddetalle_devolucion = isset($_POST["iddetalle_devolucion"]) ? limpiarCadena($_POST["iddetalle_devolucion"]) : "";

		switch ($_GET["op"]) {
				// artículos más devueltos

			case 'articulosmasdevueltos_tipo1':

				$rspta = $consulta->articulosmasdevueltos_tipo1();
				$data = array();

				$lastIdDevolucion = null;
				$firstIteration = true;
				$devolucionesUnicas = array();

				while ($reg = $rspta->fetch_object()) {
					if (!$firstIteration && $reg->iddevolucion != $lastIdDevolucion) {
						$data[] = array_fill(0, 10, '');
					}

					$data[] = array(
						"0" => 'N° ' . $reg->codigo_pedido,
						"1" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"2" => $reg->nombre,
						"3" => $reg->local,
						"4" => (($reg->categoria != "") ? $reg->categoria : "Sin registrar."),
						"5" => '<div class="nowrap-cell">' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</div>',
						"6" => $reg->codigo_producto,
						"7" => $reg->stock,
						"8" => $reg->cantidad_devuelta,
						"9" => $reg->fecha,
					);

					if (!isset($devolucionesUnicas[$reg->iddevolucion])) {
						$devolucionesUnicas[$reg->iddevolucion] = true;
					}

					$firstIteration = false;
					$lastIdDevolucion = $reg->iddevolucion;
				}

				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);

				break;

			case 'articulosmasdevueltos_tipo2':

				$rspta = $consulta->articulosmasdevueltos_tipo2();
				$data = array();

				$lastIdDevolucion = null;
				$firstIteration = true;
				$devolucionesUnicas = array();

				while ($reg = $rspta->fetch_object()) {
					if (!$firstIteration && $reg->iddevolucion != $lastIdDevolucion) {
						$data[] = array_fill(0, 11, '');
					}

					$data[] = array(
						"0" => '<div style="display: flex; justify-content: center;"><button style="height: 34px;" class="btn btn-info" title="Reparar producto" onclick="reparar(\'' . $reg->iddetalle_devolucion . '\',\'' . $reg->local . '\')"><i class="fa fa-gavel"></i></button></div>',
						"1" => 'N° ' . $reg->codigo_pedido,
						"2" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"3" => $reg->nombre,
						"4" => $reg->local,
						"5" => (($reg->categoria != "") ? $reg->categoria : "Sin registrar."),
						"6" => '<div class="nowrap-cell">' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</div>',
						"7" => $reg->codigo_producto,
						"8" => $reg->stock,
						"9" => $reg->cantidad_devuelta,
						"10" => $reg->fecha,
					);

					if (!isset($devolucionesUnicas[$reg->iddevolucion])) {
						$devolucionesUnicas[$reg->iddevolucion] = true;
					}

					$firstIteration = false;
					$lastIdDevolucion = $reg->iddevolucion;
				}

				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);

				break;

			case 'reparar':
				$rspta = $consulta->reparar($iddetalle_devolucion);
				echo $rspta ? "Artículo reparado y enviado al almacén de origen exitosamente." : "Artículo no se puede reparar";
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
