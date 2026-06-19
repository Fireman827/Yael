<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'inicio';
$route['404_override'] = 'inicio/NoEncontrado';
$route['translate_uri_dashes'] = FALSE;
// Rutas personalizadas controlador Inicio
$route['CerrarSesion'] = 'inicio/CerrarSesion';
$route['InicioSesion'] = 'inicio/InicioSesion';
// Rutas personalizadas controlador Usuario
$route['UsuarioMostrar'] = 'usuarios/UsuarioMostrar';
$route['UsuarioAgregar'] = 'usuarios/UsuarioAgregar';
$route['UsuarioEditar'] = 'usuarios/UsuarioEditar';
$route['UsuarioEditar/(:any)'] = 'usuarios/UsuarioEditar/$1';
$route['UsuarioEliminar'] = 'usuarios/UsuarioEliminar';
$route['UsuarioCambiarEstado'] = 'usuarios/UsuarioCambiarEstado';
$route['UsuarioPermisos/(:any)'] = 'usuarios/UsuarioPermisos/$1';
$route['UsuarioPermisos'] = 'usuarios/UsuarioPermisos';
// Rutas personalizadas controlador Roles
$route['RolMostrar'] = 'roles/RolMostrar';
$route['RolAgregar'] = 'roles/RolAgregar';
$route['RolEditar'] = 'roles/RolEditar';
$route['RolEditar/(:any)'] = 'roles/RolEditar/$1';
$route['RolEliminar'] = 'Roles/RolEliminar';
$route['RolCambiarEstado'] = 'roles/RolCambiarEstado';

// Rutas personalizadas controlador Cliente
$route['ClienteMostrar'] = 'clientes/ClienteMostrar';
$route['ClienteAgregar'] = 'clientes/ClienteAgregar';
$route['ClienteAgregar/(:any)'] = 'clientes/ClienteAgregar/$1';
$route['ClienteAgregarAvanzado'] = 'clientes/ClienteAgregarAvanzado';
$route['ClienteAgregarAvanzado/(:any)'] = 'clientes/ClienteAgregarAvanzado/$1';
$route['ClienteEditar'] = 'clientes/ClienteEditar';
$route['ClienteEditar/(:any)'] = 'clientes/ClienteEditar/$1';
$route['ClienteEditar/(:any)/(:any)'] = 'clientes/ClienteEditar/$1/$1';
$route['ClienteEditarAvanzado'] = 'clientes/ClienteEditarAvanzado';
$route['ClienteEditarAvanzado/(:any)'] = 'clientes/ClienteEditarAvanzado/$1';
$route['ClienteEditarAvanzado/(:any)/(:any)'] = 'clientes/ClienteEditarAvanzado/$1/$1';
$route['ClienteEliminar'] = 'clientes/ClienteEliminar';
$route['ClienteCambiarEstado'] = 'clientes/ClienteCambiarEstado';
//$route['ClientePermisos/(:any)'] = 'clientes/ClientePermisos/$1';
//$route['ClientePermisos'] = 'clientes/ClientePermisos';
$route['ClienteMunicipios'] = 'clientes/ClienteMunicipios';

// Rutas personalizadas controlador Proveedor
$route['ProveedorMostrar'] = 'proveedores/ProveedorMostrar';
$route['ProveedorAgregar'] = 'proveedores/ProveedorAgregar';
$route['ProveedorAgregarAvanzado'] = 'proveedores/ProveedorAgregarAvanzado';
$route['ProveedorEditar'] = 'proveedores/ProveedorEditar';
$route['ProveedorEditarAvanzado'] = 'proveedores/ProveedorEditarAvanzado';
$route['ProveedorEditar/(:any)'] = 'proveedores/ProveedorEditar/$1';
$route['ProveedorEditarAvanzado/(:any)'] = 'proveedores/ProveedorEditarAvanzado/$1';
$route['ProveedorEliminar'] = 'proveedores/ProveedorEliminar';
$route['ProveedorCambiarEstado'] = 'proveedores/ProveedorCambiarEstado';
//$route['ProveedorPermisos/(:any)'] = 'proveedores/ProveedorPermisos/$1';
//$route['ProveedorPermisos'] = 'proveedores/ProveedorPermisos';
$route['ProveedorMunicipios'] = 'proveedores/ProveedorMunicipios';

//Rutas personalizadas controlador Productos
$route['ProductoMostrar'] = 'Productos/ProductoMostrar';
$route['ProductoAgregar'] = 'productos/ProductoAgregar';
$route['ProductoAgregarModificador'] = 'Productos/ProductoAgregarModificador';
$route['ProductoAgregarInsumoGeneral'] = 'Productos/ProductoAgregarInsumoGeneral';
$route['ProductoEditar'] = 'productos/ProductoEditar';
$route['ProductoEditar/(:any)'] = 'productos/ProductoEditar/$1';
$route['ProductoEditarModificador'] = 'productos/ProductoEditarModificador';
$route['ProductoEditarModificador/(:any)'] = 'productos/ProductoEditarModificador/$1';
$route['ProductoEditarInsumoGeneral'] = 'Productos/ProductoEditarInsumoGeneral';
$route['ProductoEditarInsumoGeneral/(:any)'] = 'Productos/ProductoEditarInsumoGeneral/$1';
$route['ProductoCambiarEstado'] = 'Productos/ProductoCambiarEstado';
$route['ProductoEliminar'] = 'Productos/ProductoEliminar';
$route['ProductoListarCategoria'] = 'Productos/ProductoListarCategoria';
$route['ProductoListarModificador'] = 'Productos/ProductoListarModificador';
$route['ProductoListarModificadorTipo'] = 'Productos/ProductoListarModificadorTipo';
$route['ProductoListarModificadorDetalle'] = 'Productos/ProductoListarModificadorDetalle';
$route['ProductoListarInsumoGeneral'] = 'Productos/ProductoListarInsumoGeneral';
$route['ProductoListarInsumosPresentacion'] = 'Productos/ProductoListarInsumosPresentacion';
$route['ProductoAutocompletarInsumo'] = 'Productos/ProductoAutocompletarInsumo';
$route['ProductoVerificarInsumosModificador'] = 'Productos/ProductoVerificarInsumosModificador';

