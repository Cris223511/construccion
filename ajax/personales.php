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
	if ($_SESSION['salidas'] == 1) {
		require_once "../modelos/Personales.php";

		$personales = new Personal();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
		$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
		$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
		$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
		$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idpersonal)) {
					$nombreExiste = $personales->verificarDniExiste($num_documento);
					if ($nombreExiste && $num_documento != '') {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $personales->agregar($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
						echo $rspta ? "Personal registrado" : "El personal no se pudo registrar";
					}
				} else {
					$nombreExiste = $personales->verificarDniEditarExiste($nombre, $idpersonal);
					if ($nombreExiste && $num_documento != '') {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $personales->editar($idpersonal, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
						echo $rspta ? "Personal actualizado" : "El personal no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $personales->desactivar($idpersonal);
				echo $rspta ? "Personal desactivado" : "El personal no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $personales->activar($idpersonal);
				echo $rspta ? "Personal activado" : "El personal no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $personales->eliminar($idpersonal);
				echo $rspta ? "Personal eliminado" : "El personal no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $personales->mostrar($idpersonal);
				echo json_encode($rspta);
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				if ($cargo == "superadmin") {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $personales->listar();
					} else {
						$rspta = $personales->listarPorFecha($fecha_inicio, $fecha_fin);
					}
				} else {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $personales->listarPorUsuario($idlocalSession);
					} else {
						$rspta = $personales->listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin);
					}
				}

				$data = array();

				function mostrarBoton($reg, $cargo, $idusuario, $buttonType)
				{
					if ($reg != "superadmin" && $cargo == "admin") {
						return $buttonType;
					} elseif ($cargo == "superadmin" || ($cargo == "usuario" && $idusuario == $_SESSION["idusuario"])) {
						return $buttonType;
					} elseif ($cargo == "mirador") {
						return '';
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
						case 'usuario':
							$cargo_detalle = "Usuario";
							break;
						case 'mirador':
							$cargo_detalle = "Mirador";
							break;
						default:
							break;
					}

					$telefono = ($reg->telefono == '') ? 'Sin registrar.' : number_format($reg->telefono, 0, '', ' ');

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idpersonal . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idpersonal . ')"><i class="fa fa-close"></i></button>')) : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idpersonal . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idpersonal . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->fecha,
						"2" => ucwords($reg->nombre),
						"3" => $reg->local,
						"4" => ($reg->tipo_documento == '') ? 'Sin registrar.' : $reg->tipo_documento,
						"5" => ($reg->num_documento == '') ? 'Sin registrar.' : $reg->num_documento,
						"6" => ($reg->direccion == '') ? 'Sin registrar.' : $reg->direccion,
						"7" => $telefono,
						"8" => ($reg->email == '') ? 'Sin registrar.' : $reg->email,
						"9" => $reg->usuario,
						"10" => $cargo_detalle,
						"11" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
							'<span class="label bg-red">Desactivado</span>'
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
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
