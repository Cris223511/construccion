<?php
require "../config/Conexion.php";

class Cliente
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO clientes (idusuario, nombre, tipo_documento, num_documento, direccion, telefono, email, fecha_nac, estado, eliminado)
            VALUES ('$idusuario','$nombre','$tipo_documento','$num_documento','$direccion','$telefono', '$email', '$fecha_nac', 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarDniExiste($num_documento)
	{
		$sql = "SELECT * FROM clientes WHERE num_documento = '$num_documento' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function verificarDniEditarExiste($num_documento, $idcliente)
	{
		$sql = "SELECT * FROM clientes WHERE num_documento = '$num_documento' AND idcliente != '$idcliente' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function editar($idcliente, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_nac)
	{
		$sql = "UPDATE clientes SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',fecha_nac='$fecha_nac' WHERE idcliente='$idcliente'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idcliente)
	{
		$sql = "UPDATE clientes SET estado='desactivado' WHERE idcliente='$idcliente'";
		return ejecutarConsulta($sql);
	}

	public function activar($idcliente)
	{
		$sql = "UPDATE clientes SET estado='activado' WHERE idcliente='$idcliente'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idcliente)
	{
		$sql = "UPDATE clientes SET eliminado = '1' WHERE idcliente='$idcliente'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idcliente)
	{
		$sql = "SELECT * FROM clientes WHERE idcliente='$idcliente'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarClientes()
	{
		$sql = "SELECT c.idcliente, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
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
				FROM clientes c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.eliminado = '0' ORDER BY c.idcliente DESC";
		return ejecutarConsulta($sql);
	}

	public function listarClientesFechaNormal()
	{
		$sql = "SELECT c.idcliente, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo, c.fecha_nac as fecha, c.estado
				FROM clientes c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.eliminado = '0' ORDER BY c.idcliente DESC";
		return ejecutarConsulta($sql);
	}

	public function listarClientesPorUsuario($idusuario)
	{
		$sql = "SELECT c.idcliente, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo,
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
				FROM clientes c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' ORDER BY c.idcliente DESC";
		return ejecutarConsulta($sql);
	}

	public function listarclientesFechaNormalPorUsuario($idusuario)
	{
		$sql = "SELECT c.idcliente, c.nombre, c.tipo_documento, c.num_documento, c.direccion, c.telefono, c.email, u.idusuario, u.cargo as cargo, c.fecha_nac as fecha, c.estado
				FROM clientes c
				LEFT JOIN usuario u ON c.idusuario = u.idusuario
				WHERE c.idusuario = '$idusuario' AND c.eliminado = '0' ORDER BY c.idcliente DESC";
		return ejecutarConsulta($sql);
	}
}
