<?php
require "../config/Conexion.php";

class Salida
{
	public function __construct() {}

	public function agregar($idlocal, $idusuario, $idtipo, $tipo_movimiento, $idactivo, $idautorizado, $idrecibido, $idubicacion, $codigo, $origen, $destino, $num_guia, $descripcion, $impuesto, $total_compra, $idarticulo, $cantidad, $precio_compra)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		date_default_timezone_set("America/Lima");
		// Si no hay errores, continuamos con el registro de la entrada
		$sql = "INSERT INTO salidas (idlocal,idusuario,idtipo,tipo_movimiento,idactivo,idautorizado,idrecibido,idubicacion,codigo,origen,destino,num_guia,descripcion,fecha_hora,impuesto,total_compra,estado)
				VALUES ('$idlocal','$idusuario','$idtipo','$tipo_movimiento','$idactivo','$idautorizado','$idrecibido','$idubicacion','$codigo','$origen','$destino','$num_guia','$descripcion', SYSDATE(),'$impuesto','$total_compra','activado')";
		$idsalidanew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			// Consulta para obtener el valor del campo 'titulo' de la tabla 'medidas' para el artículo actual
			$sql_medida = "SELECT m.titulo FROM medidas m INNER JOIN articulo a ON a.idmedida = m.idmedida WHERE a.idarticulo = '$idarticulo[$num_elementos]'";
			$resultado = ejecutarConsultaSimpleFila($sql_medida);

			// if ($resultado['titulo'] == 'Paquetes') {
			$actualizar_art = "UPDATE articulo SET precio_compra_mayor='$precio_compra[$num_elementos]' WHERE idarticulo='$idarticulo[$num_elementos]'";
			// } else {
			// $actualizar_art = "UPDATE articulo SET precio_compra='$precio_compra[$num_elementos]' WHERE idarticulo='$idarticulo[$num_elementos]'";
			// }

			ejecutarConsulta($actualizar_art) or $sw = false;

			// Inserción del detalle de salida
			$sql_detalle = "INSERT INTO detalle_salida(idsalida,idarticulo,cantidad,precio_compra) 
							VALUES ('$idsalidanew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_compra[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;

			$num_elementos++;
		}

