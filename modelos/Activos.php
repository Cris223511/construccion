<?php
require "../config/Conexion.php";

class Activo
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO activos (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM activos WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idactivo)
	{
		$sql = "SELECT * FROM activos WHERE titulo = '$titulo' AND idactivo != '$idactivo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idactivo, $titulo, $descripcion)
	{
		$sql = "UPDATE activos SET titulo='$titulo',descripcion='$descripcion' WHERE idactivo='$idactivo'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idactivo)
	{
		$sql = "UPDATE activos SET estado='desactivado' WHERE idactivo='$idactivo'";
		return ejecutarConsulta($sql);
	}

	public function activar($idactivo)
	{
		$sql = "UPDATE activos SET estado='activado' WHERE idactivo='$idactivo'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idactivo)
	{
		$sql = "UPDATE activos SET eliminado = '1' WHERE idactivo='$idactivo'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idactivo)
	{
		$sql = "SELECT * FROM activos WHERE idactivo='$idactivo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idactivo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM activos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idactivo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idactivo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM activos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idactivo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idactivo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM activos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idactivo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idactivo, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM activos m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idactivo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idactivo, titulo FROM activos WHERE estado='activado' AND eliminado = '0' ORDER BY idactivo DESC";
		return ejecutarConsulta($sql);
	}
}
