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
	if ($_SESSION['entradas'] == 1) {
		require_once "../modelos/Proveedores.php";

		$proveedores = new Proveedor();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idproveedor = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
		$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
		$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
		$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
		$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idproveedor)) {
					$nombreExiste = $proveedores->verificarDniExiste($num_documento);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $proveedores->agregar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
						echo $rspta ? "Proveedor registrado" : "El proveedor no se pudo registrar";
					}
				} else {
					$nombreExiste = $proveedores->verificarDniEditarExiste($nombre, $idproveedor);
					if ($nombreExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else {
						$rspta = $proveedores->editar($idproveedor, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
						echo $rspta ? "Proveedor actualizado" : "El proveedor no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $proveedores->desactivar($idproveedor);
				echo $rspta ? "Proveedor desactivado" : "El proveedor no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $proveedores->activar($idproveedor);
				echo $rspta ? "Proveedor activado" : "El proveedor no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $proveedores->eliminar($idproveedor);
				echo $rspta ? "Proveedor eliminado" : "El proveedor no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $proveedores->mostrar($idproveedor);
				echo json_encode($rspta);
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];
				
				// if ($cargo == "superadmin") {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $proveedores->listar();
					} else {
						$rspta = $proveedores->listarPorFecha($fecha_inicio, $fecha_fin);
					}
				// } else {
				// 	if ($fecha_inicio == "" && $fecha_fin == "") {
				// 		$rspta = $proveedores->listarPorUsuario($idusuario);
				// 	} else {
				// 		$rspta = $proveedores->listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin);
				// 	}
				// }

				$data = array();

				function mostrarBoton($reg, $cargo, $idusuario, $buttonType)
				{
					if ($reg == "admin" && $cargo == "admin" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} elseif ($cargo == "superadmin" || $cargo == "usuario" && $idusuario == $_SESSION["idusuario"]) {
						return $buttonType;
					} else {
						return '';
					}
				}

				while ($reg = $rspta->fetch_object()) {
					$telefono = ($reg->telefono == "") ? 'Sin registrar' : number_format($reg->telefono, 0, '', ' ');
					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idproveedor . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idproveedor . ')"><i class="fa fa-close"></i></button>')) :
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idproveedor . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idproveedor . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->fecha,
						"2" => ucwords($reg->nombre),
						"3" => ($reg->tipo_documento == '') ? 'Sin registrar' : $reg->tipo_documento,
						"4" => ($reg->num_documento == '') ? 'Sin registrar' : $reg->num_documento,
						"5" => ($reg->direccion == '') ? 'Sin registrar' : $reg->direccion,
						"6" => $telefono,
						"7" => ($reg->email == '') ? 'Sin registrar' : $reg->email,
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
