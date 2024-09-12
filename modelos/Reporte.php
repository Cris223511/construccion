<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Reporte
{
    /* ======================= REPORTE DE ENTRADAS ======================= */

    public function listarEntradas($condiciones = "")
    {
        $sql = "SELECT DISTINCT
                  e.identrada,
                  e.idusuario,
                  u.nombre as usuario,
                  u.cargo as cargo,
                  u.cargo,
                  lo.titulo as local,
                  t.titulo as tipo,
                  p.nombre as proveedor,
                  e.codigo,
                  e.ubicacion,
                  e.descripcion,
                  DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
                  e.total_compra,
                  e.impuesto,
                  e.estado 
                FROM entradas e
                LEFT JOIN locales lo ON e.idlocal = lo.idlocal
                LEFT JOIN tipos t ON e.idtipo = t.idtipo
                LEFT JOIN proveedores p ON e.idproveedor = p.idproveedor
                LEFT JOIN usuario u ON e.idusuario = u.idusuario
                $condiciones
                ORDER BY e.identrada DESC";
        return ejecutarConsulta($sql);
    }

    public function listarEntradasLocal($idlocal, $condiciones = "")
    {
        $sql = "SELECT DISTINCT
                  e.identrada,
                  e.idusuario,
                  u.nombre as usuario,
                  u.cargo as cargo,
                  u.cargo,
                  lo.titulo as local,
                  t.titulo as tipo,
                  p.nombre as proveedor,
                  e.codigo,
                  e.ubicacion,
                  e.descripcion,
                  DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
                  e.total_compra,
                  e.impuesto,
                  e.estado 
                FROM entradas e
                LEFT JOIN locales lo ON e.idlocal = lo.idlocal
                LEFT JOIN tipos t ON e.idtipo = t.idtipo
                LEFT JOIN proveedores p ON e.idproveedor = p.idproveedor
                LEFT JOIN usuario u ON e.idusuario = u.idusuario
                $condiciones
                AND c.idlocal = '$idlocal'
                ORDER BY e.identrada DESC";
        return ejecutarConsulta($sql);
    }

    /* ======================= REPORTE DE ENTRADAS ======================= */

    public function listarSalidas($condiciones = "")
    {
        $sql = "SELECT
                  s.idsalida,
                  s.idusuario,
                  u.nombre as usuario,
                  u.cargo as cargo,
                  lo.titulo as local,
                  t.titulo as tipo,
                  ma.titulo as maquinaria,
                  pea.nombre AS autorizado,
                  per.nombre AS recibido,
                  s.codigo,
                  s.tipo_movimiento,
                  s.ubicacion,
                  s.descripcion,
                  s.descripcion,
                  DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
                  s.total_compra,
                  s.impuesto,
                  s.estado FROM salidas s
                LEFT JOIN locales lo ON s.idlocal=lo.idlocal
                LEFT JOIN tipos t ON s.idtipo=t.idtipo
                LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria
                LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal
                LEFT JOIN personales per ON s.idrecibido=per.idpersonal
                LEFT JOIN usuario u ON s.idusuario=u.idusuario
                $condiciones
                ORDER BY s.idsalida DESC";
        return ejecutarConsulta($sql);
    }

    public function listarSalidasLocal($idlocal, $condiciones = "")
    {
        $sql = "SELECT
                  s.idsalida,
                  s.idusuario,
                  u.nombre as usuario,
                  u.cargo as cargo,
                  lo.titulo as local,
                  t.titulo as tipo,
                  ma.titulo as maquinaria,
                  pea.nombre AS autorizado,
                  per.nombre AS recibido,
                  s.codigo,
                  s.tipo_movimiento,
                  s.ubicacion,
                  s.descripcion,
                  s.descripcion,
                  DATE_FORMAT(s.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,
                  s.total_compra,
                  s.impuesto,
                  s.estado FROM salidas s
                LEFT JOIN locales lo ON s.idlocal=lo.idlocal
                LEFT JOIN tipos t ON s.idtipo=t.idtipo
                LEFT JOIN maquinarias ma ON s.idmaquinaria=ma.idmaquinaria
                LEFT JOIN personales pea ON s.idautorizado=pea.idpersonal
                LEFT JOIN personales per ON s.idrecibido=per.idpersonal
                LEFT JOIN usuario u ON s.idusuario=u.idusuario
                $condiciones
                AND c.idlocal = '$idlocal'
                ORDER BY s.idsalida DESC";
        return ejecutarConsulta($sql);
    }
}
