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
	if ($_SESSION['almacen'] == 1) {
		require_once "../modelos/Servicios.php";

		$servicios = new Servicio();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idservicio = isset($_POST["idservicio"]) ? limpiarCadena($_POST["idservicio"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
		$costo = isset($_POST["costo"]) ? limpiarCadena($_POST["costo"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idservicio)) {
					$nombreExiste = $servicios->verificarNombreExiste($titulo);
					$codigoExiste = $servicios->verficarCodigoExiste($codigo);
					if ($nombreExiste) {
						echo "El nombre del servicio ya existe.";
					} else if ($codigoExiste) {
						echo "El código del servicio ya existe.";
					} else {
						$rspta = $servicios->agregar($idusuario, $titulo, $codigo, $descripcion, $costo);
						echo $rspta ? "Servicio registrado" : "El servicio no se pudo registrar";
					}
				} else {
					$nombreExiste = $servicios->verificarNombreEditarExiste($titulo, $idservicio);
					$codigoExiste = $servicios->verficarCodigoEditarExiste($codigo, $idservicio);
					if ($nombreExiste) {
						echo "El nombre del servicio ya existe.";
					} else if ($codigoExiste) {
						echo "El código del servicio ya existe.";
					} else {
						$rspta = $servicios->editar($idservicio, $titulo, $codigo, $descripcion, $costo);
						echo $rspta ? "Servicio actualizado" : "El servicio no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $servicios->desactivar($idservicio);
				echo $rspta ? "Servicio desactivado" : "El servicio no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $servicios->activar($idservicio);
				echo $rspta ? "Servicio activado" : "El servicio no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $servicios->eliminar($idservicio);
				echo $rspta ? "Servicio eliminado" : "El servicio no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $servicios->mostrar($idservicio);
				echo json_encode($rspta);
				break;

			case 'listar':

				if ($cargo == "superadmin" || $cargo == "admin") {
					$rspta = $servicios->listar();
				} else {
					$rspta = $servicios->listarPorUsuario($idusuario);
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
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idservicio . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idservicio . ')"><i class="fa fa-close"></i></button>')) :
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idservicio . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idservicio . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->titulo,
						"2" => "N° " . $reg->codigo,
						"3" => $reg->descripcion,
						"4" => "S/. " . number_format($reg->costo, 2, '.', ','),
						"5" => ucwords($reg->nombre),
						"6" => ucwords($cargo_detalle),
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

			case 'getLastCodigo':
				$result = $servicios->getLastCodigo();
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_assoc($result);
					$last_codigo = $row["last_codigo"];
				} else {
					$last_codigo = '00000';
				}
				echo $last_codigo;
				break;
				
				// case 'selectServicios':
				// 	if ($cargo == "superadmin" || $cargo == "admin") {
				// 		$rspta = $servicios->listar();
				// 	} else {
				// 		$rspta = $servicios->listarPorUsuario($idusuario);
				// 	}

				// 	echo '<option value="">- Seleccione -</option>';
				// 	while ($reg = $rspta->fetch_object()) {
				// 		echo '<option value="' . $reg->idservicio . '"> ' . $reg->titulo . ' - ' . $reg->nombre . '</option>';
				// 	}
				// 	break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
