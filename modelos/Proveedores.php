<?php
require "../config/Conexion.php";

class Proveedor
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO proveedores (idusuario, nombre, tipo_documento, num_documento, direccion, telefono, email, fecha_nac, estado, eliminado)
            VALUES ('$idusuario','$nombre','$tipo_documento','$num_documento','$direccion','$telefono', '$email', '$fecha_nac', 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarDniExiste($num_documento)
	{
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

	public function editar($idproveedor, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac)
	{
		$sql = "UPDATE proveedores SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',fecha_nac='$fecha_nac' WHERE idproveedor='$idproveedor'";
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
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(c.fecha_nac), ' de ', 
				CASE MONTH(c.fecha_nac)
					WHEN 1 THEN 'Enero'
					WHEN 2 THEN 'Febrero'
					WHEN 3 THEN 'Marzo'
					WHEN 4 THEN 'Abril'
					WHEN 5 THEN 'Mayo'
					WHEN 6 THEN 'Junio'
					WHEN 7 THEN 'Julio'
					WHEN 8 THEN 'Agosto'
					WHEN 9 THEN 'Septiembre'
					WHEN 10 THEN 'Octubre'
					WHEN 11 THEN 'Noviembre'
					WHEN 12 THEN 'Diciembre'
				END, ' del ', YEAR(c.fecha_nac)) as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.eliminado = '0' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(c.fecha_nac), ' de ', 
				CASE MONTH(c.fecha_nac)
					WHEN 1 THEN 'Enero'
					WHEN 2 THEN 'Febrero'
					WHEN 3 THEN 'Marzo'
					WHEN 4 THEN 'Abril'
					WHEN 5 THEN 'Mayo'
					WHEN 6 THEN 'Junio'
					WHEN 7 THEN 'Julio'
					WHEN 8 THEN 'Agosto'
					WHEN 9 THEN 'Septiembre'
					WHEN 10 THEN 'Octubre'
					WHEN 11 THEN 'Noviembre'
					WHEN 12 THEN 'Diciembre'
				END, ' del ', YEAR(c.fecha_nac)) as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.eliminado = '0' AND DATE(c.fecha_nac) >= '$fecha_inicio' AND DATE(c.fecha_nac) <= '$fecha_fin' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormal()
	{
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo, c.fecha_nac as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.eliminado = '0' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuario)
	{
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(c.fecha_nac), ' de ', 
				CASE MONTH(c.fecha_nac)
					WHEN 1 THEN 'Enero'
					WHEN 2 THEN 'Febrero'
					WHEN 3 THEN 'Marzo'
					WHEN 4 THEN 'Abril'
					WHEN 5 THEN 'Mayo'
					WHEN 6 THEN 'Junio'
					WHEN 7 THEN 'Julio'
					WHEN 8 THEN 'Agosto'
					WHEN 9 THEN 'Septiembre'
					WHEN 10 THEN 'Octubre'
					WHEN 11 THEN 'Noviembre'
					WHEN 12 THEN 'Diciembre'
				END, ' del ', YEAR(c.fecha_nac)) as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idusuario, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(c.fecha_nac), ' de ', 
				CASE MONTH(c.fecha_nac)
					WHEN 1 THEN 'Enero'
					WHEN 2 THEN 'Febrero'
					WHEN 3 THEN 'Marzo'
					WHEN 4 THEN 'Abril'
					WHEN 5 THEN 'Mayo'
					WHEN 6 THEN 'Junio'
					WHEN 7 THEN 'Julio'
					WHEN 8 THEN 'Agosto'
					WHEN 9 THEN 'Septiembre'
					WHEN 10 THEN 'Octubre'
					WHEN 11 THEN 'Noviembre'
					WHEN 12 THEN 'Diciembre'
				END, ' del ', YEAR(c.fecha_nac)) as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' AND DATE(c.fecha_nac) >= '$fecha_inicio' AND DATE(c.fecha_nac) <= '$fecha_fin' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}

	public function listarFechaNormalPorUsuario($idusuario)
	{
		$sql = "SELECT c.idproveedor, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo, c.fecha_nac as fecha, c.estado
				FROM proveedores c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' ORDER BY c.idproveedor DESC";
		return ejecutarConsulta($sql);
	}
}
