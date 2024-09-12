<?php
require_once "global.php";

// Configurar la zona horaria
date_default_timezone_set('America/Lima');

$conexion = new mysqli('p:' . DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');

// Si tenemos un posible error en la conexión lo mostramos
if (mysqli_connect_errno()) {
	printf("Falló conexión a la base de datos: %s\n", mysqli_connect_error());
	exit();
}

if (!function_exists('ejecutarConsulta')) {
	function ejecutarConsulta($sql)
	{
		global $conexion;

		if (strpos($sql, "SYSDATE()") !== false) {
			$sql = str_replace("SYSDATE()", "'" . date('Y-m-d H:i:s') . "'", $sql);
		}

		$query = $conexion->query($sql);
		if (!$query) {
			echo "Error en la consulta SQL: " . mysqli_error($conexion);
			return false;
		}
		return $query;
	}

	function ejecutarConsultaSimpleFila($sql)
	{
		global $conexion;

		if (strpos($sql, "SYSDATE()") !== false) {
			$sql = str_replace("SYSDATE()", "'" . date('Y-m-d H:i:s') . "'", $sql);
		}

		$query = $conexion->query($sql);
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;

		if (strpos($sql, "SYSDATE()") !== false) {
			$sql = str_replace("SYSDATE()", "'" . date('Y-m-d H:i:s') . "'", $sql);
		}

		$query = $conexion->query($sql);
		return $conexion->insert_id;
	}

	function limpiarCadena($str)
	{
		global $conexion;
		$str = mysqli_real_escape_string($conexion, trim($str));
		return htmlspecialchars($str);
	}
}
