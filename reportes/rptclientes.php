<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['personas'] == 1) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage();

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(45, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE CLIENTES', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(35, 6, 'Nombre', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'Documento', 1, 0, 'C', 1);
    $pdf->Cell(28, 6, utf8_decode('Número'), 1, 0, 'C', 1);
    $pdf->Cell(22, 6, utf8_decode('Teléfono'), 1, 0, 'C', 1);
    $pdf->Cell(51, 6, 'Email', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'Fecha Nac.', 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Clientes.php";
    $clientes = new Cliente();

    $idusuario = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin" || $cargo == "admin") {
      $rspta = $clientes->listarClientesFechaNormal();
    } else {
      $rspta = $clientes->listarclientesFechaNormalPorUsuario($idusuario);
    }

    $pdf->SetWidths(array(35, 25, 28, 22, 51, 25));

    while ($reg = $rspta->fetch_object()) {
      $nombre = $reg->nombre;
      $tipo_documento = $reg->tipo_documento;
      $num_documento = $reg->num_documento;
      $telefono = $reg->telefono;
      $email = $reg->email;
      $fecha = $reg->fecha;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($nombre), $tipo_documento, $num_documento, $telefono, $email, utf8_decode($fecha)));
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