-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-11-2023 a las 15:16:57
-- Versión del servidor: 10.4.28-MariaDB-log
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vamos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler`
--

CREATE TABLE `alquiler` (
  `id` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `costo` float NOT NULL,
  `fecha_solicitud` date NOT NULL DEFAULT current_timestamp(),
  `aprobado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler`
--

INSERT INTO `alquiler` (`id`, `id_publicacion`, `id_usuario`, `fecha_inicio`, `fecha_fin`, `costo`, `fecha_solicitud`, `aprobado`) VALUES
(5, 20, 20, '2023-10-16', '2023-10-20', 800000, '2023-10-22', 2),
(6, 26, 7, '2023-10-27', '2023-10-29', 800000, '2023-10-26', 1),
(8, 22, 20, '2023-10-02', '2023-10-24', 90000, '2023-10-26', 1),
(9, 25, 7, '0000-00-00', '0000-00-00', 800000, '2023-10-26', 1),
(10, 24, 20, '1970-01-01', '1970-01-01', 800000, '2023-10-26', 1),
(11, 28, 19, '2023-10-27', '2023-10-30', 800000, '2023-10-23', 2),
(12, 20, 19, '1970-01-01', '1970-01-01', 800000, '2023-10-26', 1),
(16, 20, 19, '2023-10-14', '2023-10-15', 800000, '2023-10-10', 2),
(17, 20, 19, '2023-10-30', '2023-10-31', 800000, '2023-10-26', 1),
(18, 20, 19, '2023-11-02', '2023-11-09', 800000, '2023-10-24', 2),
(19, 25, 20, '2023-11-05', '2023-11-07', 100000, '2023-11-05', 2),
(20, 25, 7, '2023-11-20', '2023-11-21', 100000, '2023-11-15', 1),
(22, 21, 20, '2023-11-15', '2023-11-17', 50000, '2023-11-15', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigo_pais`
--

