<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['salidas'] == 1) {
    require('Salida.php');

    require_once "../modelos/Perfiles.php";
    $perfil = new Perfiles();
    $rspta = $perfil->mostrarReporte();

    //Establecemos los datos de la empresa
    $logo = $rspta["imagen"];
    $ext_logo = strtolower(pathinfo($rspta["imagen"], PATHINFO_EXTENSION));
    $empresa = $rspta["titulo"];
    $documento = ($rspta["ruc"] == '') ? 'Sin registrar' : $rspta["ruc"];
    $direccion = ($rspta["direccion"] == '') ? 'Sin registrar' : $rspta["direccion"];
    $telefono = ($rspta["telefono"] == '') ? 'Sin registrar' : number_format($rspta["telefono"], 0, '', ' ');
    $email = ($rspta["email"] == '') ? 'Sin registrar' : $rspta["email"];

    require_once "../modelos/Salidas.php";
    $salida = new Salida();
    $rsptav = $salida->listarCabecera($_GET["id"]);
    $regv = $rsptav->fetch_object();

    $pdf = new PDF_Invoice('P', 'mm', 'A4');
    $pdf->AddPage();

    $pdf->addSociete(
      utf8_decode($empresa),
      $documento . "\n" .
        utf8_decode("Dirección: ") . utf8_decode($direccion) . "\n" .
        utf8_decode("Teléfono: ") . $telefono . "\n" .
        "Email: " . $email . "\n" .
        utf8_decode("Local: ") . $regv->local . "\n",
      '../files/logo_reportes/' . $logo,
      $ext_logo
    );

    $pdf->fact_dev(utf8_decode('Salida N° '), $regv->codigo);
    $pdf->temporaire("");
    $pdf->FancyTable("", "");
    $pdf->addDate($regv->fecha);

    if ($regv->tipo_movimiento == "personal") {
      $pdf->addClientAdresse(
        utf8_decode($regv->usuario),
        "Domicilio: " . utf8_decode($regv->direccion !== "" ? $regv->direccion : "Sin registrar."),
        $regv->tipo_documento . ": " . ($regv->num_documento !== "" ? $regv->num_documento : "Sin registrar."),
        "Email: " . utf8_decode($regv->email !== "" ? $regv->email : "Sin registrar."),
        "Telefono: " . utf8_decode($regv->telefono !== "" ? $regv->telefono : "Sin registrar.")
      );
    } else {
      $pdf->addClientAdresse5(
        "Nombre: " . utf8_decode($regv->maquinaria !== "" ? $regv->maquinaria : "Sin registrar."),
      );
    }

    $pdf->addClientAdresse2(
      utf8_decode($regv->autorizado == '' ? 'Sin registrar' : $regv->autorizado),
      // utf8_decode($regv->entregado == '' ? 'Sin registrar' : $regv->entregado),
      utf8_decode($regv->recibido == '' ? 'Sin registrar' : $regv->recibido),
      utf8_decode($regv->final == '' ? 'Sin registrar' : $regv->final)
    );

    $cols = array(
      "CODIGO" => 30,
      "NOMBRE" => 90,
      "U. MEDIDA" => 40,
      "CANTIDAD" => 30
    );

    $pdf->addCols($cols);

    $cols = array(
      "CODIGO" => "L",
      "NOMBRE" => "L",
      "U. MEDIDA" => "C",
      "CANTIDAD" => "C",
    );
    $pdf->addLineFormat($cols);
    $y = 89;

    $rsptad = $salida->listarDetalle($_GET["id"]);
    $total = 0;

    while ($regd = $rsptad->fetch_object()) {
      $line = array(
        "CODIGO" => "$regd->codigo_producto",
        "NOMBRE" => utf8_decode("$regd->articulo"),
        "U. MEDIDA" => utf8_decode("$regd->medida"),
        "CANTIDAD" => "$regd->cantidad"
      );
      $size = $pdf->addLine($y, $line);
      $y   += $size + 2;

      $total = $total + round($regd->cantidad);
    }

    $formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
    $izquierda = intval(floor($total));
    $derecha = intval(($total - floor($total)) * 100);

    $texto = $formatterES->format($izquierda) . " ARTÍCULOS EN TOTAL.";
    $textoEnMayusculas = mb_strtoupper($texto, 'UTF-8');

    $pdf->addCadreTVAs("---" . utf8_decode($textoEnMayusculas));

    $pdf->addTVAs($total);
    $pdf->addCadreEurosFrancs();
    $pdf->Output('Reporte de Salidas.pdf', 'I');
  } else {
    echo 'No tiene permiso para visualizar el reporte.';
  }
}
ob_end_flush();
