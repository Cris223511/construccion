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
	if ($_SESSION['personas'] == 1) {
		require_once "../modelos/Trabajadores.php";

		$trabajadores = new Trabajador();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idtrabajador = isset($_POST["idtrabajador"]) ? limpiarCadena($_POST["idtrabajador"]) : "";
		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
		$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
		$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
		$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
		$fecha_nac = isset($_POST["fecha_nac"]) ? limpiarCadena($_POST["fecha_nac"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idtrabajador)) {
					$nombreExiste = $trabajadores->verificarDniExiste($num_documento);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $trabajadores->agregar($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $telefono, $email, $fecha_nac);
						echo $rspta ? "Trabajador registrado" : "El trabajador no se pudo registrar";
					}
				} else {
					$nombreExiste = $trabajadores->verificarDniEditarExiste($nombre, $idtrabajador);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $trabajadores->editar($idtrabajador, $idlocal, $nombre, $tipo_documento, $num_documento, $telefono, $email, $fecha_nac);
						echo $rspta ? "Trabajador actualizado" : "El trabajador no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $trabajadores->desactivar($idtrabajador);
				echo $rspta ? "Trabajador desactivado" : "El trabajador no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $trabajadores->activar($idtrabajador);
				echo $rspta ? "Trabajador activado" : "El trabajador no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $trabajadores->eliminar($idtrabajador);
				echo $rspta ? "Trabajador eliminado" : "El trabajador no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $trabajadores->mostrar($idtrabajador);
				echo json_encode($rspta);
				break;

			case 'listar':

				if ($cargo == "superadmin" || $cargo == "admin") {
					$rspta = $trabajadores->listarTrabajadores();
				} else {
					$rspta = $trabajadores->listarTrabajadoresPorLocal($idlocalSession);
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
					$telefono = number_format($reg->telefono, 0, '', ' ');
					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idtrabajador . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idtrabajador . ')"><i class="fa fa-close"></i></button>')) :
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idtrabajador . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idtrabajador . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => ucwords($reg->nombre),
						"2" => $reg->tipo_documento,
						"3" => $reg->num_documento,
						"4" => $reg->local,
						"5" => $telefono,
						"6" => $reg->email,
						"7" => $reg->fecha,
						"8" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
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
