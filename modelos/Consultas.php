<?php
require "../config/Conexion.php";

class Consultas
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    public function totalentradahoy()
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM entradas";
        return ejecutarConsulta($sql);
    }

    public function totalsalidahoy()
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM salidas";
        return ejecutarConsulta($sql);
    }

    public function entradasultimos_10dias()
    {
        ejecutarConsulta("SET lc_time_names = 'es_ES'");
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%d-%M') AS fecha, COUNT(*) AS total FROM entradas WHERE fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) GROUP BY DATE(fecha_hora) ORDER BY fecha_hora ASC LIMIT 30";
        return ejecutarConsulta($sql);
    }

    public function salidasultimos_10dias()
    {
        ejecutarConsulta("SET lc_time_names = 'es_ES'");
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%d-%M') AS fecha, COUNT(*) AS total FROM salidas WHERE fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) GROUP BY DATE(fecha_hora) ORDER BY fecha_hora ASC LIMIT 30";
        return ejecutarConsulta($sql);
    }

    public function totalentradahoyUsuario($idlocal)
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM entradas WHERE idlocal = '$idlocal'";
        return ejecutarConsulta($sql);
    }

    public function totalsalidahoyUsuario($idlocal)
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM salidas WHERE idlocal = '$idlocal'";
        return ejecutarConsulta($sql);
    }

    public function entradasultimos_10diasUsuario($idlocal)
    {
        ejecutarConsulta("SET lc_time_names = 'es_ES'");
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%d-%M') AS fecha, COUNT(*) AS total FROM entradas WHERE idlocal = '$idlocal' AND fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) GROUP BY DATE(fecha_hora) ORDER BY fecha_hora ASC LIMIT 30";
        return ejecutarConsulta($sql);
    }

    public function salidasultimos_10diasUsuario($idlocal)
    {
        ejecutarConsulta("SET lc_time_names = 'es_ES'");
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%d-%M') AS fecha, COUNT(*) AS total FROM salidas WHERE idlocal = '$idlocal' AND fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) GROUP BY DATE(fecha_hora) ORDER BY fecha_hora ASC LIMIT 30";
        return ejecutarConsulta($sql);
    }

    // artículos más devueltos

    public function articulosmasdevueltos_tipo1()
    {
        $sql = "SELECT 
				  dd.iddevolucion,
				  a.idcategoria as idcategoria,
				  c.titulo as categoria,
				  m.titulo as marca,
				  al.titulo as local,
				  a.codigo as codigo,
				  a.codigo_producto as codigo_producto,
				  d.codigo_pedido as codigo_pedido,
				  a.nombre as nombre,
				  a.stock as stock,
				  a.descripcion as descripcion,
				  a.imagen as imagen,
				  dd.cantidad_devuelta,
				  DATE_FORMAT(dd.fecha_hora, '%d-%m-%Y %H:%i:%s') AS fecha
				FROM detalle_devolucion dd
				LEFT JOIN articulo a ON dd.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN locales al ON a.idlocal = al.idlocal
				LEFT JOIN marcas m ON a.idmarca = m.idmarca
				LEFT JOIN devolucion d ON dd.iddevolucion = d.iddevolucion
				WHERE dd.opcion = 1
				AND a.eliminado = '0'
                ORDER BY dd.iddevolucion DESC";

        return ejecutarConsulta($sql);
    }

    public function articulosmasdevueltos_tipo2()
    {
        $sql = "SELECT 
				  dd.iddetalle_devolucion,
				  dd.iddevolucion,
				  a.idcategoria as idcategoria,
				  c.titulo as categoria,
				  m.titulo as marca,
				  al.titulo as local,
				  a.codigo as codigo,
				  a.codigo_producto as codigo_producto,
				  d.codigo_pedido as codigo_pedido,
				  a.nombre as nombre,
				  a.stock as stock,
				  a.descripcion as descripcion,
				  a.imagen as imagen,
				  dd.cantidad_devuelta,
				  DATE_FORMAT(dd.fecha_hora, '%d-%m-%Y %H:%i:%s') AS fecha
				FROM detalle_devolucion dd
				LEFT JOIN articulo a ON dd.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN locales al ON a.idlocal = al.idlocal
				LEFT JOIN marcas m ON a.idmarca = m.idmarca
				LEFT JOIN devolucion d ON dd.iddevolucion = d.iddevolucion
				WHERE dd.opcion = 2
				AND a.eliminado = '0'
                ORDER BY dd.iddevolucion DESC";

        return ejecutarConsulta($sql);
    }

    public function reparar($iddetalle_devolucion)
    {
        $sql = "UPDATE detalle_devolucion SET opcion = '1', cantidad_a_devolver = cantidad_devuelta WHERE iddetalle_devolucion = '$iddetalle_devolucion'";
        return ejecutarConsulta($sql);
    }
}