//Rutas personalizadas controlador ProductosCategoria
$route['ProductosCategoriaMostrar'] = 'ProductosCategoria/ProductosCategoriaMostrar';
$route['ProductosCategoriaAgregar'] = 'productosCategoria/ProductosCategoriaAgregar';
$route['ProductosCategoriaEditar'] = 'productosCategoria/ProductosCategoriaEditar';
$route['ProductosCategoriaEditar/(:any)'] = 'productosCategoria/ProductosCategoriaEditar/$1';
$route['ProductosCategoriaCambiarEstado'] = 'ProductosCategoria/ProductosCategoriaCambiarEstado';
$route['ProductosCategoriaEliminar'] = 'ProductosCategoria/ProductosCategoriaEliminar';

//Rutas personalizadas controlador Modificadores
$route['ModificadoresMostrar'] = 'Modificadores/ModificadoresMostrar';
$route['ModificadoresAgregar'] = 'modificadores/ModificadoresAgregar';
$route['ModificadoresEditar'] = 'modificadores/ModificadoresEditar';
$route['ModificadoresEditar/(:any)'] = 'modificadores/ModificadoresEditar/$1';
$route['ModificadoresCambiarEstado'] = 'Modificadores/ModificadoresCambiarEstado';
$route['ModificadoresEliminar'] = 'Modificadores/ModificadoresEliminar';
$route['ModificadoresAutocomplete'] = 'Modificadores/ModificadoresAutocomplete';
$route['ModificadoresTraerProductoCategoria'] = 'Modificadores/ModificadoresTraerProductoCategoria';

//Rutas personalizadas controlador Modificadores Tipo
$route['ModificadoresTipoMostrar'] = 'ModificadoresTipo/ModificadoresTipoMostrar';
$route['ModificadoresTipoCambiarEstado'] = 'ModificadoresTipo/ModificadoresTipoCambiarEstado';
$route['ModificadoresTipoAgregar'] = 'ModificadoresTipo/ModificadoresTipoAgregar';
$route['ModificadoresTipoEditar'] = 'ModificadoresTipo/ModificadoresTipoEditar';
$route['ModificadoresTipoEditar/(:any)'] = 'ModificadoresTipo/ModificadoresTipoEditar/$1';
$route['ModificadoresTipoEliminar'] = 'ModificadoresTipo/ModificadoresTipoEliminar';

//Rutas personalizadas controlador Zonas
$route['ZonasMostrar'] = 'Zonas/ZonasMostrar';
$route['ZonasAgregar'] = 'Zonas/ZonasAgregar';
$route['ZonasEditar'] = 'Zonas/ZonasEditar';
$route['ZonasEditar/(:any)'] = 'Zonas/ZonasEditar/$1';
$route['ZonasCambiarEstado'] = 'Zonas/ZonasCambiarEstado';
$route['ZonasEliminar'] = 'Zonas/ZonasEliminar';

$route['ZonasMesas'] = 'Zonas/ZonasMesas';
$route['ZonasMesas/(:any)'] = 'Zonas/ZonasMesas/$1';
$route['ZonasMesasAgregar'] = 'Zonas/ZonasMesasAgregar';
$route['ZonasMesasEditar'] = 'Zonas/ZonasMesasEditar';
$route['ZonasMesasCambiarEstado'] = 'Zonas/ZonasMesasCambiarEstado';
$route['ZonasMesasEliminar'] = 'Zonas/ZonasMesasEliminar';
$route['ZonasMesasTrasladar'] = 'Zonas/ZonasMesasTrasladar';
$route['ZonasMesasNuevoNombre'] = 'Zonas/ZonasMesasNuevoNombre';

//Rutas personalizadas controlador Cliente Categoria
$route['ClientesCategoriaMostrar'] = 'ClientesCategoria/ClientesCategoriaMostrar';
$route['ClientesCategoriaAgregar'] = 'ClientesCategoria/ClientesCategoriaAgregar';
$route['ClientesCategoriaEditar'] = 'ClientesCategoria/ClientesCategoriaEditar';
$route['ClientesCategoriaEditar/(:any)'] = 'ClientesCategoria/ClientesCategoriaEditar/$1';
$route['ClientesCategoriaCambiarEstado'] = 'ClientesCategoria/ClientesCategoriaCambiarEstado';
$route['ClientesCategoriaEliminar'] = 'ClientesCategoria/ClientesCategoriaEliminar';

//Rutas personalizadas controlador Impresora
$route['ImpresorasMostrar'] = 'Impresoras/ImpresorasMostrar';
$route['ImpresorasAgregar'] = 'Impresoras/ImpresorasAgregar';
$route['ImpresorasEditar'] = 'Impresoras/ImpresorasEditar';
$route['ImpresorasEditar/(:any)'] = 'Impresoras/ImpresorasEditar/$1';
$route['ImpresorasCambiarEstado'] = 'Impresoras/ImpresorasCambiarEstado';
$route['ImpresorasEliminar'] = 'Impresoras/ImpresorasEliminar';
$route['ImpresorasTest'] = 'Impresoras/ImpresorasTest';

//Rutas personalizadas controlador Presentación
$route['PresentacionesMostrar'] = 'Presentaciones/PresentacionesMostrar';
$route['PresentacionesAgregar'] = 'Presentaciones/PresentacionesAgregar';
$route['PresentacionesEditar'] = 'Presentaciones/PresentacionesEditar';
$route['PresentacionesEditar/(:any)'] = 'Presentaciones/PresentacionesEditar/$1';
$route['PresentacionesCambiarEstado'] = 'Presentaciones/PresentacionesCambiarEstado';
$route['PresentacionesEliminar'] = 'Presentaciones/PresentacionesEliminar';

