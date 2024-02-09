<?php
ob_start();
if (strlen(session_id()) < 1) {
    session_start(); //Validamos si existe o no la sesión
}
require_once "../modelos/Usuario.php";
require_once "../modelos/Perfiles.php";

$usuario = new Usuario();
$perfil = new Perfiles();

// Variables de sesión a utilizar.
$idusuario = $_SESSION["idusuario"];
$cargo = $_SESSION["cargo"];

$idlocal = isset($_POST["idlocal"]) ? limpiarCadena($_POST["idlocal"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
        } else {
            //Validamos el acceso solo al usuario logueado y autorizado.
            if ($_SESSION['perfilu'] == 1) {
                if (!empty($_FILES['imagen']['name'])) {
					$uploadDirectory = "../files/usuarios/";
				
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

                $nombreExiste = $usuario->verificarNombreExiste($nombre);
                $dniExiste = $usuario->verificarDniExiste($num_documento);
                $emailExiste = $usuario->verificarEmailExiste($email);
                $usuarioExiste = $usuario->verificarUsuarioExiste($login);
                $perfilUsuario = $usuario->mostrar($idusuario);

                if ($nombreExiste && $nombre != $perfilUsuario['nombre']) {
                    echo "El nombre que ha ingresado ya existe.";
                } else if ($dniExiste && $num_documento != $perfilUsuario['num_documento']) {
                    echo "El número de documento que ha ingresado ya existe.";
                } else if ($emailExiste && $email != $perfilUsuario['email']) {
                    echo "El email que ha ingresado ya existe.";
                } else if ($usuarioExiste && $login != $perfilUsuario['login']) {
                    echo "El nombre del usuario que ha ingresado ya existe.";
                } else {
                    $rspta = $perfil->actualizarPerfilUsuario($idusuario, $idlocal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $login, $clave, $imagen);
                    echo $rspta ? "Perfil actualizado correctamente" : "Perfil no se pudo actualizar";
                    if ($rspta) {
                        $_SESSION['nombre'] = $nombre;
                        $_SESSION['imagen'] = $imagen;
                        $_SESSION['idlocal'] = $idlocal;
                    }
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['perfilu'] == 1) {
                $rspta = $usuario->mostrar($idusuario);
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'actualizarSession':
        $info = array(
            'nombre' => $_SESSION['nombre'],
            'imagen' => $_SESSION['imagen'],
            'local' => $_SESSION['local'],
            'cargo' => $_SESSION['cargo_detalle']
        );
        echo json_encode($info);
        break;
}
ob_end_flush();