CREATE TABLE `codigo_pais` (
  `id` int(11) NOT NULL,
  `codigo` varchar(15) NOT NULL,
  `pais` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `codigo_pais`
--

INSERT INTO `codigo_pais` (`id`, `codigo`, `pais`) VALUES
(1, '+54', 'Argentina'),
(2, '+55', 'Brasil'),
(3, '+591', 'Bolivia'),
(4, '+001', 'Canada'),
(5, '+56', 'Chile'),
(6, '+57', 'Colombia'),
(7, '+506', 'Costa Rica'),
(8, '+53', 'Cuba'),
(9, '+593', 'Ecuador'),
(10, '+503', 'El Salvador'),
(11, '+1', 'Estados Unidos De América'),
(12, '+502', 'Guatemala'),
(13, '+504', 'Honduras '),
(14, '+52', 'México'),
(15, '+505', 'Nicaragua'),
(16, '+507', 'Panamá'),
(17, '+595', 'Paraguay'),
(18, '+51', 'Perú'),
(19, '+1-809', 'República Dominicana'),
(20, '+598', 'Uruguay'),
(21, '+58', 'Venezuela');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiqueta`
--

CREATE TABLE `etiqueta` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiqueta`
--

INSERT INTO `etiqueta` (`id`, `nombre`) VALUES
(1, 'Casa'),
(2, 'Cabaña'),
(5, 'Montaña'),
(6, 'Apartamento'),
(7, 'Finca'),
(8, 'Domo'),
(9, 'Habitación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiqueta_publicacion`
--

CREATE TABLE `etiqueta_publicacion` (
  `id_etiqueta` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiqueta_publicacion`
--

INSERT INTO `etiqueta_publicacion` (`id_etiqueta`, `id_publicacion`) VALUES
(1, 22),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(2, 30),
(2, 31),
(2, 35),
(2, 36),
(2, 37),
(5, 20),
(5, 22),
(5, 24),
(5, 30),
(5, 31),
(6, 29),
(6, 38),
(9, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagen`
--

CREATE TABLE `imagen` (
  `id` int(11) NOT NULL,
  `ruta` varchar(100) NOT NULL,
  `id_publicacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagen`
--

INSERT INTO `imagen` (`id`, `ruta`, `id_publicacion`) VALUES
(18, 'person-circle.svg', 20),
(19, 'airbnb.svg', 21),
(20, 'imgF1.webp', 23),
(21, 'imgF2.webp', 23),
(22, 'imgF3.webp', 23),
(23, 'imgF4.webp', 23),
(24, 'imgD1.webp', 24),
(25, 'imgD2.jpg', 24),
(26, 'imgD3.jpg', 24),
(27, 'imgD4.webp', 24),
(28, 'imgD5.jpg', 24),
(29, 'imgD6.jpg', 24),
(30, 'imgD7.jpg', 24),
(31, 'imgD8.webp', 24),
(32, 'imgD9.jpg', 24),
(33, 'imgI1.webp', 25),
(34, 'imgI2.webp', 25),
(35, 'imgI3.webp', 25),
(36, 'imgI4.webp', 25),
(37, 'imgI5.webp', 25),
(38, 'imgI6.webp', 25),
(39, 'imgI7.jfif', 25),
(40, 'imgI8.webp', 25),
(41, 'imgI9.webp', 25),
(42, 'imgI10.webp', 25),
(51, 'imgA1.webp', 28),
(52, 'imgA2.webp', 28),
(53, 'imgA3.webp', 28),
(54, 'imgA4.webp', 28),
(55, 'imgA5.webp', 28),
(56, 'imgA6.webp', 28),
(57, 'imgA7.jpeg', 28),
(58, 'imgA8.jpeg', 28),
(59, 'im.webp', 29),
(60, 'im2.jpeg', 29),
(61, 'im3.webp', 29),
(62, 'im4.webp', 29),
(63, 'im5.webp', 29),
(64, 'im6.webp', 29),
(65, 'im7.webp', 29),
(66, 'ime.webp', 30),
(67, 'ime2.webp', 30),
(68, 'ime3.webp', 30),
(69, 'ime4.webp', 30),
(70, 'ime5.webp', 30),
(71, 'imi (2).webp', 31),
(72, 'imi (3).webp', 31),
(73, 'imi (4).webp', 31),
(74, 'imi (5).webp', 31),
(75, 'imi (6).webp', 31),
(76, 'imi (7).webp', 31),
(77, 'imi (8).webp', 31),
(78, 'imi (9).webp', 31),
(79, 'imi.webp', 31),
(80, 'al.webp', 32),
(81, 'pa.webp', 33),
(82, 'pa1.webp', 33),
(83, 'pa2.jpeg', 33),
(84, 'pa3.webp', 33),
(85, 'pa4.jpeg', 33),
(86, 'pa4.webp', 33),
(87, 'pa5.webp', 33),
(88, 'pa6.webp', 33),
(89, 'pa7.webp', 33),
(90, '4aa67be8e3cd982db317b31126606303871b186b9c8f26b213438a061fd1.webp', 35),
(91, '05ba1abbb4ebf37f09692f7c2f9ab9150f1168318faaad277b173fc6b36a.webp', 35),
(92, '5c1f98cfad397c648627f4683869cacbee7de599f9fb632d7c98fbd1bd3d.webp', 35),
(93, '0111aac61ae401e0c34c656e4bf62c3bb3f85c3045ca156a28f71d9249d5.jpeg', 35),
(94, '434e24e91c2008b0badcf030eff0ee335d4ce1e01a43b777215038e30459.jpeg', 35),
(95, 'be7e2053e5586ec677f323cf436ec37e70cba7ac1eacace15322de406562.webp', 35),
(96, 'e0bbfc7cbf11c0e0f860d53de7b1a93079dc273ff099ef52f145748b5292.webp', 35),
(97, 'ee5b0f8d727857a6c845341de63bc5ca78e900e72b377f3a8916342f7b80.webp', 35),
(98, 'fd2637336bf566d8718f13c53137205ec1e489cc6571cf23ca5d38f49bcb.webp', 35),
(99, '0cd8a2dde4ec1d6a6c6764697a4cd899f0fe8e0c936b44253f6fe0aed75b.webp', 36),
(100, '08a7516700692c47ce8e72a8e8f86c14aefc195f6c7f31b817e29c857a40.webp', 36),
(101, '26b7d2c4d56a85ac93b469f1c3f52b4b9acca584379f2de50c34fe91a2f7.webp', 36),
(102, '1707dde6ed26e2743c7730eb21bd489498d8ce55fe757a8139cd452c6f5f.webp', 36),
(103, '553424c62a2278cc26119c64625d02e7b0e1733e63bf72f4be8910f4349e.jpeg', 36),
(104, 'a2fe6e16ff1b7e72324e5501bc6ba0cfe59e0e0ce893a8d808e76c8a006c.jpeg', 36),
(105, 'bf92e6af379837b4661ac68b0f5420ca7b3b541e037ba7d9d6923873c0ac.webp', 36),
(106, 'f6383db4a7a67e6a546cf875790b1e374c1976b24d7141e90c95d917dd04.jpeg', 36),
(107, '1.webp', 37),
(108, '5a34e283292d7583743f3b58f21792759bcd471b44a48f51da13eae94fcd.webp', 37),
(109, '11f69a5bf8d750dd1c4793a8b4da72a745d30709a4012e2982c7fb811352.webp', 37),
(110, '27dc8b12aebef3b849971d9c3d5a1bf7ff7d3c6f8c6d2764f45d117e4f9c.jpeg', 37),
(111, '29a5b49c2f414e408817ee084845dba7039e01d9cccb8def43a9f7d05f4b.webp', 37),
(112, '8477f6f0d0d5a5b0076c2ac98700ed34f617d591e11beafdbed19876745b.webp', 37),
(113, '422926ad9b4ba1110b618e854f6afa42ad188e531a535bd685c70eb51e2b.webp', 37),
(114, '2242633de047f548d60d60b9e566259f3a3b395e896ced40e23a9df63e6d.webp', 37),
(115, 'b1118fe09dccc8488c371b6f1a5874ccdaa31955618d94f0c2d6a34c0727.webp', 37),
(116, 'c2574751e89c7fa08afb52e573e6dab30f701c6759e82dba4b2382341a19.webp', 37),
(117, 'f00eb8c105379dab0a0ea390ec84c1c2a4cc49389fa6d5c06070fbe5ae74.jpeg', 37),
(118, 'fc004954b0fe5760cdbee1a28c478e381c2d82861a1d0ea4e139bf9226d7.jpeg', 37),
(119, '0fd627fdcafecba3247a04b100f81eee1f1ec3d133e71c776e4e1c7b2006.webp', 38),
(120, '5f1ad2e5b00fac1c219995bf062087d79d359847b906edf21b72b9d9dc0e.jpeg', 38),
(121, '39a6c5cd468eb7b52f1ff581bf1028632b9fd25839698759166504c5a027.webp', 38),
(122, '826a6e1a51c99d57761d6c111f099a74f1e7560e3ddf525e53d75ab33373.webp', 38),
(123, 'b102b74ff3e987dfa84649b4268f6d48aef702faf49c542d4985384570b7.webp', 38),
(124, 'cad448efc3d57b0307b6aaa45af221ac1cf8ed507c8a3fa0a9611a02fd51.webp', 38),
(125, 'fe6ffca34f8c34cbf8dec071a531c60afa7c85935949cd65608adf900eed.webp', 38);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interes`
--

CREATE TABLE `interes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `interes`
--

INSERT INTO `interes` (`id`, `nombre`) VALUES
(1, 'playa'),
(2, 'montaña'),
(3, 'correr'),
(4, 'familia'),
(5, 'rios'),
(7, 'ciudad'),
(8, 'esquiar'),
(9, 'nadar'),
(10, 'senderismo'),
(11, 'mascotas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interes_user`
--

CREATE TABLE `interes_user` (
  `id_usuario` int(11) NOT NULL,
  `id_interes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `interes_user`
--

INSERT INTO `interes_user` (`id_usuario`, `id_interes`) VALUES
(7, 2),
(7, 3),
(20, 3),
(20, 4),
(20, 5),
(23, 2),
(23, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `ubicacion` varchar(500) NOT NULL,
  `costo` double NOT NULL,
  `cupo` int(11) NOT NULL,
  `tiempo_minimo` int(11) DEFAULT NULL,
  `tiempo_maximo` int(11) DEFAULT NULL,
  `fecha_inicio_publicacion` date DEFAULT NULL,
  `fecha_fin_publicacion` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_solicitud` date NOT NULL DEFAULT current_timestamp(),
  `fecha_revision` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id`, `titulo`, `descripcion`, `ubicacion`, `costo`, `cupo`, `tiempo_minimo`, `tiempo_maximo`, `fecha_inicio_publicacion`, `fecha_fin_publicacion`, `estado`, `id_usuario`, `fecha_solicitud`, `fecha_revision`) VALUES
(20, 'Las  playas', '2 habitaciones, 2 baños', 'Las Rocas 558', 80000, 3, 1, 10, '2023-10-18', '2023-11-24', 1, 7, '2023-10-20', NULL),
(21, 'Las Palma', 'casa de un ambiente', 'Las Palmeras 927', 50000, 2, 1, 2, '2023-10-17', '2023-12-04', 1, 7, '2023-10-20', '2023-10-23'),
(22, 'Santa Rosa', 'Bella casa frente al mar', 'La Guarira', 90000, 1, 1, 2, '2023-10-01', '2023-11-30', 2, 7, '2023-10-20', '2023-10-20'),
(23, 'Las Rosas De Brasil', 'apto frente a la playa totalmente amoblado', 'Santa Rosa 1580', 10000, 3, 2, 5, '2023-10-02', '2023-11-24', 2, 7, '2023-10-20', '2023-10-21'),
(24, 'Las Casonas', 'Casa grande con dos habitaciones, dos baños, patio, cerca de las montañas', 'Santa Teresa 558', 58000, 4, 0, 0, NULL, NULL, 1, 19, '2023-10-20', NULL),
(25, 'Las Mansiones', 'Apto grande en altas montañas, con dos cuartos, 3 baños. Completamente amoblada', 'El Ávila', 100000, 5, 2, 4, '2023-10-19', '2023-12-28', 1, 19, '2023-10-20', NULL),
(26, 'Las Rocas', 'Casa grande', 'Las Rocas', 120000, 4, 0, 0, NULL, NULL, 1, 19, '2023-10-20', NULL),
(28, 'Mis niñas', 'Casa de 2 habitaciones, full amoblada', 'Las Rocas Del Tuy', 150000, 5, 0, 0, NULL, NULL, 1, 20, '2023-10-20', NULL),
(29, 'Las Gaviotas', 'Hermoso Departamento en el piso 5, en el centro de la capital. Próximo a plazas, parques y gimnasios. Completamente amoblado.', 'Av. Pueyrredón 5800', 50000, 2, 1, 5, NULL, NULL, 1, 23, '2023-11-06', NULL),
(30, 'Las Cabanas', 'Hermosa cabaña para pasar unos días de tranquilidad, muy cerca de las sierras.', 'Mendoza, El Rincón 890, cabaña 4.', 90000, 5, 1, 9, NULL, NULL, 1, 7, '2023-11-06', NULL),
(31, 'EL Amanecer', 'Hermosa habitación, en gran cabaña , al lado del lago con una hermosa vista .', 'Bariloche, Azcuénaga 896', 45000, 2, 1, 15, NULL, NULL, 1, 7, '2023-12-29', NULL),
(32, 'Las Rocas', 'Casa a orillas de playa', 'Santa Teresa', 90000, 2, 1, 5, NULL, '2023-11-29', 1, 7, '2023-11-13', NULL),
(33, 'Casa Rio Chico', 'Hermosa casa, con todos los servicios y comodidades', 'La Estrella, calle 56, Rio Chico', 65000, 5, 1, 15, '2023-11-15', '2024-11-15', 1, 7, '2023-11-15', NULL),
(34, 'Los Recuerdos', 'Casa grande , hermosa , con todos los servicios', 'El Jarillo, calle los recuerdos, Los Teques', 80000, 5, 1, 15, NULL, NULL, 1, 24, '2023-11-15', NULL),
(35, 'El Paraiso', 'Casa Grande, todos los servicios disponibles, full amoblada. Para pasar unos días relax', 'Las Aves del Paraíso 1556', 100000, 5, 1, 15, '2023-11-17', '2023-12-14', 1, 24, '2023-11-17', NULL),
(36, 'Casas Palmares', 'Hermosas casas para el descanso', 'Palmares 950', 90000, 5, 1, 20, NULL, NULL, 1, 24, '2023-11-17', NULL),
(37, 'Casona Mis Amores', 'Casona para pasar unos días en armonía, amoblada', 'Ruta 88 , las aves 456', 60000, 5, 1, 5, NULL, NULL, 1, 24, '2023-11-17', NULL),
(38, 'Emirates', 'Apartamentos muy cómodos, amoblados', 'Las Brisas De Oriente 489', 30000, 2, 1, 9, '2023-11-17', '2023-12-30', 1, 24, '2023-11-17', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reseña`
--

CREATE TABLE `reseña` (
  `id` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL,
  `respuesta` varchar(500) DEFAULT NULL,
  `fecha_comentario` date DEFAULT current_timestamp(),
  `fecha_respuesta` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reseña`
--

INSERT INTO `reseña` (`id`, `id_publicacion`, `id_usuario`, `comentario`, `calificacion`, `respuesta`, `fecha_comentario`, `fecha_respuesta`) VALUES
(1, 24, 20, 'Casa muy linda, pero falto limpieza', 2, 'Gracias por informar, y ofrezco disculpa .', '2023-10-01', '2023-11-05'),
(5, 20, 20, 'La tv tiene una muy buena calidad de imagen. El sonido está bien también, aunque yo lo tengo conectado a un home theater (mediante la entrada de auriculares), tiene entrada de audio digital, por lo que se puede tener muy buen sonido, si uno quiere complementar con algo, pero por sí solo está bien. Tiene chromecast integrado, al ser tv android tiene un sistema amigable para navegar, y se le puede descargar un montón de aplicaciones sin problemas. Recomiendo cada tanto reiniciarlo, ', 3, 'Estamos a la orden', '2023-10-22', '2023-10-26'),
(6, 25, 7, 'Hermoso departamento', 4, NULL, '2023-11-05', '2023-11-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`) VALUES
(5, 'Calefacción'),
(6, 'A/A'),
(7, 'Wifi'),
(8, 'Cable'),
(9, 'TV/HD'),
(10, 'Almohadas'),
(11, 'Ropa De Cama'),
(12, 'Tostadora'),
(13, 'Lavarropas'),
(14, 'Cocina'),
(15, 'Cámaras'),
(16, 'Estacionamiento'),
(17, 'Nevera'),
(18, 'Plancha'),
(19, 'Placar'),
(20, 'Cafetera'),
(21, 'Licuadora'),
(22, 'Caja Fuerte'),
(23, 'Sun'),
(24, 'Pileta'),
(25, 'Gimnasio'),
(26, 'Patio'),
(27, 'Balcón '),
(28, 'Secador de Pelo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_publicacion`
--

CREATE TABLE `servicio_publicacion` (
  `id_servicio` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio_publicacion`
--

INSERT INTO `servicio_publicacion` (`id_servicio`, `id_publicacion`) VALUES
(5, 20),
(5, 21),
(5, 22),
(5, 23),
(5, 24),
(5, 25),
(5, 29),
(5, 30),
(5, 31),
(5, 32),
(5, 33),
(5, 34),
(5, 35),
(5, 36),
(5, 38),
(6, 20),
(6, 24),
(6, 25),
(6, 26),
(6, 29),
(6, 30),
(6, 31),
(6, 33),
(6, 34),
(6, 35),
(6, 36),
(6, 38),
(7, 20),
(7, 23),
(7, 24),
(7, 25),
(7, 29),
(7, 30),
(7, 31),
(7, 33),
(7, 34),
(7, 35),
(7, 36),
(7, 38),
(8, 20),
(8, 29),
(8, 30),
(8, 31),
(8, 33),
(8, 35),
(8, 36),
(8, 38),
(9, 20),
(9, 22),
(9, 29),
(9, 30),
(9, 31),
(9, 33),
(9, 35),
(9, 36),
(9, 38),
(10, 20),
(10, 29),
(10, 35),
(11, 21),
(11, 34),
(11, 35),
(12, 35),
(13, 33),
(13, 35),
(14, 35),
(16, 35),
(17, 21),
(17, 24),
(17, 35),
(18, 35),
(19, 22),
(20, 35),
(24, 33),
(24, 36),
(25, 36),
(26, 33),
(26, 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `nombre` char(50) NOT NULL,
  `apellido` char(50) NOT NULL,
  `dni` int(11) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `cod_pais` varchar(10) NOT NULL,
  `telefono` int(11) NOT NULL,
  `sexo` char(1) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `es_verificado` tinyint(1) NOT NULL,
  `documento_verificado` varchar(100) DEFAULT NULL,
  `es_administrador` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `email`, `clave`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `cod_pais`, `telefono`, `sexo`, `bio`, `foto`, `es_verificado`, `documento_verificado`, `es_administrador`) VALUES
(7, 'guillermo54@gmail.com', '$2y$10$vIr5d.RzBPGGi0ZH9C5sXekVvfYONyL1QY8m948IcsHF/QW/C7kWi', 'Guillermo', 'Zapata', 95874236, '1969-02-03', '+55', 1126939789, 'm', '', '652c6055f3448.webp', 1, '65466d9622adb.webp', 0),
(19, 'ortizclaudis@gmail.com', '$2y$10$6sxU.u2Iz1/U4FncoLYRI.OuPwLAnyxC3kjC4GLqx8KEveTKxrUFW', 'Claudis', 'Ortiz', 454676846, '0000-00-00', '+58', 1126939176, 'f', '', NULL, 0, NULL, 0),
(20, 'maria789@gmail.com', '$2y$10$Ke2f2gLOUIu/ovvw6VG6H.9iIOxHyDpaSpJrrwcqoPkKOgRHuLQOm', 'Maria', 'DB', 95802977, '1984-01-02', '+54', 1236547890, 'f', '', NULL, 0, '65566b90a9dfc.webp', 0),
(21, 'miguel58@gmail.com', '$2y$10$8etoXzGHY41rgDxhmpT3UulS4p9Cfq2F4xL5pTEqhosI.gQ8KVGVK', 'Miguel', 'Perez', 56478963, '1996-12-30', '+52', 1145698741, 'm', '', NULL, 0, NULL, 1),
(22, 'jose@gmail.com', '$2y$10$W0SLT0m.kSE6R0.u2WVf3.4U/8qeN393tKZaMsnwyVpaPZk5LaNdW', 'Jose', 'Ortiz', 5649178, '1959-03-30', '+58', 2147483647, '', NULL, NULL, 0, NULL, 0),
(23, 'martinezc@gmail.com', '$2y$10$QaAURWb/Ocj2KnU4MRCD6eJanKuOOxnuoGbr283e3R3v97216V2NS', 'Carlos', 'Martinez', 45789632, '1982-01-06', '+51', 1478523698, 'm', '', NULL, 0, '65566a1564afb.jpg', 0),
(24, 'karlak@hotmail.com', '$2y$10$tL.cO3qdMW2bgvE2.0tir.AoPCjW9dnXJI1Sl8q.tYlBz4TbrgP.G', 'Karla', 'Ramirez', 15900601, '1998-12-28', '+593', 2147483647, 'f', NULL, NULL, 1, '65550506ac2f2.webp', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verificacion_cuenta`
--

CREATE TABLE `verificacion_cuenta` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `documento` varchar(200) NOT NULL,
  `comentario` varchar(300) DEFAULT NULL,
  `fecha_solicitud` date NOT NULL DEFAULT current_timestamp(),
  `estado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_revision` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `verificacion_cuenta`
--

INSERT INTO `verificacion_cuenta` (`id`, `id_usuario`, `documento`, `comentario`, `fecha_solicitud`, `estado`, `fecha_revision`, `fecha_vencimiento`) VALUES
(1, 7, '652b566cd0a36.jpg', '', '2022-10-17', 1, '2022-10-16', '2023-10-21'),
(6, 7, '6546511d4003b.jpg', '', '2023-11-04', 2, '2023-11-04', '2024-11-13'),
(33, 7, '654663907721f.webp', '', '2023-11-04', 2, '2023-11-04', '2024-11-04'),
(41, 7, '65466d9622adb.webp', '', '2023-11-04', 1, '2023-11-04', '2024-11-04'),
(42, 20, '65495c4d5154f.webp', '', '2023-11-06', 2, '2023-11-06', '2024-11-06'),
(43, 24, '65550506ac2f2.webp', '', '2023-11-15', 1, '2023-11-17', '2024-11-17'),
(44, 23, '65566a1564afb.jpg', '', '2023-11-16', 0, NULL, NULL),
(45, 20, '65566b90a9dfc.webp', '', '2023-11-16', 0, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `codigo_pais`
--
ALTER TABLE `codigo_pais`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `etiqueta`
--
ALTER TABLE `etiqueta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `etiqueta_publicacion`
--
ALTER TABLE `etiqueta_publicacion`
  ADD UNIQUE KEY `id_etiqueta` (`id_etiqueta`,`id_publicacion`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `interes`
--
ALTER TABLE `interes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `interes_user`
--
ALTER TABLE `interes_user`
  ADD PRIMARY KEY (`id_usuario`,`id_interes`),
  ADD UNIQUE KEY `id_usuario_2` (`id_usuario`,`id_interes`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_etiqueta` (`id_interes`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicio_publicacion`
--
ALTER TABLE `servicio_publicacion`
  ADD PRIMARY KEY (`id_servicio`,`id_publicacion`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `verificacion_cuenta`
--
ALTER TABLE `verificacion_cuenta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `codigo_pais`
--
ALTER TABLE `codigo_pais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `etiqueta`
--
ALTER TABLE `etiqueta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `imagen`
--
ALTER TABLE `imagen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `interes`
--
ALTER TABLE `interes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `reseña`
--
ALTER TABLE `reseña`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `verificacion_cuenta`
--
ALTER TABLE `verificacion_cuenta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD CONSTRAINT `alquiler_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `alquiler_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `etiqueta_publicacion`
--
ALTER TABLE `etiqueta_publicacion`
  ADD CONSTRAINT `etiqueta_publicacion_ibfk_1` FOREIGN KEY (`id_etiqueta`) REFERENCES `etiqueta` (`id`),
  ADD CONSTRAINT `etiqueta_publicacion_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD CONSTRAINT `imagen_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `interes_user`
--
ALTER TABLE `interes_user`
  ADD CONSTRAINT `interes_user_ibfk_1` FOREIGN KEY (`id_interes`) REFERENCES `interes` (`id`),
  ADD CONSTRAINT `interes_user_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reseña`
--
ALTER TABLE `reseña`
  ADD CONSTRAINT `reseña_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `reseña_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `servicio_publicacion`
--
ALTER TABLE `servicio_publicacion`
  ADD CONSTRAINT `servicio_publicacion_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id`),
  ADD CONSTRAINT `servicio_publicacion_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `verificacion_cuenta`
--
ALTER TABLE `verificacion_cuenta`
  ADD CONSTRAINT `verificacion_cuenta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
