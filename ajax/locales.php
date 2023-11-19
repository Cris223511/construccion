<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}

// si no está logeado o no tiene ningún cargo...
if (empty($_SESSION['idusuario']) || empty($_SESSION['cargo'])) {
	// opciones a las que NO pueden tener acceso... si no colocamos ninguno, quiere decir
	// que tiene acceso a todas las opciones si es que está logeado o tiene un cargo.
	if (($_GET["op"] == 'selectLocal' || $_GET["op"] == 'selectLocalUsuario' || $_GET["op"] == 'selectLocalDisponible')) {
		echo 'No está autorizado para realizar esta acción.';
		exit();
	}
}

if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html");
} else {
	if ($_SESSION['perfilu'] == 1) {
		require_once "../modelos/Locales.php";

		$locales = new Local();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$idlocal_session = $_SESSION['idlocal'];
		$cargo = $_SESSION["cargo"];

		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$idusuariolocal = isset($_POST["idusuariolocal"]) ? limpiarCadena($_POST["idusuariolocal"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$local_ruc = isset($_POST["local_ruc"]) ? limpiarCadena($_POST["local_ruc"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idlocal)) {
					$nombreExiste = $locales->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre del local ya existe.";
					} else {
						$rspta = $locales->agregar($idusuario, $titulo, $local_ruc, $descripcion);
						echo $rspta ? "Local registrado" : "El local no se pudo registrar";
					}
				} else {
					$nombreExiste = $locales->verificarNombreEditarExiste($titulo, $idlocal);
					if ($nombreExiste) {
						echo "El nombre del local ya existe.";
					} else {
						$rspta = $locales->editar($idlocal, $titulo, $local_ruc, $descripcion);
						echo $rspta ? "Local actualizado" : "El local no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $locales->desactivar($idlocal);
				echo $rspta ? "Local desactivado" : "El local no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $locales->activar($idlocal);
				echo $rspta ? "Local activado" : "El local no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $locales->eliminar($idlocal);
				echo $rspta ? "Local eliminado" : "El local no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $locales->mostrar($idlocal);
				echo json_encode($rspta);
				break;

			case 'listar':

				$rspta = $locales->listar();

				$data = array();

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
						"0" => ($cargo == "admin" || $cargo == "cajero") ?
							('<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
								(($reg->estado == 'activado') ?
									(($reg->idusuario == $_SESSION["idusuario"]) ? ('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>') : ('')) .
									(('<a data-toggle="modal" href="#myModal"><button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="trabajadores(' . $reg->idlocal . ',\'' . $reg->titulo . '\')"><i class="fa fa-user"></i></button></a>')) .
									(($reg->idusuario == $_SESSION["idusuario"]) ? ('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idlocal . ')"><i class="fa fa-close"></i></button>') : ('')) :
									(('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
									(($reg->idusuario == $_SESSION["idusuario"]) ? ('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idlocal . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>') : (''))) . '</div>')
							: ('<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
								(($reg->estado == 'activado') ?
									('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>') .
									(('<a data-toggle="modal" href="#myModal"><button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="trabajadores(' . $reg->idlocal . ',\'' . $reg->titulo . '\')"><i class="fa fa-user"></i></button></a>')) .
									('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idlocal . ')"><i class="fa fa-close"></i></button>') : (('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
									('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idlocal . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>')) . '</div>'),
						"1" => $reg->titulo,
						"2" => $reg->local_ruc,
						"3" => $reg->descripcion,
						"4" => ucwords($reg->nombre),
						"5" => ucwords($cargo_detalle),
						"6" => $reg->fecha,
						"7" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
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

			case 'listarTrabajadores':

				$idlocal2 = isset($_GET["idlocal"]) ? limpiarCadena($_GET["idlocal"]) : "";

				$rspta = $locales->listarTrabajadoresPorLocal($idlocal2);

				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => ucwords($reg->nombre),
						"1" => $reg->tipo_documento,
						"2" => $reg->num_documento,
						"3" => $reg->local,
						"4" => $reg->telefono,
						"5" => $reg->email,
						"6" => $reg->fecha,
						"7" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
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

			case 'selectLocal':
				$rspta = $locales->listarPorUsuarioActivos($idusuario);
				$result = mysqli_fetch_all($rspta, MYSQLI_ASSOC);

				$data = [];
				foreach ($result as $row) {
					$data["locales"][] = $row;
				}

				echo json_encode($data);
				break;

			case 'selectLocalASC':
				$rspta = $locales->listarPorUsuarioActivosASC($idusuario);
				$result = mysqli_fetch_all($rspta, MYSQLI_ASSOC);

				$data = [];
				foreach ($result as $row) {
					$data["locales"][] = $row;
				}

				echo json_encode($data);
				break;

			case 'selectLocales':
				$rspta = $locales->listarActivos();

				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->idlocal . '"> ' . $reg->titulo . '</option>';
				}
				break;

			case 'selectLocalesUsuario':

				if ($cargo == "superadmin") {
					$rspta = $locales->listarActivosASC();
				} else {
					$rspta = $locales->listarPorUsuarioActivosASC($idusuario);
				}

				echo '<option value="">- Seleccione -</option>';
				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->idlocal . '"> ' . $reg->titulo . '</option>';
				}
				break;

			case 'selectLocalUsuario':
				$rspta = $locales->listarPorUsuarioActivos($idusuariolocal);

				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->idlocal . '" data-local-ruc="' . $reg->local_ruc . '"> ' . $reg->titulo . '</option>';
				}
				break;

			case 'selectLocalDisponible':
				$rspta = $locales->listarLocalesDisponiblesActivos();
				$result = mysqli_fetch_all($rspta, MYSQLI_ASSOC);

				$data = [];
				foreach ($result as $row) {
					$data["locales"][] = $row;
				}

				echo json_encode($data);
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
