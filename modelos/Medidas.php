<?php
require "../config/Conexion.php";

class Medida
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO medidas (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM medidas WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idmedida)
	{
		$sql = "SELECT * FROM medidas WHERE titulo = '$titulo' AND idmedida != '$idmedida' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idmedida, $titulo, $descripcion)
	{
		$sql = "UPDATE medidas SET titulo='$titulo',descripcion='$descripcion' WHERE idmedida='$idmedida'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idmedida)
	{
		$sql = "UPDATE medidas SET estado='desactivado' WHERE idmedida='$idmedida'";
		return ejecutarConsulta($sql);
	}

	public function activar($idmedida)
	{
		$sql = "UPDATE medidas SET estado='activado' WHERE idmedida='$idmedida'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idmedida)
	{
		$sql = "UPDATE medidas SET eliminado = '1' WHERE idmedida='$idmedida'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idmedida)
	{
		$sql = "SELECT * FROM medidas WHERE idmedida='$idmedida'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idmedida, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idmedida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idmedida, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idmedida DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idmedida, titulo FROM medidas WHERE estado='activado' AND eliminado = '0' ORDER BY idmedida DESC";
		return ejecutarConsulta($sql);
	}
}