		return $sw;
	}


	public function verificarCodigo($codigo, $idlocal)
	{
		$sql = "SELECT * FROM salidas WHERE codigo = '$codigo' AND idlocal = '$idlocal'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El codigo ya existe en la tabla
			return true;
		}
		// El codigo no existe en la tabla
		return false;
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

	public function desactivar($idsalida)
	{
		$sql = "UPDATE salidas SET estado='desactivado' WHERE idsalida='$idsalida'";
		return ejecutarConsulta($sql);
	}

	public function activar($idsalida)
	{
		$sql = "UPDATE salidas SET estado='activado' WHERE idsalida='$idsalida'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idsalida)
	{
		$sql = "DELETE FROM salidas WHERE idsalida='$idsalida'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idsalida)
	{
		$sql = "SELECT * FROM salidas WHERE idsalida='$idsalida'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, lo.imagen as local_imagen, t.titulo as tipo,ubi.titulo as ubicacion,pea.nombre AS autorizado, per.idpersonal AS idpersonal_recibido, per.nombre AS recibido,per.tipo_documento AS tipo_documento_recibido, per.num_documento AS num_documento_recibido,s.codigo,s.num_guia,s.origen,s.destino,s.tipo_movimiento,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.total_compra,s.impuesto,s.estado,SUM(ds.cantidad) as total_cantidad FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN activos ma ON s.idactivo=ma.idactivo LEFT JOIN ubicaciones ubi ON s.idubicacion=ubi.idubicacion LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN detalle_salida ds ON s.idsalida = ds.idsalida LEFT JOIN usuario u ON s.idusuario=u.idusuario GROUP BY s.idsalida ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, lo.imagen as local_imagen, t.titulo as tipo,ubi.titulo as ubicacion,pea.nombre AS autorizado, per.idpersonal AS idpersonal_recibido, per.nombre AS recibido,per.tipo_documento AS tipo_documento_recibido, per.num_documento AS num_documento_recibido,s.codigo,s.num_guia,s.origen,s.destino,s.tipo_movimiento,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.total_compra,s.impuesto,s.estado,SUM(ds.cantidad) as total_cantidad FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN activos ma ON s.idactivo=ma.idactivo LEFT JOIN ubicaciones ubi ON s.idubicacion=ubi.idubicacion LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN detalle_salida ds ON s.idsalida = ds.idsalida LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' GROUP BY s.idsalida ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, lo.imagen as local_imagen, t.titulo as tipo,ubi.titulo as ubicacion,pea.nombre AS autorizado, per.idpersonal AS idpersonal_recibido, per.nombre AS recibido,per.tipo_documento AS tipo_documento_recibido, per.num_documento AS num_documento_recibido,s.codigo,s.num_guia,s.origen,s.destino,s.tipo_movimiento,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.total_compra,s.impuesto,s.estado,SUM(ds.cantidad) as total_cantidad FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN activos ma ON s.idactivo=ma.idactivo LEFT JOIN ubicaciones ubi ON s.idubicacion=ubi.idubicacion LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN detalle_salida ds ON s.idsalida = ds.idsalida LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE s.idlocal = '$idlocalSession' GROUP BY s.idsalida ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, lo.imagen as local_imagen, t.titulo as tipo,ubi.titulo as ubicacion,pea.nombre AS autorizado, per.idpersonal AS idpersonal_recibido, per.nombre AS recibido,per.tipo_documento AS tipo_documento_recibido, per.num_documento AS num_documento_recibido,s.codigo,s.num_guia,s.origen,s.destino,s.tipo_movimiento,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.total_compra,s.impuesto,s.estado,SUM(ds.cantidad) as total_cantidad FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN activos ma ON s.idactivo=ma.idactivo LEFT JOIN ubicaciones ubi ON s.idubicacion=ubi.idubicacion LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN detalle_salida ds ON s.idsalida = ds.idsalida LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE s.idlocal = '$idlocalSession' AND DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' GROUP BY s.idsalida ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCabecera($idsalida)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,u.tipo_documento, u.num_documento, u.direccion, u.email, u.direccion, u.telefono, lo.titulo as local, lo.imagen as local_imagen, t.titulo as tipo,ubi.titulo as ubicacion,pea.nombre AS autorizado, per.idpersonal AS idpersonal_recibido, per.nombre AS recibido,per.tipo_documento AS tipo_documento_recibido, per.num_documento AS num_documento_recibido,ma.titulo AS activo, ma.descripcion,s.codigo,s.num_guia,s.origen,s.destino,s.tipo_movimiento,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.total_compra,s.impuesto,s.estado,SUM(ds.cantidad) as total_cantidad FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN activos ma ON s.idactivo=ma.idactivo LEFT JOIN ubicaciones ubi ON s.idubicacion=ubi.idubicacion LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN detalle_salida ds ON s.idsalida = ds.idsalida LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE s.idsalida = '$idsalida' GROUP BY s.idsalida ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($idsalida)
	{
		$sql = "SELECT ds.idsalida, ds.idarticulo, a.nombre AS articulo, c.titulo AS categoria, ma.titulo AS marca, me.titulo AS medida, a.codigo, a.codigo_producto, a.stock, a.stock_minimo, a.imagen, ds.cantidad, ds.precio_compra, SUM(ds.cantidad) as total_cantidad
				FROM detalle_salida ds
				LEFT JOIN salidas s ON ds.idsalida = s.idsalida
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN marcas ma ON a.idmarca = ma.idmarca
				LEFT JOIN medidas me ON a.idmedida = me.idmedida
				WHERE ds.idsalida = '$idsalida'
				GROUP BY s.idsalida";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos($idlocal)
	{
		$sql = "SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario, NULL AS ruc FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'activo' AS tabla, ma.idactivo AS id, ma.titulo, u.nombre AS usuario, NULL AS ruc FROM activos ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.estado='activado' AND ma.eliminado='0'
			UNION ALL
			SELECT 'ubicacion' AS tabla, ubi.idubicacion AS id, ubi.titulo, u.nombre AS usuario, NULL AS ruc FROM ubicaciones ubi LEFT JOIN usuario u ON ubi.idusuario = u.idusuario WHERE ubi.estado='activado' AND ubi.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'local2' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'local3' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'correlativo' AS tabla, 0 AS id, (SELECT codigo FROM salidas WHERE idlocal = '$idlocal' ORDER BY idsalida DESC LIMIT 1) AS correlativo, NULL AS usuario, NULL AS ruc";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosUsuario($idlocal)
	{
		$sql = "SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario, NULL AS ruc FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'activo' AS tabla, ma.idactivo AS id, ma.titulo, u.nombre AS usuario, NULL AS ruc FROM activos ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.estado='activado' AND ma.eliminado='0'
			UNION ALL
			SELECT 'ubicacion' AS tabla, ubi.idubicacion AS id, ubi.titulo, u.nombre AS usuario, NULL AS ruc FROM ubicaciones ubi LEFT JOIN usuario u ON ubi.idusuario = u.idusuario WHERE ubi.estado='activado' AND ubi.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'local2' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'local3' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'correlativo' AS tabla, 0 AS id, (SELECT codigo FROM salidas WHERE idlocal = '$idlocal' ORDER BY idsalida DESC LIMIT 1) AS correlativo, NULL AS usuario, NULL AS ruc";

		return ejecutarConsulta($sql);
	}

	public function listarTodosLocalActivosPorUsuario($idlocal)
	{
		$sql = "SELECT 'autorizado' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario, NULL AS ruc FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idlocal='$idlocal' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'recibido' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario, NULL AS ruc FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idlocal='$idlocal' AND pe.estado='activado' AND pe.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	/* ======================= CORRELATIVO ======================= */

	public function getLastNumCodigo()
	{
		$sql = "SELECT codigo as last_codigo FROM salidas ORDER BY idsalida DESC LIMIT 1";
		return ejecutarConsulta($sql);
	}
}
