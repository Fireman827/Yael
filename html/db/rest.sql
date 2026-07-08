-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-12-2024 a las 10:01:12
-- Versión del servidor: 10.11.6-MariaDB-0+deb12u1
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rest`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `cleanTables` ()  NO SQL BEGIN
TRUNCATE TABLE producto;
TRUNCATE TABLE productoCategoria;
TRUNCATE TABLE productoCategoriaEspecifica;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `crearMenu` ()  NO SQL BEGIN
INSERT INTO productoCategoria (idSucursalProductoCategoria,nombreProductoCategoria)
(SELECT 1, categoria FROM plantilla GROUP BY categoria);
UPDATE plantilla, productoCategoria SET plantilla.categoria=productoCategoria.idProductoCategoria WHERE plantilla.categoria=productoCategoria.nombreProductoCategoria; 
INSERT INTO producto (idSucursalProducto,idProductoCategoria,nombreProducto,precioVentaProducto,impresoraProducto)
(SELECT 1, categoria,nombre,precio,2 FROM plantilla);
INSERT INTO productoCategoriaEspecifica (idProducto,idProductoCategoria)
(SELECT idProducto,idProductoCategoria FROM producto);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `limpiarBase` ()   BEGIN
TRUNCATE `corteCaja`;
TRUNCATE `corteHistorial`;
TRUNCATE `corteHistorialDocumento`;
TRUNCATE `corteTurno`;
TRUNCATE `factura`;
TRUNCATE `insumo`;
TRUNCATE `insumoCategoria`;
TRUNCATE `insumoCosto`;
TRUNCATE `insumoLote`;
TRUNCATE `insumoMovimiento`;
TRUNCATE `insumoMovimientoDetalle`;
TRUNCATE `insumoPresentacion`;
TRUNCATE `insumoStock`;
TRUNCATE `marca`;
TRUNCATE `modificador`;
TRUNCATE `modificadorTipo`;
TRUNCATE `pagosDetalle`;
TRUNCATE `pedido`;
TRUNCATE `pedidoDetalle`;
TRUNCATE `pedidoSubDetalle`;
TRUNCATE `presentacion`;
TRUNCATE `productoInsumo`;
TRUNCATE `productoModificador`;
TRUNCATE `productoModificadorDetalle`;
TRUNCATE `usuarioPermisos`;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activoFijo`
--

