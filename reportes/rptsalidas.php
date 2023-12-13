<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['salidas'] == 1) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage('L');

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(200, 6, 'LISTA DE SALIDAS', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(39.5, 6, utf8_decode('Tipo'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('N° documento'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('Autorizado por'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('Entregado por'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('Recibido por'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('Usuario final'), 1, 0, 'C', 1);
    $pdf->Cell(39.5, 6, utf8_decode('Estado'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Salidas.php";
    $salida = new Salida();

    $idusuario = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rspta = $salida->listar();
    } else {
      $rspta = $salida->listarPorUsuario($idusuario);
    }

    $pdf->SetWidths(array(39.5, 39.5, 39.5, 39.5, 39.5, 39.5, 39.5));

    while ($reg = $rspta->fetch_object()) {
      $tipo = $reg->tipo;
      $codigo = $reg->codigo;
      $autorizado = !empty($reg->autorizado) ? $reg->autorizado : "Sin registrar";
      $usuario = $reg->usuario;
      $recibido = !empty($reg->recibido) ? $reg->recibido : "Sin registrar";
      $final = !empty($reg->final) ? $reg->final : "Sin registrar";
      $estado = $reg->estado;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($tipo), utf8_decode($codigo), utf8_decode($autorizado), utf8_decode($usuario), utf8_decode($recibido), utf8_decode($final), $estado));
    }

    $pdf->Output();

?>
<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>