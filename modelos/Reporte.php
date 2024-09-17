<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Reporte
{
    /* ======================= REPORTE DE ENTRADAS ======================= */

    public function listarEntradas($condiciones = "")
    {
        $sql = "SELECT
                  de.identrada,
                  de.idarticulo,
                  de.cantidad,
                  de.precio_compra,
                  a.nombre as nombre,
				  a.stock as stock,
				  a.imagen as imagen,
				  a.codigo_producto as codigo_producto,
                  u.nombre as usuario,
                  u.cargo,
				  al.titulo as local,
                  e.codigo,
                  e.ubicacion,
                  DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha
                FROM detalle_entrada de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				LEFT JOIN entradas e ON de.identrada = e.identrada
				LEFT JOIN locales al ON a.idlocal = al.idlocal
                LEFT JOIN usuario u ON e.idusuario = u.idusuario
                $condiciones
                ORDER BY de.identrada DESC";
        return ejecutarConsulta($sql);
    }

    public function listarEntradasLocal($idlocal, $condiciones = "")
    {
        $sql = "SELECT
                  de.identrada,
                  de.idarticulo,
                  de.cantidad,
                  de.precio_compra,
                  a.nombre as nombre,
				  a.stock as stock,
				  a.imagen as imagen,
				  a.codigo_producto as codigo_producto,
                  u.nombre as usuario,
                  u.cargo,
				  al.titulo as local,
                  e.codigo,
                  e.ubicacion,
                  DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha
                FROM detalle_entrada de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				LEFT JOIN entradas e ON de.identrada = e.identrada
				LEFT JOIN locales al ON a.idlocal = al.idlocal
                LEFT JOIN usuario u ON e.idusuario = u.idusuario
                $condiciones
                AND c.idlocal = '$idlocal'
                ORDER BY de.identrada DESC";
        return ejecutarConsulta($sql);
    }

    /* ======================= REPORTE DE ENTRADAS ======================= */

    public function listarSalidas($condiciones = "")
    {
        $sql = "SELECT
                  ds.idsalida,
                  ds.idarticulo,
                  ds.cantidad,
                  ds.precio_compra,
                  a.nombre as nombre,
				  a.stock as stock,
				  a.imagen as imagen,
				  a.codigo_producto as codigo_producto,
                  u.nombre as usuario,
                  u.cargo,
				  al.titulo as local,
                  s.codigo,
                  s.ubicacion,
                  DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha
                FROM detalle_salida ds
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				LEFT JOIN salidas s ON ds.idsalida = s.idsalida
				LEFT JOIN locales al ON a.idlocal = al.idlocal
                LEFT JOIN usuario u ON s.idusuario = u.idusuario
                $condiciones
                ORDER BY ds.idsalida DESC";
        return ejecutarConsulta($sql);
    }

    public function listarSalidasLocal($idlocal, $condiciones = "")
    {
        $sql = "SELECT
                  ds.idsalida,
                  ds.idarticulo,
                  ds.cantidad,
                  ds.precio_compra,
                  a.nombre as nombre,
				  a.stock as stock,
				  a.imagen as imagen,
				  a.codigo_producto as codigo_producto,
                  u.nombre as usuario,
                  u.cargo,
				  al.titulo as local,
                  s.codigo,
                  s.ubicacion,
                  DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha
                FROM detalle_salida ds
				LEFT JOIN articulo a ON ds.idarticulo = a.idarticulo
				LEFT JOIN salidas s ON ds.idsalida = s.idsalida
				LEFT JOIN locales al ON a.idlocal = al.idlocal
                LEFT JOIN usuario u ON s.idusuario = u.idusuario
                $condiciones
                AND c.idlocal = '$idlocal'
                ORDER BY ds.idsalida DESC";
        return ejecutarConsulta($sql);
    }

    /* ======================= REPORTE DE COMBINACIONES ======================= */

    public function listarCombinaciones($condiciones = "")
    {
        $sql = "SELECT 
                  a.idarticulo,
                  a.nombre AS nombre,
                  a.codigo_producto,
                  a.stock AS stock,
                  a.imagen,
                  SUM(de.cantidad) AS cantidad_entrada,
                  SUM(ds.cantidad) AS cantidad_salida,
                  e.codigo AS codigo_entrada,
                  s.codigo AS codigo_salida,
                  e.fecha_hora AS fecha_entrada,
                  s.fecha_hora AS fecha_salida
                FROM articulo a
                LEFT JOIN detalle_entrada de ON a.idarticulo = de.idarticulo
                LEFT JOIN entradas e ON de.identrada = e.identrada
                LEFT JOIN detalle_salida ds ON a.idarticulo = ds.idarticulo
                LEFT JOIN salidas s ON ds.idsalida = s.idsalida
                $condiciones
                GROUP BY a.idarticulo
                HAVING cantidad_entrada IS NOT NULL AND cantidad_salida IS NOT NULL
                ORDER BY a.idarticulo ASC";

        return ejecutarConsulta($sql);
    }

    public function listarCombinacionesLocal($idlocal, $condiciones = "")
    {
        $sql = "SELECT 
                  a.idarticulo,
                  a.nombre AS nombre,
                  a.codigo_producto,
                  a.stock AS stock,
                  a.imagen,
                  SUM(de.cantidad) AS cantidad_entrada,
                  SUM(ds.cantidad) AS cantidad_salida,
                  e.codigo AS codigo_entrada,
                  s.codigo AS codigo_salida,
                  e.fecha_hora AS fecha_entrada,
                  s.fecha_hora AS fecha_salida
                FROM articulo a
                LEFT JOIN detalle_entrada de ON a.idarticulo = de.idarticulo
                LEFT JOIN entradas e ON de.identrada = e.identrada
                LEFT JOIN detalle_salida ds ON a.idarticulo = ds.idarticulo
                LEFT JOIN salidas s ON ds.idsalida = s.idsalida
                LEFT JOIN locales l ON a.idlocal = l.idlocal
                $condiciones
                AND l.idlocal = '$idlocal'
                GROUP BY a.idarticulo
                HAVING cantidad_entrada IS NOT NULL AND cantidad_salida IS NOT NULL
                ORDER BY a.idarticulo ASC";

        return ejecutarConsulta($sql);
    }
}
