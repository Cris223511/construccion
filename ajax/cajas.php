<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}

if (empty($_SESSION['idusuario']) || empty($_SESSION['cargo'])) {
	echo 'No está autorizado para realizar esta acción.';
	exit();
}

if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html");
} else {
	if ($_SESSION['cajas'] == 1) {
		require_once "../modelos/Cajas.php";

		$cajas = new Caja();

		// Variables de sesión a utilizar.
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
		$idcaja = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";
		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$monto = isset($_POST["monto"]) ? limpiarCadena($_POST["monto"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idcaja)) {
					$nombreExiste = $cajas->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre de la caja ya existe.";
					} else {
						$rspta = $cajas->agregar($idusuario, $idlocal, $titulo, $monto, $descripcion);
						echo $rspta ? "Caja registrada" : "La caja no se pudo registrar";
					}
				} else {
					$nombreExiste = $cajas->verificarNombreEditarExiste($titulo, $idcaja);
					if ($nombreExiste) {
						echo "El nombre de la caja ya existe.";
					} else {
						$rspta = $cajas->editar($idcaja, $idusuario, $idlocal, $titulo, $monto, $descripcion);
						echo $rspta ? "Caja actualizada" : "La caja no se pudo actualizar";
					}
				}
				break;

			case 'cerrar':
				$rspta = $cajas->cerrar($idcaja);
				echo $rspta ? "Caja cerrada" : "La caja no se pudo cerrar";
				break;

			case 'aperturar':
				$rspta = $cajas->aperturar($idcaja);
				echo $rspta ? "Caja aperturada" : "La caja no se pudo aperturar";
				break;

			case 'eliminar':
				$rspta = $cajas->eliminar($idcaja);
				echo $rspta ? "Caja eliminada" : "La caja no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $cajas->mostrar($idcaja);
				echo json_encode($rspta);
				break;

			case 'listar':

				$param1 = $_GET["param1"]; // valor fecha inicio
				$param2 = $_GET["param2"]; // valor fecha fin
				$param3 = $_GET["param3"]; // valor local

				if ($cargo == "superadmin" || $cargo == "admin") {
					if ($param1 != '' && $param2 != '' && $param3 == '') {
						$rspta = $cajas->listarPorParametro("DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2'");
					} else if ($param1 != '' && $param2 != '' && $param3 != '') {
						$rspta = $cajas->listarPorParametro("DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2' AND c.idlocal = '$param3'");
					} else if ($param1 == '' && $param2 == '' && $param3 != '') {
						$rspta = $cajas->listarPorParametro("c.idlocal = '$param3'");
					} else {
						$rspta = $cajas->listar();
					}
				} else {
					if ($param1 != '' && $param2 != '' && $param3 == '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2'");
					} else if ($param1 != '' && $param2 != '' && $param3 != '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2' AND c.idlocal = '$param3'");
					} else if ($param1 == '' && $param2 == '' && $param3 != '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "c.idlocal = '$param3'");
					} else {
						$rspta = $cajas->listarPorUsuario($idlocalSession);
					}
				}

				$data = array();

				function mostrarBoton($reg, $cargo, $idusuario, $buttonType)
				{
					if ($reg == "admin" && $cargo == "admin" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} elseif ($cargo == "superadmin" || $cargo == "cajero" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} else {
						return '';
					}
				}

				while ($reg = $rspta->fetch_object()) {
					$cargo_detalle = "";

					switch ($reg->cargo) {
						case 'superadmin':
							$cargo_detalle = "Superadministrador";
							break;
						case 'admin':
							$cargo_detalle = "Administrador";
							break;
						case 'cajero':
							$cargo_detalle = "Cajero";
							break;
						default:
							break;
					}

					$reg->descripcion = (strlen($reg->descripcion) > 70) ? substr($reg->descripcion, 0, 70) . "..." : $reg->descripcion;

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idcaja . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado != 'aperturado' && ($cargo == "superadmin" || $cargo == "admin")) ?
								mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="aperturar(' . $reg->idcaja . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>') : ('')) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idcaja . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->titulo,
						"2" => $reg->local,
						"3" => ucwords($reg->nombre),
						"4" => ucwords($cargo_detalle),
						"5" => 'S/. ' . number_format($reg->monto, 2, '.', ','),
						"6" => $reg->fecha,
						"7" => ($reg->estado == 'aperturado') ? '<span class="label bg-green">Aperturado</span>' :
							'<span class="label bg-red">Cerrado</span>'
					);
				}
				$results = array(
					"sEcho" => 1,
					"iTotalRecords" => count($data),
					"iTotalDisplayRecords" => count($data),
					"aaData" => $data
				);

				echo json_encode($results);
				break;

			case 'listar2':
				$param1 = $_GET["param1"]; // valor fecha inicio
				$param2 = $_GET["param2"]; // valor fecha fin
				$param3 = $_GET["param3"]; // valor local

				if ($cargo == "superadmin" || $cargo == "admin") {
					if ($param1 != '' && $param2 != '' && $param3 == '') {
						$rspta = $cajas->listarPorParametro("DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2'");
					} else if ($param1 != '' && $param2 != '' && $param3 != '') {
						$rspta = $cajas->listarPorParametro("DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2' AND c.idlocal = '$param3'");
					} else if ($param1 == '' && $param2 == '' && $param3 != '') {
						$rspta = $cajas->listarPorParametro("c.idlocal = '$param3'");
					} else {
						$rspta = $cajas->listar();
					}
				} else {
					if ($param1 != '' && $param2 != '' && $param3 == '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2'");
					} else if ($param1 != '' && $param2 != '' && $param3 != '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "DATE(c.fecha_hora) >= '$param1' AND DATE(c.fecha_hora) <= '$param2' AND c.idlocal = '$param3'");
					} else if ($param1 == '' && $param2 == '' && $param3 != '') {
						$rspta = $cajas->listarPorUsuarioParametro($idlocalSession, "c.idlocal = '$param3'");
					} else {
						$rspta = $cajas->listarPorUsuario($idlocalSession);
					}
				}

				$data = array();

				function mostrarBoton($reg, $cargo, $idusuario, $buttonType)
				{
					if ($reg == "admin" && $cargo == "admin" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} elseif ($cargo == "superadmin" || $cargo == "cajero" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} else {
						return '';
					}
				}

				while ($reg = $rspta->fetch_object()) {
					$cargo_detalle = "";

					switch ($reg->cargo) {
						case 'superadmin':
							$cargo_detalle = "Superadministrador";
							break;
						case 'admin':
							$cargo_detalle = "Administrador";
							break;
						case 'cajero':
							$cargo_detalle = "Cajero";
							break;
						default:
							break;
					}

					$reg->descripcion = (strlen($reg->descripcion) > 70) ? substr($reg->descripcion, 0, 70) . "..." : $reg->descripcion;

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px; justify-content: center">' .
							(($reg->estado == 'aperturado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; width: 35px; height: 35px;" onclick="cerrar(' . $reg->idcaja . ')"><i class="fa fa-close"></i></button>')) : ('')) .
							'</div>',
						"1" => $reg->titulo,
						"2" => $reg->local,
						"3" => ucwords($reg->nombre),
						"4" => ucwords($cargo_detalle),
						"5" => 'S/. ' . number_format($reg->monto, 2, '.', ','),
						"6" => $reg->fecha,
						"7" => ($reg->fecha_cierre == '00-00-0000 00:00:00') ? 'Sin registrar.' : $reg->fecha_cierre,
						"8" => ($reg->estado == 'aperturado') ? '<span class="label bg-green">Aperturado</span>' :
							'<span class="label bg-red">Cerrado</span>'
					);
				}
				$results = array(
					"sEcho" => 1,
					"iTotalRecords" => count($data),
					"iTotalDisplayRecords" => count($data),
					"aaData" => $data
				);

				echo json_encode($results);
				break;

				// case 'selectCajas':
				// 	if ($cargo == "superadmin" || $cargo == "admin") {
				// 		$rspta = $cajas->listar();
				// 	} else {
				// 		$rspta = $cajas->listarPorUsuario($idusuario);
				// 	}

				// 	echo '<option value="">- Seleccione -</option>';
				// 	while ($reg = $rspta->fetch_object()) {
				// 		echo '<option value="' . $reg->idcaja . '"> ' . $reg->titulo . ' - ' . $reg->nombre . '</option>';
				// 	}
				// 	break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
