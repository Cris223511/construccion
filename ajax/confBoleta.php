<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
require_once "../modelos/Perfiles.php";

$perfil = new Perfiles();

$idreporte = isset($_POST["idreporte"]) ? limpiarCadena($_POST["idreporte"]) : "";
$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
$ruc = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
		} else {
			//Validamos el acceso solo al usuario logueado y autorizado.
			if ($_SESSION['perfilu'] == 1) {
				$rspta = $perfil->actualizarBoleta($idreporte, $titulo, $ruc, $direccion, $telefono, $email);
				echo $rspta ? "Boleta actualizado correctamente" : "Boleta no se pudo actualizar";
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'mostrar':
		$rspta = $perfil->mostrarReporte();
		echo json_encode($rspta);
		break;
}
ob_end_flush();
