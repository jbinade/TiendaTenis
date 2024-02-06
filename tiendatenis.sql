-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-02-2024 a las 20:49:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tiendatenis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `categoria` int(11) NOT NULL,
  `precio` float NOT NULL,
  `preciodest` float NOT NULL,
  `imagen` varchar(200) NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `preciodest`, `imagen`, `activo`) VALUES
('ACC001', 'cordaje luxilon adrenaline', 'cordaje perfecto para jugadores que buscan control y efectos', 51, 150, 105, 'imagen cordaje', 1),
('ACC002', 'grip wilson pro', 'pack de 12 overgrips wilson pro blanco', 52, 20, 14, 'imagen grips', 1),
('ACC003', 'dunlop fort', 'bote de pelotas x4 dunlop fort', 53, 7, 4.9, 'imagen pelotas', 1),
('BOL001', 'bolsa asics', 'bolsa asics de deporte con 4 bolsillos ', 41, 50, 35, 'imagen bolsa', 1),
('BOL002', 'raquetero wilson', 'raquetero wilson capacidad de 12 raquetas y varios compartimentos', 42, 80, 56, 'imagen raquetero', 1),
('BOL003', 'mochila under armour', 'mochila ideal para deporte con capacidad para multiples cosas', 43, 65, 45.5, 'imagen mochila', 1),
('RAQ001', 'raqueta babolat pure areo', 'raqueta babolat pure aero 2023 300gr perfecta para potencia y efectos', 11, 190, 1330, 'imagen raqueta', 1),
('RAQ002', 'raqueta head radical', 'raqueta head radical 300gr perfecta para control y efectos', 12, 175, 122.5, 'imagen raqueta', 1),
('RAQ003', 'raqueta wilson pro staff', 'raqueta wilson pro staff perfecta para jugadores de control', 13, 240, 168, 'imagen raqueta', 1),
('RAQ004', 'raqueta yonex vcore', 'raqueta babolat 305gr. raqueta de control perfecta para jugadores avanzados', 11, 155.5, 108.5, 'imagen raqueta', 1),
('RAQ005', 'raqueta head speed mp', 'head speed mp 300gr. raqueta versatil que combina potencia y control', 12, 185, 129.5, 'imagen raqueta', 1),
('RAQ006', 'raqueta wilson blade v8', 'raqueta wilson blade v8 para control y efectos', 13, 220, 154, 'imagen raqueta', 1),
('TEX001', 'camiseta adidas open australia', 'camiseta adidas open australia azul', 21, 65, 45.5, 'imagen camiseta', 1),
('TEX002', 'vestido nike open australia', 'vestido nike open de australia ', 22, 75, 52.5, 'imagen vestido', 1),
('TEX003', 'camiseta asics niños', 'camiseta de niño asics colores azul y amarillo', 23, 50, 35, 'imagen camiseta', 1),
('ZAP001', 'zapatillas nike zoom vapor', 'zapatillas nike zoom vapor perfectas para un movimiento rapido', 31, 120, 84, 'imagen zapatillas', 1),
('ZAP002', 'zapatillas adidas barricade', 'zapatillas adidas barricade 2024 estables con gran agarre', 32, 135, 94.5, 'imagen zapatillas', 1),
('ZAP003', 'zapatillas adidas niño', 'zapatillas de niño adidas tallas de la 32 a 35', 33, 45, 31.5, 'imagen zapatillas', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `codCategoriaPadre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`codigo`, `nombre`, `activo`, `codCategoriaPadre`) VALUES
(1, 'Raquetas', 1, NULL),
(2, 'Textil', 1, NULL),
(3, 'Zapatillas', 1, NULL),
(4, 'Bolsas', 1, NULL),
(5, 'Otros', 1, NULL),
(11, 'Babolat', 1, 1),
(12, 'Head', 1, 1),
(13, 'Wilson', 1, 1),
(21, 'Ropa Hombre', 1, 2),
(22, 'Ropa Mujer', 1, 2),
(23, 'Ropa Niño', 1, 2),
(31, 'Zapatillas Hombre', 1, 3),
(32, 'Zapatillas Mujer', 1, 3),
(33, 'Zapatillas Niño', 1, 3),
(41, 'Bolsas', 1, 4),
(42, 'Raqueteros', 1, 4),
(43, 'Mochilas', 1, 4),
(51, 'Cordajes', 1, 5),
(52, 'Grips', 1, 5),
(53, 'Pelotas', 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(75) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'usuario',
  `direccion` varchar(60) NOT NULL,
  `localidad` varchar(40) NOT NULL,
  `provincia` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `contrasena` varchar(256) NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`dni`, `nombre`, `apellidos`, `telefono`, `rol`, `direccion`, `localidad`, `provincia`, `email`, `contrasena`, `activo`) VALUES
('05036599J', 'esteban ', 'garcia gonzalez', '645321654', 'usuario', '', '', '', 'esteban@correo.com', '$2y$10$DZBfO6aLw3ukiKW8n2wMLOWFC0Aw4VOWuaPMK2HXQIqHx89Kntq/a', 1),
('13997380V', 'ana', 'rodriguez fernandez', '632156541', 'empleado', 'calle severo ochoa', 'crevillente', 'alicante', 'ana@correo.com', '$2y$10$qe4yCqRq.gvK7d8xNf3oNOeBiNSALLlCTJ4wUljTyXBj4Cla.7jF.', 1),
('15419519C', 'jesus', 'binade trives', '647321698', 'administrador', 'calle severo ochoa', 'elche', 'alicante', 'jesus@correo.com', '$2y$10$CmUalWMamIbPxd1/ezawGu1s7PZ7ML5E9qTUsQBjHluSSPTecCWdS', 1),
('17285347L', 'javier', 'cano vicente', '', 'usuario', '', '', '', 'javier@correo.com', '$2y$10$whtN66KbnyNjvsliTQDyuu00n1yE5eXgPEZR91QnDSqt/amzkyYbu', 1),
('21498028C', 'lucas', 'fernandez herrero', '', 'usuario', '', '', '', 'lucas@correo.com', '$2y$10$/UMPkStuzGNWrCNSwHbh5Oktr6UzDtzM7lzBxGyiwe2ZHrawzYdVS', 1),
('22281826R', 'juan', 'garcia martinez', '654123684', 'usuario', 'calle barcelona', 'orihuela', 'alicante', 'juan@correo.com', '$2y$10$KspuTlc0bEKDSV0/zEk8G.SYtg3ujTSSoT.YX/Tl0UVaPTivsPfKK', 0),
('40460424C', 'sandra', 'gimenez ochoa', '649876598', 'empleado', 'avenida libertad', 'elche', 'alicante', 'sandra@gmail.com', '$2y$10$qOpjQ992LJ29TiILLV/77.gYxdJ.DkJLeXiOZQrQc1nhyUyAjTiG6', 1),
('49066652Q', 'maria', 'fernandez herrero', '', 'usuario', '', '', '', 'maria@correo.com', '$2y$10$s1qvj2iDvv/VpGw3Nh4rD.p22uz.z0B/z6tV3CDAuRja0luzitXVu', 1),
('53398475L', 'jose', 'martinez torres', '', 'usuario', '', '', '', 'jose@correo.com', '$2y$10$N2tbQ11Ey8c8i2Y5sqEPFuE2eCx0zuh9C7zBQqDtAzUL/sAyp2u5K', 1),
('66181253H', 'irene', 'ferrer escudero', '', 'usuario', '', '', '', 'irene@correo.com', '$2y$10$jRzD.rnDTFylS7jwpDJXPesbywY9mnbpjc1MuERc.kjpMRbSrbc0a', 1),
('71282101H', 'daniel', 'fernandez garcia', '', 'usuario', '', '', '', 'daniel@correo.com', '$2y$10$uQIGqnfqf.Tiaf5Huy7ut.krihAxX3E8v6TAg0QU54xKkFg5HPYEa', 1),
('75729462F', 'lucia', 'hernandez gimenez', '', 'usuario', '', '', '', 'lucia@correo.com', '$2y$10$f9/Q5d3dq.Ufc80SvsxzoeuwPLdmg0vhXzMd0VcuU0Xkl/Jwv3aTW', 1),
('81006738W', 'maria', 'herrero lorenzo', '', 'usuario', '', '', '', 'maria2@correo.com', '$2y$10$k4mptmLavX5VLnXkqJDZKeCqg1DF.DoDJk/hwrURO7/LvXabRNocy', 1),
('92411943J', 'marta', 'romero pascual', '', 'usuario', '', '', '', 'marta@correo.com', '$2y$10$m88XFQssaz69rsGqnh92M.x2IsU0uErT6ygemocJL9Ggs3srz7Wu6', 1),
('96837541C', 'pedro', 'garcia muñoz', '639854658', 'usuario', 'calle valencia', 'orihuela', 'alicante', 'pedro@gmail.com', '$2y$10$XDewHI5FE/hSJANyx.PoyeF4rMCSBj20DJOXIQJwT5HzMTdkUJycm', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineapedido`
--

CREATE TABLE `lineapedido` (
  `numPedido` int(11) NOT NULL,
  `numLinea` int(11) NOT NULL,
  `codArticulo` varchar(8) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` float NOT NULL,
  `preciodest` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `idPedido` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` float NOT NULL,
  `estado` smallint(6) NOT NULL,
  `codCliente` varchar(9) NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigo` (`codigo`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigo` (`codigo`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`dni`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dni` (`dni`);

--
-- Indices de la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD PRIMARY KEY (`numPedido`),
  ADD UNIQUE KEY `numLinea` (`numLinea`),
  ADD KEY `numPedido` (`numPedido`),
  ADD KEY `numLinea_2` (`numLinea`),
  ADD KEY `numPedido_2` (`numPedido`,`codArticulo`),
  ADD KEY `codArticulo` (`codArticulo`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `idPedido` (`idPedido`),
  ADD KEY `codCliente` (`codCliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  MODIFY `numPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lineapedido`
--
ALTER TABLE `lineapedido`
  ADD CONSTRAINT `lineapedido_ibfk_1` FOREIGN KEY (`numPedido`) REFERENCES `pedidos` (`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lineapedido_ibfk_2` FOREIGN KEY (`codArticulo`) REFERENCES `articulos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`codCliente`) REFERENCES `clientes` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
