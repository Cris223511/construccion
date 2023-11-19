-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-11-2023 a las 00:13:16
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
-- Base de datos: `peluqueria`
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
  `codigo` varchar(50) NOT NULL,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`idarticulo`, `idusuario`, `idcategoria`, `idlocal`, `idmarca`, `codigo`, `codigo_producto`, `nombre`, `stock`, `stock_minimo`, `descripcion`, `imagen`, `estado`) VALUES
(1, 1, 1, 1, 1, '7 75 6631 5 0049 8', '2344232348567', 'RETEN DE EMBOLO', 20, 10, '', '1627845886.png', 1),
(2, 1, 1, 2, 1, '7 75 5328 6 0088 1', '2345345893452', 'RETEN DE EMBOLO', 43, 10, '', '1627845886.png', 1),
(3, 1, 1, 2, 1, '7 75 9222 9 0033 9', '6645456821243', 'RETEN DE EMBOLO', 38, 10, '', '1627845886.png', 1),
(4, 3, 1, 1, 1, '7 75 3137 1 0057 8', '2343454566456', 'RETEN DE EMBOLO', 2, 10, '', '1627845886.png', 1),
(5, 2, 1, 1, 1, '7 75 7982 6 0018 8', '2866038989324', 'RETEN DE EMBOLO', 3, 10, '', '1627845886.png', 1),
(7, 2, 1, 3, 1, '7 75 7057 8 0019 6', '6645678756', 'asdasd', 20, 25, 'asdasdasd', '1698694256.jpg', 1);

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
(1, 1, 'tecnologia', 'materiales tecnológicos para el corte de cabello.', '2023-10-18 19:07:28', 'activado', 0),
(2, 1, 'cortes', 'materiales para el corte de cabello.', '2023-10-17 19:07:31', 'activado', 0),
(3, 1, 'asdasd', 'asdads', '2023-10-19 14:30:24', 'activado', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `idcliente` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `direccion` varchar(70) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fecha_nac` date NOT NULL,
  `estado` varchar(20) NOT NULL,
  `eliminado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingreso`
--

CREATE TABLE `detalle_ingreso` (
  `iddetalle_ingreso` int(11) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_compra` decimal(11,2) NOT NULL,
  `precio_venta` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detalle_ingreso`
--

INSERT INTO `detalle_ingreso` (`iddetalle_ingreso`, `idingreso`, `idarticulo`, `cantidad`, `precio_compra`, `precio_venta`) VALUES
(1, 1, 4, 1, 450.00, 98.00),
(2, 2, 2, 1, 150.00, 21.00),
(3, 3, 5, 1, 190.00, 110.00),
(4, 4, 5, 1, 1.00, 1.00),
(5, 5, 6, 6, 40.00, 50.00),
(6, 0, 4, 12, 133.00, 153.00),
(7, 6, 6, 2, 132.00, 140.00),
(8, 6, 5, 1, 114.00, 150.00),
(9, 6, 5, 1, 155.00, 190.00),
(10, 0, 7, 2, 23.00, 22.00),
(11, 0, 6, 5, 155.00, 142.00),
(12, 0, 1315, 2, 50.00, 70.00),
(13, 7, 1314, 2, 100.00, 90.00),
(14, 0, 1314, 4, 222.00, 334.00),
(15, 8, 1314, 2, 44.00, 23.00),
(16, 9, 5, 3, 12.00, 12.00),
(17, 9, 4, 3, 12.00, 12.00),
(18, 0, 1316, 2, 30.00, 40.00),
(19, 10, 1316, 2, 123.00, 123.00),
(20, 10, 1316, 3, 123.00, 123.00),
(21, 11, 1316, 1, 123.00, 1.00),
(22, 11, 1316, 2, 123.00, 1.00),
(23, 12, 1315, 13, 14.00, 12.00),
(24, 12, 1316, 12, 11.00, 12.00),
(25, 13, 1314, 13, 12.00, 19.00),
(26, 13, 1315, 3, 14.00, 20.00),
(27, 14, 1322, 2, 15.00, 24.00),
(28, 0, 1319, 2, 134.00, 1.00),
(29, 0, 1319, 2, 13.00, 1.00),
(30, 0, 1322, 2, 12.00, 1.00),
(31, 0, 1322, 2, 33.00, 1.00),
(32, 15, 1317, 2, 13.00, 1.00),
(33, 16, 1319, 12, 13.00, 15.00),
(34, 16, 1324, 12, 13.00, 15.00),
(35, 17, 1324, 2, 13.00, 14.00),
(36, 18, 1324, 1, 1.00, 1.00),
(37, 0, 1324, 2, 14.00, 11.00),
(38, 0, 1325, 3, 14.00, 11.00),
(39, 0, 1326, 2, 13.00, 1.00),
(40, 0, 1327, 2, 34.00, 11.00),
(41, 0, 1324, 17, 22.00, 12.00),
(42, 0, 1319, 45, 2.00, 1.00),
(43, 0, 1324, 30, 16.00, 10.00),
(44, 0, 1319, 17, 10.00, 1.00),
(45, 0, 1325, 15, 10.00, 4.00);

--
-- Disparadores `detalle_ingreso`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockIngreso` AFTER INSERT ON `detalle_ingreso` FOR EACH ROW BEGIN
 UPDATE articulo SET stock = stock + NEW.cantidad 
 WHERE articulo.idarticulo = NEW.idarticulo;
END
$$
DELIMITER ;

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
(3, 3, 'Local de Ate Vitarte, Lima', '44839384560', 'un local donde se almacenará productos, listo para ser comercializados en el mercado.', '2023-10-18 11:40:28', 'activado', 0),
(9, 0, 'local nuevo', '23234234234', 'asdasddasasddas', '2023-11-02 00:43:15', 'activado', 0);

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
(1, 1, 'nike', 'la mejor marca del Peru', '2023-10-18 18:50:53', 'activado', 0),
(2, 1, 'cielo', 'agua hidratante', '2023-10-18 13:24:15', 'activado', 0),
(3, 1, 'mateo', 'la mejor marca de agua del peru', '2023-10-17 08:26:52', 'activado', 0),
(4, 1, 'adiddas', 'zapatillas a buen precio', '2023-10-17 14:36:51', 'activado', 0);

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
(5, 'Personas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `idtrabajador` int(11) NOT NULL,
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
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`idtrabajador`, `idusuario`, `idlocal`, `nombre`, `tipo_documento`, `num_documento`, `telefono`, `email`, `fecha_nac`, `estado`, `eliminado`) VALUES
(1, 1, 1, 'Juan Pérez', 'DNI', '12345678', '123456789', 'juan@example.com', '1985-03-15', 'activado', 0),
(2, 1, 1, 'Pedro López', 'DNI', '98765432', '123456789', 'pedro@example.com', '1988-11-30', 'activado', 0),
(3, 1, 1, 'Ana Martínez', 'CEDULA', '123487655890', '123456789', 'ana@example.com', '1995-05-10', 'activado', 0),
(4, 1, 1, 'Luis Rodríguez', 'DNI', '34561234', '123456789', 'luis@example.com', '1982-09-05', 'activado', 0),
(5, 2, 2, 'Carlos Fernández', 'CEDULA', '234567897920', '123456789', 'carlos@example.com', '1980-04-02', 'activado', 0),
(6, 2, 2, 'Sofía González', 'DNI', '67891234', '123456789', 'sofia@example.com', '1992-02-15', 'activado', 0),
(7, 2, 2, 'Jorge Pérez', 'RUC', '45678912528', '123456789', 'jorge@example.com', '1984-08-10', 'activado', 0),
(8, 3, 3, 'Isabel Martínez', 'CEDULA', '567891236289', '123456789', 'isabel@example.com', '1998-03-20', 'activado', 0),
(9, 3, 3, 'Miguel González', 'DNI', '87651234', '123456789', 'miguel@example.com', '1987-07-05', 'activado', 0),
(10, 3, 2, 'Ana Pérez', 'DNI', '99999999', '123456789', 'ana@example.com', '1997-05-20', 'activado', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
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

INSERT INTO `usuario` (`idusuario`, `idlocal`, `nombre`, `tipo_documento`, `num_documento`, `direccion`, `telefono`, `email`, `cargo`, `login`, `clave`, `imagen`, `estado`, `eliminado`) VALUES
(1, 1, 'christopher PS', 'DNI', '66559348', 'Lima, La Molina, Perú', '931742904', 'admin@admin.com', 'superadmin', 'admin', 'admin', '1487132068.jpg', 1, 0),
(2, 2, 'julio RH', 'DNI', '66448963', 'Lima, La Molina, Perú', '931742904', 'admin@admin.com', 'admin', 'admin2', 'admin2', '1487132068.jpg', 1, 0),
(3, 3, 'luis FG', 'DNI', '54845893', 'Lima, La Molina, Perú', '931742904', 'cajero@cajero.com', 'cajero', 'cajero', 'cajero', '1487132068.jpg', 1, 0);

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
(42, 3, 1),
(43, 3, 3),
(44, 3, 4),
(45, 3, 5),
(46, 2, 1),
(47, 2, 2),
(48, 2, 3),
(49, 2, 4),
(50, 2, 5),
(51, 1, 1),
(52, 1, 2),
(53, 1, 3),
(54, 1, 4),
(55, 1, 5);

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
  ADD KEY `idmarcas` (`idmarca`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `idlocal` (`idlocal`);

--
-- Indices de la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  ADD PRIMARY KEY (`iddetalle_ingreso`),
  ADD KEY `fk_detalle_ingreso_ingreso_idx` (`idingreso`),
  ADD KEY `fk_detalle_ingreso_articulo_idx` (`idarticulo`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`idlocal`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`idmarca`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`idtrabajador`),
  ADD KEY `idlocal` (`idlocal`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `idlocal` (`idlocal`);

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
  MODIFY `idarticulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  MODIFY `iddetalle_ingreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `idlocal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `idmarca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `idtrabajador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  MODIFY `idusuario_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
