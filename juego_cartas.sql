-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 09-11-2026 a las 00:24:36
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `juego_cartas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_partidas`
--

DROP TABLE IF EXISTS `historial_partidas`;
CREATE TABLE IF NOT EXISTS `historial_partidas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ganador` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perdedor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `puntaje_j1` int DEFAULT NULL,
  `puntaje_j2` int DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_partidas`
--

INSERT INTO `historial_partidas` (`id`, `ganador`, `perdedor`, `puntaje_j1`, `puntaje_j2`, `fecha`) VALUES
(1, 'Boca', 'Racing', 1, 2, '2026-10-15 18:38:00'),
(2, 'Racing', 'Computadora', 3, 0, '2026-10-15 19:09:52'),
(3, 'Gimnasia (M)', 'Deportivo Madryn', 3, 0, '2026-10-15 19:28:40'),
(4, 'River', 'Computadora', 3, 0, '2026-10-15 20:42:08'),
(5, 'River', 'Computadora', 2, 1, '2026-10-21 19:21:17'),
(6, 'Chaco For Ever', 'Computadora', 2, 1, '2026-10-21 20:29:53'),
(7, 'River', 'Computadora', 3, 0, '2026-11-09 00:23:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

DROP TABLE IF EXISTS `jugadores`;
CREATE TABLE IF NOT EXISTS `jugadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ataque` int NOT NULL,
  `defensa` int NOT NULL,
  `control` int NOT NULL,
  `carta_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `division` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `escudo_equipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'img/logo.png',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id`, `nombre`, `ataque`, `defensa`, `control`, `carta_img`, `equipo`, `division`, `escudo_equipo`) VALUES
(1, 'Armani', 45, 87, 42, 'img/cartas/armani.png', 'River', 'primera', 'img/river.png'),
(2, 'Borja', 85, 60, 70, 'img/cartas/borja.png', 'River', 'primera', 'img/river.png'),
(3, 'Nacho F.', 75, 70, 80, 'img/cartas/nacho.png', 'River', 'primera', 'img/river.png'),
(4, 'Marchesin', 50, 80, 60, 'img/cartas/marchesin.png', 'Boca', 'primera', 'img/boca.png'),
(5, 'Cavani', 82, 60, 75, 'img/cartas/gagani.png', 'Boca', 'primera', 'img/boca.png'),
(6, 'Paredes', 78, 75, 87, 'img/cartas/paredes.png', 'Boca', 'primera', 'img/boca.png'),
(7, 'Cambeses', 52, 81, 63, 'img/cartas/cambeses.png', 'Racing', 'primera', 'img/racing.png'),
(8, 'A.Maravilla', 87, 64, 76, 'img/cartas/maravilla.png', 'Racing', 'primera', 'img/racing.png'),
(9, 'Nardoni', 74, 75, 85, 'img/cartas/nardoni.png', 'Racing', 'primera', 'img/racing.png'),
(10, 'Longo', 73, 71, 70, 'img/cartas/longo.png', 'Belgrano', 'primera', 'img/belgrano.png'),
(11, 'Zelarayan', 81, 68, 75, 'img/cartas/zelarayan.png', 'Belgrano', 'primera', 'img/belgrano.png'),
(12, 'L. Lopez', 40, 80, 73, 'img/cartas/llopez.png', 'Belgrano', 'primera', 'img/belgrano.png'),
(13, 'Roffo', 50, 77, 60, 'img/cartas/roffo.png', 'Instituto', 'primera', 'img/instituto.png'),
(14, 'Lodico', 70, 77, 58, 'img/cartas/lodico.png', 'Instituto', 'primera', 'img/instituto.png'),
(15, 'Rodríguez', 74, 74, 77, 'img/cartas/rodriguez.png', 'Instituto', 'primera', 'img/instituto.png'),
(16, 'Matias Romero', 75, 47, 69, 'img/cartas/mromero.png', 'Chaco For Ever', 'segunda', 'img/forever.png'),
(17, 'Canuto', 37, 74, 55, 'img/cartas/canuto.png', 'Chaco For Ever', 'segunda', 'img/forever.png'),
(18, 'Valdez', 60, 74, 62, 'img/cartas/valdez.png', 'Chaco For Ever', 'segunda', 'img/forever.png'),
(19, 'Yair Bonnin', 40, 74, 62, 'img/cartas/bonnin.png', 'Deportivo Madryn', 'segunda', 'img/madryn.png'),
(20, 'Luis Silba', 76, 55, 68, 'img/cartas/silba.png', 'Deportivo Madryn', 'segunda', 'img/madryn.png'),
(21, 'Recalde', 70, 71, 75, 'img/cartas/recalde.png', 'Deportivo Madryn', 'segunda', 'img/madryn.png'),
(22, 'Brian Ferreyra', 77, 50, 65, 'img/cartas/ferreyra.png', 'Gimnasia (M)', 'segunda', 'img/gimnasiam.png'),
(23, 'Rigamonti', 50, 76, 65, 'img/cartas/rigamonti.png', 'Gimnasia (M)', 'segunda', 'img/gimnasiam.png'),
(24, 'Mondino', 60, 71, 76, 'img/cartas/mondino.png', 'Gimnasia (M)', 'segunda', 'img/gimnasiam.png'),
(25, 'Leonel Niz', 72, 62, 68, 'img/cartas/niz.png', 'Sarmiento', 'segunda', 'img/SarmientoR.png'),
(26, 'Di Pietro', 67, 70, 68, 'img/cartas/dipietro.png', 'Sarmiento', 'segunda', 'img/SarmientoR.png'),
(27, 'Yacaruso', 35, 71, 50, 'img/cartas/yacaruso.png', 'Sarmiento', 'segunda', 'img/SarmientoR.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin', ''),
(2, 'jugador', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rol_id` int NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuarios_roles` (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `password_hash`, `creado_en`, `rol_id`) VALUES
(2, 'hola', 'marianocardozo477@gmail.com', '$2y$10$dVI.5AHMsUly09XRAzcuGemIoNWlGnzuKxZTJwhi6cpRBo.TpXwLS', '2026-10-15 19:28:14', 1),
(3, 'rampi123', 'berrytag@yahoo.com', '$2y$10$T3AX39X/joFtLNpVuvK0LOq31Av93howL/lioJDV123Esh43cVsm2', '2026-10-21 19:20:32', 2),
(4, 'gustavo67', 'gustavo67@gmail.com', '$2y$10$dFVbv/mAMr2Ceyu2Tb/4n.1Kc.0JG8jmuqEYIfuN.1JWgP3SEdqg.', '2026-10-21 20:29:02', 2),
(5, 'rampi', 'enzoxd097@gmail.com', '$2y$10$tjZVVyl8MHQOjT8lPCTVCOyRuZ6lWCthh7EJS7LCOdK7dzQtGNlbS', '2026-11-09 00:23:01', 2);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
