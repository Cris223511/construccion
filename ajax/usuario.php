<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}

if (empty($_SESSION['idusuario']) && empty($_SESSION['cargo']) && $_GET["op"] !== 'verificar') {
	echo json_encode(['error' => 'No está autorizado para realizar esta acción.']);
	exit();
}

require_once "../modelos/Usuario.php";

$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$cargo = isset($_POST["cargo"]) ? limpiarCadena($_POST["cargo"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				if (!empty($_FILES['imagen']['name'])) {
					$uploadDirectory = "../files/usuarios/";

					$tempFile = $_FILES['imagen']['tmp_name'];
					$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
					$newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
					$targetFile = $uploadDirectory . $newFileName;

					// Verificar si es una imagen y mover el archivo
					$allowedExtensions = array('jpg', 'jpeg', 'png');
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

				if (empty($idusuario)) {
					$dniExiste = $usuario->verificarDniExiste($num_documento);
					$usuarioExiste = $usuario->verificarUsuarioExiste($login);
					if ($dniExiste) {
						echo "El número de documento que ha ingresado ya existe.";
					} else if ($usuarioExiste) {
						echo "El nombre del usuario que ha ingresado ya existe.";
					} else {
						$rspta = $usuario->insertar($idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $_POST['permiso']);
						echo $rspta ? "Usuario registrado" : "Usuario no se pudo registrar.";
					}
				} else {
					$usuarioExiste = $usuario->verificarUsuarioEditarExiste($login, $idusuario);
					if ($usuarioExiste) {
						echo "El nombre del usuario que ha ingresado ya existe.";
					} else {
						$rspta = $usuario->editar($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $_POST['permiso']);
						echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
					}
				}
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'desactivar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$rspta = $usuario->desactivar($idusuario);
				echo $rspta ? "Usuario Desactivado" : "Usuario no se puede desactivar";
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'activar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$rspta = $usuario->activar($idusuario);
				echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'eliminar':
		$rspta = $usuario->eliminar($idusuario);
		echo $rspta ? "Usuario eliminado" : "Usuario no se puede eliminar";
		break;

	case 'mostrar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['perfilu'] == 1) {
				$rspta = $usuario->mostrar($idusuario);
				echo json_encode($rspta);
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'listar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$rspta = $usuario->listar();
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
						case 'usuario':
							$cargo_detalle = "Usuario";
							break;
						default:
							break;
					}

					$telefono = ($reg->telefono == '') ? 'Sin registrar' : number_format($reg->telefono, 0, '', ' ');

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							(!($reg->cargo == "superadmin" && $_SESSION['cargo'] == 'admin') ?
								((($reg->estado) ?
									(($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idusuario . '); verificarCargo(\'' . $reg->cargo . '\');"><i class="fa fa-pencil"></i></button>') : '') .
									(($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idusuario . ')"><i class="fa fa-close"></i></button>') : '') .
									(($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idusuario . ')"><i class="fa fa-trash"></i></button>') : '') : (($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idusuario . '), verificarCargo(\'' . $reg->cargo . '\');"><i class="fa fa-pencil"></i></button>') : '') .
									(($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px; padding: 0;" onclick="activar(' . $reg->idusuario . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>') : '') .
									(($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idusuario . ')"><i class="fa fa-trash"></i></button>') : '')) . '</div>') : ("")),
						"1" => $reg->login,
						"2" => $cargo_detalle,
						"3" => $reg->nombre,
						"4" => $reg->tipo_documento,
						"5" => $reg->num_documento,
						"6" => $telefono,
						"7" => $reg->email,
						"8" => $reg->local,
						"9" => "N° " . $reg->local_ruc,
						"10" => "<img src='../files/usuarios/" . $reg->imagen . "' height='50px' width='50px' >",
						"11" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' :
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
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'listarUsuariosActivos':
		$rspta = $usuario->listarUsuariosActivos();

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
				default:
					break;
			}
			echo '<option value="' . $reg->idusuario . '"> ' . $reg->nombre  . ' - ' . $cargo_detalle . '</option>';
		}
		break;

	case 'selectUsuarios':
		$cargoSession = $_SESSION["cargo"];
		if ($cargoSession == "superadmin") {
			$rspta = $usuario->listarASCactivos();
		} else {
			$rspta = $usuario->listarPorUsuarioASCActivos($_SESSION['idusuario']);
		}

		echo '<option value="">- Seleccione -</option>';
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
				default:
					break;
			}
			echo '<option value="' . $reg->idusuario . '"> ' . $reg->nombre  . ' - ' . $cargo_detalle . '</option>';
		}
		break;

	case 'permisos':
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();

		$id = $_GET['id'];
		$marcados = $usuario->listarmarcados($id);
		$valores = array();

		while ($per = $marcados->fetch_object()) {
			array_push($valores, $per->idpermiso);
		}

		while ($reg = $rspta->fetch_object()) {
			$sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
			echo '<li> <input type="checkbox" ' . $sw . '  name="permiso[]" value="' . $reg->idpermiso . '">' . $reg->nombre . '</li>';
		}
		break;

	case 'getSessionId':
		$sessionIdData = array(
			'idusuario' => $_SESSION['idusuario'],
			'idlocal' => $_SESSION['idlocal']
		);

		echo json_encode($sessionIdData);
		break;

	case 'verificar':
		$logina = $_POST['logina'];
		$clavea = $_POST['clavea'];

		$rspta = $usuario->verificar($logina, $clavea);

		$fetch = $rspta->fetch_object();

		if (isset($fetch)) {
			if ($fetch->eliminado == "1") {
				echo 1;
				return;
			}

			if ($fetch->estado == "0") {
				echo 0;
				return;
			}

			$localExiste = $usuario->localExiste($fetch->idlocal);
			if ($localExiste == 0) {
				echo 3;
				return;
			}

			if ($fetch->estadoLocal == "desactivado") {
				echo 2;
				return;
			}

			//Declaramos las variables de sesión
			$_SESSION['idusuario'] = $fetch->idusuario;
			$_SESSION['idlocal'] = $fetch->idlocal;
			$_SESSION['local'] = $fetch->local;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['login'] = $fetch->login;
			$_SESSION['clave'] = $fetch->clave;
			$_SESSION['cargo'] = $fetch->cargo;

			switch ($_SESSION['cargo']) {
				case 'superadmin':
					$_SESSION['cargo_detalle'] = "Superadministrador";
					break;
				case 'admin':
					$_SESSION['cargo_detalle'] = "Administrador";
					break;
				case 'usuario':
					$_SESSION['cargo_detalle'] = "Usuario";
					break;
				default:
					break;
			}

			$marcados = $usuario->listarmarcados($fetch->idusuario);
			$valores = array();

			while ($per = $marcados->fetch_object()) {
				array_push($valores, $per->idpermiso);
			}

			in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
			in_array(2, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
			in_array(3, $valores) ? $_SESSION['perfilu'] = 1 : $_SESSION['perfilu'] = 0;
			in_array(4, $valores) ? $_SESSION['almacen'] = 1 : $_SESSION['almacen'] = 0;
			in_array(5, $valores) ? $_SESSION['entradas'] = 1 : $_SESSION['entradas'] = 0;
			in_array(6, $valores) ? $_SESSION['salidas'] = 1 : $_SESSION['salidas'] = 0;
		}
		echo json_encode($fetch);
		break;

	case 'salir':
		//Limpiamos las variables de sesión   
		session_unset();
		//Destruìmos la sesión
		session_destroy();
		//Redireccionamos al login
		header("Location: ../index.php");

		break;
}

ob_end_flush();