//Rutas personalizadas controlador Insumos
$route['InsumosMostrar'] = 'Insumos/InsumosMostrar';
$route['InsumosAgregar'] = 'insumos/InsumosAgregar';
$route['InsumosEditar'] = 'insumos/InsumosEditar';
$route['InsumosEditar/(:any)'] = 'insumos/InsumosEditar/$1';
$route['InsumosCambiarEstado'] = 'Insumos/InsumosCambiarEstado';
$route['InsumosEliminar'] = 'Insumos/InsumosEliminar';
$route['InsumosPresentaciones'] = 'Insumos/InsumosPresentaciones';
$route['InsumosVerificarBorrado'] = 'Insumos/InsumosVerificarBorrado';

//Rutas personalizadas controlador Insumos Categoria
$route['InsumosCategoriaMostrar'] = 'InsumosCategoria/InsumosCategoriaMostrar';
$route['InsumosCategoriaAgregar'] = 'insumosCategoria/InsumosCategoriaAgregar';
$route['InsumosCategoriaEditar'] = 'insumosCategoria/InsumosCategoriaEditar';
$route['InsumosCategoriaEditar/(:any)'] = 'insumosCategoria/InsumosCategoriaEditar/$1';
$route['InsumosCategoriaCambiarEstado'] = 'InsumosCategoria/InsumosCategoriaCambiarEstado';
$route['InsumosCategoriaEliminar'] = 'InsumosCategoria/InsumosCategoriaEliminar';

//Rutas personalizadas controlador Caja
$route['CajasMostrar'] = 'Cajas/CajasMostrar';
$route['CajasAgregar'] = 'Cajas/CajasAgregar';
$route['CajasEditar'] = 'Cajas/CajasEditar';
$route['CajasEditar/(:any)'] = 'Cajas/CajasEditar/$1';
$route['CajasCambiarEstado'] = 'Cajas/CajasCambiarEstado';
$route['CajasEliminar'] = 'Cajas/CajasEliminar';
$route['CajasDocumentoEliminar'] = 'Cajas/CajasDocumentoEliminar';
$route['CajasDocumento'] = 'Cajas/CajasDocumento';
$route['CajasDocumentoCambiarEstado'] = 'Cajas/CajasDocumentoCambiarEstado';
$route['CajasDocumentoEditar'] = 'Cajas/CajasDocumentoEditar';
$route['CajasDocumentoEditar/(:any)'] = 'Cajas/CajasDocumentoEditar/$1';

//Rutas personalizadas para Activos fijos
$route['ActivoFijoMostrar'] = 'ActivoFijo/ActivoFijoMostrar';
$route['ActivoFijoAgregar'] = 'ActivoFijo/ActivoFijoAgregar';
$route['ActivoFijoEditar/(:any)'] = 'ActivoFijo/ActivoFijoEditar/$1';
$route['ActivoFijoEditar'] = 'ActivoFijo/ActivoFijoEditar';
$route['ActivoFijo/(:any)'] = 'ActivoFijo/ActivoFijoImprimir/$1';
$route['ActivoFijoDepreciacion/(:any)'] = 'ActivoFijo/ActivoFijoDepreciacion/$1';

//Rutas personalizadas para controlador Cargo
$route['CargosMostrar'] = 'Cargos/CargosMostrar';
$route['CargosAgregar'] = 'Cargos/CargosAgregar';
$route['CargosEditar'] = 'Cargos/CargosEditar';
$route['CargosEditar/(:any)'] = 'Cargos/CargosEditar/$1';
$route['CargosCambiarEstado'] = 'Cargos/CargosCambiarEstado';
$route['CargosEliminar'] = 'Cargos/CargosEliminar';

//Rutas personalizadas para Membrecia
$route['MembreciaMostrar'] = 'Membrecia/MembreciaMostrar';
$route['MembreciaAgregar'] = 'Membrecia/MembreciaAgregar';
$route['MembreciaEditar'] = 'Membrecia/MembreciaEditar';
$route['MembreciaEditar/(:any)'] = 'Membrecia/MembreciaEditar/$1';
$route['MembreciaPin'] = 'Membrecia/MembreciaPin';

//Rutas personalizadas para Touch
$route['TraerProductoCategoria'] = 'Touch/TraerProductoCategoria';
$route['TraerProductoModificadores'] = 'Touch/TraerProductoModificadores';
$route['TraerProductoModificadoresDetalle'] = 'Touch/TraerProductoModificadoresDetalle';
$route['TraerProductoModificadoresDetallePrecio'] = 'Touch/TraerProductoModificadoresDetallePrecio';
$route['TraerServicioCategoria'] = 'Touch/TraerServicioCategoria';
$route['TraerCaja'] = 'Touch/TraerCaja';
$route['TraerCorrelativo/(:any)'] = 'Touch/TraerCorrelativo/$1';
$route['TraerMesasZona'] = 'Touch/TraerMesasZona';
$route['TraerSenoritaCategoria'] = 'Touch/TraerSenoritaCategoria';
$route['TraerSenoritaOption'] = 'Touch/TraerSenoritaOption';
$route['PagarServicio'] = 'Touch/PagarServicio';
$route['PagarProducto'] = 'Touch/PagarProducto';
$route['AbrirCuenta'] = 'Touch/AbrirCuenta';
$route['AgregarACuenta'] = 'Touch/AgregarACuenta';
$route['FinalizarCuenta'] = 'Touch/FinalizarCuenta';
$route['RealizarMovimientoCaja'] = 'Touch/RealizarMovimientoCaja';
$route['ActualizarCuentas'] = 'Touch/ActualizarCuentas';
$route['ActualizarCuentasCanceladas'] = 'Touch/ActualizarCuentasCanceladas';
$route['ActualizarCuentasLocal'] = 'Touch/ActualizarCuentasLocal';
$route['AnularCuenta'] = 'Touch/AnularCuenta';
$route['DescargarInsumosPedido'] = 'Touch/DescargarInsumosPedido';
$route['VerDetalleCuenta'] = 'Touch/VerDetalleCuenta';
$route['DividirCuenta'] = 'Touch/DividirCuenta';
$route['UnirCuenta'] = 'Touch/UnirCuenta';
$route['CambiarEstadoDetalleCuenta'] = 'Touch/CambiarEstadoDetalleCuenta';
$route['VerDetalleCuenta/(:any)'] = 'Touch/VerDetalleCuenta/$1';
$route['ClienteAutocomplete'] = 'Touch/ClienteAutocomplete';
$route['ValidarPermiso'] = 'Touch/ValidarPermiso';
$route['CambioMesa'] = 'Touch/CambioMesa';

