<?php
require "../config/Conexion.php";

class MetodoPago
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $titulo, $descripcion, $imagen)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO metodo_pago (idusuario, titulo, descripcion, imagen, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$titulo', '$descripcion', '$imagen', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarNombreExiste($titulo)
	{
		$sql = "SELECT * FROM metodo_pago WHERE titulo = '$titulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function verificarNombreEditarExiste($titulo, $idmetodopago)
	{
		$sql = "SELECT * FROM metodo_pago WHERE titulo = '$titulo' AND idmetodopago != '$idmetodopago' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El titulo ya existe en la tabla
			return true;
		}
		// El titulo no existe en la tabla
		return false;
	}

	public function editar($idmetodopago, $titulo, $descripcion, $imagen)
	{
		$sql = "UPDATE metodo_pago SET titulo='$titulo',descripcion='$descripcion',imagen='$imagen' WHERE idmetodopago='$idmetodopago'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idmetodopago)
	{
		$sql = "UPDATE metodo_pago SET estado='desactivado' WHERE idmetodopago='$idmetodopago'";
		return ejecutarConsulta($sql);
	}

	public function activar($idmetodopago)
	{
		$sql = "UPDATE metodo_pago SET estado='activado' WHERE idmetodopago='$idmetodopago'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idmetodopago)
	{
		$sql = "UPDATE metodo_pago SET eliminado = '1' WHERE idmetodopago='$idmetodopago'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idmetodopago)
	{
		$sql = "SELECT * FROM metodo_pago WHERE idmetodopago='$idmetodopago'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT m.idmetodopago, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, m.imagen, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM metodo_pago m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.eliminado = '0' ORDER BY m.idmetodopago DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT m.idmetodopago, u.idusuario, u.nombre as nombre, u.cargo as cargo, m.titulo, m.descripcion, m.imagen, DATE_FORMAT(m.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, m.estado FROM metodo_pago m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.idusuario = '$idusuario' AND m.eliminado = '0' ORDER BY m.idmetodopago DESC";
		return ejecutarConsulta($sql);
	}

	public function listarActivos()
	{
		$sql = "SELECT idmetodopago, titulo FROM metodo_pago WHERE estado='activado' AND eliminado = '0' ORDER BY idmetodopago DESC";
		return ejecutarConsulta($sql);
	}
}
