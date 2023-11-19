<?php
require "../config/Conexion.php";

class Categoria
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO categoria (idusuario, titulo, descripcion, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM categoria WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idcategoria)
	{
		$sql = "SELECT * FROM categoria WHERE titulo = '$titulo' AND idcategoria != '$idcategoria' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idcategoria, $titulo, $descripcion)
	{
		$sql = "UPDATE categoria SET titulo='$titulo',descripcion='$descripcion' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idcategoria)
	{
		$sql = "UPDATE categoria SET estado='desactivado' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	public function activar($idcategoria)
	{
		$sql = "UPDATE categoria SET estado='activado' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idcategoria)
	{
		$sql = "UPDATE categoria SET eliminado = '1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idcategoria)
	{
		$sql = "SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT c.idcategoria, u.idusuario, u.nombre as nombre, u.cargo as cargo, c.titulo, c.descripcion, DATE_FORMAT(c.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, c.estado FROM categoria c LEFT JOIN usuario u ON c.idusuario = u.idusuario WHERE c.eliminado = '0' ORDER BY c.idcategoria DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT c.idcategoria, u.idusuario, u.nombre as nombre, u.cargo as cargo, c.titulo, c.descripcion, DATE_FORMAT(c.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, c.estado FROM categoria c LEFT JOIN usuario u ON c.idusuario = u.idusuario WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' ORDER BY c.idcategoria DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idcategoria, titulo FROM categoria WHERE estado='activado' AND eliminado = '0' ORDER BY idcategoria DESC";
		return ejecutarConsulta($sql);
	}
}
