-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2025 a las 17:41:12
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
-- Base de datos: `bibliogestor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `idLog` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `idCategoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`idCategoria`, `nombre`, `descripcion`) VALUES
(1, 'Novela', 'Ficción narrativa'),
(2, 'Cienciaaa', 'Libros de divulgación científica'),
(3, 'Tecnología', 'Informática, programación y avances tecnológicos'),
(4, 'Historia', 'Eventos y figuras históricas'),
(5, 'Infantil', 'Lecturas para niños');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `idConfig` int(11) NOT NULL,
  `diasMaximoPrestamo` int(11) DEFAULT 14,
  `valorMultaDia` int(11) DEFAULT 1000,
  `multaMaximaSuspendida` int(11) NOT NULL DEFAULT 50000,
  `diasAvisoVencimiento` int(11) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`idConfig`, `diasMaximoPrestamo`, `valorMultaDia`, `multaMaximaSuspendida`, `diasAvisoVencimiento`) VALUES
(1, 2, 1000, 50000, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `idEtiqueta` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `idLibro` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `autor` varchar(150) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `editorial` varchar(150) DEFAULT NULL,
  `anio` int(4) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `estado` enum('Disponible','Prestado') NOT NULL DEFAULT 'Disponible',
  `portada` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`idLibro`, `titulo`, `autor`, `isbn`, `editorial`, `anio`, `categoria_id`, `estado`, `portada`, `fecha_creacion`) VALUES