/////////////////////////////////////////////////
/////////////////////////////////////////////////

//Rutas personalizadas controlador Cocina
$route['OrdenesCocina'] = 'Cocina/OrdenesCocina';
$route['VerificarCocina'] = 'Cocina/VerificarCocina';
$route['DetalleOrden'] = 'Cocina/DetalleOrden';
$route['FinalizarPlato'] = 'Cocina/FinalizarPlato';


//Rutas personalizadas controlador Tipos de Contrato
$route['ContratosMostrar'] = 'Contratos/ContratosMostrar';
$route['ContratosAgregar'] = 'Contratos/ContratosAgregar';
$route['ContratosEditar'] = 'Contratos/ContratosEditar';
$route['ContratosEditar/(:any)'] = 'Contratos/ContratosEditar/$1';
$route['ContratosCambiarEstado'] = 'Contratos/ContratosCambiarEstado';
$route['ContratosEliminar'] = 'Contratos/ContratosEliminar';
$route['ContratosAutocompleteEmpleado'] = 'Contratos/ContratosAutocompleteEmpleado';
$route['ContratosPdf/(:any)'] = 'Contratos/ContratosPdf/$1';

//Rutas personalizadas controlador Tipos de Contrato
$route['ContratosTipoMostrar'] = 'ContratosTipo/ContratosTipoMostrar';
$route['ContratosTipoAgregar'] = 'ContratosTipo/ContratosTipoAgregar';
$route['ContratosTipoEditar'] = 'ContratosTipo/ContratosTipoEditar';
$route['ContratosTipoEditar/(:any)'] = 'ContratosTipo/ContratosTipoEditar/$1';
$route['ContratosTipoCambiarEstado'] = 'ContratosTipo/ContratosTipoCambiarEstado';
$route['ContratosTipoEliminar'] = 'ContratosTipo/ContratosTipoEliminar';
$route['ContratosTipoClausulaEliminar'] = 'ContratosTipo/ContratosTipoClausulaEliminar';
$route['ContratosTipoClausula'] = 'ContratosTipo/ContratosTipoClausula';

//Rutas personalizadas controlador Clausulas de Contrato
$route['ContratosClausulaMostrar'] = 'ContratosClausula/ContratosClausulaMostrar';
$route['ContratosClausulaAgregar'] = 'ContratosClausula/ContratosClausulaAgregar';
$route['ContratosClausulaEditar'] = 'ContratosClausula/ContratosClausulaEditar';
$route['ContratosClausulaEditar/(:any)'] = 'ContratosClausula/ContratosClausulaEditar/$1';
$route['ContratosClausulaCambiarEstado'] = 'ContratosClausula/ContratosClausulaCambiarEstado';
$route['ContratosClausulaEliminar'] = 'ContratosClausula/ContratosClausulaEliminar';

//Rutas personalizadas controlador Pago
$route['PagosMostrar'] = 'Pagos/PagosMostrar';
$route['PagosAgregar'] = 'Pagos/PagosAgregar';
$route['PagosEditar'] = 'Pagos/PagosEditar';
$route['PagosEditar/(:any)'] = 'Pagos/PagosEditar/$1';
$route['PagosCambiarEstado'] = 'Pagos/PagosCambiarEstado';
$route['PagosEliminar'] = 'Pagos/PagosEliminar';
$route['PagosDetalleEliminar'] = 'Pagos/PagosDetalleEliminar';
$route['PagosDetalleCambiarEstado'] = 'Pagos/PagosDetalleCambiarEstado';
$route['PagosDetalleEditar'] = 'Pagos/PagosDetalleEditar';
$route['PagosDetalleEditar/(:any)'] = 'Pagos/PagosDetalleEditar/$1';

//Rutas personalizadas para Parqueo
$route['ParqueoMostrar'] = 'Parqueo/ParqueoMostrar';
$route['ParqueoAgregar'] = 'Parqueo/ParqueoAgregar';
$route['ParqueoEditar'] = 'Parqueo/ParqueoEditar';
$route['ParqueoEditar/(:any)'] = 'Parqueo/ParqueoEditar/$1';
$route['ParqueoCobro'] = 'Parqueo/ParqueoCobro';
$route['ParqueoCobro/(:any)'] = 'Parqueo/ParqueoCobro/$1';
$route['ParqueoImprimir'] = 'Parqueo/ParqueoImprimir';
$route['ParqueoImprimir/(:any)'] = 'Parqueo/ParqueoImprimir/$1';
$route['ParqueoImprimirTicket'] = '../../imprimir/printparqueo';

//Rutas personalizadas controlador Institución Financiera
$route['PlanillasMostrar'] = 'Planillas/PlanillasMostrar';
$route['PlanillasGenerar'] = 'Planillas/PlanillasGenerar';
$route['PlanillasDetalle'] = 'Planillas/PlanillasDetalle';
$route['PlanillasDetalle/(:any)'] = 'Planillas/PlanillasDetalle/$1';
$route['PlanillasDetalleMostrar'] = 'Planillas/PlanillasDetalleMostrar';
$route['PlanillasDetalleMostrar/(:any)'] = 'Planillas/PlanillasDetalleMostrar/$1';
$route['PlanillasImprimir'] = 'Planillas/PlanillasImprimir';
$route['PlanillasImprimir/(:any)'] = 'Planillas/PlanillasImprimir/$1';
$route['PlanillasBoletaImprimir'] = 'Planillas/PlanillasBoletaImprimir';
$route['PlanillasBoletaImprimir/(:any)'] = 'Planillas/PlanillasBoletaImprimir/$1';
$route['PlanillasBoletasImprimir'] = 'Planillas/PlanillasBoletasImprimir';
$route['PlanillasBoletasImprimir/(:any)'] = 'Planillas/PlanillasBoletasImprimir/$1';
$route['PlanillasCambiarEstado'] = 'Planillas/PlanillasCambiarEstado';
$route['PlanillasEliminar'] = 'Planillas/PlanillasEliminar';

