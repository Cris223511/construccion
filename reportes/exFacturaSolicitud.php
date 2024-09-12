<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['prestamo'] == 1) {
    //Incluímos el archivo FacturaSolicitud.php
    require('FacturaSolicitud.php');

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

    //Obtenemos los datos de la cabecera de la solicitud actual
    require_once "../modelos/Solicitudes.php";
    $solicitud = new Solicitud();
    $rsptas = $solicitud->solicitudcabecera($_GET["id"]);
    //Recorremos todos los valores obtenidos
    $regs = $rsptas->fetch_object();

    //Establecemos la configuración de la factura
    $pdf = new PDF_Invoice('P', 'mm', 'A4');
    $pdf->AddPage();

    //Enviamos los datos de la empresa al método addSociete de la clase Factura
    $pdf->addSociete(
      utf8_decode($empresa),
      $documento . "\n" .
        utf8_decode("Dirección: ") . utf8_decode($direccion) . "\n" .
        utf8_decode("Teléfono: ") . $telefono . "\n" .
        "Email: " . $email,
      '../files/logo_reportes/' . $logo,
      $ext_logo
    );

    $pdf->fact_dev(utf8_decode("COD: N° $regs->codigo_pedido"));
    $pdf->fact_dev2(utf8_decode($regs->estado));

    $pdf->temporaire("");
    $pdf->FancyTable("", "");

    $pdf->addDate1(($regs->fecha_hora_despacho == "01-01-2000 00:00:00") ? "Sin registrar" : $regs->fecha_hora_despacho);
    $pdf->addDate2($regs->fecha_hora_pedido);

    //Enviamos los datos del ENCARGADO al método addClientAdresse de la clase Factura

    if (($regs->idalmacenero != 0 || $regs->idalmacenero != "0")) {
      //Enviamos los datos del DESPACHADOR al método addClientAdresse de la clase Factura
      $pdf->addClientAdresse1("Nombres: " . utf8_decode($regs->responsable_pedido), "Domicilio: " . utf8_decode($regs->direccion_pedido), $regs->tipo_documento_pedido . ": " . $regs->num_documento_pedido, "Email: " . $regs->email_pedido, "Telefono: " . $regs->telefono_pedido);
      $pdf->addClientAdresse2("Nombres: " . utf8_decode($regs->responsable_despacho), "Domicilio: " . utf8_decode($regs->direccion_despacho), $regs->tipo_documento_despacho . ": " . $regs->num_documento_despacho, "Email: " . $regs->email_despacho, "Telefono: " . $regs->telefono_despacho);
    } else {
      $pdf->addClientAdresse("Nombres: " . utf8_decode($regs->responsable_pedido), "Domicilio: " . utf8_decode($regs->direccion_pedido), $regs->tipo_documento_pedido . ": " . $regs->num_documento_pedido, "Email: " . $regs->email_pedido, "Telefono: " . $regs->telefono_pedido);
    }

    $regs->empresa = ($regs->empresa == '') ? 'Sin registrar.' : ($regs->empresa);
    $regs->telefono = ($regs->telefono == '') ? 'Sin registrar.' : number_format($regs->telefono, 0, '', ' ');
    $regs->destino = ($regs->destino == '') ? 'Sin registrar.' : ($regs->destino);

    $pdf->additionalInfo(utf8_decode($regs->empresa), utf8_decode($regs->telefono), utf8_decode($regs->destino));

    //Establecemos las columnas que va a tener la sección donde mostramos los detalles de la solicitud
    $cols = array(
      "CODIGO" => 34,
      "NOMBRE DE PRODUCTO" => 80,
      "CANTIDAD" => 28,
      "C. PRESTADA" => 28,
      "P.C." => 20
    );

    $pdf->addCols($cols);
    $cols = array(
      "CODIGO" => "L",
      "NOMBRE DE PRODUCTO" => "L",
      "CANTIDAD" => "C",
      "C. PRESTADA" => "C",
      "P.C." => "C"
    );

    $pdf->addLineFormat($cols);
    $pdf->addLineFormat($cols);

    //Actualizamos el valor de la coordenada "y", que será la ubicación desde donde empezaremos a mostrar los datos
    $y = 84;

    //Obtenemos todos los detalles de la solicitud actual
    $rsptad = $solicitud->solicituddetalle($_GET["id"]);
    $total_compra = 0;
    $total_cantidad = 0;

    while ($regd = $rsptad->fetch_object()) {
      $line = array(
        "CODIGO" => "$regd->codigo_producto",
        "NOMBRE DE PRODUCTO" => utf8_decode("$regd->nombre"),
        "CANTIDAD" => "$regd->cantidad",
        "C. PRESTADA" => "$regd->cantidad_prestada",
        "P.C." => $regd->precio_compra
      );
      $size = $pdf->addLine($y, $line);
      $y   += $size + 2;

      $total_compra = $total_compra + $regd->precio_compra;
      $total_cantidad = $total_cantidad + $regd->cantidad;
    }

    // Convertimos el total en letras
    // require_once "Letras.php";
    // $V = new EnLetras();
    // $con_letra = strtoupper($V->ValorEnLetras(floatval($total_compra), "NUEVOS SOLES"));
    // $pdf->addCadreTVAs("---" . $con_letra);

    $formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
    $izquierda = intval(floor($total_compra));
    $derecha = intval(($total_compra - floor($total_compra)) * 100);

    $texto = $formatterES->format($izquierda) . " NUEVOS SOLES CON " . $formatterES->format($derecha) . " CÉNTIMOS";
    $textoEnMayusculas = mb_strtoupper($texto, 'UTF-8');

    $pdf->addCadreTVAs("---" . utf8_decode($textoEnMayusculas));

    //Mostramos el impuesto
    $pdf->addTVAs($total_cantidad, $total_compra);
    $pdf->addCadreEurosFrancs();

    $pdf->Output('Reporte de Solicitud.pdf', 'I');
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>

<style>

</style>