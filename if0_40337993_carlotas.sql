-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql100.infinityfree.com
-- Tiempo de generación: 31-01-2026 a las 21:56:05
-- Versión del servidor: 11.4.9-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_40337993_carlotas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesorios`
--

CREATE TABLE `accesorios` (
  `id_accesorio` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `accesorios`
--

INSERT INTO `accesorios` (`id_accesorio`, `id_categoria`, `codigo_barras`, `nombre`, `descripcion`, `precio`, `imagen_url`, `stock`, `visible`, `fecha_creacion`) VALUES
(5, 1, '7501357071482', 'collar plata', 'collar plata 925', '500.00', '1762373806_690bb0ae19346.jpg', 11, 1, '2025-11-05 20:16:46'),
(6, 3, '6932849425208', 'Anillo de oro', 'Anillo de oro de 8k', '6000.00', '1762375743_690bb83fbb32c.jpeg', 10, 1, '2025-11-05 20:49:03'),
(7, 2, '7506129441326', 'Brazalete de corazones', 'Brazalete de acero inoxidable con forma de corazones', '120.00', '1762375829_690bb8959d7d6.jpeg', 13, 1, '2025-11-05 20:50:29'),
(8, 1, '6971664934847', 'Collar Rosado Enzo', 'Collar de acero inoxidable', '130.00', '1764707414_692f4c56db232.jpg', 1, 1, '2025-12-02 20:30:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(1, 'Collares'),
(2, 'Brazaletes'),
(3, 'Anillos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_accesorio` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id_detalle`, `id_pedido`, `id_accesorio`, `cantidad`, `precio_unitario`) VALUES
(4, 5, 5, 2, '500.00'),
(5, 6, 5, 1, '500.00'),
(6, 7, 7, 1, '120.00'),
(7, 7, 8, 1, '130.00'),
(8, 8, 8, 1, '130.00'),
(9, 8, 7, 1, '120.00'),
(10, 9, 8, 1, '130.00'),
(11, 9, 5, 1, '500.00'),
(12, 10, 8, 1, '130.00'),
(13, 11, 8, 1, '130.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_pedido` enum('pendiente','en proceso de envio','completado') NOT NULL DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL,
  `envio_nombre` varchar(100) NOT NULL,
  `envio_apellido` varchar(100) NOT NULL,
  `envio_direccion` varchar(255) NOT NULL,
  `envio_colonia` varchar(100) NOT NULL,
  `envio_cp` varchar(10) NOT NULL,
  `envio_num_interior` varchar(50) DEFAULT NULL,
  `envio_estado` varchar(100) NOT NULL,
  `envio_municipio` varchar(100) NOT NULL,
  `envio_telefono` varchar(20) NOT NULL,
  `envio_indicaciones` text DEFAULT NULL,
  `envio_tipo` varchar(100) NOT NULL,
  `envio_costo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_pedido`, `estado_pedido`, `total`, `envio_nombre`, `envio_apellido`, `envio_direccion`, `envio_colonia`, `envio_cp`, `envio_num_interior`, `envio_estado`, `envio_municipio`, `envio_telefono`, `envio_indicaciones`, `envio_tipo`, `envio_costo`) VALUES
