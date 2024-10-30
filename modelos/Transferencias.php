<?php
require "../config/Conexion.php";

class Transferencia
{
	public function __construct() {}

	public function agregar($origen, $destino, $idusuario, $codigo, $lugar_destino, $comentario, $idarticulo, $cantidad)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		date_default_timezone_set("America/Lima");

		// Inserta la transferencia en la tabla `transferencias`
		$sql = "INSERT INTO transferencias (origen, destino, idusuario, codigo, lugar_destino, comentario, fecha_hora, estado)
				VALUES ('$origen','$destino','$idusuario','$codigo','$lugar_destino','$comentario', SYSDATE(),'activado')";
		$idtransferencianew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;
		$sw = true;

		// Iteramos sobre todos los artículos transferidos
		while ($num_elementos < count($idarticulo)) {
			// Insertamos el detalle de la transferencia inicialmente sin `idarticulo_transferido`
			$sql_detalle = "INSERT INTO detalle_transferencia (idtransferencia, idarticulo, cantidad) 
							VALUES ('$idtransferencianew', '$idarticulo[$num_elementos]', '$cantidad[$num_elementos]')";
			$iddetalletransferencia = ejecutarConsulta_retornarID($sql_detalle);

			// Solo si el origen y destino son diferentes realizamos las operaciones adicionales
			if ($origen != $destino) {
				// Actualizamos el stock del local de origen
				$sql_actualizar_stock_origen = "UPDATE articulo SET stock = stock - '$cantidad[$num_elementos]' WHERE idarticulo = '$idarticulo[$num_elementos]' AND idlocal = '$origen'";
				ejecutarConsulta($sql_actualizar_stock_origen) or $sw = false;

				// Obtenemos los datos del artículo actual para duplicarlo en el local de destino
				$sql_datos_articulo = "SELECT * FROM articulo WHERE idarticulo = '$idarticulo[$num_elementos]' LIMIT 1";
				$articulo_datos = ejecutarConsultaSimpleFila($sql_datos_articulo);

				if ($articulo_datos) {
					// Creamos el nuevo artículo en el local de destino con el stock transferido
					$sql_insertar_articulo_destino = "INSERT INTO articulo 
													  (idusuario, idcategoria, idlocal, idmarca, idmedida, idubicacion, codigo, codigo_producto, nombre, stock, stock_minimo, precio_compra, 
													   precio_compra_mayor, descripcion, talla, color, peso, imagen, imei, serial, nota_1, nota_2, fecha_emision, 
													   fecha_vencimiento, fecha_hora, estado, eliminado) 
													  VALUES 
													  ('" . $articulo_datos['idusuario'] . "', 
													   '" . $articulo_datos['idcategoria'] . "', 
													   '$destino', 
													   '" . $articulo_datos['idmarca'] . "', 
													   '" . $articulo_datos['idmedida'] . "', 
													   '" . $articulo_datos['idubicacion'] . "', 
													   '" . $articulo_datos['codigo'] . "', 
													   '" . $articulo_datos['codigo_producto'] . "', 
													   '" . $articulo_datos['nombre'] . "', 
													   '" . $cantidad[$num_elementos] . "', 
													   '" . $articulo_datos['stock_minimo'] . "', 
													   '" . $articulo_datos['precio_compra'] . "', 
													   '" . $articulo_datos['precio_compra_mayor'] . "', 
													   '" . $articulo_datos['descripcion'] . "', 
													   '" . $articulo_datos['talla'] . "', 
													   '" . $articulo_datos['color'] . "', 
													   '" . $articulo_datos['peso'] . "', 
													   '" . $articulo_datos['imagen'] . "', 
													   '" . $articulo_datos['imei'] . "', 
													   '" . $articulo_datos['serial'] . "', 
													   '" . $articulo_datos['nota_1'] . "', 
													   '" . $articulo_datos['nota_2'] . "', 
													   '" . $articulo_datos['fecha_emision'] . "', 
													   '" . $articulo_datos['fecha_vencimiento'] . "', 
													   SYSDATE(), 
													   '" . $articulo_datos['estado'] . "', 
													   '" . $articulo_datos['eliminado'] . "')";
					$idarticulotransferido = ejecutarConsulta_retornarID($sql_insertar_articulo_destino);

					// Actualizamos la columna `idarticulo_transferido` en `detalle_transferencia`
					$sql_actualizar_detalle = "UPDATE detalle_transferencia 
											   SET idarticulo_transferido = '$idarticulotransferido' 
											   WHERE iddetalle_transferencia = '$iddetalletransferencia'";
					ejecutarConsulta($sql_actualizar_detalle) or $sw = false;
				}
			}

			$num_elementos++;
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

	public function desactivar($idtransferencia)
	{
		$sql = "UPDATE transferencias SET estado='desactivado' WHERE idtransferencia='$idtransferencia'";
		return ejecutarConsulta($sql);
	}

	public function activar($idtransferencia)
	{
		$sql = "UPDATE transferencias SET estado='activado' WHERE idtransferencia='$idtransferencia'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idtransferencia)
	{
		// Obtener los detalles de la transferencia
		$sql_detalles = "SELECT idarticulo, idarticulo_transferido, cantidad 
						 FROM detalle_transferencia 
						 WHERE idtransferencia = '$idtransferencia'";
		$detalles = ejecutarConsulta($sql_detalles);

		// Procesar cada artículo en el detalle de la transferencia
		while ($detalle = $detalles->fetch_object()) {
			// 1. Actualizar el stock del artículo original (idarticulo)
			$sql_actualizar_stock_origen = "UPDATE articulo SET stock = stock + '$detalle->cantidad' WHERE idarticulo = '$detalle->idarticulo'";
			ejecutarConsulta($sql_actualizar_stock_origen);

			// 2. Cambiar el estado del artículo transferido (idarticulo_transferido) a eliminado (1)
			if ($detalle->idarticulo_transferido) {
				$sql_eliminar_articulo_transferido = "UPDATE articulo SET eliminado = 1 WHERE idarticulo = '$detalle->idarticulo_transferido'";
				ejecutarConsulta($sql_eliminar_articulo_transferido);
			}
		}

		// 3. Eliminar la transferencia
		$sql_eliminar_transferencia = "DELETE FROM transferencias WHERE idtransferencia = '$idtransferencia'";
		ejecutarConsulta($sql_eliminar_transferencia);

		// 4. Eliminar los detalles de la transferencia
		$sql_eliminar_detalle = "DELETE FROM detalle_transferencia WHERE idtransferencia = '$idtransferencia'";
		return ejecutarConsulta($sql_eliminar_detalle);
	}

	public function limpiar($idtransferencia)
	{
		$sql = "DELETE FROM transferencias WHERE idtransferencia='$idtransferencia'";
		ejecutarConsulta($sql);
		$sql2 = "DELETE FROM detalle_transferencia WHERE idtransferencia='$idtransferencia'";
		return ejecutarConsulta($sql2);
	}

	public function mostrar($idtransferencia)
	{
		$sql = "SELECT * FROM transferencias WHERE idtransferencia='$idtransferencia'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT tr.idtransferencia,tr.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,loo.titulo as origen,lod.titulo as destino,tr.lugar_destino,tr.codigo,tr.comentario,DATE_FORMAT(tr.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,tr.estado,SUM(dtr.cantidad) as total_cantidad FROM transferencias tr LEFT JOIN locales loo ON tr.origen = loo.idlocal LEFT JOIN locales lod ON tr.destino = lod.idlocal LEFT JOIN detalle_transferencia dtr ON tr.idtransferencia = dtr.idtransferencia LEFT JOIN usuario u ON tr.idusuario=u.idusuario GROUP BY tr.idtransferencia ORDER BY tr.idtransferencia DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT tr.idtransferencia,tr.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,loo.titulo as origen,lod.titulo as destino,tr.lugar_destino,tr.codigo,tr.comentario,DATE_FORMAT(tr.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,tr.estado,SUM(dtr.cantidad) as total_cantidad FROM transferencias tr LEFT JOIN locales loo ON tr.origen = loo.idlocal LEFT JOIN locales lod ON tr.destino = lod.idlocal LEFT JOIN detalle_transferencia dtr ON tr.idtransferencia = dtr.idtransferencia LEFT JOIN usuario u ON tr.idusuario=u.idusuario WHERE DATE(tr.fecha_hora) >= '$fecha_inicio' AND DATE(tr.fecha_hora) <= '$fecha_fin' GROUP BY tr.idtransferencia ORDER BY tr.idtransferencia DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT tr.idtransferencia,tr.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,loo.titulo as origen,lod.titulo as destino,tr.lugar_destino,tr.codigo,tr.comentario,DATE_FORMAT(tr.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,tr.estado,SUM(dtr.cantidad) as total_cantidad FROM transferencias tr LEFT JOIN locales loo ON tr.origen = loo.idlocal LEFT JOIN locales lod ON tr.destino = lod.idlocal LEFT JOIN detalle_transferencia dtr ON tr.idtransferencia = dtr.idtransferencia LEFT JOIN usuario u ON tr.idusuario=u.idusuario WHERE tr.origen = '$idlocalSession' GROUP BY tr.idtransferencia ORDER BY tr.idtransferencia DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT tr.idtransferencia,tr.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,loo.titulo as origen,lod.titulo as destino,tr.lugar_destino,tr.codigo,tr.comentario,DATE_FORMAT(tr.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,tr.estado,SUM(dtr.cantidad) as total_cantidad FROM transferencias tr LEFT JOIN locales loo ON tr.origen = loo.idlocal LEFT JOIN locales lod ON tr.destino = lod.idlocal LEFT JOIN detalle_transferencia dtr ON tr.idtransferencia = dtr.idtransferencia LEFT JOIN usuario u ON tr.idusuario=u.idusuario WHERE tr.origen = '$idlocalSession' AND DATE(tr.fecha_hora) >= '$fecha_inicio' AND DATE(tr.fecha_hora) <= '$fecha_fin' GROUP BY tr.idtransferencia ORDER BY tr.idtransferencia DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($idtransferencia)
	{
		$sql = "SELECT dtr.idtransferencia, dtr.idarticulo, a.nombre AS articulo, c.titulo AS categoria, ma.titulo AS marca, me.titulo AS medida, l.titulo AS local, a.codigo, a.codigo_producto, a.stock, a.stock_minimo, a.imagen, a.precio_compra, dtr.cantidad
				FROM detalle_transferencia dtr
				LEFT JOIN articulo a ON dtr.idarticulo = a.idarticulo
				LEFT JOIN locales l ON l.idlocal = a.idlocal
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN marcas ma ON a.idmarca = ma.idmarca
				LEFT JOIN medidas me ON a.idmedida = me.idmedida
				WHERE dtr.idtransferencia = '$idtransferencia'";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'origen' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
				UNION ALL
				SELECT 'destino' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario, $idlocal)
	{
		$sql = "SELECT 'origen' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
				UNION ALL
				SELECT 'destino' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'";

		return ejecutarConsulta($sql);
	}
}
