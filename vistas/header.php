<?php
if (strlen(session_id()) < 1)
  session_start();

$nombre_login = $_SESSION['nombre'];
$cargo_login = $_SESSION['cargo_detalle'];
$local_login = $_SESSION['local'];
?>

<style>
  .skin-blue-light .main-header .navbar .sidebar-toggle {
    transition: .3s ease all;
  }

  .skin-blue-light .main-header .navbar .sidebar-toggle:hover {
    transition: .3s ease all;
    background: #0234ac !important;
  }

  .skin-blue-light .main-header .navbar .nav>li>a {
    transition: .3s ease all;
  }

  .skin-blue-light .main-header .navbar .nav>li>a:hover {
    transition: .3s ease all;
    background: #0234ac !important;
  }

  .sidebar-menu .fa {
    color: #3e79fd;
  }

  .sidebar-menu .pull-right {
    color: #Fa7d1e;
    font-weight: bold;
    transition: .3s ease all;
  }

  .btn-bcp,
  .btn-danger,
  .btn-warning,
  .btn-info,
  .btn-success,
  .btn-secondary {
    transition: .3s ease all;
    border: 0px !important;
  }

  .btn-bcp.focus,
  .btn-danger.focus,
  .btn-warning.focus,
  .btn-info.focus,
  .btn-success.focus,
  .btn-secondary.focus {
    color: white !important;
    text-decoration: none;
  }

  .btn-bcp:focus,
  .btn-danger:focus,
  .btn-warning:focus,
  .btn-info:focus,
  .btn-success:focus,
  .btn-secondary:focus {
    color: white !important;
    text-decoration: none;
  }

  .main-header .navbar-custom-menu a,
  .main-header .navbar-right a {
    color: white !important;
  }

  .btn-warning {
    background-color: #Fa7d1e !important;
    color: while !important;
  }

  .btn-warning:hover {
    background-color: #ff961f !important;
  }

  .btn-default {
    background-color: #ffffff !important;
    transition: .3s ease all;
    border-color: #ccc;
  }

  .btn-default.disabled {
    background-color: #eeeeee !important;
    transition: .3s ease all;
    opacity: 1 !important;
  }

  .modal-footer .btn+.btn,
  .btn-bcp {
    background-color: #3e79fd !important;
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    transition: .3s ease all;
    color: white;
  }

  .modal-footer .btn+.btn:hover,
  .btn-bcp:hover {
    background-color: #5288ff !important;
    transition: .3s ease all !important;
    color: white !important;
  }

  .btn-bcp.disabled {
    background-color: #5288ff !important;
    transition: .3s ease all;
    opacity: 1 !important;
  }

  .nowrap-cell {
    white-space: nowrap;
  }

  .two-row {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  body {
    padding-right: 0 !important
  }

  .table-responsive {
    border: none !important;
  }

  #total2,
  #total {
    font-weight: bold;
  }

  .box {
    box-shadow: none !important;
  }
</style>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- avoid cache -->
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <title>ALMACENES S.A.C</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../public/css/font-awesome.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
  <!-- Lightbox style -->
  <link href="../public/glightbox/css/glightbox.min.css" rel="stylesheet" asp-append-version="true">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../public/css/_all-skins.min.css">
  <link rel="apple-touch-icon" href="../public/img/apple-touch-icon.png">
  <link rel="shortcut icon" href="../public/img/favicon.ico">

  <!-- DATATABLES -->
  <link rel="stylesheet" type="text/css" href="../public/datatables/jquery.dataTables.min.css">
  <link href="../public/datatables/buttons.dataTables.min.css" rel="stylesheet" />
  <link href="../public/datatables/responsive.dataTables.min.css" rel="stylesheet" />

  <link rel="stylesheet" type="text/css" href="../public/css/bootstrap-select.min.css">
</head>

