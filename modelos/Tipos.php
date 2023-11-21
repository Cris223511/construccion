<?php
require "../config/Conexion.php";

class Tipo
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO tipos (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM tipos WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idtipo)
	{
		$sql = "SELECT * FROM tipos WHERE titulo = '$titulo' AND idtipo != '$idtipo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idtipo, $titulo, $descripcion)
	{
		$sql = "UPDATE tipos SET titulo='$titulo',descripcion='$descripcion' WHERE idtipo='$idtipo'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idtipo)
	{
		$sql = "UPDATE tipos SET estado='desactivado' WHERE idtipo='$idtipo'";
		return ejecutarConsulta($sql);
	}

	public function activar($idtipo)
	{
		$sql = "UPDATE tipos SET estado='activado' WHERE idtipo='$idtipo'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idtipo)
	{
		$sql = "UPDATE tipos SET eliminado = '1' WHERE idtipo='$idtipo'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idtipo)
	{
		$sql = "SELECT * FROM tipos WHERE idtipo='$idtipo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idtipo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM tipos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idtipo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idtipo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM tipos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idtipo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idtipo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM tipos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idtipo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idtipo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM tipos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idtipo DESC";
		return ejecutarConsulta($sql);
	}
	
	public function listarActivos()
	{
		$sql = "SELECT idtipo, titulo FROM tipos WHERE estado='activado' AND eliminado = '0' ORDER BY idtipo DESC";
		return ejecutarConsulta($sql);
	}
}
