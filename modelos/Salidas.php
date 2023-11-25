<?php
require "../config/Conexion.php";

class Salida
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $idcategoria, $idmarca, $idmedida, $idtipo, $idmaquinaria, $idpersonal, $codigo, $ubicacion, $peso, $descripcion, $idarticulo, $cantidad)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		date_default_timezone_set("America/Lima");
		// Si no hay errores, continuamos con el registro de la entrada
		$sql = "INSERT INTO salidas (idusuario,idcategoria,idmarca,idmedida,idtipo,idmaquinaria,idpersonal,codigo,ubicacion,peso,descripcion,fecha_hora,estado)
            VALUES ('$idusuario','$idcategoria','$idmarca','$idmedida','$idtipo','$idmaquinaria','$idpersonal','$codigo','$ubicacion','$peso','$descripcion', SYSDATE(), 'activado')";
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
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,s.codigo,s.ubicacion,s.peso,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN categoria c ON s.idcategoria=c.idcategoria LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pe ON s.idpersonal=pe.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario LEFT JOIN marcas m ON s.idmarca=m.idmarca ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,s.codigo,s.ubicacion,s.peso,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN categoria c ON s.idcategoria=c.idcategoria LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pe ON s.idpersonal=pe.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario LEFT JOIN marcas m ON s.idmarca=m.idmarca AND DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,s.codigo,s.ubicacion,s.peso,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN categoria c ON s.idcategoria=c.idcategoria LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pe ON s.idpersonal=pe.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario LEFT JOIN marcas m ON s.idmarca=m.idmarca WHERE s.idusuario = '$idusuario' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT s.idsalida,s.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,s.codigo,s.ubicacion,s.peso,s.descripcion,s.descripcion,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,s.estado FROM salidas s LEFT JOIN categoria c ON s.idcategoria=c.idcategoria LEFT JOIN tipos t ON s.idtipo=t.idtipo LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pe ON s.idpersonal=pe.idpersonal LEFT JOIN usuario u ON s.idusuario=u.idusuario LEFT JOIN marcas m ON s.idmarca=m.idmarca WHERE s.idusuario = '$idusuario' AND DATE(s.fecha_hora) >= '$fecha_inicio' AND DATE(s.fecha_hora) <= '$fecha_fin' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCabecera($idsalida)
	{
		$sql = "SELECT s.idsalida,ma.titulo AS maquinaria, pe.nombre AS personal, pe.tipo_documento, pe.num_documento, pe.direccion, pe.email, pe.direccion, pe.telefono, s.codigo,DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha FROM salidas s LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria LEFT JOIN personales pe ON s.idpersonal=pe.idpersonal WHERE s.idsalida = '$idsalida' ORDER BY s.idsalida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($idsalida)
	{
		$sql = "SELECT de.idsalida, de.idarticulo, a.nombre AS articulo, a.codigo, a.codigo_producto, de.cantidad
				FROM detalle_salida de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				WHERE de.idsalida = '$idsalida'";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'personal' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'maquinaria' AS tabla, ma.idmaquinaria AS id, ma.titulo, u.nombre AS usuario FROM maquinarias ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.estado='activado' AND ma.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario)
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.idusuario='$idusuario' AND b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.idusuario='$idusuario' AND o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.idusuario='$idusuario' AND t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'personal' AS tabla, pe.idpersonal AS id, pe.nombre, u.nombre AS usuario FROM personales pe LEFT JOIN usuario u ON pe.idusuario = u.idusuario WHERE pe.idusuario='$idusuario' AND pe.estado='activado' AND pe.eliminado='0'
			UNION ALL
			SELECT 'maquinaria' AS tabla, ma.idmaquinaria AS id, ma.titulo, u.nombre AS usuario FROM maquinarias ma LEFT JOIN usuario u ON ma.idusuario = u.idusuario WHERE ma.idusuario='$idusuario' AND ma.estado='activado' AND ma.eliminado='0'";

		return ejecutarConsulta($sql);
	}
}