//Rutas personalizadas para controlador Pago
$route['TramosRentaMostrar'] = 'TramosRenta/TramosRentaMostrar';
$route['TramosRentaAgregar'] = 'TramosRenta/TramosRentaAgregar';
$route['TramosRentaEditar'] = 'TramosRenta/TramosRentaEditar';
$route['TramosRentaEditar/(:any)'] = 'TramosRenta/TramosRentaEditar/$1';
$route['TramosRentaCambiarEstado'] = 'TramosRenta/TramosRentaCambiarEstado';
$route['TramosRentaEliminar'] = 'TramosRenta/TramosRentaEliminar';

//Rutas personalizadas para controlador Prestaciones Configurar
$route['PrestacionesConfigurarEditar'] = 'PrestacionesConfigurar/PrestacionesConfigurarEditar';
$route['PrestacionesConfigurarEditar/(:any)'] = 'PrestacionesConfigurar/PrestacionesConfigurarEditar/$1';

//Rutas personalizadas para controlador Periodos de Planilla
$route['PeriodosPlanillaMostrar'] = 'PeriodosPlanilla/PeriodosPlanillaMostrar';
$route['PeriodosPlanillaAgregar'] = 'PeriodosPlanilla/PeriodosPlanillaAgregar';
$route['PeriodosPlanillaEditar'] = 'PeriodosPlanilla/PeriodosPlanillaEditar';
$route['PeriodosPlanillaEditar/(:any)'] = 'PeriodosPlanilla/PeriodosPlanillaEditar/$1';
$route['PeriodosPlanillaCambiarEstado'] = 'PeriodosPlanilla/PeriodosPlanillaCambiarEstado';
$route['PeriodosPlanillaEliminar'] = 'PeriodosPlanilla/PeriodosPlanillaEliminar';
$route['PeriodosPlanillaVigente'] = 'PeriodosPlanilla/PeriodosPlanillaVigente';

//Rutas personalizadas para Senorita
$route['SenoritaMostrar'] = 'Senorita/SenoritaMostrar';
$route['SenoritaAgregar'] = 'senorita/SenoritaAgregar';
$route['SenoritaEditar'] = 'senorita/SenoritaEditar';
$route['SenoritaEditar/(:any)'] = 'senorita/SenoritaEditar/$1';
$route['SenoritaCambiarEstado'] = 'Senorita/SenoritaCambiarEstado';
$route['SenoritaEliminar'] = 'Senorita/SenoritaEliminar';

//Rutas personalizadas para Senorita Categoria
$route['SenoritaCategoriaMostrar'] = 'SenoritaCategoria/SenoritaCategoriaMostrar';
$route['SenoritaCategoriaAgregar'] = 'senoritaCategoria/senoritaCategoriaAgregar';
$route['SenoritaCategoriaEditar'] = 'senoritaCategoria/senoritaCategoriaEditar';
$route['SenoritaCategoriaEditar/(:any)'] = 'SenoritaCategoria/SenoritaCategoriaEditar/$1';
$route['SenoritaCategoriaCambiarEstado'] = 'SenoritaCategoria/senoritaCategoriaCambiarEstado';
$route['SenoritaCategoriaEliminar'] = 'SenoritaCategoria/senoritaCategoriaEliminar';

//Rutas personalizadas para Servicio Categoria
$route['ServicioCategoriaMostrar'] = 'ServicioCategoria/ServicioCategoriaMostrar';
$route['ServicioCategoriaAgregar'] = 'servicioCategoria/servicioCategoriaAgregar';
$route['ServicioCategoriaEditar'] = 'servicioCategoria/servicioCategoriaEditar';
$route['ServicioCategoriaEditar/(:any)'] = 'ServicioCategoria/ServicioCategoriaEditar/$1';
$route['ServicioCategoriaCambiarEstado'] = 'ServicioCategoria/servicioCategoriaCambiarEstado';
$route['ServicioCategoriaEliminar'] = 'ServicioCategoria/servicioCategoriaEliminar';

//Rutas personalizadas para Servicio Categoria
$route['ServicioMostrar'] = 'Servicios/ServicioMostrar';
$route['ServicioAgregar'] = 'servicios/ServicioAgregar';
$route['ServicioEditar'] = 'servicios/ServicioEditar';
$route['ServicioEditar/(:any)'] = 'servicios/ServicioEditar/$1';
$route['ServicioCambiarEstado'] = 'Servicios/servicioCambiarEstado';
$route['ServicioEliminar'] = 'Servicios/servicioEliminar';

//Rutas personalizadas controlador Empleado
$route['EmpleadosMostrar'] = 'Empleados/EmpleadosMostrar';
$route['EmpleadosAgregar'] = 'Empleados/EmpleadosAgregar';
$route['EmpleadosAgregarRapido'] = 'Empleados/EmpleadosAgregarRapido';
$route['EmpleadosEditar'] = 'Empleados/EmpleadosEditar';
$route['EmpleadosEditar/(:any)'] = 'Empleados/EmpleadosEditar/$1';
$route['EmpleadosEditarRapido'] = 'Empleados/EmpleadosEditarRapido';
$route['EmpleadosEditarRapido/(:any)'] = 'Empleados/EmpleadosEditarRapido/$1';
$route['EmpleadosCambiarEstado'] = 'Empleados/EmpleadosCambiarEstado';
$route['EmpleadosEliminar'] = 'Empleados/EmpleadosEliminar';

