<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['almacen'] == 1) {

    //Inlcuímos a la clase PDF_MC_Table
    require('PDF_MC_Table.php');

    //Instanciamos la clase para generar el documento pdf
    $pdf = new PDF_MC_Table();

    //Agregamos la primera página al documento pdf
    $pdf->AddPage('L');

    //Seteamos el inicio del margen superior en 25 pixeles 
    $y_axis_initial = 25;

    //Seteamos el tipo de letra y creamos el título de la página. No es un encabezado no se repetirá
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(200, 6, 'LISTA DE ARTICULOS', 1, 0, 'C');
    $pdf->Ln(10);

    //Creamos las celdas para los títulos de cada columna y le asignamos un fondo gris y el tipo de letra
    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, 'Nombre', 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Categoría'), 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Código barra'), 1, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Código producto'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, utf8_decode('Stock normal'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, utf8_decode('Stock mínimo'), 1, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('P. compra'), 1, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('P. venta'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    //Comenzamos a crear las filas de los registros según la consulta mysql
    require_once "../modelos/Articulo.php";
    $articulo = new Articulo();

    $rspta = $articulo->listar();

    //Table with rows and columns
    $pdf->SetWidths(array(60, 35, 35, 35, 25, 25, 30, 30));

    while ($reg = $rspta->fetch_object()) {
      $nombre = $reg->nombre;
      $categoria = $reg->categoria;
      $codigo_barra = $reg->codigo;
      $codigo_producto = $reg->codigo_producto;
      $stock = $reg->stock;
      $stock_minimo = $reg->stock_minimo;
      $precio_compra = $reg->precio_compra;
      $precio_venta = $reg->precio_venta;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($nombre), utf8_decode($categoria), $codigo_barra, $codigo_producto, $stock, $stock_minimo, $precio_compra, $precio_venta));
    }

    //Mostramos el documento pdf
    $pdf->Output();

?>
<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>