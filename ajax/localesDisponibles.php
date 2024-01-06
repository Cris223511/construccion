<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}

// si no está logeado o no tiene ningún cargo, no accede a ninguna opción, si está logeado, accede a todas las opciones.
if ((empty($_SESSION['idusuario']) || empty($_SESSION['cargo'])) && ($_SESSION['cargo'] == "superadmin" || $_SESSION['cargo'] == "admin")) {
	echo 'No está autorizado para realizar esta acción.';
	exit();
}

if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html");
} else {
	if ($_SESSION['perfilu'] == 1) {
		require_once "../modelos/LocalesDisponibles.php";

		$locales = new LocalDisponible();

		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
		$local_ruc = isset($_POST["local_ruc"]) ? limpiarCadena($_POST["local_ruc"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		$idlocal_asignar = isset($_POST["idlocal_asignar"]) ? limpiarCadena($_POST["idlocal_asignar"]) : "";
		$idusuario_asignar = isset($_POST["idusuario_asignar"]) ? limpiarCadena($_POST["idusuario_asignar"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($idlocal)) {
					$nombreExiste = $locales->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre del local ya existe.";
					} else {
						$rspta = $locales->agregar($titulo, $local_ruc, $descripcion);
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

			case 'guardaryeditar2':
				$rspta = $locales->asignar($idlocal_asignar, $idusuario_asignar);
				echo $rspta ? "Local asignado correctamente" : "El local no se pudo asignar";
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

				$rspta = $locales->listarLocalesDisponibles();
				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$reg->descripcion = (strlen($reg->descripcion) > 70) ? substr($reg->descripcion, 0, 70) . "..." : $reg->descripcion;

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							(($reg->estado == 'activado') ?
								(('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
								(('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idlocal . ')"><i class="fa fa-close"></i></button>')) .
								(('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idlocal . ')"><i class="fa fa-trash"></i></button>')) : (('<button class="btn btn-warning" STYLE="margin-right: 3px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
								(('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idlocal . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>')) .
								(('<button class="btn btn-danger" style="width: 35px; height: 35px;" onclick="eliminar(' . $reg->idlocal . ')"><i class="fa fa-trash"></i></button>'))) . '</div>',
						"1" => $reg->titulo,
						"2" => "N° " . $reg->local_ruc,
						"3" => $reg->descripcion,
						"4" => $reg->fecha,
						"5" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
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

			case 'selectLocalDisponible':
				$rspta = $locales->listarLocalesDisponiblesActivos();

				echo '<option value="">- Seleccione -</option>';
				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->idlocal . '"> ' . $reg->titulo . '</option>';
				}
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
