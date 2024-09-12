<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Solicitud
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	public function insertar($idencargado, $codigo_pedido, $telefono, $empresa, $destino, $idarticulo, $cantidad, $precio_compra)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		$sql = "INSERT INTO solicitud (idalmacenero, idencargado, codigo_pedido, telefono, comentario, empresa, destino, fecha_hora_pedido, fecha_hora_despacho, estado)
		VALUES (0, '$idencargado', '$codigo_pedido', '$telefono', '', '$empresa', '$destino', SYSDATE(), '2000-01-01 00:00:00', 'Recibido')";
		$idsolicitudnew = ejecutarConsulta_retornarID($sql);

		$sql2 = "INSERT INTO devolucion (idalmacenero, idencargado, codigo_pedido, telefono, comentario, empresa, destino, fecha_hora_pedido, fecha_hora_devolucion, opcion, estado)
		VALUES (0, '$idencargado', '$codigo_pedido', '$telefono', '', '$empresa', '$destino', SYSDATE(), '2000-01-01 00:00:00', '0', 'Recibido')";
		ejecutarConsulta($sql2);

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			$sql_detalle1 = "INSERT INTO detalle_solicitud(idsolicitud, idarticulo, cantidad, cantidad_prestada, precio_compra) VALUES ('$idsolicitudnew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]',0,'$precio_compra[$num_elementos]')";
			ejecutarConsulta($sql_detalle1) or $sw = false;
			$sql_detalle2 = "INSERT INTO detalle_devolucion(iddevolucion, idarticulo, cantidad, cantidad_prestada, cantidad_devuelta, cantidad_a_devolver, precio_compra, opcion, fecha_hora) VALUES ('$idsolicitudnew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]',0,0,0,'$precio_compra[$num_elementos]',0,SYSDATE())";
			ejecutarConsulta($sql_detalle2) or $sw = false;
			$num_elementos = $num_elementos + 1;
		}

		return $sw;
	}

	public function validarStock($idarticulo, $cantidad)
	{
		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql = "SELECT stock FROM articulo WHERE idarticulo = '$idarticulo[$i]'";
			$res = ejecutarConsultaSimpleFila($sql);
			$stockActual = $res['stock'];
			if ($cantidad[$i] > $stockActual) {
				return true;
			}
		}
		return false;
	}

	public function actualizarSolicitud($idalmacenero, $idsolicitud, $idarticulo, $cantidad_prestada)
	{
		// Primero, debemos verificar si hay suficiente stock a prestar para cada artículo
		$error = $this->validarStockPrestar($idsolicitud, $idarticulo, $cantidad_prestada);

		if ($error) {
			// Si hay un error, no se puede actualizar
			return false;
		}

		$num_elementos = 0;
		$sw = true;

		$this->validarCantidadFinalizada($idalmacenero, $idsolicitud, $idarticulo, $cantidad_prestada);

		while ($num_elementos < count($idarticulo)) {
			$sql_detalle1 = "UPDATE detalle_solicitud SET cantidad_prestada=cantidad_prestada+'$cantidad_prestada[$num_elementos]' WHERE idsolicitud='$idsolicitud' AND idarticulo='$idarticulo[$num_elementos]'";
			ejecutarConsulta($sql_detalle1) or $sw = false;
			$sql_detalle2 = "UPDATE detalle_devolucion SET cantidad_prestada=cantidad_prestada+'$cantidad_prestada[$num_elementos]' WHERE iddevolucion='$idsolicitud' AND idarticulo='$idarticulo[$num_elementos]'";
			ejecutarConsulta($sql_detalle2) or $sw = false;
			$num_elementos = $num_elementos + 1;
		}

		return $sw;
	}

	public function validarCantidadFinalizada($idalmaceneroActual, $idsolicitud, $idarticulo, $cantidad_prestada)
	{
		$todosCumplen = 0; // Variable para rastrear si todos los artículos cumplen la condición
		$estadoDevolucionEnCurso = 0;

		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql1 = "SELECT cantidad, cantidad_prestada FROM detalle_solicitud WHERE idsolicitud='$idsolicitud' AND idarticulo='$idarticulo[$i]'";
			$res1 = ejecutarConsultaSimpleFila($sql1);
			$cantidad = $res1['cantidad'];
			$cantidadPrestadaActual = $res1['cantidad_prestada'];

			if (($cantidad_prestada[$i] + $cantidadPrestadaActual) < $cantidad) {
				$todosCumplen = 1;
			}
		}

		$sql2 = "SELECT estado FROM devolucion WHERE iddevolucion='$idsolicitud'";
		$res2 = ejecutarConsultaSimpleFila($sql2);
		$EstadoDevolucionActual = $res2['estado'];

		if ($EstadoDevolucionActual == "En curso") {
			$estadoDevolucionEnCurso = 1; // comenzó a devolver, su estado es "En curso".
		}

		// $todosCumplen  => Si es "1", alguna de las sumas de la cantidad prestada con la cantidad prestada actual no es igual a la cantidad.
		//				  => Si es "0", todas las sumas de la cantidad prestada con la cantidad prestada actual es igual a la cantidad.

		if ($todosCumplen == 1) {
			$sql3 = "UPDATE solicitud SET idalmacenero='$idalmaceneroActual', estado='Pendiente', fecha_hora_despacho=SYSDATE() WHERE idsolicitud='$idsolicitud'";
		} else {
			$sql3 = "UPDATE solicitud SET idalmacenero='$idalmaceneroActual', estado='Finalizado', fecha_hora_despacho=SYSDATE() WHERE idsolicitud='$idsolicitud'";
		}

		if ($estadoDevolucionEnCurso == 0) {
			$sql4 = "UPDATE devolucion SET idalmacenero='$idalmaceneroActual', estado='Pendiente' WHERE iddevolucion='$idsolicitud'";
		} else {
			$sql4 = "UPDATE devolucion SET idalmacenero='$idalmaceneroActual', estado='En curso' WHERE iddevolucion='$idsolicitud'";
		}

		ejecutarConsulta($sql3);
		ejecutarConsulta($sql4);
	}

	public function obtenerDatosParaPrueba($idalmacenero, $idsolicitud, $idarticulo, $cantidad_prestada)
	{
		$resultados = array();
		$todosCompletos = true;

		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql = "SELECT ds.cantidad, ds.cantidad_prestada, a.nombre AS nombre_articulo 
					FROM detalle_solicitud ds 
					JOIN articulo a ON ds.idarticulo = a.idarticulo 
					WHERE ds.idsolicitud='$idsolicitud' AND ds.idarticulo='$idarticulo[$i]'";
			$res = ejecutarConsultaSimpleFila($sql);

			$cantidadSolicitada = $res['cantidad'];
			$cantidadPrestadaActual = $res['cantidad_prestada'];
			$nombreArticulo = $res['nombre_articulo'];

			if (($cantidadPrestadaActual + $cantidad_prestada[$i]) == $cantidadSolicitada) {
				$todosCompletos = false;
			}

			$resultados[] = array(
				'idarticulo' => $idarticulo[$i],
				'nombre_articulo' => $nombreArticulo,
				'cantidad_solicitada' => $cantidadSolicitada,
				'cantidad_prestada_actual' => $cantidadPrestadaActual,
				'cantidad_prestada_nueva' => $cantidad_prestada[$i],
			);
		}

		$estadoGeneral = $todosCompletos ? "FINALIZADO" : "PENDIENTE";

		return array(
			'idalmacenero' => $idalmacenero,
			'idsolicitud' => $idsolicitud,
			'articulos' => $resultados,
			'todos_completos' => $estadoGeneral
		);
	}

	public function validarStockPrestar($idsolicitud, $idarticulo, $cantidad_prestada)
	{
		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql = "SELECT cantidad, cantidad_prestada FROM detalle_solicitud WHERE idsolicitud='$idsolicitud' AND idarticulo='$idarticulo[$i]'";
			$res = ejecutarConsultaSimpleFila($sql);
			$cantidad = $res['cantidad'];
			$cantidadPrestadaActual = $res['cantidad_prestada'];
			if ($cantidad_prestada[$i] + $cantidadPrestadaActual > $cantidad) {
				return true;
			}
		}
		return false;
	}

	public function verificarCodigoPedidoExiste($codigo_pedido)
	{
		$sql = "SELECT * FROM solicitud WHERE codigo_pedido = '$codigo_pedido'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número ya existe en la tabla
			return true;
		}
		// El número no existe en la tabla
		return false;
	}

	public function anular($idsolicitud)
	{
		$sql1 = "UPDATE solicitud SET estado='Anulado' WHERE idsolicitud='$idsolicitud'";
		ejecutarConsulta($sql1);

		$sql2 = "UPDATE devolucion SET estado='Anulado' WHERE iddevolucion='$idsolicitud'";
		ejecutarConsulta($sql2);

		return true;
	}

	public function activar($idsolicitud)
	{
		$sql1 = "UPDATE solicitud SET estado='Recibido' WHERE idsolicitud='$idsolicitud'";
		ejecutarConsulta($sql1);

		$sql2 = "UPDATE devolucion SET estado='Recibido' WHERE iddevolucion='$idsolicitud'";
		ejecutarConsulta($sql2);

		return true;
	}

	public function rechazar($idsolicitud)
	{
		$sql1 = "UPDATE solicitud SET estado='Rechazado' WHERE idsolicitud='$idsolicitud'";
		ejecutarConsulta($sql1);

		$sql2 = "UPDATE devolucion SET estado='Rechazado' WHERE iddevolucion='$idsolicitud'";
		ejecutarConsulta($sql2);

		return true;
	}

	public function eliminar($idsolicitud)
	{
		$sql1 = "DELETE FROM solicitud WHERE idsolicitud='$idsolicitud'";
		ejecutarConsulta($sql1);

		$sql2 = "DELETE FROM devolucion WHERE iddevolucion='$idsolicitud'";
		ejecutarConsulta($sql2);

		return true;
	}

	public function mostrar($idsolicitud)
	{
		$sql = "SELECT
					s.idsolicitud,
					ual.idusuario AS idalmacenero,
					uen.idusuario AS idencargado,
					s.codigo_pedido,
					s.empresa,
					s.destino,
					s.telefono,
					s.estado
				FROM solicitud s
				LEFT JOIN usuario ual ON s.idalmacenero = ual.idusuario
				LEFT JOIN usuario uen ON s.idencargado = uen.idusuario
				WHERE s.idsolicitud='$idsolicitud'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT
					s.idsolicitud,
					s.idalmacenero,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					s.codigo_pedido,
					s.empresa,
					s.destino,
					s.telefono,
					DATE_FORMAT(s.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(s.fecha_hora_despacho, '%d-%m-%Y %H:%i:%s') AS fecha_hora_despacho,
					s.estado,
					d.estado as estado_devolucion
				FROM solicitud s
				LEFT JOIN usuario uen ON s.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON s.idalmacenero = ual.idusuario
				LEFT JOIN devolucion d ON s.idsolicitud = d.iddevolucion
				ORDER BY s.idsolicitud DESC";

		return ejecutarConsulta($sql);
	}

	public function listarUsuario($idencargado)
	{
		$sql = "SELECT
					s.idsolicitud,
					s.idalmacenero,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					s.codigo_pedido,
					s.empresa,
					s.destino,
					s.telefono,
					DATE_FORMAT(s.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(s.fecha_hora_despacho, '%d-%m-%Y %H:%i:%s') AS fecha_hora_despacho,
					s.estado,
					d.estado as estado_devolucion
				FROM solicitud s
				LEFT JOIN usuario uen ON s.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON s.idalmacenero = ual.idusuario
				LEFT JOIN devolucion d ON s.idsolicitud = d.iddevolucion
				WHERE s.idencargado = '$idencargado' 
				ORDER BY s.idsolicitud DESC";

		return ejecutarConsulta($sql);
	}


	public function actualizarComentario($idsolicitud, $comentario)
	{
		$sql = "UPDATE solicitud SET comentario='$comentario' WHERE idsolicitud='$idsolicitud'";
		return ejecutarConsulta($sql);
	}

	public function mostrarComentario($idsolicitud)
	{
		$sql = "SELECT idsolicitud, comentario FROM solicitud WHERE idsolicitud='$idsolicitud'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($idsolicitud)
	{
		$sql = "SELECT ds.idsolicitud,
					ds.idarticulo,
					a.nombre,
					c.titulo as categoria,
					m.titulo as marca,
					al.titulo as local,
					ds.precio_compra,
					ds.cantidad,
					ds.cantidad_prestada
				FROM detalle_solicitud ds
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				LEFT JOIN marcas m ON a.idmarca = m.idmarca
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN locales al ON a.idlocal = al.idlocal
				WHERE ds.idsolicitud = '$idsolicitud'";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle2($idsolicitud)
	{
		$sql = "SELECT ds.idsolicitud,
					ds.idarticulo,
					a.nombre,
					c.titulo as categoria,
					m.titulo as marca,
					al.titulo as local,
					ds.precio_compra,
					ds.cantidad,
					ds.cantidad_prestada
				FROM detalle_solicitud ds
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				LEFT JOIN marcas m ON a.idmarca = m.idmarca
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN locales al ON a.idlocal = al.idlocal
				WHERE ds.idsolicitud = '$idsolicitud'";
		return ejecutarConsulta($sql);
	}

	public function solicitudcabecera($idsolicitud)
	{
		$sql = "SELECT
					s.idsolicitud,
					s.idalmacenero,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					uen.direccion AS direccion_pedido, uen.tipo_documento AS tipo_documento_pedido, uen.num_documento AS num_documento_pedido, uen.email AS email_pedido, uen.telefono AS telefono_pedido,
					ual.direccion AS direccion_despacho, ual.tipo_documento AS tipo_documento_despacho, ual.num_documento AS num_documento_despacho, ual.email AS email_despacho, ual.telefono AS telefono_despacho,
					s.codigo_pedido,
					s.empresa,
					s.destino,
					s.telefono,
					s.estado,
					DATE_FORMAT(s.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(s.fecha_hora_despacho, '%d-%m-%Y %H:%i:%s') AS fecha_hora_despacho
				FROM solicitud s
				LEFT JOIN usuario uen ON s.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON s.idalmacenero = ual.idusuario
				WHERE s.idsolicitud = '$idsolicitud'";
		return ejecutarConsulta($sql);
	}

	public function solicituddetalle($idsolicitud)
	{
		$sql = "SELECT
					ds.idsolicitud,
					ds.idarticulo,
					a.nombre,
					a.codigo_producto,
					ds.cantidad_prestada,
					ds.precio_compra,
					ds.cantidad
				FROM detalle_solicitud ds
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				WHERE ds.idsolicitud='$idsolicitud'";
		return ejecutarConsulta($sql);
	}

	public function getLastCodigoPedido()
	{
		$sql = "SELECT MAX(codigo_pedido) AS codigo_pedido FROM solicitud";
		return ejecutarConsulta($sql);
	}

	public function listarSelectAlmacenero()
	{
		$sql = "SELECT idusuario, nombre, cargo FROM usuario ORDER BY idusuario DESC";
		return ejecutarConsulta($sql);
	}
}
