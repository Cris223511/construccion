<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['prestamo'] == 1) {

    //Inlcuímos a la clase PDF_MC_Table
    require('PDF_MC_Table.php');

    //Instanciamos la clase para generar el documento pdf
    $pdf = new PDF_MC_Table();

    //Agregamos la primera página al documento pdf
    $pdf->AddPage();

    //Seteamos el inicio del margen superior en 25 pixeles 
    $y_axis_initial = 25;

    //Seteamos el tipo de letra y creamos el título de la página. No es un encabezado no se repetirá
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE DEVOLUCIONES', 1, 0, 'C');
    $pdf->Ln(10);

    //Creamos las celdas para los títulos de cada columna y le asignamos un fondo gris y el tipo de letra
    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Fecha pedido', 1, 0, 'C', 1);
    $pdf->Cell(40, 6, utf8_decode('Fecha devolución'), 1, 0, 'C', 1);
    $pdf->Cell(37, 6, 'Resp. Pedido', 1, 0, 'C', 1);
    $pdf->Cell(37, 6, 'Resp. Despacho', 1, 0, 'C', 1);
    $pdf->Cell(37, 6, utf8_decode('Código'), 1, 0, 'C', 1);

    $pdf->Ln(10);
    //Comenzamos a crear las filas de los registros según la consulta mysql
    require_once "../modelos/Devoluciones.php";
    $devolucion = new Devolucion();

    if (($_SESSION['cargo'] == 'superadmin' || $_SESSION['cargo'] == 'admin' || $_SESSION['cargo'] == 'almacenero'))
      $rspta = $devolucion->listar();
    else
      $rspta = $devolucion->listarUsuario($idencargado);

    //Table with rows and columns
    $pdf->SetWidths(array(40, 40, 37, 37, 37));

    while ($reg = $rspta->fetch_object()) {
      $fecha_hora_pedido = $reg->fecha_hora_pedido;
      $fecha_hora_devolucion = ($reg->fecha_hora_devolucion == "01-01-2000 00:00:00") ? "Sin registrar" : $reg->fecha_hora_devolucion;
      $responsable_pedido = ucwords($reg->responsable_pedido);
      $responsable_despacho = ucwords($reg->responsable_despacho);
      $codigo_pedido = 'N° ' . $reg->codigo_pedido;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($fecha_hora_pedido), utf8_decode($fecha_hora_devolucion), utf8_decode($responsable_pedido), utf8_decode($responsable_despacho), utf8_decode($codigo_pedido), utf8_decode($numero)));
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