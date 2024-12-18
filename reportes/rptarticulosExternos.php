<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['almacen'] == 1 && ($_SESSION["cargo"] == "superadmin" || $_SESSION["cargo"] == "mirador")) {

    require('PDF_MC_Table.php');

    $pdf = new PDF_MC_Table();

    $pdf->AddPage('L');

    $y_axis_initial = 25;

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(45, 6, '', 0, 0, 'C');
    $pdf->Cell(200, 6, 'LISTA DE ARTICULOS', 1, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, 'Nombre', 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Categoría'), 1, 0, 'C', 1);
    $pdf->Cell(60, 6, utf8_decode('Local'), 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Código barra'), 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Código producto'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, utf8_decode('Stock normal'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, utf8_decode('Stock mínimo'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    require_once "../modelos/Articulo.php";
    $articulo = new Articulo();

    $idusuario = $_SESSION["idusuario"];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rspta = $articulo->listar();
    } else {
      $rspta = $articulo->listarPorUsuario($idusuario);
    }

    $pdf->SetWidths(array(60, 35, 60, 35, 35, 25, 25));

    while ($reg = $rspta->fetch_object()) {
      $nombre = $reg->nombre;
      $categoria = $reg->categoria;
      $local = $reg->local;
      $codigo_barra = (($reg->codigo != "") ? $reg->codigo : "Sin registrar.");
      $codigo_producto = $reg->codigo_producto;
      $stock = $reg->stock;
      $stock_minimo = $reg->stock_minimo;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($nombre), utf8_decode($categoria), utf8_decode($local), $codigo_barra, $codigo_producto, $stock, $stock_minimo));
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