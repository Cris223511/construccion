<?php
require "../config/Conexion.php";

class Trabajador
{
	public function __construct()
	{
	}

	public function agregar($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $telefono, $email, $fecha_nac)
	{
		date_default_timezone_set("America/Lima");
		$sql = "INSERT INTO trabajadores (idusuario, idlocal, nombre, tipo_documento, num_documento, telefono, email, fecha_nac, estado, eliminado)
            VALUES ('$idusuario','$idlocal','$nombre','$tipo_documento','$num_documento','$telefono', '$email', '$fecha_nac', 'activado','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarDniExiste($num_documento)
	{
		$sql = "SELECT * FROM trabajadores WHERE num_documento = '$num_documento' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function verificarDniEditarExiste($num_documento, $idtrabajador)
	{
		$sql = "SELECT * FROM trabajadores WHERE num_documento = '$num_documento' AND idtrabajador != '$idtrabajador' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El número documento ya existe en la tabla
			return true;
		}
		// El número documento no existe en la tabla
		return false;
	}

	public function editar($idtrabajador, $idlocal, $nombre, $tipo_documento, $num_documento, $telefono, $email, $fecha_nac)
	{
		$sql = "UPDATE trabajadores SET idlocal='$idlocal',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',telefono='$telefono',email='$email',fecha_nac='$fecha_nac' WHERE idtrabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idtrabajador)
	{
		$sql = "UPDATE trabajadores SET estado='desactivado' WHERE idtrabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function activar($idtrabajador)
	{
		$sql = "UPDATE trabajadores SET estado='activado' WHERE idtrabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($idtrabajador)
	{
		$sql = "UPDATE trabajadores SET eliminado = '1' WHERE idtrabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idtrabajador)
	{
		$sql = "SELECT * FROM trabajadores WHERE idtrabajador='$idtrabajador'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarTrabajadores()
	{
		$sql = "SELECT t.idtrabajador, t.nombre, l.titulo as local, t.tipo_documento, t.num_documento, t.telefono, t.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(t.fecha_nac), ' de ', 
				CASE MONTH(t.fecha_nac)
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
				END, ' del ', YEAR(t.fecha_nac)) as fecha, t.estado
				FROM trabajadores t
				LEFT JOIN locales l ON t.idlocal = l.idlocal
				LEFT JOIN usuario u ON t.idusuario = u.idusuario
				WHERE t.eliminado = '0' ORDER BY t.idtrabajador DESC";
		return ejecutarConsulta($sql);
	}

	public function listarTrabajadoresFechaNormal()
	{
		$sql = "SELECT t.idtrabajador, t.nombre, l.titulo as local, t.tipo_documento, t.num_documento, t.telefono, t.email, u.idusuario, u.cargo as cargo, t.fecha_nac as fecha, t.estado
				FROM trabajadores t
				LEFT JOIN locales l ON t.idlocal = l.idlocal
				LEFT JOIN usuario u ON t.idusuario = u.idusuario
				WHERE t.eliminado = '0' ORDER BY t.idtrabajador DESC";
		return ejecutarConsulta($sql);
	}

	public function listarTrabajadoresPorLocal($idlocal)
	{
		$sql = "SELECT t.idtrabajador, t.nombre, l.titulo as local, t.tipo_documento, t.num_documento, t.telefono, t.email, u.idusuario, u.cargo as cargo,
				CONCAT(DAY(t.fecha_nac), ' de ', 
				CASE MONTH(t.fecha_nac)
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
				END, ' del ', YEAR(t.fecha_nac)) as fecha, t.estado
				FROM trabajadores t
				LEFT JOIN locales l ON t.idlocal = l.idlocal
				LEFT JOIN usuario u ON t.idusuario = u.idusuario
				WHERE t.idlocal = '$idlocal' AND t.eliminado = '0' ORDER BY t.idtrabajador DESC";
		return ejecutarConsulta($sql);
	}

	public function listarTrabajadoresFechaNormalPorLocal($idlocal)
	{
		$sql = "SELECT t.idtrabajador, t.nombre, l.titulo as local, t.tipo_documento, t.num_documento, t.telefono, t.email, u.idusuario, u.cargo as cargo, t.fecha_nac as fecha, t.estado
				FROM trabajadores t
				LEFT JOIN locales l ON t.idlocal = l.idlocal
				LEFT JOIN usuario u ON t.idusuario = u.idusuario
				WHERE t.idlocal = '$idlocal' AND t.eliminado = '0' ORDER BY t.idtrabajador DESC";
		return ejecutarConsulta($sql);
	}
}
