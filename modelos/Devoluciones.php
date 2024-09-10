<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Devolucion
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	public function actualizarDevolucion($iddevolucion, $idarticulo, $cantidad_devuelta)
	{
		// Primero, debemos verificar si hay suficiente stock a devolver para cada artículo
		$error = $this->validarStockDevolver($iddevolucion, $idarticulo, $cantidad_devuelta);

		if ($error) {
			// Si hay un error, no se puede actualizar
			return false;
		}

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			$sql_detalle1 = "UPDATE detalle_devolucion SET cantidad_devuelta=cantidad_devuelta+'$cantidad_devuelta[$num_elementos]' WHERE iddevolucion='$iddevolucion' AND idarticulo='$idarticulo[$num_elementos]'";
			ejecutarConsulta($sql_detalle1) or $sw = false;
			$num_elementos = $num_elementos + 1;
		}

		$sql1 = "UPDATE devolucion SET estado='En curso' WHERE iddevolucion='$iddevolucion'";
		ejecutarConsulta($sql1);

		return $sw;
	}

	public function actualizarDevolucion2($iddevolucion, $opcion, $idarticulo, $cantidad_devuelta)
	{
		$sql1 = "UPDATE devolucion SET opcion='$opcion', estado='En curso' WHERE iddevolucion='$iddevolucion'";
		ejecutarConsulta($sql1);

		$num_elementos = 0;
		$sw = true;

		if ($opcion == 1) {
			while ($num_elementos < count($idarticulo)) {
				$sql_detalle1 = "UPDATE detalle_devolucion SET cantidad_a_devolver=cantidad_a_devolver+'$cantidad_devuelta[$num_elementos]' WHERE iddevolucion='$iddevolucion' AND idarticulo='$idarticulo[$num_elementos]'";
				ejecutarConsulta($sql_detalle1) or $sw = false;
				$num_elementos = $num_elementos + 1;
			}

			$sql2 = "UPDATE devolucion SET opcion='$opcion', estado='Finalizado', fecha_hora_devolucion=SYSDATE() WHERE iddevolucion='$iddevolucion'";
			ejecutarConsulta($sql2);
			$sql3 = "UPDATE solicitud SET estado='Finalizado', fecha_hora_despacho=SYSDATE() WHERE idsolicitud='$iddevolucion'";
			ejecutarConsulta($sql3);
		}

		return $sw;
	}

	public function validarStockDevolver($iddevolucion, $idarticulo, $cantidad_devuelta)
	{
		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql = "SELECT cantidad_prestada, cantidad_devuelta FROM detalle_devolucion WHERE iddevolucion='$iddevolucion' AND idarticulo='$idarticulo[$i]'";
			$res = ejecutarConsultaSimpleFila($sql);
			$cantidad_prestada = $res['cantidad_prestada'];
			$cantidadDevueltaActual = $res['cantidad_devuelta'];
			if ($cantidad_devuelta[$i] + $cantidadDevueltaActual > $cantidad_prestada) {
				return true;
			}
		}
		return false;
	}

	public function mostrar($iddevolucion)
	{
		$sql = "SELECT
					d.iddevolucion,
					ual.idusuario AS idalmacenero,
					uen.idusuario AS idencargado,
					d.codigo_pedido,
					d.empresa,
					d.destino,
					d.telefono,
					d.opcion,
					d.estado
				FROM devolucion d
				LEFT JOIN usuario ual ON d.idalmacenero = ual.idusuario
				LEFT JOIN usuario uen ON d.idencargado = uen.idusuario
				WHERE d.iddevolucion='$iddevolucion' AND (d.estado = 'Pendiente' OR d.estado = 'Finalizado' OR d.estado = 'En curso')";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT
					d.iddevolucion,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					d.codigo_pedido,
					d.empresa,
					d.destino,
					d.telefono,
					d.opcion,
					DATE_FORMAT(d.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(d.fecha_hora_devolucion, '%d-%m-%Y %H:%i:%s') AS fecha_hora_devolucion,
					d.estado FROM devolucion d
				LEFT JOIN usuario uen ON d.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON d.idalmacenero = ual.idusuario
				WHERE (d.estado = 'Pendiente' OR d.estado = 'Finalizado' OR d.estado = 'En curso')
				ORDER BY d.iddevolucion DESC";

		return ejecutarConsulta($sql);
	}

	public function listarUsuario($idencargado)
	{
		$sql = "SELECT
					d.iddevolucion,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					d.codigo_pedido,
					d.empresa,
					d.destino,
					d.telefono,
					d.opcion,
					DATE_FORMAT(d.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(d.fecha_hora_devolucion, '%d-%m-%Y %H:%i:%s') AS fecha_hora_devolucion,
					d.estado FROM devolucion d
				LEFT JOIN usuario uen ON d.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON d.idalmacenero = ual.idusuario
				WHERE d.idencargado = '$idencargado' AND (d.estado = 'Pendiente' OR d.estado = 'Finalizado' OR d.estado = 'En curso')
				ORDER BY d.iddevolucion DESC";

		return ejecutarConsulta($sql);
	}

	public function actualizarComentario($iddevolucion, $comentario)
	{
		$sql = "UPDATE devolucion SET comentario='$comentario' WHERE iddevolucion='$iddevolucion'";
		return ejecutarConsulta($sql);
	}

	public function mostrarComentario($iddevolucion)
	{
		$sql = "SELECT iddevolucion, comentario FROM devolucion WHERE iddevolucion='$iddevolucion'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($iddevolucion)
	{
		$sql = "SELECT dd.iddevolucion,
					dd.idarticulo,
					a.nombre,
					c.titulo as categoria,
					m.titulo as marca,
					al.titulo as local,
					dd.cantidad,
					dd.precio_compra,
					dd.cantidad_prestada,
					dd.cantidad_devuelta
				FROM detalle_devolucion dd
				LEFT JOIN articulo a ON dd.idarticulo = a.idarticulo
				LEFT JOIN marcas m ON a.idmarca = m.idmarca
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN locales al ON a.idlocal = al.idlocal
				WHERE dd.iddevolucion='$iddevolucion'";
		return ejecutarConsulta($sql);
	}

	public function devolucioncabecera($iddevolucion)
	{
		$sql = "SELECT
					d.iddevolucion,
					d.idalmacenero,
					uen.nombre AS responsable_pedido,
					ual.nombre AS responsable_despacho,
					uen.cargo AS cargo_pedido,
					ual.cargo AS cargo_despacho,
					uen.direccion AS direccion_pedido, uen.tipo_documento AS tipo_documento_pedido, uen.num_documento AS num_documento_pedido, uen.email AS email_pedido, uen.telefono AS telefono_pedido,
					ual.direccion AS direccion_despacho, ual.tipo_documento AS tipo_documento_despacho, ual.num_documento AS num_documento_despacho, ual.email AS email_despacho, ual.telefono AS telefono_despacho,
					d.codigo_pedido,
					d.empresa,
					d.destino,
					d.telefono,
					d.opcion,
					d.estado,
					DATE_FORMAT(d.fecha_hora_pedido, '%d-%m-%Y %H:%i:%s') AS fecha_hora_pedido,
					DATE_FORMAT(d.fecha_hora_devolucion, '%d-%m-%Y %H:%i:%s') AS fecha_hora_devolucion
				FROM devolucion d
				LEFT JOIN usuario uen ON d.idencargado = uen.idusuario
				LEFT JOIN usuario ual ON d.idalmacenero = ual.idusuario
				WHERE d.iddevolucion = '$iddevolucion' AND (d.estado = 'Pendiente' OR d.estado = 'Finalizado' OR d.estado = 'En curso')";
		return ejecutarConsulta($sql);
	}

	public function devoluciondetalle($iddevolucion)
	{
		$sql = "SELECT
					dd.iddevolucion,
					dd.idarticulo,
					a.nombre,
					a.codigo_producto,
					dd.precio_compra,
					dd.cantidad,
					dd.cantidad_devuelta as cantidad_devuelta
				FROM detalle_devolucion dd
				LEFT JOIN articulo a ON dd.idarticulo = a.idarticulo
				WHERE dd.iddevolucion='$iddevolucion'";
		return ejecutarConsulta($sql);
	}

	public function listarSelectAlmacenero()
	{
		$sql = "SELECT idusuario, nombre, cargo FROM usuario ORDER BY idusuario DESC";
		return ejecutarConsulta($sql);
	}
}
