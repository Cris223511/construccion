<?php
require "../config/Conexion.php";

class Proveedor
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO proveedores (idusuario, nombre, tipo_documento, num_documento, direccion, telefono, email, fecha_hora, estado, eliminado)
            VALUES ('$idusuario','$nombre','$tipo_documento','$num_documento','$direccion','$telefono', '$email', SYSDATE(), 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarDniExiste($num_documento)
	{
		if (empty($num_documento)) {
			return false; // El número documento está vacío, consideramos que no existe
		}

		$sql = "SELECT * FROM proveedores WHERE num_documento = '$num_documento' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function verificarDniEditarExiste($num_documento, $idproveedor)
	{
		$sql = "SELECT * FROM proveedores WHERE num_documento = '$num_documento' AND idproveedor != '$idproveedor' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function editar($idproveedor, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
	{
		$sql = "UPDATE proveedores SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email' WHERE idproveedor='$idproveedor'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idproveedor)
	{
		$sql = "UPDATE proveedores SET estado='desactivado' WHERE idproveedor='$idproveedor'";
		return ejecutarConsulta($sql);
	}

	public function activar($idproveedor)
	{
		$sql = "UPDATE proveedores SET estado='activado' WHERE idproveedor='$idproveedor'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idproveedor)
	{
		$sql = "UPDATE proveedores SET eliminado = '1' WHERE idproveedor='$idproveedor'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idproveedor)
	{
		$sql = "SELECT * FROM proveedores WHERE idproveedor='$idproveedor'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.eliminado = '0' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.eliminado = '0' AND DATE(p.fecha_hora) >= '$fecha_inicio' AND DATE(p.fecha_hora) <= '$fecha_fin' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormal()
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.eliminado = '0' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.idusuario = '$idusuario' AND p.eliminado = '0' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo,
				DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.idusuario = '$idusuario' AND p.eliminado = '0' AND DATE(p.fecha_hora) >= '$fecha_inicio' AND DATE(p.fecha_hora) <= '$fecha_fin' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormalPorUsuario($idusuario)
	{
		$sql = "SELECT p.idproveedor, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.telefono, p.email, u.idusuario, u.cargo as cargo, DATE_FORMAT(p.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha, p.estado
				FROM proveedores p
				LEFT JOIN usuario u ON p.idusuario = u.idusuario
				WHERE p.idusuario = '$idusuario' AND p.eliminado = '0' ORDER BY p.idproveedor DESC";
		return ejecutarConsulta($sql);
	}
}
