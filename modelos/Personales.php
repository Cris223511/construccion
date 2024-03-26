<?php
require "../config/Conexion.php";

class Personal
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO personales (idusuario, idlocal, nombre, tipo_documento, num_documento, direccion, telefono, email, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$idlocal','$nombre','$tipo_documento','$num_documento','$direccion','$telefono', '$email', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarDniExiste($num_documento)
	{
		if (empty($num_documento)) {
			return false; // El número documento está vacío, consideramos que no existe
		}

		$sql = "SELECT * FROM personales WHERE num_documento = '$num_documento' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function verificarDniEditarExiste($num_documento, $idpersonal)
	{
		$sql = "SELECT * FROM personales WHERE num_documento = '$num_documento' AND idpersonal != '$idpersonal' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function editar($idpersonal, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
	{
		$sql = "UPDATE personales SET idlocal='$idlocal',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idpersonal)
	{
		$sql = "UPDATE personales SET estado='desactivado' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	public function activar($idpersonal)
	{
		$sql = "UPDATE personales SET estado='activado' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idpersonal)
	{
		$sql = "UPDATE personales SET eliminado = '1' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idpersonal)
	{
		$sql = "SELECT * FROM personales WHERE idpersonal='$idpersonal'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.eliminado = '0' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.eliminado = '0' AND DATE(p.fecha_hora) >= '$fecha_inicio' AND DATE(p.fecha_hora) <= '$fecha_fin' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormal()
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.eliminado = '0' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idlocal)
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.idlocal = '$idlocal' AND p.eliminado = '0' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocal, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.idlocal = '$idlocal' AND p.eliminado = '0' AND DATE(p.fecha_hora) >= '$fecha_inicio' AND DATE(p.fecha_hora) <= '$fecha_fin' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormalPorUsuario($idlocal)
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.idlocal = '$idlocal' AND p.eliminado = '0' ORDER BY p.idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	public function listarASC()
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.eliminado = '0' ORDER BY p.idpersonal ASC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioASC($idlocal)
	{
		$sql = "SELECT p.idpersonal, p.nombre, u.nombre as usuario, u.cargo as cargo, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, l.titulo as local,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM personales p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				LEFT JOIN locales l ON p.idlocal = l.idlocal
				WHERE p.idlocal = '$idlocal' AND p.eliminado = '0' ORDER BY p.idpersonal ASC";
		return ejecutarConsulta($sql);
	}
}