(5, 2, '2025-11-05 20:25:40', 'pendiente', '1075.00', 'ALEX', 'TIPA', 'OCOSINGO 126', 'ISSSTE', '29060', '', 'CHIAPAS', 'TUXTLA GUTIÉRREZ', '0123456789', 'FAFAFA', 'MexPost México 2-3 Semanas', '75.00'),
(6, 2, '2025-11-05 20:32:06', 'pendiente', '575.00', 'chicharito', 'mamarre', 'imaginaria 123', 'lolazo', '12345', '', 'gaseoso', 'acalavuelta', '0123456789', 'a 10 casas del kinder', 'MexPost México 2-3 Semanas', '75.00'),
(7, 2, '2025-12-02 20:45:18', 'completado', '250.00', 'Venta', 'Mostrador', 'Tienda Física', '-', '00000', NULL, '-', '-', '0000000000', NULL, 'Entregado en Tienda', '0.00'),
(8, 2, '2025-12-02 21:03:49', 'completado', '250.00', 'Venta', 'Mostrador', 'Tienda Física', '-', '00000', NULL, '-', '-', '0000000000', NULL, 'Entregado en Tienda', '0.00'),
(9, 2, '2025-12-02 21:29:17', 'completado', '630.00', 'Venta', 'Mostrador', 'Tienda Física', '-', '00000', NULL, '-', '-', '0000000000', NULL, 'Entregado en Tienda', '0.00'),
(10, 2, '2025-12-02 21:48:18', 'completado', '130.00', 'Venta', 'Mostrador', 'Tienda Física', '-', '00000', NULL, '-', '-', '0000000000', NULL, 'Entregado en Tienda', '0.00'),
(11, 2, '2025-12-02 21:53:33', 'completado', '130.00', 'Getsemani', 'Madrazo', 'Tienda Física', '-', '00000', NULL, '-', '-', '0123456789', NULL, 'Entregado en Tienda', '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `tipo_usuario` enum('cliente','admin') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `tipo_usuario`, `fecha_registro`) VALUES
(1, 'Admin', 'Carlota\'s', 'admin@carlotas.com', '$2y$10$EZyenWma0k6Vfo/nk7SaEuXwGlFQTJfwAlcG4tyaS5J4oZsteDnii', 'admin', '2025-11-04 23:59:06'),
(2, 'ALEX', 'TIPA', '123@123.com', '$2y$10$v9Gq2hmSJMMnuYL3V8ansOEocXJGKeGfmvSTOg2f07.tRWNy7ur0O', 'cliente', '2025-11-05 00:41:39'),
(5, 'Mamberroi', 'Admin', 'mamberroi@admin.com', '$2y$10$7UQ2Z17tXg1aoYY6gkAP/OfGTYKAelZoO/Hala9s.TQVTiSutoMoe', 'admin', '2025-11-05 03:15:34'),
(7, 'oracio', 'pancracio', 'oracio@pancracio.com', '$2y$10$ChAHo.7unT4GCF.tI6CvguuQY.QCkGpEXPIsg.V6uR5w23aR/cYqu', 'cliente', '2025-11-05 15:32:54'),
(8, 'alex', 'mamarre', 'alex@mamarre.com', '$2y$10$zj4a.Ju6pTTdE2Ilcb/9LutVCdvKQ/wMeVioKA3qShv/Ov9RpriAy', 'admin', '2025-11-05 15:49:00'),
(9, 'Prueba', 'Segundo', 'prueba@segundo.com', '$2y$10$tg6L9vSCpwwnsKtiGQpErusiJeFB476jzl1WwHjNk21pRwnY10rW2', 'cliente', '2025-11-05 20:44:16'),
(10, 'Guadalupe', 'Gonzalez Cruz', 'cruzalondra150@gmail.com', '$2y$10$5picQIlu0gEjum2U2DT2HuuBDADI3QEPFlkQkavw62EJawfY108iO', 'cliente', '2025-11-06 00:22:54'),
(11, 'Guadalupe', 'Gonzalez Cruz', 'guadalupecruz0041@gmail.com', '$2y$10$9CnLTKcyPuzw..0KyQT.AOL8E7C8CDILlWDeQVSgsWhJFaT6P6.By', 'cliente', '2025-11-06 00:24:38'),
(12, 'Pilar', 'Perez', 'isabel@pilar.com', '$2y$10$0cvVjS/2ZUYkWfNw5n76HOGQ82TxnkKPtNZpVN2ju02kAlc5ZiY7y', 'cliente', '2025-12-01 07:26:16'),
(13, 'Cliente', 'Mostrador', 'mostrador@tienda.com', 'NO_LOGIN', 'cliente', '2025-12-02 20:37:37');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesorios`
--
ALTER TABLE `accesorios`
  ADD PRIMARY KEY (`id_accesorio`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_accesorio` (`id_accesorio`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesorios`
--
ALTER TABLE `accesorios`
  MODIFY `id_accesorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesorios`
--
ALTER TABLE `accesorios`
  ADD CONSTRAINT `accesorios_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`id_accesorio`) REFERENCES `accesorios` (`id_accesorio`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
