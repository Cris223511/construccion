<?php
ob_start();

require_once "../modelos/Perfiles.php";

$portada = new Perfiles();

switch ($_GET["op"]) {
	case 'mostrar':
		$rspta = $portada->obtenerPortadaLogin();
		echo json_encode($rspta);
		break;
}
ob_end_flush();
