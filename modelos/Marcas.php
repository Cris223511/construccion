<?php
require "../config/Conexion.php";

class Marca
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO marcas (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM marcas WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idmarca)
	{
		$sql = "SELECT * FROM marcas WHERE titulo = '$titulo' AND idmarca != '$idmarca' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idmarca, $titulo, $descripcion)
	{
		$sql = "UPDATE marcas SET titulo='$titulo',descripcion='$descripcion' WHERE idmarca='$idmarca'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idmarca)
	{
		$sql = "UPDATE marcas SET estado='desactivado' WHERE idmarca='$idmarca'";
		return ejecutarConsulta($sql);
	}

	public function activar($idmarca)
	{
		$sql = "UPDATE marcas SET estado='activado' WHERE idmarca='$idmarca'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idmarca)
	{
		$sql = "UPDATE marcas SET eliminado = '1' WHERE idmarca='$idmarca'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idmarca)
	{
		$sql = "SELECT * FROM marcas WHERE idmarca='$idmarca'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idmarca, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM marcas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idmarca DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idmarca, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM marcas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idmarca DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idmarca, titulo FROM marcas WHERE estado='activado' AND eliminado = '0' ORDER BY idmarca DESC";
		return ejecutarConsulta($sql);
	}
}
