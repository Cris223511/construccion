<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['entradas'] == 1) {
    require('Entrada.php');

    $logo = "logo.jpeg";
    $ext_logo = "jpg";
    $empresa = "Almacenes S.A.C.";
    $documento = "20477157772";
    $direccion = "Av Gerardo Unger 5689 - Los Olivos - Lima";
    $telefono = "998 393 220";

    require_once "../modelos/Entradas.php";
    $entrada = new Entrada();
    $rsptav = $entrada->listarCabecera($_GET["id"]);
    $regv = $rsptav->fetch_object();

    $pdf = new PDF_Invoice('P', 'mm', 'A4');
    $pdf->AddPage();

    $pdf->addSociete(
      utf8_decode($empresa),
      $documento . "\n" .
        utf8_decode("Dirección: ") . utf8_decode($direccion) . "\n" .
        utf8_decode("Teléfono: ") . $telefono . "\n" .
        utf8_decode("Local: ") . $regv->local . "\n",
      $logo,
      $ext_logo
    );
    $pdf->fact_dev(utf8_decode('Entrada N° '), $regv->codigo);
    $pdf->temporaire("");
    $pdf->FancyTable("", "");
    $pdf->addDate($regv->fecha);

    $pdf->addClientAdresse(
      utf8_decode($regv->nombre),
      "Domicilio: " . utf8_decode($regv->direccion !== "" ? $regv->direccion : "Sin registrar."),
      $regv->tipo_documento . ": " . ($regv->num_documento !== "" ? $regv->num_documento : "Sin registrar."),
      "Email: " . ($regv->email !== "" ? $regv->email : "Sin registrar."),
      "Telefono: " . ($regv->telefono !== "" ? $regv->telefono : "Sin registrar.")
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

    $rsptad = $entrada->listarDetalle($_GET["id"]);
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
    $pdf->Output('Reporte de Entradas.pdf', 'I');
  } else {
    echo 'No tiene permiso para visualizar el reporte.';
  }
}
ob_end_flush();
