<?php
require "../config/Conexion.php";

class Entrada
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $idproveedor, $idtipo, $codigo, $ubicacion, $descripcion, $idarticulo, $cantidad)
	{
		// // Primero, debemos verificar si hay suficiente stock para cada artículo
		// $error = $this->validarStock($idarticulo, $cantidad);
		// if ($error) {
		// 	// Si hay un error, no se puede insertar
		// 	return false;
		// }

		date_default_timezone_set("America/Lima");
		// Si no hay errores, continuamos con el registro de la entrada
		$sql = "INSERT INTO entradas (idusuario,idproveedor,idtipo,codigo,ubicacion,descripcion,fecha_hora,estado)
            VALUES ('$idusuario','$idproveedor','$idtipo','$codigo','$ubicacion','$descripcion', SYSDATE(), 'activado')";
		$identradanew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			$sql_detalle = "INSERT INTO detalle_entrada(identrada,idarticulo,cantidad) VALUES ('$identradanew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
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

	public function desactivar($identrada)
	{
		$sql = "UPDATE entradas SET estado='desactivado' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function activar($identrada)
	{
		$sql = "UPDATE entradas SET estado='activado' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($identrada)
	{
		$sql = "DELETE FROM entradas WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($identrada)
	{
		$sql = "SELECT * FROM entradas WHERE identrada='$identrada'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE e.idusuario = '$idusuario' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE e.idusuario = '$idusuario' AND DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCabecera($identrada)
	{
		$sql = "SELECT e.identrada,p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.email, p.direccion, p.telefono, e.codigo,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha FROM entradas e LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor WHERE e.identrada = '$identrada' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($identrada)
	{
		$sql = "SELECT de.identrada, de.idarticulo, a.nombre AS articulo, c.titulo AS categoria, ma.titulo AS marca, me.titulo AS medida, a.codigo, a.codigo_producto, a.stock, a.stock_minimo, a.imagen, de.cantidad
				FROM detalle_entrada de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN marcas ma ON a.idmarca = ma.idmarca
				LEFT JOIN medidas me ON a.idmedida = me.idmedida
				WHERE de.identrada = '$identrada'";
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
			SELECT 'proveedor' AS tabla, p.idproveedor AS id, p.nombre, u.nombre AS usuario FROM proveedores p LEFT JOIN usuario u ON p.idusuario = u.idusuario WHERE p.estado='activado' AND p.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'";

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
			SELECT 'proveedor' AS tabla, p.idproveedor AS id, p.nombre, u.nombre AS usuario FROM proveedores p LEFT JOIN usuario u ON p.idusuario = u.idusuario WHERE p.idusuario='$idusuario' AND p.estado='activado' AND p.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.idusuario='$idusuario' AND t.estado='activado' AND t.eliminado='0'";

		return ejecutarConsulta($sql);
	}
}
