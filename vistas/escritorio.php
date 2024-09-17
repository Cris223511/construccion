<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['escritorio'] == 1) {
    require_once "../modelos/Consultas.php";
    $consulta = new Consultas();

    $idlocal = $_SESSION['idlocal'];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rsptaE = $consulta->totalentradahoy();
      $rsptaS = $consulta->totalsalidahoy();
      $entrada10 = $consulta->entradasultimos_10dias();
      $salidas12 = $consulta->salidasultimos_10dias();
    } else {
      $rsptaE = $consulta->totalentradahoyUsuario($idlocal);
      $rsptaS = $consulta->totalsalidahoyUsuario($idlocal);
      $entrada10 = $consulta->entradasultimos_10diasUsuario($idlocal);
      $salidas12 = $consulta->salidasultimos_10diasUsuario($idlocal);
    }

    $regE = $rsptaE->fetch_object();
    $totalE = $regE->total_entradas;
    $totalEE = $regE->cantidad_total_entradas;

    $regS = $rsptaS->fetch_object();
    $totalS = $regS->total_salidas;
    $totalSS = $regS->cantidad_total_salidas;

    $fechasE = '';
    $totalesE = '';

    while ($regfechaE = $entrada10->fetch_object()) {
      $fechasE = $fechasE . '"' . $regfechaE->fecha . '",';
      $totalesE = $totalesE . $regfechaE->total . ',';
    }

    $fechasE = substr($fechasE, 0, -1);
    $totalesE = substr($totalesE, 0, -1);

    $fechasS = '';
    $totalesS = '';

    while ($regfechaS = $salidas12->fetch_object()) {
      $fechasS = $fechasS . '"' . $regfechaS->fecha . '",';
      $totalesS = $totalesS . $regfechaS->total . ',';
    }

    $fechasS = substr($fechasS, 0, -1);
    $totalesS = substr($totalesS, 0, -1);
?>

    <style>
      @media (max-width: 991px) {
        .botones {
          width: 100% !important;
        }

        #labelCustom {
          display: none;
        }

        .listadoregistros {
          margin-bottom: 0;
        }
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Escritorio</h1>
                <div class="box-tools pull-right"></div>
              </div>
              <div class="panel-body table-responsive listadoregistros" style="overflow: visible; padding: 10px;">
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                  <label>Fecha Inicial:</label>
                  <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                  <label>Fecha Final:</label>
                  <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="padding: 5px; margin: 0px;">
                  <label id="labelCustom">ㅤ</label>
                  <div style="display: flex; gap: 10px;">
                    <button style="width: 100%; height: 34px;" class="btn btn-bcp" onclick="buscar()">Buscar</button>
                    <button style="height: 34px;" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
                  </div>
                </div>
              </div>
              <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="table-responsive listadoregistros2" style="overflow-x: visible; display: inline-block; width: 100%; padding-top: 20px !important; background-color: white; height: max-content;">
                  <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="small-box bg-green">
                      <div class="inner">
                        <div style="font-size: 15px; display: flex; flex-direction: column;">
                          <span style="margin-bottom: 5px;">Total de entradas:<strong id="total_entradas"> <?php echo $totalE; ?></strong></span>
                          <span>Cantidad total de productos:<strong id="cantidad_total_entradas"> <?php echo $totalEE; ?></strong></span>
                        </div>
                        <strong>
                          <p style="font-size: 17px; margin-top: 10px;">Entradas</p>
                        </strong>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                      <a href="entradas.php" class="small-box-footer">Entradas <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="small-box bg-red">
                      <div class="inner">
                        <div style="font-size: 15px; display: flex; flex-direction: column;">
                          <span style="margin-bottom: 5px;">Total de salidas:<strong id="total_salidas"> <?php echo $totalS; ?></strong></span>
                          <span>Cantidad total de productos:<strong id="cantidad_total_salidas"> <?php echo $totalSS; ?></strong></span>
                        </div>
                        <strong>
                          <p style="font-size: 17px; margin-top: 10px;">Salidas</p>
                        </strong>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                      <a href="salidas.php" class="small-box-footer">Salidas <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Entradas en los últimos 30 días
                    </div>
                    <div class="box-body">
                      <canvas id="entradas" width="400" height="300"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Salidas en los últimos 30 días
                    </div>
                    <div class="box-body">
                      <canvas id="salidas" width="400" height="300"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <script src="../public/js/chart.min.js"></script>
    <script src="../public/js/Chart.bundle.min.js"></script>
    <script type="text/javascript">
      var ctx = document.getElementById("entradas").getContext('2d');
      var entradas = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php echo $fechasE; ?>],
          datasets: [{
            label: 'Entradas en los últimos 30 días',
            data: [<?php echo $totalesE; ?>],
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgba(255,99,132,1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(255,99,132,1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });

      var ctx = document.getElementById("salidas").getContext('2d');
      var salidas = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php echo $fechasS; ?>],
          datasets: [{
            label: 'Salidas en los últimos 30 días',
            data: [<?php echo $totalesS; ?>],
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
              'rgba(255,99,132,1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(255,99,132,1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    </script>
<?php

  } else {
    require 'noacceso.php';
  }
}

require 'footer.php';
ob_end_flush();

?>
<script type="text/javascript" src="scripts/escritorio.js"></script>