CREATE TABLE `activoFijo` (
  `idActivoFijo` int(11) NOT NULL,
  `idSucursalActivoFijo` int(11) NOT NULL,
  `nombreActivoFijo` varchar(250) NOT NULL,
  `marcaActivoFijo` varchar(200) NOT NULL,
  `modeloActivoFijo` varchar(200) NOT NULL,
  `precioActivoFijo` decimal(10,4) NOT NULL,
  `estadoActivoFijo` enum('Activo','Inactivo','Borrado','Depreciado') NOT NULL DEFAULT 'Activo',
  `vidaActivoFijo` decimal(10,2) NOT NULL,
  `depreciacionActivoFijo` int(11) NOT NULL,
  `categoriaActivoFijo` int(11) NOT NULL,
  `aleatorioActivoFijo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `baseDatos`
--

CREATE TABLE `baseDatos` (
  `idBaseDatos` int(11) NOT NULL,
  `idSucursalBaseDatos` int(11) NOT NULL,
  `accionBaseDatos` enum('Descarga','Carga') NOT NULL,
  `fechaBaseDatos` date NOT NULL,
  `horaBaseDatos` time NOT NULL,
  `idUsuarioBaseDatos` int(11) NOT NULL,
  `rutaBaseDatos` varchar(500) NOT NULL,
  `fechaHoraRestauracionBaseDatos` date NOT NULL,
  `aleatorioBaseDatos` varchar(50) NOT NULL,
  `estadoBaseDatos` enum('Activo','Inactivo','Borrado','Restaurado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `idCaja` int(11) NOT NULL,
  `idSucursalCaja` int(11) NOT NULL,
  `nombreCaja` varchar(50) NOT NULL,
  `impresoraCaja` int(11) NOT NULL,
  `aleatorioCaja` varchar(100) NOT NULL,
  `estadoCaja` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aperturaCaja` tinyint(1) NOT NULL,
  `turnoCaja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`idCaja`, `idSucursalCaja`, `nombreCaja`, `impresoraCaja`, `aleatorioCaja`, `estadoCaja`, `aperturaCaja`, `turnoCaja`) VALUES
(1, 1, 'CAJA 1', 1, '66eaf051069bb', 'Activo', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajaDocumento`
--

CREATE TABLE `cajaDocumento` (
  `idCajaDocumento` int(11) NOT NULL,
  `idCajaCajaDocumento` int(11) NOT NULL,
  `idDocumentoCajaDocumento` int(11) NOT NULL,
  `inicioCajaDocumento` int(11) NOT NULL,
  `finalCajaDocumento` int(11) NOT NULL,
  `actualCajaDocumento` int(11) NOT NULL,
  `serieCajaDocumento` varchar(500) NOT NULL,
  `fechaAutorizacionCajaDocumento` date NOT NULL,
  `fechaResolucionCajaDocumento` date NOT NULL,
  `numeroResolucionCajaDocumento` varchar(50) NOT NULL,
  `aleatorioCajaDocumento` varchar(50) NOT NULL,
  `estadoCajaDocumento` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `cajaDocumento`
--

INSERT INTO `cajaDocumento` (`idCajaDocumento`, `idCajaCajaDocumento`, `idDocumentoCajaDocumento`, `inicioCajaDocumento`, `finalCajaDocumento`, `actualCajaDocumento`, `serieCajaDocumento`, `fechaAutorizacionCajaDocumento`, `fechaResolucionCajaDocumento`, `numeroResolucionCajaDocumento`, `aleatorioCajaDocumento`, `estadoCajaDocumento`) VALUES
(1, 1, 1, 1, 1000000, 1, '0', '2000-01-01', '2000-01-01', '0', '670c96881b909', 'Activo'),
(2, 1, 2, 1, 1000000, 1, '0', '2000-01-01', '2000-01-01', '0', '668c36898f29c', 'Activo'),
(3, 1, 3, 1, 1000000, 1, '0', '2000-01-01', '2000-01-01', '0', '668c36898f4f5', 'Activo'),
(25, 1, 7, 1, 1000000, 1, '0', '2000-01-01', '2000-01-01', '0', '668c36898e73e', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajaDocumentoHistorial`
--

CREATE TABLE `cajaDocumentoHistorial` (
  `idCajaDocumentoHistorial` int(11) NOT NULL,
  `idCajaDocumentoCajaDocumentoHistorial` int(11) NOT NULL,
  `idDocumentoCajaDocumentoHistorial` int(11) NOT NULL,
  `inicioCajaDocumentoHistorial` int(11) NOT NULL,
  `finalCajaDocumentoHistorial` int(11) NOT NULL,
  `fechaAutorizaCajaDocumentoHistorial` date NOT NULL,
  `fechaResolucionCajaDocumentoHistorial` date NOT NULL,
  `numeroResolucionCajaDocumentoHistorial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajaMovimiento`
--

CREATE TABLE `cajaMovimiento` (
  `idCajaMovimiento` int(11) NOT NULL,
  `idSucursalCajaMovimiento` int(11) NOT NULL,
  `idCaja` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idTurno` int(11) NOT NULL,
  `idTurnoCajaMovimiento` int(11) NOT NULL,
  `idUsuarioCajaMovimiento` int(11) NOT NULL,
  `tipoCajaMovimiento` enum('Entrada','Salida') NOT NULL,
  `recibeCajaMovimiento` varchar(500) NOT NULL,
  `entregaCajaMovimiento` varchar(500) NOT NULL,
  `conceptoCajaMovimiento` varchar(500) NOT NULL,
  `montoCajaMovimiento` decimal(10,2) NOT NULL,
  `fechaRegistroCajaMovimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoCajaMovimiento` enum('Activo','Inactivo','Borrado','') NOT NULL,
  `aleatorioCajaMovimiento` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `idCargo` int(11) NOT NULL,
  `idSucursalCargo` int(4) NOT NULL,
  `nombreCargo` varchar(200) NOT NULL,
  `descripcionCargo` text NOT NULL,
  `funcionesCargo` text NOT NULL,
  `aleatorioCargo` varchar(50) NOT NULL,
  `estadoCargo` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriaActivoFijo`
--

CREATE TABLE `categoriaActivoFijo` (
  `idCategoria` int(11) NOT NULL,
  `nombreCategoria` varchar(200) NOT NULL,
  `estadoCategoria` enum('Activo','Inactivo','Borrado') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idCliente` int(11) NOT NULL,
  `idSucursalCliente` int(11) NOT NULL,
  `nombreCliente` varchar(100) NOT NULL,
  `nombreComercialCliente` varchar(500) NOT NULL,
  `direccionCliente` varchar(250) NOT NULL,
  `documentoFacturacionCliente` enum('FAC','CCF') NOT NULL,
  `departamentoCliente` varchar(5) NOT NULL,
  `municipioCliente` varchar(5) NOT NULL,
  `telefonoCliente` varchar(20) NOT NULL,
  `referenciaCliente` varchar(250) NOT NULL,
  `facturarConCliente` enum('NIT','DUI') NOT NULL,
  `duiCliente` varchar(50) NOT NULL,
  `nitCliente` varchar(50) NOT NULL,
  `nrcCliente` varchar(50) NOT NULL,
  `giroCliente` varchar(50) NOT NULL,
  `retieneIvaCliente` tinyint(1) NOT NULL,
  `retieneRentaCliente` tinyint(1) NOT NULL,
  `emailCliente` varchar(100) NOT NULL,
  `idCategoriaCliente` int(4) NOT NULL,
  `avanzadoCliente` tinyint(1) NOT NULL,
  `aleatorioCliente` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `estadoCliente` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idCliente`, `idSucursalCliente`, `nombreCliente`, `nombreComercialCliente`, `direccionCliente`, `documentoFacturacionCliente`, `departamentoCliente`, `municipioCliente`, `telefonoCliente`, `referenciaCliente`, `facturarConCliente`, `duiCliente`, `nitCliente`, `nrcCliente`, `giroCliente`, `retieneIvaCliente`, `retieneRentaCliente`, `emailCliente`, `idCategoriaCliente`, `avanzadoCliente`, `aleatorioCliente`, `estadoCliente`) VALUES
(1, 1, 'CLIENTES VARIOS', '', 'no definida', 'FAC', '06', '23', '0000-0000', '', 'NIT', '00000000-0', '0000-000000-000-0', '00000-0', '', 0, 0, 'clientesvarios@gmail.com', 2, 1, '66feb02dd7dc0', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clienteCategoria`
--

CREATE TABLE `clienteCategoria` (
  `idClienteCategoria` int(11) NOT NULL,
  `idSucursalClienteCategoria` int(11) NOT NULL,
  `nombreClienteCategoria` varchar(50) NOT NULL,
  `descripcionClienteCategoria` varchar(250) NOT NULL,
  `aleatorioClienteCategoria` varchar(100) NOT NULL,
  `estadoClienteCategoria` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `clienteCategoria`
--

INSERT INTO `clienteCategoria` (`idClienteCategoria`, `idSucursalClienteCategoria`, `nombreClienteCategoria`, `descripcionClienteCategoria`, `aleatorioClienteCategoria`, `estadoClienteCategoria`) VALUES
(1, 1, 'FRECUENTE', 'CATEGORÍA PARA CLIENTES FRECUENTES', '6168bf9e52b37', 'Activo'),
(2, 1, 'REGULAR', 'CATEGORÍA PARA CLIENTES REGULARES', '6168bf14b0fc0', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `idConfiguracion` int(11) NOT NULL,
  `idSucursalConfiguracion` int(5) NOT NULL,
  `parametroConfiguracion` varchar(100) NOT NULL,
  `valorConfiguracion` varchar(250) NOT NULL,
  `comentarioConfiguracion` text NOT NULL,
  `aleatorioConfiguracion` varchar(50) NOT NULL,
  `estadoConfiguracion` enum('Activo','Inactivo','Borrado','Sistema') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`idConfiguracion`, `idSucursalConfiguracion`, `parametroConfiguracion`, `valorConfiguracion`, `comentarioConfiguracion`, `aleatorioConfiguracion`, `estadoConfiguracion`) VALUES
(1, 1, 'colorPlantilla', 'navbar-dark', 'Oscuro:navbar-dark\nClaro: navbar-white navbar-light', '', 'Sistema'),
(2, 1, 'modoPlantilla', 'dark-mode', 'Oscuro: dark-mode\r\nClaro: Dejar vacio', '', 'Sistema'),
(3, 1, 'colorComponentes', 'primary', 'default\r\nprimary\r\nsuccess\r\ninfo\r\nwarning\r\ndanger', '', 'Sistema'),
(4, 1, 'tarifaParqueo', '1', 'Tarifa en dolares para los cobros de parqueo', '', 'Sistema'),
(5, 1, 'nombreEmpresa', '', '', '66f05e5cb8528', 'Activo'),
(6, 1, 'direccionEmpresa', '', '', '66f05e458fa67', 'Activo'),
(7, 1, 'telefonoEmpresa', '', 'Teléfono de la empresa', '638f82461cda2', 'Activo'),
(8, 1, 'logoEmpresa', 'vendors/core/img/logo.png', 'Logo de la Empresa', '638f9c0000cc7', 'Sistema'),
(9, 1, 'nombrePatrono', '', 'Nombre del patrono', '637d1d7e9ae73', 'Sistema'),
(10, 1, 'domicilioPatrono', '', 'Domicilio del Patrono', '637d1d8675c2e', 'Sistema'),
(11, 1, 'residenciaPatrono', '', 'Residencia del Patrono', '637d1d8ee6018', 'Sistema'),
(12, 1, 'nacionalidadPatrono', 'Salvadoreña', 'Nacionalidad del Patrono', '', 'Sistema'),
(13, 1, 'fechaNacimientoPatrono', 'YYYY-MM-DD', 'Fecha de Nacimiento del Patrono', '637d1da10d827', 'Sistema'),
(14, 1, 'sexoPatrono', 'Femenino', 'Sexo del Patrono', '62e011245fe77', 'Sistema'),
(15, 1, 'estadoCivilPatrono', 'Casado', 'Estado Civil del Patrono', '637d1dacdd35e', 'Sistema'),
(16, 1, 'profesionOficioPatrono', 'Master en Administración de Empresas', 'Profesion u Oficio del Patrono', '', 'Sistema'),
(17, 1, 'duiPatrono', '00000000-0', 'DUI del Patrono', '62e011649e2af', 'Sistema'),
(18, 1, 'expedicionDuiPatrono', 'San Miguel, San Miguel', 'Fecha de Expedición del DUI del Patrono', '', 'Sistema'),
(19, 1, 'cobroPropina', 'No', 'No', '66d8a84ed1478', 'Activo'),
(20, 1, 'valorPropina', '10', 'Porcentaje 0 a 100', '63e70f850e94e', 'Activo'),
(21, 1, 'porcentaje_bebida_senorita', '50', 'Porcentaje 0 a 100', '', 'Sistema'),
(22, 1, 'impresionEnRed', 'Si', 'Si/No', '638f8c74cfd5d', 'Sistema'),
(23, 1, 'ServicioSenorita', 'No', 'Si/No', '', 'Sistema'),
(24, 1, 'Domicilio', 'Si', 'Si/No', '', 'Sistema'),
(25, 1, 'Llevar', 'Si', 'Si/No', '', 'Sistema'),
(26, 1, 'Recoger', 'No', 'Si/No', '', 'Sistema'),
(27, 1, 'Mesas', 'No', 'Si/No', '', 'Sistema'),
(28, 1, 'Cover', 'No', 'Si/No', '', 'Sistema'),
(29, 1, 'Cuentas', 'Si', 'Si/No', '', 'Sistema'),
(30, 1, 'PrecioEmpleado', 'No', 'Si/No', '', 'Sistema'),
(31, 1, 'PrecioEspecial', 'No', 'Si/No', '', 'Sistema'),
(32, 1, 'Sucursales', 'No', 'Si/No', '', 'Sistema'),
(33, 1, 'Movimientos', 'Si', 'Si/No', '', 'Sistema'),
(34, 1, 'DescargaInsumoVenta', 'Si', 'Si/No', '', 'Sistema'),
(35, 1, 'ImpresionRed', 'Si', 'Si/No', '', 'Sistema'),
(36, 1, 'DescargaInsumoHeredado', 'No', 'Si/No', '', 'Sistema'),
(37, 1, 'nitPatrono', '', 'NIT del patrono', '62e011af9dd78', 'Sistema'),
(38, 1, 'nrcPatrono', '', 'NRC del patrono', '62e011c599937', 'Sistema'),
(39, 1, 'rutaRol', 'Dashboard:inicio,Corte:CorteAdmin,Touch:touch', '', '', 'Sistema'),
(40, 1, 'cargaCorrelativo', '3', 'Correlativos de cargas de inventario.', '', 'Sistema'),
(41, 1, 'compraCorrelativo', '0', 'Correlativos de compras.', '', 'Sistema'),
(42, 1, 'inventarioCorrelativo', '2', 'Correlativos de inventario inicial.', '', 'Sistema'),
(43, 1, 'descargaCorrelativo', '1', 'Correlativos de descarga de inventario.', '', 'Sistema'),
(44, 1, 'stockApertura', 'No', 'Si/No', '', 'Sistema'),
(45, 1, 'iva', '13', '', '', 'Sistema'),
(46, 1, 'ImpresionComanda', 'Si', 'Si/No', '', 'Sistema'),
(47, 1, 'nitEmpresa', '', 'NIT de la Empresa', '66eaee1dbe6ee', 'Activo'),
(48, 1, 'nrcEmpresa', '', 'NRC de la Empresa', '66eaedf2cfb07', 'Activo'),
(49, 1, 'giroEmpresa', '', '', '66f05e1b6ac16', 'Activo'),
(50, 1, 'cuentaMeseroSeparada', 'No', 'Si/No', '', 'Sistema'),
(51, 1, 'impuestoAdvaloremAlcohol', '8', 'del 1% al 100%', '632c80fd24967', 'Sistema'),
(52, 1, 'impuestoAdvaloremTabaco', '39', 'del 1% al 100%', '', 'Sistema'),
(54, 1, 'horaEntradaEmpleado', '10:30:00', 'Hora formato 24 horas (HH:MM:SS)', '63c8c7b6bfbe2', 'Sistema'),
(55, 1, 'horasTrabajoEmpleado', '08:00:00', 'numero de horas de trabajo por día', '', 'Sistema'),
(56, 1, 'tipoCobroFe', 'Fijo', 'El tipo cobro puede ser \'Fijo\' o \'Por Documento\'', '', 'Sistema'),
(57, 1, 'clientSecretWompi', '38874b83-4d01-4ed4-98c5-03cac0938f10', 'la llave secreta de cliente en wompi', '', 'Sistema'),
(58, 1, 'clientIdWompi', '22607046-55a5-4236-8797-ba6dc3f33eb9', 'El id de cliente en wompi', '', 'Sistema'),
(59, 1, 'nombreEnvioComprobante', 'Nelson Orlando Benavides Cuadra', 'El nombre al que le llegara el comprobante de pago mensual en Digitals Minds Systems', '', 'Sistema'),
(60, 1, 'correoEnvioComprobante', 'nobenavides17@gmail.com', 'El correo al que le llegara el comprobante de pago mensual en Digitals Minds Systems', '', 'Sistema'),
(61, 1, 'valorTransaccion', '0.05', 'El monto que pagara el cliente por cada transacción de facturación electrónica', '', 'Sistema'),
(62, 1, 'alojamientoMensual', '56.5', 'El monto que pagara el cliente por el alojamiento mensual', '', 'Sistema'),
(65, 1, 'entornoFE', 'prueba', 'prueba\nproduccion', '65a991c83f4fa', 'Sistema'),
(67, 1, 'imagen_anulacion', 'vendors/core/img/anulado.png', '', '65a991c83f4fa', 'Sistema'),
(68, 1, 'facturacion_electronica', 'No', 'Si/No', '65a991c83f4fa', 'Sistema'),
(76, 1, 'height-panel', '800px', '0px', '65a991c83f4fa', 'Sistema'),
(77, 1, 'height-panel-interno', '650px', 'panel - 150px', '65a991c83f4fa', 'Sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrato`
--

CREATE TABLE `contrato` (
  `idContrato` int(11) NOT NULL,
  `idSucursalContrato` int(4) NOT NULL,
  `idEmpleadoContrato` int(4) NOT NULL,
  `idContratoTipoContrato` int(3) NOT NULL,
  `duiContrato` varchar(10) NOT NULL,
  `nitContrato` varchar(20) NOT NULL,
  `desdeContrato` date NOT NULL,
  `hastaContrato` date NOT NULL,
  `horarioContrato` varchar(400) NOT NULL,
  `contenidoContrato` varchar(200) NOT NULL,
  `aleatorioContrato` varchar(50) NOT NULL,
  `estadoContrato` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratoClausula`
--

CREATE TABLE `contratoClausula` (
  `idContratoClausula` int(11) NOT NULL,
  `idSucursalContratoClausula` int(11) NOT NULL,
  `nombreContratoClausula` varchar(200) NOT NULL,
  `descripcionContratoClausula` text NOT NULL,
  `anexosContratoClausula` int(11) NOT NULL,
  `aleatorioContratoClausula` varchar(50) NOT NULL,
  `estadoContratoClausula` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratoTipo`
--

CREATE TABLE `contratoTipo` (
  `idContratoTipo` int(11) NOT NULL,
  `idSucursalContratoTipo` int(11) NOT NULL,
  `nombreContratoTipo` varchar(50) NOT NULL,
  `aleatorioContratoTipo` varchar(50) NOT NULL,
  `estadoContratoTipo` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratoTipoClausula`
--

CREATE TABLE `contratoTipoClausula` (
  `idContratoTipoClausula` int(6) NOT NULL,
  `idContratoTipoContratoTipoClausula` int(5) NOT NULL,
  `idContratoClausulaContratoTipoClausula` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corteCaja`
--

CREATE TABLE `corteCaja` (
  `idCorteCaja` int(11) NOT NULL,
  `idCaja` int(11) NOT NULL,
  `idSucursalCorte` int(11) NOT NULL,
  `fechaCorte` date NOT NULL,
  `horaCorte` time NOT NULL,
  `fechaHoraCorteCierre` datetime NOT NULL,
  `idUsuarioCorte` int(11) NOT NULL,
  `montoApertura` decimal(8,4) NOT NULL,
  `totalCorte` decimal(10,2) NOT NULL,
  `diferenciaCorte` decimal(10,2) NOT NULL,
  `montoCorte` decimal(8,4) NOT NULL,
  `idTurnoVigente` int(11) NOT NULL,
  `revisionInsumo` tinyint(1) NOT NULL,
  `correlativoPedidoCorteCaja` int(11) NOT NULL DEFAULT 1,
  `estadoCorte` enum('Vigente','Finalizado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corteHistorial`
--

CREATE TABLE `corteHistorial` (
  `idCorteHistorial` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idUsuarioCorteHistorial` int(11) NOT NULL,
  `idSucursalCorteHistorial` int(11) NOT NULL,
  `idTurnoCorteHistorial` int(11) NOT NULL,
  `fechaCorteHistorial` datetime NOT NULL DEFAULT current_timestamp(),
  `idCajaCorteHistorial` int(11) NOT NULL,
  `ticketCorteHistorial` int(11) NOT NULL,
  `ticketInicioCorteHistorial` int(11) NOT NULL,
  `ticketFinalCorteHistorial` int(11) NOT NULL,
  `ticketTotalCorteHistorial` decimal(10,2) NOT NULL,
  `facturaCorteHistorial` int(11) NOT NULL,
  `facturaInicioCorteHistorial` int(11) NOT NULL,
  `facturaFinalCorteHistorial` int(11) NOT NULL,
  `facturaTotalCorteHistorial` decimal(10,2) NOT NULL,
  `creditoFiscalCorteHistorial` int(11) NOT NULL,
  `creditoFiscalInicioCorteHistorial` int(11) NOT NULL,
  `creditoFiscalFinalCorteHistorial` int(11) NOT NULL,
  `diferenciaTurnoCorteHistorial` decimal(10,2) NOT NULL,
  `montoAperturaTurnoCorteHistorial` decimal(10,2) NOT NULL,
  `totalCorteHistorial` decimal(10,2) NOT NULL,
  `totalCorteEfectivo` decimal(10,2) NOT NULL,
  `totalCorteTarjeta` decimal(10,2) NOT NULL,
  `totalCorteBitcoin` decimal(10,2) NOT NULL,
  `totalCortePedidosYa` decimal(10,2) NOT NULL,
  `tipoCorteHistorial` enum('C','X','Z') NOT NULL,
  `estadoCorteHistorial` enum('Finalizado','Anulado','Borrado') NOT NULL DEFAULT 'Finalizado',
  `aleatorioCorteHistorial` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corteHistorialDocumento`
--

CREATE TABLE `corteHistorialDocumento` (
  `idCorteHistorialDocumento` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idTurno` int(11) NOT NULL,
  `idCorteHistorial` int(11) NOT NULL,
  `tipoCorteDocumento` varchar(200) NOT NULL,
  `inicioDocumento` varchar(100) NOT NULL,
  `finDocumento` varchar(100) NOT NULL,
  `totalNumeroDocumento` int(11) NOT NULL,
  `totalDocumento` decimal(10,2) NOT NULL,
  `totalDescuentoDocumento` decimal(10,4) NOT NULL,
  `tipoDocumentoNombre` varchar(100) NOT NULL,
  `estadoDocumento` enum('Activo','Anulado','Eliminado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corteRevisionInsumo`
--

CREATE TABLE `corteRevisionInsumo` (
  `idRevisionInsumo` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `idInsumoPresentacion` int(11) NOT NULL,
  `existenciaInicioInsumo` decimal(10,2) NOT NULL,
  `existenciaFinInsumo` decimal(10,2) NOT NULL,
  `unidadMinimaInsumo` double(10,2) NOT NULL,
  `unidadInsumoPresentacion` double(10,2) NOT NULL,
  `descripcionInsumoPresentacion` varchar(200) NOT NULL,
  `existenciaMinima` float(10,2) NOT NULL,
  `existenciaReal` decimal(10,2) NOT NULL,
  `diferenciaInsumo` decimal(10,2) NOT NULL,
  `aleatorioCorteRevisionInsumo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corteTurno`
--

CREATE TABLE `corteTurno` (
  `idTurno` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `corteTurno` int(11) NOT NULL,
  `montoTurnoCorteCaja` decimal(10,2) NOT NULL,
  `montoTurnoCierreCorteCaja` decimal(10,2) NOT NULL,
  `montoTurnoCierreCorteCajaDiferencia` decimal(10,2) NOT NULL,
  `totalTurnoCierreCorteCaja` decimal(10,2) NOT NULL,
  `idUsuarioCorteTurno` int(11) NOT NULL,
  `fechaCorteTurno` datetime NOT NULL DEFAULT current_timestamp(),
  `horaCorteTurno` time NOT NULL,
  `fechaHoraCierreCorteTurno` datetime NOT NULL,
  `estadoCorteTurno` enum('Vigente','Finalizado','Anulado','Borrado') NOT NULL DEFAULT 'Vigente',
  `aleatorioCorteTurno` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cover`
--

CREATE TABLE `cover` (
  `idCover` int(11) NOT NULL,
  `idProductoCover` int(11) NOT NULL,
  `montoCover` decimal(9,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentasBancarias`
--

CREATE TABLE `cuentasBancarias` (
  `idCuentaBancaria` varchar(50) NOT NULL,
  `nombreBanco` varchar(100) NOT NULL,
  `numeroCuenta` varchar(50) NOT NULL,
  `nombreDestinatario` varchar(300) NOT NULL,
  `correoElectronicoDestinatario` varchar(100) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions_pedidos`
--

CREATE TABLE `ci_sessions_pedidos` (
  `id`         varchar(128) NOT NULL,
  `ip_address` varchar(45)  NOT NULL,
  `timestamp`  int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data`       blob         NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_pedidos_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `cuentasBancarias`
--

INSERT INTO `cuentasBancarias` (`idCuentaBancaria`, `nombreBanco`, `numeroCuenta`, `nombreDestinatario`, `correoElectronicoDestinatario`, `estado`, `fechaRegistro`) VALUES
('1', 'Agricola', '3800984536', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58'),
('2', 'BAC (America Central)', '117426700', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58'),
('3', 'Cuscatlan', '423401000011974', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58'),
('4', 'Davivienda', '777541873657', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58'),
('5', 'Fedecredito', '360100537471', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58'),
('6', 'Promerica', '20000009022326', 'Nelson Orlando Benavides Cuadra', 'nobenavides17@gmail.com', 'Activo', '2024-03-05 08:36:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `damas`
--

CREATE TABLE `damas` (
  `idDama` int(11) NOT NULL,
  `nombreDama` varchar(500) NOT NULL,
  `apodoDama` varchar(500) NOT NULL,
  `procentajeDama` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `idDepartamento` int(3) NOT NULL,
  `nombreDepartamento` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Departamentos de El Salvador';

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`idDepartamento`, `nombreDepartamento`) VALUES
(1, 'Ahuachapán'),
(2, 'Santa Ana'),
(3, 'Sonsonate'),
(4, 'La Libertad'),
(5, 'Chalatenango'),
(6, 'San Salvador'),
(7, 'Cuscatlán'),
(8, 'La Paz'),
(9, 'Cabañas'),
(10, 'San Vicente'),
(11, 'Usulután'),
(12, 'Morazán'),
(13, 'San Miguel'),
(14, 'La Unión');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleCompra`
--

CREATE TABLE `detalleCompra` (
  `idCompraDetalle` int(11) NOT NULL,
  `idCompra` int(11) NOT NULL,
  `idInsumoDetalle` int(11) NOT NULL,
  `cantidadInsumoDetalle` decimal(8,4) NOT NULL,
  `costoInsumoDetalle` decimal(8,4) NOT NULL,
  `exentoDetalle` decimal(8,4) NOT NULL,
  `idPresentacionDetalle` int(11) NOT NULL,
  `idUsuarioDetalle` int(11) NOT NULL,
  `subtotalInsumoDetalle` decimal(8,4) NOT NULL,
  `fechaDetalle` date NOT NULL,
  `horaDetalle` time NOT NULL,
  `fechaRegistoDetalle` date NOT NULL,
  `estadoCompraDetalle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `idDocumento` int(3) NOT NULL,
  `nombreDocumento` varchar(30) DEFAULT NULL,
  `aliasDocumento` char(4) DEFAULT NULL,
  `tipoDocumento` enum('Compra','Venta','Nota') NOT NULL,
  `impresionPdf` int(11) NOT NULL,
  `aleatorioDocumento` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `documento`
--

INSERT INTO `documento` (`idDocumento`, `nombreDocumento`, `aliasDocumento`, `tipoDocumento`, `impresionPdf`, `aleatorioDocumento`) VALUES
(1, 'TIQUETE', 'TIK', 'Compra', 0, 'O5ddd24c14d3bd9.80403935'),
(2, 'FACTURA', 'FAC', 'Compra', 1, 'O5ddd24c14dc060.61591537'),
(3, 'COMPROBANTE FISCAL', 'CCF', 'Compra', 1, 'O5ddd24c14e4983.69295570'),
(4, 'DEVOLUCION', 'DEV', 'Compra', 0, 'O5ddd24c14ea3f9.05779248'),
(5, 'IMPORTACION', 'IMP', 'Venta', 0, 'O5ddd24c14f2971.49967050'),
(6, 'NOTA DE REMISION', 'REM', 'Venta', 0, 'O5ddd24c14fc534.96876077'),
(7, 'NOTA DE CREDITO', 'NDC', 'Nota', 0, 'O5ddd24c1503459.92130685'),
(8, 'NOTA DE DEBITO', 'NDD', 'Nota', 0, 'O5ddd24c15081a2.41094981'),
(9, 'NOTA DE RETENCION', 'NTR', 'Venta', 0, 'O5ddd24c150ca90.74201519'),
(10, 'RESERVA PRODUCTO', 'RES', 'Compra', 0, 'O5ddd24c15517d4.25886720'),
(24, 'COMPROBANTE DE COBRO', 'CDC', 'Compra', 0, 'O5ddd24c14d3bd9.80403936');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `idEmpleado` int(11) NOT NULL,
  `idSucursalEmpleado` int(3) NOT NULL,
  `nombreEmpleado` varchar(100) NOT NULL,
  `apellidoEmpleado` varchar(100) NOT NULL,
  `nitEmpleado` varchar(20) NOT NULL,
  `duiEmpleado` varchar(16) NOT NULL,
  `direccionEmpleado` varchar(250) NOT NULL,
  `telefono1Empleado` varchar(12) NOT NULL,
  `telefono2Empleado` varchar(12) NOT NULL,
  `emailEmpleado` varchar(100) NOT NULL,
  `sangreEmpleado` varchar(20) NOT NULL,
  `imagenEmpleado` varchar(150) NOT NULL,
  `fechaNacimientoEmpleado` date NOT NULL,
  `sexoEmpleado` varchar(100) NOT NULL,
  `estadoCivilEmpleado` varchar(100) NOT NULL,
  `profesionOficioEmpleado` varchar(200) NOT NULL,
  `expedicionDuiEmpleado` varchar(200) NOT NULL,
  `familiaresEmpleado` text NOT NULL,
  `residenciaEmpleado` varchar(200) NOT NULL,
  `nacionalidadEmpleado` varchar(100) NOT NULL,
  `idCargoEmpleado` int(4) NOT NULL,
  `salarioBaseEmpleado` decimal(10,2) NOT NULL,
  `formaPagoEmpleado` varchar(20) NOT NULL,
  `departamentoEmpleado` varchar(20) NOT NULL,
  `afiliadoAfpEmpleado` varchar(20) NOT NULL,
  `afpEmpleado` enum('SI','NO') NOT NULL,
  `isssEmpleado` enum('SI','NO') NOT NULL,
  `rentaEmpleado` enum('SI','NO') NOT NULL,
  `fechaContratacionEmpleado` date NOT NULL,
  `fechaCeseEmpleado` date NOT NULL,
  `modalidadEmpleado` varchar(20) NOT NULL,
  `documentoEmpleado` varchar(20) NOT NULL,
  `aleatorioEmpleado` varchar(50) NOT NULL,
  `estadoEmpleado` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleadoBono`
--

CREATE TABLE `empleadoBono` (
  `idEmpleadoBono` int(11) NOT NULL,
  `idSucursalEmpleadoBono` int(3) NOT NULL,
  `idEmpleadoEmpleadoBono` int(5) NOT NULL,
  `montoEmpleadoBono` decimal(10,2) NOT NULL,
  `descripcionEmpleadoBono` varchar(200) NOT NULL,
  `mesEmpleadoBono` int(2) NOT NULL,
  `anioEmpleadoBono` int(4) NOT NULL,
  `idPeriodoEmpleadoBono` int(5) NOT NULL,
  `fechaRegistroEmpleadoBono` date NOT NULL,
  `aplicadoEmpleadoBono` enum('SI','NO') NOT NULL,
  `aleatorioEmpleadoBono` varchar(50) NOT NULL,
  `estadoEmpleadoBono` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleadoDescuento`
--

CREATE TABLE `empleadoDescuento` (
  `idEmpleadoDescuento` int(11) NOT NULL,
  `idSucursalEmpleadoDescuento` int(3) NOT NULL,
  `idEmpleadoEmpleadoDescuento` int(11) NOT NULL,
  `tipoEmpleadoDescuento` enum('DESCUENTO','ANTICIPO') NOT NULL,
  `montoEmpleadoDescuento` decimal(10,2) NOT NULL,
  `descripcionEmpleadoDescuento` varchar(200) NOT NULL,
  `mesEmpleadoDescuento` int(2) NOT NULL,
  `anioEmpleadoDescuento` int(4) NOT NULL,
  `idPeriodoEmpleadoDescuento` int(5) NOT NULL,
  `fechaRegistroEmpleadoDescuento` date NOT NULL,
  `aplicadoEmpleadoDescuento` enum('SI','NO') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `aleatorioEmpleadoDescuento` varchar(50) NOT NULL,
  `estadoEmpleadoDescuento` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleadoDescuentoCuota`
--

CREATE TABLE `empleadoDescuentoCuota` (
  `idEmpleadoDescuentoCuota` int(11) NOT NULL,
  `idSucursalEmpleadoDescuentoCuota` int(3) NOT NULL,
  `idEmpleadoEmpleadoDescuentoCuota` int(5) NOT NULL,
  `idInstitucionEmpleadoDescuentoCuota` int(5) NOT NULL,
  `montoEmpleadoDescuentoCuota` decimal(10,2) NOT NULL,
  `numeroCuotasEmpleadoDescuentoCuota` int(11) NOT NULL,
  `fechaInicioEmpleadoDescuentoCuota` date NOT NULL,
  `idPeriodoEmpleadoDescuentoCuota` int(5) NOT NULL,
  `descripcionEmpleadoDescuentoCuota` text NOT NULL,
  `pagadasEmpleadoDescuentoCuota` int(5) NOT NULL,
  `restantesEmpleadoDescuentoCuota` int(11) NOT NULL,
  `aleatorioEmpleadoDescuentoCuota` varchar(50) NOT NULL,
  `estadoEmpleadoDescuentoCuota` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleadoDescuentoDetalle`
--

CREATE TABLE `empleadoDescuentoDetalle` (
  `idEmpleadoDescuentoDetalle` int(11) NOT NULL,
  `idEmpleadoEmpleadoDescuentoDetalle` int(6) NOT NULL,
  `idDescuentoEmpleadoDescuentoDetalle` int(5) NOT NULL,
  `montoEmpleadoDescuentoDetalle` decimal(10,2) NOT NULL,
  `mesEmpleadoDescuentoDetalle` int(2) NOT NULL,
  `anioEmpleadoDescuentoDetalle` int(4) NOT NULL,
  `idPeriodoEmpleadoDescuentoDetalle` int(5) NOT NULL,
  `aplicadoEmpleadoDescuentoDetalle` enum('SI','NO') NOT NULL,
  `aleatorioEmpleadoDescuentoDetalle` varchar(100) NOT NULL,
  `estadoEmpleadoDescuentoDetalle` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleadoInstitucionFinanciera`
--

CREATE TABLE `empleadoInstitucionFinanciera` (
  `idInstitucionFinanciera` int(5) NOT NULL,
  `idSucursalInstitucionFinanciera` int(3) NOT NULL,
  `nombreInstitucionFinanciera` varchar(500) NOT NULL,
  `descripcionInstitucionFinanciera` varchar(500) NOT NULL,
  `aleatorioInstitucionFinanciera` varchar(50) NOT NULL,
  `estadoInstitucionFinanciera` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `idFactura` int(11) NOT NULL,
  `idSucursalFactura` int(11) NOT NULL,
  `tipoFactura` enum('Producto','Servicio','Producto Especial') NOT NULL,
  `idReferenciaFactura` int(11) NOT NULL COMMENT 'id de la Cuenta a la que se relaciona.',
  `tipoCuentaFactura` enum('local','domicilio','llevar','recoger') NOT NULL,
  `serieFactura` varchar(100) NOT NULL DEFAULT '',
  `resolucionFactura` varchar(100) NOT NULL DEFAULT '',
  `porConsumoFactura` int(11) NOT NULL,
  `tipoPagoFactura` enum('Contado','Credito') NOT NULL,
  `idCliente` int(11) NOT NULL,
  `fechaFactura` date NOT NULL,
  `horaFactura` time NOT NULL,
  `tipoDocumentoFactura` varchar(100) NOT NULL,
  `numeroDocumentoFactura` varchar(100) NOT NULL,
  `sumasFactura` float(10,2) NOT NULL,
  `ivaFactura` float(10,2) NOT NULL,
  `retencionFactura` float(10,2) NOT NULL,
  `noSujetoFactura` float(10,2) NOT NULL,
  `exentoFactura` float(10,2) NOT NULL,
  `regaliaFactura` decimal(10,2) NOT NULL,
  `propinaFactura` decimal(10,2) NOT NULL,
  `descuentoFactura` decimal(10,2) NOT NULL,
  `descuentoDolarFactura` decimal(10,2) NOT NULL,
  `totalFactura` float(10,2) NOT NULL,
  `envioFactura` decimal(10,2) NOT NULL,
  `vueltoFactura` float(10,2) NOT NULL,
  `efectivoFactura` float(10,2) NOT NULL,
  `tarjetaFactura` decimal(10,2) NOT NULL,
  `bitcoinFactura` decimal(10,2) NOT NULL,
  `pedidosYaFactura` decimal(10,2) NOT NULL,
  `transferenciaFactura` decimal(10,2) NOT NULL,
  `decuentoFactura` float(10,2) NOT NULL,
  `porcentajeFactura` float(10,2) NOT NULL,
  `idAfectaFactura` varchar(50) NOT NULL COMMENT 'id de la factura afectada cuando se hace una devolucion',
  `idUsuario` int(11) NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idTurno` int(11) NOT NULL,
  `idCajaFactura` int(11) NOT NULL,
  `abonoFactura` float(10,2) NOT NULL,
  `saldoFactura` float(10,2) NOT NULL,
  `nombreFactura` varchar(500) NOT NULL,
  `direccionFactura` varchar(500) NOT NULL,
  `nitFactura` varchar(20) NOT NULL,
  `nrcFactura` varchar(20) NOT NULL,
  `departamentoFactura` varchar(100) NOT NULL,
  `municipioFactura` varchar(100) NOT NULL,
  `fechaRegistroFactura` datetime NOT NULL DEFAULT current_timestamp(),
  `justificacionFactura` varchar(500) NOT NULL,
  `estadoFactura` enum('Activo','Inactivo','Borrado','Cobrado','Anulado','Pendiente') NOT NULL,
  `aleatorioFactura` varchar(100) NOT NULL,
  `correlativoFactura` int(11) NOT NULL,
  `jsonDTE` text NOT NULL,
  `jsonFirmado` text NOT NULL,
  `numeroControl` varchar(50) NOT NULL,
  `codigoGeneracion` varchar(50) NOT NULL,
  `selloRecibido` varchar(50) NOT NULL,
  `fhProcesamiento` varchar(50) NOT NULL,
  `error` text NOT NULL,
  `jsonDTEAnulacion` text NOT NULL,
  `jsonFirmadoAnulacion` text NOT NULL,
  `codigoGeneracionAnulacion` varchar(50) NOT NULL,
  `selloRecibidoAnulacion` varchar(50) NOT NULL,
  `fhProcesamientoAnulacion` varchar(50) NOT NULL,
  `errorAnulacion` text NOT NULL,
  `codigoGeneracionContingencia` varchar(50) NOT NULL,
  `selloRecibidoContingencia` varchar(50) NOT NULL,
  `fhProcesamientoContingencia` varchar(50) NOT NULL,
  `errorContingencia` text NOT NULL,
  `motivoContingencia` varchar(50) NOT NULL,
  `tipoContingencia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturaDetalle`
--

CREATE TABLE `facturaDetalle` (
  `idFacturaDetalle` int(11) NOT NULL,
  `idFactura` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL COMMENT 'cuando es producto',
  `idServicio` int(11) NOT NULL COMMENT 'cuando es Servicio',
  `cantidadFacturaDetalle` float(10,2) NOT NULL,
  `precioUnitarioFacturaDetalle` float(10,2) NOT NULL,
  `costoUnitarioFacturaDetalle` float(10,2) NOT NULL,
  `subTotalFacturaDetalle` float(10,2) NOT NULL,
  `descuentoFacturaDetalle` float(10,2) NOT NULL,
  `comentarioFacturaDetalle` varchar(500) NOT NULL,
  `estadoFacturaDetalle` enum('Activo','Inactivo','Borrado','') NOT NULL,
  `aleatorioFacturaDetalle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturaModificacion`
--

CREATE TABLE `facturaModificacion` (
  `idFacturaModificacion` int(11) NOT NULL,
  `idFactura` int(11) NOT NULL,
  `tipoFacturaModificacion` enum('Anular','Eliminar') NOT NULL,
  `comentarioFacturaModificacion` varchar(500) NOT NULL,
  `idUsuarioFacturaModificacion` int(11) NOT NULL,
  `fechaFacturaMovimiento` date NOT NULL DEFAULT current_timestamp(),
  `horaFacturaMovimiento` time NOT NULL DEFAULT current_timestamp(),
  `estadoFacturaModificacion` enum('Activo','Inactivo','Borrado','') NOT NULL,
  `aleatorioFacturaModificacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_002_TipodeDocumento`
--

CREATE TABLE `FE_CAT_002_TipodeDocumento` (
  `codigo` varchar(5) NOT NULL,
  `valores` varchar(33) DEFAULT NULL,
  `version` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_002_TipodeDocumento`
--

INSERT INTO `FE_CAT_002_TipodeDocumento` (`codigo`, `valores`, `version`) VALUES
('01', 'Factura', '1'),
('03', 'Comprobante de crédito fiscal', '3'),
('04', 'Nota de remisión', '3'),
('05', 'Nota de crédito', '3'),
('06', 'Nota de débito', '3'),
('07', 'Comprobante de retención', '1'),
('08', 'Comprobante de liquidación', '1'),
('09', 'Documento contable de liquidación', '1'),
('11', 'Facturas de exportación', '1'),
('14', 'Factura de sujeto excluido', '1'),
('15', 'Comprobante de donación', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_003_ModelodeFacturacion`
--

CREATE TABLE `FE_CAT_003_ModelodeFacturacion` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(27) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_003_ModelodeFacturacion`
--

INSERT INTO `FE_CAT_003_ModelodeFacturacion` (`codigo`, `valores`) VALUES
(1, 'Modelo Facturación previo'),
(2, 'Modelo Facturación diferido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_004_TipodeTransmision`
--

CREATE TABLE `FE_CAT_004_TipodeTransmision` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(28) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_004_TipodeTransmision`
--

INSERT INTO `FE_CAT_004_TipodeTransmision` (`codigo`, `valores`) VALUES
(1, 'Transmisión normal'),
(2, 'Transmisión por contingencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_005_TipodeContingencia`
--

CREATE TABLE `FE_CAT_005_TipodeContingencia` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(103) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_005_TipodeContingencia`
--

INSERT INTO `FE_CAT_005_TipodeContingencia` (`codigo`, `valores`) VALUES
(1, 'No disponibilidad de sistema del MH'),
(2, 'No disponibilidad de sistema del emisor'),
(3, 'Falla en el suministro de servicio de Internet del Emisor'),
(4, 'Falla en el suministro de servicio de energía eléctrica del emisor que impida la transmisión de los DTE'),
(5, 'Otro (deberá digitar un máximo de 500 caracteres explicando el motivo)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_006_RetencionIVAMH`
--

CREATE TABLE `FE_CAT_006_RetencionIVAMH` (
  `codigo` varchar(2) DEFAULT NULL,
  `valores` varchar(38) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_006_RetencionIVAMH`
--

INSERT INTO `FE_CAT_006_RetencionIVAMH` (`codigo`, `valores`) VALUES
('22', 'Retención IVA 1%'),
('C4', 'Retención IVA 13%'),
('C9', 'Otras retenciones IVA casos especiales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_007_TipodeGendelDoc`
--

CREATE TABLE `FE_CAT_007_TipodeGendelDoc` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_007_TipodeGendelDoc`
--

INSERT INTO `FE_CAT_007_TipodeGendelDoc` (`codigo`, `valores`) VALUES
(1, 'Físico'),
(2, 'Electrónico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_009_Tipodeestablecimient`
--

CREATE TABLE `FE_CAT_009_Tipodeestablecimient` (
  `codigo` int(2) DEFAULT NULL,
  `valores` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_009_Tipodeestablecimient`
--

INSERT INTO `FE_CAT_009_Tipodeestablecimient` (`codigo`, `valores`) VALUES
(1, 'Sucursal / Agencia'),
(2, 'Casa matriz'),
(4, 'Bodega'),
(7, 'Predio y/o patio'),
(20, 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_010_CodtipoServicioMed`
--

CREATE TABLE `FE_CAT_010_CodtipoServicioMed` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_010_CodtipoServicioMed`
--

INSERT INTO `FE_CAT_010_CodtipoServicioMed` (`codigo`, `valores`) VALUES
(1, 'Cirugía'),
(2, 'Operación'),
(3, 'Tratamiento médico'),
(4, 'Cirugía instituto salvadoreño de Bienestar Magisterial'),
(5, 'Operación Instituto Salvadoreño de Bienestar Magisterial'),
(6, 'Tratamiento médico Instituto Salvadoreño de Bienestar Magisterial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_011_Tipodeitem`
--

CREATE TABLE `FE_CAT_011_Tipodeitem` (
  `codigo` int(1) DEFAULT NULL,
  `valores` varchar(81) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_011_Tipodeitem`
--

INSERT INTO `FE_CAT_011_Tipodeitem` (`codigo`, `valores`) VALUES
(1, 'Bienes'),
(2, 'Servicios'),
(3, 'Ambos (Bienes y Servicios, incluye los dos inherente a los Productos o servicios)'),
(4, 'Otros tributos por ítem');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_012_Departamento`
--

CREATE TABLE `FE_CAT_012_Departamento` (
  `codigo` varchar(5) NOT NULL,
  `valores` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_012_Departamento`
--

INSERT INTO `FE_CAT_012_Departamento` (`codigo`, `valores`) VALUES
('00', 'Otro'),
('01', 'Ahuachapán'),
('02', 'Santa Ana'),
('03', 'Sonsonate'),
('04', 'Chalatenango'),
('05', 'La Libertad'),
('06', 'San Salvador'),
('07', 'Cuscatlán'),
('08', 'La Paz'),
('09', 'Cabañas'),
('10', 'San Vicente'),
('11', 'Usulután'),
('12', 'San Miguel'),
('13', 'Morazán'),
('14', 'La Unión');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_013_Municipio`
--

CREATE TABLE `FE_CAT_013_Municipio` (
  `codigo` varchar(5) DEFAULT NULL,
  `valores` varchar(25) DEFAULT NULL,
  `departamento` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_013_Municipio`
--

INSERT INTO `FE_CAT_013_Municipio` (`codigo`, `valores`, `departamento`) VALUES
('00', 'OTROS', '00'),
('13', 'AHUACHAPAN NORTE', '01'),
('14', 'AHUACHAPAN CENTRO', '01'),
('15', 'AHUACHAPAN SUR', '01'),
('14', 'SANTA ANA NORTE', '02'),
('15', 'SANTA ANA CENTRO', '02'),
('16', 'SANTA ANA ESTE', '02'),
('17', 'SANTA ANA OESTE', '02'),
('17', 'SONSONATE NORTE', '03'),
('18', 'SONSONATE CENTRO', '03'),
('19', 'SONSONATE ESTE', '03'),
('20', 'SONSONATE OESTE', '03'),
('34', 'CHALATENANGO NORTE', '04'),
('35', 'CHALATENANGO CENTRO', '04'),
('36', 'CHALATENANGO SUR', '04'),
('23', 'LA LIBERTAD NORTE', '05'),
('24', 'LA LIBERTAD CENTRO', '05'),
('25', 'LA LIBERTAD OESTE', '05'),
('26', 'LA LIBERTAD ESTE', '05'),
('27', 'LA LIBERTAD COSTA', '05'),
('28', 'LA LIBERTAD SUR', '05'),
('20', 'SAN SALVADOR NORTE', '06'),
('21', 'SAN SALVADOR OESTE', '06'),
('22', 'SAN SALVADOR ESTE', '06'),
('23', 'SAN SALVADOR CENTRO', '06'),
('24', 'SAN SALVADOR SUR', '06'),
('17', 'CUSCATLAN NORTE', '07'),
('18', 'CUSCATLAN SUR', '07'),
('23', 'LA PAZ OESTE', '08'),
('24', 'LA PAZ CENTRO', '08'),
('25', 'LA PAZ ESTE', '08'),
('10', 'CABAÑA OESTE', '09'),
('11', 'CABAÑA ESTE', '09'),
('14', 'SAN VICENTE NORTE', '10'),
('15', 'SAN VICENTE SUR', '10'),
('24', 'USULUTAN NORTE', '11'),
('25', 'USULUTAN ESTE', '11'),
('26', 'USULUTAN OESTE', '11'),
('21', 'SAN MIGUEL NORTE', '12'),
('22', 'SAN MIGUEL CENTRO', '12'),
('23', 'SAN MIGUEL OESTE', '12'),
('27', 'MORAZAN NORTE', '13'),
('28', 'MORAZAN SUR', '13'),
('19', 'LA UNION NORTE', '14'),
('20', 'LA UNION SUR', '14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_014_UnidaddeMedida`
--

CREATE TABLE `FE_CAT_014_UnidaddeMedida` (
  `codigo` varchar(5) NOT NULL,
  `valores` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_014_UnidaddeMedida`
--

INSERT INTO `FE_CAT_014_UnidaddeMedida` (`codigo`, `valores`) VALUES
('01', 'Metro'),
('02', 'Yarda'),
('03', 'Vara'),
('04', 'Pie'),
('05', 'Pulgada'),
('06', 'Milímetro'),
('08', 'Milla cuadrada'),
('09', 'Kilómetro cuadrado'),
('10', 'Hectárea'),
('11', 'Manzana'),
('12', 'Acre'),
('13', 'Metro cuadrado'),
('14', 'Yarda cuadrada'),
('15', 'Vara cuadrada'),
('16', 'Pie cuadrado'),
('17', 'Pulgada cuadrada'),
('18', 'Metro cúbico'),
('19', 'Yarda cúbica'),
('20', 'Barril'),
('21', 'Pie cúbico'),
('22', 'Galón'),
('23', 'Litro'),
('24', 'Botella'),
('25', 'Pulgada cúbica'),
('26', 'Mililitro'),
('27', 'Onza fluida'),
('29', 'Tonelada métrica'),
('30', 'Tonelada'),
('31', 'Quintal métrico'),
('32', 'Quintal'),
('33', 'Arroba'),
('34', 'Kilogramo'),
('35', 'Libra troy'),
('36', 'Libra'),
('37', 'Onza troy'),
('38', 'Onza'),
('39', 'Gramo'),
('40', 'Miligramo'),
('42', 'Megawatt'),
('43', 'Kilowatt'),
('44', 'Watt'),
('45', 'Megavoltio-amperio'),
('46', 'Kilovoltio-amperio'),
('47', 'Voltio-amperio'),
('49', 'Gigawatt-hora'),
('50', 'Megawatt-hora'),
('51', 'Kilowatt-hora'),
('52', 'Watt-hora'),
('53', 'Kilovoltio'),
('54', 'Voltio'),
('55', 'Millar'),
('56', 'Medio millar'),
('57', 'Ciento'),
('58', 'Docena'),
('59', 'Unidad'),
('99', 'Otra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_015_Tributos`
--

CREATE TABLE `FE_CAT_015_Tributos` (
  `codigo` varchar(2) DEFAULT NULL,
  `valores` varchar(96) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_015_Tributos`
--

INSERT INTO `FE_CAT_015_Tributos` (`codigo`, `valores`) VALUES
('20', 'Impuesto al Valor Agregado 13%'),
('C3', 'Impuesto al Valor Agregado (exportaciones) 0%'),
('59', 'Turismo: por alojamiento (5%)'),
('71', 'Turismo: salida del país por vía aérea $7.00'),
('D1', 'FOVIAL ($0.20 Ctvs. por galón)'),
('C8', 'COTRANS ($0.10 Ctvs. por galón)'),
('D5', 'Otras tasas casos especiales'),
('D4', 'Otros impuestos casos especiales'),
('A8', 'Impuesto Especial al Combustible (0%, 0.5%, 1%)'),
('57', 'Impuesto industria de Cemento'),
('90', 'Impuesto especial a la primera matrícula'),
('D4', 'Otros impuestos casos especiales'),
('D5', 'Otras tasas casos especiales'),
('A6', 'Impuesto ad- valorem, armas de fuego, municiones explosivas y artículos similares'),
('C5', 'Impuesto ad- valorem por diferencial de precios de bebidas alcohólicas (8%)'),
('C6', 'Impuesto ad- valorem por diferencial de precios al tabaco cigarrillos (39%)'),
('C7', 'Impuesto ad- valorem por diferencial de precios al tabaco cigarros (100%)'),
('19', 'Fabricante de Bebidas Gaseosas,Isotónicas,Deportivas, Fortificantes, Energizante o Estimulante'),
('28', 'Importador de Bebidas Gaseosas,Isotónicas,Deportivas, Fortificantes, Energizante o Estimulante'),
('31', 'Detallistas o Expendedores de Bebidas Alcohólicas'),
('32', 'Fabricante de Cerveza'),
('33', 'Importador de Cerveza'),
('34', 'Fabricante de Productos de Tabaco'),
('35', 'Importador de Productos de Tabaco'),
('36', 'Fabricante de Armas de Fuego, Municiones y Artículos Similares'),
('37', 'Importador de Arma de Fuego, Munición y Artículos. Similares'),
('38', 'Fabricante de Explosivos'),
('39', 'Importador de Explosivos'),
('42', 'Fabricante de Productos Pirotécnicos'),
('43', 'Importador de Productos Pirotécnicos'),
('44', 'Productor de Tabaco'),
('50', 'Distribuidor de Bebidas Gaseosas,Isotónicas,Deportivas, Fortificantes, Energizante o Estimulante'),
('51', 'Bebidas Alcohólicas'),
('52', 'Cerveza'),
('53', 'Productos del Tabaco'),
('54', 'Bebidas Carbonatadas o Gaseosas Simples o Endulzadas'),
('55', 'Otros Específicos'),
('58', 'Alcohol'),
('77', 'Importador de Jugos, Néctares, Bebidas con Jugo y Refrescos'),
('78', 'Distribuidor de Jugos, Néctares, Bebidas con Jugo y Refrescos'),
('79', 'Sobre Llamadas Telefónicas Provenientes del Ext.'),
('85', 'Detallista de Jugos, Néctares, Bebidas con Jugo y Refrescos'),
('86', 'Fabricante de Preparaciones Concentradas o en Polvo para la Elaboración de Bebidas'),
('91', 'Fabricante de Jugos, Néctares, Bebidas con Jugo y Refrescos'),
('92', 'Importador de Preparaciones Concentradas o en Polvo para la Elaboración de Bebidas'),
('A1', 'Específicos y Ad-Valorem'),
('A5', 'Bebidas Gaseosas, Isotónicas, Deportivas, Fortificantes, Energizantes o Estimulantes'),
('A7', 'Alcohol Etílico'),
('A9', 'Sacos Sintéticos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_016_CondiciondelaOperacion`
--

CREATE TABLE `FE_CAT_016_CondiciondelaOperacion` (
  `codigo` int(1) NOT NULL,
  `valores` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_016_CondiciondelaOperacion`
--

INSERT INTO `FE_CAT_016_CondiciondelaOperacion` (`codigo`, `valores`) VALUES
(1, 'Contado'),
(2, 'A crédito'),
(3, 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_017_FormadePago`
--

CREATE TABLE `FE_CAT_017_FormadePago` (
  `codigo` varchar(5) NOT NULL,
  `valores` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_017_FormadePago`
--

INSERT INTO `FE_CAT_017_FormadePago` (`codigo`, `valores`) VALUES
('01', 'Billetes y monedas'),
('02', 'Tarjeta Débito'),
('03', 'Tarjeta Crédito'),
('04', 'Cheque'),
('05', 'Transferencia_ Depósito Bancario'),
('06', 'Vales o Cupones'),
('08', 'Dinero electrónico'),
('09', 'Monedero electrónico'),
('10', 'Certificado o tarjeta de regalo'),
('11', 'Bitcoin'),
('12', 'Otras Criptomonedas'),
('13', 'Cuentas por pagar del receptor'),
('14', 'Giro bancario'),
('99', 'Otros (se debe indicar el medio de pago)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_018_Plazo`
--

CREATE TABLE `FE_CAT_018_Plazo` (
  `codigo` int(2) NOT NULL,
  `valores` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_018_Plazo`
--

INSERT INTO `FE_CAT_018_Plazo` (`codigo`, `valores`) VALUES
(1, 'Días'),
(2, 'Meses'),
(3, 'Años');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_019_CodigodeActividadEco`
--

CREATE TABLE `FE_CAT_019_CodigodeActividadEco` (
  `codigo` varchar(5) NOT NULL,
  `valores` varchar(161) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_019_CodigodeActividadEco`
--

INSERT INTO `FE_CAT_019_CodigodeActividadEco` (`codigo`, `valores`) VALUES
('01111', 'Cultivo de cereales excepto arroz y para forrajes'),
('01112', 'Cultivo de legumbres'),
('01113', 'Cultivo de semillas oleaginosas'),
('01114', 'Cultivo de plantas para la preparación de semillas'),
('01119', 'Cultivo de otros cereales excepto arroz y forrajeros n.c.p.'),
('01120', 'Cultivo de arroz'),
('01131', 'Cultivo de raíces y tubérculos'),
('01132', 'Cultivo de brotes, bulbos, vegetales tubérculos y cultivos similares'),
('01133', 'Cultivo hortícola de fruto'),
('01134', 'Cultivo de hortalizas de hoja y otras hortalizas ncp'),
('01140', 'Cultivo de caña de azúcar'),
('01150', 'Cultivo de tabaco'),
('01161', 'Cultivo de algodón'),
('01162', 'Cultivo de fibras vegetales excepto algodón'),
('01191', 'Cultivo de plantas no perennes para la producción de semillas y flores'),
('01192', 'Cultivo de cereales y pastos para la alimentación animal'),
('01199', 'Producción de cultivos no estacionales ncp'),
('01220', 'Cultivo de frutas tropicales'),
('01230', 'Cultivo de cítricos'),
('01240', 'Cultivo de frutas de pepita y hueso'),
('01251', 'Cultivo de frutas ncp'),
('01252', 'Cultivo de otros frutos y nueces de árboles y arbustos'),
('01260', 'Cultivo de frutos oleaginosos'),
('01271', 'Cultivo de café'),
('01272', 'Cultivo de plantas para la elaboración de bebidas excepto café'),
('01281', 'Cultivo de especias y aromáticas'),
('01282', 'Cultivo de plantas para la obtención de productos medicinales y farmacéuticos'),
('01291', 'Cultivo de árboles de hule (caucho) para la obtención de látex'),
('01292', 'Cultivo de plantas para la obtención de productos químicos y colorantes'),
('01299', 'Producción de cultivos perennes ncp'),
('01300', 'Propagación de plantas'),
('01301', 'Cultivo de plantas y flores ornamentales'),
('01410', 'Cría y engorde de ganado bovino'),
('01420', 'Cría de caballos y otros equinos'),
('01440', 'Cría de ovejas y cabras'),
('01450', 'Cría de cerdos'),
('01460', 'Cría de aves de corral y producción de huevos'),
('01491', 'Cría de abejas apicultura para la obtención de miel y otros productos apícolas'),
('01492', 'Cría de conejos'),
('01493', 'Cría de iguanas y garrobos'),
('01494', 'Cría de mariposas y otros insectos'),
('01499', 'Cría y obtención de productos animales n.c.p.'),
('01500', 'Cultivo de productos agrícolas en combinación con la cría de animales'),
('01611', 'Servicios de maquinaria agrícola'),
('01612', 'Control de plagas'),
('01613', 'Servicios de riego'),
('01614', 'Servicios de contratación de mano de obra para la agricultura'),
('01619', 'Servicios agrícolas ncp'),
('01621', 'Actividades para mejorar la reproducción, el crecimiento y el rendimiento de los animales y sus productos'),
('01622', 'Servicios de mano de obra pecuaria'),
('01629', 'Servicios pecuarios ncp'),
('01631', 'Labores post cosecha de preparación de los productos agrícolas para su comercialización o para la industria'),
('01632', 'Servicio de beneficio de café'),
('01633', 'Servicio de beneficiado de plantas textiles (incluye el beneficiado cuando este es realizado en la misma explotación agropecuaria)'),
('01640', 'Tratamiento de semillas para la propagación'),
('01700', 'Caza ordinaria y mediante trampas, repoblación de animales de caza y servicios conexos'),
('02100', 'Silvicultura y otras actividades forestales'),
('02200', 'Extracción de madera'),
('02300', 'Recolección de productos diferentes a la madera'),
('02400', 'Servicios de apoyo a la silvicultura'),
('03110', 'Pesca marítima de altura y costera'),
('03120', 'Pesca de agua dulce'),
('03210', 'Acuicultura marítima'),
('03220', 'Acuicultura de agua dulce'),
('03300', 'Servicios de apoyo a la pesca y acuicultura'),
('05100', 'Extracción de hulla'),
('05200', 'Extracción y aglomeración de lignito'),
('06100', 'Extracción de petróleo crudo'),
('06200', 'Extracción de gas natural'),
('07100', 'Extracción de minerales de hierro'),
('07210', 'Extracción de minerales de uranio y torio'),
('07290', 'Extracción de minerales metalíferos no ferrosos'),
('08100', 'Extracción de piedra, arena y arcilla'),
('08910', 'Extracción de minerales para la fabricación de abonos y productos químicos'),
('08920', 'Extracción y aglomeración de turba'),
('08930', 'Extracción de sal'),
('08990', 'Explotación de otras minas y canteras ncp'),
('09100', 'Actividades de apoyo a la extracción de petróleo y gas natural'),
('09900', 'Actividades de apoyo a la explotación de minas y canteras'),
('10001', 'Empleados'),
('10002', 'Pencionado'),
('10003', 'Estudiante'),
('10004', 'Desempleado'),
('10005', 'Otros'),
('10006', 'Comerciante'),
('10101', 'Servicio de rastros y mataderos de bovinos y porcinos'),
('10102', 'Matanza y procesamiento de bovinos y porcinos'),
('10103', 'Matanza y procesamientos de aves de corral'),
('10104', 'Elaboración y conservación de embutidos y tripas naturales'),
('10105', 'Servicios de conservación y empaque de carnes'),
('10106', 'Elaboración y conservación de grasas y aceites animales'),
('10107', 'Servicios de molienda de carne'),
('10108', 'Elaboración de productos de carne ncp'),
('10201', 'Procesamiento y conservación de pescado, crustáceos y moluscos'),
('10209', 'Fabricación de productos de pescado ncp'),
('10301', 'Elaboración de jugos de frutas y hortalizasv'),
('10302', 'Elaboración y envase de jaleas, mermeladas y frutas deshidratadas'),
('10309', 'Elaboración de productos de frutas y hortalizas n.c.p.'),
('10401', 'Fabricación de aceites y grasas vegetales y animales comestibles'),
('10402', 'Fabricación de aceites y grasas vegetales y animales no comestibles'),
('10409', 'Servicio de maquilado de aceites'),
('10501', 'Fabricación de productos lácteos excepto sorbetes y quesos sustitutos'),
('10502', 'Fabricación de sorbetes y helados'),
('10503', 'Fabricación de quesos'),
('10611', 'Molienda de cereales'),
('10612', 'Elaboración de cereales para el desayuno y similares'),
('10613', 'Servicios de beneficiado de productos agrícolas ncp (excluye Beneficio de azúcar rama 1072 y beneficio de café rama 0163)'),
('10621', 'Fabricación de almidón'),
('10628', 'Servicio de molienda de maíz húmedo molino para nixtamal'),
('10711', 'Elaboración de tortillas'),
('10712', 'Fabricación de pan, galletas y barquillos'),
('10713', 'Fabricación de repostería'),
('10721', 'Ingenios azucareros'),
('10722', 'Molienda de caña de azúcar para la elaboración de dulces'),
('10723', 'Elaboración de jarabes de azúcar y otros similares'),
('10724', 'Maquilado de azúcar de caña'),
('10730', 'Fabricación de cacao, chocolates y productos de confitería'),
('10740', 'Elaboración de macarrones, fideos, y productos farináceos similares'),
('10750', 'Elaboración de comidas y platos preparados para la reventa en locales y/o para exportación'),
('10791', 'Elaboración de productos de café'),
('10792', 'Elaboración de especies, sazonadores y condimentos'),
('10793', 'Elaboración de sopas, cremas y consomé'),
('10794', 'Fabricación de bocadillos tostados y/o fritos'),
('10799', 'Elaboración de productos alimenticios ncp'),
('10800', 'Elaboración de alimentos preparados para animales'),
('11012', 'Fabricación de aguardiente y licores'),
('11020', 'Elaboración de vinos'),
('11030', 'Fabricación de cerveza'),
('11041', 'Fabricación de aguas gaseosas'),
('11042', 'Fabricación y envasado de agua'),
('11043', 'Elaboración de refrescos'),
('11048', 'Maquilado de aguas gaseosas'),
('11049', 'Elaboración de bebidas no alcohólicas'),
('12000', 'Elaboración de productos de tabaco'),
('13111', 'Preparación de fibras textiles'),
('13112', 'Fabricación de hilados'),
('13120', 'Fabricación de telas'),
('13130', 'Acabado de productos textiles'),
('13910', 'Fabricación de tejidos de punto y ganchillo'),
('13921', 'Fabricación de productos textiles para el hogar'),
('13922', 'Sacos, bolsas y otros artículos textiles'),
('13929', 'Fabricación de artículos confeccionados con materiales textiles, excepto prendas de vestir ncp'),
('13930', 'Fabricación de tapices y alfombras'),
('13941', 'Fabricación de cuerdas de henequén y otras fibras naturales (lazos, pitas)'),
('13942', 'Fabricación de redes de diversos materiales'),
('13948', 'Maquilado de productos trenzables de cualquier material (petates, sillas, etc.)'),
('13991', 'Fabricación de adornos, etiquetas y otros artículos para prendas de vestir'),
('13992', 'Servicio de bordados en artículos y prendas de tela'),
('13999', 'Fabricación de productos textiles ncp'),
('14101', 'Fabricación de ropa interior, para dormir y similares'),
('14102', 'Fabricación de ropa para niños'),
('14103', 'Fabricación de prendas de vestir para ambos sexos'),
('14104', 'Confección de prendas a medida'),
('14105', 'Fabricación de prendas de vestir para deportes'),
('14106', 'Elaboración de artesanías de uso personal confeccionadas especialmente de materiales textiles'),
('14108', 'Maquilado de prendas de vestir, accesorios y otros'),
('14109', 'Fabricación de prendas y accesorios de vestir n.c.p.'),
('14200', 'Fabricación de artículos de piel'),
('14301', 'Fabricación de calcetines, calcetas, medias (panty house) y otros similares'),
('14302', 'Fabricación de ropa interior de tejido de punto'),
('14309', 'Fabricación de prendas de vestir de tejido de punto ncp'),
('15110', 'Curtido y adobo de cueros; adobo y teñido de pieles'),
('15121', 'Fabricación de maletas, bolsos de mano y otros artículos de marroquinería'),
('15122', 'Fabricación de monturas, accesorios y vainas talabartería'),
('15123', 'Fabricación de artesanías principalmente de cuero natural y sintético'),
('15128', 'Maquilado de artículos de cuero natural, sintético y de otros materiales'),
('15201', 'Fabricación de calzado'),
('15202', 'Fabricación de partes y accesorios de calzado'),
('15208', 'Maquilado de partes y accesorios de calzado'),
('16100', 'Aserradero y acepilladura de madera'),
('16210', 'Fabricación de madera laminada, terciada, enchapada y contrachapada, paneles para la construcción'),
('16220', 'Fabricación de partes y piezas de carpintería para edificios y construcciones'),
('16230', 'Fabricación de envases y recipientes de madera'),
('16292', 'Fabricación de artesanías de madera, semillas, materiales trenzables'),
('16299', 'Fabricación de productos de madera, corcho, paja y materiales trenzables ncp'),
('17010', 'Fabricación de pasta de madera, papel y cartón'),
('17020', 'Fabricación de papel y cartón ondulado y envases de papel y cartón'),
('17091', 'Fabricación de artículos de papel y cartón de uso personal y doméstico'),
('17092', 'Fabricación de productos de papel ncp'),
('18110', 'Impresión'),
('18120', 'Servicios relacionados con la impresión'),
('18200', 'Reproducción de grabaciones'),
('19100', 'Fabricación de productos de hornos de coque'),
('19201', 'Fabricación de combustible'),
('19202', 'Fabricación de aceites y lubricantes'),
('20111', 'Fabricación de materias primas para la fabricación de colorantes'),
('20112', 'Fabricación de materiales curtientes'),
('20113', 'Fabricación de gases industriales'),
('20114', 'Fabricación de alcohol etílico'),
('20119', 'Fabricación de sustancias químicas básicas'),
('20120', 'Fabricación de abonos y fertilizantes'),
('20130', 'Fabricación de plástico y caucho en formas primarias'),
('20210', 'Fabricación de plaguicidas y otros productos químicos de uso agropecuario'),
('20220', 'Fabricación de pinturas, barnices y productos de revestimiento similares; tintas de imprenta y masillas'),
('20231', 'Fabricación de jabones, detergentes y similares para limpieza'),
('20232', 'Fabricación de perfumes, cosméticos y productos de higiene y cuidado personal, incluyendo tintes, champú, etc.'),
('20291', 'Fabricación de tintas y colores para escribir y pintar; fabricación de cintas para impresoras'),
('20292', 'Fabricación de productos pirotécnicos, explosivos y municiones'),
('20299', 'Fabricación de productos químicos n.c.p.'),
('20300', 'Fabricación de fibras artificiales'),
('21001', 'Manufactura de productos farmacéuticos, sustancias químicas y productos botánicos'),
('21008', 'Maquilado de medicamentos'),
('22110', 'Fabricación de cubiertas y cámaras; renovación y recauchutado de cubiertas'),
('22190', 'Fabricación de otros productos de caucho'),
('22201', 'Fabricación de envases plásticos'),
('22202', 'Fabricación de productos plásticos para uso personal o doméstico'),
('22208', 'Maquila de plásticos'),
('22209', 'Fabricación de productos plásticos n.c.p.'),
('23101', 'Fabricación de vidrio'),
('23102', 'Fabricación de recipientes y envases de vidrio'),
('23108', 'Servicio de maquilado'),
('23109', 'Fabricación de productos de vidrio ncp'),
('23910', 'Fabricación de productos refractarios'),
('23920', 'Fabricación de productos de arcilla para la construcción'),
('23931', 'Fabricación de productos de cerámica y porcelana no refractaria'),
('23932', 'Fabricación de productos de cerámica y porcelana ncp'),
('23940', 'Fabricación de cemento, cal y yeso'),
('23950', 'Fabricación de artículos de hormigón, cemento y yeso'),
('23960', 'Corte, tallado y acabado de la piedra'),
('23990', 'Fabricación de productos minerales no metálicos ncp'),
('24100', 'Industrias básicas de hierro y acero'),
('24200', 'Fabricación de productos primarios de metales preciosos y metales no ferrosos'),
('24310', 'Fundición de hierro y acero'),
('24320', 'Fundición de metales no ferrosos'),
('25111', 'Fabricación de productos metálicos para uso estructural'),
('25118', 'Servicio de maquila para la fabricación de estructuras metálicas'),
('25120', 'Fabricación de tanques, depósitos y recipientes de metal'),
('25130', 'Fabricación de generadores de vapor, excepto calderas de agua caliente para calefacción central'),
('25200', 'Fabricación de armas y municiones'),
('25910', 'Forjado, prensado, estampado y laminado de metales; pulvimetalurgia'),
('25920', 'Tratamiento y revestimiento de metales'),
('25930', 'Fabricación de artículos de cuchillería, herramientas de mano y artículos de ferretería'),
('25991', 'Fabricación de envases y artículos conexos de metal'),
('25992', 'Fabricación de artículos metálicos de uso personal y/o doméstico'),
('25999', 'Fabricación de productos elaborados de metal ncp'),
('26100', 'Fabricación de componentes electrónicos'),
('26200', 'Fabricación de computadoras y equipo conexo'),
('26300', 'Fabricación de equipo de comunicaciones'),
('26400', 'Fabricación de aparatos electrónicos de consumo para audio, video radio y televisión'),
('26510', 'Fabricación de instrumentos y aparatos para medir, verificar, ensayar, navegar y de control de procesos industriales'),
('26520', 'Fabricación de relojes y piezas de relojes'),
('26600', 'Fabricación de equipo médico de irradiación y equipo electrónico de uso médico y terapéutico'),
('26700', 'Fabricación de instrumentos de óptica y equipo fotográfico'),
('26800', 'Fabricación de medios magnéticos y ópticos'),
('27100', 'Fabricación de motores, generadores, transformadores eléctricos, aparatos de distribución y control de electricidad'),
('27200', 'Fabricación de pilas, baterías y acumuladores'),
('27310', 'Fabricación de cables de fibra óptica'),
('27320', 'Fabricación de otros hilos y cables eléctricos'),
('27330', 'Fabricación de dispositivos de cableados'),
('27400', 'Fabricación de equipo eléctrico de iluminación'),
('27500', 'Fabricación de aparatos de uso doméstico'),
('27900', 'Fabricación de otros tipos de equipo eléctrico'),
('28110', 'Fabricación de motores y turbinas, excepto motores para aeronaves, vehículos automotores y motocicletas'),
('28120', 'Fabricación de equipo hidráulico'),
('28130', 'Fabricación de otras bombas, compresores, grifos y válvulas'),
('28140', 'Fabricación de cojinetes, engranajes, trenes de engranajes y piezas de transmisión'),
('28150', 'Fabricación de hornos y quemadores'),
('28160', 'Fabricación de equipo de elevación y manipulación'),
('28170', 'Fabricación de maquinaria y equipo de oficina'),
('28180', 'Fabricación de herramientas manuales'),
('28190', 'Fabricación de otros tipos de maquinaria de uso general'),
('28210', 'Fabricación de maquinaria agropecuaria y forestal'),
('28220', 'Fabricación de máquinas para conformar metales y maquinaria herramienta'),
('28230', 'Fabricación de maquinaria metalúrgica'),
('28240', 'Fabricación de maquinaria para la explotación de minas y canteras y para obras de construcción'),
('28250', 'Fabricación de maquinaria para la elaboración de alimentos, bebidas y tabaco'),
('28260', 'Fabricación de maquinaria para la elaboración de productos textiles, prendas de vestir y cueros'),
('28291', 'Fabricación de máquinas para imprenta'),
('28299', 'Fabricación de maquinaria de uso especial ncp'),
('29100', 'Fabricación vehículos automotores'),
('29200', 'Fabricación de carrocerías para vehículos automotores; fabricación de remolques y semiremolques'),
('29300', 'Fabricación de partes, piezas y accesorios para vehículos automotores'),
('30110', 'Fabricación de buques'),
('30120', 'Construcción y reparación de embarcaciones de recreo'),
('30200', 'Fabricación de locomotoras y de material rodante'),
('30300', 'Fabricación de aeronaves y naves espaciales'),
('30400', 'Fabricación de vehículos militares de combate'),
('30910', 'Fabricación de motocicletas'),
('30920', 'Fabricación de bicicletas y sillones de ruedas para inválidos'),
('30990', 'Fabricación de equipo de transporte ncp'),
('31001', 'Fabricación de colchones y somier'),
('31002', 'Fabricación de muebles y otros productos de madera a medida'),
('31008', 'Servicios de maquilado de muebles'),
('31009', 'Fabricación de muebles ncp'),
('32110', 'Fabricación de joyas platerías y joyerías'),
('32120', 'Fabricación de joyas de imitación (fantasía) y artículos conexos'),
('32200', 'Fabricación de instrumentos musicales'),
('32301', 'Fabricación de artículos de deporte'),
('32308', 'Servicio de maquila de productos deportivos'),
('32401', 'Fabricación de juegos de mesa y de salón'),
('32402', 'Servicio de maquilado de juguetes y juegos'),
('32409', 'Fabricación de juegos y juguetes n.c.p.'),
('32500', 'Fabricación de instrumentos y materiales médicos y odontológicos'),
('32901', 'Fabricación de lápices, bolígrafos, sellos y artículos de librería en general'),
('32902', 'Fabricación de escobas, cepillos, pinceles y similares'),
('32903', 'Fabricación de artesanías de materiales diversos'),
('32904', 'Fabricación de artículos de uso personal y domésticos n.c.p.'),
('32905', 'Fabricación de accesorios para las confecciones y la marroquinería n.c.p.'),
('32908', 'Servicios de maquila ncp'),
('32909', 'Fabricación de productos manufacturados n.c.p.'),
('33110', 'Reparación y mantenimiento de productos elaborados de metal'),
('33120', 'Reparación y mantenimiento de maquinaria'),
('33130', 'Reparación y mantenimiento de equipo electrónico y óptico'),
('33140', 'Reparación y mantenimiento de equipo eléctrico'),
('33150', 'Reparación y mantenimiento de equipo de transporte, excepto vehículos automotores'),
('33190', 'Reparación y mantenimiento de equipos n.c.p.'),
('33200', 'Instalación de maquinaria y equipo industrial'),
('35101', 'Generación de energía eléctrica'),
('35102', 'Transmisión de energía eléctrica'),
('35103', 'Distribución de energía eléctrica'),
('35200', 'Fabricación de gas, distribución de combustibles gaseosos por tuberías'),
('35300', 'Suministro de vapor y agua caliente'),
('36000', 'Captación, tratamiento y suministro de agua'),
('37000', 'Evacuación de aguas residuales (alcantarillado)'),
('38110', 'Recolección y transporte de desechos sólidos proveniente de hogares y sector urbano'),
('38120', 'Recolección de desechos peligrosos'),
('38210', 'Tratamiento y eliminación de desechos inicuos'),
('38220', 'Tratamiento y eliminación de desechos peligrosos'),
('38301', 'Reciclaje de desperdicios y desechos textiles'),
('38302', 'Reciclaje de desperdicios y desechos de plástico y caucho'),
('38303', 'Reciclaje de desperdicios y desechos de vidrio'),
('38304', 'Reciclaje de desperdicios y desechos de papel y cartón'),
('38305', 'Reciclaje de desperdicios y desechos metálicos'),
('38309', 'Reciclaje de desperdicios y desechos no metálicos n.c.p.'),
('39000', 'Actividades de Saneamiento y otros Servicios de Gestión de Desechos'),
('41001', 'Construcción de edificios residenciales'),
('41002', 'Construcción de edificios no residenciales'),
('42100', 'Construcción de carreteras, calles y caminos'),
('42200', 'Construcción de proyectos de servicio público'),
('42900', 'Construcción de obras de ingeniería civil n.c.p.'),
('43110', 'Demolición'),
('43120', 'Preparación de terreno'),
('43210', 'Instalaciones eléctricas'),
('43220', 'Instalación de fontanería, calefacción y aire acondicionado'),
('43290', 'Otras instalaciones para obras de construcción'),
('43300', 'Terminación y acabado de edificios'),
('43900', 'Otras actividades especializadas de construcción'),
('43901', 'Fabricación de techos y materiales diversos'),
('45100', 'Venta de vehículos automotores'),
('45201', 'Reparación mecánica de vehículos automotores'),
('45202', 'Reparaciones eléctricas del automotor y recarga de baterías'),
('45203', 'Enderezado y pintura de vehículos automotores'),
('45204', 'Reparaciones de radiadores, escapes y silenciadores'),
('45205', 'Reparación y reconstrucción de vías, stop y otros artículos de fibra de vidrio'),
('45206', 'Reparación de llantas de vehículos automotores'),
('45207', 'Polarizado de vehículos (mediante la adhesión de papel especial a los vidrios)'),
('45208', 'Lavado y pasteado de vehículos (carwash)'),
('45209', 'Reparaciones de vehículos n.c.p.'),
('45211', 'Remolque de vehículos automotores'),
('45301', 'Venta de partes, piezas y accesorios nuevos para vehículos automotores'),
('45302', 'Venta de partes, piezas y accesorios usados para vehículos automotores'),
('45401', 'Venta de motocicletas'),
('45402', 'Venta de repuestos, piezas y accesorios de motocicletas'),
('45403', 'Mantenimiento y reparación de motocicletas'),
('46100', 'Venta al por mayor a cambio de retribución o por contrata'),
('46201', 'Venta al por mayor de materias primas agrícolas'),
('46202', 'Venta al por mayor de productos de la silvicultura'),
('46203', 'Venta al por mayor de productos pecuarios y de granja'),
('46211', 'Venta de productos para uso agropecuario'),
('46291', 'Venta al por mayor de granos básicos (cereales, leguminosas)'),
('46292', 'Venta al por mayor de semillas mejoradas para cultivo'),
('46293', 'Venta al por mayor de café oro y uva'),
('46294', 'Venta al por mayor de caña de azúcar'),
('46295', 'Venta al por mayor de flores, plantas y otros productos naturales'),
('46296', 'Venta al por mayor de productos agrícolas'),
('46297', 'Venta al por mayor de ganado bovino (vivo)'),
('46298', 'Venta al por mayor de animales porcinos, ovinos, caprino, canículas, apícolas, avícolas vivos'),
('46299', 'Venta de otras especies vivas del reino animal'),
('46301', 'Venta al por mayor de alimentos'),
('46302', 'Venta al por mayor de bebidas'),
('46303', 'Venta al por mayor de tabaco'),
('46371', 'Venta al por mayor de frutas, hortalizas (verduras), legumbres y tubérculos'),
('46372', 'Venta al por mayor de pollos, gallinas destazadas, pavos y otras aves'),
('46373', 'Venta al por mayor de carne bovina y porcina, productos de carne y embutidos'),
('46374', 'Venta al por mayor de huevos'),
('46375', 'Venta al por mayor de productos lácteos'),
('46376', 'Venta al por mayor de productos farináceos de panadería (pan dulce, cakes, repostería, etc.)'),
('46377', 'Venta al por mayor de pastas alimenticias, aceites y grasas comestibles vegetal y animal'),
('46378', 'Venta al por mayor de sal comestible'),
('46379', 'Venta al por mayor de azúcar'),
('46391', 'Venta al por mayor de abarrotes (vinos, licores, productos alimenticios envasados, etc.)'),
('46392', 'Venta al por mayor de aguas gaseosas'),
('46393', 'Venta al por mayor de agua purificada'),
('46394', 'Venta al por mayor de refrescos y otras bebidas, líquidas o en polvo'),
('46395', 'Venta al por mayor de cerveza y licores'),
('46396', 'Venta al por mayor de hielo'),
('46411', 'Venta al por mayor de hilados, tejidos y productos textiles de mercería'),
('46412', 'Venta al por mayor de artículos textiles excepto confecciones para el hogar'),
('46413', 'Venta al por mayor de confecciones textiles para el hogar'),
('46414', 'Venta al por mayor de prendas de vestir y accesorios de vestir'),
('46415', 'Venta al por mayor de ropa usada'),
('46416', 'Venta al por mayor de calzado'),
('46417', 'Venta al por mayor de artículos de marroquinería y talabartería'),
('46418', 'Venta al por mayor de artículos de peletería'),
('46419', 'Venta al por mayor de otros artículos textiles n.c.p.'),
('46471', 'Venta al por mayor de instrumentos musicales'),
('46472', 'Venta al por mayor de colchones, almohadas, cojines, etc.'),
('46473', 'Venta al por mayor de artículos de aluminio para el hogar y para otros usos'),
('46474', 'Venta al por mayor de depósitos y otros artículos plásticos para el hogar y otros usos, incluyendo los desechables de durapax y no desechables'),
('46475', 'Venta al por mayor de cámaras fotográficas, accesorios y materiales'),
('46482', 'Venta al por mayor de medicamentos, artículos y otros productos de uso veterinario'),
('46483', 'Venta al por mayor de productos y artículos de belleza y de uso personal'),
('46484', 'Venta de productos farmacéuticos y medicinales'),
('46491', 'Venta al por mayor de productos medicinales, cosméticos, perfumería y productos de limpieza'),
('46492', 'Venta al por mayor de relojes y artículos de joyería'),
('46493', 'Venta al por mayor de electrodomésticos y artículos del hogar excepto bazar; artículos de iluminación'),
('46494', 'Venta al por mayor de artículos de bazar y similares'),
('46495', 'Venta al por mayor de artículos de óptica'),
('46496', 'Venta al por mayor de revistas, periódicos, libros, artículos de librería y artículos de papel y cartón en general'),
('46497', 'Venta de artículos deportivos, juguetes y rodados'),
('46498', 'Venta al por mayor de productos usados para el hogar o el uso personal'),
('46499', 'Venta al por mayor de enseres domésticos y de uso personal n.c.p.'),
('46500', 'Venta al por mayor de bicicletas, partes, accesorios y otros'),
('46510', 'Venta al por mayor de computadoras, equipo periférico y programas informáticos'),
('46520', 'Venta al por mayor de equipos de comunicación'),
('46530', 'Venta al por mayor de maquinaria y equipo agropecuario, accesorios, partes y suministros'),
('46590', 'Venta de equipos e instrumentos de uso profesional y científico y aparatos de medida y control'),
('46591', 'Venta al por mayor de maquinaria equipo, accesorios y materiales para la industria de la madera y sus productos'),
('46592', 'Venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria gráfica y del papel, cartón y productos de papel y cartón'),
('46593', 'Venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria de productos químicos, plástico y caucho'),
('46594', 'Venta al por mayor de maquinaria, equipo, accesorios y materiales para la industria metálica y de sus productos'),
('46595', 'Venta al por mayor de equipamiento para uso médico, odontológico, veterinario y servicios conexos'),
('46596', 'Venta al por mayor de maquinaria, equipo, accesorios y partes para la industria de la alimentación'),
('46597', 'Venta al por mayor de maquinaria, equipo, accesorios y partes para la industria textil, confecciones y cuero'),
('46598', 'Venta al por mayor de maquinaria, equipo y accesorios para la construcción y explotación de minas y canteras'),
('46599', 'Venta al por mayor de otro tipo de maquinaria y equipo con sus accesorios y partes'),
('46610', 'Venta al por mayor de otros combustibles sólidos, líquidos, gaseosos y de productos conexos'),
('46612', 'Venta al por mayor de combustibles para automotores, aviones, barcos, maquinaria y otros'),
('46613', 'Venta al por mayor de lubricantes, grasas y otros aceites para automotores, maquinaria industrial, etc.'),
('46614', 'Venta al por mayor de gas propano'),
('46615', 'Venta al por mayor de leña y carbón'),
('46620', 'Venta al por mayor de metales y minerales metalíferos'),
('46631', 'Venta al por mayor de puertas, ventanas, vitrinas y similares'),
('46632', 'Venta al por mayor de artículos de ferretería y pinturerías'),
('46633', 'Vidrierías'),
('46634', 'Venta al por mayor de maderas'),
('46639', 'Venta al por mayor de materiales para la construcción n.c.p.'),
('46691', 'Venta al por mayor de sal industrial sin yodar'),
('46692', 'Venta al por mayor de productos intermedios y desechos de origen textil'),
('46693', 'Venta al por mayor de productos intermedios y desechos de origen metálico'),
('46694', 'Venta al por mayor de productos intermedios y desechos de papel y cartón'),
('46695', 'Venta al por mayor fertilizantes, abonos, agroquímicos y productos similares'),
('46696', 'Venta al por mayor de productos intermedios y desechos de origen plástico'),
('46697', 'Venta al por mayor de tintas para imprenta, productos curtientes y materias y productos colorantes'),
('46698', 'Venta de productos intermedios y desechos de origen químico y de caucho'),
('46699', 'Venta al por mayor de productos intermedios y desechos ncp'),
('46701', 'Venta de algodón en oro'),
('46900', 'Venta al por mayor de otros productos'),
('46901', 'Venta al por mayor de cohetes y otros productos pirotécnicos'),
('46902', 'Venta al por mayor de artículos diversos para consumo humano'),
('46903', 'Venta al por mayor de armas de fuego, municiones y accesorios'),
('46904', 'Venta al por mayor de toldos y tiendas de campaña de cualquier material'),
('46905', 'Venta al por mayor de exhibidores publicitarios y rótulos'),
('46906', 'Venta al por mayor de artículos promocionales diversos'),
('47111', 'Venta en supermercados'),
('47112', 'Venta en tiendas de artículos de primera necesidad'),
('47119', 'Almacenes (venta de diversos artículos)'),
('47190', 'Venta al por menor de otros productos en comercios no especializados'),
('47199', 'Venta de establecimientos no especializados con surtido compuesto principalmente de alimentos, bebidas y tabaco'),
('47211', 'Venta al por menor de frutas y hortalizas'),
('47212', 'Venta al por menor de carnes, embutidos y productos de granja'),
('47213', 'Venta al por menor de pescado y mariscos'),
('47214', 'Venta al por menor de productos lácteos'),
('47215', 'Venta al por menor de productos de panadería, repostería y galletas'),
('47216', 'Venta al por menor de huevos'),
('47217', 'Venta al por menor de carnes y productos cárnicos'),
('47218', 'Venta al por menor de granos básicos y otros'),
('47219', 'Venta al por menor de alimentos n.c.p.'),
('47221', 'Venta al por menor de hielo'),
('47223', 'Venta de bebidas no alcohólicas, para su consumo fuera del establecimiento'),
('47224', 'Venta de bebidas alcohólicas, para su consumo fuera del establecimiento'),
('47225', 'Venta de bebidas alcohólicas para su consumo dentro del establecimiento'),
('47230', 'Venta al por menor de tabaco'),
('47300', 'Venta de combustibles, lubricantes y otros (gasolineras)'),
('47411', 'Venta al por menor de computadoras y equipo periférico'),
('47412', 'Venta de equipo y accesorios de telecomunicación'),
('47420', 'Venta al por menor de equipo de audio y video'),
('47510', 'Venta al por menor de hilados, tejidos y productos textiles de mercería; confecciones para el hogar y textiles n.c.p.'),
('47521', 'Venta al por menor de productos de madera'),
('47522', 'Venta al por menor de artículos de ferretería'),
('47523', 'Venta al por menor de productos de pinturerías'),
('47524', 'Venta al por menor en vidrierías'),
('47529', 'Venta al por menor de materiales de construcción y artículos conexos'),
('47530', 'Venta al por menor de tapices, alfombras y revestimientos de paredes y pisos en comercios especializados'),
('47591', 'Venta al por menor de muebles'),
('47592', 'Venta al por menor de artículos de bazar'),
('47593', 'Venta al por menor de aparatos electrodomésticos, repuestos y accesorios'),
('47594', 'Venta al por menor de artículos eléctricos y de iluminación'),
('47598', 'Venta al por menor de instrumentos musicales'),
('47610', 'Venta al por menor de libros, periódicos y artículos de papelería en comercios especializados'),
('47620', 'Venta al por menor de discos láser, cassettes, cintas de video y otros'),
('47630', 'Venta al por menor de productos y equipos de deporte'),
('47631', 'Venta al por menor de bicicletas, accesorios y repuestos'),
('47640', 'Venta al por menor de juegos y juguetes en comercios especializados'),
('47711', 'Venta al por menor de prendas de vestir y accesorios de vestir'),
('47712', 'Venta al por menor de calzado'),
('47713', 'Venta al por menor de artículos de peletería, marroquinería y talabartería'),
('47721', 'Venta al por menor de medicamentos farmacéuticos y otros materiales y artículos de uso médico, odontológico y veterinario'),
('47722', 'Venta al por menor de productos cosméticos y de tocador'),
('47731', 'Venta al por menor de productos de joyería, bisutería, óptica, relojería'),
('47732', 'Venta al por menor de plantas, semillas, animales y artículos conexos'),
('47733', 'Venta al por menor de combustibles de uso doméstico (gas propano y gas licuado)'),
('47734', 'Venta al por menor de artesanías, artículos cerámicos y recuerdos en general'),
('47735', 'Venta al por menor de ataúdes, lápidas y cruces, trofeos, artículos religiosos en general'),
('47736', 'Venta al por menor de armas de fuego, municiones y accesorios'),
('47737', 'Venta al por menor de artículos de cohetería y pirotécnicos'),
('47738', 'Venta al por menor de artículos desechables de uso personal y doméstico (servilletas, papel higiénico, pañales, toallas sanitarias, etc.)'),
('47739', 'Venta al por menor de otros productos n.c.p.'),
('47741', 'Venta al por menor de artículos usados'),
('47742', 'Venta al por menor de textiles y confecciones usados'),
('47743', 'Venta al por menor de libros, revistas, papel y cartón usados'),
('47749', 'Venta al por menor de productos usados n.c.p.'),
('47811', 'Venta al por menor de frutas, verduras y hortalizas'),
('47814', 'Venta al por menor de productos lácteos'),
('47815', 'Venta al por menor de productos de panadería, galletas y similares'),
('47816', 'Venta al por menor de bebidas'),
('47818', 'Venta al por menor en tiendas de mercado y puestos'),
('47821', 'Venta al por menor de hilados, tejidos y productos textiles de mercería en puestos de mercados y ferias'),
('47822', 'Venta al por menor de artículos textiles excepto confecciones para el hogar en puestos de mercados y ferias'),
('47823', 'Venta al por menor de confecciones textiles para el hogar en puestos de mercados y ferias'),
('47824', 'Venta al por menor de prendas de vestir, accesorios de vestir y similares en puestos de mercados y ferias'),
('47825', 'Venta al por menor de ropa usada'),
('47826', 'Venta al por menor de calzado, artículos de marroquinería y talabartería en puestos de mercados y ferias'),
('47827', 'Venta al por menor de artículos de marroquinería y talabartería en puestos de mercados y ferias'),
('47829', 'Venta al por menor de artículos textiles ncp en puestos de mercados y ferias'),
('47891', 'Venta al por menor de animales, flores y productos conexos en puestos de feria y mercados'),
('47892', 'Venta al por menor de productos medicinales, cosméticos, de tocador y de limpieza en puestos de ferias y mercados'),
('47893', 'Venta al por menor de artículos de bazar en puestos de ferias y mercados'),
('47894', 'Venta al por menor de artículos de papel, envases, libros, revistas y conexos en puestos de feria y mercados'),
('47895', 'Venta al por menor de materiales de construcción, electrodomésticos, accesorios para autos y similares en puestos de feria y mercados'),
('47896', 'Venta al por menor de equipos accesorios para las comunicaciones en puestos de feria y mercados'),
('47899', 'Venta al por menor en puestos de ferias y mercados n.c.p.'),
('47910', 'Venta al por menor por correo o Internet'),
('47990', 'Otros tipos de venta al por menor no realizada, en almacenes, puestos de venta o mercado'),
('49110', 'Transporte interurbano de pasajeros por ferrocarril'),
('49120', 'Transporte de carga por ferrocarril'),
('49211', 'Transporte de pasajeros urbanos e interurbano mediante buses'),
('49212', 'Transporte de pasajeros interdepartamental mediante microbuses'),
('49213', 'Transporte de pasajeros urbanos e interurbano mediante microbuses'),
('49214', 'Transporte de pasajeros interdepartamental mediante buses'),
('49221', 'Transporte internacional de pasajeros'),
('49222', 'Transporte de pasajeros mediante taxis y autos con chofer'),
('49223', 'Transporte escolar'),
('49225', 'Transporte de pasajeros para excursiones'),
('49226', 'Servicios de transporte de personal'),
('49229', 'Transporte de pasajeros por vía terrestre ncp'),
('49231', 'Transporte de carga urbano'),
('49232', 'Transporte nacional de carga'),
('49233', 'Transporte de carga internacional'),
('49234', 'Servicios de mudanza'),
('49235', 'Alquiler de vehículos de carga con conductor'),
('49300', 'Transporte por oleoducto o gasoducto'),
('50110', 'Transporte de pasajeros marítimo y de cabotaje'),
('50120', 'Transporte de carga marítimo y de cabotaje'),
('50211', 'Transporte de pasajeros por vías de navegación interiores'),
('50212', 'Alquiler de equipo de transporte de pasajeros por vías de navegación interior con conductor'),
('50220', 'Transporte de carga por vías de navegación interiores'),
('51100', 'Transporte aéreo de pasajeros'),
('51201', 'Transporte de carga por vía aérea'),
('51202', 'Alquiler de equipo de aerotransporte con operadores para el propósito de transportar carga'),
('52101', 'Alquiler de instalaciones de almacenamiento en zonas francas'),
('52102', 'Alquiler de silos para conservación y almacenamiento de granos'),
('52103', 'Alquiler de instalaciones con refrigeración para almacenamiento y conservación de alimentos y otros productos'),
('52109', 'Alquiler de bodegas para almacenamiento y depósito n.c.p.'),
('52211', 'Servicio de garaje y estacionamiento'),
('52212', 'Servicios de terminales para el transporte por vía terrestre'),
('52219', 'Servicios para el transporte por vía terrestre n.c.p.'),
('52220', 'Servicios para el transporte acuático'),
('52230', 'Servicios para el transporte aéreo'),
('52240', 'Manipulación de carga'),
('52290', 'Servicios para el transporte ncp'),
('52291', 'Agencias de tramitaciones aduanales'),
('53100', 'Servicios de correo nacional'),
('53200', 'Actividades de correo distintas a las actividades postales nacionales'),
('53201', 'Agencia privada de correo y encomiendas'),
('55101', 'Actividades de alojamiento para estancias cortas'),
('55102', 'Hoteles'),
('55200', 'Actividades de campamentos, parques de vehículos de recreo y parques de caravanas'),
('55900', 'Alojamiento n.c.p.'),
('56101', 'Restaurantes'),
('56106', 'Pupusería'),
('56107', 'Actividades varias de restaurantes'),
('56108', 'Comedores'),
('56109', 'Merenderos ambulantes'),
('56210', 'Preparación de comida para eventos especiales'),
('56291', 'Servicios de provisión de comidas por contrato'),
('56292', 'Servicios de concesión de cafetines y chalet en empresas e instituciones'),
('56299', 'Servicios de preparación de comidas ncp'),
('56301', 'Servicio de expendio de bebidas en salones y bares'),
('56302', 'Servicio de expendio de bebidas en puestos callejeros, mercados y ferias'),
('58110', 'Edición de libros, folletos, partituras y otras ediciones distintas a estas'),
('58120', 'Edición de directorios y listas de correos'),
('58130', 'Edición de periódicos, revistas y otras publicaciones periódicas'),
('58190', 'Otras actividades de edición'),
('58200', 'Edición de programas informáticos (software)'),
('59110', 'Actividades de producción cinematográfica'),
('59120', 'Actividades de post producción de películas, videos y programas de televisión'),
('59130', 'Actividades de distribución de películas cinematográficas, videos y programas de televisión'),
('59140', 'Actividades de exhibición de películas cinematográficas y cintas de vídeo'),
('59200', 'Actividades de edición y grabación de música'),
('60100', 'Servicios de difusiones de radio'),
('60201', 'Actividades de programación y difusión de televisión abierta'),
('60202', 'Actividades de suscripción y difusión de televisión por cable y/o suscripción'),
('60299', 'Servicios de televisión, incluye televisión por cable'),
('60900', 'Programación y transmisión de radio y televisión'),
('61101', 'Servicio de telefonía'),
('61102', 'Servicio de Internet'),
('61103', 'Servicio de telefonía fija'),
('61109', 'Servicio de Internet n.c.p.'),
('61201', 'Servicios de telefonía celular'),
('61202', 'Servicios de Internet inalámbrico'),
('61209', 'Servicios de telecomunicaciones inalámbrico n.c.p.'),
('61301', 'Telecomunicaciones satelitales'),
('61309', 'Comunicación vía satélite n.c.p.'),
('61900', 'Actividades de telecomunicación n.c.p.'),
('62010', 'Programación informática'),
('62020', 'Consultorías y gestión de servicios informáticos'),
('62090', 'Otras actividades de tecnología de información y servicios de computadora'),
('63110', 'Procesamiento de datos y actividades relacionadas'),
('63120', 'Portales WEB'),
('63910', 'Servicios de Agencias de Noticias'),
('63990', 'Otros servicios de información n.c.p.'),
('64110', 'Servicios provistos por el Banco Central de El salvador'),
('64190', 'Bancos'),
('64192', 'Entidades dedicadas al envío de remesas'),
('64199', 'Otras entidades financieras'),
('64200', 'Actividades de sociedades de cartera'),
('64300', 'Fideicomisos, fondos y otras fuentes de financiamiento'),
('64910', 'Arrendamientos financieros'),
('64920', 'Asociaciones cooperativas de ahorro y crédito dedicadas a la intermediación financiera'),
('64921', 'Instituciones emisoras de tarjetas de crédito y otros'),
('64922', 'Tipos de crédito ncp'),
('64928', 'Prestamistas y casas de empeño'),
('64990', 'Actividades de servicios financieros, excepto la financiación de planes de seguros y de pensiones n.c.p.'),
('65110', 'Planes de seguros de vida'),
('65120', 'Planes de seguro excepto de vida'),
('65199', 'Seguros generales de todo tipo'),
('65200', 'Planes se seguro'),
('65300', 'Planes de pensiones'),
('66110', 'Administración de mercados financieros (Bolsa de Valores)'),
('66120', 'Actividades bursátiles (Corredores de Bolsa)'),
('66190', 'Actividades auxiliares de la intermediación financiera ncp'),
('66210', 'Evaluación de riesgos y daños'),
('66220', 'Actividades de agentes y corredores de seguros'),
('66290', 'Otras actividades auxiliares de seguros y fondos de pensiones'),
('66300', 'Actividades de administración de fondos'),
('68101', 'Servicio de alquiler y venta de lotes en cementerios'),
('68109', 'Actividades inmobiliarias realizadas con bienes propios o arrendados n.c.p.'),
('68200', 'Actividades Inmobiliarias Realizadas a Cambio de una Retribución o por Contrata'),
('69100', 'Actividades jurídicas'),
('69200', 'Actividades de contabilidad, teneduría de libros y auditoría; asesoramiento en materia de impuestos'),
('70100', 'Actividades de oficinas centrales de sociedades de cartera'),
('70200', 'Actividades de consultoría en gestión empresarial'),
('71101', 'Servicios de arquitectura y planificación urbana y servicios conexos'),
('71102', 'Servicios de ingeniería'),
('71103', 'Servicios de agrimensura, topografía, cartografía, prospección y geofísica y servicios conexos'),
('71200', 'Ensayos y análisis técnicos'),
('72100', 'Investigaciones y desarrollo experimental en el campo de las ciencias naturales y la ingeniería'),
('72199', 'Investigaciones científicas'),
('72200', 'Investigaciones y desarrollo experimental en el campo de las ciencias sociales y las humanidades científica y desarrollo'),
('73100', 'Publicidad'),
('73200', 'Investigación de mercados y realización de encuestas de opinión pública'),
('74100', 'Actividades de diseño especializado'),
('74200', 'Actividades de fotografía'),
('74900', 'Servicios profesionales y científicos ncp'),
('75000', 'Actividades veterinarias'),
('77101', 'Alquiler de equipo de transporte terrestre'),
('77102', 'Alquiler de equipo de transporte acuático'),
('77103', 'Alquiler de equipo de transporte por vía aérea'),
('77210', 'Alquiler y arrendamiento de equipo de recreo y deportivo'),
('77220', 'Alquiler de cintas de video y discos'),
('77290', 'Alquiler de otros efectos personales y enseres domésticos'),
('77300', 'Alquiler de maquinaria y equipo'),
('77400', 'Arrendamiento de productos de propiedad intelectual'),
('78100', 'Obtención y dotación de personal'),
('78200', 'Actividades de las agencias de trabajo temporal'),
('78300', 'Dotación de recursos humanos y gestión; gestión de las funciones de recursos humanos'),
('79110', 'Actividades de agencias de viajes y organizadores de viajes;'),
('79120', 'Actividades de los operadores turísticos'),
('79900', 'Otros servicios de reservas y actividades relacionadas'),
('80100', 'Servicios de seguridad privados'),
('80201', 'Actividades de servicios de sistemas de seguridad'),
('80202', 'Actividades para la prestación de sistemas de seguridad'),
('80300', 'Actividades de investigación'),
('81100', 'Actividades combinadas de mantenimiento de edificios e instalaciones'),
('81210', 'Limpieza general de edificios'),
('81290', 'Otras actividades combinadas de mantenimiento de edificios e instalaciones ncp'),
('81300', 'Servicio de jardinería'),
('82110', 'Servicios administrativos de oficinas'),
('82190', 'Servicio de fotocopiado y similares, excepto en imprentas'),
('82200', 'Actividades de las centrales de llamadas (call center)'),
('82300', 'Organización de convenciones y ferias de negocios'),
('82910', 'Actividades de agencias de cobro y oficinas de crédito'),
('82921', 'Servicios de envase y empaque de productos alimenticios'),
('82922', 'Servicios de envase y empaque de productos medicinales'),
('82929', 'Servicio de envase y empaque ncp'),
('82990', 'Actividades de apoyo empresariales ncp'),
('84110', 'Actividades de la Administración Pública en general'),
('84111', 'Alcaldías municipales'),
('84120', 'Regulación de las actividades de prestación de servicios sanitarios, educativos, culturales y otros servicios sociales, excepto seguridad social'),
('84130', 'Regulación y facilitación de la actividad económica'),
('84210', 'Actividades de administración y funcionamiento del Ministerio de Relaciones Exteriores'),
('84220', 'Actividades de defensa'),
('84230', 'Actividades de mantenimiento del orden público y de seguridad'),
('84300', 'Actividades de planes de seguridad social de afiliación obligatoria'),
('85101', 'Guardería educativa'),
('85102', 'Enseñanza preescolar o parvularia'),
('85103', 'Enseñanza primaria'),
('85104', 'Servicio de educación preescolar y primaria integrada'),
('85211', 'Enseñanza secundaria tercer ciclo (7°, 8° y 9°)'),
('85212', 'Enseñanza secundaria de formación general bachillerato'),
('85221', 'Enseñanza secundaria de formación técnica y profesional'),
('85222', 'Enseñanza secundaria de formación técnica y profesional integrada con enseñanza primaria'),
('85301', 'Enseñanza superior universitaria'),
('85302', 'Enseñanza superior no universitaria'),
('85303', 'Enseñanza superior integrada a educación secundaria y/o primaria'),
('85410', 'Educación deportiva y recreativa'),
('85420', 'Educación cultural'),
('85490', 'Otros tipos de enseñanza n.c.p.'),
('85499', 'Enseñanza formal'),
('85500', 'Servicios de apoyo a la enseñanza'),
('86100', 'Actividades de hospitales'),
('86201', 'Clínicas médicas'),
('86202', 'Servicios de Odontología'),
('86203', 'Servicios médicos'),
('86901', 'Servicios de análisis y estudios de diagnóstico'),
('86902', 'Actividades de atención de la salud humana'),
('86909', 'Otros Servicio relacionados con la salud ncp'),
('87100', 'Residencias de ancianos con atención de enfermería'),
('87200', 'Instituciones dedicadas al tratamiento del retraso mental, problemas de salud mental y el uso indebido de sustancias nocivas'),
('87300', 'Instituciones dedicadas al cuidado de ancianos y discapacitados'),
('87900', 'Actividades de asistencia a niños y jóvenes'),
('87901', 'Otras actividades de atención en instituciones'),
('88100', 'Actividades de asistencia sociales sin alojamiento para ancianos y discapacitados'),
('88900', 'Servicios sociales sin alojamiento ncp'),
('90000', 'Actividades creativas artísticas y de esparcimiento'),
('91010', 'Actividades de bibliotecas y archivos'),
('91020', 'Actividades de museos y preservación de lugares y edificios históricos'),
('91030', 'Actividades de jardines botánicos, zoológicos y de reservas naturales'),
('92000', 'Actividades de juegos y apuestas'),
('93110', 'Gestión de instalaciones deportivas'),
('93120', 'Actividades de clubes deportivos'),
('93190', 'Otras actividades deportivas'),
('93210', 'Actividades de parques de atracciones y parques temáticos'),
('93291', 'Discotecas y salas de baile'),
('93298', 'Centros vacacionales'),
('93299', 'Actividades de esparcimiento ncp'),
('94110', 'Actividades de organizaciones empresariales y de empleadores'),
('94120', 'Actividades de organizaciones profesionales'),
('94200', 'Actividades de sindicatos'),
('94910', 'Actividades de organizaciones religiosas'),
('94920', 'Actividades de organizaciones políticas'),
('94990', 'Actividades de asociaciones n.c.p.'),
('95110', 'Reparación de computadoras y equipo periférico'),
('95120', 'Reparación de equipo de comunicación'),
('95210', 'Reparación de aparatos electrónicos de consumo'),
('95220', 'Reparación de aparatos doméstico y equipo de hogar y jardín'),
('95230', 'Reparación de calzado y artículos de cuero'),
('95240', 'Reparación de muebles y accesorios para el hogar'),
('95291', 'Reparación de Instrumentos musicales'),
('95292', 'Servicios de cerrajería y copiado de llaves'),
('95293', 'Reparación de joyas y relojes'),
('95294', 'Reparación de bicicletas, sillas de ruedas y rodados n.c.p.'),
('95299', 'Reparaciones de enseres personales n.c.p.'),
('96010', 'Lavado y limpieza de prendas de tela y de piel, incluso la limpieza en seco'),
('96020', 'Peluquería y otros tratamientos de belleza'),
('96030', 'Pompas fúnebres y actividades conexas'),
('96091', 'Servicios de sauna y otros servicios para la estética corporal n.c.p.'),
('96092', 'Servicios n.c.p.'),
('97000', 'Actividad de los hogares en calidad de empleadores de personal doméstico'),
('98100', 'Actividades indiferenciadas de producción de bienes de los hogares privados para uso propio'),
('98200', 'Actividades indiferenciadas de producción de servicios de los hogares privados para uso propio'),
('99000', 'Actividades de organizaciones y órganos extraterritoriales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_CAT_020_PAIS`
--

CREATE TABLE `FE_CAT_020_PAIS` (
  `codigo` int(4) DEFAULT NULL,
  `valores` varchar(38) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_CAT_020_PAIS`
--

INSERT INTO `FE_CAT_020_PAIS` (`codigo`, `valores`) VALUES
(9320, 'ANGUILA'),
(9539, 'ISLAS TURCAS Y CAICOS'),
(9565, 'LITUANIA'),
(9905, 'DAKOTA DEL SUR (USA)'),
(9999, 'No definido en migración'),
(9303, 'AFGANISTÁN'),
(9306, 'ALBANIA'),
(9309, 'ALEMANIA OCCID'),
(9310, 'ALEMANIA ORIENT'),
(9315, 'ALTO VOLTA'),
(9317, 'ANDORRA'),
(9318, 'ANGOLA'),
(9319, 'ANTIG Y BARBUDA'),
(9324, 'ARABIA SAUDITA'),
(9327, 'ARGELIA'),
(9330, 'ARGENTINA'),
(9333, 'AUSTRALIA'),
(9336, 'AUSTRIA'),
(9339, 'BANGLADESH'),
(9342, 'BAHRÉIN'),
(9345, 'BARBADOS'),
(9348, 'BÉLGICA'),
(9349, 'BELICE'),
(9350, 'BENÍN'),
(9354, 'BIRMANIA'),
(9357, 'BOLIVIA'),
(9360, 'BOTSWANA'),
(9363, 'BRASIL'),
(9366, 'BRUNÉI'),
(9372, 'BURUNDI'),
(9374, 'BOPHUTHATSWANA'),
(9375, 'BUTÁN'),
(9377, 'CABO VERDE'),
(9378, 'CAMBOYA'),
(9381, 'CAMERÚN'),
(9384, 'CANADÁ'),
(9387, 'CEILÁN'),
(9390, 'CTRO AFRIC REP'),
(9393, 'COLOMBIA'),
(9394, 'COMORAS-ISLAS'),
(9396, 'CONGO REP DEL'),
(9399, 'CONGO REP DEMOC'),
(9402, 'COREA NORTE'),
(9405, 'COREA SUR'),
(9408, 'COSTA DE MARFIL'),
(9411, 'COSTA RICA'),
(9414, 'CUBA'),
(9417, 'CHAD'),
(9420, 'CHECOSLOVAQUIA'),
(9423, 'CHILE'),
(9426, 'CHINA REP POPUL'),
(9432, 'CHIPRE'),
(9435, 'DAHOMEY'),
(9438, 'DINAMARCA'),
(9440, 'DOMINICA'),
(9441, 'DOMINICANA REP'),
(9444, 'ECUADOR'),
(9446, 'EMIRAT ARAB UNI'),
(9447, 'ESPAÑA'),
(9450, 'EE UU'),
(9453, 'ETIOPIA'),
(9456, 'FIJI-ISLAS'),
(9459, 'FILIPINAS'),
(9462, 'FINLANDIA'),
(9465, 'FRANCIA'),
(9468, 'GABÓN'),
(9471, 'GAMBIA'),
(9474, 'GHANA'),
(9477, 'GIBRALTAR'),
(9480, 'GRECIA'),
(9481, 'GRENADA'),
(9483, 'GUATEMALA'),
(9486, 'GUINEA'),
(9487, 'GUYANA'),
(9495, 'HAITÍ'),
(9498, 'HOLANDA'),
(9501, 'HONDURAS'),
(9504, 'HONG KONG'),
(9507, 'HUNGRÍA'),
(9513, 'INDONESIA'),
(9516, 'IRAK'),
(9519, 'IRÁN'),
(9522, 'IRLANDA'),
(9525, 'ISLANDIA'),
(9526, 'ISLAS SALOMÓN'),
(9528, 'ISRAEL'),
(9531, 'ITALIA'),
(9534, 'JAMAICA'),
(9537, 'JAPÓN'),
(9540, 'JORDANIA'),
(9543, 'KENIA'),
(9544, 'KIRIBATI'),
(9546, 'KUWAIT'),
(9549, 'LAOS'),
(9552, 'LESOTHO'),
(9555, 'LÍBANO'),
(9558, 'LIBERIA'),
(9561, 'LIBIA'),
(9564, 'LIECHTENSTEIN'),
(9567, 'LUXEMBURGO'),
(9570, 'MADAGASCAR'),
(9573, 'MALASIA'),
(9576, 'MALAWI'),
(9577, 'MALDIVAS'),
(9582, 'MALTA'),
(9585, 'MARRUECOS'),
(9591, 'MASCATE Y OMÁN'),
(9594, 'MAURICIO'),
(9597, 'MAURITANIA'),
(9600, 'MÉXICO'),
(9601, 'MICRONESIA'),
(9603, 'MÓNACO'),
(9606, 'MONGOLIA'),
(9609, 'MOZAMBIQUE'),
(9611, 'NAURU'),
(9612, 'NEPAL'),
(9615, 'NICARAGUA'),
(9618, 'NÍGER'),
(9621, 'NIGERIA'),
(9624, 'NORUEGA'),
(9627, 'NVA CALEDONIA'),
(9633, 'NVA ZELANDIA'),
(9636, 'NUEVAS HEBRIDAS'),
(9638, 'PAPUA NV GUINEA'),
(9639, 'PAKISTÁN'),
(9642, 'PANAMÁ'),
(9645, 'PARAGUAY'),
(9648, 'PERÚ'),
(9651, 'POLONIA'),
(9660, 'QATAR EL'),
(9663, 'REINO UNIDO'),
(9666, 'EGIPTO'),
(9669, 'RODESIA'),
(9672, 'RUANDA'),
(9675, 'RUMANIA'),
(9677, 'SAN MARINO'),
(9678, 'SAMOA OCCID'),
(9679, 'SAINT KITTS AND NEVIS'),
(9680, 'SANTA LUCIA'),
(9681, 'SENEGAL'),
(9682, 'SAOTOME Y PRINC'),
(9683, 'SN VIC Y GRENAD'),
(9684, 'SIERRA LEONA'),
(9687, 'SINGAPUR'),
(9690, 'SIRIA'),
(9691, 'SEYCHELLES'),
(9693, 'SOMALIA'),
(9696, 'SUDÁFRICA REP'),
(9699, 'SUDAN'),
(9702, 'SUECIA'),
(9705, 'SUIZA'),
(9706, 'SURINAM'),
(9707, 'SRI LANKA'),
(9708, 'SUECILANDIA'),
(9714, 'TANZANIA'),
(9717, 'TOGO'),
(9720, 'TRINIDAD TOBAGO'),
(9722, 'TONGA'),
(9723, 'TÚNEZ'),
(9725, 'TRANSKEI'),
(9726, 'TURQUÍA'),
(9727, 'TUVALU'),
(9729, 'UGANDA'),
(9732, 'U R S S'),
(9735, 'URUGUAY'),
(9738, 'VATICANO'),
(9739, 'VANUATU'),
(9740, 'VENDA'),
(9741, 'VENEZUELA'),
(9744, 'VIETNAM NORTE'),
(9747, 'VIETNAM SUR'),
(9750, 'YEMEN SUR REP'),
(9756, 'YUGOSLAVIA'),
(9758, 'ZAIRE'),
(9759, 'ZAMBIA'),
(9760, 'ZIMBABWE'),
(9850, 'PUERTO RICO'),
(9862, 'BAHAMAS'),
(9863, 'BERMUDAS'),
(9865, 'MARTINICA'),
(9886, 'NUEVA GUINEA'),
(9898, 'ANT HOLANDESAS'),
(9899, 'TAIWÁN'),
(9897, 'ISLAS VÍRGENES BRITÁNICAS'),
(9887, 'ISLAS GRAN CAIMÁN'),
(9571, 'MACEDONIA'),
(9300, 'EL SALVADOR'),
(9369, 'BULGARIA'),
(9439, 'DJIBOUTI'),
(9510, 'INDIA'),
(9579, 'MALI'),
(9654, 'PORTUGAL'),
(9711, 'TAILANDIA'),
(9736, 'UCRANIA'),
(9737, 'UZBEKISTÁN'),
(9640, 'PALESTINA'),
(9641, 'CROACIA'),
(9673, 'REPUBLICA DE ARMENIA'),
(9472, 'GEORGIA'),
(9311, 'ALEMANIA'),
(9733, 'RUSIA'),
(9541, 'KASAKISTAN'),
(9746, 'VIETNAM'),
(9551, 'LETONIA'),
(9451, 'ESLOVENIA'),
(9338, 'AZERBAIYÁN'),
(9353, 'BIELORRUSIA'),
(9482, 'GROENLANDIA'),
(9494, 'GUINEA-BISSAU'),
(9524, 'ISLA DE COCOS'),
(9304, 'ALAND'),
(9332, 'ARUBA'),
(9454, 'ERITREA'),
(9457, 'ESTONIA'),
(9489, 'GUADALUPE'),
(9491, 'GUAYANA FRANCESA'),
(9492, 'GUERNSEY'),
(9523, 'ISLA DE NAVIDAD'),
(9530, 'ISLAS AZORES'),
(9532, 'ISLA QESHM'),
(9535, 'ISLAS MARIANAS DEL NORTE'),
(9542, 'ISLAS ULTRAMARINAS DE EE UU'),
(9547, 'JERSEY'),
(9548, 'KIRGUISTÁN'),
(9574, 'MALI'),
(9598, 'MAYOTTE'),
(9602, 'MOLDAVIA'),
(9607, 'MONTENEGRO'),
(9608, 'MONSERRAT'),
(9623, 'NORFOLK'),
(9652, 'POLINESIA FRANCESA'),
(9692, 'SVALBARD Y JAN MAYEN'),
(9709, 'TAYIKISTÁN'),
(9712, 'TERRITORIO BRITÁNICO DEL OCÉANO INDICO'),
(9716, 'TIMOR ORIENTAL'),
(9718, 'TOKELAU'),
(9719, 'TURKMENISTÁN'),
(9751, 'YIBUTI'),
(9452, 'WALLIS Y FUTUNA'),
(9901, 'NEVADA (USA)'),
(9902, 'WYOMING (USA)'),
(9903, 'CAMPIONE D\'ITALIA, ITALIA'),
(9664, 'REPUBLICA CHECA'),
(9415, 'CURAZAO'),
(9904, 'FLORIDA (USA)'),
(9514, 'INGLATERRA Y GALES'),
(9906, 'TEXAS (USA)'),
(9359, 'BOSNIA Y HERZEGOVINA'),
(9493, 'GUINEA ECUATORIAL'),
(9521, 'ISLA DE MAN'),
(9533, 'ISLAS MALVINAS'),
(9538, 'ISLAS PITCAIM'),
(9689, 'SERBIA'),
(9713, 'TERRITORIOS AUSTRALES FRANCESES'),
(9449, 'ESLOVAQUIA'),
(9888, 'SAN MAARTEN'),
(9490, 'GUAM'),
(9527, 'ISLAS COOK'),
(9529, 'ISLAS FEROE'),
(9536, 'ISLAS MARSHALL'),
(9545, 'ISLAS VÍRGENES ESTADOUNIDENSES'),
(9568, 'MACAO'),
(9610, 'NAMIBIA'),
(9622, 'NIUE'),
(9643, 'PALAOS'),
(9667, 'REUNIÓN'),
(9676, 'SAHARA OCCIDENTAL'),
(9685, 'SAMOA AMERICANA'),
(9686, 'SAN PEDRO Y MIQUELÓN'),
(9688, 'SANTA ELENA'),
(9715, 'TERRITORIOS PALESTINOS'),
(9900, 'DELAWARE (USA)'),
(9371, 'BURKINA FASO'),
(9376, 'CABINDA'),
(9907, 'WASHINGTON (USA)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_Configuraciones`
--

CREATE TABLE `FE_Configuraciones` (
  `id` int(11) NOT NULL,
  `idConfiguracion` int(11) NOT NULL,
  `parametro` varchar(500) NOT NULL,
  `prueba` text NOT NULL,
  `produccion` text NOT NULL,
  `comentario` varchar(500) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `FE_Configuraciones`
--

INSERT INTO `FE_Configuraciones` (`id`, `idConfiguracion`, `parametro`, `prueba`, `produccion`, `comentario`, `fechaRegistro`) VALUES
(1, 1, 'usuario', '', '', '', '0000-00-00 00:00:00'),
(2, 1, 'clave', '', '', '', '0000-00-00 00:00:00'),
(3, 1, 'token', '', '', '', '2024-07-08 11:49:56'),
(4, 1, 'urlLogin', 'https://apitest.dtes.mh.gob.sv/seguridad/auth', 'https://api.dtes.mh.gob.sv/seguridad/auth', '', '0000-00-00 00:00:00'),
(5, 1, 'urlFirma', 'http://firmadortest.dms.ovh', 'http://firmador.dms.ovh', '', '0000-00-00 00:00:00'),
(6, 1, 'urlEnvio', 'https://apitest.dtes.mh.gob.sv/fesv/recepciondte', 'https://api.dtes.mh.gob.sv/fesv/recepciondte', '', '0000-00-00 00:00:00'),
(7, 1, 'identificacion', 'M001P001', 'M001P001', '', '0000-00-00 00:00:00'),
(8, 1, 'nitEmisor', '', '', '', '0000-00-00 00:00:00'),
(9, 1, 'nrcEmisor', '', '', '', '0000-00-00 00:00:00'),
(10, 1, 'nombreEmisor', '', '', '', '0000-00-00 00:00:00'),
(11, 1, 'codGiroEmisor', '', '', '', '0000-00-00 00:00:00'),
(12, 1, 'giroEmisor', '', '', '', '0000-00-00 00:00:00'),
(13, 1, 'nombreComercialEmisor', '', '', '', '0000-00-00 00:00:00'),
(14, 1, 'departamentoEmisor', '06', '06', '', '0000-00-00 00:00:00'),
(15, 1, 'municipioEmisor', '23', '23', '', '0000-00-00 00:00:00'),
(16, 1, 'direccionEmisor', '', '', '', '0000-00-00 00:00:00'),
(17, 1, 'telefonoEmisor', '', '', '', '0000-00-00 00:00:00'),
(18, 1, 'correoEmisor', '', '', '', '0000-00-00 00:00:00'),
(19, 1, 'tipoEstablecimientoEmisor', '02', '02', '', '0000-00-00 00:00:00'),
(20, 1, 'ambiente', '00', '01', '', '0000-00-00 00:00:00'),
(21, 1, 'modeloFacturacion', '1', '1', '', '0000-00-00 00:00:00'),
(22, 1, 'iva', '0.13', '0.13', '', '0000-00-00 00:00:00'),
(23, 1, 'urlAnulacion', 'https://apitest.dtes.mh.gob.sv/fesv/anulardte', 'https://api.dtes.mh.gob.sv/fesv/anulardte', '', '0000-00-00 00:00:00'),
(24, 1, 'claveApi', '', '', '', '0000-00-00 00:00:00'),
(25, 1, 'ivaRet', '0.01', '0.01', '', '0000-00-00 00:00:00'),
(26, 1, 'urlContingencia', 'https://apitest.dtes.mh.gob.sv/fesv/contingencia', 'https://api.dtes.mh.gob.sv/fesv/contingencia', '', '0000-00-00 00:00:00'),
(27, 1, 'codEstableMH', 'M001', 'M001', '', '0000-00-00 00:00:00'),
(68, 1, 'codPuntoVentaMH', 'P001', 'P001', '', '2024-01-26 14:51:02'),
(125, 1, 'ivaRent', '0.10', '0.10', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impresora`
--

CREATE TABLE `impresora` (
  `idImpresora` int(11) NOT NULL,
  `idSucursalImpresora` int(11) NOT NULL,
  `nombreImpresora` varchar(50) NOT NULL,
  `cocinaImpresora` int(11) NOT NULL,
  `cobroImpresora` int(11) NOT NULL,
  `tipoImpresora` enum('IP','WIN','LIN') NOT NULL,
  `recursoCompartidoImpresora` varchar(50) NOT NULL,
  `IpImpresora` varchar(20) NOT NULL,
  `cuentaImpresora` int(11) NOT NULL,
  `servidorImpresora` varchar(500) NOT NULL,
  `cocineroImpresora` int(11) NOT NULL,
  `pagoImpresora` int(11) NOT NULL COMMENT 'para saber si se pago o no el pedido en la comanda',
  `correlativoImpresora` int(11) NOT NULL,
  `aleatorioImpresora` varchar(100) NOT NULL,
  `estadoImpresora` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `impresora`
--

INSERT INTO `impresora` (`idImpresora`, `idSucursalImpresora`, `nombreImpresora`, `cocinaImpresora`, `cobroImpresora`, `tipoImpresora`, `recursoCompartidoImpresora`, `IpImpresora`, `cuentaImpresora`, `servidorImpresora`, `cocineroImpresora`, `pagoImpresora`, `correlativoImpresora`, `aleatorioImpresora`, `estadoImpresora`) VALUES
(1, 1, 'CAJA 1', 0, 1, 'WIN', 'ticket', '', 1, '127.0.0.1', 0, 0, 0, '66ccb93fdff8d', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumo`
--

CREATE TABLE `insumo` (
  `idInsumo` int(11) NOT NULL,
  `idSucursalInsumo` int(11) NOT NULL,
  `idCategoriaInsumo` int(11) NOT NULL,
  `nombreInsumo` varchar(100) NOT NULL,
  `descripcionInsumo` varchar(500) NOT NULL,
  `marcaInsumo` varchar(100) NOT NULL,
  `stockMinimoInsumo` int(11) NOT NULL,
  `costoPromedioInsumo` decimal(10,4) NOT NULL,
  `proveedor1Insumo` int(11) NOT NULL,
  `proveedor2Insumo` int(11) NOT NULL,
  `proveedor3Insumo` int(11) NOT NULL,
  `exentoIVAInsumo` tinyint(1) NOT NULL,
  `perecederoInsumo` tinyint(1) NOT NULL,
  `revisarInsumo` int(11) NOT NULL COMMENT 'revisar existencia al abrir y cerrar corte',
  `advaloremInsumo` int(11) NOT NULL,
  `advaloremTipoInsumo` enum('Alcohol','Tabaco') NOT NULL,
  `montoSugeridoInsumo` decimal(10,4) NOT NULL,
  `estadoInsumo` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioInsumo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoAjuste`
--

CREATE TABLE `insumoAjuste` (
  `idInsumoAjuste` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `cantidadInicialInsumoAjuste` decimal(10,4) NOT NULL,
  `cantidadFinalInsumoAjuste` decimal(10,4) NOT NULL,
  `idUsuarioInsumoAjuste` int(11) NOT NULL,
  `fechaInsumoAjuste` date NOT NULL DEFAULT current_timestamp(),
  `horaInsumoAjuste` time NOT NULL DEFAULT current_timestamp(),
  `estadoInsumoAjuste` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioInsumoAjuste` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoCategoria`
--

CREATE TABLE `insumoCategoria` (
  `idInsumoCategoria` int(11) NOT NULL,
  `idSucursalInsumoCategoria` int(11) NOT NULL,
  `nombreInsumoCategoria` varchar(100) NOT NULL,
  `descripcionInsumoCategoria` varchar(500) NOT NULL,
  `estadoInsumoCategoria` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioInsumoCategoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoCosto`
--

CREATE TABLE `insumoCosto` (
  `idInsumoCosto` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `costoPromedioInsumoCosto` decimal(10,4) NOT NULL,
  `fechaRegistroInsumoCosto` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoInsumoCosto` enum('Activo','Inactivo','Borrado','') NOT NULL,
  `aleatorioInsumoCosto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoLote`
--

CREATE TABLE `insumoLote` (
  `idInsumoLote` int(11) NOT NULL,
  `idInsumoMovimientoLote` int(11) NOT NULL,
  `idProductoInsumoLote` int(11) NOT NULL,
  `cantidadInsumoLote` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `fechaVencimientoInsumoLote` date NOT NULL,
  `fechaRegistroInsumoLote` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoInsumoLote` enum('Activo','Inactivo','Borrado','Vencido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoMovimiento`
--

CREATE TABLE `insumoMovimiento` (
  `idInsumoMovimiento` int(11) NOT NULL,
  `idSucursalInsumoMovimiento` int(11) NOT NULL,
  `categoriaInsumoMovimiento` enum('Carga','Descarga','Ajuste') NOT NULL,
  `tipoMovimientoInsumo` enum('Carga','Descarga','Compra','Ajuste','Inventario Inicial','Vencimiento','Descarte','Danado','Consumo','Venta') NOT NULL,
  `idPedidoInsumoMovimiento` int(11) NOT NULL COMMENT 'ID del pedido relacionado al descargo cuando es venta',
  `idFacturaInsumoMovimiento` int(11) NOT NULL COMMENT 'ID de la factura relacionada al descargo cuando es por venta',
  `descripcionInsumoMovimiento` text NOT NULL,
  `idProveedorInsumoMovimiento` int(11) NOT NULL,
  `fechaHoraInsumoMovimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `tipoDocumentoInsumoMovimiento` varchar(100) NOT NULL,
  `numeroDocumentoInsumoMovimiento` varchar(50) NOT NULL,
  `totalInsumoMovimiento` decimal(10,4) NOT NULL,
  `fechaRegistroInsumoMovimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `idUsuarioInsumoMovimiento` int(11) NOT NULL,
  `estadoInsumoMovimiento` enum('Activo','Inactivo','Borrado') NOT NULL DEFAULT 'Activo',
  `aleatorioMovimiento` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoMovimientoDetalle`
--

CREATE TABLE `insumoMovimientoDetalle` (
  `idDetalleInsumoMovimiento` int(11) NOT NULL,
  `idInsumoMovimiento` int(11) NOT NULL,
  `idPedidoMovimientoDetalle` int(11) NOT NULL COMMENT 'ID del detalle del pedido relacionado cuando se descarga por venta',
  `idProductoInsumo` int(11) NOT NULL COMMENT 'ID del producto relacionado cuando se descarga por venta',
  `idInsumo` int(11) NOT NULL,
  `cantidadInsumoMovimientoDetalle` decimal(10,4) NOT NULL,
  `stockAnteriorInsumoMovimientoDetalle` decimal(10,4) NOT NULL,
  `stockActualInsumoMovimientoDetalle` decimal(10,4) NOT NULL,
  `descripcionInsumoMovimientoDetalle` varchar(250) NOT NULL,
  `costoInsumoMovimientoDetalle` decimal(10,4) NOT NULL,
  `precioInsumoMovimientoDetalle` decimal(10,4) NOT NULL,
  `idPresentacionInsumoMovimientoDetalle` int(11) NOT NULL,
  `fechaRegistroInsumoMovimientoDetalle` datetime NOT NULL DEFAULT current_timestamp(),
  `idUsuarioInsumoMovimientoDetalle` int(11) NOT NULL,
  `estadoInsumoMovimientoDetalle` enum('Activo','Inactivo','Borrado') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoPresentacion`
--

CREATE TABLE `insumoPresentacion` (
  `idInsumoPresentacion` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `idPresentacion` int(11) NOT NULL,
  `unidadInventarioInsumoPresentacion` int(11) NOT NULL COMMENT 'indica si es la unidad usada en control de inventario',
  `descripcionInsumoPresentacion` varchar(500) NOT NULL,
  `unidadInsumoPresentacion` int(11) NOT NULL,
  `costoInsumoPresentacion` float(10,4) NOT NULL,
  `precioInsumoPresentacion` float(10,4) NOT NULL,
  `fechaRegistroInsumoPresentacion` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoInsumoPresentacion` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioInsumoPresentacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumoStock`
--

CREATE TABLE `insumoStock` (
  `idInsumoStock` int(11) NOT NULL,
  `idSucursalInsumoStock` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `cantidadInsumoStock` decimal(10,4) NOT NULL,
  `estadoInsumoStock` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioInsumoStock` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `idMarca` int(11) NOT NULL,
  `idSucursalMarca` int(11) NOT NULL,
  `idUsuarioMarca` int(11) NOT NULL,
  `fechaHoraEntradaMarca` datetime NOT NULL,
  `fechaHoraSalidaMarca` datetime NOT NULL,
  `estadoMarca` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioMarca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membrecia`
--

CREATE TABLE `membrecia` (
  `idMembrecia` int(11) NOT NULL,
  `idClienteMembrecia` int(11) NOT NULL,
  `codigoMembrecia` varchar(100) NOT NULL,
  `fechaMembrecia` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoMembrecia` enum('Activo','Inactivo','Borrado') NOT NULL DEFAULT 'Activo',
  `idSucursalMembrecia` int(11) NOT NULL,
  `aleatorioMembrecia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `idMenu` int(11) NOT NULL,
  `nombreMenu` varchar(250) NOT NULL,
  `prioridadMenu` int(11) NOT NULL,
  `iconoMenu` varchar(100) NOT NULL,
  `visibleMenu` tinyint(1) NOT NULL,
  `adminMenu` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`idMenu`, `nombreMenu`, `prioridadMenu`, `iconoMenu`, `visibleMenu`, `adminMenu`) VALUES
(1, 'Usuarios', 12, 'fa fa-users', 1, 0),
(2, 'Roles', 8, 'fa fa-tasks', 1, 0),
(3, 'POS', 9, 'fa fa-desktop', 1, 0),
(4, 'Productos', 3, 'fa fa-shopping-cart', 1, 0),
(5, 'Clientes', 1, 'fa fa-users', 1, 0),
(6, 'Modificadores', 5, 'fa fa-outdent', 1, 0),
(7, 'Proveedores', 2, 'fa fa-cubes', 1, 0),
(8, 'Modificadores Tipo', 5, 'fa fa-plus', 0, 0),
(9, 'Zonas', 8, 'fa fa-map', 1, 0),
(10, 'Impresoras', 10, 'fa fa-print', 1, 0),
(11, 'Insumos', 4, 'fa fa-drumstick-bite', 1, 0),
(12, 'Presentaciones', 7, 'fa fa-at', 1, 0),
(13, 'Cajas', 10, 'fa fa-cash-register', 1, 0),
(14, 'Activo Fijo', 13, 'fa fa-building', 0, 0),
(15, 'Contratos', 14, 'fa fa-file-alt', 0, 0),
(16, 'Compras', 15, 'fa fa-shopping-cart', 0, 0),
(17, 'Membrecia', 13, 'fa fa-id-card', 0, 0),
(18, 'Parqueo', 13, 'fa fa-car', 0, 0),
(19, 'Pagos', 14, 'fas fa-money-bill-alt', 0, 0),
(20, 'Planilla', 15, 'fa fa-file-invoice-dollar', 0, 0),
(21, 'Empleados', 16, 'fas fa-user-tie', 0, 0),
(22, 'Señoritas', 13, 'fa fa-female', 0, 0),
(23, 'Servicios', 13, 'fa fa-user-clock', 0, 0),
(24, 'Configuraciones', 17, 'fas fa-cogs', 1, 0),
(25, 'Inventario', 6, 'fa fa-trash', 1, 0),
(26, 'Facturas', 8, 'fa fa-receipt', 1, 0),
(27, 'Reportes', 11, 'fa fa-file-pdf', 1, 0),
(28, 'Corte', 9, 'fa fa-file-invoice-dollar', 1, 0),
(29, 'Marcacion', 11, 'far fa-clock', 1, 0),
(30, 'Pagos', 21, 'fas fa-credit-card', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menuModulos`
--

CREATE TABLE `menuModulos` (
  `idMenuModulo` int(11) NOT NULL,
  `idMenu` int(11) NOT NULL,
  `nombreModulo` varchar(250) NOT NULL,
  `controladorModulo` varchar(250) NOT NULL,
  `funcionModulo` varchar(50) NOT NULL,
  `mostrarModulo` tinyint(1) NOT NULL,
  `adminModulo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menuModulos`
--

INSERT INTO `menuModulos` (`idMenuModulo`, `idMenu`, `nombreModulo`, `controladorModulo`, `funcionModulo`, `mostrarModulo`, `adminModulo`) VALUES
(1, 14, 'Administrar Activo Fijo', 'ActivoFijo', '', 1, 0),
(2, 1, 'Agregar Usuario', 'Usuarios', 'UsuarioAgregar', 0, 0),
(3, 1, 'Editar Usuario', 'Usuarios', 'UsuarioEditar', 0, 0),
(4, 1, 'Eliminar Usuario', 'Usuarios', 'UsuarioEliminar', 0, 0),
(5, 1, 'Cambiar Estado', 'Usuarios', 'UsuarioCambiarEstado', 0, 0),
(6, 1, 'Permisos Usuario', 'Usuarios', 'UsuarioPermisos', 0, 0),
(7, 1, 'Administrar Roles', 'Roles', '', 1, 0),
(8, 1, 'Agregar Rol', 'Roles', 'RolAgregar', 0, 0),
(9, 1, 'Editar Rol', 'Roles', 'RolEditar', 0, 0),
(10, 1, 'Eliminar Rol', 'Roles', 'RolEliminar', 0, 0),
(11, 1, 'Cambiar Estado', 'Roles', 'RolCambiarEstado', 0, 0),
(12, 4, 'Administrar Producto', 'Productos', '', 1, 0),
(13, 5, 'Administrar Cliente', 'Clientes', '', 1, 0),
(14, 7, 'Administrar Proveedor', 'Proveedores', '', 1, 0),
(15, 7, 'Agregar Proveedor', 'Proveedores', 'ProveedorAgregar', 0, 0),
(16, 7, 'Editar Proveedor', 'Proveedores', 'ProveedorEditar', 0, 0),
(17, 7, 'Agregar Proveedor Avanzado', 'Proveedores', 'ProveedorAgregarAvanzado', 0, 0),
(18, 7, 'Editar Proveedor Avanzado', 'Proveedores', 'ProveedorEditarAvanzado', 0, 0),
(19, 6, 'Administrar Modificador', 'Modificadores', '', 1, 0),
(20, 6, 'Administrar Tipo Modificador', 'ModificadoresTipo', '', 1, 0),
(21, 6, 'Agregar Tipo Modificador', 'ModificadoresTipo', 'ModificadoresTipoAgregar', 0, 0),
(22, 6, 'Editar Tipo Modificador', 'ModificadoresTipo', 'ModificadoresTipoEditar', 0, 0),
(23, 6, 'Eliminar Tipo Modificadores', 'ModificadoresTipo', 'ModificadoresTipoEliminar', 0, 0),
(24, 6, 'Estado Tipo Modificadores', 'ModificadoresTipo', 'ModificadoresTipoCambiarEstado', 0, 0),
(25, 4, 'Administrar Categoria Productos', 'ProductosCategoria', '', 1, 0),
(26, 4, 'Agregar Categoria Producto', 'ProductosCategoria', 'ProductosCategoriaAgregar', 0, 0),
(27, 4, 'Editar Categoria Producto ', 'ProductosCategoria', 'ProductosCategoriaEditar', 0, 0),
(28, 6, 'Agregar Modificador', 'Modificadores', 'ModificadoresAgregar', 0, 0),
(29, 6, 'Editar Modificador', 'Modificadores', 'ModificadoresEditar', 0, 0),
(30, 6, 'Eliminar Modificador', 'Modificadores', 'ModificadoresEliminar', 0, 0),
(31, 6, 'Estado Modificador', 'Modificadores', 'ModificadoresCambiarEstado', 0, 0),
(32, 9, 'Administrar Zonas', 'Zonas', '', 1, 0),
(33, 5, 'Administrar Cliente Categoria', 'ClientesCategoria', '', 1, 0),
(34, 9, 'Agregar Zonas', 'Zonas', 'ZonasAgregar', 0, 0),
(35, 9, 'Editar Zonas', 'Zonas', 'ZonasEditar', 0, 0),
(36, 9, 'Cambiar Estado Zonas', 'Zonas', 'ZonasCambiarEstado', 0, 0),
(37, 9, 'Eliminar Zonas', 'Zonas', 'ZonasEliminar', 0, 0),
(38, 9, 'Mesas por Zona', 'Zonas', 'ZonasMesas', 0, 0),
(39, 9, 'Agregar Mesas por Zona', 'Zonas', 'ZonasMesasAgregar', 0, 0),
(40, 9, 'Editar Mesas por Zona', 'Zonas', 'ZonasMesasEditar', 0, 0),
(41, 9, 'Eliminar Mesas por Zona', 'Zonas', 'ZonasMesasEliminar', 0, 0),
(42, 9, 'Trasladar Mesas por Zona', 'Zonas', 'ZonasMesasTrasladar', 0, 0),
(43, 5, 'Agregar Cliente Categoria', 'ClientesCategoria', 'ClientesCategoriaAgregar', 0, 0),
(44, 10, 'Administrar Impresoras', 'Impresoras', '', 1, 0),
(45, 10, 'Agregar Impresora', 'Impresoras', 'ImpresorasAgregar', 0, 0),
(46, 10, 'Editar Impresora', 'Impresoras', 'ImpresorasEditar', 0, 0),
(47, 10, 'Eliminar Impresora', 'Impresoras', 'ImpresorasEliminar', 0, 0),
(48, 10, 'Cambiar Estado Impresora', 'Impresoras', 'ImpresorasCambiarEstado', 0, 0),
(49, 11, 'Administrar Insumos', 'Insumos', '', 1, 0),
(50, 12, 'Administrar Presentaciones', 'Presentaciones', '', 1, 0),
(51, 12, 'Agregar Presentación', 'Presentaciones', 'PresentacionesAgregar', 0, 0),
(52, 12, 'Editar Presentación', 'Presentaciones', 'PresentacionesEditar', 0, 0),
(53, 12, 'Eliminar Presentación', 'Presentaciones', 'PresentacionesEliminar', 0, 0),
(54, 12, 'Cambiar Estado Presentación', 'Presentaciones', 'PresentacionesCambiarEstado', 0, 0),
(55, 11, 'Administrar Categoria Insumo', 'InsumosCategoria', '', 1, 0),
(56, 13, 'Administrar Cajas', 'Cajas', '', 1, 0),
(57, 13, 'Agregar Caja', 'Cajas', 'CajasAgregar', 0, 1),
(58, 13, 'Editar Caja', 'Cajas', 'CajasEditar', 0, 0),
(59, 13, 'Cambiar Estado Caja', 'Cajas', 'CajasCambiarEstado', 0, 0),
(60, 13, 'Eliminar Caja', 'Cajas', 'CajasEliminar', 0, 0),
(61, 3, 'Touch', 'Touch', '', 1, 0),
(62, 14, 'Agregar Activo', 'ActivoFijo', 'ActiviFijoAgregar', 0, 0),
(63, 14, 'Editar Activo', 'ActivoFijo', 'ActiviFijoEditar', 0, 0),
(64, 14, 'Depreciacion Activo', 'ActivoFijo', 'ActiviFijoDepreciacion', 0, 0),
(66, 21, 'Agregar Cargo', 'Cargos', 'CargosAgregar', 0, 0),
(67, 21, 'Editar Cargo', 'Cargos', 'CargosEditar', 0, 0),
(68, 21, 'Eliminar Cargo', 'Cargos', 'CargosEliminar', 0, 0),
(69, 21, 'Cambiar Estado Cargo', 'Cargos', 'CargosCambiarEstado', 0, 0),
(70, 15, 'Administrar Contratos', 'Contratos', '', 1, 0),
(71, 15, 'Agregar Contrato', 'Contratos', 'ContratosAgregar', 0, 0),
(72, 15, 'Editar Contrato', 'Contratos', 'ContratosEditar', 0, 0),
(73, 15, 'Eliminar Contrato', 'Contratos', 'ContratosEliminar', 0, 0),
(74, 15, 'Cambiar Estado Contrato', 'Contratos', 'ContratosCambiarEstado', 0, 0),
(75, 15, 'Administrar Clausulas', 'ContratosClausula', '', 1, 0),
(76, 15, 'Agregar Clausula', 'ContratosClausula', 'ContratosClausulaAgregar', 0, 0),
(77, 15, 'Editar Clausula', 'ContratosClausula', 'ContratosClausulaEditar', 0, 0),
(78, 15, 'Eliminar Clausula', 'ContratosClausula', 'ContratosClausulaEliminar', 0, 0),
(79, 15, 'Cambiar Estado Clausula', 'ContratosClausula', 'ContratosClausulaCambiarEstado', 0, 0),
(80, 16, 'Administrar Compras', 'Compras', '', 1, 0),
(81, 17, 'Administrar Membrecia', 'Membrecia', '', 1, 0),
(82, 17, 'Agregar Membrecia', 'Membrecia', 'MembreciaAgregar', 0, 0),
(83, 17, 'Editar Membrecia', 'Membrecia', 'MembreciaEditar', 0, 0),
(84, 18, 'Administrar Parqueo', 'Parqueo', '', 1, 0),
(85, 18, 'Agregar Parqueo', 'Parqueo', 'ParqueoAgregar', 0, 0),
(86, 18, 'Editar Parqueo', 'Parqueo', 'ParqueoEditar', 0, 0),
(87, 15, 'Administrar Tipos de Contrato', 'ContratosTipo', '', 1, 0),
(88, 15, 'Agregar Tipo de Contrato', 'ContratosTipo', 'ContratosTipoAgregar', 0, 0),
(89, 15, 'Editar Tipo de Contrato', 'ContratosTipo', 'ContratosTipoEditar', 0, 0),
(90, 15, 'Eliminar Tipo de Contrato', 'ContratosTipo', 'ContratosTipoEliminar', 0, 0),
(91, 15, 'Cambiar Estado de Tipo de Contrato', 'ContratosTipos', 'ContratosTiposCambiarEstado', 0, 0),
(92, 19, 'Administrar Pagos', 'Pagos', '', 1, 0),
(93, 19, 'Agregar Pago', 'Pagos', 'PagosAgregar', 0, 0),
(94, 19, 'Editar Pagos', 'Pagos', 'PagosEditar', 0, 0),
(95, 19, 'Eliminar Pago', 'Pagos', 'PagosEliminar', 0, 0),
(96, 19, 'Cambiar Estado de Pago', 'Pagos', 'PagosCambiarEstado', 0, 0),
(97, 20, 'Administrar Planilla', 'Planillas', '', 1, 0),
(98, 20, 'Detalle Planilla', 'Planillas', 'PlanillasDetalle', 0, 0),
(99, 20, 'Mostrar Detalle de Planillas', 'Planillas', 'PlanillasDetalleMostrar', 0, 0),
(100, 20, 'Eliminar Planilla', 'Planillas', 'PlanillasEliminar', 0, 0),
(101, 20, 'Cambiar Estado de la Planilla', 'Planillas', 'PlanillasCambiarEstado', 0, 0),
(102, 20, 'Administrar Porcentaje de Cotización', 'PrestacionesConfigurar', '', 1, 0),
(103, 20, 'Administrar Periodo de Planilla', 'PeriodosPlanilla', '', 1, 0),
(104, 20, 'Agregar Periodo', 'PeriodosPlanilla', 'PeriodosPlanillaAgregar', 0, 0),
(105, 20, 'Finalizar Periodo', 'PeriodosPlanilla', 'PeriodosPlanillaEditar', 0, 0),
(106, 20, 'Eliminar Periodo', 'PeriodosPlanilla', 'PeriodosPlanillaEliminar', 0, 0),
(107, 20, 'Cambiar Estado del Periodo', 'PeriodosPlanilla', 'PeriodosPlanillaCambiarEstado', 0, 0),
(108, 20, 'Consultar Periodo Vigente', 'PeriodosPlanilla', 'PeriodosPlanillaVigente', 0, 0),
(109, 21, 'Administrar Empleado', 'Empleados', '', 1, 0),
(110, 21, 'Agregar Empleado', 'Empleados', 'EmpleadosAgregar', 0, 0),
(111, 21, 'Editar Empleado', 'Empleados', 'EmpleadosEditar', 0, 0),
(112, 21, 'Eliminar Empleado', 'Empleados', 'EmpleadosEliminar', 0, 0),
(113, 21, 'Cambiar Estado del Empleado', 'Empleados', 'EmpleadosCambiarEstado', 0, 0),
(114, 22, 'Administrar Señoritas', 'Senorita', '', 1, 0),
(115, 22, 'Administrar Categoria Señorita', 'SenoritaCategoria', '', 1, 0),
(118, 23, 'Administrar Servicios', 'Servicios', '', 1, 0),
(119, 23, 'Administrar Categoria Servicios', 'ServicioCategoria', '', 1, 0),
(120, 21, 'Administrar Cargo', 'Cargos', '', 1, 0),
(122, 15, 'Buscar Empleado', 'Contratos', 'ContratosAutocompleteEmpleado', 0, 0),
(123, 15, 'Eliminar Clausula del Tipo de Contrato', 'ContratosTipo', 'ContratosTipoClausulaEliminar', 0, 0),
(124, 15, 'Consultar Clausula para Tipo de Contrato', 'ContratosTipo', 'ContratosTipoClausula', 0, 0),
(125, 21, 'Administrar Descuentos', 'EmpleadosDescuento', '', 1, 0),
(126, 21, 'Agregar Descuento', 'EmpleadosDescuento', 'EmpleadosDescuentoAgregar', 0, 0),
(127, 21, 'Editar Descuento', 'EmpleadosDescuento', 'EmpleadosDescuentoEditar', 0, 0),
(128, 21, 'Eliminar Descuento', 'EmpleadosDescuento', 'EmpleadosDescuentoEliminar', 0, 0),
(129, 21, 'Cambiar Estado Descuento', 'EmpleadosDescuento', 'EmpleadosDescuentoCambiarEstado', 0, 0),
(130, 21, 'Administrar Bonos', 'EmpleadosBono', '', 1, 0),
(131, 21, 'Agregar Bono', 'EmpleadosBono', 'EmpleadosBonoAgregar', 0, 0),
(132, 21, 'Editar Bono', 'EmpleadosBono', 'EmpleadosBonoEditar', 0, 0),
(133, 21, 'Eliminar Bono', 'EmpleadosBono', 'EmpleadosBonoEliminar', 0, 0),
(134, 21, 'Cambiar Estado Bono', 'EmpleadosBono', 'EmpleadosBonoCambiarEstado', 0, 0),
(135, 21, 'Administrar Descuentos por Cuota', 'EmpleadosDescuentoCuota', '', 1, 0),
(136, 21, 'Agregar Descuento por Cuota', 'EmpleadosDescuentoCuota', 'EmpleadosDescuentoCuotaAgregar', 0, 0),
(137, 21, 'Editar Descuento por Cuota', 'EmpleadosDescuentoCuota', 'EmpleadosDescuentoCuotaEditar', 0, 0),
(138, 21, 'Eliminar Descuento por Cuota', 'EmpleadosDescuentoCuota', 'EmpleadosDescuentoCuotaEliminar', 0, 0),
(139, 21, 'Cambiar Estado de Descuento por Cuota', 'EmpleadosDescuentoCuota', 'EmpleadosDescuentoCuotaCambiarEstado', 0, 0),
(140, 21, 'Administrar Institución Financiera', 'InstitucionFinanciera', '', 1, 0),
(141, 21, 'Agregar Institución Financiera', 'InstitucionFinanciera', 'InstitucionFinancieraAgregar', 0, 0),
(142, 21, 'Editar Institución Financiera', 'InstitucionFinanciera', 'InstitucionFinancieraEditar', 0, 0),
(143, 21, 'Eliminar Institución Financiera', 'InstitucionFinanciera', 'InstitucionFinancieraEliminar', 0, 0),
(144, 21, 'Cambiar Estado de Institución Financiera', 'InstitucionFinanciera', 'InstitucionFinancieraCambiarEstado', 0, 0),
(145, 20, 'Administrar Tramo de Renta', 'TramosRenta', '', 1, 0),
(146, 20, 'Agregar Tramo', 'TramosRenta', 'TramosRentaAgregar', 0, 0),
(147, 20, 'Editar Tramo', 'TramosRenta', 'TramosRentaEditar', 0, 0),
(148, 20, 'Eliminar Tramo', 'TramosRenta', 'TramosRentaEliminar', 0, 0),
(149, 20, 'Cambiar Estado del Tramo de la Renta', 'TramosRenta', 'TramosRentaCambiarEstado', 0, 0),
(150, 20, 'Editar Planillas', 'Planillas', 'PlanillasEditar', 0, 0),
(151, 20, 'Generar Planilla', 'Planillas', 'PlanillasGenerar', 0, 0),
(152, 24, 'Administrar Configuraciones', 'Configuraciones', '', 1, 0),
(153, 24, 'Agregar Configuración', 'Configuraciones', 'ConfiguracionesAgregar', 0, 0),
(154, 24, 'Editar Configuración', 'Configuraciones', 'ConfiguracionesEditar', 0, 0),
(155, 24, 'Eliminar Configuración', 'Configuraciones', 'ConfiguracionesEliminar', 0, 0),
(156, 24, 'Cambiar Estado de Configuración', 'Configuraciones', 'ConfiguracionesCambiarEstado', 0, 0),
(157, 25, 'Movimientos Inventario', 'MovimientosInventario', '', 1, 0),
(158, 25, 'Consulta de Stock', 'ConsultaStock', '', 1, 0),
(159, 25, 'Ver Movimiento Inventario', 'MovimientosInventario', 'MovimientoInsumoVer', 0, 0),
(160, 25, 'Cargas de Inventario', 'MovimientosInventario', 'MovimientoInsumoAgregar', 0, 0),
(161, 25, 'Descargas de Inventario', 'MovimientosInventario', 'MovimientoInsumoDescarga', 0, 0),
(162, 26, 'Administrar Facturas', 'Facturas', '', 1, 0),
(163, 27, 'Reporte de Ventas', 'Reportes', 'ReporteVenta', 1, 0),
(164, 13, 'Administrar Movimientos', 'MovimientosCaja', '', 1, 0),
(165, 28, 'Administrar Corte', 'CorteAdmin', '', 1, 0),
(166, 28, 'Realizar Apertura', 'CorteAdmin', 'aperturaCaja', 0, 0),
(167, 28, 'Realizar Corte', 'CorteAdmin', 'RealizarCorte', 0, 0),
(168, 28, 'Realizar Apertura Turno', 'CorteAdmin', 'aperturaTurno', 0, 0),
(169, 28, 'Realizar Apertura Turno Usuario', 'CorteAdmin', 'aperturaTurnoUsuario', 0, 0),
(170, 28, 'Revisar Inventario', 'CorteAdmin', 'RevisionInventario', 0, 0),
(171, 28, 'Realizar Cierre Turno', 'CorteAdmin', 'RealizarCierreTurno', 0, 0),
(172, 28, 'Revisar Inventario Cierre', 'CorteAdmin', 'RevisionInventarioFinal', 0, 0),
(173, 27, 'Reporte de Utilidades', 'Reportes', 'ReporteUtilidad', 1, 0),
(174, 27, 'Reporte de Utilidad por Producto', 'Reportes', 'ReporteUtilidadProducto', 1, 0),
(175, 27, 'Reporte de Kardex', 'Reportes', 'ReporteKardex', 1, 0),
(176, 1, 'Administrar Usuarios', 'Usuarios', '', 1, 0),
(177, 27, 'Reporte de Inventario', 'Reportes', 'ReporteInventario', 1, 0),
(178, 27, 'Reporte General de Utilidad', 'Reportes', 'ReporteGeneralUtilidad', 1, 0),
(179, 27, 'Cinta de Auditoria', 'Reportes', 'CintaAuditoria', 1, 0),
(180, 27, 'Reporte de Reposicion', 'Reportes', 'ReporteReposicion', 1, 0),
(181, 27, 'Reporte de Vencimiento', 'Reportes', 'ReporteVencimiento', 1, 0),
(182, 27, 'Reporte Venta Mesero', 'Reportes', 'ReporteVentaMesero', 1, 0),
(183, 27, 'Reporte Detalle Cuentas', 'Reportes', 'ReporteDetallePedido', 1, 0),
(184, 3, 'Cocina', 'Cocina', '', 1, 0),
(185, 13, 'Agregar Ingresos de Caja', 'MovimientosCaja', 'MovimientosCajaIngreso', 0, 0),
(186, 13, 'Agregar Salidas de Caja', 'MovimientosCaja', 'MovimientosCajaSalida', 0, 0),
(187, 13, 'Editar Movimientos de Caja', 'MovimientosCaja', 'MovimientosCajaEditar', 0, 0),
(188, 13, 'Borrar Movimientos de Caja', 'MovimientosCaja', 'MovimientosCajaBorrar', 0, 0),
(189, 24, 'Administrar Respaldos', 'Respaldos', '', 1, 0),
(190, 24, 'Hacer Respaldos', 'Respaldos', 'respaldoHacer', 0, 0),
(191, 24, 'Cargar Respaldos', 'Respaldos', 'respaldoCargar', 0, 0),
(192, 24, 'Restaurar Respaldos', 'Respaldos', 'respaldoRestaurar', 0, 0),
(193, 24, 'Borrar Respaldo', 'Respaldos', 'respaldoEliminar', 0, 0),
(194, 24, 'Cambiar Estado Respaldo', 'Respaldos', 'respaldoCambiarEstado', 0, 0),
(195, 27, 'Reporte Advalorem', 'Reportes', 'ReporteAdvalorem', 1, 0),
(196, 5, 'Agregar Cliente', 'Clientes', 'ClienteAgregar', 0, 0),
(197, 5, 'Editar Cliente', 'Clientes', 'ClienteEditar', 0, 0),
(198, 5, 'Agregar Cliente Avanzado', 'Clientes', 'ClienteAgregarAvanzado', 0, 0),
(199, 5, 'Editar Cliente Avanzado', 'Clientes', 'ClienteEditarAvanzado', 0, 0),
(200, 5, 'Cambiar Estado Cliente', 'Clientes', 'ClienteCambiarEstado', 0, 0),
(201, 5, 'Eliminar Cliente', 'Clientes', 'ClienteEliminar', 0, 0),
(202, 5, 'Editar Cliente Categoria', 'ClientesCategoria', 'ClientesCategoriaEditar', 0, 0),
(203, 5, 'Cambiar Estado Cliente Categoria', 'ClientesCategoria', 'ClientesCategoriaCambiarEstado', 0, 0),
(204, 5, 'Eliminar Cliente Categoria', 'ClientesCategoria', 'ClientesCategoriaEliminar', 0, 0),
(205, 7, 'Cambiar Estado Proveedor', 'Proveedores', 'ProveedorCambiarEstado', 0, 0),
(206, 7, 'Eliminar Proveedor', 'Proveedores', 'ProveedorEliminar', 0, 0),
(207, 4, 'Agregar Producto', 'Productos', 'ProductoAgregar', 0, 0),
(208, 4, 'Agregar Modificadores Producto', 'Productos', 'ProductoAgregarModificador', 0, 0),
(209, 4, 'Agregar Receta Producto', 'Productos', 'ProductoAgregarInsumoGeneral', 0, 0),
(210, 4, 'Editar Producto', 'Productos', 'ProductoEditar', 0, 0),
(211, 4, 'Editar Modificadores Producto', 'Productos', 'ProductoEditarModificador', 0, 0),
(212, 4, 'Editar Receta Producto', 'Productos', 'ProductoEditarInsumoGeneral', 0, 0),
(213, 4, 'Cambiar Estado Producto', 'Productos', 'ProductoCambiarEstado', 0, 0),
(214, 4, 'Eliminar Producto', 'Productos', 'ProductoEliminar', 0, 0),
(215, 4, 'Cambiar Estado Categoria Producto', 'ProductosCategoria', 'ProductosCategoriaCambiarEstado', 0, 0),
(216, 4, 'Eliminar Categoria Producto', 'ProductosCategoria', 'ProductosCategoriaEliminar', 0, 0),
(217, 11, 'Agregar Insumos', 'Insumos', 'InsumoAgregar', 0, 0),
(218, 11, 'Editar Insumos', 'Insumos', 'InsumoEditar', 0, 0),
(219, 11, 'Cambiar Estado Insumos', 'Insumos', 'InsumoCambiarEstado', 0, 0),
(220, 11, 'Eliminar Insumos', 'Insumos', 'InsumoEliminar', 0, 0),
(221, 11, 'Agregar Categoria Insumo', 'InsumosCategoria', 'InsumosCategoriaAgregar', 0, 0),
(222, 11, 'Editar Categoria Insumo', 'InsumosCategoria', 'InsumosCategoriaEditar', 0, 0),
(223, 11, 'Cambiar Estado Categoria Insumo', 'InsumosCategoria', 'InsumosCategoriaCambiarEstado', 0, 0),
(224, 11, 'Eliminar Categoria Insumo', 'InsumosCategoria', 'InsumosCategoriaEliminar', 0, 0),
(225, 25, 'Ajuste de Stock', 'ConsultaStock', 'ConsultaStockAjuste', 0, 0),
(226, 29, 'Marcas', 'Marcas', '', 1, 0),
(227, 27, 'Reporte de Marcas', 'Reportes', 'ReporteMarca', 1, 0),
(228, 27, 'Reporte de Compras', 'Reportes', 'ReporteCompra', 1, 0),
(229, 27, 'Reporte de Conteo de Inventario', 'Reportes', 'ReporteConteo', 1, 0),
(230, 27, 'Reporte de Ventas Detalle', 'Reportes', 'ReporteVentaItem', 1, 0),
(231, 30, 'Administrar Pagos', 'Pago', '', 1, 0),
(232, 30, 'Recibo', 'Pago', 'PagoRecibo', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modificador`
--

CREATE TABLE `modificador` (
  `idModificador` int(11) NOT NULL,
  `idSucursalModificador` int(11) NOT NULL,
  `idModificadorTipo` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `nombreModificador` varchar(500) NOT NULL,
  `precioModificador` float(10,2) NOT NULL,
  `estadoModificador` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioModificador` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modificadorTipo`
--

CREATE TABLE `modificadorTipo` (
  `idModificadorTipo` int(11) NOT NULL,
  `idSucursalModificadorTipo` int(11) NOT NULL,
  `nombreModificadorTipo` varchar(500) NOT NULL,
  `variosModificadorTipo` tinyint(1) NOT NULL,
  `estadoModificadorTipo` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioModificadorTipo` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipio`
--

CREATE TABLE `municipio` (
  `idMunicipio` int(3) NOT NULL,
  `nombreMunicipio` varchar(50) NOT NULL,
  `idDepartamento` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Municipios de El Salvador';

--
-- Volcado de datos para la tabla `municipio`
--

INSERT INTO `municipio` (`idMunicipio`, `nombreMunicipio`, `idDepartamento`) VALUES
(1, 'Ahuachapán', 1),
(2, 'Jujutla', 1),
(3, 'Atiquizaya', 1),
(4, 'Concepción de Ataco', 1),
(5, 'El Refugio', 1),
(6, 'Guaymango', 1),
(7, 'Apaneca', 1),
(8, 'San Francisco Menéndez', 1),
(9, 'San Lorenzo', 1),
(10, 'San Pedro Puxtla', 1),
(11, 'Tacuba', 1),
(12, 'Turín', 1),
(13, 'Candelaria de la Frontera', 2),
(14, 'Chalchuapa', 2),
(15, 'Coatepeque', 2),
(16, 'El Congo', 2),
(17, 'El Porvenir', 2),
(18, 'Masahuat', 2),
(19, 'Metapán', 2),
(20, 'San Antonio Pajonal', 2),
(21, 'San Sebastián Salitrillo', 2),
(22, 'Santa Ana', 2),
(23, 'Santa Rosa Guachipilín', 2),
(24, 'Santiago de la Frontera', 2),
(25, 'Texistepeque', 2),
(26, 'Acajutla', 3),
(27, 'Armenia', 3),
(28, 'Caluco', 3),
(29, 'Cuisnahuat', 3),
(30, 'Izalco', 3),
(31, 'Juayúa', 3),
(32, 'Nahuizalco', 3),
(33, 'Nahulingo', 3),
(34, 'Salcoatitán', 3),
(35, 'San Antonio del Monte', 3),
(36, 'San Julián', 3),
(37, 'Santa Catarina Masahuat', 3),
(38, 'Santa Isabel Ishuatán', 3),
(39, 'Santo Domingo de Guzmán', 3),
(40, 'Sonsonate', 3),
(41, 'Sonzacate', 3),
(42, 'Alegría', 4),
(43, 'Berlín', 11),
(44, 'California', 11),
(45, 'Concepción Batres', 11),
(46, 'El Triunfo', 11),
(47, 'Ereguayquín', 11),
(48, 'Estanzuelas', 11),
(49, 'Jiquilisco', 11),
(50, 'Jucuapa', 11),
(51, 'Jucuarán', 11),
(52, 'Mercedes Umaña', 11),
(53, 'Nueva Granada', 11),
(54, 'Ozatlán', 11),
(55, 'Puerto El Triunfo', 11),
(56, 'San Agustín', 11),
(57, 'San Buenaventura', 11),
(58, 'San Dionisio', 11),
(59, 'San Francisco Javier', 11),
(60, 'Santa Elena', 11),
(61, 'Santa María', 11),
(62, 'Santiago de María', 11),
(63, 'Tecapán', 11),
(64, 'Usulután', 11),
(65, 'Carolina', 13),
(66, 'Chapeltique', 13),
(67, 'Chinameca', 13),
(68, 'Chirilagua', 13),
(69, 'Ciudad Barrios', 13),
(70, 'Comacarán', 13),
(71, 'El Tránsito', 13),
(72, 'Lolotique', 13),
(73, 'Moncagua', 13),
(74, 'Nueva Guadalupe', 13),
(75, 'Nuevo Edén de San Juan', 13),
(76, 'Quelepa', 13),
(77, 'San Antonio del Mosco', 13),
(78, 'San Gerardo', 13),
(79, 'San Jorge', 13),
(80, 'San Luis de la Reina', 13),
(81, 'San Miguel', 13),
(82, 'San Rafael Oriente', 13),
(83, 'Sesori', 13),
(84, 'Uluazapa', 13),
(85, 'Arambala', 12),
(86, 'Cacaopera', 12),
(87, 'Chilanga', 12),
(88, 'Corinto', 12),
(89, 'Delicias de Concepción', 12),
(90, 'El Divisadero', 12),
(91, 'El Rosario', 12),
(92, 'Gualococti', 12),
(93, 'Guatajiagua', 12),
(94, 'Joateca', 12),
(95, 'Jocoaitique', 12),
(96, 'Jocoro', 12),
(97, 'Lolotiquillo', 12),
(98, 'Meanguera', 12),
(99, 'Osicala', 12),
(100, 'Perquín', 12),
(101, 'San Carlos', 12),
(102, 'San Fernando', 12),
(103, 'San Francisco Gotera', 12),
(104, 'San Isidro', 12),
(105, 'San Simón', 12),
(106, 'Sensembra', 12),
(107, 'Sociedad', 12),
(108, 'Torola', 12),
(109, 'Yamabal', 12),
(110, 'Yoloaiquín', 12),
(111, 'La Unión', 14),
(112, 'San Alejo', 14),
(113, 'Yucuaiquín', 14),
(114, 'Conchagua', 14),
(115, 'Intipucá', 14),
(116, 'San José', 14),
(117, 'El Carmen', 14),
(118, 'Yayantique', 14),
(119, 'Bolívar', 14),
(120, 'Meanguera del Golfo', 14),
(121, 'Santa Rosa de Lima', 14),
(122, 'Pasaquina', 14),
(123, 'ANAMOROS', 14),
(124, 'Nueva Esparta', 14),
(125, 'El Sauce', 14),
(126, 'Concepción de Oriente', 14),
(127, 'Polorós', 14),
(128, 'Lislique ', 14),
(129, 'Antiguo Cuscatlán', 4),
(130, 'Chiltiupán', 4),
(131, 'Ciudad Arce', 4),
(132, 'Colón', 4),
(133, 'Comasagua', 4),
(134, 'Huizúcar', 4),
(135, 'Jayaque', 4),
(136, 'Jicalapa', 4),
(137, 'La Libertad', 4),
(138, 'Santa Tecla', 4),
(139, 'Nuevo Cuscatlán', 4),
(140, 'San Juan Opico', 4),
(141, 'Quezaltepeque', 4),
(142, 'Sacacoyo', 4),
(143, 'San José Villanueva', 4),
(144, 'San Matías', 4),
(145, 'San Pablo Tacachico', 4),
(146, 'Talnique', 4),
(147, 'Tamanique', 4),
(148, 'Teotepeque', 4),
(149, 'Tepecoyo', 4),
(150, 'Zaragoza', 4),
(151, 'Agua Caliente', 5),
(152, 'Arcatao', 5),
(153, 'Azacualpa', 5),
(154, 'Cancasque', 5),
(155, 'Chalatenango', 5),
(156, 'Citalá', 5),
(157, 'Comapala', 5),
(158, 'Concepción Quezaltepeque', 5),
(159, 'Dulce Nombre de María', 5),
(160, 'El Carrizal', 5),
(161, 'El Paraíso', 5),
(162, 'La Laguna', 5),
(163, 'La Palma', 5),
(164, 'La Reina', 5),
(165, 'Las Vueltas', 5),
(166, 'Nueva Concepción', 5),
(167, 'Nueva Trinidad', 5),
(168, 'Nombre de Jesús', 5),
(169, 'Ojos de Agua', 5),
(170, 'Potonico', 5),
(171, 'San Antonio de la Cruz', 5),
(172, 'San Antonio Los Ranchos', 5),
(173, 'San Fernando', 5),
(174, 'San Francisco Lempa', 5),
(175, 'San Francisco Morazán', 5),
(176, 'San Ignacio', 5),
(177, 'San Isidro Labrador', 5),
(178, 'Las Flores', 5),
(179, 'San Luis del Carmen', 5),
(180, 'San Miguel de Mercedes', 5),
(181, 'San Rafael', 5),
(182, 'Santa Rita', 5),
(183, 'Tejutla', 5),
(184, 'Cojutepeque', 7),
(185, 'Candelaria', 7),
(186, 'El Carmen', 7),
(187, 'El Rosario', 7),
(188, 'Monte San Juan', 7),
(189, 'Oratorio de Concepción', 7),
(190, 'San Bartolomé Perulapía', 7),
(191, 'San Cristóbal', 7),
(192, 'San José Guayabal', 7),
(193, 'San Pedro Perulapán', 7),
(194, 'San Rafael Cedros', 7),
(195, 'San Ramón', 7),
(196, 'Santa Cruz Analquito', 7),
(197, 'Santa Cruz Michapa', 7),
(198, 'Suchitoto', 7),
(199, 'Tenancingo', 7),
(200, 'Aguilares', 6),
(201, 'Apopa', 6),
(202, 'Ayutuxtepeque', 6),
(203, 'Cuscatancingo', 6),
(204, 'Ciudad Delgado', 6),
(205, 'El Paisnal', 6),
(206, 'Guazapa', 6),
(207, 'Ilopango', 6),
(208, 'Mejicanos', 6),
(209, 'Nejapa', 6),
(210, 'Panchimalco', 6),
(211, 'Rosario de Mora', 6),
(212, 'San Marcos', 6),
(213, 'San Martín', 6),
(214, 'San Salvador', 6),
(215, 'Santiago Texacuangos', 6),
(216, 'Santo Tomás', 6),
(217, 'Soyapango', 6),
(218, 'Tonacatepeque', 6),
(219, 'Zacatecoluca', 8),
(220, 'Cuyultitán', 8),
(221, 'El Rosario', 8),
(222, 'Jerusalén', 8),
(223, 'Mercedes La Ceiba', 8),
(224, 'Olocuilta', 8),
(225, 'Paraíso de Osorio', 8),
(226, 'San Antonio Masahuat', 8),
(227, 'San Emigdio', 8),
(228, 'San Francisco Chinameca', 8),
(229, 'San Pedro Masahuat', 8),
(230, 'San Juan Nonualco', 8),
(231, 'San Juan Talpa', 8),
(232, 'San Juan Tepezontes', 8),
(233, 'San Luis La Herradura', 8),
(234, 'San Luis Talpa', 8),
(235, 'San Miguel Tepezontes', 8),
(236, 'San Pedro Nonualco', 8),
(237, 'San Rafael Obrajuelo', 8),
(238, 'Santa María Ostuma', 8),
(239, 'Santiago Nonualco', 8),
(240, 'Tapalhuaca', 8),
(241, 'Cinquera', 9),
(242, 'Dolores', 9),
(243, 'Guacotecti', 9),
(244, 'Ilobasco', 9),
(245, 'Jutiapa', 9),
(246, 'San Isidro', 9),
(247, 'Sensuntepeque', 9),
(248, 'Tejutepeque', 9),
(249, 'Victoria', 9),
(250, 'Apastepeque', 10),
(251, 'Guadalupe', 10),
(252, 'San Cayetano Istepeque', 10),
(253, 'San Esteban Catarina', 10),
(254, 'San Ildefonso', 10),
(255, 'San Lorenzo', 10),
(256, 'San Sebastián', 10),
(257, 'San Vicente', 10),
(258, 'Santa Clara', 10),
(259, 'Santo Domingo', 10),
(260, 'Tecoluca', 10),
(261, 'Tepetitán', 10),
(262, 'Verapaz', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `idPago` int(11) NOT NULL,
  `idSucursalPago` int(11) NOT NULL,
  `nombrePago` varchar(50) NOT NULL,
  `montoPago` decimal(9,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `aleatorioPago` varchar(50) NOT NULL,
  `estadoPago` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagoDetalle`
--

CREATE TABLE `pagoDetalle` (
  `idPagoDetalle` int(11) NOT NULL,
  `idPagoPagoDetalle` int(11) NOT NULL,
  `montoPagoDetalle` decimal(9,2) NOT NULL,
  `fechaPagoDetalle` date NOT NULL,
  `aleatorioPagoDetalle` varchar(50) NOT NULL,
  `estadoPagoDetalle` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL,
  `idSucursal` varchar(50) NOT NULL,
  `mes` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `dia` int(11) NOT NULL,
  `fechaPago` date NOT NULL,
  `horaPago` time NOT NULL,
  `pagado` tinyint(1) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `idCuentaBancaria` varchar(50) NOT NULL,
  `total` decimal(20,8) NOT NULL,
  `metodoPago` varchar(50) NOT NULL,
  `capturaTransferencia` text NOT NULL,
  `idTransaccion` varchar(250) NOT NULL,
  `codigoAutorizacion` varchar(100) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`idPago`, `idSucursal`, `mes`, `anio`, `dia`, `fechaPago`, `horaPago`, `pagado`, `nombre`, `correo`, `idCuentaBancaria`, `total`, `metodoPago`, `capturaTransferencia`, `idTransaccion`, `codigoAutorizacion`, `fechaRegistro`) VALUES
(1, '1', 11, 2024, 25, '0000-00-00', '00:00:00', 0, '', '', '', 0.00000000, '', '', '', '', '2024-09-27 10:34:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagosDetalle`
--

CREATE TABLE `pagosDetalle` (
  `idPagoDetalle` int(11) NOT NULL,
  `idPago` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(20,8) NOT NULL,
  `subtotal` decimal(20,8) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parqueo`
--

CREATE TABLE `parqueo` (
  `idParqueo` int(11) NOT NULL,
  `idClienteParqueo` int(11) NOT NULL,
  `horaEntradaParqueo` time NOT NULL,
  `horaSalidaParqueo` time NOT NULL,
  `placaParqueo` varchar(50) NOT NULL,
  `totalParqueo` decimal(9,2) NOT NULL,
  `fechaEntradaParqueo` date NOT NULL,
  `fechaSalidaParqueo` date NOT NULL,
  `estadoParqueo` enum('Pendiente','Cobrado','Anulado','Borrado') NOT NULL DEFAULT 'Pendiente',
  `idSucursalParqueo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL,
  `idSucursalPedido` int(11) NOT NULL,
  `tipoCuentaPedido` varchar(50) NOT NULL,
  `idZonaPedido` int(11) DEFAULT NULL,
  `zonaPedido` varchar(500) DEFAULT NULL,
  `tipoAumentoPedido` varchar(50) DEFAULT NULL,
  `aumentoPedido` float(10,2) DEFAULT NULL,
  `idMesaPedido` int(11) DEFAULT NULL,
  `totalPedido` float(10,2) NOT NULL,
  `nombreClientePedido` varchar(500) NOT NULL,
  `direccionClientePedido` varchar(500) NOT NULL,
  `personasPedido` int(11) NOT NULL,
  `idUsuarioPedido` int(11) NOT NULL,
  `fechaPedido` date NOT NULL DEFAULT current_timestamp(),
  `horaPedido` time NOT NULL DEFAULT current_timestamp(),
  `idCortePedido` int(11) NOT NULL,
  `estadoPedido` enum('Activo','Inactivo','Borrado','Pendiente','Finalizado','Anulado') NOT NULL,
  `aleatorioPedido` varchar(50) NOT NULL,
  `idCliente` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidoComentario`
--

CREATE TABLE `pedidoComentario` (
  `idPedidoComentario` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `comentarioPedidoComentario` varchar(500) NOT NULL,
  `idUsuarioPedidoComentario` int(11) NOT NULL,
  `fechaHoraPedidoComentario` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoPedidoComentario` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioPedidoComentario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidoDetalle`
--

CREATE TABLE `pedidoDetalle` (
  `idPedidoDetalle` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `tipoPedido` enum('Producto','Producto Especial','Producto Empleado') NOT NULL,
  `idCorte` int(11) NOT NULL,
  `idReferenciaPedidoDetalle` int(11) NOT NULL,
  `idProductoPedidoDetalle` int(11) NOT NULL,
  `cantidadPedidoDetalle` int(11) NOT NULL,
  `precioPedidoDetalle` float(10,2) NOT NULL,
  `precioOriginalPedidoDetalle` float(10,2) NOT NULL,
  `comentarioPedidoDetalle` varchar(500) NOT NULL,
  `regaliaPedidoDetalle` int(11) NOT NULL,
  `senoritaPedidoDetalle` int(11) NOT NULL,
  `grupoPedidoDetalle` int(11) NOT NULL,
  `fechaHoraPedidoDetalle` datetime NOT NULL DEFAULT current_timestamp(),
  `estadoPedidoDetalle` enum('Activo','Inactivo','Borrado','Pendiente','Finalizado') NOT NULL,
  `llevarLocalPedidoDetalle` int(11) NOT NULL COMMENT 'si este detalle es para llevar en cuentas de local',
  `impreso` tinyint(1) NOT NULL,
  `motivoPedidoDetalle` varchar(500) NOT NULL,
  `usuarioAutorizaPedidoDetalle` int(11) NOT NULL COMMENT 'id de quien autoriza la modificación',
  `idUsuarioPedidoDetalle` int(11) NOT NULL,
  `aleatorioPedidoDetalle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidoSubDetalle`
--

CREATE TABLE `pedidoSubDetalle` (
  `idPedidoSubDetalle` int(11) NOT NULL,
  `idPedidoDetalle` int(11) NOT NULL,
  `idReferenciaPedidoSubDetalle` int(11) NOT NULL,
  `variosPedidoSubDetalle` int(11) NOT NULL,
  `nombreModTipoPedidoSubDetalle` varchar(500) NOT NULL,
  `aumentoPedidoSubDetalle` float(10,2) NOT NULL,
  `idModPedidoSubDetalle` int(11) NOT NULL,
  `nombrePedidoSubDetalle` varchar(500) NOT NULL,
  `estadoPedidoSubDetalle` enum('Activo','Inactivo','Borrado','Pendiente','Finalizado') NOT NULL,
  `aleatorioPedidoSubDetalle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodoPlanilla`
--

CREATE TABLE `periodoPlanilla` (
  `idPeriodoPlanilla` int(11) NOT NULL,
  `idSucursalPeriodoPlanilla` int(11) NOT NULL,
  `desdePeriodoPlanilla` date NOT NULL,
  `hastaPeriodoPlanilla` date NOT NULL,
  `mesPeriodoPlanilla` int(11) NOT NULL,
  `anioPeriodoPlanilla` int(11) NOT NULL,
  `descripcionPeriodoPlanilla` varchar(200) NOT NULL,
  `vencidoPeriodoPlanilla` enum('Finalizado','Vigente') NOT NULL,
  `fechaInicioPagoPeriodoPlanilla` date NOT NULL,
  `fechaFinPagoPeriodoPlanilla` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planilla`
--

CREATE TABLE `planilla` (
  `idPlanilla` int(11) NOT NULL,
  `idSucursalPlanilla` int(11) NOT NULL,
  `idEmpleadoPlanilla` int(5) NOT NULL,
  `correlativoPlanilla` int(11) NOT NULL,
  `idDepartamentoPlanilla` int(11) NOT NULL,
  `sueldoPlanilla` decimal(10,2) NOT NULL,
  `isssPlanilla` decimal(10,2) NOT NULL,
  `afpPlanilla` decimal(10,2) NOT NULL,
  `rentaPlanilla` decimal(10,2) NOT NULL,
  `abonosPlanilla` decimal(10,2) DEFAULT NULL,
  `descuentosPlanilla` decimal(10,2) NOT NULL,
  `liquidoPlanilla` decimal(10,2) NOT NULL,
  `idPeriodoPlanilla` int(11) NOT NULL,
  `fechaRegistroPlanilla` date NOT NULL,
  `impresoPlanilla` enum('true','false') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pagadoPlanilla` enum('true','false') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fraccionPlanilla` decimal(10,2) NOT NULL,
  `horasTrabajadasPlanilla` int(11) NOT NULL,
  `minutosTrabajadosPlanilla` int(11) NOT NULL,
  `horasExtraPlanilla` int(11) NOT NULL,
  `minutosExtraPlanilla` int(11) NOT NULL,
  `aleatorioPlanilla` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `estadoPlanilla` enum('Activo','Inactivo','Borrado') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantilla`
--

CREATE TABLE `plantilla` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(500) NOT NULL,
  `precio` varchar(20) NOT NULL,
  `categoria` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE `presentacion` (
  `idPresentacion` int(11) NOT NULL,
  `idSucursalPresentacion` int(11) NOT NULL,
  `nombrePresentacion` varchar(50) NOT NULL,
  `unidadPresentacion` varchar(50) NOT NULL,
  `descripcionPresentacion` varchar(100) NOT NULL,
  `aleatorioPresentacion` varchar(100) NOT NULL,
  `estadoPresentacion` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `idProductoCategoria` int(11) NOT NULL,
  `idSucursalProducto` int(11) NOT NULL,
  `insumoProducto` tinyint(1) NOT NULL,
  `barcodeProducto` text NOT NULL,
  `nombreProducto` varchar(500) NOT NULL,
  `descripcionProducto` varchar(500) NOT NULL,
  `precioVentaProducto` decimal(10,2) NOT NULL,
  `precioEspecialProducto` decimal(10,2) NOT NULL,
  `precioEmpleadoProducto` decimal(10,2) NOT NULL,
  `impresoraProducto` int(11) NOT NULL,
  `imagenProducto` varchar(1000) NOT NULL,
  `estadoProducto` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProducto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoCategoria`
--

CREATE TABLE `productoCategoria` (
  `idProductoCategoria` int(11) NOT NULL,
  `idSucursalProductoCategoria` int(11) NOT NULL,
  `nombreProductoCategoria` varchar(500) NOT NULL,
  `estadoProductoCategoria` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoCategoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoCategoriaEspecifica`
--

CREATE TABLE `productoCategoriaEspecifica` (
  `idProductoCategoriaEspecifica` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idProductoCategoria` int(11) NOT NULL,
  `estadoProductoCategoriaEspecifica` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoCategoriaEspecifica` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoInsumo`
--

CREATE TABLE `productoInsumo` (
  `idProductoInsumo` int(11) NOT NULL,
  `idSucursalProductoInsumo` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idModificador` int(11) NOT NULL COMMENT 'Se usa si el insumo del producto viene de un modificador',
  `nombreProductoInsumo` varchar(500) NOT NULL,
  `idPresentacionProductoInsumo` int(11) NOT NULL,
  `cantidadProductoInsumo` float(10,4) NOT NULL,
  `idUnicoProductoInsumo` varchar(100) NOT NULL,
  `estadoProductoInsumo` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoInsumo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoModificador`
--

CREATE TABLE `productoModificador` (
  `idProductoModificador` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idModificadorTipo` int(11) NOT NULL,
  `cantidadProductoModificador` int(11) NOT NULL,
  `idUnicoProductoModificador` varchar(100) NOT NULL,
  `idUnicoSelectProductoModificador` varchar(100) NOT NULL,
  `nombreProductoModificador` varchar(100) NOT NULL,
  `variosProductoModificador` int(11) NOT NULL,
  `estadoProductoModificador` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoModificador` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoModificadorDetalle`
--

CREATE TABLE `productoModificadorDetalle` (
  `idProductoModificadorDetalle` int(11) NOT NULL,
  `idProductoModificador` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idModificador` int(11) NOT NULL,
  `aumentoProductoModificadorDetalle` float(10,2) NOT NULL,
  `idUnicoProductoModificadorDetalle` varchar(100) NOT NULL,
  `idUnicoPadreProductoModificadorDetalle` varchar(100) NOT NULL,
  `idUnicoAbueloProductoModificadorDetalle` varchar(100) NOT NULL,
  `idModificadorTipoProductoModificadorDetalle` int(11) NOT NULL,
  `variosProductoModificadorDetalle` int(11) NOT NULL,
  `nombreProductoModificadorDetalle` varchar(100) NOT NULL,
  `nombrePadreProductoModificadorDetalle` varchar(100) NOT NULL,
  `estadoProductoModificadorDetalle` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoModificadorDetalle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productoModificadorInsumo`
--

CREATE TABLE `productoModificadorInsumo` (
  `idProductoModificadorInsumo` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idModificador` int(11) NOT NULL,
  `idInsumo` int(11) NOT NULL,
  `idPresentacionModificadorInsumo` int(11) NOT NULL,
  `cantidadModificadorInsumo` float(10,4) NOT NULL,
  `estadoProductoModificadorInsumo` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProductoModificadorInsumo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` int(11) NOT NULL,
  `idSucursalProveedor` int(11) NOT NULL,
  `nombreProveedor` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `razonSocialProveedor` varchar(30) NOT NULL,
  `departamentoProveedor` int(3) NOT NULL,
  `municipioProveedor` int(5) NOT NULL,
  `nrcProveedor` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `duiProveedor` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nitProveedor` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `giroProveedor` varchar(30) NOT NULL,
  `categoriaProveedor` varchar(30) NOT NULL,
  `direccionProveedor` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `telefonoProveedor` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `correoProveedor` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `porcentajeRetencionProveedor` decimal(3,2) NOT NULL,
  `avanzadoProveedor` tinyint(1) NOT NULL,
  `estadoProveedor` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioProveedor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idProveedor`, `idSucursalProveedor`, `nombreProveedor`, `razonSocialProveedor`, `departamentoProveedor`, `municipioProveedor`, `nrcProveedor`, `duiProveedor`, `nitProveedor`, `giroProveedor`, `categoriaProveedor`, `direccionProveedor`, `telefonoProveedor`, `correoProveedor`, `porcentajeRetencionProveedor`, `avanzadoProveedor`, `estadoProveedor`, `aleatorioProveedor`) VALUES
(1, 1, 'PROVEEDORES VARIOS', '', 0, 0, '00000-0', '', '0000-000000-000-0', '', '', 'DIRECCION', '1111-1111', 'correo@correo.com', 0.00, 0, 'Activo', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedorContactos`
--

CREATE TABLE `proveedorContactos` (
  `idContactoProveedor` int(11) NOT NULL,
  `nombreContactoProveedor` varchar(100) NOT NULL,
  `cargoContactoProveedor` varchar(30) NOT NULL,
  `telefonoContactoProveedor` varchar(12) NOT NULL,
  `correoContactoProveedor` varchar(50) NOT NULL,
  `idProveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `senorita`
--

CREATE TABLE `senorita` (
  `idSenorita` int(11) NOT NULL,
  `idSenoritaCategoria` int(11) NOT NULL,
  `idSucursalSenorita` int(11) NOT NULL,
  `nombreSenorita` varchar(500) NOT NULL,
  `apodoSenorita` varchar(100) NOT NULL,
  `alturaSenorita` float(10,2) NOT NULL,
  `pesoSenorita` float(10,2) NOT NULL,
  `nacionalidadSenorita` varchar(100) NOT NULL,
  `edadSenorita` int(11) NOT NULL,
  `extraSenorita` varchar(500) NOT NULL,
  `estadoSenorita` enum('Activo','Inactivo','Borrado','Ocupado') NOT NULL,
  `aleatorioSenorita` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `senoritaCategoria`
--

CREATE TABLE `senoritaCategoria` (
  `idSenoritaCategoria` int(11) NOT NULL,
  `idSucursalSenoritaCategoria` int(11) NOT NULL,
  `nombreSenoritaCategoria` varchar(500) NOT NULL,
  `tipoComisionSenoritaCategoria` enum('Porcentaje','Monto') NOT NULL,
  `comisionSenoritaCategoria` float(10,2) NOT NULL,
  `estadoSenoritaCategoria` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioSenoritaCategoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `senoritaServicio`
--

CREATE TABLE `senoritaServicio` (
  `idSenoritaServicio` int(11) NOT NULL,
  `idSenorita` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `montoSenoritaServicio` float(10,2) NOT NULL,
  `idFacturaSenoritaServicio` int(11) NOT NULL,
  `estadoSenoritaServicio` enum('Activo','Inactivo','Borrado','Pendiente','Finalizado') NOT NULL,
  `aleatorioSenoritaServicio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(11) NOT NULL,
  `idServicioCategoria` int(11) NOT NULL,
  `idSucursalServicio` int(11) NOT NULL,
  `descripcionServicio` varchar(500) NOT NULL,
  `tiempoServicio` int(11) NOT NULL,
  `estadoServicio` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioServicio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicioCategoria`
--

CREATE TABLE `servicioCategoria` (
  `idServicioCategoria` int(11) NOT NULL,
  `idSucursalServicioCategoria` int(11) NOT NULL,
  `nombreServicioCategoria` varchar(500) NOT NULL,
  `descripcionServicioCategoria` varchar(500) NOT NULL,
  `estadoServicioCategoria` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioServicioCategoria` varchar(100) NOT NULL,
  `prioridadServicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicioDetalle`
--

CREATE TABLE `servicioDetalle` (
  `idServicioDetalle` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `idSenoritaCategoriaServicioDetalle` int(11) NOT NULL,
  `montoServicioDetalle` float(10,2) NOT NULL,
  `porcentajeSenoritaServicioDetalle` float(10,2) NOT NULL,
  `estadoServicioDetalle` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioServicioDetalle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `idSucursal` int(11) NOT NULL,
  `idConfiguracionFe` int(11) NOT NULL,
  `nombreSucursal` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`idSucursal`, `idConfiguracionFe`, `nombreSucursal`) VALUES
(1, 1, 'Sucursal 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramoRenta`
--

CREATE TABLE `tramoRenta` (
  `idTramoRenta` int(11) NOT NULL,
  `idSucursalTramoRenta` int(11) NOT NULL,
  `desdeTramoRenta` float NOT NULL,
  `hastaTramoRenta` float NOT NULL,
  `porcentajeTramoRenta` float NOT NULL,
  `excesoTramoRenta` float NOT NULL,
  `cuotaTramoRenta` float NOT NULL,
  `aleatorioTramoRenta` varchar(50) NOT NULL,
  `estadoTramoRenta` enum('Activo','Inactivo','Borrado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Volcado de datos para la tabla `tramoRenta`
--

INSERT INTO `tramoRenta` (`idTramoRenta`, `idSucursalTramoRenta`, `desdeTramoRenta`, `hastaTramoRenta`, `porcentajeTramoRenta`, `excesoTramoRenta`, `cuotaTramoRenta`, `aleatorioTramoRenta`, `estadoTramoRenta`) VALUES
(1, 1, 0.02, 472, 0, 0, 0, '6215c6e7d3a7e', 'Activo'),
(2, 1, 472.01, 895.24, 0.1, 472, 17.67, '', 'Activo'),
(3, 1, 895.25, 2038.1, 0.2, 895.24, 60, '', 'Activo'),
(4, 1, 2038.11, 1000000000, 0.3, 2038.1, 288.57, '', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `idSucursalUsuario` int(11) NOT NULL,
  `nombreUsuario` varchar(50) NOT NULL,
  `usuarioUsuario` varchar(50) NOT NULL,
  `claveUsuario` varchar(250) NOT NULL,
  `codigoUsuario` varchar(350) NOT NULL,
  `autorizadoUsuario` int(11) NOT NULL,
  `codigoAutorizacionUsuario` varchar(50) NOT NULL,
  `rolUsuario` int(11) NOT NULL,
  `adminUsuario` tinyint(1) NOT NULL,
  `activoUsuario` tinyint(1) NOT NULL,
  `superAdminUsuario` tinyint(1) NOT NULL,
  `aleatorioUsuario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `idSucursalUsuario`, `nombreUsuario`, `usuarioUsuario`, `claveUsuario`, `codigoUsuario`, `autorizadoUsuario`, `codigoAutorizacionUsuario`, `rolUsuario`, `adminUsuario`, `activoUsuario`, `superAdminUsuario`, `aleatorioUsuario`) VALUES
(-1, 1, 'ADMINISTRACION', 'zeus', '4fe93c18d41df71e6057a86006e9f916fa8eeba425fc041afd1092f7d3a6b857554fa66fed1bf71660d418cab074680958a4ea4bbb15764444a733de6b9407a4muvQvNOTUY3ur1ITTdHuo0Be8T1b07reQOOc6r3m95c=', '2020', 1, '', 1, 1, 1, 0, ''),
(1, 1, 'ADMIN', 'admin', '4c852d3559a7879333b4721f493cc248adb2a4bb264f224c9de8119d4759a1bd86b1c1af48f703af399a8612042d3cdf15a1c7af2ac091f745694a76cc6fc64boZnXm4XbTQBJ+4wcHvzQwyNYlwhkeFNllG7OvRjIF9s=', '2024', 1, '', 1, 1, 1, 0, '64d5580461239');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarioPermisos`
--

CREATE TABLE `usuarioPermisos` (
  `idUsuarioPermiso` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarioRoles`
--

CREATE TABLE `usuarioRoles` (
  `idRol` int(11) NOT NULL,
  `idSucursalRol` int(11) NOT NULL,
  `nombreRol` varchar(100) NOT NULL,
  `activoRol` tinyint(1) NOT NULL,
  `aleatorioRol` varchar(100) NOT NULL,
  `rutaRol` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarioRoles`
--

INSERT INTO `usuarioRoles` (`idRol`, `idSucursalRol`, `nombreRol`, `activoRol`, `aleatorioRol`, `rutaRol`) VALUES
(1, 1, 'Cajero', 1, '62c07dcaeb45d', 'CorteAdmin'),
(2, 1, 'Mesero', 1, '62b681d76288b', 'touch'),
(6, 2, 'Bodeguero', 1, '', 'inicio'),
(10, 1, 'Cocinero', 1, '', 'Cocina'),
(13, 1, 'encargado de bodega', 1, '', 'inicio'),
(14, 1, 'Contadora', 1, '', 'inicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarioRolesDetalle`
--

CREATE TABLE `usuarioRolesDetalle` (
  `idRolDetalle` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarioRolesDetalle`
--

INSERT INTO `usuarioRolesDetalle` (`idRolDetalle`, `idRol`, `idModulo`) VALUES
(49, 6, 7),
(50, 6, 8),
(70, 7, 7),
(71, 7, 1),
(72, 7, 3),
(73, 7, 6),
(109, 2, 5),
(110, 2, 6),
(111, 2, 61),
(130, 1, 165),
(131, 1, 166),
(132, 1, 167),
(133, 1, 168),
(134, 1, 169),
(135, 1, 170),
(136, 1, 171),
(137, 1, 172),
(138, 1, 61),
(139, 10, 61),
(219, 13, 49),
(220, 13, 217),
(221, 13, 218),
(222, 13, 55),
(223, 13, 221),
(224, 13, 222),
(225, 13, 158),
(226, 13, 157),
(227, 13, 159),
(228, 13, 160),
(229, 13, 161),
(230, 14, 176);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zona`
--

CREATE TABLE `zona` (
  `idZona` int(11) NOT NULL,
  `idSucursalZona` int(11) NOT NULL,
  `nombreZona` varchar(500) NOT NULL,
  `capacidadZona` int(11) NOT NULL,
  `tipoAumentoZona` enum('Ninguno','Monto','Porcentaje') NOT NULL,
  `aumentoZona` float(10,2) NOT NULL,
  `visibleZona` tinyint(1) NOT NULL,
  `precioRegularZona` int(11) NOT NULL,
  `precioEspecialZona` int(11) NOT NULL,
  `precioEmpleadoZona` int(11) NOT NULL,
  `estadoZona` enum('Activo','Inactivo','Borrado') NOT NULL,
  `aleatorioZona` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonaMesa`
--

CREATE TABLE `zonaMesa` (
  `idZonaMesa` int(11) NOT NULL,
  `idZona` int(11) NOT NULL,
  `nombreZonaMesa` int(100) NOT NULL,
  `capacidadZonaMesa` int(11) NOT NULL,
  `estadoZonaMesa` enum('Activo','Inactivo','Borrado','Ocupada') NOT NULL,
  `aleatorioZonaMesa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activoFijo`
--
ALTER TABLE `activoFijo`
  ADD PRIMARY KEY (`idActivoFijo`);

--
-- Indices de la tabla `baseDatos`
--
ALTER TABLE `baseDatos`
  ADD PRIMARY KEY (`idBaseDatos`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`idCaja`);

--
-- Indices de la tabla `cajaDocumento`
--
ALTER TABLE `cajaDocumento`
  ADD PRIMARY KEY (`idCajaDocumento`);

--
-- Indices de la tabla `cajaDocumentoHistorial`
--
ALTER TABLE `cajaDocumentoHistorial`
  ADD PRIMARY KEY (`idCajaDocumentoHistorial`);

--
-- Indices de la tabla `cajaMovimiento`
--
ALTER TABLE `cajaMovimiento`
  ADD PRIMARY KEY (`idCajaMovimiento`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`idCargo`);

--
-- Indices de la tabla `categoriaActivoFijo`
--
ALTER TABLE `categoriaActivoFijo`
  ADD PRIMARY KEY (`idCategoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`);

--
-- Indices de la tabla `clienteCategoria`
--
ALTER TABLE `clienteCategoria`
  ADD PRIMARY KEY (`idClienteCategoria`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`idConfiguracion`);

--
-- Indices de la tabla `contrato`
--
ALTER TABLE `contrato`
  ADD PRIMARY KEY (`idContrato`);

--
-- Indices de la tabla `contratoClausula`
--
ALTER TABLE `contratoClausula`
  ADD PRIMARY KEY (`idContratoClausula`);

--
-- Indices de la tabla `contratoTipo`
--
ALTER TABLE `contratoTipo`
  ADD PRIMARY KEY (`idContratoTipo`);

--
-- Indices de la tabla `contratoTipoClausula`
--
ALTER TABLE `contratoTipoClausula`
  ADD PRIMARY KEY (`idContratoTipoClausula`);

--
-- Indices de la tabla `corteCaja`
--
ALTER TABLE `corteCaja`
  ADD PRIMARY KEY (`idCorteCaja`);

--
-- Indices de la tabla `corteHistorial`
--
ALTER TABLE `corteHistorial`
  ADD PRIMARY KEY (`idCorteHistorial`);

--
-- Indices de la tabla `corteHistorialDocumento`
--
ALTER TABLE `corteHistorialDocumento`
  ADD PRIMARY KEY (`idCorteHistorialDocumento`);

--
-- Indices de la tabla `corteRevisionInsumo`
--
ALTER TABLE `corteRevisionInsumo`
  ADD PRIMARY KEY (`idRevisionInsumo`);

--
-- Indices de la tabla `corteTurno`
--
ALTER TABLE `corteTurno`
  ADD PRIMARY KEY (`idTurno`);

--
-- Indices de la tabla `cover`
--
ALTER TABLE `cover`
  ADD PRIMARY KEY (`idCover`);

--
-- Indices de la tabla `cuentasBancarias`
--
ALTER TABLE `cuentasBancarias`
  ADD PRIMARY KEY (`idCuentaBancaria`);

--
-- Indices de la tabla `damas`
--
ALTER TABLE `damas`
  ADD PRIMARY KEY (`idDama`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`idDepartamento`);

--
-- Indices de la tabla `detalleCompra`
--
ALTER TABLE `detalleCompra`
  ADD PRIMARY KEY (`idCompraDetalle`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`idDocumento`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`idEmpleado`);

--
-- Indices de la tabla `empleadoBono`
--
ALTER TABLE `empleadoBono`
  ADD PRIMARY KEY (`idEmpleadoBono`);

--
-- Indices de la tabla `empleadoDescuento`
--
ALTER TABLE `empleadoDescuento`
  ADD PRIMARY KEY (`idEmpleadoDescuento`);

--
-- Indices de la tabla `empleadoDescuentoCuota`
--
ALTER TABLE `empleadoDescuentoCuota`
  ADD PRIMARY KEY (`idEmpleadoDescuentoCuota`);

--
-- Indices de la tabla `empleadoDescuentoDetalle`
--
ALTER TABLE `empleadoDescuentoDetalle`
  ADD PRIMARY KEY (`idEmpleadoDescuentoDetalle`);

--
-- Indices de la tabla `empleadoInstitucionFinanciera`
--
ALTER TABLE `empleadoInstitucionFinanciera`
  ADD PRIMARY KEY (`idInstitucionFinanciera`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`idFactura`);

--
-- Indices de la tabla `facturaDetalle`
--
ALTER TABLE `facturaDetalle`
  ADD PRIMARY KEY (`idFacturaDetalle`);

--
-- Indices de la tabla `facturaModificacion`
--
ALTER TABLE `facturaModificacion`
  ADD PRIMARY KEY (`idFacturaModificacion`);

--
-- Indices de la tabla `FE_CAT_002_TipodeDocumento`
--
ALTER TABLE `FE_CAT_002_TipodeDocumento`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_012_Departamento`
--
ALTER TABLE `FE_CAT_012_Departamento`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_014_UnidaddeMedida`
--
ALTER TABLE `FE_CAT_014_UnidaddeMedida`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_016_CondiciondelaOperacion`
--
ALTER TABLE `FE_CAT_016_CondiciondelaOperacion`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_017_FormadePago`
--
ALTER TABLE `FE_CAT_017_FormadePago`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_018_Plazo`
--
ALTER TABLE `FE_CAT_018_Plazo`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_CAT_019_CodigodeActividadEco`
--
ALTER TABLE `FE_CAT_019_CodigodeActividadEco`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `FE_Configuraciones`
--
ALTER TABLE `FE_Configuraciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `impresora`
--
ALTER TABLE `impresora`
  ADD PRIMARY KEY (`idImpresora`);

--
-- Indices de la tabla `insumo`
--
ALTER TABLE `insumo`
  ADD PRIMARY KEY (`idInsumo`);

--
-- Indices de la tabla `insumoAjuste`
--
ALTER TABLE `insumoAjuste`
  ADD PRIMARY KEY (`idInsumoAjuste`);

--
-- Indices de la tabla `insumoCategoria`
--
ALTER TABLE `insumoCategoria`
  ADD PRIMARY KEY (`idInsumoCategoria`);

--
-- Indices de la tabla `insumoCosto`
--
ALTER TABLE `insumoCosto`
  ADD PRIMARY KEY (`idInsumoCosto`);

--
-- Indices de la tabla `insumoLote`
--
ALTER TABLE `insumoLote`
  ADD PRIMARY KEY (`idInsumoLote`);

--
-- Indices de la tabla `insumoMovimiento`
--
ALTER TABLE `insumoMovimiento`
  ADD PRIMARY KEY (`idInsumoMovimiento`);

--
-- Indices de la tabla `insumoMovimientoDetalle`
--
ALTER TABLE `insumoMovimientoDetalle`
  ADD PRIMARY KEY (`idDetalleInsumoMovimiento`);

--
-- Indices de la tabla `insumoPresentacion`
--
ALTER TABLE `insumoPresentacion`
  ADD PRIMARY KEY (`idInsumoPresentacion`);

--
-- Indices de la tabla `insumoStock`
--
ALTER TABLE `insumoStock`
  ADD PRIMARY KEY (`idInsumoStock`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`idMarca`);

--
-- Indices de la tabla `membrecia`
--
ALTER TABLE `membrecia`
  ADD PRIMARY KEY (`idMembrecia`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`idMenu`);

--
-- Indices de la tabla `menuModulos`
--
ALTER TABLE `menuModulos`
  ADD PRIMARY KEY (`idMenuModulo`);

--
-- Indices de la tabla `modificador`
--
ALTER TABLE `modificador`
  ADD PRIMARY KEY (`idModificador`);

--
-- Indices de la tabla `modificadorTipo`
--
ALTER TABLE `modificadorTipo`
  ADD PRIMARY KEY (`idModificadorTipo`);

--
-- Indices de la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD PRIMARY KEY (`idMunicipio`,`idDepartamento`),
  ADD KEY `fk_municipio_departamento1_idx` (`idDepartamento`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`idPago`);

--
-- Indices de la tabla `pagoDetalle`
--
ALTER TABLE `pagoDetalle`
  ADD PRIMARY KEY (`idPagoDetalle`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idPago`);

--
-- Indices de la tabla `pagosDetalle`
--
ALTER TABLE `pagosDetalle`
  ADD PRIMARY KEY (`idPagoDetalle`);

--
-- Indices de la tabla `parqueo`
--
ALTER TABLE `parqueo`
  ADD PRIMARY KEY (`idParqueo`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`);

--
-- Indices de la tabla `pedidoComentario`
--
ALTER TABLE `pedidoComentario`
  ADD PRIMARY KEY (`idPedidoComentario`);

--
-- Indices de la tabla `pedidoDetalle`
--
ALTER TABLE `pedidoDetalle`
  ADD PRIMARY KEY (`idPedidoDetalle`);

--
-- Indices de la tabla `pedidoSubDetalle`
--
ALTER TABLE `pedidoSubDetalle`
  ADD PRIMARY KEY (`idPedidoSubDetalle`);

--
-- Indices de la tabla `periodoPlanilla`
--
ALTER TABLE `periodoPlanilla`
  ADD PRIMARY KEY (`idPeriodoPlanilla`);

--
-- Indices de la tabla `planilla`
--
ALTER TABLE `planilla`
  ADD PRIMARY KEY (`idPlanilla`);

--
-- Indices de la tabla `plantilla`
--
ALTER TABLE `plantilla`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  ADD PRIMARY KEY (`idPresentacion`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`);

--
-- Indices de la tabla `productoCategoria`
--
ALTER TABLE `productoCategoria`
  ADD PRIMARY KEY (`idProductoCategoria`);

--
-- Indices de la tabla `productoCategoriaEspecifica`
--
ALTER TABLE `productoCategoriaEspecifica`
  ADD PRIMARY KEY (`idProductoCategoriaEspecifica`);

--
-- Indices de la tabla `productoInsumo`
--
ALTER TABLE `productoInsumo`
  ADD PRIMARY KEY (`idProductoInsumo`);

--
-- Indices de la tabla `productoModificador`
--
ALTER TABLE `productoModificador`
  ADD PRIMARY KEY (`idProductoModificador`);

--
-- Indices de la tabla `productoModificadorDetalle`
--
ALTER TABLE `productoModificadorDetalle`
  ADD PRIMARY KEY (`idProductoModificadorDetalle`);

--
-- Indices de la tabla `productoModificadorInsumo`
--
ALTER TABLE `productoModificadorInsumo`
  ADD PRIMARY KEY (`idProductoModificadorInsumo`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idProveedor`);

--
-- Indices de la tabla `proveedorContactos`
--
ALTER TABLE `proveedorContactos`
  ADD PRIMARY KEY (`idContactoProveedor`);

--
-- Indices de la tabla `senorita`
--
ALTER TABLE `senorita`
  ADD PRIMARY KEY (`idSenorita`);

--
-- Indices de la tabla `senoritaCategoria`
--
ALTER TABLE `senoritaCategoria`
  ADD PRIMARY KEY (`idSenoritaCategoria`);

--
-- Indices de la tabla `senoritaServicio`
--
ALTER TABLE `senoritaServicio`
  ADD PRIMARY KEY (`idSenoritaServicio`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idServicio`);

--
-- Indices de la tabla `servicioCategoria`
--
ALTER TABLE `servicioCategoria`
  ADD PRIMARY KEY (`idServicioCategoria`);

--
-- Indices de la tabla `servicioDetalle`
--
ALTER TABLE `servicioDetalle`
  ADD PRIMARY KEY (`idServicioDetalle`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`idSucursal`);

--
-- Indices de la tabla `tramoRenta`
--
ALTER TABLE `tramoRenta`
  ADD PRIMARY KEY (`idTramoRenta`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- Indices de la tabla `usuarioPermisos`
--
ALTER TABLE `usuarioPermisos`
  ADD PRIMARY KEY (`idUsuarioPermiso`);

--
-- Indices de la tabla `usuarioRoles`
--
ALTER TABLE `usuarioRoles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `usuarioRolesDetalle`
--
ALTER TABLE `usuarioRolesDetalle`
  ADD PRIMARY KEY (`idRolDetalle`);

--
-- Indices de la tabla `zona`
--
ALTER TABLE `zona`
  ADD PRIMARY KEY (`idZona`);

--
-- Indices de la tabla `zonaMesa`
--
ALTER TABLE `zonaMesa`
  ADD PRIMARY KEY (`idZonaMesa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activoFijo`
--
ALTER TABLE `activoFijo`
  MODIFY `idActivoFijo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `baseDatos`
--
ALTER TABLE `baseDatos`
  MODIFY `idBaseDatos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `idCaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cajaDocumento`
--
ALTER TABLE `cajaDocumento`
  MODIFY `idCajaDocumento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `cajaDocumentoHistorial`
--
ALTER TABLE `cajaDocumentoHistorial`
  MODIFY `idCajaDocumentoHistorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajaMovimiento`
--
ALTER TABLE `cajaMovimiento`
  MODIFY `idCajaMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `idCargo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoriaActivoFijo`
--
ALTER TABLE `categoriaActivoFijo`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clienteCategoria`
--
ALTER TABLE `clienteCategoria`
  MODIFY `idClienteCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `idConfiguracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `contrato`
--
ALTER TABLE `contrato`
  MODIFY `idContrato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratoClausula`
--
ALTER TABLE `contratoClausula`
  MODIFY `idContratoClausula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratoTipo`
--
ALTER TABLE `contratoTipo`
  MODIFY `idContratoTipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratoTipoClausula`
--
ALTER TABLE `contratoTipoClausula`
  MODIFY `idContratoTipoClausula` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `corteCaja`
--
ALTER TABLE `corteCaja`
  MODIFY `idCorteCaja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `corteHistorial`
--
ALTER TABLE `corteHistorial`
  MODIFY `idCorteHistorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `corteHistorialDocumento`
--
ALTER TABLE `corteHistorialDocumento`
  MODIFY `idCorteHistorialDocumento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `corteRevisionInsumo`
--
ALTER TABLE `corteRevisionInsumo`
  MODIFY `idRevisionInsumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `corteTurno`
--
ALTER TABLE `corteTurno`
  MODIFY `idTurno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cover`
--
ALTER TABLE `cover`
  MODIFY `idCover` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `damas`
--
ALTER TABLE `damas`
  MODIFY `idDama` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalleCompra`
--
ALTER TABLE `detalleCompra`
  MODIFY `idCompraDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `idDocumento` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `idEmpleado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleadoBono`
--
ALTER TABLE `empleadoBono`
  MODIFY `idEmpleadoBono` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleadoDescuento`
--
ALTER TABLE `empleadoDescuento`
  MODIFY `idEmpleadoDescuento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleadoDescuentoCuota`
--
ALTER TABLE `empleadoDescuentoCuota`
  MODIFY `idEmpleadoDescuentoCuota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleadoDescuentoDetalle`
--
ALTER TABLE `empleadoDescuentoDetalle`
  MODIFY `idEmpleadoDescuentoDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleadoInstitucionFinanciera`
--
ALTER TABLE `empleadoInstitucionFinanciera`
  MODIFY `idInstitucionFinanciera` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `idFactura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturaDetalle`
--
ALTER TABLE `facturaDetalle`
  MODIFY `idFacturaDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturaModificacion`
--
ALTER TABLE `facturaModificacion`
  MODIFY `idFacturaModificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `FE_Configuraciones`
--
ALTER TABLE `FE_Configuraciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `impresora`
--
ALTER TABLE `impresora`
  MODIFY `idImpresora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `insumo`
--
ALTER TABLE `insumo`
  MODIFY `idInsumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoAjuste`
--
ALTER TABLE `insumoAjuste`
  MODIFY `idInsumoAjuste` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoCategoria`
--
ALTER TABLE `insumoCategoria`
  MODIFY `idInsumoCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoCosto`
--
ALTER TABLE `insumoCosto`
  MODIFY `idInsumoCosto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoLote`
--
ALTER TABLE `insumoLote`
  MODIFY `idInsumoLote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoMovimiento`
--
ALTER TABLE `insumoMovimiento`
  MODIFY `idInsumoMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoMovimientoDetalle`
--
ALTER TABLE `insumoMovimientoDetalle`
  MODIFY `idDetalleInsumoMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoPresentacion`
--
ALTER TABLE `insumoPresentacion`
  MODIFY `idInsumoPresentacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumoStock`
--
ALTER TABLE `insumoStock`
  MODIFY `idInsumoStock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `idMarca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `membrecia`
--
ALTER TABLE `membrecia`
  MODIFY `idMembrecia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `idMenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `menuModulos`
--
ALTER TABLE `menuModulos`
  MODIFY `idMenuModulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=233;

--
-- AUTO_INCREMENT de la tabla `modificador`
--
ALTER TABLE `modificador`
  MODIFY `idModificador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modificadorTipo`
--
ALTER TABLE `modificadorTipo`
  MODIFY `idModificadorTipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagoDetalle`
--
ALTER TABLE `pagoDetalle`
  MODIFY `idPagoDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idPago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagosDetalle`
--
ALTER TABLE `pagosDetalle`
  MODIFY `idPagoDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parqueo`
--
ALTER TABLE `parqueo`
  MODIFY `idParqueo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidoComentario`
--
ALTER TABLE `pedidoComentario`
  MODIFY `idPedidoComentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidoDetalle`
--
ALTER TABLE `pedidoDetalle`
  MODIFY `idPedidoDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidoSubDetalle`
--
ALTER TABLE `pedidoSubDetalle`
  MODIFY `idPedidoSubDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `periodoPlanilla`
--
ALTER TABLE `periodoPlanilla`
  MODIFY `idPeriodoPlanilla` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planilla`
--
ALTER TABLE `planilla`
  MODIFY `idPlanilla` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plantilla`
--
ALTER TABLE `plantilla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  MODIFY `idPresentacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoCategoria`
--
ALTER TABLE `productoCategoria`
  MODIFY `idProductoCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoCategoriaEspecifica`
--
ALTER TABLE `productoCategoriaEspecifica`
  MODIFY `idProductoCategoriaEspecifica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoInsumo`
--
ALTER TABLE `productoInsumo`
  MODIFY `idProductoInsumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoModificador`
--
ALTER TABLE `productoModificador`
  MODIFY `idProductoModificador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoModificadorDetalle`
--
ALTER TABLE `productoModificadorDetalle`
  MODIFY `idProductoModificadorDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productoModificadorInsumo`
--
ALTER TABLE `productoModificadorInsumo`
  MODIFY `idProductoModificadorInsumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proveedorContactos`
--
ALTER TABLE `proveedorContactos`
  MODIFY `idContactoProveedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `senorita`
--
ALTER TABLE `senorita`
  MODIFY `idSenorita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `senoritaCategoria`
--
ALTER TABLE `senoritaCategoria`
  MODIFY `idSenoritaCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `senoritaServicio`
--
ALTER TABLE `senoritaServicio`
  MODIFY `idSenoritaServicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicioCategoria`
--
ALTER TABLE `servicioCategoria`
  MODIFY `idServicioCategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicioDetalle`
--
ALTER TABLE `servicioDetalle`
  MODIFY `idServicioDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `idSucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tramoRenta`
--
ALTER TABLE `tramoRenta`
  MODIFY `idTramoRenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarioPermisos`
--
ALTER TABLE `usuarioPermisos`
  MODIFY `idUsuarioPermiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarioRoles`
--
ALTER TABLE `usuarioRoles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarioRolesDetalle`
--
ALTER TABLE `usuarioRolesDetalle`
  MODIFY `idRolDetalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT de la tabla `zona`
--
ALTER TABLE `zona`
  MODIFY `idZona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `zonaMesa`
--
ALTER TABLE `zonaMesa`
  MODIFY `idZonaMesa` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