<body class="hold-transition skin-blue-light sidebar-mini" style="padding: 0 !important;">
  <div class="wrapper">

    <header class="main-header" style="box-shadow: 0px 0px 15px -7px; position: sticky !important; width: 100%">
      <a href="escritorio.php" class="logo" style="color: white !important; background-color: #002a8e !important;">
        <span class="logo-mini"><b>A.</b></span>
        <span class="logo-lg" style="font-size: 15px;"><b>ALMACENES S.A.C</b></span>
      </a>
      <nav class="navbar" role="navigation" style="background-color: #002a8e !important;">
        <div style="display: flex; align-items: center; float: left;">
          <a href="#" class="sidebar-toggle" style="background: #002a8e; color: white !important;" data-toggle="offcanvas" role="button">
            <span class="sr-only">Navegación</span>
          </a>
        </div>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu" style="background: #002a8e !important; display: inline-flex; align-items: center; height: 50px;">
              <span style="color: white !important;" class="hidden-xs user-info local"><?php echo '<strong> Local: ' . $local_login . '</strong>' ?></span>
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: white !important; height: 50px;">
                <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="user-image" alt="User Image">
                <span class="hidden-xs user-info user"><?php echo $nombre_login; ?> - <?php echo '<strong> Rol: ' . $cargo_login . '</strong>' ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header" style="background: #002a8e !important;">
                  <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">
                  <p style="color: white !important;">
                    ALMACENES S.A.C
                    <small>nuestro contacto: +51 937 075 845</small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-right">
                    <a href="../ajax/usuario.php?op=salir" class="btn btn-warning btn-flat" onclick="destruirSession()">Cerrar sesión</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <aside class="main-sidebar">
      <section class="sidebar">
        <ul class="sidebar-menu">
          <li class="header"></li>
          <?php
          if ($_SESSION['escritorio'] == 1) {
            echo '<li id="mEscritorio">
              <a href="escritorio.php">
                <i class="fa fa-tasks"></i> <span>Escritorio</span>
              </a>
            </li>';
          }
          ?>

          <?php
          if ($_SESSION['almacen'] == 1) {
            echo '<li id="mAlmacen" class="treeview">
              <a href="#">
                <i class="fa fa-laptop"></i>
                <span>Almacén</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lArticulos"><a href="articulo.php"><i class="fa fa-circle-o"></i> Productos</a></li>
            ';
            if ($_SESSION['cargo'] == "superadmin") {
              echo '<li id="lArticuloExterno"><a href="articuloExterno.php"><i class="fa fa-circle-o"></i> Productos externos</a></li>';
            }
            echo '<li id="lCategorias"><a href="categoria.php"><i class="fa fa-circle-o"></i> Categorías</a></li>
                <li id="lLocales"><a href="locales.php"><i class="fa fa-circle-o"></i> Mi local</a></li>
                <li id="lMarcas"><a href="marcas.php"><i class="fa fa-circle-o"></i> Marcas</a></li>
                <li id="lMedidas"><a href="medidas.php"><i class="fa fa-circle-o"></i> Unidades de medidas</a></li>
              </ul>
            </li>';
          }
          ?>

          <?php
          if ($_SESSION['entradas'] == 1) {
            echo '<li id="mEntradas" class="treeview">
              <a href="#">
                <i class="fa fa-sign-in"></i>
                <span>Entradas</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lEntradas"><a href="entradas.php"><i class="fa fa-circle-o"></i> Entradas</a></li>
                <li id="lTipos"><a href="tipos.php"><i class="fa fa-circle-o"></i> Tipos</a></li>
                <li id="lProveedores"><a href="proveedores.php"><i class="fa fa-circle-o"></i> Proveedores</a></li>
                </ul>
            </li>';
          }
          ?>

          <?php
          if ($_SESSION['salidas'] == 1) {
            echo '<li id="mSalidas" class="treeview">
              <a href="#">
                <i style="margin-left: -6px; margin-right: 6px" class="fa fa-sign-in fa-flip-horizontal"></i>
                <span>Salidas</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lSalidas"><a href="salidas.php"><i class="fa fa-circle-o"></i> Salidas</a></li>
                <li id="lPersonales"><a href="personales.php"><i class="fa fa-circle-o"></i> Personales</a></li>
                <li id="lMaquinarias"><a href="maquinarias.php"><i class="fa fa-circle-o"></i> Maquinarias</a></li>
              </ul>
            </li>';
          }
          ?>

          <?php
          if ($_SESSION['acceso'] == 1) {
            echo '<li id="mAcceso" class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i> <span>Acceso</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li id="lUsuarios"><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                <li id="lPermisos"><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
              </ul>
            </li>';
          }
          ?>

          <?php
          if ($_SESSION['perfilu'] == 1) {
            echo '
          <li id="mPerfilUsuario" class="treeview">
            <a href="#">
              <i class="fa fa-user"></i> <span>Perfil de usuario</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li id="lConfUsuario"><a href="confUsuario.php"><i class="fa fa-circle-o"></i> Configuración de perfil</a></li>
              ';
            if ($_SESSION['cargo'] == "superadmin" || $_SESSION['cargo'] == "admin") {
              echo '
                <li id="lLocalesExternos"><a href="localesExternos.php"><i class="fa fa-circle-o"></i> Locales externos</a></li>
                ';
            }
            if ($_SESSION['cargo'] == "superadmin" || $_SESSION['cargo'] == "admin") {
              echo '
              <li id="lLocalesDisponibles"><a href="localesDisponibles.php"><i class="fa fa-circle-o"></i> Crear locales disponibles</a></li>
              ';
            }
            echo '
            </ul>
          </li>';
          }
          ?>

          <li>
            <a href="ayuda.php">
              <i class="fa fa-plus-square"></i> <span>Ayuda</span>
              <small class="label pull-right bg-red">PDF</small>
            </a>
          </li>
          <?php
          if ($_SESSION['cargo'] == "superadmin" || $_SESSION['cargo'] == "admin") {
          ?>
            <!-- <li id="sql_export">
              <a>
                <?php
                // if ($_POST) {
                //   if ($_POST["backup"]) {
                //     require("backup/backup.php");
                //     $backupdb = new backupdb();
                //   }
                // }
                ?>
                <form method="post" style="margin: 0 !important;">
                  <input type="hidden" value="true" name="backup">
                  <i class="fa fa-file"></i>
                  <input id="sql" type="submit" value="Exportar DB." style="border: none; background-color: transparent; outline: none;">
                </form>
                <small class="label pull-right bg-green">SQL</small>
              </a>
            </li> -->
            <div style="display: none;" id="rolUsuario"><?php echo $_SESSION['cargo'] ?></div>
          <?php
          }
          ?>
        </ul>
      </section>
    </aside>
    <script>
      function destruirSession() {
        sessionStorage.clear();
      }
    </script>