<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	//Validamos el acceso solo al usuario logueado y autorizado.
	if ($_SESSION['perfilu'] == 1) {
		require_once "../modelos/Perfiles.php";

		$portada = new Perfiles();

		$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

		switch ($_GET["op"]) {
			case 'guardaryeditar':
				if ($_SESSION['perfilu'] == 1) {
					if (!empty($_FILES['imagen']['name'])) {
						$uploadDirectory = "../files/portadas/";

						$tempFile = $_FILES['imagen']['tmp_name'];
						$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
						$newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
						$targetFile = $uploadDirectory . $newFileName;

						// Verificar si es una imagen y mover el archivo
						$allowedExtensions = array('jpg', 'jpeg', 'png');
						if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
							// El archivo se ha movido correctamente, ahora $newFileName contiene el nombre del archivo
							$imagen = $newFileName;
						} else {
							// Error en la subida del archivo
							echo "Error al subir la imagen.";
							exit;
						}
					} else {
						// No se ha seleccionado ninguna imagen
						$imagen = $_POST["imagenactual"];
					}

					$rspta = $portada->actualizarPortadaLogin($imagen);
					echo $rspta ? "Portada actualizada correctamente." : "Portada no se pudo actualizar.";
				} else {
					require 'noacceso.php';
				}
				break;

			case 'mostrar':
				if ($_SESSION['perfilu'] == 1) {
					$rspta = $portada->obtenerPortadaLogin();
					echo json_encode($rspta);
				} else {
					require 'noacceso.php';
				}
				break;
		}
		//Fin de las validaciones de acceso
	} else {
		require 'noacceso.php';
	}
}
ob_end_flush();
