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
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['almacen'] == 1) {
		require_once "../modelos/Articulo.php";

		$articulo = new Articulo();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idarticulo = isset($_POST["idarticulo"]) ? limpiarCadena($_POST["idarticulo"]) : "";
		$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$idmarca = isset($_POST["idmarca"]) ? limpiarCadena($_POST["idmarca"]) : "";
		$idmedida = isset($_POST["idmedida"]) ? limpiarCadena($_POST["idmedida"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$codigo_producto = isset($_POST["codigo_producto"]) ? limpiarCadena($_POST["codigo_producto"]) : "";
		$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
		$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
		$stock_minimo = isset($_POST["stock_minimo"]) ? limpiarCadena($_POST["stock_minimo"]) : "";
		$precio_compra = isset($_POST["precio_compra"]) ? limpiarCadena($_POST["precio_compra"]) : "";
		$precio_compra_mayor = isset($_POST["precio_compra_mayor"]) ? limpiarCadena($_POST["precio_compra_mayor"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
		$casillero = isset($_POST["casillero"]) ? limpiarCadena($_POST["casillero"]) : "";
		$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
		$fecha_emision = isset($_POST["fecha_emision"]) ? limpiarCadena($_POST["fecha_emision"]) : "";
		$fecha_vencimiento = isset($_POST["fecha_vencimiento"]) ? limpiarCadena($_POST["fecha_vencimiento"]) : "";
		$talla = isset($_POST["talla"]) ? limpiarCadena($_POST["talla"]) : "";
		$color = isset($_POST["color"]) ? limpiarCadena($_POST["color"]) : "";
		$peso = isset($_POST["peso"]) ? limpiarCadena($_POST["peso"]) : "";
		$nota_1 = isset($_POST["nota_1"]) ? limpiarCadena($_POST["nota_1"]) : "";
		$nota_2 = isset($_POST["nota_2"]) ? limpiarCadena($_POST["nota_2"]) : "";
		$imei = isset($_POST["imei"]) ? limpiarCadena($_POST["imei"]) : "";
		$serial = isset($_POST["serial"]) ? limpiarCadena($_POST["serial"]) : "";
		$barra = isset($_POST["barra"]) ? limpiarCadena($_POST["barra"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':

				if (!empty($_FILES['imagen']['name'])) {
					$uploadDirectory = "../files/articulos/";

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

				if (empty($idarticulo)) {
					$codigoExiste = $articulo->verificarCodigoExiste($codigo);
					$codigoProductoExiste = $articulo->verificarCodigoProductoExiste($codigo_producto);
					if ($codigoProductoExiste) {
						echo "El código del producto que ha ingresado ya existe.";
					} else if ($codigoExiste && $codigo != "") {
						echo "El código de barra del producto que ha ingresado ya existe.";
					} else {
						$rspta = $articulo->insertar($idusuario, $idcategoria, $idlocal, $idmarca, $idmedida, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $precio_compra, $precio_compra_mayor, $descripcion, $casillero, $imagen, $fecha_emision, $fecha_vencimiento, $talla, $color, $peso, $nota_1, $nota_2, $imei, $serial);
						echo $rspta ? "Producto registrado" : "El producto no se pudo registrar";
					}
				} else {
					$nombreExiste = $articulo->verificarCodigoProductoEditarExiste($codigo_producto, $idarticulo);
					if ($nombreExiste) {
						echo "El código del producto que ha ingresado ya existe.";
					} else {
						$rspta = $articulo->editar($idarticulo, $idcategoria, $idlocal, $idmarca, $idmedida, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $precio_compra, $precio_compra_mayor, $descripcion, $casillero, $imagen, $fecha_emision, $fecha_vencimiento, $talla, $color, $peso, $nota_1, $nota_2, $imei, $serial);
						echo $rspta ? "Producto actualizado" : "El producto no se pudo actualizar";
					}
				}
				break;

			case 'desactivar':
				$rspta = $articulo->desactivar($idarticulo);
				echo $rspta ? "Producto desactivado" : "El producto no se puede desactivar";
				break;

			case 'activar':
				$rspta = $articulo->activar($idarticulo);
				echo $rspta ? "Producto activado" : "El producto no se puede activar";
				break;

			case 'eliminar':
				$rspta = $articulo->eliminar($idarticulo);
				echo $rspta ? "Producto eliminado" : "El producto no se puede eliminar";
				break;

			case 'mostrar':
				$rspta = $articulo->mostrar($idarticulo);
				//Codificar el resultado utilizando json
				echo json_encode($rspta);
				break;

			case 'listar':
				$param = $_GET["param"]; // valor buscar

				$param1 = $_GET["param1"]; // valor marca
				$param2 = $_GET["param2"]; // valor categoria
				$param3 = $_GET["param3"]; // valor estado

				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				if ($param == 0) {
					if ($fecha_inicio != "" && $fecha_fin != "") {
						$rspta = $articulo->listarPorFechaUsuario($idlocalSession, $fecha_inicio, $fecha_fin);
					} else {
						$rspta = $articulo->listarPorUsuario($idlocalSession);
					}
				} else {
					if ($param1 != '' && $param2 == '' && $param3 == '') {
						$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1'");
					} else if ($param1 == '' && $param2 != '' && $param3 == '') {
						$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idcategoria = '$param2'");
					} else if ($param1 == '' && $param2 == '' && $param3 != '') {
						if ($param3 == "1") {
							// Disponible
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.stock > a.stock_minimo");
						} else if ($param3 == "2") {
							// Agotándose
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.stock > 0 AND a.stock < a.stock_minimo");
						} else {
							// Agotado
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.stock = 0");
						}
					} else if ($param1 != '' && $param2 != '' && $param3 == '') {
						$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.idcategoria = '$param2'");
					} else if ($param1 != '' && $param2 == '' && $param3 != '') {
						if ($param3 == "1") {
							// Disponible
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.stock > a.stock_minimo");
						} else if ($param3 == "2") {
							// Agotándose
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.stock > 0 AND a.stock < a.stock_minimo");
						} else {
							// Agotado
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.stock = 0");
						}
					} else if ($param1 == '' && $param2 != '' && $param3 != '') {
						if ($param3 == "1") {
							// Disponible
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idcategoria = '$param2' AND a.stock > a.stock_minimo");
						} else if ($param3 == "2") {
							// Agotándose
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idcategoria = '$param2' AND a.stock > 0 AND a.stock < a.stock_minimo");
						} else {
							// Agotado
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idcategoria = '$param2' AND a.stock = 0");
						}
					} else if ($param1 != '' && $param2 != '' && $param3 != '') {
						if ($param3 == "1") {
							// Disponible
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.idcategoria = '$param2' AND a.stock > a.stock_minimo");
						} else if ($param3 == "2") {
							// Agotándose
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.idcategoria = '$param2' AND a.stock > 0 AND a.stock < a.stock_minimo");
						} else {
							// Agotado
							$rspta = $articulo->listarPorUsuarioParametro($idlocalSession, "a.idmarca = '$param1' AND a.idcategoria = '$param2' AND a.stock = 0");
						}
					} else {
						$rspta = $articulo->listarPorUsuario($idlocalSession);
					}
				}

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
							$cargo_detalle = "Administrador";
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
							$cargo_detalle = "Encargado";
							break;
						default:
							break;
					}

					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idarticulo . ')"><i class="fa fa-pencil"></i></button>') .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger "style="height: 35px;" onclick="eliminar(' . $reg->idarticulo . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"2" => $reg->nombre,
						"3" => $reg->medida,
						"4" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->descripcion == '') ? 'Sin registrar.' : $reg->descripcion) . "</textarea>",
						"5" => (($reg->categoria != "") ? $reg->categoria : "Sin registrar."),
						"6" => (($reg->marca != "") ? $reg->marca : "Sin registrar."),
						"7" => $reg->local,
						"8" => ($reg->stock > 0 && $reg->stock < $reg->stock_minimo) ? '<span style="color: #Ea9900; font-weight: bold">' . $reg->stock . '</span>' : (($reg->stock != '0') ? '<span>' . $reg->stock . '</span>' : '<span style="color: red; font-weight: bold">' . $reg->stock . '</span>'),
						"9" => $reg->stock_minimo,
						"10" => "S/. " . number_format($reg->precio_compra, 2, '.', ','),
						"11" => "S/. " . number_format($reg->precio_compra_mayor, 2, '.', ','),
						"12" => $reg->codigo_producto,
						"13" => (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
						"14" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->talla == "") ? 'Sin registrar.' : $reg->talla) . "</textarea>",
						"15" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->color == "") ? 'Sin registrar.' : $reg->color) . "</textarea>",
						"16" => ($reg->peso != "") ? $reg->peso : "Sin registrar.",
						"17" => ($reg->fecha_emision == '00-00-0000') ? 'Sin registrar.' : $reg->fecha_emision,
						"18" => ($reg->fecha_vencimiento == '00-00-0000') ? 'Sin registrar.' : $reg->fecha_vencimiento,
						"19" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->nota_1 == "") ? 'Sin registrar.' : $reg->nota_1) . "</textarea>",
						"20" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->nota_2 == "") ? 'Sin registrar.' : $reg->nota_2) . "</textarea>",
						"21" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->imei == "") ? 'Sin registrar.' : $reg->imei) . "</textarea>",
						"22" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->serial == "") ? 'Sin registrar.' : $reg->serial) . "</textarea>",
						"23" => $reg->usuario,
						"24" => $cargo_detalle,
						"25" => $reg->fecha,
						"26" => ($reg->stock > 0 && $reg->stock < $reg->stock_minimo) ? '<span class="label bg-orange">agotandose</span>' : (($reg->stock != '0') ? '<span class="label bg-green">Disponible</span>' : '<span class="label bg-red">agotado</span>')
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

				/* ======================= SELECTS ======================= */

			case 'listarTodosActivos':
				if ($cargo == "superadmin") {
					$rspta = $articulo->listarTodosActivos();
				} else {
					$rspta = $articulo->listarTodosActivosPorUsuario($idusuario, $idlocalSession);
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
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
