-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 30-10-2025 a las 13:33:28
-- Versión del servidor: 10.5.27-MariaDB
-- Versión de PHP: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recibos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arbitros`
--

CREATE TABLE `arbitros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `activo` enum('SI','NO') NOT NULL DEFAULT 'SI',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(10) DEFAULT NULL,
  `temporada_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `abreviatura`, `temporada_id`) VALUES
(1, 'JUNIOR MASCULINO', 'JM', NULL),
(3, 'PRIMERA DIVISIÓN NACIONAL MASCULINA', 'PNM', NULL),
(4, 'PRIMERA DIVISIÓN NACIONAL FEMENINA', 'PNF', NULL),
(5, '3X3 INFANTIL MASCULINO', '3IM', NULL),
(6, '3X3 INFANTIL FEMENINO', '3IF', NULL),
(7, '3X3 CADETE MASCULINO', '3CM', NULL),
(8, '3X3 CADETE FEMENINO', '3CF', NULL),
(9, 'CADETE MASCULINO', 'CM', NULL),
(10, 'CADETE FEMENINO', 'CF', NULL),
(11, 'INFANTIL MASCULINO', 'IM', NULL),
(12, 'INFANTIL FEMENINO', 'IF', NULL),
(13, 'MINIBASKET', 'MB', NULL),
(14, 'PRIMERA FEB', 'PFEB', NULL),
(15, 'SEGUNDA FEB', 'SFEB', NULL),
(16, 'TERCERA FEB', 'TFEB', NULL),
(17, 'LIGA FEMENINA ENDESA', 'LFE', NULL),
(18, 'LIGA FEMENINA CHALLENGE', 'LFC', NULL),
(19, 'LIGA FEMENINA 2', 'LF2', NULL),
(20, '1ª DIVISIÓN FEMENINO - LIGA REGULAR', '1DFLR', NULL),
(21, '1ª DIVISIÓN FEMENINO - PLAY OFF', '1DFPO', NULL),
(22, 'SUPERCOPA IBERCAJA FEMENINA (F. Aragonesa)', 'SCIF', 1),
(23, 'JUNIOR FEMENINA', 'JF', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desplazamientos`
--

CREATE TABLE `desplazamientos` (
  `id` int(11) NOT NULL,
  `temporada_id` int(11) NOT NULL,
  `precio_km` decimal(5,3) DEFAULT 0.210,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `desplazamientos`
--

INSERT INTO `desplazamientos` (`id`, `temporada_id`, `precio_km`, `activo`) VALUES
(1, 1, 0.260, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `temporada_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `categoria_id`, `temporada_id`, `usuario_id`) VALUES
(1, 'C.B Cenicero', 3, 2, NULL),
(2, 'C.B Haro', 1, 2, NULL),
(3, 'C.B. CLAVIJO A CADETE', 1, 1, NULL),
(4, 'LOYOLA', 1, 1, NULL),
(5, 'LOGROBASKET', 3, 1, NULL),
(6, 'CB CENICERO-MARISTAS', 3, 1, NULL),
(7, 'UNIBASKET LOGROÑO', 22, 1, NULL),
(8, 'NAJERA TURISMO', 22, 1, NULL),
(9, 'C.B. CLAVIJO B', 1, 1, NULL),
(10, 'C.B. Haro', 1, 1, NULL),
(11, 'LOGROBASKET 08', 1, 1, NULL),
(12, 'CB CENICERO-MARISTAS', 1, 1, NULL),
(13, 'Tecnomarmol Calahorra Basket JM', 1, 1, NULL),
(14, 'LOGROCLIMA', 3, 1, NULL),
(15, 'C.B. CLAVIJO A', 1, 1, NULL),
(16, 'CBA NATURAL WORLD JUN', 1, 1, NULL),
(17, 'LOGROBASKET U23', 3, 1, NULL),
(18, 'MARISTAS LOGROÑO', 3, 1, NULL),
(19, 'LOYOLA 08', 23, 1, NULL),
(20, 'Junior Ezcaray', 23, 1, NULL),
(21, 'MARISTAS LOGROÑO SF', 23, 1, NULL),
(22, 'SANIGNACIO JUNIOR', 1, 1, NULL),
(23, 'PRESEI NORTE C.B. ALFARO', 1, 1, NULL),
(24, 'C.B. CLAVIJO', 23, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invites`
--

CREATE TABLE `invites` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_enviado` tinyint(1) DEFAULT 0,
  `usado` tinyint(1) DEFAULT 0,
  `nombre_usuario` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT (current_timestamp() + interval 3 day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `jornada` varchar(20) DEFAULT NULL,
  `equipo_local_id` int(11) NOT NULL,
  `equipo_visitante_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `tarifa_id` int(11) DEFAULT NULL,
  `temporada_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT 0.00,
  `importe_desplazamiento` decimal(10,2) DEFAULT 0.00,
  `usa_tablet` tinyint(1) DEFAULT 0,
  `rol` varchar(50) DEFAULT NULL,
  `tablet` tinyint(1) DEFAULT 0,
  `km` decimal(6,2) DEFAULT 0.00,
  `dieta` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

CREATE TABLE `tarifas` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `temporada_id` int(11) NOT NULL,
  `rol` enum('arbitro','arbitro_solo','oficial','oficial_solo') NOT NULL,
  `importe` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporadas`
--

CREATE TABLE `temporadas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activa` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `temporadas`
--

INSERT INTO `temporadas` (`id`, `nombre`, `activa`, `fecha_creacion`) VALUES
(1, 'Temporada 2025/2026', 1, '2025-10-27 07:54:39'),
(2, 'Temporada 2024/2025', 0, '2025-10-27 11:54:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `domicilio` varchar(255) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `cuenta_bancaria` varchar(34) DEFAULT NULL,
  `dni` varchar(15) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `colegiado` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arbitros`
--
ALTER TABLE `arbitros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_arbitros_nombre` (`nombre`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temporada_id` (`temporada_id`);

--
-- Indices de la tabla `desplazamientos`
--
ALTER TABLE `desplazamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temporada_id` (`temporada_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `temporada_id` (`temporada_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `invites`
--
ALTER TABLE `invites`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_local_id` (`equipo_local_id`),
  ADD KEY `equipo_visitante_id` (`equipo_visitante_id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `temporada_id` (`temporada_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `tarifa_id` (`tarifa_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `temporada_id` (`temporada_id`);

--
-- Indices de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arbitros`
--
ALTER TABLE `arbitros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `desplazamientos`
--
ALTER TABLE `desplazamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `invites`
--
ALTER TABLE `invites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `desplazamientos`
--
ALTER TABLE `desplazamientos`
  ADD CONSTRAINT `desplazamientos_ibfk_1` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipos_ibfk_2` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipos_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `partidos_ibfk_1` FOREIGN KEY (`equipo_local_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_2` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `partidos_ibfk_4` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_5` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_6` FOREIGN KEY (`tarifa_id`) REFERENCES `tarifas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD CONSTRAINT `tarifas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarifas_ibfk_2` FOREIGN KEY (`temporada_id`) REFERENCES `temporadas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
