-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-11-2023 a las 21:42:39
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `construccion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

CREATE TABLE `articulo` (
  `idarticulo` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idmarca` int(11) NOT NULL,
  `idmedida` int(11) NOT NULL,
  `idtipo` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `peso` decimal(11,2) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `precio_compra` decimal(11,2) NOT NULL,
  `precio_compra` decimal(11,2) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`idarticulo`, `idusuario`, `idcategoria`, `idlocal`, `idmarca`, `idmedida`, `idtipo`, `codigo`, `codigo_producto`, `nombre`, `stock`, `stock_minimo`, `peso`, `descripcion`, `imagen`, `precio_compra`, `precio_compra`, `estado`, `eliminado`) VALUES
(1, 1, 1, 1, 2, 2, 1, '7 75 6631 5 0049 8', '2344232348567', 'RETEN DE EMBOLO', 20, 10, 14.16, 'producto a vender', '1627845886.png', 90.00, 110.00, 1, 0),
(2, 1, 2, 2, 1, 1, 2, '7 75 5328 6 0088 1', '2345345893452', 'RETEN DE EMBOLO', 43, 10, 7.54, 'producto a vender', '1157835826.png', 82.60, 94.30, 1, 0),
(3, 1, 1, 2, 2, 2, 1, '7 75 9222 9 0033 9', '6645456821243', 'RETEN DE EMBOLO', 0, 10, 9.81, 'producto a vender', '1627845886.png', 92.73, 100.10, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(15) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idcategoria`, `idusuario`, `titulo`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'cemento', 'Categoría para los productos de construcción.', '2023-10-18 19:07:28', 'activado', 0),
(2, 1, 'herramientas', 'Categoría para los productos de construcción.', '2023-10-17 19:07:31', 'activado', 0),
(4, 2, 'iluminación', 'Categoría para los productos de construcción.', '2023-11-05 15:38:53', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `identrada` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `idmarca` int(11) NOT NULL,
  `idmedida` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `idtipo` int(11) NOT NULL,
  `cantidad` varchar(5) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `ubicacion` varchar(50) NOT NULL,
  `tipo_documento` int(20) NOT NULL,
  `num_documento` int(20) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `local_ruc` varchar(15) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`idlocal`, `idusuario`, `titulo`, `local_ruc`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'Local de Chorrillos, Lima', '55849586943', 'un local donde se almacenará productos, listo para ser comercializados en el mercado.', '2023-10-18 13:27:21', 'activado', 0),
(2, 2, 'Local de Los Olivos, Lima', '78549384595', 'un local donde se almacenará productos, listo para ser comercializados en el mercado.', '2023-10-18 12:33:38', 'activado', 0),
(3, 2, 'Local de Ate Vitarte, Lima', '44839384560', 'un local donde se almacenará productos, listo para ser comercializados en el mercado.', '2023-10-18 11:40:28', 'activado', 0),
(9, 1, 'Local de San Miguel, Lima', '23234234234', 'un local donde se almacenará productos, listo para ser comercializados en el mercado.', '2023-11-02 00:43:15', 'activado', 0),
(12, 2, 'asdasd', '42234423342', 'sadsasdasd', '2023-11-19 15:20:50', 'activado', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinarias`
--

CREATE TABLE `maquinarias` (
  `idmaquinaria` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `maquinarias`
--

INSERT INTO `maquinarias` (`idmaquinaria`, `idusuario`, `titulo`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'Excavadoras', 'Maquinaria para la elaboración de construcciones.', '2023-11-18 21:32:22', 'activado', 0),
(2, 1, 'Grúas', 'Maquinaria para la elaboración de construcciones.', '2023-11-18 14:21:41', 'activado', 0),
(3, 1, 'Rodillos', 'Maquinaria para la elaboración de construcciones.', '2023-11-18 03:29:21', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `idmarca` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`idmarca`, `idusuario`, `titulo`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'Caterpillar', 'Marca de construcciones en el Perú.', '2023-10-18 18:50:53', 'activado', 0),
(2, 1, 'Bosh', 'Marca de construcciones en el Perú.', '2023-10-18 13:24:15', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medidas`
--

CREATE TABLE `medidas` (
  `idmedida` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `medidas`
--

INSERT INTO `medidas` (`idmedida`, `idusuario`, `titulo`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'kg (kilogramos)', 'unidad de medida para los productos de construcción.', '2023-11-06 01:02:29', 'activado', 0),
(2, 3, 'mg (miligramos)', 'unidad de medida para los productos de la peluquería.unidad de medida para los productos de construcción.', '2023-11-06 01:02:29', 'activado', 0),
(3, 1, 'dag (decagramos)', 'unidad de medida para los productos de la peluquería.unidad de medida para los productos de construcción.', '2023-11-06 01:05:28', 'activado', 0),
(4, 3, 'hg (hectogramos)', 'unidad de medida para los productos de la peluquería.unidad de medida para los productos de construcción.', '2023-11-06 01:05:28', 'activado', 0),
(6, 2, 'm3 (metros cúbicos)', 'unidad de medida para los productos de la peluquería.unidad de medida para los productos de construcción.', '2023-11-09 11:35:30', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `idpermiso` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`idpermiso`, `nombre`) VALUES
(1, 'Escritorio'),
(2, 'Acceso'),
(3, 'Perfil usuario'),
(4, 'Almacén'),
(5, 'Entradas'),
(6, 'Salidas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personales`
--

CREATE TABLE `personales` (
  `idpersonal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fecha_nac` date NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `personales`
--

INSERT INTO `personales` (`idpersonal`, `idusuario`, `idlocal`, `nombre`, `tipo_documento`, `num_documento`, `telefono`, `email`, `fecha_nac`, `estado`, `eliminado`) VALUES
(1, 1, 1, 'Juan Pérez', 'DNI', '12345678', '123456789', 'juan@example.com', '1985-03-15', 'activado', 0),
(2, 1, 1, 'Pedro López', 'DNI', '98765432', '123456789', 'pedro@example.com', '1988-11-30', 'activado', 0),
(3, 1, 1, 'Ana Martínez', 'CEDULA', '123487655890', '123456789', 'ana@example.com', '1995-05-10', 'activado', 0),
(4, 1, 1, 'Luis Rodríguez', 'DNI', '34561234', '123456789', 'luis@example.com', '1982-09-05', 'activado', 0),
(5, 2, 2, 'Carlos Fernández', 'CEDULA', '234567897920', '123456789', 'carlos@example.com', '1980-04-02', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `idproveedor` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fecha_nac` date NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`idproveedor`, `idusuario`, `idlocal`, `nombre`, `tipo_documento`, `num_documento`, `telefono`, `email`, `fecha_nac`, `estado`, `eliminado`) VALUES
(1, 1, 1, 'Jorge Serna', 'DNI', '12345678', '123456789', 'jorge@example.com', '1985-03-15', 'activado', 0),
(2, 1, 1, 'Mario Nuñez', 'DNI', '98765432', '123456789', 'mario@example.com', '1988-11-30', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas`
--

CREATE TABLE `salidas` (
  `idsalida` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `idmarca` int(11) NOT NULL,
  `idmedida` int(11) NOT NULL,
  `idtipo` int(11) NOT NULL,
  `idmaquinaria` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `cantidad` varchar(5) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `ubicacion` varchar(50) NOT NULL,
  `tipo_documento` int(20) NOT NULL,
  `num_documento` int(20) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `idtipo` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`idtipo`, `idusuario`, `titulo`, `descripcion`, `fecha_hora`, `estado`, `eliminado`) VALUES
(1, 1, 'Luminarias', 'Tipo de artículos de construcción.', '2023-11-18 21:37:08', 'activado', 0),
(2, 1, 'Aislantes', 'Tipo de artículos de construcción.', '2023-11-18 17:21:24', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cargo` varchar(20) DEFAULT NULL,
  `login` varchar(20) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `tipo_documento`, `num_documento`, `direccion`, `telefono`, `email`, `cargo`, `login`, `clave`, `imagen`, `estado`, `eliminado`) VALUES
(1, 'christopher PS', 'DNI', '66559348', 'Lima, La Molina, Perú', '931742904', 'admin@admin.com', 'superadmin', 'admin', 'admin', '1487132068.jpg', 1, 0),
(2, 'julio RH', 'DNI', '66448963', 'Lima, La Molina, Perú', '931742904', 'admin@admin.com', 'admin', 'admin2', 'admin2', '1487132068.jpg', 1, 0),
(3, 'luis FG', 'DNI', '54845893', 'Lima, La Molina, Perú', '931742904', 'cajero@cajero.com', 'usuario', 'usuario', 'usuario', '1487132068.jpg', 1, 0),
(12, 'nuevo usuario', 'DNI', '12312312', 'Lima', '987745634', 'cris_antonio2001@hotmail.com', 'usuario', 'nuevo', 'nuevo', '1700423467.png', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_permiso`
--

CREATE TABLE `usuario_permiso` (
  `idusuario_permiso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idpermiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_permiso`
--

INSERT INTO `usuario_permiso` (`idusuario_permiso`, `idusuario`, `idpermiso`) VALUES
(24, 6, 1),
(25, 6, 2),
(26, 6, 3),
(27, 6, 4),
(28, 7, 1),
(29, 7, 2),
(30, 7, 3),
(31, 7, 4),
(32, 8, 1),
(33, 8, 2),
(34, 8, 3),
(35, 8, 4),
(36, 8, 5),
(37, 9, 1),
(38, 9, 2),
(39, 9, 3),
(40, 9, 4),
(41, 9, 5),
(56, 3, 1),
(57, 3, 3),
(58, 3, 4),
(59, 3, 5),
(60, 3, 6),
(61, 3, 7),
(62, 3, 8),
(63, 3, 9),
(64, 2, 1),
(65, 2, 2),
(66, 2, 3),
(67, 2, 4),
(68, 2, 5),
(69, 2, 6),
(70, 2, 7),
(71, 2, 8),
(72, 2, 9),
(73, 1, 1),
(74, 1, 2),
(75, 1, 3),
(76, 1, 4),
(77, 1, 5),
(78, 1, 6),
(79, 1, 7),
(80, 1, 8),
(81, 1, 9),
(90, 10, 1),
(91, 10, 2),
(92, 10, 3),
(93, 10, 4),
(94, 10, 5),
(95, 10, 6),
(96, 10, 7),
(97, 10, 8),
(98, 10, 9),
(99, 11, 1),
(100, 11, 2),
(101, 11, 3),
(102, 11, 6),
(103, 11, 7),
(104, 11, 8),
(105, 11, 9),
(106, 12, 1),
(107, 12, 3),
(108, 12, 4),
(109, 12, 5),
(110, 12, 6);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`idarticulo`),
  ADD KEY `fk_articulo_categoria_idx` (`idcategoria`),
  ADD KEY `idalmacen` (`idlocal`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idmedida` (`idmedida`),
  ADD KEY `idtipo` (`idtipo`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`identrada`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idcategoria` (`idcategoria`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idmedida` (`idmedida`),
  ADD KEY `idproveedor` (`idproveedor`),
  ADD KEY `idtipo` (`idtipo`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`idlocal`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `maquinarias`
--
ALTER TABLE `maquinarias`
  ADD PRIMARY KEY (`idmaquinaria`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`idmarca`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `medidas`
--
ALTER TABLE `medidas`
  ADD PRIMARY KEY (`idmedida`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indices de la tabla `personales`
--
ALTER TABLE `personales`
  ADD PRIMARY KEY (`idpersonal`),
  ADD KEY `idlocal` (`idlocal`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`idproveedor`),
  ADD KEY `idlocal` (`idlocal`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`idsalida`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idcategoria` (`idcategoria`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idmedida` (`idmedida`),
  ADD KEY `idtipo` (`idtipo`),
  ADD KEY `idmaquinaria` (`idmaquinaria`),
  ADD KEY `idpersonal` (`idpersonal`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`idtipo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD PRIMARY KEY (`idusuario_permiso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulo`
--
ALTER TABLE `articulo`
  MODIFY `idarticulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `identrada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `idlocal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `maquinarias`
--
ALTER TABLE `maquinarias`
  MODIFY `idmaquinaria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `idmarca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `medidas`
--
ALTER TABLE `medidas`
  MODIFY `idmedida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `personales`
--
ALTER TABLE `personales`
  MODIFY `idpersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `idproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `salidas`
--
ALTER TABLE `salidas`
  MODIFY `idsalida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `idtipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  MODIFY `idusuario_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
