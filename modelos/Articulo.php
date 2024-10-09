<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Articulo
{
	//Implementamos nuestro constructor
	public function __construct() {}

	//Implementamos un método para insertar registros
	public function insertar($idusuario, $idcategoria, $idlocal, $idmarca, $idmedida, $idubicacion, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $precio_compra, $precio_compra_mayor, $descripcion, $imagen, $fecha_emision, $fecha_vencimiento, $talla, $color, $peso, $nota_1, $nota_2, $imei, $serial)
	{
		if (empty($imagen))
			$imagen = "product.jpg";

		$sql = "INSERT INTO articulo (idusuario,idcategoria,idlocal,idmarca,idmedida,idubicacion,codigo,codigo_producto,nombre,stock,stock_minimo,precio_compra,precio_compra_mayor,descripcion,imagen,fecha_emision,fecha_vencimiento,talla,color,peso,nota_1,nota_2,imei,serial,fecha_hora,estado,eliminado)
		VALUES ('$idusuario','$idcategoria','$idlocal','$idmarca','$idmedida','$idubicacion','$codigo','$codigo_producto','$nombre','$stock', '$stock_minimo','$precio_compra','$precio_compra_mayor','$descripcion','$imagen','$fecha_emision','$fecha_vencimiento','$talla','$color','$peso','$nota_1','$nota_2','$imei','$serial',SYSDATE(),'1','0')";
		return ejecutarConsulta($sql);
	}

	public function verificarCodigoExiste($codigo)
	{
		$sql = "SELECT * FROM articulo WHERE codigo = '$codigo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código ya existe en la tabla
			return true;
		}
		// El código no existe en la tabla
		return false;
	}

	public function verificarCodigoProductoExiste($codigo_producto)
	{
		$sql = "SELECT * FROM articulo WHERE codigo_producto = '$codigo_producto' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código ya existe en la tabla
			return true;
		}
		// El código no existe en la tabla
		return false;
	}

	public function verificarStockMinimo($idarticulo, $cantidad)
	{
		$sql = "SELECT stock_minimo, stock FROM articulo WHERE idarticulo = '$idarticulo'";
		$resultado = ejecutarConsulta($sql);

		if (mysqli_num_rows($resultado) > 0) {
			$row = mysqli_fetch_assoc($resultado);
			$resultado = $row['stock'] - $cantidad;

			if ($resultado > 0 && $resultado <= $row['stock_minimo']) {
				return true; // Está dentro del rango del stock mínimo
			} else {
				return false; // Está fuera del rango del stock mínimo
			}
		} else {
			return false; // El artículo no existe en la tabla
		}
	}

	public function identificarStockMinimo($idarticulo)
	{
		$sql = "SELECT stock_minimo as stock_minimo FROM articulo WHERE idarticulo = '$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idarticulo, $idcategoria, $idlocal, $idmarca, $idmedida, $idubicacion, $codigo, $codigo_producto, $nombre, $stock, $stock_minimo, $precio_compra, $precio_compra_mayor, $descripcion, $imagen, $fecha_emision, $fecha_vencimiento, $talla, $color, $peso, $nota_1, $nota_2, $imei, $serial)
	{
		$sql = "UPDATE articulo SET idcategoria='$idcategoria',idlocal='$idlocal',idmarca='$idmarca',idmedida='$idmedida',idubicacion='$idubicacion',codigo='$codigo',codigo_producto='$codigo_producto',nombre='$nombre',stock='$stock',stock_minimo='$stock_minimo',precio_compra='$precio_compra',precio_compra_mayor='$precio_compra_mayor',descripcion='$descripcion',imagen='$imagen',fecha_emision='$fecha_emision',fecha_vencimiento='$fecha_vencimiento',talla='$talla',color='$color',peso='$peso',nota_1='$nota_1',nota_2='$nota_2',imei='$imei',serial='$serial' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	public function verificarCodigoProductoEditarExiste($codigo_producto, $idarticulo)
	{
		$sql = "SELECT * FROM articulo WHERE codigo_producto = '$codigo_producto' AND idarticulo != '$idarticulo' AND eliminado = '0'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			// El código de artículo ya existe en la tabla
			return true;
		}
		// El código de artículo no existe en la tabla
		return false;
	}

	//Implementamos un método para desactivar registros
	public function desactivar($idarticulo)
	{
		$sql = "UPDATE articulo SET estado='0' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar registros
	public function activar($idarticulo)
	{
		$sql = "UPDATE articulo SET estado='1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar registros
	public function eliminar($idarticulo)
	{
		$sql = "UPDATE articulo SET eliminado = '1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idarticulo)
	{
		$sql = "SELECT *, 
					   DATE_FORMAT(fecha_emision, '%Y-%m-%d') as fecha_emision_formateada, 
					   DATE_FORMAT(fecha_vencimiento, '%Y-%m-%d') as fecha_vencimiento_formateada 
				FROM articulo 
				WHERE idarticulo='$idarticulo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFecha($fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.eliminado = '0' AND DATE(a.fecha_hora) >= '$fecha_inicio' AND DATE(a.fecha_hora) <= '$fecha_fin' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorParametro($param)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE $param AND a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarPorUsuario($idlocalSession)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.idlocal = '$idlocalSession' AND a.eliminado = '0' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorFechaUsuario($idlocalSession, $fecha_inicio, $fecha_fin)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.idlocal = '$idlocalSession' AND a.eliminado = '0' AND DATE(a.fecha_hora) >= '$fecha_inicio' AND DATE(a.fecha_hora) <= '$fecha_fin' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioParametro($idlocalSession, $param)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE $param AND a.eliminado = '0' AND a.idlocal = '$idlocalSession' ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}

	/* ======================= SELECTS ======================= */

	public function listarTodosActivos()
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario, NULL AS ruc FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario, NULL AS ruc FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario, NULL AS ruc FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'
			UNION ALL
			SELECT 'ubicacion' AS tabla, ub.idubicacion AS id, ub.titulo, u.nombre AS usuario, NULL AS ruc FROM ubicaciones ub LEFT JOIN usuario u ON u.idusuario = ub.idusuario WHERE ub.estado='activado' AND ub.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	public function listarTodosActivosPorUsuario($idusuario, $idlocal)
	{
		$sql = "SELECT 'categoria' AS tabla, b.idcategoria AS id, b.titulo, u.nombre AS usuario, NULL AS ruc FROM categoria b LEFT JOIN usuario u ON b.idusuario = u.idusuario WHERE b.estado='activado' AND b.eliminado='0'
			UNION ALL
			SELECT 'marca' AS tabla, o.idmarca AS id, o.titulo, u.nombre AS usuario, NULL AS ruc FROM marcas o LEFT JOIN usuario u ON o.idusuario = u.idusuario WHERE o.estado='activado' AND o.eliminado='0'
			UNION ALL
			SELECT 'local' AS tabla, l.idlocal AS id, l.titulo, u.nombre AS usuario, local_ruc AS ruc FROM locales l LEFT JOIN usuario u ON l.idusuario = u.idusuario WHERE l.idlocal='$idlocal' AND l.idusuario <> 0 AND l.estado='activado' AND l.eliminado='0'
			UNION ALL
			SELECT 'medida' AS tabla, m.idmedida AS id, m.titulo, u.nombre AS usuario, NULL AS ruc FROM medidas m LEFT JOIN usuario u ON m.idusuario = u.idusuario WHERE m.estado='activado' AND m.eliminado='0'
			UNION ALL
			SELECT 'ubicacion' AS tabla, ub.idubicacion AS id, ub.titulo, u.nombre AS usuario, NULL AS ruc FROM ubicaciones ub LEFT JOIN usuario u ON u.idusuario = ub.idusuario WHERE ub.estado='activado' AND ub.eliminado='0'";

		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarActivosPorArticulo($idarticulo)
	{
		$sql = "SELECT a.idarticulo,a.idusuario,u.nombre as usuario,u.cargo as cargo,a.idcategoria,c.titulo as categoria,DATE_FORMAT(a.fecha_hora, '%d-%m-%Y %H:%i:%s') as fecha,al.titulo as local,m.titulo as marca, me.titulo as medida,a.codigo,a.codigo_producto,a.nombre,a.stock,a.stock_minimo,a.precio_compra,a.precio_compra_mayor,a.descripcion,a.imagen,DATE_FORMAT(a.fecha_emision, '%d-%m-%Y') as fecha_emision,DATE_FORMAT(a.fecha_vencimiento, '%d-%m-%Y') as fecha_vencimiento,a.talla,a.color,a.peso,a.nota_1,a.nota_2,a.imei,a.serial,a.estado FROM articulo a LEFT JOIN categoria c ON a.idcategoria=c.idcategoria LEFT JOIN medidas me ON a.idmedida=me.idmedida LEFT JOIN locales al ON a.idlocal=al.idlocal LEFT JOIN usuario u ON a.idusuario=u.idusuario LEFT JOIN marcas m ON a.idmarca=m.idmarca WHERE a.eliminado = '0' AND a.idarticulo = $idarticulo ORDER BY a.idarticulo DESC";
		return ejecutarConsulta($sql);
	}
}