//Rutas personalizadas controlador Bono
$route['EmpleadosBonoMostrar'] = 'EmpleadosBono/EmpleadosBonoMostrar';
$route['EmpleadosBonoAgregar'] = 'EmpleadosBono/EmpleadosBonoAgregar';
$route['EmpleadosBonoEditar'] = 'EmpleadosBono/EmpleadosBonoEditar';
$route['EmpleadosBonoEditar/(:any)'] = 'EmpleadosBono/EmpleadosBonoEditar/$1';
$route['EmpleadosBonoCambiarEstado'] = 'EmpleadosBono/EmpleadosBonoCambiarEstado';
$route['EmpleadosBonoEliminar'] = 'EmpleadosBono/EmpleadosBonoEliminar';
$route['EmpleadosBonoAutocompleteEmpleado'] = 'EmpleadosBono/EmpleadosBonoAutocompleteEmpleado';

//Rutas personalizadas controlador Empleados Descuento
$route['EmpleadosDescuentoMostrar'] = 'EmpleadosDescuento/EmpleadosDescuentoMostrar';
$route['EmpleadosDescuentoAgregar'] = 'EmpleadosDescuento/EmpleadosDescuentoAgregar';
$route['EmpleadosDescuentoEditar'] = 'EmpleadosDescuento/EmpleadosDescuentoEditar';
$route['EmpleadosDescuentoEditar/(:any)'] = 'EmpleadosDescuento/EmpleadosDescuentoEditar/$1';
$route['EmpleadosDescuentoCambiarEstado'] = 'EmpleadosDescuento/EmpleadosDescuentoCambiarEstado';
$route['EmpleadosDescuentoEliminar'] = 'EmpleadosDescuento/EmpleadosDescuentoEliminar';
$route['EmpleadosDescuentoAutocompleteEmpleado'] = 'EmpleadosDescuento/EmpleadosDescuentoAutocompleteEmpleado';

