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
		require_once "../modelos/Entradas.php";

		$entradas = new Entrada();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$cargo = $_SESSION["cargo"];

		$identrada = isset($_POST["identrada"]) ? limpiarCadena($_POST["identrada"]) : "";
		$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
		$idmarca = isset($_POST["idmarca"]) ? limpiarCadena($_POST["idmarca"]) : "";
		$idtipo = isset($_POST["idtipo"]) ? limpiarCadena($_POST["idtipo"]) : "";
		$idmedida = isset($_POST["idmedida"]) ? limpiarCadena($_POST["idmedida"]) : "";
		$peso = isset($_POST["peso"]) ? limpiarCadena($_POST["peso"]) : "";
		$cantidad = isset($_POST["cantidad"]) ? limpiarCadena($_POST["cantidad"]) : "";
		$ubicacion = isset($_POST["ubicacion"]) ? limpiarCadena($_POST["ubicacion"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if (empty($identrada)) {
					$rspta = $entradas->agregar($idusuario, $idcategoria, $idmarca, $idtipo, $idmedida, $peso, $cantidad, $ubicacion, $codigo, $descripcion);
					echo $rspta ? "Entrada registrada" : "La entrada no se pudo registrar";
				} else {
					$rspta = $entradas->editar($identrada, $idcategoria, $idmarca, $idtipo, $idmedida, $peso, $cantidad, $ubicacion, $codigo, $descripcion);
					echo $rspta ? "Entrada actualizada" : "La entrada no se pudo actualizar";
				}
				break;

			case 'desactivar':
				$rspta = $entradas->desactivar($identrada);
				echo $rspta ? "Entrada desactivada" : "La entrada no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $entradas->activar($identrada);
				echo $rspta ? "Entrada activada" : "La entrada no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $entradas->eliminar($identrada);
				echo $rspta ? "Entrada eliminada" : "La entrada no se pudo eliminar";
				break;

			case 'mostrar':
				$rspta = $entradas->mostrar($identrada);
				echo json_encode($rspta);
				break;

			case 'listarDetalle':
				$id = $_GET['id'];

				$rspta = $entradas->listarDetalle($id);

				$total = 0;
				echo '<thead style="background-color:#A9D0F5">
									<th>Opciones</th>
									<th>Artículo</th>
									<th>Cantidad</th>
									<th>Código de barra</th>
								</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas"><td></td><td>' . $reg->nombre . '</td><td>' . $reg->cantidad . '</td><td>' . $reg->codigo . '</td></tr>';
				}
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				if ($cargo == "superadmin") {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $entradas->listar();
					} else {
						$rspta = $entradas->listarPorFecha($fecha_inicio, $fecha_fin);
					}
				} else {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $entradas->listarPorUsuario($idusuario);
					} else {
						$rspta = $entradas->listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin);
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
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->identrada . ')"><i class="fa fa-eye"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->identrada . ')"><i class="fa fa-close"></i></button>')) : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->identrada . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->identrada . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->usuario,
						"2" => $cargo_detalle,
						"3" => $reg->nombre,
						"4" => $reg->categoria,
						"6" => $reg->marca,
						"6" => $reg->tipo,
						"6" => $reg->proveedor,
						"8" => $reg->codigo,
						"5" => $reg->cantidad,
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
						"0" => ($reg->stock != '0') ? '<button class="btn btn-warning" data-idarticulo="' . $reg->idarticulo . '" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',\'' . $reg->codigo . '\'); disableButton(this);"><span class="fa fa-plus"></span></button>' : '',
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
					$rspta = $entradas->listarTodosActivos();
				} else {
					$rspta = $entradas->listarTodosActivosPorUsuario($idusuario);
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
