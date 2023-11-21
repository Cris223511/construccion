<?php
require "../config/Conexion.php";

class Maquinaria
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO maquinarias (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM maquinarias WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idmaquinaria)
	{
		$sql = "SELECT * FROM maquinarias WHERE titulo = '$titulo' AND idmaquinaria != '$idmaquinaria' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idmaquinaria, $titulo, $descripcion)
	{
		$sql = "UPDATE maquinarias SET titulo='$titulo',descripcion='$descripcion' WHERE idmaquinaria='$idmaquinaria'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idmaquinaria)
	{
		$sql = "UPDATE maquinarias SET estado='desactivado' WHERE idmaquinaria='$idmaquinaria'";
		return ejecutarConsulta($sql);
	}

	public function activar($idmaquinaria)
	{
		$sql = "UPDATE maquinarias SET estado='activado' WHERE idmaquinaria='$idmaquinaria'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idmaquinaria)
	{
		$sql = "UPDATE maquinarias SET eliminado = '1' WHERE idmaquinaria='$idmaquinaria'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idmaquinaria)
	{
		$sql = "SELECT * FROM maquinarias WHERE idmaquinaria='$idmaquinaria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idmaquinaria, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM maquinarias m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idmaquinaria DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idmaquinaria, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM maquinarias m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idmaquinaria DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idmaquinaria, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM maquinarias m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idmaquinaria DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT m.idmaquinaria, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM maquinarias m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' AND DATE(m.fecha_hora) >= '$fecha_inicio' AND DATE(m.fecha_hora) <= '$fecha_fin' ORDER BY m.idmaquinaria DESC";
		return ejecutarConsulta($sql);
	}
	
	public function listarActivos()
	{
		$sql = "SELECT idmaquinaria, titulo FROM maquinarias WHERE estado='activado' AND eliminado = '0' ORDER BY idmaquinaria DESC";
		return ejecutarConsulta($sql);
	}
}
