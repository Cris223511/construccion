<?php
require "../config/Conexion.php";

class Entrada
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO entradas (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function editar($identrada, $titulo, $descripcion)
	{
		$sql = "UPDATE entradas SET titulo='$titulo',descripcion='$descripcion' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
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
		$sql = "UPDATE entradas SET eliminado = '1' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($identrada)
	{
		$sql = "SELECT * FROM entradas WHERE identrada='$identrada'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,p.nombre as proveedor,e.cantidad,e.codigo,e.ubicacion,e.tipo_documento,e.num_documento,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN categoria c ON e.idcategoria=c.idcategoria LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario LEFT JOIN marcas m ON e.idmarca=e.idmarca WHERE e.eliminado = '0' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,p.nombre as proveedor,e.cantidad,e.codigo,e.ubicacion,e.tipo_documento,e.num_documento,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN categoria c ON e.idcategoria=c.idcategoria LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario LEFT JOIN marcas m ON e.idmarca=e.idmarca WHERE e.eliminado = '0' AND DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,p.nombre as proveedor,e.cantidad,e.codigo,e.ubicacion,e.tipo_documento,e.num_documento,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN categoria c ON e.idcategoria=c.idcategoria LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario LEFT JOIN marcas m ON e.idmarca=e.idmarca WHERE e.idusuario = '$idusuario' AND e.eliminado = '0' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,c.titulo as categoria,m.titulo as marca,t.titulo as tipo,p.nombre as proveedor,e.cantidad,e.codigo,e.ubicacion,e.tipo_documento,e.num_documento,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.estado FROM entradas e LEFT JOIN categoria c ON e.idcategoria=c.idcategoria LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario LEFT JOIN marcas m ON e.idmarca=e.idmarca WHERE e.idusuario = '$idusuario' AND e.eliminado = '0' AND DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT identrada, titulo FROM entradas WHERE estado='activado' AND eliminado = '0' ORDER BY identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($identrada)
	{
		$sql = "SELECT de.identrada,de.idarticulo,a.nombre,a.codigo,de.cantidad FROM detalle_entrada de LEFT JOIN articulo a on de.idarticulo=a.idarticulo where de.identrada='$identrada'";
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
