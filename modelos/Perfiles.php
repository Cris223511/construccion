<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Perfiles
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	/* ===================  PERFILES DE USUARIO ====================== */
	public function mostrarUsuario($idusuario)
	{
		$sql = "SELECT * FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function actualizarPerfilUsuario($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $login, $clave, $imagen)
	{
		$sql = "UPDATE usuario SET idlocal='$idlocal',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',login='$login',clave='$clave',imagen='$imagen' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	/* ===================  PORTADA DE LOGIN ====================== */
	public function actualizarPortadaLogin($imagen)
	{
		$sql = "UPDATE portada_login SET imagen='$imagen'";
		return ejecutarConsulta($sql);
	}

	public function obtenerPortadaLogin()
	{
		$sql = "SELECT * FROM portada_login";
		return ejecutarConsultaSimpleFila($sql);
	}

	/* ===================  REPORTES ====================== */
	public function mostrarReporte()
	{
		$sql = "SELECT * FROM reportes";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function actualizarBoleta($idreporte, $titulo, $ruc, $direccion, $telefono, $email, $imagen)
	{
		$sql = "UPDATE reportes SET titulo='$titulo',ruc='$ruc',direccion='$direccion',telefono='$telefono',email='$email',imagen='$imagen' WHERE idreporte='$idreporte'";
		return ejecutarConsulta($sql);
	}
}
