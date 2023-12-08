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
        $sql = "SELECT CONCAT(DAY(fecha_hora),'-',MONTH(fecha_hora)) AS fecha,COUNT(*) AS total FROM entradas GROUP BY fecha_hora ORDER BY fecha_hora ASC LIMIT 0,10";
        return ejecutarConsulta($sql);
    }

    public function salidasultimos_12meses()
    {
        $sql = "SELECT DATE_FORMAT(fecha_hora,'%M') AS fecha,COUNT(*) AS total FROM salidas GROUP BY MONTH(fecha_hora) ORDER BY fecha_hora ASC LIMIT 0,10";
        return ejecutarConsulta($sql);
    }

    public function totalentradahoyUsuario($idusuario)
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM entradas WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function totalsalidahoyUsuario($idusuario)
    {
        $sql = "SELECT COUNT(*) AS cantidad FROM salidas WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function entradasultimos_10diasUsuario($idusuario)
    {
        $sql = "SELECT CONCAT(DAY(fecha_hora),'-',MONTH(fecha_hora)) AS fecha,COUNT(*) AS total FROM entradas WHERE idusuario = '$idusuario' GROUP BY fecha_hora ORDER BY fecha_hora ASC LIMIT 0,10";
        return ejecutarConsulta($sql);
    }

    public function salidasultimos_12mesesUsuario($idusuario)
    {
        $sql = "SELECT DATE_FORMAT(fecha_hora,'%M') AS fecha,COUNT(*) AS total FROM salidas WHERE idusuario = '$idusuario' GROUP BY MONTH(fecha_hora) ORDER BY fecha_hora ASC LIMIT 0,10";
        return ejecutarConsulta($sql);
    }
}
