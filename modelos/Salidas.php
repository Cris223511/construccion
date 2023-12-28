<?php
require "../config/Conexion.php";

class Salida
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $idtipo, $tipo_movimiento, $idmaquinaria, $idautorizado, $identregado, $idrecibido, $idfinal, $codigo, $ubicacion, $descripcion, $idarticulo, $cantidad)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		date_default_timezone_set("America/Lima");
		// Si no hay errores, continuamos con el registro de la entrada
		$sql = "INSERT INTO salidas (idusuario,idtipo,tipo_movimiento,idmaquinaria,idautorizado,identregado,idrecibido,idfinal,codigo,ubicacion,descripcion,fecha_hora,estado)
            VALUES ('$idusuario','$idtipo','$tipo_movimiento','$idmaquinaria','$idautorizado','$identregado','$idrecibido','$idfinal','$codigo','$ubicacion','$descripcion', SYSDATE(), 'activado')";
		$idsalidanew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			$sql_detalle = "INSERT INTO detalle_salida(idsalida,idarticulo,cantidad) VALUES ('$idsalidanew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos = $num_elementos + 1;
		}

		return $sw;
	}

	public function verificarCodigo($codigo)
	{
		$sql = "SELECT * FROM salidas WHERE codigo = '$codigo'";
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
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, t.titulo as tipo,pea.nombre AS autorizado, pee.nombre AS entregado, per.nombre AS recibido, pef.nombre AS final,s.codigo,s.tipo_movimiento,s.ubicacion,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales pee ON s.identregado=pee.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN personales pef ON s.idfinal=pef.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, t.titulo as tipo,pea.nombre AS autorizado, pee.nombre AS entregado, per.nombre AS recibido, pef.nombre AS final,s.codigo,s.tipo_movimiento,s.ubicacion,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales pee ON s.identregado=pee.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN personales pef ON s.idfinal=pef.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, t.titulo as tipo,pea.nombre AS autorizado, pee.nombre AS entregado, per.nombre AS recibido, pef.nombre AS final,s.codigo,s.tipo_movimiento,s.ubicacion,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales pee ON s.identregado=pee.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN personales pef ON s.idfinal=pef.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE s.idlocal = '$idlocalSession' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,lo.titulo as local, t.titulo as tipo,pea.nombre AS autorizado, pee.nombre AS entregado, per.nombre AS recibido, pef.nombre AS final,s.codigo,s.tipo_movimiento,s.ubicacion,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN locales lo ON s.idlocal=lo.idlocal LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales pee ON s.identregado=pee.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN personales pef ON s.idfinal=pef.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario WHERE s.idlocal = '$idlocalSession' AND DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCabecera($idsalida)
	{
		$sql = "SELECT s.idsalida,s.tipo_movimiento,ma.idmaquinaria, pea.idpersonal AS idautorizado, u.idusuario, ma.titulo AS maquinaria, ma.descripcion, u.nombre AS usuario, pea.nombre AS autorizado, pee.nombre AS entregado, per.nombre AS recibido, pef.nombre AS final, u.tipo_documento, u.num_documento, u.direccion, u.email, u.direccion, u.telefono, s.codigo,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha FROM salidas s LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN usuario u ON s.idusuario=u.idusuario LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal LEFT JOIN personales pee ON s.identregado=pee.idpersonal LEFT JOIN personales per ON s.idrecibido=per.idpersonal LEFT JOIN personales pef ON s.idfinal=pef.idpersonal WHERE s.idsalida = '$idsalida' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($idsalida)
	{
		$sql = "SELECT de.idsalida, de.idarticulo, a.nombre AS articulo, c.titulo AS categoria, ma.titulo AS marca, me.titulo AS medida, a.codigo, a.codigo_producto, a.stock, a.stock_minimo, a.imagen, de.cantidad
				FROM detalle_salida de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN marcas ma ON a.idmarca = ma.idmarca
				LEFT JOIN medidas me ON a.idmedida = me.idmedida
				WHERE de.idsalida = '$idsalida'";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'autorizado' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'entregado' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'recibido' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'final' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'maquinaria' AS tabla, ma.idmaquinaria AS id, ma.titulo, u.nombre AS usuario FROM maquinarias ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.estado='activado' AND ma.eliminado='0'
			UNION ALL
			SELECT 'correlativo' AS tabla, 0 AS id, (SELECT codigo FROM salidas ORDER BY idsalida DESC LIMIT 1) AS correlativo, NULL AS usuario";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario)
	{
		$sql = "SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'autorizado' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idusuario='$idusuario' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'entregado' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idusuario='$idusuario' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'recibido' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idusuario='$idusuario' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'final' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idusuario='$idusuario' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'maquinaria' AS tabla, ma.idmaquinaria AS id, ma.titulo, u.nombre AS usuario FROM maquinarias ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.estado='activado' AND ma.eliminado='0'
			UNION ALL
			SELECT 'correlativo' AS tabla, 0 AS id, (SELECT codigo FROM salidas ORDER BY idsalida DESC LIMIT 1) AS correlativo, NULL AS usuario";

		return ejecutarConsulta($sql);
	}

	/* ======================= CORRELATIVO ======================= */

	public function getLastNumCodigo()
	{
		$sql = "SELECT codigo as last_codigo FROM salidas ORDER BY idsalida DESC LIMIT 1";
		return ejecutarConsulta($sql);
	}
}
