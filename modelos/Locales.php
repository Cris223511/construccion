<?php
require "../config/Conexion.php";

class Local
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $local_ruc, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO locales (idusuario, titulo, local_ruc, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo','$local_ruc','$descripcion', SYSDATE(), 'activado', '0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM locales WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idlocal)
	{
		$sql = "SELECT * FROM locales WHERE titulo = '$titulo' AND idlocal != '$idlocal' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idlocal, $titulo, $local_ruc, $descripcion)
	{
		$sql = "UPDATE locales SET titulo='$titulo',local_ruc='$local_ruc',descripcion='$descripcion' WHERE idlocal='$idlocal'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idlocal)
	{
		$sql = "UPDATE locales SET estado='desactivado' WHERE idlocal='$idlocal'";
		return ejecutarConsulta($sql);
	}

	public function activar($idlocal)
	{
		$sql = "UPDATE locales SET estado='activado' WHERE idlocal='$idlocal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idlocal)
	{
		$sql = "SELECT * FROM locales WHERE idlocal='$idlocal'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function eliminar($idlocal)
	{
		$sql = "UPDATE locales SET eliminado = '1' WHERE idlocal='$idlocal'";
		return ejecutarConsulta($sql);
	}

	// todos los locales

	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal = '$idlocalSession' AND l.eliminado = '0' ORDER BY l.idlocal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal = '$idlocalSession' AND l.eliminado = '0' AND DATE(l.fecha_hora) >= '$fecha_inicio' AND DATE(l.fecha_hora) <= '$fecha_fin' ORDER BY l.idlocal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.estado='activado' AND l.eliminado = '0' ORDER BY l.idlocal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivosASC()
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.estado='activado' AND l.eliminado = '0' ORDER BY l.idlocal ASC";
		return ejecutarConsulta($sql);
	}

	public function listarUsuariosPorLocal($idlocal)
	{
		$sql = "SELECT
					u.idusuario,
					u.idlocal,
					u.nombre,
					l.titulo as local,
					l.local_ruc as local_ruc,
					u.tipo_documento,
					u.num_documento,
					u.direccion,
					u.telefono,
					u.email,
					u.cargo,
					u.login,
					u.clave,
					u.imagen,
					u.estado
				FROM usuario u
				LEFT JOIN locales l ON u.idlocal = l.idlocal
				WHERE u.idlocal = '$idlocal' AND u.eliminado = '0' ORDER BY u.idusuario DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioActivos($idlocalSession)
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal = '$idlocalSession' AND l.estado='activado' AND l.eliminado = '0' ORDER BY l.idlocal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioActivosASC($idlocalSession)
	{
		$sql = "SELECT l.idlocal, u.idusuario, u.nombre as nombre, u.cargo as cargo, l.titulo, l.local_ruc, l.descripcion, DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, l.estado FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal = '$idlocalSession' AND l.estado='activado' AND l.eliminado = '0' ORDER BY l.idlocal ASC";
		return ejecutarConsulta($sql);
	}

	// locales disponibles

	public function listarLocalesDisponibles()
	{
		$sql = "SELECT 
				  l.idlocal,
				  u.idusuario,
				  u.nombre as nombre,
				  u.cargo as cargo,
				  l.titulo,
				  l.local_ruc,
				  l.descripcion,
				  DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
				  l.estado
				FROM locales l 
				LEFT JOIN usuario u ON l.idusuario = u.idusuario 
				WHERE l.idusuario = '0'
				AND l.eliminado = '0'
				ORDER BY l.idlocal DESC";

		return ejecutarConsulta($sql);
	}

	public function listarLocalesDisponiblesActivos()
	{
		$sql = "SELECT 
				  l.idlocal,
				  u.idusuario,
				  u.nombre as nombre,
				  u.cargo as cargo,
				  l.titulo,
				  l.local_ruc,
				  l.descripcion,
				  DATE_FORMAT(l.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
				  l.estado
				FROM locales l 
				LEFT JOIN usuario u ON l.idusuario = u.idusuario 
				WHERE l.idusuario = '0'
				AND l.estado='activado'
				AND l.eliminado = '0'
				ORDER BY l.idlocal DESC";

		return ejecutarConsulta($sql);
	}
}
