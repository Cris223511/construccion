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
		require_once "../modelos/Activos.php";

		$activos = new Activo();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idactivo = isset($_POST["idactivo"]) ? limpiarCadena($_POST["idactivo"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idactivo)) {
					$nombreExiste = $activos->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre del activo ya existe.";
					} else {
						$rspta = $activos->agregar($idusuario, $titulo, $descripcion);
						echo $rspta ? "Activo registrado" : "El activo no se pudo registrar";
					}
				} else {
					$nombreExiste = $activos->verificarNombreEditarExiste($titulo, $idactivo);
					if ($nombreExiste) {
						echo "El nombre del activo ya existe.";
					} else {
						$rspta = $activos->editar($idactivo, $titulo, $descripcion);
						echo $rspta ? "Activo actualizado" : "El activo no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $activos->desactivar($idactivo);
				echo $rspta ? "Activo desactivado" : "El activo no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $activos->activar($idactivo);
				echo $rspta ? "Activo activado" : "El activo no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $activos->eliminar($idactivo);
				echo $rspta ? "Activo eliminado" : "El activo no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $activos->mostrar($idactivo);
				echo json_encode($rspta);
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				// if ($cargo == "superadmin") {
				if ($fecha_inicio == "" && $fecha_fin == "") {
					$rspta = $activos->listar();
				} else {
					$rspta = $activos->listarPorFecha($fecha_inicio, $fecha_fin);
				}
				// } else {
				// if ($fecha_inicio == "" && $fecha_fin == "") {
				// $rspta = $activos->listarPorUsuario($idusuario);
				// } else {
				// $rspta = $activos->listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin);
				// }
				// }

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
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idactivo . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idactivo . ')"><i class="fa fa-close"></i></button>')) : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idactivo . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idactivo . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->titulo,
						"2" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;'' readonly>" . (($reg->descripcion == '') ? 'Sin registrar.' : $reg->descripcion) . "</textarea>",
						"3" => ucwords($reg->nombre),
						"4" => ucwords($cargo_detalle),
						"5" => $reg->fecha,
						"6" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
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

			case 'selectActivos':
				$rspta = $activos->listar();

				echo '<option value="">- Seleccione -</option>';
				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->titulo . '"> ' . $reg->titulo . '</option>';
				}
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
