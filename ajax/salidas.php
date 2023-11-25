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
		require_once "../modelos/Salidas.php";

		$salidas = new Salida();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$idsalida = isset($_POST["idsalida"]) ? limpiarCadena($_POST["idsalida"]) : "";
		$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
		$idmarca = isset($_POST["idmarca"]) ? limpiarCadena($_POST["idmarca"]) : "";
		$idmedida = isset($_POST["idmedida"]) ? limpiarCadena($_POST["idmedida"]) : "";
		$idtipo = isset($_POST["idtipo"]) ? limpiarCadena($_POST["idtipo"]) : "";
		$idmaquinaria = isset($_POST["idmaquinaria"]) ? limpiarCadena($_POST["idmaquinaria"]) : "";
		$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$ubicacion = isset($_POST["ubicacion"]) ? limpiarCadena($_POST["ubicacion"]) : "";
		$peso = isset($_POST["peso"]) ? limpiarCadena($_POST["peso"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				$codigoExiste = $salidas->verificarCodigo($codigo);
				if ($codigoExiste) {
					echo "El código de la salida ya existe.";
				} else {
					$rspta = $salidas->agregar($idusuario, $idcategoria, $idmarca, $idmedida, $idtipo, $idmaquinaria, $idpersonal, $codigo, $ubicacion, $peso, $descripcion,  $_POST["idarticulo"], $_POST["cantidad"]);
				echo $rspta ? "Salida registrada" : "Una de las cantidades superan al stock normal del artículo.";
				}
				break;

			case 'desactivar':
				$rspta = $salidas->desactivar($idsalida);
				echo $rspta ? "Salida desactivada" : "La salida no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $salidas->activar($idsalida);
				echo $rspta ? "Salida activada" : "La salida no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $salidas->eliminar($idsalida);
				echo $rspta ? "Salida eliminada" : "La salida no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $salidas->mostrar($idsalida);
				echo json_encode($rspta);
				break;

			case 'listarDetalle':
				$id = $_GET['id'];

				$rspta = $salidas->listarDetalle($id);

				$total = 0;
				echo '<thead style="background-color:#A9D0F5">
									<th>Opciones</th>
									<th>Artículo</th>
									<th>Cantidad</th>
									<th>Código de barra</th>
								</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas"><td></td><td>' . $reg->articulo . '</td><td>' . $reg->cantidad . '</td><td>' . $reg->codigo . '</td></tr>';
				}
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				if ($cargo == "superadmin") {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $salidas->listar();
					} else {
						$rspta = $salidas->listarPorFecha($fecha_inicio, $fecha_fin);
					}
				} else {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $salidas->listarPorUsuario($idusuario);
					} else {
						$rspta = $salidas->listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin);
					}
				}

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
					$reg->descripcion = (strlen($reg->descripcion) > 70) ? substr($reg->descripcion, 0, 70) . "..." : $reg->descripcion;

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idsalida . ')"><i class="fa fa-eye"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idsalida . ')"><i class="fa fa-close"></i></button>')) .
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<a target="_blank" href="../reportes/exSalida.php?id=' . $reg->idsalida . '"><button class="btn btn-success" style="margin-right: 3px; height: 35px;"><i class="fa fa-file"></i></button></a>')) :
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idsalida . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idsalida . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->usuario,
						"2" => $cargo_detalle,
						"3" => $reg->categoria,
						"4" => $reg->marca,
						"5" => $reg->tipo,
						"6" => $reg->codigo,
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

			case 'listarArticulos':
				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				if ($cargo == "superadmin" || $cargo == "admin") {
					$rspta = $articulo->listar();
				} else {
					$rspta = $articulo->listarPorUsuario($idusuario);
				}

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

					$data[] = array(
						"0" => ($reg->stock != '0') ? '<div style="display: flex; justify-content: center;"><button class="btn btn-warning" data-idarticulo="' . $reg->idarticulo . '" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',\'' . $reg->codigo . '\'); disableButton(this);"><span class="fa fa-plus"></span></button></div>' : '',
						"1" => $reg->usuario,
						"2" => $cargo_detalle,
						"3" => $reg->nombre,
						"4" => $reg->categoria,
						"5" => $reg->local,
						"6" => $reg->marca,
						"7" => $reg->codigo_producto,
						"8" => $reg->codigo,
						"9" => ($reg->stock > 0 && $reg->stock < $reg->stock_minimo) ? '<span style="color: #Ea9900; font-weight: bold">' . $reg->stock . '</span>' : (($reg->stock != '0') ? '<span>' . $reg->stock . '</span>' : '<span style="color: red; font-weight: bold">' . $reg->stock . '</span>'),
						"10" => $reg->stock_minimo,
						"11" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px' >",
						"12" => $reg->fecha,
						"13" => ($reg->stock > 0 && $reg->stock < $reg->stock_minimo) ? '<span class="label bg-orange">agotandose</span>' : (($reg->stock != '0') ? '<span class="label bg-green">Disponible</span>' : '<span class="label bg-red">agotado</span>')
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);

				echo json_encode($results);
				break;

			case 'selectProducto':
				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				if ($cargo == "superadmin") {
					$rspta = $articulo->listar();
				} else {
					$rspta = $articulo->listarPorUsuario($idusuario);
				}

				echo '<option value="">Busca un producto.</option>';
				while ($reg = $rspta->fetch_object()) {
					if (!empty($reg->codigo) && $reg->stock != '0') {
						echo '<option value="' . $reg->idarticulo . '">' . $reg->codigo . ' - ' . $reg->nombre . '</option>';
					}
				}
				break;

			case 'listarProductos':
				$idarticulos = $_GET["idarticulo"];

				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				$rspta = $articulo->listarActivosPorArticulo($idarticulos);

				$productos = array();
				while ($reg = $rspta->fetch_object()) {
					$producto = array(
						'idarticulo' => $reg->idarticulo,
						'articulo' => $reg->nombre,
						'codigo' => $reg->codigo
					);
					array_push($productos, $producto);
				}
				echo json_encode($productos);
				break;

				/* ======================= SELECTS ======================= */

			case 'listarTodosActivos':
				if ($cargo == "superadmin") {
					$rspta = $salidas->listarTodosActivos();
				} else {
					$rspta = $salidas->listarTodosActivosPorUsuario($idusuario);
				}

				$result = mysqli_fetch_all($rspta, MYSQLI_ASSOC);

				$data = [];
				foreach ($result as $row) {
					$tabla = $row['tabla'];
					unset($row['tabla']);
					$data[$tabla][] = $row;
				}

				echo json_encode($data);
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
