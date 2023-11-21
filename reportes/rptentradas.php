<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['almacen'] == 1) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage('L');

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(200, 6, 'LISTA DE ENTRADAS', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, utf8_decode('Categoría'), 1, 0, 'C', 1);
    $pdf->Cell(40, 6, utf8_decode('Marca'), 1, 0, 'C', 1);
    $pdf->Cell(40, 6, utf8_decode('Tipo'), 1, 0, 'C', 1);
    $pdf->Cell(60, 6, utf8_decode('Proveedor'), 1, 0, 'C', 1);
    $pdf->Cell(40, 6, utf8_decode('Código'), 1, 0, 'C', 1);
    $pdf->Cell(27, 6, utf8_decode('Cantidad'), 1, 0, 'C', 1);
    $pdf->Cell(27, 6, utf8_decode('Estado'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Entradas.php";
    $entrada = new Entrada();

    $idusuario = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rspta = $entrada->listar();
    } else {
      $rspta = $entrada->listarPorUsuario($idusuario);
    }

    $pdf->SetWidths(array(40, 40, 40, 60, 40, 27, 27));

    while ($reg = $rspta->fetch_object()) {
      $categoria = $reg->categoria;
      $marca = $reg->marca;
      $tipo = $reg->tipo;
      $proveedor = $reg->proveedor;
      $codigo = $reg->codigo;
      $cantidad = $reg->cantidad;
      $estado = $reg->estado;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($categoria), utf8_decode($marca), utf8_decode($tipo), utf8_decode($proveedor), $codigo, $cantidad, $estado));
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