(16, 'El Principito', 'Antoine de Saint-Exupéry', '9780156013987', NULL, 1943, 5, 'Disponible', 'p6.png', '2025-11-24 17:23:55'),
(21, 'Don Quijote de la Mancha', 'Miguel de Cervantes', '9788420412146', 'Alfaguara', 1605, 1, 'Prestado', 'p2.jpg', '2025-11-24 18:11:59'),
(22, 'El Principito', 'Antoine de Saint-Exupéry', '9780156012195', 'Salamandra', 1943, 2, 'Disponible', 'p3.jpg', '2025-11-24 18:11:59'),
(23, '1984', 'George Orwell', '9780451524935', 'Penguin', 1949, 3, 'Prestado', 'p4.jpg', '2025-11-24 18:11:59'),
(24, 'Orgullo y Prejuicio', 'Jane Austen', '9780141439518', 'Penguin', 1813, 1, 'Disponible', 'p5.jpg', '2025-11-24 18:11:59'),
(25, 'Harry Potter y la piedra filosofal', 'J. K. Rowling', '9788478884452', 'Salamandra', 1997, 4, 'Disponible', 'p6.jpg', '2025-11-24 18:11:59'),
(26, 'El Hobbit', 'J. R. R. Tolkien', '9780547928227', 'Minotauro', 1937, 4, 'Disponible', 'p7.jpg', '2025-11-24 18:11:59'),
(27, 'El diario de Ana Frank', 'Ana Frank', '9780553296983', 'Debolsillo', 1947, 2, 'Disponible', 'p8.jpg', '2025-11-24 18:11:59'),
(28, 'Crónica de una muerte anunciada', 'Gabriel García Márquez', '9780307387387', 'Sudamericana', 1981, 1, 'Disponible', 'p9.jpg', '2025-11-24 18:11:59'),
(29, 'La sombra del viento', 'Carlos Ruiz Zafón', '9788408172171', 'Planeta', 2001, 1, 'Disponible', 'p10.jpg', '2025-11-24 18:11:59'),
(30, 'El alquimista', 'Paulo Coelho', '9780061122415', 'Planeta', 1988, 2, 'Prestado', 'p11.jpg', '2025-11-24 18:11:59'),
(31, 'Fahrenheit 451', 'Ray Bradbury', '9781451673319', 'Simon & Schuster', 1953, 3, 'Disponible', 'p12.jpg', '2025-11-24 18:11:59'),
(32, 'El retrato de Dorian Gray', 'Oscar Wilde', '9780141442464', 'Penguin', 1890, 1, 'Disponible', 'p13.jpg', '2025-11-24 18:11:59'),
(33, 'La ladrona de libros', 'Markus Zusak', '9780375842207', 'Alfaguara', 2005, 2, 'Disponible', 'p14.jpg', '2025-11-24 18:11:59'),
(34, 'Matar a un ruiseñor', 'Harper Lee', '9780060935467', 'HarperCollins', 1960, 2, 'Disponible', 'p15.jpg', '2025-11-24 18:11:59'),
(35, 'El código Da Vinci', 'Dan Brown', '9780307474278', 'Planeta', 2003, 5, 'Disponible', 'p16.jpg', '2025-11-24 18:11:59'),
(36, 'Ready Player One', 'Ernest Cline', '9780307887443', 'Crown', 2011, 4, 'Disponible', 'p17.jpg', '2025-11-24 18:11:59'),
(37, 'Los juegos del hambre', 'Suzanne Collins', '9780439023528', 'Scholastic', 2008, 4, 'Disponible', 'p18.jpg', '2025-11-24 18:11:59'),
(38, 'It', 'Stephen King', '9781501142970', 'Scribner', 1986, 4, 'Disponible', 'p19.jpg', '2025-11-24 18:11:59'),
(39, 'Sherlock Holmes: Estudio en escarlata', 'Arthur Conan Doyle', '9780486477831', 'Wordsworth', 1887, 5, 'Disponible', 'p20.jpg', '2025-11-24 18:11:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro_etiqueta`
--

CREATE TABLE `libro_etiqueta` (
  `idLibro` int(11) NOT NULL,
  `idEtiqueta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `idNotificacion` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`idNotificacion`, `usuario_id`, `mensaje`, `leido`, `fecha`) VALUES
(1, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 5000 por 5 día(s) de retraso.', 0, '2025-11-25 13:58:45'),
(2, 2, 'Se ha extendido el plazo del préstamo del libro \'1984\' hasta 2025-11-21.', 0, '2025-11-25 16:45:15'),
(3, 2, 'Se ha extendido el plazo del préstamo del libro \'1984\' hasta 2025-11-22.', 0, '2025-11-25 16:45:29'),
(4, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 3000 por 3 día(s) de retraso.', 0, '2025-11-25 17:03:42'),
(5, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:26:57'),
(6, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:26:57'),
(7, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:27:12'),
(8, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:27:12'),
(9, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:28:39'),
(10, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:28:39'),
(11, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:28:41'),
(12, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:28:41'),
(13, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:28:42'),
(14, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:28:42'),
(15, 2, 'Tu préstamo del libro \'1984\' tiene ahora multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:28:45'),
(16, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s) (fecha: 2025-11-27).', 0, '2025-11-25 18:28:45'),
(17, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:36'),
(18, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:36'),
(19, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:40'),
(20, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:40'),
(21, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:42'),
(22, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:42'),
(23, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:43'),
(24, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:43'),
(25, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:44'),
(26, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:44'),
(27, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:44'),
(28, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:44'),
(29, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:45'),
(30, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:45'),
(31, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:45'),
(32, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:45'),
(33, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:45'),
(34, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:45'),
(35, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:47'),
(36, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:47'),
(37, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:47'),
(38, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:47'),
(39, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:29:47'),
(40, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:29:47'),
(41, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:30:49'),
(42, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:30:49'),
(43, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:30:52'),
(44, 4, 'Tu préstamo del libro \'El alquimista\' vence en 1 día(s).', 0, '2025-11-25 18:30:52'),
(45, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:32:29'),
(46, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 35000 por 35 día(s) de retraso.', 0, '2025-11-25 18:32:29'),
(47, 4, 'Se ha extendido el plazo del préstamo del libro \'El alquimista\' hasta 2025-11-11.', 0, '2025-11-25 18:32:49'),
(48, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:32:53'),
(49, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 15000 por 15 día(s) de retraso.', 0, '2025-11-25 18:32:53'),
(50, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 18:35:33'),
(51, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 15000 por 15 día(s) de retraso.', 0, '2025-11-25 18:35:33'),
(52, 4, 'Se ha extendido el plazo del préstamo del libro \'Crónica de una muerte anunciada\' hasta 2025-11-29.', 0, '2025-11-25 19:28:58'),
(53, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 19:31:04'),
(54, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 15000 por 15 día(s) de retraso.', 0, '2025-11-25 19:31:04'),
(55, 4, 'Tu préstamo del libro \'Crónica de una muerte anunciada\' tiene multa de 6000 por 6 día(s) de retraso.', 0, '2025-11-25 19:31:04'),
(56, 4, 'Tu préstamo del libro (ID: 28) tiene una multa de 6000 por 6 día(s) de retraso.', 0, '2025-11-25 19:31:31'),
(57, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 19:31:53'),
(58, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 15000 por 15 día(s) de retraso.', 0, '2025-11-25 19:31:53'),
(59, 2, 'Tu préstamo del libro \'1984\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-25 19:33:12'),
(60, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 15000 por 15 día(s) de retraso.', 0, '2025-11-25 19:33:12'),
(61, 1, 'Tu préstamo del libro \'Don Quijote de la Mancha\' tiene multa de 3000 por 3 día(s) de retraso.', 0, '2025-11-25 19:33:12'),
(62, 2, 'Tu préstamo del libro \'1984\' tiene multa de 5000 por 5 día(s) de retraso.', 0, '2025-11-27 07:42:16'),
(63, 4, 'Tu préstamo del libro \'El alquimista\' tiene multa de 16000 por 16 día(s) de retraso.', 0, '2025-11-27 07:42:16'),
(64, 1, 'Tu préstamo del libro \'Don Quijote de la Mancha\' tiene multa de 4000 por 4 día(s) de retraso.', 0, '2025-11-27 07:42:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `idPrestamo` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date NOT NULL,
  `fechaDevuelto` date DEFAULT NULL,
  `multa` int(11) DEFAULT 0,
  `estado` enum('Activo','Finalizado','Vencido') DEFAULT 'Activo',
  `multaPagada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`idPrestamo`, `usuario_id`, `libro_id`, `fechaPrestamo`, `fechaDevolucion`, `fechaDevuelto`, `multa`, `estado`, `multaPagada`) VALUES
(1, 2, 23, '2025-11-25', '2025-11-22', NULL, 5000, 'Activo', 0),
(2, 3, 35, '2025-11-25', '2025-12-09', '2025-11-25', 0, 'Finalizado', 1),
(3, 1, 28, '2025-11-25', '2025-12-09', '2025-11-25', 0, 'Finalizado', 1),
(4, 4, 30, '2025-11-25', '2025-11-11', NULL, 16000, 'Activo', 0),
(5, 4, 28, '2025-11-26', '2025-11-20', '2025-11-26', 6000, 'Finalizado', 0),
(6, 1, 21, '2025-11-26', '2025-11-23', NULL, 4000, 'Activo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Bibliotecario'),
(3, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `identificacion` varchar(50) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nombre`, `apellido`, `identificacion`, `correo`, `clave`, `rol_id`, `telefono`, `direccion`, `estado`, `fecha_creacion`, `fecha_modificacion`) VALUES
(1, 'Oscar Daniel', 'colorado', '123', 'da@da.com', '$2y$10$ezaqbcf60PGrBHOWt0EFleqykADmJ.lpu2qjgtmAUE4M5vtXMRR2i', 1, '123', 'tv', 1, '2025-11-24 16:38:26', '2025-11-24 16:42:15'),
(2, 'pepe', 'dias', '1234', 'pepe@pe.com', '$2y$10$vvXxv/8.uP0p14jjMb80venCr0Qs717zdCo7vhb.YSjHt4pElsttO', 3, '12334', 'r', 1, '2025-11-24 16:47:54', '2025-11-25 16:49:36'),
(3, 'papa', 'gome', '087', 'papa@pa.com', '$2y$10$T8ESQK91NLAHN3hKrIxTzOUq9DBYX2xtttUbHRYHa.YIwKod9yYE2', 2, '34', '5', 1, '2025-11-24 16:49:01', NULL),
(4, 'samuel', 'skalaaa', '12345', 'sa@sa.com', '$2y$10$V5mAtN2GoAzD4a00ua9WU.KuTAwXf42iJUElkk0kns3QPcGc36IIS', 2, '345', 'tvfd', 1, '2025-11-25 16:49:28', '2025-11-25 16:49:47');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`idLog`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`idCategoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`idConfig`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`idEtiqueta`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`idLibro`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `libro_etiqueta`
--
ALTER TABLE `libro_etiqueta`
  ADD PRIMARY KEY (`idLibro`,`idEtiqueta`),
  ADD KEY `idEtiqueta` (`idEtiqueta`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`idNotificacion`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`idPrestamo`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `identificacion` (`identificacion`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `idEtiqueta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `idLibro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `idPrestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`idCategoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `libro_etiqueta`
--
ALTER TABLE `libro_etiqueta`
  ADD CONSTRAINT `libro_etiqueta_ibfk_1` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libro_etiqueta_ibfk_2` FOREIGN KEY (`idEtiqueta`) REFERENCES `etiquetas` (`idEtiqueta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`idLibro`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`idRol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
