<?php
require "../config/Conexion.php";

class Entrada
{
	public function __construct() {}

	public function agregar($idlocal, $idusuario, $idproveedor, $idtipo, $codigo, $ubicacion, $descripcion, $impuesto, $total_compra, $idarticulo, $cantidad, $precio_compra)
	{
		// Primero, debemos verificar si hay suficiente stock para cada artículo
		$error = $this->validarStock($idarticulo, $cantidad);
		if ($error) {
			// Si hay un error, no se puede insertar
			return false;
		}

		date_default_timezone_set("America/Lima");
		// Si no hay errores, continuamos con el registro de la entrada
		$sql = "INSERT INTO entradas (idlocal,idusuario,idproveedor,idtipo,codigo,ubicacion,descripcion,fecha_hora,impuesto,total_compra,estado)
            VALUES ('$idlocal','$idusuario','$idproveedor','$idtipo','$codigo','$ubicacion','$descripcion', SYSDATE(),'$impuesto','$total_compra','activado')";
		$identradanew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($idarticulo)) {
			// Consulta para obtener el valor del campo 'titulo' de la tabla 'medidas' para el artículo actual
			$sql_medida = "SELECT m.titulo FROM medidas m INNER JOIN articulo a ON a.idmedida = m.idmedida WHERE a.idarticulo = '$idarticulo[$num_elementos]'";
			$resultado = ejecutarConsultaSimpleFila($sql_medida);

			if ($resultado['titulo'] == 'Paquetes') {
				$actualizar_art = "UPDATE articulo SET precio_compra_mayor='$precio_compra[$num_elementos]' WHERE idarticulo='$idarticulo[$num_elementos]'";
			} else {
				$actualizar_art = "UPDATE articulo SET precio_compra='$precio_compra[$num_elementos]' WHERE idarticulo='$idarticulo[$num_elementos]'";
			}

			ejecutarConsulta($actualizar_art) or $sw = false;

			// Inserción del detalle de entrada
			$sql_detalle = "INSERT INTO detalle_entrada(identrada,idarticulo,cantidad,precio_compra) 
							VALUES ('$identradanew', '$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_compra[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;

			$num_elementos++;
		}

		return $sw;
	}

	public function validarStock($idarticulo, $cantidad)
	{
		for ($i = 0; $i < count($idarticulo); $i++) {
			$sql = "SELECT stock FROM articulo WHERE idarticulo = '$idarticulo[$i]'";
			$res = ejecutarConsultaSimpleFila($sql);
			$stockActual = $res['stock'];
			if ($cantidad[$i] > $stockActual) {
				return true;
			}
		}
		return false;
	}

	public function desactivar($identrada)
	{
		$sql = "UPDATE entradas SET estado='desactivado' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function activar($identrada)
	{
		$sql = "UPDATE entradas SET estado='activado' WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function eliminar($identrada)
	{
		$sql = "DELETE FROM entradas WHERE identrada='$identrada'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($identrada)
	{
		$sql = "SELECT * FROM entradas WHERE identrada='$identrada'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar()
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,lo.titulo as local,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.total_compra,e.impuesto,e.estado FROM entradas e LEFT JOIN locales lo ON e.idlocal=lo.idlocal LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,u.cargo,lo.titulo as local,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.total_compra,e.impuesto,e.estado FROM entradas e LEFT JOIN locales lo ON e.idlocal=lo.idlocal LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,u.cargo,lo.titulo as local,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.total_compra,e.impuesto,e.estado FROM entradas e LEFT JOIN locales lo ON e.idlocal=lo.idlocal LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE e.idlocal = '$idlocalSession' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioFecha($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo,u.cargo,u.cargo,lo.titulo as local,t.titulo as tipo,p.nombre as proveedor,e.codigo,e.ubicacion,e.descripcion,e.descripcion,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.total_compra,e.impuesto,e.estado FROM entradas e LEFT JOIN locales lo ON e.idlocal=lo.idlocal LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE e.idlocal = '$idlocalSession' AND DATE(e.fecha_hora) >= '$fecha_inicio' AND DATE(e.fecha_hora) <= '$fecha_fin' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCabecera($identrada)
	{
		$sql = "SELECT e.identrada,e.idusuario,u.nombre as usuario,u.cargo as cargo, u.tipo_documento, u.num_documento, u.direccion, u.email, u.direccion, u.telefono, lo.titulo as local, t.titulo as tipo, p.nombre, p.tipo_documento, p.num_documento, p.direccion, p.email, p.direccion, p.telefono, e.codigo,DATE_FORMAT(e.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,e.total_compra,e.impuesto,e.estado FROM entradas e LEFT JOIN locales AS lo ON e.idlocal=lo.idlocal LEFT JOIN tipos t ON e.idtipo=t.idtipo LEFT JOIN proveedores p ON e.idproveedor=p.idproveedor LEFT JOIN usuario u ON e.idusuario=u.idusuario WHERE e.identrada = '$identrada' ORDER BY e.identrada DESC";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($identrada)
	{
		$sql = "SELECT de.identrada, de.idarticulo, a.nombre AS articulo, c.titulo AS categoria, ma.titulo AS marca, me.titulo AS medida, a.codigo, a.codigo_producto, a.stock, a.stock_minimo, a.imagen, de.cantidad, de.precio_compra, (de.cantidad * de.precio_compra) as subtotal
				FROM detalle_entrada de
				LEFT JOIN articulo a ON de.idarticulo = a.idarticulo
				LEFT JOIN categoria c ON a.idcategoria = c.idcategoria
				LEFT JOIN marcas ma ON a.idmarca = ma.idmarca
				LEFT JOIN medidas me ON a.idmedida = me.idmedida
				WHERE de.identrada = '$identrada'";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'proveedor' AS tabla, p.idproveedor AS id, p.nombre, u.nombre AS usuario, NULL AS ruc FROM proveedores p LEFT JOIN usuario u ON p.idusuario = u.idusuario WHERE p.estado='activado' AND p.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario, NULL AS ruc FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario, $idlocal)
	{
		$sql = "SELECT 'proveedor' AS tabla, p.idproveedor AS id, p.nombre, u.nombre AS usuario, NULL AS ruc FROM proveedores p LEFT JOIN usuario u ON p.idusuario = u.idusuario WHERE p.estado='activado' AND p.eliminado='0'
			UNION ALL
			SELECT 'tipo' AS tabla, t.idtipo AS id, t.titulo, u.nombre AS usuario, NULL AS ruc FROM tipos t LEFT JOIN usuario u ON t.idusuario = u.idusuario WHERE t.estado='activado' AND t.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'";

		return ejecutarConsulta($sql);
	}
}
