<?php
require "../config/Conexion.php";

class Servicio
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $codigo, $descripcion, $costo)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO servicios (idusuario, titulo, codigo, descripcion, costo, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$codigo', '$descripcion', '$costo', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verficarCodigoExiste($codigo)
	{
		$sql = "SELECT * FROM servicios WHERE codigo = '$codigo'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El codigo ya existe en la tabla
			return true;
		}
		// El codigo no existe en la tabla
		return false;
	}

	public function verficarCodigoEditarExiste($codigo, $idservicio)
	{
		$sql = "SELECT * FROM servicios WHERE codigo = '$codigo' AND idservicio != '$idservicio' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El codigo ya existe en la tabla
			return true;
		}
		// El codigo no existe en la tabla
		return false;
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM servicios WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idservicio)
	{
		$sql = "SELECT * FROM servicios WHERE titulo = '$titulo' AND idservicio != '$idservicio' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idservicio, $titulo, $codigo, $descripcion, $costo)
	{
		$sql = "UPDATE servicios SET titulo='$titulo',codigo='$codigo',descripcion='$descripcion',costo='$costo' WHERE idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idservicio)
	{
		$sql = "UPDATE servicios SET estado='desactivado' WHERE idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

	public function activar($idservicio)
	{
		$sql = "UPDATE servicios SET estado='activado' WHERE idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idservicio)
	{
		$sql = "UPDATE servicios SET eliminado = '1' WHERE idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idservicio)
	{
		$sql = "SELECT * FROM servicios WHERE idservicio='$idservicio'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT s.idservicio, u.idusuario, u.nombre as nombre, u.cargo as cargo, s.titulo, s.codigo, s.descripcion, s.costo, DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, s.estado FROM servicios s LEFT JOIN usuario u ON s.idusuario = u.idusuario WHERE s.eliminado = '0' ORDER BY s.idservicio DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT s.idservicio, u.idusuario, u.nombre as nombre, u.cargo as cargo, s.titulo, s.codigo, s.descripcion, s.costo, DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, s.estado FROM servicios s LEFT JOIN usuario u ON s.idusuario = u.idusuario WHERE s.idusuario = '$idusuario' AND s.eliminado = '0' ORDER BY s.idservicio DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idservicio, titulo FROM servicios WHERE estado='activado' AND eliminado = '0' ORDER BY idservicio DESC";
		return ejecutarConsulta($sql);
	}

	public function getLastCodigo()
	{
		$sql = "SELECT codigo as last_codigo FROM servicios WHERE eliminado = '0' ORDER BY idservicio DESC LIMIT 1";
		return ejecutarConsulta($sql);
	}
}
