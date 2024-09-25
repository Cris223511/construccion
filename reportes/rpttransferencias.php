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

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE ENTRADAS', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(47.5, 6, utf8_decode('Tipo'), 1, 0, 'C', 1);
    $pdf->Cell(47.5, 6, utf8_decode('N° de documento'), 1, 0, 'C', 1);
    $pdf->Cell(47.5, 6, utf8_decode('Proveedor'), 1, 0, 'C', 1);
    $pdf->Cell(47.5, 6, utf8_decode('Estado'), 1, 0, 'C', 1);

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

    $pdf->SetWidths(array(47.5, 47.5, 47.5, 47.5));

    while ($reg = $rspta->fetch_object()) {
      $tipo = $reg->tipo;
      $codigo = (($reg->codigo != "") ? $reg->codigo : "Sin registrar.");
      $proveedor = $reg->proveedor;
      $estado = $reg->estado;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($tipo), $codigo, utf8_decode($proveedor), $estado));
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