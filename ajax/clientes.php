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
		require_once "../modelos/Clientes.php";

		$clientes = new Cliente();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
		$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
		$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
		$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
		$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
		$fecha_nac = isset($_POST["fecha_nac"]) ? limpiarCadena($_POST["fecha_nac"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idcliente)) {
					$nombreExiste = $clientes->verificarDniExiste($num_documento);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $clientes->agregar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac);
						echo $rspta ? "Cliente registrado" : "El cliente no se pudo registrar";
					}
				} else {
					$nombreExiste = $clientes->verificarDniEditarExiste($nombre, $idcliente);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $clientes->editar($idcliente, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac);
						echo $rspta ? "Cliente actualizado" : "El cliente no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $clientes->desactivar($idcliente);
				echo $rspta ? "Cliente desactivado" : "El cliente no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $clientes->activar($idcliente);
				echo $rspta ? "Cliente activado" : "El cliente no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $clientes->eliminar($idcliente);
				echo $rspta ? "Cliente eliminado" : "El cliente no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $clientes->mostrar($idcliente);
				echo json_encode($rspta);
				break;

			case 'listar':

				if ($cargo == "superadmin" || $cargo == "admin") {
					$rspta = $clientes->listarClientes();
				} else {
					$rspta = $clientes->listarClientesPorUsuario($idusuario);
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
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idcliente . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idcliente . ')"><i class="fa fa-close"></i></button>')) :
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idcliente . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idcliente . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => ucwords($reg->nombre),
						"2" => $reg->tipo_documento,
						"3" => $reg->num_documento,
						"4" => $reg->direccion,
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
