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
		require_once "../modelos/Tipos.php";

		$tipos = new Tipo();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idtipo = isset($_POST["idtipo"]) ? limpiarCadena($_POST["idtipo"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idtipo)) {
					$nombreExiste = $tipos->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre del tipo ya existe.";
					} else {
						$rspta = $tipos->agregar($idusuario, $titulo, $descripcion);
						echo $rspta ? "Tipo registrado" : "El tipo no se pudo registrar";
					}
				} else {
					$nombreExiste = $tipos->verificarNombreEditarExiste($titulo, $idtipo);
					if ($nombreExiste) {
						echo "El nombre del tipo ya existe.";
					} else {
						$rspta = $tipos->editar($idtipo, $titulo, $descripcion);
						echo $rspta ? "Tipo actualizado" : "El tipo no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $tipos->desactivar($idtipo);
				echo $rspta ? "Tipo desactivado" : "El tipo no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $tipos->activar($idtipo);
				echo $rspta ? "Tipo activado" : "El tipo no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $tipos->eliminar($idtipo);
				echo $rspta ? "Tipo eliminado" : "El tipo no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $tipos->mostrar($idtipo);
				echo json_encode($rspta);
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				// if ($cargo == "superadmin") {
				if ($fecha_inicio == "" && $fecha_fin == "") {
					$rspta = $tipos->listar();
				} else {
					$rspta = $tipos->listarPorFecha($fecha_inicio, $fecha_fin);
				}
				// } else {
				// if ($fecha_inicio == "" && $fecha_fin == "") {
				// $rspta = $tipos->listarPorUsuario($idusuario);
				// } else {
				// $rspta = $tipos->listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin);
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
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idtipo . ')"><i class="fa fa-pencil"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idtipo . ')"><i class="fa fa-close"></i></button>')) : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idtipo . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idtipo . ')"><i class="fa fa-trash"></i></button>') .
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

			case 'selectTipos':
				$rspta = $tipos->listar();

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
