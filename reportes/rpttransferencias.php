<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['transferencias'] == 1) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage('');

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(45, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE TRANSFERENCIAS', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(33, 6, utf8_decode('N° de documento'), 1, 0, 'C', 1);
    $pdf->Cell(38, 6, utf8_decode('Cantidad transferida'), 1, 0, 'C', 1);
    $pdf->Cell(42, 6, utf8_decode('Local Origen'), 1, 0, 'C', 1);
    $pdf->Cell(42, 6, utf8_decode('Local Destino'), 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Fecha y hora'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Transferencias.php";
    $transferencia = new Transferencia();

    $idusuario = $_SESSION["idusuario"];
    $idlocalSession = $_SESSION["idlocal"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rspta = $transferencia->listar();
    } else {
      $rspta = $transferencia->listarPorUsuario($idlocalSession);
    }

    $pdf->SetWidths(array(33, 38, 42, 42, 35));

    while ($reg = $rspta->fetch_object()) {
      $codigo = (($reg->codigo != "") ? $reg->codigo : "Sin registrar.");
      $total_cantidad = $reg->total_cantidad;
      $origen = $reg->origen;
      $destino = $reg->destino;
      $fecha_hora = $reg->fecha;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array($codigo, $total_cantidad, utf8_decode($origen), utf8_decode($destino), $fecha_hora));
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