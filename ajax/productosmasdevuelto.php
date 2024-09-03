<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['reporteP'] == 1) {
?>
    <style>
      .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
        margin: 0 15px;
        margin-bottom: 8px;
      }

      .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 10px 12px;
        transition: 0.3s;
        font-size: 14px;
        border: 1px solid #ccc;
      }

      .tab button:hover {
        background-color: #ddd;
      }

      .tab button.active {
        background-color: #ccc;
        font-weight: 600;
      }

      .tabcontent {
        display: none;
        padding: 6px 12px;
        -webkit-animation: fadeEffect .7s;
        animation: fadeEffect .7s;
      }

      /* Fade in tabs */
      @-webkit-keyframes fadeEffect {
        from {
          opacity: 0;
        }

        to {
          opacity: 1;
        }
      }

      @keyframes fadeEffect {
        from {
          opacity: 0;
        }

        to {
          opacity: 1;
        }
      }
    </style>

    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="tab">
            <button class="tablinks active" onclick="changeTables(event, 'type_1')">Tipo 1 (Almacén de origen)</button>
            <button class="tablinks" onclick="changeTables(event, 'type_2')">Tipo 2 (Almacén de devolución)</button>
          </div>
          <div id="type_1" class="col-md-12 tabcontent">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">PRODUCTOS MÁS DEVUELTOS DEL <strong>ALMACÉN DE ORIGEN</strong>:</h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado_1" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                  <thead>
                    <th>Código de producto</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Ubicación del local</th>
                    <th>Stock normal</th>
                    <th>Imagen</th>
                    <th>Veces devueltos</th>
                    <th>Fecha de devolución</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>Código de producto</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Ubicación del local</th>
                    <th>Stock normal</th>
                    <th>Imagen</th>
                    <th>Veces devueltos</th>
                    <th>Fecha de devolución</th>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div id="type_2" class="col-md-12 tabcontent">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">PRODUCTOS MÁS DEVUELTOS DEL <strong>ALMACÉN DE DEVOLUCIÓN</strong>:</h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body table-responsive" id="listadoregistros">
                <table id="tbllistado_2" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                  <thead>
                    <th>Código de producto</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Ubicación del local</th>
                    <th>Stock normal</th>
                    <th>Imagen</th>
                    <th>Veces devueltos</th>
                    <th>Fecha de devolución</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>Código de producto</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Ubicación del local</th>
                    <th>Stock normal</th>
                    <th>Imagen</th>
                    <th>Veces devueltos</th>
                    <th>Fecha de devolución</th>
                  </tfoot>
                </table>
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
  <script>
    function changeTables(e, table) {
      var i, tabcontent, tablinks;

      tabcontent = document.getElementsByClassName("tabcontent");
      tablinks = document.getElementsByClassName("tablinks");

      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }

      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }

      document.getElementById(table).style.display = "block";
      e.currentTarget.className += " active";
    }

    document.getElementById("type_1").style.display = "block";
  </script>
  <script type="text/javascript" src="scripts/productosmasdevuelto11.js"></script>
<?php
}
ob_end_flush();
?>