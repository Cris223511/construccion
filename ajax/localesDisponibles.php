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
		$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

		$idlocal_asignar = isset($_POST["idlocal_asignar"]) ? limpiarCadena($_POST["idlocal_asignar"]) : "";
		$idusuario_asignar = isset($_POST["idusuario_asignar"]) ? limpiarCadena($_POST["idusuario_asignar"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (!empty($_FILES['imagen']['name'])) {
					$uploadDirectory = "../files/locales/";

					$tempFile = $_FILES['imagen']['tmp_name'];
					$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
					$newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
					$targetFile = $uploadDirectory . $newFileName;

					// Verificar si es una imagen y mover el archivo
					$allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif', 'bmp');
					if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
						// El archivo se ha movido correctamente, ahora $newFileName contiene el nombre del archivo
						$imagen = $newFileName;
					} else {
						// Error en la subida del archivo
						echo "Error al subir la imagen.";
						exit;
					}
				} else {
					// No se ha seleccionado ninguna imagen
					$imagen = $_POST["imagenactual"];
				}

				if (empty($idlocal)) {
					$nombreExiste = $locales->verificarNombreExiste($titulo);
					if ($nombreExiste) {
						echo "El nombre del local ya existe.";
					} else {
						$rspta = $locales->agregar($titulo, $local_ruc, $descripcion, $imagen);
						echo $rspta ? "Local registrado" : "El local no se pudo registrar";
					}
				} else {
					$nombreExiste = $locales->verificarNombreEditarExiste($titulo, $idlocal);
					if ($nombreExiste) {
						echo "El nombre del local ya existe.";
					} else {
						$rspta = $locales->editar($idlocal, $titulo, $local_ruc, $descripcion, $imagen);
						echo $rspta ? "Local actualizado" : "El local no se pudo actualizar";

						if ($idlocal == $_SESSION['idlocal']) {
							$_SESSION['local'] = $titulo;
							$_SESSION['local_imagen'] = $imagen;
						}
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

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							(($reg->estado == 'activado') ?
								(('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
								(('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idlocal . ')"><i class="fa fa-close"></i></button>')) .
								(('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idlocal . ')"><i class="fa fa-trash"></i></button>')) : (('<button class="btn btn-warning" STYLE="margin-right: 3px;" onclick="mostrar(' . $reg->idlocal . ')"><i class="fa fa-pencil"></i></button>')) .
								(('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idlocal . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>')) .
								(('<button class="btn btn-danger" style="width: 35px; height: 35px;" onclick="eliminar(' . $reg->idlocal . ')"><i class="fa fa-trash"></i></button>'))) . '</div>',
						"1" => '<a href="../files/locales/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/locales/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"2" => $reg->titulo,
						"3" => "N° " . $reg->local_ruc,
						"4" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;'' readonly>" . (($reg->descripcion == '') ? 'Sin registrar.' : $reg->descripcion) . "</textarea>",
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

			case 'selectLocalDisponible':
				$rspta = $locales->listarLocalesDisponiblesActivos();

				echo '<option value="">- Seleccione -</option>';
				while ($reg = $rspta->fetch_object()) {
					echo '<option value="' . $reg->idlocal . '"> ' . $reg->titulo . '</option>';
				}
				break;

			case 'actualizarSession':
				$idlocal = isset($_POST['idlocal']) ? $_POST['idlocal'] : '';

				if ($idlocal == $_SESSION['idlocal']) {
					$info = array('local' => $_SESSION['local'], 'local_imagen' => $_SESSION['local_imagen']);
				} else {
					$info = array('local' => '', 'local_imagen' => '');
				}

				echo json_encode($info);
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
