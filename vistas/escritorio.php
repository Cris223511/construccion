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

    $idusuario = $_SESSION['idusuario'];
    $cargo = $_SESSION["cargo"];

    if ($cargo == "superadmin") {
      $rsptaE = $consulta->totalentradahoy();
      $rsptaS = $consulta->totalsalidahoy();
      $entrada10 = $consulta->entradasultimos_10dias();
      $salidas12 = $consulta->salidasultimos_12meses();
    } else {
      $rsptaE = $consulta->totalentradahoyUsuario($idusuario);
      $rsptaS = $consulta->totalsalidahoyUsuario($idusuario);
      $entrada10 = $consulta->entradasultimos_10diasUsuario($idusuario);
      $salidas12 = $consulta->salidasultimos_12mesesUsuario($idusuario);
    }

    $regE = $rsptaE->fetch_object();
    $totalE = $regE->cantidad;

    $regS = $rsptaS->fetch_object();
    $totalS = $regS->cantidad;

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
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Escritorio </h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h4 style="font-size:17px;">
                        <strong>Total: <?php echo $totalE; ?></strong>
                      </h4>
                      <p>Entradas</p>
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
                      <h4 style="font-size:17px;">
                        <strong>Total: <?php echo $totalS; ?></strong>
                      </h4>
                      <p>Salidas</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="salidas.php" class="small-box-footer">Salidas <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Entradas en los últimos 10 días
                    </div>
                    <div class="box-body">
                      <canvas id="entradas" width="400" height="300"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      Salidas en los últimos 12 meses
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
  <?php
  } else {
    require 'noacceso.php';
  }

  require 'footer.php';
  ?>

  <script src="../public/js/chart.min.js"></script>
  <script src="../public/js/Chart.bundle.min.js"></script>
  <script type="text/javascript">
    var ctx = document.getElementById("entradas").getContext('2d');
    var entradas = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php echo $fechasE; ?>],
        datasets: [{
          label: 'Entradas en los últimos 10 días',
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
          label: 'Salidas en los últimos 12 meses',
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
}
ob_end_flush();
?>