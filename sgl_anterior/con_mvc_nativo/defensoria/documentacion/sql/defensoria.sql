CREATE TABLE `def_auditoria` (
  `id_log` int unsigned NOT NULL AUTO_INCREMENT,
  `fecha_hora_log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operacion` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tabla` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `netusername` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `netpcname` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observaciones_log` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Movimientos en el Sistema de Defensoria';

--
-- Estructura de tabla para la tabla `def_provincias`
--
CREATE TABLE `def_provincias` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `def_provincias`
--
INSERT INTO `def_provincias` (`id`, `nombre`, `habilitado`, `editando`) VALUES
(1, 'Buenos Aires', '1', '0'),
(2, 'Catamarca', '1', '0'),
(3, 'Córdoba', '1', '0'),
(4, 'Corrientes', '1', '0'),
(5, 'Entre Ríos', '1', '0'),
(6, 'Jujuy', '1', '0'),
(7, 'Mendoza', '1', '0'),
(8, 'La Rioja', '1', '0'),
(9, 'Salta', '1', '0'),
(10, 'San Juan', '1', '0'),
(11, 'San Luis', '1', '0'),
(12, 'Santa Fe', '1', '0'),
(13, 'Santiago del Estero', '1', '0'),
(14, 'Tucumán', '1', '0'),
(16, 'Chaco', '1', '0'),
(17, 'Chubut', '1', '0'),
(18, 'Formosa', '1', '0'),
(19, 'Misiones', '1', '0'),
(20, 'Neuquén', '1', '0'),
(21, 'La Pampa', '1', '0'),
(22, 'Río Negro', '1', '0'),
(23, 'Santa Cruz', '1', '0'),
(24, 'Tierra del Fuego', '1', '0');

--
-- Estructura de tabla para la tabla `def_modelos_escrito`
--
CREATE TABLE `def_modelos_escrito` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `def_tipos_proceso`
--
CREATE TABLE `def_tipos_proceso` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `def_presentadores`
--

CREATE TABLE `def_presentadores` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `provincia_id` int(10) UNSIGNED DEFAULT NULL,
  `apellido` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localidad` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_calle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_numero` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `direccion_piso` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_departamento` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_fijo_cod_area` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_fijo_numero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movil_cod_area` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movil_numero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_alta` date DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Filtros para la tabla `def_presentadores`
--
ALTER TABLE `def_presentadores`
  ADD CONSTRAINT `fk_reside_en_provincia` FOREIGN KEY (`provincia_id`) REFERENCES `def_provincias` (`id`);

--
-- Estructura de tabla para la tabla `def_expedientes`
--
CREATE TABLE `def_expedientes` (
  `numero` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `presentante_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_proceso_id` int(11) UNSIGNED DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` enum('en trámite','archivado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Filtros para la tabla `def_expedientes`
--
ALTER TABLE `def_expedientes`
  ADD CONSTRAINT `fk_iniciado_por_presentado` FOREIGN KEY (`presentante_id`) REFERENCES `def_presentadores` (`id`);

--
-- Estructura de tabla para la tabla `def_movimientos`
--
CREATE TABLE `hcd`.`def_movimientos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero` INT UNSIGNED NOT NULL,
  `documento` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`));

--
-- Estructura de tabla para la tabla `def_notas`
--
CREATE TABLE `hcd`.`def_notas` (
  `numero` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME NOT NULL,
  `documento` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`numero`));