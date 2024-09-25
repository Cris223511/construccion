<?php
require "../config/Conexion.php";

class Ubicacion
{
	public function __construct() {}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO ubicaciones (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM ubicaciones WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idubicacion)
	{
		$sql = "SELECT * FROM ubicaciones WHERE titulo = '$titulo' AND idubicacion != '$idubicacion' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idubicacion, $titulo, $descripcion)
	{
		$sql = "UPDATE ubicaciones SET titulo='$titulo',descripcion='$descripcion' WHERE idubicacion='$idubicacion'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idubicacion)
	{
		$sql = "UPDATE ubicaciones SET estado='desactivado' WHERE idubicacion='$idubicacion'";
		return ejecutarConsulta($sql);
	}

	public function activar($idubicacion)
	{
		$sql = "UPDATE ubicaciones SET estado='activado' WHERE idubicacion='$idubicacion'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idubicacion)
	{
		$sql = "UPDATE ubicaciones SET eliminado = '1' WHERE idubicacion='$idubicacion'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idubicacion)
	{
		$sql = "SELECT * FROM ubicaciones WHERE idubicacion='$idubicacion'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idubicacion, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM ubicaciones m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idubicacion DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idubicacion, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM ubicaciones m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idubicacion DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idubicacion, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM ubicaciones m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idubicacion DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idubicacion, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM ubicaciones m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idubicacion DESC";
		return ejecutarConsulta($sql);
	}

	public function listarubicaciones()
	{
		$sql = "SELECT idubicacion, titulo FROM ubicaciones WHERE estado='activado' AND eliminado = '0' ORDER BY idubicacion DESC";
		return ejecutarConsulta($sql);
	}
}
