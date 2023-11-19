<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['perfilu'] == 1) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage();

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(45, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE LOCALES', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, utf8_decode('Local'), 1, 0, 'C', 1);
    $pdf->Cell(110, 6, utf8_decode('Descripción'), 1, 0, 'C', 1);
    $pdf->Cell(40, 6, utf8_decode('Fecha y hora'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Locales.php";
    $locales = new Local();

    $idusuario = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin" || $cargo == "admin") {
      $rspta = $locales->listar();
    } else {
      $rspta = $locales->listarPorUsuario($idusuario);
    }

    $pdf->SetWidths(array(40, 110, 40));

    while ($reg = $rspta->fetch_object()) {
      $titulo = $reg->titulo;
      $descripcion = $reg->descripcion;
      $fecha = $reg->fecha;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($titulo), utf8_decode($descripcion), utf8_decode($fecha)));
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