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
	if ($_SESSION['transferencias'] == 1) {
		require_once "../modelos/Transferencias.php";

		$transferencias = new Transferencia();

		// Variables de sesión a utilizar.
		$idusuario = $_SESSION["idusuario"];
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idtransferencia = isset($_POST["idtransferencia"]) ? limpiarCadena($_POST["idtransferencia"]) : "";
		$origen = isset($_POST["origen"]) ? limpiarCadena($_POST["origen"]) : "";
		$destino = isset($_POST["destino"]) ? limpiarCadena($_POST["destino"]) : "";
		$lugar_destino = isset($_POST["lugar_destino"]) ? limpiarCadena($_POST["lugar_destino"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$comentario = isset($_POST["comentario"]) ? limpiarCadena($_POST["comentario"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				$rspta = $transferencias->agregar($origen, $destino, $idusuario, $codigo, $lugar_destino, $comentario, $_POST["idarticulo"], $_POST["cantidad"]);
				echo $rspta ? "Transferencia registrada" : "Una de las cantidades superan al stock normal del artículo.";
				break;

			case 'desactivar':
				$rspta = $transferencias->desactivar($idtransferencia);
				echo $rspta ? "Transferencia desactivada" : "La transferencia no se pudo desactivar";
				break;

			case 'activar':
				$rspta = $transferencias->activar($idtransferencia);
				echo $rspta ? "Transferencia activada" : "La transferencia no se pudo activar";
				break;

			case 'eliminar':
				$rspta = $transferencias->eliminar($idtransferencia);
				echo $rspta ? "Transferencia eliminada" : "La transferencia no se pudo eliminar";
				break;

			case 'limpiar':
				$rspta = $transferencias->limpiar($idtransferencia);
				echo $rspta ? "Transferencia removida" : "La transferencia no se pudo remover";
				break;

			case 'mostrar':
				$rspta = $transferencias->mostrar($idtransferencia);
				echo json_encode($rspta);
				break;

			case 'listarDetalle':
				$id = $_GET['id'];

				$rspta = $transferencias->listarDetalle($id);
				$rspta2 = $transferencias->mostrar($id);

				$total = 0;
				echo '<thead style="background-color:#A9D0F5">
										<th>Opciones</th>
										<th>Imagen</th>
										<th>Artículo</th>
										<th>Categoría</th>
										<th>Marca</th>
										<th style="white-space: nowrap;">Código de producto</th>
										<th style="white-space: nowrap;">Código de barra</th>
										<th>Stock</th>
										<th style="white-space: nowrap;">Stock Mínimo</th>
										<th>Cantidad a transferir</th>
										<th>Precio compra</th>
										<th style="white-space: nowrap;">Unidad de medida</th>
									</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas"><td></td> <td><a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;"><img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid"></a></td><td>' . $reg->articulo . '</td><td>' . (($reg->categoria != "") ? $reg->categoria : "Sin registrar.") . '</td><td>' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</td><td>' . $reg->codigo_producto . '</td><td>' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . '</td><td>' . $reg->stock . '</td><td>' . $reg->stock_minimo . '</td><td>' . $reg->cantidad . '</td><td>' . "<nav>S/. " . number_format($reg->precio_compra, 2) . "</nav>" . '</td><td>' . $reg->medida . '</td></tr>';
				}
				break;

			case 'listar':
				$fecha_inicio = $_GET["fecha_inicio"];
				$fecha_fin = $_GET["fecha_fin"];

				if ($cargo == "superadmin") {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $transferencias->listar();
					} else {
						$rspta = $transferencias->listarPorFecha($fecha_inicio, $fecha_fin);
					}
				} else {
					if ($fecha_inicio == "" && $fecha_fin == "") {
						$rspta = $transferencias->listarPorUsuario($idlocalSession);
					} else {
						$rspta = $transferencias->listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin);
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

				$firstIteration = true;
				$totalCantidad = 0;

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
							'<button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idtransferencia . ')"><i class="fa fa-eye"></i></button>' .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idtransferencia . ')"><i class="fa fa-close"></i></button>')) : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idtransferencia . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="eliminar(' . $reg->idtransferencia . ')"><i class="fa fa-trash"></i></button>')) .
							(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-warning" style="height: 35px;" onclick="limpiarTransferencia(' . $reg->idtransferencia . ')"><i class="fa fa-minus-circle"></i></button>')) .
							'</div>',
						"1" => $reg->fecha,
						"2" => "N° " . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
						"3" => $reg->total_cantidad,
						"4" => $reg->origen,
						"5" => $reg->destino,
						"6" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->comentario == "") ? 'Sin registrar.' : $reg->comentario) . "</textarea>",
						"7" => $reg->usuario,
						"8" => $cargo_detalle,
						"9" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
							'<span class="label bg-red">Desactivado</span>'
					);

					$totalCantidad += $reg->total_cantidad;
					$firstIteration = false; // Marcar que ya no es la primera iteración
				}

				if (!$firstIteration) {
					$data[] = array(
						"0" => "",
						"1" => "",
						"2" => "<strong>TOTAL</strong>",
						"3" => '<strong>' . number_format($totalCantidad, 2) . '</strong>',
						"4" => "",
						"5" => "",
						"6" => "",
						"7" => "",
						"8" => "",
						"9" => "",
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

				$idlocal = $_GET["idlocal"];

				$rspta = $articulo->listarPorUsuario($idlocal);

				$data = array();

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
						"0" => '<div style="display: flex; justify-content: center;"><button class="btn btn-warning" style="height: 35px;" data-idarticulo="' . $reg->idarticulo . '" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',\'' . (($reg->categoria != "") ? $reg->categoria : "Sin registrar.") . '\',\'' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '\',\'' . $reg->medida . '\',\'' . $reg->stock . '\',\'' . $reg->stock_minimo . '\',\'' . ($reg->precio_compra) . '\',\'' . $reg->codigo_producto . '\',\'' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . '\',\'' . $reg->imagen . '\'); disableButton(this);"><span class="fa fa-plus"></span></button></div>',
						"1" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
										<img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
									</a>',
						"2" => $reg->nombre,
						"3" => $reg->medida,
						"4" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->descripcion == '') ? 'Sin registrar.' : $reg->descripcion) . "</textarea>",
						"5" => (($reg->categoria != "") ? $reg->categoria : "Sin registrar."),
						"6" => '<div class="nowrap-cell">' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</div>',
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

			case 'selectProducto':
				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				$idlocal = $_GET["idlocal"];

				$rspta = $articulo->listarPorUsuario($idlocal);

				echo '<option value="">Busca un producto.</option>';
				while ($reg = $rspta->fetch_object()) {
					if ($reg->stock != '0') {
						echo '<option value="' . $reg->idarticulo . '">' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . ' - ' . $reg->nombre . ' - ' . $reg->local . '</option>';
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
						'categoria' => $reg->categoria,
						'marca' => '<div class="nowrap-cell">' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</div>',
						'medida' => $reg->medida,
						'stock' => $reg->stock,
						'stock_minimo' => $reg->stock_minimo,
						// 'precio_compra' => ($reg->medida != "Paquetes") ? ($reg->precio_compra == '' ? "0" : $reg->precio_compra) : ($reg->precio_compra_mayor == '' ? "0" : $reg->precio_compra_mayor),
						'precio_compra' => $reg->precio_compra == '' ? "0" : $reg->precio_compra,
						'codigo_producto' => $reg->codigo_producto,
						'codigo' => (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
						'imagen' => $reg->imagen,
					);
					array_push($productos, $producto);
				}
				echo json_encode($productos);
				break;

				/* ======================= SELECTS ======================= */

			case 'listarTodosActivos':
				if ($cargo == "superadmin") {
					$rspta = $transferencias->listarTodosActivos();
				} else {
					$rspta = $transferencias->listarTodosActivosPorUsuario($idusuario, $idlocalSession);
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
