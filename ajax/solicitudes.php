<?php
ob_start();
if (strlen(session_id()) < 1) {
  session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
  header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
  //Validamos el acceso solo al usuario logueado y autorizado.
  if ($_SESSION['solicitud'] == 1) {
    require_once "../modelos/Solicitudes.php";

    $solicitud = new Solicitud();

    $idencargado = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    $idsolicitud = isset($_POST["idsolicitud"]) ? limpiarCadena($_POST["idsolicitud"]) : "";
    $idalmacenero = isset($_POST["idalmacenero"]) ? limpiarCadena($_POST["idalmacenero"]) : "";
    $idalmaceneroActual = $_SESSION["idusuario"];
    $codigo_pedido = isset($_POST["codigo_pedido"]) ? limpiarCadena($_POST["codigo_pedido"]) : "";
    $telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
    $comentario = isset($_POST["comentario"]) ? limpiarCadena($_POST["comentario"]) : "";
    $empresa = isset($_POST["empresa"]) ? limpiarCadena($_POST["empresa"]) : "";

    switch ($_GET["op"]) {
      case 'guardaryeditar':
        if (empty($idsolicitud)) {
          $codigoPedidoExiste = $solicitud->verificarCodigoPedidoExiste($codigo_pedido);
          if ($codigoPedidoExiste) {
            echo "El número correlativo que ha ingresado ya existe.";
          } else {
            $rspta = $solicitud->insertar($idencargado, $codigo_pedido, $telefono, $empresa, $_POST["idarticulo"], $_POST["cantidad"]);
            echo $rspta ? "Solicitud registrada" : "Una de las cantidades superan a la cantidad o stock del artículo.";
          }
        } else {
        }
        break;

      case 'actualizarSolicitud':
        $rspta = $solicitud->actualizarSolicitud($idalmaceneroActual, $idsolicitud, $_POST["idarticulo"], $_POST["cantidad_prestada"]);
        echo $rspta ? "Préstamos de artículos actualizados correctamente" : "Una de las cantidades a prestar superan a la cantidad solicitada del artículo.";
        break;

      case 'anular':
        $rspta = $solicitud->anular($idsolicitud);
        echo $rspta ? "Solicitud anulada" : "Solicitud no se puede anular";
        break;

      case 'activar':
        $rspta = $solicitud->activar($idsolicitud);
        echo $rspta ? "Solicitud activada" : "Solicitud no se puede activar";
        break;

      case 'rechazar':
        $rspta = $solicitud->rechazar($idsolicitud);
        echo $rspta ? "Solicitud rechazada" : "Solicitud no se puede rechazar";
        break;

      case 'mostrar':
        $rspta = $solicitud->mostrar($idsolicitud);
        echo json_encode($rspta);
        break;

      case 'eliminar':
        $rspta = $solicitud->eliminar($idsolicitud);
        echo $rspta ? "Solicitud eliminada" : "Solicitud no se puede eliminar";
        break;

      case 'guardaryeditarcomentario':
        $rspta = $solicitud->actualizarComentario($idsolicitud, $comentario);
        echo $rspta ? "Comentario registrado" : "Comentario no se puede registrar";
        break;

      case 'mostrarComentario':
        if (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin') || ($_SESSION['cargo'] == 'almacenero')) {
          $rspta = $solicitud->mostrarComentario($idsolicitud);
          $rspta['script'] = '
						<script>
							$("#comentario").attr("placeholder", "Ingrese un comentario.");
							$("#btnGuardar2").show();
							$("#comentario").prop("disabled", false);
						</script>
					';
          echo json_encode($rspta);
        } else {
          $rspta = $solicitud->mostrarComentario($idsolicitud);
          $rspta['script'] = '<script>$("#comentario").attr("placeholder", "Sin comentarios.");</script>';
          echo json_encode($rspta);
        }
        break;

      case 'listarDetalle':
        //Recibimos el idingreso
        $id = $_GET['id'];

        $rspta = $solicitud->listarDetalle($id);
        $total = 0;

        $estado = '';

        echo '<thead style="background-color:#A9D0F5">
                                    <th>Opciones</th>
                                    <th>Artículo</th>
                                    <th>Categoría</th>
                                    <th>Marca</th>
									<th>Local</th>
                                    <th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
                                    <th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el almacenero prestó." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
									<th>Estado</th>
                                </thead>';

        while ($reg = $rspta->fetch_object()) {
          if ($reg->cantidad == $reg->cantidad_prestada) {
            $estado = '<span class="label bg-green">Completado</span>';
          } else {
            $estado = '<span class="label bg-orange">Incompleto</span>';
          }

          echo '<tr class="filas"><td></td><td>' . $reg->nombre . '</td><td>' . $reg->categoria . '</td><td>' . $reg->marca . '</td><td>' . $reg->local . '</td><td>' . $reg->cantidad . '</td><td>' . $reg->cantidad_prestada . '</td><td>' . $estado . '</td></tr>';
        }
        break;

      case 'listarDetalle2':
        //Recibimos el idingreso
        $id = $_GET['id'];

        $rspta = $solicitud->listarDetalle2($id);
        $total = 0;

        $estado = '';

        echo '<thead style="background-color:#A9D0F5">
										<th>Opciones</th>
										<th>Artículo</th>
										<th>Categoría</th>
										<th>Marca</th>
										<th>Local</th>
										<th>Cantidad Solicitada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Solicitada" data-content="Es la cantidad solicitada a prestar." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
										<th>Cantidad Prestada <a href="#" data-toggle="popover" data-placement="top" title="Cantidad Prestada" data-content="Es la cantidad que el almacenero prestó." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
										<th>Cantidad a Prestar <a href="#" data-toggle="popover" data-placement="top" title="Cantidad a Prestar" data-content="Digita la cantidad que deseas prestar al encargado (no debe superar la cantidad solicitada)." style="color: #002a8e"><i class="fa fa-question-circle"></i></a></th>
										<th>Estado</th>
									</thead>';

        $iterador = 1;
        while ($reg = $rspta->fetch_object()) {
          if ($reg->cantidad == $reg->cantidad_prestada) {
            $estado = '<span class="label bg-green">Completado</span>';
          } else {
            $estado = '<span class="label bg-orange">Incompleto</span>';
          }

          echo '<tr class="filas"><td></td><td><input type="hidden" name="idarticulo[]" value="' . $reg->idarticulo . '">' . $reg->nombre . '</td><td>' . $reg->categoria . '</td><td>' . $reg->marca . '</td><td>' . $reg->local . '</td><td data-cantidadsolicitada="' . $iterador . '">' . $reg->cantidad . '</td><td data-cantidadprestada="' . $iterador . '">' . $reg->cantidad_prestada . '</td><td><input type="number" data-cantidadprestar="' . $iterador . '" name="cantidad_prestada[]" id="cantidad_prestada[]" step="any" value="0" min="0.1" required></td><td>' . $estado . '</td></tr>';
          $iterador = $iterador + 1;
        }
        break;

      case 'listar':
        if (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero'))
          $rspta = $solicitud->listar();
        else
          $rspta = $solicitud->listarUsuario($idencargado);
        //Vamos a declarar un array
        $data = array();

        $url = '../reportes/exFacturaSolicitud.php?id=';

        while ($reg = $rspta->fetch_object()) {
          $cargo_pedido = "";

          switch ($reg->cargo_pedido) {
            case 'superadmin':
              $cargo_pedido = "Superadministrador";
              break;
            case 'admin':
              $cargo_pedido = "Administrador";
              break;
            case 'usuario':
              $cargo_pedido = "Usuario";
              break;
            case 'mirador':
              $cargo_pedido = "Mirador";
              break;
            case 'almacenero':
              $cargo_pedido = "Almacenero";
              break;
            case 'encargado':
              $cargo_pedido = "Encargado";
              break;
            default:
              break;
          }

          $data[] = array(
            "0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
              (($reg->estado == 'Recibido') ?
                ('<a data-toggle="modal" href="#myModal2" title="Mirar detalles de solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar(' . $reg->idsolicitud . ')"><i class="fa fa-eye"></i></button></a>' .
                  (($_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Anular solicitud" style="color: black" onclick="anular(' . $reg->idsolicitud . ')"><i class="fa fa-close"></i></button>') : '') .
                  (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero') ? ('<button class="btn btn-secondary" title="Rechazar solicitud" style="color: black" onclick="rechazar(' . $reg->idsolicitud . ')"><i class="fa fa-times-circle"></i></button>') : '') .
                  (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Eliminar solicitud" style="color: black" onclick="eliminar(' . $reg->idsolicitud . ')"><i class="fa fa-trash"></i></button>') : '') .
                  (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado' || $_SESSION['cargo'] == 'almacenero') ? ('<a data-toggle="modal" href="#myModal3" title="Enviar comentario" style="color: black"><button class="btn btn-secondary" onclick="mostrarComentario(' . $reg->idsolicitud . ')"><i class="fa fa-commenting"></i></button></a>') : '') .
                  (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero') ? ('<a data-toggle="modal" href="#myModal4" title="Aceptar solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar2(' . $reg->idsolicitud . ')"><i class="fa fa-retweet"></i></button></a>') : '') .
                  '<a target="_blank" href="' . $url . $reg->idsolicitud . '"> <button class="btn btn-secondary" style="color: black;"><i class="fa fa-file"></i></button></a>')
                : (($reg->estado == 'Pendiente') ?
                  (('<a data-toggle="modal" href="#myModal2" title="Mirar detalles de solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar(' . $reg->idsolicitud . ')"><i class="fa fa-eye"></i></button></a>' .
                    (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero') ? ('<button class="btn btn-secondary" title="Rechazar solicitud" style="color: black" onclick="rechazar(' . $reg->idsolicitud . ')"><i class="fa fa-times-circle"></i></button>') : '') .
                    (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Eliminar solicitud" style="color: black" onclick="eliminar(' . $reg->idsolicitud . ')"><i class="fa fa-trash"></i></button>') : '') .
                    (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado' || $_SESSION['cargo'] == 'almacenero') ? ('<a data-toggle="modal" href="#myModal3" title="Enviar comentario" style="color: black"><button class="btn btn-secondary" onclick="mostrarComentario(' . $reg->idsolicitud . ')"><i class="fa fa-commenting"></i></button></a>') : '') .
                    (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero') ? ('<a data-toggle="modal" href="#myModal4" title="Aceptar solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar2(' . $reg->idsolicitud . ')"><i class="fa fa-retweet"></i></button></a>') : '') .
                    '<a target="_blank" href="' . $url . $reg->idsolicitud . '"> <button class="btn btn-secondary" style="color: black;"><i class="fa fa-file"></i></button></a>'))
                  : (($reg->estado == 'Finalizado' || $reg->estado == 'Rechazado') ?
                    ('<a data-toggle="modal" href="#myModal2" title="Mirar detalles de solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar(' . $reg->idsolicitud . ')"><i class="fa fa-eye"></i></button></a>' .
                      (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado' || $_SESSION['cargo'] == 'almacenero') ? ('<a data-toggle="modal" href="#myModal3" title="Enviar comentario" style="color: black"><button class="btn btn-secondary" onclick="mostrarComentario(' . $reg->idsolicitud . ')"><i class="fa fa-commenting"></i></button></a>') : '') .
                      (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Eliminar solicitud" style="color: black" onclick="eliminar(' . $reg->idsolicitud . ')"><i class="fa fa-trash"></i></button>') .
                        '<a target="_blank" href="' . $url . $reg->idsolicitud . '"> <button class="btn btn-secondary" style="color: black;"><i class="fa fa-file"></i></button></a>' : ''))
                    : ('<a data-toggle="modal" href="#myModal2" title="Mirar detalles de solicitud" style="color: black"><button class="btn btn-secondary" onclick="mostrar(' . $reg->idsolicitud . ')"><i class="fa fa-eye"></i></button></a>' .
                      (($_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Activar solicitud" style="color: black; width: 36px" onclick="activar(' . $reg->idsolicitud . ')"><i class="fa fa-check"></i></button>') : '') .
                      (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'encargado') ? ('<button class="btn btn-secondary" title="Eliminar solicitud" style="color: black" onclick="eliminar(' . $reg->idsolicitud . ')"><i class="fa fa-trash"></i></button>') : '')) . '</div>'))),
            "1" => "N° " . $reg->codigo_pedido,
            "2" => $reg->fecha_hora_pedido,
            "3" => ($reg->fecha_hora_despacho == "01-01-2000 00:00:00") ? "Sin registrar" : $reg->fecha_hora_despacho,
            "4" => ucwords($reg->responsable_pedido) . " - " . $cargo_pedido,
            "5" => ($reg->idalmacenero == 0 || $reg->idalmacenero == "0") ? "Sin registrar" : ucwords($reg->responsable_despacho) . " - " . $reg->cargo_despacho,
            "6" => $reg->empresa,
            "7" => $reg->telefono,
            "8" => ($reg->estado == 'Recibido') ? (($_SESSION['cargo'] == 'superadmin') || ($_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero') ? '<span class="label bg-blue">Recibido</span>' : '<span class="label bg-blue">Enviado</span>') : (($reg->estado == 'Pendiente') ? '<span class="label bg-orange">Pendiente</span>' : (($reg->estado == 'Finalizado') ? '<span class="label bg-green">Finalizado</span>' : (($reg->estado == 'Rechazado') ? '<span class="label bg-red">Rechazado</span>' : '<span class="label bg-red">Anulado</span>')))
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

      case 'selectAlmacenero':
        $rspta = $solicitud->listarSelectAlmacenero();
        echo '<option value="">- Sin registrar -</option>';
        while ($reg = $rspta->fetch_object()) {
          echo '<option value=' . $reg->idusuario . '>' . $reg->nombre . ' - ' . $reg->cargo . '</option>';
        }
        break;

      case 'listarArticulosSolicitud':
        require_once "../modelos/Articulo.php";
        $articulo = new Articulo();

        $rspta = $articulo->listar();
        //Vamos a declarar un array
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
            "0" => ($reg->stock != '0') ? '<div style="display: flex; justify-content: center;"><button class="btn btn-warning" style="height: 35px;" data-idarticulo="' . $reg->idarticulo . '" onclick="agregarDetalle(\'' . $reg->marca . '\',\'' . $reg->local . '\',\'' . $reg->categoria . '\',\'' . $reg->idarticulo . '\',\'' . $reg->stock . '\',\'' . $reg->nombre . '\'); disableButton(this);"><span class="fa fa-plus"></span></button></div>' : '',
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

      case 'getLastCodigoPedido':
        $result = $solicitud->getLastCodigoPedido();
        if (mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $codigo_pedido = $row["codigo_pedido"];
        } else {
          $codigo_pedido = 0;
        }
        echo $codigo_pedido;
        break;

      case 'verificarStockMinimo':
        $idarticulo = $_GET['id'];
        $nombre = $_GET['nombre'];
        $cantidad = $_GET['cantidad'];

        require_once "../modelos/Articulo.php";
        $articulo = new Articulo();
        // saber su stock mínimo
        $row = mysqli_fetch_assoc($articulo->identificarStockMinimo($idarticulo));
        $stock_minimo = $row['stock_minimo'];

        // validar su stock mínimo
        $validar_stock_minimo = $articulo->verificarStockMinimo($idarticulo, $cantidad);

        if ($validar_stock_minimo) {
          echo "La cantidad de tu artículo <strong>" . $nombre . "</strong> es menor a su stock mínimo, que es <strong>" . $stock_minimo . "</strong>.";
        } else {
        }
        break;
    }
    //Fin de las validaciones de acceso
  } else {
    require 'noacceso.php';
  }
}
ob_end_flush();