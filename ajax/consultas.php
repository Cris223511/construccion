<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start();
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html");
} else {
	if ($_SESSION['reporte'] == 1 || $_SESSION['reporteP'] == 1) {
		require_once "../modelos/Consultas.php";

		$consulta = new Consultas();


		switch ($_GET["op"]) {
				// artículos más devueltos

			case 'articulosmasdevueltos_tipo1':

				$rspta = $consulta->articulosmasdevueltos_tipo1();
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => $reg->codigo_producto,
						"1" => $reg->nombre,
						"2" => $reg->categoria,
						"3" => $reg->marca,
						"4" => $reg->local,
						"5" => $reg->stock,
						"6" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px' >",
						"7" => $reg->cantidad,
						"8" => $reg->fecha,
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

			case 'articulosmasdevueltos_tipo2':

				$rspta = $consulta->articulosmasdevueltos_tipo2();
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => $reg->codigo_producto,
						"1" => $reg->nombre,
						"2" => $reg->categoria,
						"3" => $reg->marca,
						"4" => $reg->local,
						"5" => $reg->stock,
						"6" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px' >",
						"7" => $reg->cantidad,
						"8" => $reg->fecha,
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
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
