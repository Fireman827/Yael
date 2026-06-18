<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MovimientosInventario extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "insumoMovimiento";
	private $tablaInsumo = "insumo";
	private $controlador = "MovimientosInventario";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Movimientos Inventario";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-trash",
				"botones" => array(
					array(
						"icono"=> "fa fa-minus",
						'controlador' => $this->controlador,
						'url' => 'MovimientosInventarioDescarga',
						'txt' => 'Descarga de inventario',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
            			'id'=>'insumoMovimientosDescarga'
					),
					array(
						"icono"=> "fa fa-plus ",
						'controlador' => $this->controlador,
						'url' => 'MovimientosInventarioAgregar',
						'txt' => 'Carga de inventario',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
            			'id'=>'insumoMovimientosAgregar'
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Detalle"=>3,
					"Tipo"=>2,
					"Responsable"=>2,
					"Total"=>1,
					"Fecha"=>2,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/movimientosinventario.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}
	
	
	
	function MovimientosInventarioMostrar(){
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = $busquedaAreglo['value'];
		$col = 0;
		$ordenDireccion = "";
		if (!empty($order)){
			foreach ($order as $o){
				$col = $o['column'];
				$ordenDireccion = $o['dir'];
			}
		}
		if ($ordenDireccion != "asc" && $ordenDireccion != "desc"){
			$ordenDireccion = "desc";
		}
		//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		$columnasValidas = array(
			0 => 'insumoMovimiento.idInsumoMovimiento',
			1 => 'insumoMovimiento.tipoMovimientoInsumo',
			2 => 'insumoMovimiento.fechaHoraInsumoMovimiento',
			3 => 'insumoMovimiento.descripcionInsumoMovimiento',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else{
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$condicion = array('insumoMovimiento.estadoInsumoMovimiento !=' => "Borrado", 'insumoMovimiento.idSucursalInsumoMovimiento' => $sucursal);
    	$join = array(
			array(
				"tabla" => "usuario",
			"condicion" => "usuario.idUsuario = insumoMovimiento.idUsuarioInsumoMovimiento"
			),
		);
		$insumoMovimientos = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion, $join);
		//print_r($insumoMovimientos);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($insumoMovimientos != 0){
			$datosMostrar = array();
			foreach ($insumoMovimientos as $insumoMovimiento){


				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="insumoMovimientoVer";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Ver' idInsumoMovimiento=" .md5($insumoMovimiento->idInsumoMovimiento). " ><i class='fa fa-eye' ></i> Ver Detalle</a>";
				}

				$menuOpciones .= "
				</div>
				</div>";
				$fecha = date_create($insumoMovimiento->fechaHoraInsumoMovimiento);
				$fecha = date_format($fecha,"d-m-Y  h:i:s A");
				$datosMostrar[] = array(
					$insumoMovimiento->idInsumoMovimiento,
					$insumoMovimiento->descripcionInsumoMovimiento,
					$insumoMovimiento->categoriaInsumoMovimiento,
					$insumoMovimiento->nombreUsuario,
					$insumoMovimiento->totalInsumoMovimiento,
					$fecha,
					$menuOpciones,
				);
			}
			$totalinsumoMovimientos = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalinsumoMovimientos,
				"recordsFiltered" => $totalinsumoMovimientos,
				"data" => $datosMostrar
			);
		} else{
			$output = array(
				"draw" => $draw,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => 0
			);
		}
		echo json_encode($output);
		exit();
	}
	/*/ Funcion para hacer Carga de Inventario*/
	function MovimientosInventarioAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

		$id_sucursal = $this->session->idSucursal;
        $titulo = "Cargar Inventario";
		$condicion = array('idSucursalProveedor' => $id_sucursal);
		$ordenCampos = "";
		$proveedor = TraerDatos('proveedor', $condicion, $ordenCampos);
        // $clientes = TraerDatos('cliente');
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-trash",
            "controlador" => "MovimientosInventario",
            "proceso" => "Carga",
            "id_sucursal" => $id_sucursal,
            "proveedor" => $proveedor,
            // "pin" => $randomStrinsumoMovimientosing,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/movimientosinventario.js"
            ),
        );
        GblPlantilla("insumoMovimiento/ingresoInsumoMovimiento",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$datos = $this->input->post('datos');
				$fecha = $this->input->post('fecha1');
				$total_compras = $this->input->post('total_dineroh');
				$concepto=$this->input->post('concepto');
				$tipo=$this->input->post('tipo');
				$n_documento=$this->input->post('n_documento');
				$tipo_documento=$this->input->post('tipo_documento');
				if ($tipo == "Compra"){
					$correlativo = $n_documento;
				} elseif ($tipo == "Carga") {
					$tipo_documento = "CI";
					$correlativo = GblTraerConfiguracion("cargaCorrelativo");
				} else {
					$tipo_documento = "II";
					$correlativo = GblTraerConfiguracion("inventarioCorrelativo");
				}
				$hora = date("H:i:s");
				$fecha_movimiento = date("Y-m-d");
				$id_usuario = $this->session->idUsuario;
				$id_sucursal = $this->session->idSucursal;
				$proveedor = $this->input->post("proveedor");
				$actualizados = 0;

				$tabla_movimiento='insumoMovimiento';
				$form_data_movimiento = array(
					'categoriaInsumoMovimiento' => 'Carga',
					'tipoMovimientoInsumo' => $tipo,
					'descripcionInsumoMovimiento' => $concepto,
					'idProveedorInsumoMovimiento' => $proveedor,
					'fechaHoraInsumoMovimiento' => $fecha." ".$hora,
					'tipoDocumentoInsumoMovimiento' => $tipo_documento,
					'numeroDocumentoInsumoMovimiento' => $correlativo,
					'totalInsumoMovimiento' => $total_compras,
					'fechaRegistroInsumoMovimiento' => $fecha." ".$hora,
					'idUsuarioInsumoMovimiento' => $id_usuario,
					'idSucursalInsumoMovimiento' => $id_sucursal,
					"estadoInsumoMovimiento" => "Activo",
				);
				$error = false;
				IniciarTransaccion();
				$guardar = GuardarDatos($tabla_movimiento,$form_data_movimiento);
				if($guardar){
					if ($tipo == "Compra"){

					}
					elseif ($tipo == "Carga"){
						$condicionCorrelativo = array(
							'parametroConfiguracion' => "cargaCorrelativo",
						);
						$actualizarCorrelativo = ActualizarCorrelativo("configuraciones",$condicionCorrelativo,"valorConfiguracion",(1));
					} else {
						$condicionCorrelativo = array(
							'parametroConfiguracion' => "inventarioCorrelativo",
						);
						$actualizarCorrelativo = ActualizarCorrelativo("configuraciones",$condicionCorrelativo,"valorConfiguracion",(1));
					}

					$error = false;
					$idMovimiento =  $guardar;
					$d = explode("#",$datos);
					for($i = 0 ; $i < (count($d) - 1) ; $i++){
						$elemento = explode("|",$d[$i]);
						$datosDetalle = array(
							'idInsumoMovimiento' => $idMovimiento,
							'idInsumo' => $elemento[0],
							'cantidadInsumoMovimientoDetalle' => $elemento[4],
							'descripcionInsumoMovimientoDetalle' => $elemento[1],
							'costoInsumoMovimientoDetalle' => $elemento[2],
							'precioInsumoMovimientoDetalle' => $elemento[3],
							'idPresentacionInsumoMovimientoDetalle' => $elemento[7],
							'idUsuarioInsumoMovimientoDetalle' => $this->session->idUsuario,
							'estadoInsumoMovimientoDetalle' => 'Activo',
						);
						$guardarDetalle = GuardarDatos("insumoMovimientoDetalle",$datosDetalle);
						if($guardarDetalle){
							$cantidad = $elemento[4];
							$unidad = $elemento[8];
							$datosLote = array(
								'idInsumoMovimientoLote' => $idMovimiento,
								'idProductoInsumoLote' => $elemento[0],
								'cantidadInsumoLote' =>($cantidad * $unidad),
								'fechaVencimientoInsumoLote' => $elemento[6],
								'estadoInsumoLote' => 'Activo',
							);
							$guardarLote = GuardarDatos("insumoLote",$datosLote);
							if($guardarLote){
								//Calculo de Costo Promedio del Inventario
								$idSucursal = $this->session->idSucursal;
								$condicionStock = array(
									'idSucursalInsumoStock' => $idSucursal,
									'idInsumo' => $elemento[0],
								);
								$condicionInsumo =  array(
									"idInsumo" => $elemento[0],
								);
								// Valores Actuales
								$costoPromedio = TraerUnDato("insumo",$condicionInsumo)->costoPromedioInsumo;
								$stockActual=0;
								if(TraerUnDato("insumoStock",$condicionStock) !==  false){
									$stockActual = TraerUnDato("insumoStock",$condicionStock)->cantidadInsumoStock;
								}
								$valorAnterior = $costoPromedio * $stockActual;
								// Valores Nuevos
								$cantidad = $elemento[4];
								$unidad = $elemento[8];
								$costo = $elemento[2];
								$valorNuevo = $cantidad * $costo;
								// Nuevo Promedio
								$nuevoPromedio = ($valorAnterior + $valorNuevo) / ($stockActual + ($cantidad * $unidad));
								$actualizarPromedio = EditarDatos("insumo",array("costoPromedioInsumo" => $nuevoPromedio,"aleatorioInsumo"=>uniqid()),$condicionInsumo);

								if($actualizarPromedio){
									if(TraerDatos("insumoStock",$condicionStock) !== false){
										$guardarStock = ActualizarCorrelativo("insumoStock",$condicionStock,"cantidadInsumoStock",($cantidad * $unidad));
									} else {
										$guardarStock = GuardarDatos("insumoStock",array("cantidadInsumoStock" => ($cantidad * $unidad),"idInsumo" =>$elemento[0],"idSucursalInsumoStock" => $idSucursal));
									}
									if($guardarStock){
										$datosCosto = array(
											"idInsumo" =>$elemento[0],
											"costoPromedioInsumoCosto" => $nuevoPromedio
										);
										$historialCostoPromedio = GuardarDatos("insumoCosto",$datosCosto);
										if($historialCostoPromedio){
											$respuesta['codigo'] = 200;
											$error = false;
										}
										else{
											$respuesta['codigo'] = 505;
											$error = true;
											break;
										}
									}
									else{
										$respuesta['codigo'] = 504;
										$error = true;
										break;
									}
								}
								else{
									$respuesta['codigo'] = 503;
									$error = true;
									break;
								}
							}
							else{
								$respuesta['codigo'] = 502;
								$error = true;
								break;
							}
						}
						else{
							$respuesta['codigo'] = 501;
							$error = true;
							break;
						}
					}
				}
				else{
					$respuesta['codigo'] = 500;
					$error = true;
				}

				($error == true) ? DeshacerTransaccion() : EjecutarTransaccion();

				echo json_encode($respuesta);

			}
		}
	}
	function MovimientosInventarioDescarga(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

		$id_sucursal = $this->session->idSucursal;
        $titulo = "Descargar Inventario";
		$condicion = array('idSucursalProveedor' => $id_sucursal);
		$ordenCampos = "";
		$proveedor = TraerDatos('proveedor', $condicion, $ordenCampos);
        // $clientes = TraerDatos('cliente');
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-trash",
            "controlador" => "MovimientosInventario",
            "proceso" => "Descarga",
            "id_sucursal" => $id_sucursal,
            "proveedor" => $proveedor,
            // "pin" => $randomStrinsumoMovimientosing,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/movimientosinventario.js"
            ),
        );
        GblPlantilla("insumoMovimiento/descargaInsumoMovimiento",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$tipo_documento = "DI";
				$correlativo = GblTraerConfiguracion("descargaCorrelativo");
				$datos = $this->input->post('datos');
				$fecha = $this->input->post('fecha1');
				$total_compras = $this->input->post('total_dineroh');
				$concepto=$this->input->post('concepto');
				$tipo=$this->input->post('tipo');
				$hora=date("H:i:s");
				$id_usuario=$this->session->idUsuario;
				$id_sucursal=$this->session->idSucursal;

				$tabla_movimiento='insumoMovimiento';
				$form_data_movimiento = array(
					'categoriaInsumoMovimiento' => 'Descarga',
					'tipoMovimientoInsumo' => $tipo,
					'descripcionInsumoMovimiento' => $concepto,
					'fechaHoraInsumoMovimiento' => $fecha." ".$hora,
					'totalInsumoMovimiento' => $total_compras,
					'tipoDocumentoInsumoMovimiento' => $tipo_documento,
					'numeroDocumentoInsumoMovimiento' => $correlativo,
					'fechaRegistroInsumoMovimiento' => $fecha." ".$hora,
					'idUsuarioInsumoMovimiento' => $id_usuario,
					'idSucursalInsumoMovimiento' => $id_sucursal,
					"estadoInsumoMovimiento" => "Activo",
				);
				$error = false;
				IniciarTransaccion();
				$guardar = GuardarDatos($tabla_movimiento,$form_data_movimiento);
				if($guardar){
					$condicionCorrelativo = array(
						'parametroConfiguracion' => "descargaCorrelativo",
					);
					$actualizarCorrelativo = ActualizarCorrelativo("configuraciones",$condicionCorrelativo,"valorConfiguracion",(1));
					$error = false;
					$idMovimiento =  $guardar;
					$d = explode("#",$datos);
					for($i = 0 ; $i < (count($d) - 1) ; $i++){
						$elemento = explode("|",$d[$i]);
						$datosDetalle = array(
							'idInsumoMovimiento' => $idMovimiento,
							'idInsumo' => $elemento[0],
							'cantidadInsumoMovimientoDetalle' => $elemento[4],
							'descripcionInsumoMovimientoDetalle' => $elemento[1],
							'costoInsumoMovimientoDetalle' => $elemento[2],
							'precioInsumoMovimientoDetalle' => $elemento[3],
							'idPresentacionInsumoMovimientoDetalle' => $elemento[7],
							'idUsuarioInsumoMovimientoDetalle' => $this->session->idUsuario,
							'estadoInsumoMovimientoDetalle' => 'Activo',
						);
						$guardarDetalle = GuardarDatos("insumoMovimientoDetalle",$datosDetalle);
						if($guardarDetalle){
							$lotes = TraerDatos("insumoLote",array("idProductoInsumoLote" => $elemento[0],"cantidadInsumoLote >"=>"0"),"fechaRegistroInsumoLote ASC" );
							if($lotes){
								$cantidad = $elemento[4];
								$unidad = $elemento[8];
								$cantidad = $cantidad * $unidad;
								$loteActualizado = false;
								foreach($lotes as $l){
									$existencia = $l->cantidadInsumoLote;
									$descuento = ($cantidad >= $existencia) ? $existencia : $cantidad;
									$cantidad = $cantidad - $existencia;

									$actualizarLote = ActualizarCorrelativo("insumoLote",array("idInsumoLote" =>$l->idInsumoLote),"cantidadInsumoLote", -$descuento );
									if($actualizarLote){
										if($cantidad <= 0){
											$error = false;
											break 1;
										}
									}
									else{
										$respuesta['codigo'] = 504;
										$error = true;
										break 2;
									}
								}
							}
							if($error == false){
								$actualizarStock = ActualizarCorrelativo("insumoStock",array("idInsumo" =>$elemento[0]),"cantidadInsumoStock", -$elemento[4] );
								if($actualizarStock){
									$respuesta['codigo'] = 200;
									$error = false;
								}
								else{
									$respuesta['codigo'] = 505;
									$error = true;
									break;
								}
							}
							else{
								$respuesta['codigo'] = 503;
								$error = true;
								break;
							}
						}
						else{
							$respuesta['codigo'] = 501;
							$error = true;
							break;
						}
					}
				}
				else{
					$respuesta['codigo'] = 500;
					$error = true;
				}

				($error == true) ? DeshacerTransaccion() : EjecutarTransaccion();

				echo json_encode($respuesta);

			}
		}
	}
	function MovimientoInsumoAutocomplete(){
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
        } else {
			if($this->input->method(TRUE) == "POST"){
				$busquedaParametro = $this->input->post("query");

				$sucursal = $this->session->idSucursal;
				$condicionWhere = array('estadoInsumo!=' => 'Borrado');
				// $condicionWhere = array('idSucursalInsumo' => $sucursal,'estadoInsumo!=' => 'Borrado');
				$condicionLike = array(
					'nombreInsumo' => $busquedaParametro,
					// 'marcaInsumo' => $busquedaParametro,
					// 'nombreInsumoCategoria' => $busquedaParametro
				);
				$join=array(
					array(
						"tabla" => "insumoStock",
						"condicion" => "insumoStock.idInsumo = insumo.idInsumo",
						"tipo" => "inner",
						"campos" => "insumoStock.cantidadInsumoStock"
					),
					array(
						"tabla" => "insumoCategoria",
						"condicion" => "insumoCategoria.idInsumoCategoria = insumo.idCategoriaInsumo",
						"tipo" => "inner",
						"campos" => "insumoCategoria.nombreInsumoCategoria"
					),
				);
				$Insumos = TraerDatosComo("insumo",$condicionWhere,$condicionLike,$join);
				echo json_encode($Insumos);
			}

        }
    }
	function insumoMovimientosEditar($idinsumoMovimientos=""){
     if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

        $titulo = "Editar insumoMovimientos";
        $clientes = TraerDatos('cliente');
        $condicion = array('md5(idinsumoMovimientos)' => $idinsumoMovimientos);
        $datos = TraerUnDato('insumoMovimientos', $condicion);
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-map",
            "controlador" => "insumoMovimientos",
            "proceso" => "Editar",
            "clientes" => $clientes,
            "datos" => $datos,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/insumoMovimientos.js"
            ),
        );
        $this->load->view("insumoMovimientos/insumoMovimientosEditar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
        $idinsumoMovimientos = $this->input->post("idinsumoMovimientos");
        $idClienteinsumoMovimientos = $this->input->post("idClienteinsumoMovimientos");
				$pin = $this->input->post("pin");
				$sucursalinsumoMovimientos  = (!is_null($this->input->post("sucursalinsumoMovimientos"))) ? $this->input->post("sucursalinsumoMovimientos") : $this->session->idSucursal;

				$condicionExiste = array('codigoinsumoMovimientos' => $pin, 'md5(idinsumoMovimientos) !=' => $idinsumoMovimientos);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				$condicionExiste2 = array('idClienteinsumoMovimientos' => $idClienteinsumoMovimientos, 'estadoinsumoMovimientos' => "Activo",  'md5(idinsumoMovimientos) !=' => $idinsumoMovimientos);
				$existe2 = ExistenDatos($this->tabla, $condicionExiste2);
				if($existe==0 && $existe2 == 0){
					$datosinsumoMovimientos = array(
						"codigoinsumoMovimientos"=>$pin,
            "aleatorioinsumoMovimientos" => uniqid(),
					);
					IniciarTransaccion();
          $condicion = array('md5(idinsumoMovimientos)'=> $idinsumoMovimientos);
					$guardar = EditarDatos($this->tabla,$datosinsumoMovimientos, $condicion);
					if($guardar)
          {
						$idUsuario = $guardar;
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
					}
          else
          {
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				}
        else
        {
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function MovimientoInsumoConsulta(){
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
        } else {
			if($this->input->method(TRUE) == "POST"){
				$id_producto = $this->input->post("id_producto");
				$proceso = $this->input->post("proceso");
				$cantidad = $this->input->post("cantidad");

				$i=0;
				$unidadp=0;
				$preciop=0;
				$costop=0;
				$descripcionp=0;

				$condicion = array('insumoPresentacion.idInsumo' => $id_producto,'insumoPresentacion.estadoInsumoPresentacion' => 'Activo');
				$join = array(
					array('tabla' => 'presentacion', 'condicion' => 'insumoPresentacion.idPresentacion = presentacion.idPresentacion',"tipo"=>"","campos"=>"nombrePresentacion, unidadPresentacion"),
					array('tabla' => 'insumo', 'condicion' => 'insumo.idInsumo = insumoPresentacion.idInsumo',"tipo" => "left","campos" => "costoPromedioInsumo")
				);
				$joinarray= array(
					'tabla'
				);

				$datos = TraerDatosJoin("insumoPresentacion",$condicion, "", $join);

				$select="<select class='select2 sel col-12 presentacion' required>";
				$select .=  "<option></option>";
				foreach ($datos as $row){
				  if ($i==0) {
					$unidadp=$row->unidadInsumoPresentacion;
					$costop=$row->costoInsumoPresentacion;
					$preciop=$row->precioInsumoPresentacion;
					$descripcionp=$row->descripcionInsumoPresentacion;
				  }
				  $select.="<option value='".$row->idPresentacion."' cantidad='".($cantidad / $row->unidadInsumoPresentacion)."' costo='".$row->costoInsumoPresentacion."' precio='".$row->precioInsumoPresentacion."'  unidad='".$row->unidadInsumoPresentacion."'>".$row->nombrePresentacion." (".$row->unidadPresentacion.")</option>";
				  $i=$i+1;
				}
				$select.="</select>";

				$xdatos['select']= $select;
				$xdatos['costop']= $costop;
				$xdatos['preciop']= $preciop;
				$xdatos['unidadp']= $unidadp;
				$xdatos['descripcionp']= $descripcionp;
				$xdatos['i']=$i;


				echo json_encode($xdatos);
			}
        }
    }
	function MovimientoInsumoVer($idInsumoMovimiento = ''){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Ver Movimiento Inventario";
				$join =  array(
					array(
						"tabla" => "proveedor",
						"condicion" => "proveedor.idProveedor = insumoMovimiento.idProveedorInsumoMovimiento",
						"tipo" => "left",
						"campos" => "proveedor.nombreProveedor"
					),
				);
				$datos =  TraerDatosJoin("insumoMovimiento",array("md5(idInsumoMovimiento)" => $idInsumoMovimiento),"",$join);
				$joinDet = array(
					array(
						"tabla" => "insumo",
						"condicion" => "insumo.idInsumo = insumoMovimientoDetalle.idInsumo",
						"tipo" => "inner",
						"campos" => "insumo.nombreInsumo"
					),
					array(
						"tabla" => "insumoPresentacion",
						"condicion" => "insumoMovimientoDetalle.idInsumo = insumoPresentacion.idInsumo AND insumoMovimientoDetalle.idPresentacionInsumoMovimientoDetalle = insumoPresentacion.idPresentacion",
						"tipo" => "inner",
						"campos" => "unidadInsumoPresentacion, SUM(cantidadInsumoMovimientoDetalle) AS cantidad "
					),
					array(
						"tabla" => "presentacion",
						"condicion" => "presentacion.idPresentacion = insumoPresentacion.idPresentacion",
						"tipo" => "inner",
						"campos" => "presentacion.nombrePresentacion, presentacion.unidadPresentacion"
					),
				);
				$condicionDetalle = array(
					"md5(idInsumoMovimiento)" => $idInsumoMovimiento,
					//"unidadInventarioInsumoPresentacion" => "1",
					"estadoInsumoPresentacion" => "Activo",

				);
				$datosDetalle = TraerDatosJoin("insumoMovimientoDetalle",$condicionDetalle,"",$joinDet,"insumoMovimientoDetalle.idInsumo" );
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-trash",
					"controlador" => "MovimientosInventario",
					"proceso" => "Ver",
					"idInsumoMovimiento" => $idInsumoMovimiento,
					"datos" => $datos,
					"datosDetalle" => $datosDetalle,
				);

				$this->load->view("insumoMovimiento/insumoMovimientoVer",$datosVista);
			}
		}
	}
	public function ConsultaStock(){

    if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
        GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
        return;
    }

    /* =====================================================
       GET → Vista
    ===================================================== */
    if($this->input->method(TRUE) == "GET"){

        $campos = array(
            "idInsumoCategoria" => "idCategoria",
            "nombreInsumoCategoria" => "nombreCategoria"
        );

        $categoria   = TraerDatosRenombrados('insumoCategoria',$campos,array("estadoInsumoCategoria"=>"Activo"));
        $idSucursal  = $this->session->idSucursal;
        $admin       = $this->session->admin;
        $sucursales  = TraerDatos("sucursal");

        $datosVista = array(
            "titulo"      => "Consulta de Existencias",
            "icono"       => "fa fa-box",
            "controlador" => "ConsultaStock",
            "proceso"     => "Consulta",
            "idSucursal"  => $idSucursal,
            "admin"       => $admin,
            "sucursales"  => $sucursales,
            "categorias"  => $categoria,
        );

        $extras = array(
            "css" => array(),
            "js"  => array("scripts/movimientosinventario.js"),
        );

        GblPlantilla("insumoMovimiento/consultaStock",$datosVista,$extras,"Consulta de Existencias");
        return;
    }

    /* =====================================================
       POST → DataTables
    ===================================================== */

    $draw          = intval($this->input->post("draw"));
    $desdeFilas    = intval($this->input->post("start"));
    $cantidadFilas = intval($this->input->post("length"));
    $order         = $this->input->post("order");
    $categoria     = $this->input->post("categoria");
    $sucursal      = $this->input->post("sucursal");
    $busqueda      = $this->input->post("busqueda");

    $col = 0;
    $ordenDireccion = "desc";

    if(!empty($order)){
        $col = $order[0]["column"];
        $ordenDireccion = ($order[0]["dir"] === "asc") ? "asc" : "desc";
    }

    $columnasValidas = array(
        0 => 'insumo.idInsumo',
        1 => 'insumo.nombreInsumo',
        2 => 'insumoCategoria.nombreInsumoCategoria',
        3 => 'insumo.descripcionInsumo'
    );

    $ordenCampos = $columnasValidas[$col] ?? null;

    /* ===========================
       JOINS CORRECTOS
    =========================== */
    $join = array(

        array(
            "tabla"     => "insumoStock",
            "tipo"      => "left",
            "condicion" => "insumo.idInsumo = insumoStock.idInsumo 
                            AND insumoStock.idSucursalInsumoStock = ".$this->db->escape($sucursal)
        ),

        array(
            "tabla"     => "insumoCategoria",
            "tipo"      => "left",
            "condicion" => "insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria"
        ),

        array(
            "tabla"     => "insumoPresentacion",
            "tipo"      => "left",
            "condicion" => "insumo.idInsumo = insumoPresentacion.idInsumo
                            AND insumoPresentacion.unidadInventarioInsumoPresentacion = 1
                            AND insumoPresentacion.estadoInsumoPresentacion = 'Activo'"
        ),

        array(
            "tabla"     => "presentacion",
            "tipo"      => "left",
            "condicion" => "insumoPresentacion.idPresentacion = presentacion.idPresentacion"
        )
    );

    /* ===========================
       CONDICIONES
    =========================== */
    $condicion = array(
        "insumo.estadoInsumo !=" => "Borrado"
    );

    if($categoria != "All"){
        $condicion["insumoCategoria.idInsumoCategoria"] = $categoria;
    }

    /* ===========================
       SELECT ONLY_FULL_GROUP_BY
    =========================== */
    $campos = "
        insumo.idInsumo,
        MAX(insumo.nombreInsumo) AS nombreInsumo,
        MAX(insumoCategoria.nombreInsumoCategoria) AS nombreInsumoCategoria,
        MAX(insumo.descripcionInsumo) AS descripcionInsumo,
        MAX(insumoPresentacion.unidadInsumoPresentacion) AS unidadInsumoPresentacion,
        MAX(presentacion.nombrePresentacion) AS nombrePresentacion,
        MAX(presentacion.unidadPresentacion) AS unidadPresentacion,
        MAX(insumo.stockMinimoInsumo) AS stockMinimoInsumo,
        IFNULL(SUM(insumoStock.cantidadInsumoStock),0) AS cantidadInsumoStock
    ";

    /* ===========================
       QUERY PRINCIPAL
    =========================== */
    $insumos = TraerDatosTablaJoinGroup(
        $this->tablaInsumo,
        $ordenCampos,
        $busqueda,
        $columnasValidas,
        $cantidadFilas,
        $desdeFilas,
        $ordenDireccion,
        $condicion,
        $join,
        $campos,
        "insumo.idInsumo"
    );

    if($insumos == 0){
        echo json_encode(array(
            "draw"=>$draw,
            "recordsTotal"=>0,
            "recordsFiltered"=>0,
            "data"=>array()
        ));
        exit;
    }

    /* ===========================
       FORMATEO DATOS
    =========================== */
    $data = array();

    foreach($insumos as $i){

        $cantidad = ($i->unidadInsumoPresentacion > 0)
            ? number_format($i->cantidadInsumoStock / $i->unidadInsumoPresentacion,2)
            : 0;

        $menu = "
        <div class='input-group-prepend'>
            <button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-sm dropdown-toggle'>Menu</button>
            <div class='dropdown-menu dropdown-menu-right'>
                <a class='dropdown-item insumoVer' idInsumo='".md5($i->idInsumo)."'>
                    <i class='fa fa-eye'></i> Ver Insumo
                </a>
                <a class='dropdown-item ConsultaStockAjuste' 
                   stock='".$i->cantidadInsumoStock."' 
                   idInsumo='".md5($i->idInsumo)."'>
                    <i class='fa fa-edit'></i> Ajuste
                </a>
            </div>
        </div>";

        $data[] = array(
            $i->idInsumo,
            $i->nombreInsumo,
            $i->nombreInsumoCategoria,
            $i->descripcionInsumo,
            $i->nombrePresentacion." (".$i->unidadPresentacion.")",
            $i->stockMinimoInsumo,
            $cantidad,
            $menu
        );
    }

    /* ===========================
       TOTAL CORRECTO PARA GROUP
    =========================== */
    $total = TraerTotalDatos(
        $this->tablaInsumo,
        $condicion,
        array(),
        array(),
        $join,
        "insumo.idInsumo"
    );

    echo json_encode(array(
        "draw"            => $draw,
        "recordsTotal"    => $total,
        "recordsFiltered" => $total,
        "data"            => $data
    ));
    exit;
}

	function ConsultaStockVer($idInsumo = ''){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Detalle Insumo";
				$join =  array(
					array(
						"tabla" => "insumoCategoria",
						"condicion" => "insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria",
						"tipo" => "left",
						"campos" => "insumoCategoria.nombreInsumoCategoria"
					),
					array(
						"tabla" => "insumoPresentacion",
					"condicion" => "insumo.idInsumo = insumoPresentacion.idInsumo",
						 "tipo" => "left",
					   "campos" => "insumoPresentacion.unidadInsumoPresentacion",
					),
					array(
						"tabla" => "insumoStock",
						"condicion" => "insumo.idInsumo = insumoStock.idInsumo",
						"tipo" => "left",
						"campos" => "insumoStock.cantidadInsumoStock"
					),
				);
				// $campos = "insumoStock.cantidadInsumoStock,insumo.nombreInsumo,insumoCategoria.nombreInsumoCategoria,insumo.descripcionInsumo,insumo.marcaInsumo,insumo.stockMinimoInsumo,insumoPresentacion.unidadInsumoPresentacion";
				$datos =  TraerDatosJoin("insumo",array("md5(insumo.idInsumo)" => $idInsumo, "insumoPresentacion.unidadInventarioInsumoPresentacion"=>"1","insumoPresentacion.estadoInsumoPresentacion"=>"Activo"),"",$join);

				$joinDet = array(
					array(
						"tabla" => "presentacion",
						"condicion" => "presentacion.idPresentacion = insumoPresentacion.idPresentacion",
						"tipo" => "inner",
						"campos" => "presentacion.nombrePresentacion, presentacion.unidadPresentacion"
					),
				);
				$datosDetalle = TraerDatosJoin("insumoPresentacion",array("md5(insumoPresentacion.idInsumo)" => $idInsumo,"insumoPresentacion.estadoInsumoPresentacion"=> "Activo"),"insumoPresentacion.unidadInsumoPresentacion DESC",$joinDet );

				$joinDetProd = array(
					array(
						"tabla" => "presentacion",
						"condicion" => "presentacion.idPresentacion = productoInsumo.idPresentacionProductoInsumo",
						"tipo" => "inner",
						"campos" => "presentacion.nombrePresentacion, presentacion.unidadPresentacion"
					),
					array(
						"tabla" => "producto",
						"condicion" => "productoInsumo.idProducto = producto.idProducto",
						"tipo" => "inner",
						"campos" => "producto.nombreProducto"
					),
				);
				$datosDetalleProd = TraerDatosJoin("productoInsumo",array("md5(productoInsumo.idInsumo)" => $idInsumo,"productoInsumo.estadoProductoInsumo"=>"Activo"),"",$joinDetProd ,"producto.idProducto");

				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-trash",
					"controlador" => "MovimientosInventario",
					"proceso" => "Ver",
					"idInsumo" => $idInsumo,
					"datos" => $datos,
					"datosDetalle" => $datosDetalle,
					"datosDetalleProd" => $datosDetalleProd,
				);
				$this->load->view("insumoMovimiento/consultaStockVer",$datosVista);
			}
		}
	}
	function ConsultaStockAjuste($idInsumo = ''){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Ajuste de Inventario";

				$join =  array(
					array(
						"tabla" => "presentacion",
						"condicion" => "presentacion.idPresentacion = insumoPresentacion.idPresentacion",
						"tipo" => "inner",
						"campos" => "presentacion.nombrePresentacion, presentacion.unidadPresentacion"
					),
				);
				$presentaciones =  TraerDatosJoin("insumoPresentacion",array("md5(insumoPresentacion.idInsumo)" => $idInsumo,"insumoPresentacion.estadoInsumoPresentacion"=>"Activo"),"",$join);

				$join =  array(
					array(
						"tabla" => "insumoCategoria",
						"condicion" => "insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria",
						"tipo" => "left",
						"campos" => "insumoCategoria.nombreInsumoCategoria"
					),
					array(
						"tabla" => "insumoPresentacion",
					"condicion" => "insumo.idInsumo = insumoPresentacion.idInsumo",
						 "tipo" => "left",
					   "campos" => "insumoPresentacion.unidadInsumoPresentacion",
					),
					array(
						"tabla" => "insumoStock",
						"condicion" => "insumo.idInsumo = insumoStock.idInsumo",
						"tipo" => "left",
						"campos" => "insumoStock.cantidadInsumoStock"
					),
				);
				// $campos = "insumoStock.cantidadInsumoStock,insumo.nombreInsumo,insumoCategoria.nombreInsumoCategoria,insumo.descripcionInsumo,insumo.marcaInsumo,insumo.stockMinimoInsumo,insumoPresentacion.unidadInsumoPresentacion";
				$datos =  TraerUnDatoJoin("insumo",array("md5(insumo.idInsumo)" => $idInsumo, "insumoPresentacion.unidadInventarioInsumoPresentacion"=>"1","insumoPresentacion.estadoInsumoPresentacion"=>"Activo"),$join);


				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-trash",
					"controlador" => "MovimientosInventario",
					"proceso" => "Ajuste",
					"idInsumo" => $idInsumo,
					"presentaciones" => $presentaciones,
					"datos" => $datos,
				);
				$this->load->view("insumoMovimiento/consultaStockAjuste",$datosVista);
			}else if($this->input->method(TRUE) == "POST"){
				$idInsumo = $this->input->post('idInsumo');
				$cantidad = $this->input->post('cantidad');
				$costo = $this->input->post('costo');
				$presentacion = $this->input->post('presentacion');
				$unidad = $this->input->post('unidad');
				$error = false;
				$join = array(
					array(
						"tabla" => "insumoStock",
						"condicion" => "insumo.idInsumo = insumoStock.idInsumo",
						"tipo" => "inner",
						"campos" => "insumoStock.cantidadInsumoStock, costoPromedioInsumo"
					),
				);
				$insumo = TraerUnDatoJoin("insumo",array("md5(insumo.idInsumo)"=>$idInsumo),$join);
				$cantidadOriginal = $insumo->cantidadInsumoStock;
				$costoPromedio = $insumo->costoPromedioInsumo;
				$cantidadNueva = $cantidad * $unidad;
				IniciarTransaccion();
				$datosAjuste = array(
					"idInsumo" => $insumo->idInsumo,
					"cantidadInicialInsumoAjuste" => $cantidadOriginal,
					"cantidadFinalInsumoAjuste" => $cantidadNueva,
					"idUsuarioInsumoAjuste" => $this->session->idUsuario,
					"estadoInsumoAjuste" => "Activo",
				);
				$ajuste = GuardarDatos("insumoAjuste",$datosAjuste);
				if ($ajuste){
					if($cantidadOriginal > $cantidadNueva){
						$diferencia = $cantidadOriginal - $cantidadNueva;
						$lotes = TraerDatos("insumoLote",array("md5(idProductoInsumoLote)" => $idInsumo,"cantidadInsumoLote >"=>"0"),"fechaRegistroInsumoLote ASC" );
						if($lotes){
							$cantidadN = $cantidadNueva;
							foreach($lotes as $l){
								$existencia = $l->cantidadInsumoLote;
								$descuento = ($cantidadN >= $existencia) ? $existencia : $cantidadN;
								$cantidadN = $cantidadN - $existencia;

								$actualizarLote = ActualizarCorrelativo("insumoLote",array("idInsumoLote" =>$l->idInsumoLote),"cantidadInsumoLote", -$descuento );
								if($actualizarLote){
									if($cantidadN <= 0){
										$error = false;
										break 1;
									}
								}
								else{
									$respuesta['codigo'] = 500;
									$error = true;
									break 1;
								}
							}
						}
						if($error == false){
							$actualizarStock = ActualizarCorrelativo("insumoStock",array("md5(idInsumo)" =>$idInsumo),"cantidadInsumoStock", -$diferencia );
							if($actualizarStock){
								$datosMovimiento = array(
									"idSucursalInsumoMovimiento" => $this->session->idSucursal,
									"categoriaInsumoMovimiento" => "Descarga",
									"tipoMovimientoInsumo" => "Ajuste",
									"tipoDocumentoInsumoMovimiento" => "DI",
									"numeroDocumentoInsumoMovimiento" => GblTraerConfiguracion("descargaCorrelativo"),
									"descripcionInsumoMovimiento" => "Descargo por Ajuste de Inventario",
									"totalInsumoMovimiento" => ($diferencia / $unidad) * $costoPromedio,
									"idUsuarioInsumoMovimiento" => $this->session->idUsuario,
									"estadoInsumoMovimiento" => "Activo",
								);
								$movimiento = GuardarDatos("insumoMovimiento",$datosMovimiento);
								if($movimiento){
									$actualizarCor = ActualizarCorrelativo("configuraciones",array("parametroConfiguracion" => "descargaCorrelativo"),"valorConfiguracion", 1 );

									$idMovimiento = $movimiento;
									$datosMovimientoDetalle = array(
										"idInsumoMovimiento" => $idMovimiento,
										"idInsumo" => $insumo->idInsumo,
										"cantidadInsumoMovimientoDetalle" => $diferencia / $unidad ,
										"costoInsumoMovimientoDetalle" => $costoPromedio,
										"idPresentacionInsumoMovimientoDetalle" => $presentacion,
										"idUsuarioInsumoMovimientoDetalle" => $this->session->idUsuario,
										"estadoInsumoMovimientoDetalle" => "Activo",
									);
									$movimientoDetalle = GuardarDatos("insumoMovimientoDetalle",$datosMovimientoDetalle);
									if($movimientoDetalle){
										$error = false;
									} else {
										$respuesta['codigo'] = 505;
										$error = true;
									}
								} else{
									$respuesta['codigo'] = 505;
									$error = true;
								}
							}
							else{
								$respuesta['codigo'] = 501;
								$error = true;
							}
						}
						else{
							$respuesta['codigo'] = 502;
							$error = true;
						}
					} else {
						$diferencia = $cantidadNueva - $cantidadOriginal;
						if($diferencia != 0){
							$ultimoLote = TraerMaxValor("insumoLote","idInsumoLote",array("md5(idProductoInsumoLote)"=>$idInsumo));
							$lotes = TraerUnDato("insumoLote",array("idInsumoLote" => $ultimoLote,"cantidadInsumoLote >"=>"0"));
							if($lotes){
								$actualizarLote = ActualizarCorrelativo("insumoLote",array("idInsumoLote" =>$lotes->idInsumoLote),"cantidadInsumoLote", $diferencia );
								if($actualizarLote){
									$error = false;
								} else {
									$respuesta['codigo'] = 503;
									$error = true;
								}
							}
							$actualizarStock = ActualizarCorrelativo("insumoStock",array("md5(idInsumo)" =>$idInsumo),"cantidadInsumoStock", $diferencia );
							if($actualizarStock){
								$datosMovimiento = array(
									"idSucursalInsumoMovimiento" => $this->session->idSucursal,
									"categoriaInsumoMovimiento" => "Carga",
									"tipoMovimientoInsumo" => "Ajuste",
									"tipoDocumentoInsumoMovimiento" => "CI",
									"numeroDocumentoInsumoMovimiento" => GblTraerConfiguracion("cargaCorrelativo"),
									"descripcionInsumoMovimiento" => "Carga por Ajuste de Inventario",
									"totalInsumoMovimiento" => ($diferencia / $unidad) * $costoPromedio,
									"idUsuarioInsumoMovimiento" => $this->session->idUsuario,
									"estadoInsumoMovimiento" => "Activo",
								);
								$movimiento = GuardarDatos("insumoMovimiento",$datosMovimiento);
								if($movimiento){
									$actualizarCor = ActualizarCorrelativo("configuraciones",array("parametroConfiguracion" => "cargaCorrelativo"),"valorConfiguracion", 1 );

									$idMovimiento = $movimiento;
									$datosMovimientoDetalle = array(
										"idInsumoMovimiento" => $idMovimiento,
										"idInsumo" => $insumo->idInsumo,
										"cantidadInsumoMovimientoDetalle" => $diferencia / $unidad ,
										"costoInsumoMovimientoDetalle" => $costoPromedio,
										"idPresentacionInsumoMovimientoDetalle" => $presentacion,
										"idUsuarioInsumoMovimientoDetalle" => $this->session->idUsuario,
										"estadoInsumoMovimientoDetalle" => "Activo",
									);
									$movimientoDetalle = GuardarDatos("insumoMovimientoDetalle",$datosMovimientoDetalle);
									if($movimientoDetalle){
										$error = false;
									} else {
										$respuesta['codigo'] = 505;
										$error = true;
									}
								} else{
									$respuesta['codigo'] = 505;
									$error = true;
								}
							}
							else{
								$respuesta['codigo'] = 504;
								$error = true;
							}
						}

					}
				}
				if($costo != ""){
					$nuevoPromedio = $costo / $unidad;
					$condicionInsumoCosto = array(
						"idInsumo" => $insumo->idInsumo,
					);
					$actualizarPromedio = EditarDatos("insumo",array("costoPromedioInsumo" => $nuevoPromedio,"aleatorioInsumo"=>uniqid()),$condicionInsumoCosto);

					if($actualizarPromedio){
						$error = false;
					}
					else{
						$respuesta['codigo'] = 504;
						$error = true;
					}

					$datosCosto = array(
						"idInsumo" =>$insumo->idInsumo,
						"costoPromedioInsumoCosto" => $nuevoPromedio
					);
					$historialCostoPromedio = GuardarDatos("insumoCosto",$datosCosto);
					if($historialCostoPromedio){
						$error = false;
					}
					else{
						$respuesta['codigo'] = 505;
						$error = true;
					}
				}

				($error == false) ? $respuesta['codigo'] = 200 : '';
				($error == false) ? EjecutarTransaccion() : DeshacerTransaccion();
				echo json_encode($respuesta);
			}
		}
	}
}
/* End of file Usuarios.php */