//Rutas personalizadas controlador Empleados Descuento Cuota
$route['EmpleadosDescuentoCuotaMostrar'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaMostrar';
$route['EmpleadosDescuentoCuotaAgregar'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaAgregar';
$route['EmpleadosDescuentoCuotaEditar'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaEditar';
$route['EmpleadosDescuentoCuotaEditar/(:any)'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaEditar/$1';
$route['EmpleadosDescuentoCuotaCambiarEstado'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaCambiarEstado';
$route['EmpleadosDescuentoCuotaEliminar'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaEliminar';
$route['EmpleadosDescuentoCuotaAutocompleteEmpleado'] = 'EmpleadosDescuentoCuota/EmpleadosDescuentoCuotaAutocompleteEmpleado';

//Rutas personalizadas controlador Institución Financiera
$route['InstitucionFinancieraMostrar'] = 'InstitucionFinanciera/InstitucionFinancieraMostrar';
$route['InstitucionFinancieraAgregar'] = 'InstitucionFinanciera/InstitucionFinancieraAgregar';
$route['InstitucionFinancieraEditar'] = 'InstitucionFinanciera/InstitucionFinancieraEditar';
$route['InstitucionFinancieraEditar/(:any)'] = 'InstitucionFinanciera/InstitucionFinancieraEditar/$1';
$route['InstitucionFinancieraCambiarEstado'] = 'InstitucionFinanciera/InstitucionFinancieraCambiarEstado';
$route['InstitucionFinancieraEliminar'] = 'InstitucionFinanciera/InstitucionFinancieraEliminar';

//Rutas personalizadas para Imprimir
$route['ImprimirTicketServicio/(:any)'] = 'Imprimir/ImprimirTicketServicio/$1';
$route['ImprimirTicketProducto/(:any)'] = 'Imprimir/ImprimirTicketProducto/$1';
$route['ImprimirFacturaProducto/(:any)'] = 'Imprimir/ImprimirFacturaProducto/$1';
$route['ImprimirTicketMovimientoCaja/(:any)'] = 'Imprimir/ImprimirTicketMovimientoCaja/$1';
$route['ImprimirCorte'] = 'Imprimir/ImprimirCorte';
$route['ImprimirCortes'] = 'Imprimir/ImprimirCortes';
$route['ImprimirCortes2'] = 'Imprimir/ImprimirCortes2';
$route['ImprimirCuenta'] = 'Imprimir/ImprimirCuenta';
$route['ImprimirComandaCocina'] = 'Imprimir/ImprimirComandaCocina';
$route['ImprimirCorteFiscal'] = 'Imprimir/ImprimirCorteFiscal';


//Rutas personalizadas controlador Configuración
$route['ConfiguracionesMostrar'] = 'Configuraciones/ConfiguracionesMostrar';
$route['ConfiguracionesAgregar'] = 'Configuraciones/ConfiguracionesAgregar';
$route['ConfiguracionesEditar'] = 'Configuraciones/ConfiguracionesEditar';
$route['ConfiguracionesEditar/(:any)'] = 'Configuraciones/ConfiguracionesEditar/$1';
$route['ConfiguracionesCambiarEstado'] = 'Configuraciones/ConfiguracionesCambiarEstado';
$route['ConfiguracionesEliminar'] = 'Configuraciones/ConfiguracionesEliminar';

//Rutas personalizadas para Movimiento
$route['MovimientosInventarioMostrar'] = 'MovimientosInventario/MovimientosInventarioMostrar';
$route['MovimientosInventarioAgregar'] = 'MovimientosInventario/MovimientosInventarioAgregar';
$route['MovimientosInventarioDescarga'] = 'MovimientosInventario/MovimientosInventarioDescarga';
$route['MovimientoInsumoAutocomplete'] = 'MovimientosInventario/MovimientoInsumoAutocomplete';
$route['MovimientoInsumoConsulta'] = 'MovimientosInventario/MovimientoInsumoConsulta';
$route['MovimientoInsumoAgregar'] = 'MovimientosInventario/MovimientoInsumoAgregar';
$route['MovimientoInsumoVer/(:any)'] = 'MovimientosInventario/MovimientoInsumoVer/$1';
$route['ConsultaStock'] = 'MovimientosInventario/ConsultaStock/';
$route['ConsultaStockVer/(:any)'] = 'MovimientosInventario/ConsultaStockVer/$1';
$route['ConsultaStockAjuste/(:any)'] = 'MovimientosInventario/ConsultaStockAjuste/$1';
$route['ConsultaStockAjuste'] = 'MovimientosInventario/ConsultaStockAjuste';
// $route['MovimientoInventarioEditar'] = 'MovimientoInventario/MovimientoInventarioEditar';
// $route['MovimientoInventarioEditar/(:any)'] = 'MovimientoInventario/MovimientoInventarioEditar/$1';
// $route['MovimientoInventarioPin'] = 'MovimientoInventario/MovimientoInventarioPin';


//Rutas personalizadas para Corte
$route['CorteMostrar'] = 'corte/CorteMostrar';
$route['CorteAgregar'] = 'Corte/CorteAgregar';
$route['CorteEditar'] = 'corte/CorteEditar';
$route['CorteEditar/(:any)'] = 'corte/CorteEditar/$1';
$route['CorteHistorial'] = 'corte/CorteHistorial';
$route['RealizarAperturar'] = 'corte/RealizarAperturar';

//Rutas personalizadas controlador Facturas
$route['FacturasMostrar'] = 'Facturas/FacturasMostrar';
$route['FacturasDoc/(:any)'] = 'Facturas/FacturasDoc/$1';
$route['FacturasVer/(:any)'] = 'Facturas/FacturasVer/$1';
//$route['ImpresorasEditar'] = 'Impresoras/ImpresorasEditar';
//$route['ImpresorasEditar/(:any)'] = 'Impresoras/ImpresorasEditar/$1';
$route['FacturasCambiarEstado'] = 'Facturas/FacturasCambiarEstado';
//$route['ImpresorasEliminar'] = 'Impresoras/ImpresorasEliminar';

//Rutas personalizadas para Corte
$route['ReporteVenta'] = 'Reportes/ReporteVenta';
$route['VerReporteVenta'] = 'Reportes/VerReporteVenta';
$route['ReporteVentaMesero'] = 'Reportes/ReporteVentaMesero';
$route['VerReporteVentaMesero'] = 'Reportes/VerReporteVentaMesero';
$route['ReporteVenta'] = 'Reportes/ReporteUtilidad';
$route['VerReporteUtilidad'] = 'Reportes/VerReporteUtilidad';
$route['ReporteVenta'] = 'Reportes/ReporteUtilidadProducto';
$route['VerReporteUtilidadProducto'] = 'Reportes/VerReporteUtilidadProducto';
$route['ReporteKardex'] = 'Reportes/ReporteKardex';
$route['VerReporteKardex'] = 'Reportes/VerReporteKardex';
$route['ReporteInventario'] = 'Reportes/ReporteInventario';
$route['VerReporteInventario'] = 'Reportes/VerReporteInventario';
$route['ReporteGeneralUtilidad'] = 'Reportes/ReporteGeneralUtilidad';
$route['VerReporteGeneralUtilidad'] = 'Reportes/VerReporteGeneralUtilidad';
$route['VerReporteCintaAuditoria'] = 'Reportes/VerReporteCintaAuditoria';
$route['ReporteDetallePedido'] = 'Reportes/ReporteDetallePedido';
$route['VerReporteDetallePedido'] = 'Reportes/VerReporteDetallePedido';
$route['VerReporteReposicion'] = 'Reportes/VerReporteReposicion';
$route['VerReporteVencimiento'] = 'Reportes/VerReporteVencimiento';
$route['ReporteAdvalorem'] = 'Reportes/ReporteAdvalorem';
$route['VerReporteAdvalorem'] = 'Reportes/VerReporteAdvalorem';
$route['ReporteMarca'] = 'Reportes/ReporteMarca';
$route['VerReporteMarca'] = 'Reportes/VerReporteMarca';
$route['ReporteCompra'] = 'Reportes/ReporteCompra';
$route['VerReporteCompra'] = 'Reportes/VerReporteCompra';
$route['ReporteConteo'] = 'Reportes/ReporteConteo';
$route['VerReporteConteo'] = 'Reportes/VerReporteConteo';
$route['ReporteVentaItem'] = 'Reportes/ReporteVentaItem';
$route['VerReporteVentaItem'] = 'Reportes/VerReporteVentaItem';

$route['ReporteRevisionInsumo/(:any)'] = 'Reportes/ReporteRevisionInsumo/$1';
$route['ReporteProductoAutocomplete'] = 'Reportes/ReporteProductoAutocomplete';
$route['ReporteInsumoAutocomplete'] = 'Reportes/ReporteInsumoAutocomplete';

$route['ExportarReporteVentaExcel'] = 'Reportes/ExportarReporteVentaExcel';
$route['ExportarReporteVentaExcelContador'] = 'Reportes/ExportarReporteVentaExcelContador';

//Rutas personalizadas para Movimiento de caja
$route['MovimientosCajaMostrar'] = 'MovimientosCaja/MovimientosCajaMostrar';
$route['MovimientosCajaIngreso'] = 'MovimientosCaja/MovimientosCajaIngreso';
$route['MovimientosCajaSalida'] = 'MovimientosCaja/MovimientosCajaSalida';
$route['MovimientosCajaEditar'] = 'MovimientosCaja/MovimientosCajaEditar';
$route['MovimientosCajaEditar/(:any)'] = 'MovimientosCaja/MovimientosCajaEditar/$1';
$route['MovimientosCajaBorrar'] = 'MovimientosCaja/MovimientosCajaBorrar';

//Rutas personalizadas para Corte Administracion
$route['CorteMostrar'] = 'corteAdmin/CorteMostrar';
$route['CorteAgregar'] = 'corteAdmin/CorteAgregar';
$route['AperturaCaja'] = 'corteAdmin/AperturaCaja';
$route['AperturaTurno/(:any)'] = 'corteAdmin/AperturaTurno/$1';
$route['AperturaTurno'] = 'corteAdmin/AperturaTurno';
$route['AperturaTurnoUsuario'] = 'corteAdmin/AperturaTurnoUsuario';
$route['CorteTraerApertura'] = 'corteAdmin/CorteTraerApertura';
$route['CorteTurnoDetalle/(:any)'] = 'corteAdmin/CorteTurnoDetalle/$1';
$route['RealizarCorte/(:any)/(:any)/(:any)'] = 'corteAdmin/RealizarCorte/$1/$2/$3';
$route['RealizarCierreTurno/(:any)/(:any)/(:any)'] = 'corteAdmin/RealizarCierreTurno/$1/$2/$3';
$route['RealizarCorte'] = 'corteAdmin/RealizarCorte';
$route['RealizarCierreTurno'] = 'corteAdmin/RealizarCierreTurno';
$route['TraerCajasSucursal'] = 'corteAdmin/TraerCajasSucursal';
$route['RevisionInventario/(:any)'] = 'corteAdmin/RevisionInventario/$1';
$route['RevisionInventario'] = 'corteAdmin/RevisionInventario';
$route['RevisionInventarioFinal/(:any)'] = 'corteAdmin/RevisionInventarioFinal/$1';
$route['RevisionInventarioFinal'] = 'corteAdmin/RevisionInventarioFinal';
$route['CorteReimprimir/(:any)'] = 'CorteAdmin/ReimprimirCorte/$1';
$route['AlertaCorte'] = 'corteAdmin/AlertaCorte';


//Rutas para Respaldos de Base de Datos
$route['RespaldoMostrar'] = 'Respaldos/RespaldoMostrar';
$route['RespaldoHacer'] = 'Respaldos/RespaldoHacer';
$route['RespaldoCargar'] = 'Respaldos/RespaldoCargar';
$route['RespaldoRestaurar'] = 'Respaldos/RespaldoRestaurar';
$route['RespaldoCambiarEstado'] = 'Respaldos/RespaldoCambiarEstado';
$route['RespaldoEliminar'] = 'Respaldos/RespaldoEliminar';


//Rutas para Marcas
$route['MarcaMostrar'] = 'Marcas/MarcaMostrar';
$route['MarcaEntrada'] = 'Marcas/MarcaEntrada';
$route['MarcaSalida'] = 'Marcas/MarcaSalida';


//Pagos
$route['PagosMostrar'] = 'Pago/PagoMostrar';
$route['PagoAgregar'] = 'Pago/PagoAgregar';
$route['PagoAgregarTransferencia'] = 'Pago/PagoAgregarTransferencia';
$route['PagoAgregarTarjeta'] = 'Pago/PagoAgregarTarjeta';
$route['PagoRecibo/(:any)'] = 'Pago/PagoRecibo/$1';


////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
$route['EnviarCorreo/(:any)/(:any)'] = 'Fe/EnviarCorreo/$1/$2';
$route['EnviarCorreoCompra/(:any)'] = 'Fe/EnviarCorreoCompra/$1';
$route['crearDTE/(:any)'] = 'Fe/crearDTE/$1';
$route['anularDTE/(:any)'] = 'Fe/anularDTE/$1';
$route['contingenciaDTE/(:any)'] = 'Fe/contingenciaDTE/$1';
$route['crearDTECompra/(:any)'] = 'Fe/crearDTECompra/$1';
$route['crearDTENota/(:any)'] = 'Fe/crearDTENota/$1';

$route['VentasPdf/(:any)'] = 'Facturas/VentasPdf/$1';
$route['VentasVer/(:any)'] = 'Facturas/VentasVer/$1';
$route['VentasRetransmitir'] = 'Facturas/VentasRetransmitir';
$route['VentasError/(:any)'] = 'Facturas/VentasError/$1';
$route['VentasContingencia/(:any)'] = 'Facturas/VentasContingencia/$1';
$route['VentasReenviar/(:any)'] = 'Facturas/VentasReenviar/$1';
$route['ImprimirTiket/(:any)'] = 'Imprimir/ImprimirTiket/$1';
$route['ImprimirVenta/(:any)'] = 'Imprimir/ImprimirVenta/$1';
$route['VentasDevolucion/(:any)'] = 'Facturas/VentasDevolucion/$1';

// ----------- COMPRAS -----------
$route['Compras'] = 'Compras/index';
$route['CompraMostrar'] = 'Compras/CompraMostrar';
$route['CompraAgregar'] = 'Compras/CompraAgregar';
$route['CompraGuardar'] = 'Compras/CompraGuardar';
$route['CompraVer/(:any)'] = 'Compras/CompraVer/$1';
$route['CompraImprimir/(:any)'] = 'Compras/CompraImprimir/$1';
$route['BuscarInsumos'] = 'Compras/BuscarInsumos';

// Pagos — borrado lógico (agregado en sesión de corrección)
$route['PagosBorrar'] = 'Pagos/PagosBorrar';

// Notificaciones AJAX
$route['Notificaciones/marcarLeidaAjax'] = 'Notificaciones/marcarLeidaAjax';
$route['Notificaciones/marcarTodasAjax'] = 'Notificaciones/marcarTodasAjax';
$route['Notificaciones/ajaxContador']    = 'Notificaciones/ajaxContador';

// ------------- Pedidos Online --------
// Storefront movido a html/pedidos/ (app CI3 separada)
// Solo se mantiene el endpoint de polling para cocina
$route['Online/VerificarOnlinePendientes']     = 'Online/VerificarOnlinePendientes';

// Panel Admin dentro del POS
$route['AdminOnline/ordenes']                  = 'AdminOnline/ordenes';
$route['AdminOnline/clientes']                 = 'AdminOnline/clientes';
$route['AdminOnline/configuracion']            = 'AdminOnline/configuracion';
$route['AdminOnline/cambiarEstado']            = 'AdminOnline/cambiarEstado';
$route['AdminOnline/editarCliente']            = 'AdminOnline/editarCliente';
$route['AdminOnline/enviarOtpAdmin']           = 'AdminOnline/enviarOtpAdmin';
$route['AdminOnline/guardarConfig']            = 'AdminOnline/guardarConfig';
$route['AdminOnline/subirQR']                  = 'AdminOnline/subirQR';
$route['AdminOnline/zonas']                    = 'AdminOnline/zonas';
$route['AdminOnline/listarZonas']              = 'AdminOnline/listarZonas';
$route['AdminOnline/guardarZona']              = 'AdminOnline/guardarZona';
$route['AdminOnline/eliminarZona']             = 'AdminOnline/eliminarZona';

