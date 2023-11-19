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
}
