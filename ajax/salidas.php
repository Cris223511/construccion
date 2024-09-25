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
		$idlocalSession = $_SESSION["idlocal"];
		$cargo = $_SESSION["cargo"];

		$idsalida = isset($_POST["idsalida"]) ? limpiarCadena($_POST["idsalida"]) : "";
		$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
		$idtipo = isset($_POST["idtipo"]) ? limpiarCadena($_POST["idtipo"]) : "";
		$tipo_movimiento = isset($_POST["tipo_movimiento"]) ? limpiarCadena($_POST["tipo_movimiento"]) : "";
		$idactivo = isset($_POST["idactivo"]) ? limpiarCadena($_POST["idactivo"]) : "";
		$idautorizado = isset($_POST["idautorizado"]) ? limpiarCadena($_POST["idautorizado"]) : "";
		$idrecibido = isset($_POST["idrecibido"]) ? limpiarCadena($_POST["idrecibido"]) : "";
		$idubicacion = isset($_POST["idubicacion"]) ? limpiarCadena($_POST["idubicacion"]) : "";
		$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
		$origen = isset($_POST["origen"]) ? limpiarCadena($_POST["origen"]) : "";
		$destino = isset($_POST["destino"]) ? limpiarCadena($_POST["destino"]) : "";
		$num_guia = isset($_POST["num_guia"]) ? limpiarCadena($_POST["num_guia"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
		$impuesto = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
		$total_compra = isset($_POST["total_compra"]) ? limpiarCadena($_POST["total_compra"]) : "";

		$sunat = isset($_POST["sunat"]) ? limpiarCadena($_POST["sunat"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				$codigoExiste = $salidas->verificarCodigo($codigo, $idlocal);
				if ($codigoExiste) {
					echo "El N° de documento de la salida ya existe.";
				} else {
					$rspta = $salidas->agregar($idlocal, $idusuario, $idtipo, $tipo_movimiento, $idactivo, $idautorizado, $idrecibido, $idubicacion, $codigo, $origen, $destino, $num_guia, $descripcion, $impuesto, $total_compra, $_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_compra"]);
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
				$rspta2 = $salidas->mostrar($id);

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
									<th>Cantidad</th>
                                    <th>Precio compra</th>
									<th style="white-space: nowrap;">Unidad de medida</th>
                                    <th>Subtotal</th>
								</thead>';

				while ($reg = $rspta->fetch_object()) {
					echo '<tr class="filas"><td></td> <td><a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;"><img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid"></a></td> <td>' . $reg->articulo . '</td><td>' . (($reg->categoria != "") ? $reg->categoria : "Sin registrar.") . '</td><td>' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '</td><td>' . $reg->codigo_producto . '</td><td>' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . '</td><td>' . $reg->stock . '</td><td>' . $reg->stock_minimo . '</td><td>' . $reg->cantidad . '</td><td>' . "<nav>S/. " . number_format($reg->precio_compra, 2) . "</nav>" . '</td><td>' . $reg->medida . '</td><td>' . "<nav>S/. " . number_format($reg->subtotal, 2) . "</nav>" . '</td></tr>';
					$igv = $igv + ($rspta2["impuesto"] == 18 ? $reg->subtotal * 0.18 : $reg->subtotal * 0);
				}

				echo '
				<tfoot>
					<tr>
					<th>IGV</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th><h4 id="igv">S/.' . number_format($igv, 2) . '</h4><input type="hidden" name="total_igv" id="total_igv"></th>
					</tr>
					<tr>
					<th>TOTAL</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th><h4 id="total">S/.' . number_format($rspta2["total_compra"], 2) . '</h4><input type="hidden" name="total_compra" id="total_compra"></th>
					</tr>
				</tfoot>';
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
						$rspta = $salidas->listarPorUsuario($idlocalSession);
					} else {
						$rspta = $salidas->listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin);
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
				$totalSalidas = 0;

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
							'<button class="btn btn-bcp" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idsalida . ')"><i class="fa fa-eye"></i></button>' .
							// ('<button class="btn btn-info" style="margin-right: 3px; height: 35px;" onclick="prueba(' . $reg->idsalida . ')"><i class="fa fa-info-circle"></i></button>') .
							(($reg->estado == 'activado') ?
								(mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idsalida . ')"><i class="fa fa-close"></i></button>')) .
								('<a target="_blank" href="../reportes/exSalida.php?id=' . $reg->idsalida . '"><button class="btn btn-success" style="margin-right: 3px; height: 35px;"><i class="fa fa-file"></i></button></a>') : (mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px;" onclick="activar(' . $reg->idsalida . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>'))) .
							mostrarBoton($reg->cargo, $cargo, $reg->idusuario, '<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idsalida . ')"><i class="fa fa-trash"></i></button>') .
							'</div>',
						"1" => $reg->fecha,
						"2" => $reg->local,
						"3" => 'N° ' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
						"4" => $reg->total_cantidad,
						"5" => ($reg->num_guia == '' ? 'Sin registrar.' : $reg->num_guia),
						"6" => ($reg->origen == '' ? 'Sin registrar.' : $reg->origen),
						"7" => ($reg->destino == '' ? 'Sin registrar.' : $reg->destino),
						"8" => ($reg->tipo == '' ? 'Sin registrar.' : $reg->tipo),
						"9" => ($reg->tipo_movimiento == '' ? 'Sin registrar.' : ucwords($reg->tipo_movimiento)),
						"10" => ($reg->autorizado == '' ? 'Sin registrar.' : $reg->autorizado),
						"11" => ($reg->recibido == '' ? 'Sin registrar.' : $reg->recibido),
						"12" => $reg->usuario,
						"13" => $cargo_detalle,
						"14" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' :
							'<span class="label bg-red">Desactivado</span>'
					);

					$totalSalidas += $reg->total_cantidad;
					$firstIteration = false; // Marcar que ya no es la primera iteración
				}

				if (!$firstIteration) {
					$data[] = array(
						"0" => "",
						"1" => "",
						"2" => "",
						"3" => "<strong>TOTAL</strong>",
						"4" => '<strong>' . number_format($totalSalidas, 2) . '</strong>',
						"5" => "",
						"6" => "",
						"7" => "",
						"8" => "",
						"9" => "",
						"10" => "",
						"11" => "",
						"12" => "",
						"13" => "",
						"14" => "",
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

			case 'selectPersonal':
				require_once "../modelos/Personales.php";
				$personales = new Personal();

				if ($cargo == "superadmin") {
					$rspta = $personales->listarASC();
				} else {
					$rspta = $personales->listarPorUsuarioASC($idlocalSession);
				}

				echo '<option value="">- Seleccione -</option>';
				while ($reg = $rspta->fetch_object()) {
					echo '<option value=' . $reg->idpersonal . '>' . $reg->nombre . '</option>';
				}
				break;

			case 'listarArticulos':
				require_once "../modelos/Articulo.php";
				$articulo = new Articulo();

				if ($cargo == "superadmin") {
					$rspta = $articulo->listar();
				} else {
					$rspta = $articulo->listarPorUsuario($idlocalSession);
				}

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
						"0" => '<div style="display: flex; justify-content: center;"><button class="btn btn-warning" style="height: 35px;" data-idarticulo="' . $reg->idarticulo . '" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',\'' . (($reg->categoria != "") ? $reg->categoria : "Sin registrar.") . '\',\'' . (($reg->marca != "") ? $reg->marca : "Sin registrar.") . '\',\'' . $reg->medida . '\',\'' . $reg->stock . '\',\'' . $reg->stock_minimo . '\',\'' . (($reg->precio_compra)) . '\',\'' . $reg->codigo_producto . '\',\'' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . '\',\'' . $reg->imagen . '\'); disableButton(this);"><span class="fa fa-plus"></span></button></div>',
						"1" => '<a href="../files/articulos/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/articulos/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"2" => $reg->nombre,
						"3" => $reg->medida,
						"4" => "<textarea type='text' class='form-control' rows='2' style='background-color: white !important; cursor: default; height: 60px !important;' readonly>" . (($reg->descripcion == '') ? 'Sin registrar.' : $reg->descripcion) . "</textarea>",
						"5" => $reg->categoria,
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

				if ($cargo == "superadmin") {
					$rspta = $articulo->listar();
				} else {
					$rspta = $articulo->listarPorUsuario($idlocalSession);
				}

				echo '<option value="">Busca un producto.</option>';
				while ($reg = $rspta->fetch_object()) {
					if ($reg->stock != '0') {
						echo '<option value="' . $reg->idarticulo . '">' . (($reg->codigo != "") ? $reg->codigo : "Sin registrar.") . ' - ' . $reg->nombre . '</option>';
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
						'precio_compra' => ($reg->precio_compra == '' ? "0" : $reg->precio_compra),
						'codigo_producto' => $reg->codigo_producto,
						'codigo' => (($reg->codigo != "") ? $reg->codigo : "Sin registrar."),
						'imagen' => $reg->imagen,
					);
					array_push($productos, $producto);
				}
				echo json_encode($productos);
				break;

			case 'getLastNumCodigo':
				$result = $salidas->getLastNumCodigo();
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_assoc($result);
					$last_codigo = $row["last_codigo"];
				} else {
					$last_codigo = 0;
				}
				echo $last_codigo;
				break;

			case 'prueba':
				$rspta = $salidas->listarCabecera($idsalida);

				$data = array();

				while ($reg = $rspta->fetch_object()) {
					$rowData = array();
					foreach ($reg as $key => $value) {
						$rowData[$key] = $value;
					}
					$data[] = $rowData;
				}

				echo json_encode(["data" => $data]);
				break;

				/* ======================= SELECTS ======================= */

			case 'listarTodosActivos':
				if ($cargo == "superadmin") {
					$rspta = $salidas->listarTodosActivos($idlocalSession);
				} else {
					$rspta = $salidas->listarTodosActivosUsuario($idlocalSession);
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

			case 'listarTodosLocalActivosPorUsuario':
				$rspta = $salidas->listarTodosLocalActivosPorUsuario($idlocal);

				$result = mysqli_fetch_all($rspta, MYSQLI_ASSOC);

				$data = [];
				foreach ($result as $row) {
					$tabla = $row['tabla'];
					unset($row['tabla']);
					$data[$tabla][] = $row;
				}

				echo json_encode($data);
				break;

				/* ======================= SUNAT ======================= */

			case 'consultaSunat':
				// Token para la API
				$token = 'apis-token-10245.si7J3XHG51QGHXgWHmECBYq6MOBBWDC2';

				$data = "";
				$curl = curl_init();

				try {
					if (strlen($sunat) == 8) {
						// DNI
						$url = 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $sunat;
						$referer = 'https://apis.net.pe/consulta-dni-api';
					} elseif (strlen($sunat) == 11) {
						// RUC
						$url = 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $sunat;
						$referer = 'http://apis.net.pe/api-ruc';
					} elseif (strlen($sunat) < 8) {
						// Mensaje para DNI no válido
						$data = "El DNI debe tener 8 caracteres.";
						echo $data;
						break;
					} elseif (strlen($sunat) > 8 && strlen($sunat) < 11) {
						// Mensaje para RUC no válido
						$data = "El RUC debe tener 11 caracteres.";
						echo $data;
						break;
					}

					// configuración de cURL
					curl_setopt_array($curl, array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => 0,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 2,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_CUSTOMREQUEST => 'GET',
						CURLOPT_HTTPHEADER => array(
							'Referer: ' . $referer,
							'Authorization: Bearer ' . $token
						),
					));

					$response = curl_exec($curl);

					if ($response === false) {
						throw new Exception(curl_error($curl));
					}

					if (stripos($response, 'Not Found') !== false || stripos($response, '{"message":"ruc no valido"}') !== false) {
						// Mensaje para DNI no válido o RUC no válido
						$data = (strlen($sunat) == 8) ? "DNI no valido" : "RUC no valido";
					} elseif (stripos($response, '{"message":"Superaste el limite permitido por tu token"}') !== false) {
						// Mensaje cuando se supera el límite de consultas a la SUNAT
						$data = "Acaba de superar el límite de 1000 consultas a la SUNAT este mes";
					} else {
						// Respuesta válida de la API
						$data = $response;
					}
				} catch (Exception $e) {
					$data = "Error al procesar la solicitud: " . $e->getMessage();
				} finally {
					curl_close($curl);
				}

				echo $data;
				break;
		}
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
