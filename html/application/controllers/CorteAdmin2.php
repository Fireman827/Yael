<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CorteAdmin extends CI_Controller {

	private $tabla = "corteCaja";
	//private $tablaPermisos = "usuarioPermisos";
	private $controlador = "CorteAdmin";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

  public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		}
		else
		{
			$cajasReal = "";
			$cajas = $this->TraerCajasSucursal();
			if ($cajas !== false)
			{
				foreach ($cajas as $caja)
				{
					$cajasReal .= "<option value='".$caja->idCaja."'";
					$condicionExiste = array(
							"estadoCorte" => "Vigente",
							"idUsuarioCorte" => $this->session->idUsuario,
							"idSucursalCorte" => $this->session->idSucursal,
							"idCaja" => $caja->idCaja,
					);
					$existeCaja = ExistenDatos("corteCaja", $condicionExiste);
					if ($existeCaja)
					{
						$cajasReal .= "selected";
					}
					$cajasReal .= ">".$caja->nombreCaja."</option>";
				}
			}

			$condicionExisteCajasDisponibles = array(
					"idSucursalCaja" => $this->session->idSucursal,
					"aperturaCaja" => 0,
					"turnoCaja" => 0,
			);
			$condicionExisteCajasDisponiblesTurno = array(
					"idSucursalCaja" => $this->session->idSucursal,
					"aperturaCaja" => 1,
					"turnoCaja" => 0,
			);
			$nCajasApertura = TraerTotalDatos('caja', $condicionExisteCajasDisponibles);
			$nCajasTurnos = TraerTotalDatos('caja', $condicionExisteCajasDisponiblesTurno);

			$titulo = "Corte caja";
			$datos = $this->CorteTraerApertura();
			$existe = 0;
			if($datos != "")
			{
				$existe = 1;
			}

			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-trash",
				"botones" => array(
					array(
						"icono"=> "",
						'controlador' => $this->controlador,
						'url' => 'aperturaCaja',
						'txt' => 'Realizar Apertura',
						'posicion' => 'center', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'aperturaCaja'
					),
				),
        "encabezados"=>array(
					"ID"=>1,
					"Fecha"=>3,
					"Empleado"=>2,
					"Monto Apertura"=>2,
					"Monto Cierre"=>2,
					"Estado"=>2,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
				"existe" => $existe,
				"datosApertura" => $datos,
				"cajasReal" => $cajasReal,
				"nCajasApertura" => $nCajasApertura,
				"nCajasTurnos" => $nCajasTurnos,
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/corteAdmin.js"
				),
			);
			if ($this->session->admin)
			{
				GblPlantilla("corte/CorteAdmin",$datosVista,$extras,$titulo);
			}
			else
			{
				GblPlantilla("corte/CorteUser",$datosVista,$extras,$titulo);
			}
		}
	}
  function CorteMostrar(){
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
      0 => 'idCorteCaja',
      1 => 'fechaCorte',
      2 => 'montoApertura',
      3 => 'montoCorte',
    );
    //Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
    if (!isset($columnasValidas[$col])){
      $ordenCampos = null;
    } else {
      $ordenCampos = $columnasValidas[$col];
    }
    // Fin espacio del data tabla
    $sucursal = $this->input->post("sucursal");
    $this->session->idSucursal = $sucursal;
    $condicion = array('idSucursalCorte' => $sucursal);
    // $join =
    $cortes = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
    // print_r($usuarios);
    //Lectura de datos de la base para mostrar en el datatabla
    if ($cortes != 0){
      $datosMostrar = array();
      foreach ($cortes as $corte){
        $menuOpciones = "
        <div class='input-group-prepend'>
        <button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
        <div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

        $funcion ="CorteTurnoDetalle";
        if(GblPermisos($this,$funcion,$this->controlador)){
          $menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($corte->idCorteCaja)."'><i class='fas fa-eye' ></i> Ver detalles</a>";
        }
		if ($corte->estadoCorte != "Vigente"){
			$funcion ="ReporteRevisionInsumo";
			if(GblPermisos($this,$funcion,$this->controlador)){
				$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($corte->idCorteCaja)."' target='_blank'><i class='fas fa-print' ></i> Revision Insumos</a>";
			}
		}

        $menuOpciones .= "
        </div>
        </div>";
        $nombre = TraerUnDatoIndividual("usuario","nombreUsuario",array("idUsuario" => $corte->idUsuarioCorte))[0]["nombreUsuario"];

        $datosMostrar[] = array(
          $corte->idCorteCaja,
          $corte->fechaCorte." ".$corte->horaCorte,
          $nombre,
          $corte->montoApertura,
          $corte->montoCorte,
          $corte->estadoCorte,
          $menuOpciones,
        );
      }
      $totalCortes = TraerTotalDatos($this->tabla,$condicion);
      $output = array(
        "draw" => $draw,
        "recordsTotal" => $totalCortes,
        "recordsFiltered" => $totalCortes,
        "data" => $datosMostrar
      );
    } else {
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

	function CorteTraerApertura()
	{
      if($this->input->method(TRUE) == "GET")
      {
				$caja = $this->input->post("caja");
		    $idSucursal = $this->session->idSucursal;
				$idUsuario = $this->session->idUsuario;
				$condicionExiste = array(
						"estadoCorte" => "Vigente",
						"idUsuarioCorte" => $this->session->idUsuario,
						"idSucursalCorte" => $idSucursal,
				);
				$existe = ExistenDatos("corteCaja", $condicionExiste);
				if($existe > 0)
				{
					$joinFacDet = array(
							array(
									"tabla" => "usuario",
									"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
									"tipo" => "inner",
									"campos" => "usuario.nombreUsuario"
							),
							array(
									"tabla" => "corteTurno",
									"condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente AND corteTurno.estadoCorteTurno = 'Vigente'",
									"tipo" => "inner",
									"campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
							),
					);
					$apertura = TraerDatosJoin("corteCaja", $condicionExiste,'',$joinFacDet);
					if ($apertura)
					{
						return $apertura;
					}
					else
					{
						$joinFacDet = array(
								array(
										"tabla" => "usuario",
										"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
										"tipo" => "inner",
										"campos" => "usuario.nombreUsuario"
								),
						);
						$apertura = TraerDatosJoin("corteCaja", $condicionExiste,'',$joinFacDet);
						return $apertura;
					}
				}
				else
				{
					return "";
				}
			}
			else if ($this->input->method(TRUE) == "POST")
      {
				$idSucursal = $this->input->post("sucursal");
				$caja = $this->input->post("caja");
				$idUsuario = $this->session->idUsuario;
				$adminUser = $this->session->admin;
				if($adminUser == 1)
				{
					$condicionExisteAdm = array(
							"estadoCorte" => "Vigente",
							// "idUsuarioCorte" => $this->session->idUsuario,
							"idSucursalCorte" => $idSucursal,
							"idCaja" => $caja,
					);
					$existeAmd = ExistenDatos("corteCaja", $condicionExisteAdm);
					if ($existeAmd > 0)
					{
						$joinFacDet = array(
								array(
										"tabla" => "usuario",
										"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
										"tipo" => "inner",
										"campos" => "usuario.nombreUsuario"
								),
								array(
										"tabla" => "corteTurno",
										"condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente",
										"tipo" => "inner",
										"campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
								),
						);
						$apertura = TraerDatosJoin("corteCaja", $condicionExisteAdm,'',$joinFacDet);
						if ($apertura)
						{
							foreach ($apertura as $fila)
							{
								$datosRespuesta["nombreUsuario"] = $fila->nombreUsuario;
								$datosRespuesta["fechaCorte"] = $fila->fechaCorte;
								$datosRespuesta["horaCorte"] = $fila->horaCorte;
								$datosRespuesta["montoTurnoCorteCaja"] = $fila->montoTurnoCorteCaja;
								$datosRespuesta["corteTurno"] = $fila->corteTurno;
								$datosRespuesta["montoApertura"] = $fila->montoApertura;
								$datosRespuesta["idCorteCaja"] = md5($fila->idCorteCaja);
								$datosRespuesta["idTurnoVigente"] = md5($fila->idTurnoVigente);
								$datosRespuesta["idTurnoVigenteExiste"] = $fila->idTurnoVigente;
								$datosRespuesta["idCaja"] = md5($fila->idCaja);
								$datosRespuesta["existe"] = 1;
								$datosRespuesta["color"] = GblTraerConfiguracion('colorComponentes');
							}
						}
						else
						{
							$joinFacDet = array(
									array(
											"tabla" => "usuario",
											"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
											"tipo" => "inner",
											"campos" => "usuario.nombreUsuario"
									),
							);
							$apertura = TraerDatosJoin("corteCaja", $condicionExisteAdm,'',$joinFacDet);
							if ($apertura)
							{
								foreach ($apertura as $fila)
								{
									$datosRespuesta["nombreUsuario"] = $fila->nombreUsuario;
									$datosRespuesta["fechaCorte"] = $fila->fechaCorte;
									$datosRespuesta["horaCorte"] = $fila->horaCorte;
									$datosRespuesta["montoTurnoCorteCaja"] = 0;
									$datosRespuesta["corteTurno"] = 0;
									$datosRespuesta["montoApertura"] = $fila->montoApertura;
									$datosRespuesta["idCorteCaja"] = md5($fila->idCorteCaja);
									$datosRespuesta["idTurnoVigente"] = md5($fila->idTurnoVigente);
									$datosRespuesta["idTurnoVigenteExiste"] = $fila->idTurnoVigente;
									$datosRespuesta["idCaja"] = md5($fila->idCaja);
									$datosRespuesta["existe"] = 1;
									$datosRespuesta["color"] = GblTraerConfiguracion('colorComponentes');
								}
							}
						}
						echo json_encode($datosRespuesta);
					}
					else
					{
						$datosRespuesta["nombreUsuario"] = "";
						$datosRespuesta["fechaCorte"] = "";
						$datosRespuesta["horaCorte"] = "";
						$datosRespuesta["montoTurnoCorteCaja"] = "";
						$datosRespuesta["corteTurno"] = "";
						$datosRespuesta["montoApertura"] = "";
						$datosRespuesta["idCorteCaja"] = "";
						$datosRespuesta["idTurnoVigente"] = "";
						$datosRespuesta["idTurnoVigenteExiste"] = "";
						$datosRespuesta["idCaja"] = "";
						$datosRespuesta["existe"] = 0;
						$datosRespuesta["color"] = "";
						echo json_encode($datosRespuesta);
					}
				}
				else
				{
					$condicionExiste = array(
							"estadoCorte" => "Vigente",
							"idUsuarioCorte" => $this->session->idUsuario,
							"idSucursalCorte" => $idSucursal,
					);
					$existe = ExistenDatos("corteCaja", $condicionExiste);
					if($existe > 0)
					{
						$joinFacDet = array(
								array(
										"tabla" => "usuario",
										"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
										"tipo" => "inner",
										"campos" => "usuario.nombreUsuario"
								),
								array(
										"tabla" => "corteTurno",
										"condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente",
										"tipo" => "inner",
										"campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
								),
						);
						$apertura = TraerDatosJoin("corteCaja", $condicionExiste,'',$joinFacDet);

						foreach ($apertura as $fila)
						{
							$datosRespuesta["nombreUsuario"] = $fila->nombreUsuario;
							$datosRespuesta["fechaCorte"] = $fila->fechaCorte;
							$datosRespuesta["horaCorte"] = $fila->horaCorte;
							$datosRespuesta["montoTurnoCorteCaja"] = $fila->montoTurnoCorteCaja;
							$datosRespuesta["corteTurno"] = $fila->corteTurno;
							$datosRespuesta["montoApertura"] = $fila->montoApertura;
							$datosRespuesta["idCorteCaja"] = md5($fila->idCorteCaja);
							$datosRespuesta["idTurnoVigente"] = md5($fila->idTurnoVigente);
							$datosRespuesta["idTurnoVigenteExiste"] = $fila->idTurnoVigente;
							$datosRespuesta["idCaja"] = md5($fila->idCaja);
							$datosRespuesta["existe"] = 1;
							$datosRespuesta["color"] = GblTraerConfiguracion('colorComponentes');
						}
						echo json_encode($datosRespuesta);
					}
					else
					{
						$datosRespuesta["nombreUsuario"] = "";
						$datosRespuesta["fechaCorte"] = "";
						$datosRespuesta["horaCorte"] = "";
						$datosRespuesta["montoTurnoCorteCaja"] = "";
						$datosRespuesta["corteTurno"] = "";
						$datosRespuesta["montoApertura"] = "";
						$datosRespuesta["idCorteCaja"] = "";
						$datosRespuesta["idTurnoVigente"] = "";
						$datosRespuesta["idTurnoVigenteExiste"] = "";
						$datosRespuesta["idCaja"] = "";
						$datosRespuesta["existe"] = 0;
						$datosRespuesta["color"] = "";
						echo json_encode($datosRespuesta);
					}
				}
			}
	}
	function TraerCajasSucursal()
	{
		if($this->input->method(TRUE) == "GET")
		{
			$idSucursal = $this->session->idSucursal;
			$idUsuario = $this->session->idUsuario;
			$condicionExiste = array(
					"idSucursalCaja" => $idSucursal,
			);
			$existe = ExistenDatos("caja", $condicionExiste);
			if ($existe)
			{
				return $cajas = TraerDatos('caja', $condicionExiste);
			}
			else
			{
				echo "";
			}
		}
		else if ($this->input->method(TRUE) == "POST")
		{
			$option = "";
			$idSucursal = $this->input->post("sucursal");
			$idUsuario = $this->session->idUsuario;
			$condicionExiste = array(
					"idSucursalCaja" => $idSucursal,
			);
			$existe = ExistenDatos("caja", $condicionExiste);
			if ($existe)
			{
				$cajas = TraerDatos('caja', $condicionExiste);
				$n = 1;
				foreach ($cajas as $fila)
				{
					if($n == 1)
					{
						$option .= "<option value='".$fila->idCaja."' selected>".$fila->nombreCaja."</option>";
					}
					else
					{
						$option .= "<option value='".$fila->idCaja."'>".$fila->nombreCaja."</option>";
					}
					$n++;
				}
			}

			echo $option;
		}
	}
	function AperturaCaja(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$idSucursal = $this->session->idSucursal;
				$condicionExiste = array(
						"idSucursalCaja" => $idSucursal,
						"aperturaCaja" => 0,
						"turnoCaja" => 0,
				);
				$cajas = TraerDatos('caja', $condicionExiste);
				$condicionUser = array("idSucursalUsuario" => $idSucursal,);
				$usuarios = TraerDatos("usuario", $condicionUser);
				$titulo = "Apertura Caja";
				$datosVista = array(
						"titulo" => $titulo,
						"icono" => "fas fa-plus",
						"controlador" => $this->controlador,
						"proceso" => "Apertura",
						"caja" => $cajas,
						"usuarios" => $usuarios,
						"admin" => $this->session->admin,
						"usuarioid" => $this->session->idUsuario,
				);
				$extras = array(
						'css' => array(),
						'js' => array(
								"scripts/corteAdmin.js"
						),
				);
				$this->load->view("corte/AperturaCaja",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$montoApertura = $this->input->post("montoApertura");
				$usuarioApertura = $this->input->post("usuarioApertura");
				$cajaApertura = $this->input->post("cajaApertura");

				$datosCorte = array(
					"montoApertura"=>$montoApertura,
					"idCaja"=>$cajaApertura,
					"idUsuarioCorte"=>$usuarioApertura,
					"idSucursalCorte"=>$this->session->idSucursal,
					"fechaCorte"=>date("Y-m-d"),
					"horaCorte"=>date("H:i:s"),
					"estadoCorte"=>"Vigente",
				);
				$guardar = GuardarDatos($this->tabla,$datosCorte);
				if($guardar){
					$datosTurno = array(
						"idCorte"=>$guardar,
						"montoTurnoCorteCaja"=>$montoApertura,
						"idUsuarioCorteTurno"=>$usuarioApertura,
						"corteTurno" => 1,
						// "idSucursalCorte"=>$this->session->idSucursal,
						"fechaCorteTurno"=>date("Y-m-d"),
						"horaCorteTurno"=>date("H:i:s"),
						"estadoCorteTurno"=>"Vigente",
					);
					$guardarTurno = GuardarDatos("corteTurno",$datosTurno);
					if($guardarTurno)
					{
						$dataCorte = array(
							'idTurnoVigente' => $guardarTurno,
						);
						$condicionCorte = array('idCorteCaja' => $guardar,);
						$editarCorte = EditarDatos($this->tabla, $dataCorte, $condicionCorte);
						if ($editarCorte)
						{
							$datosCaja = array(
								'aperturaCaja' => 1,
								'turnoCaja' => 1,
							);
							$condicionCaja = array('idCaja' => $cajaApertura,);
							$editarCorte = EditarDatos("caja", $datosCaja, $condicionCaja);
						}
					}
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
					$datosRespuesta["idCorteCaja"] = $guardar;
				} else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
					$datosRespuesta["idCorteCaja"] = 0;
				}

				echo json_encode($datosRespuesta);
			}
		}
	}

	function CorteTurnoDetalle($idCorte = ""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionExiste = array(
						"idUsuarioCorteTurno" => $this->session->idUsuario,
						"md5(idCorte)" => $idCorte,
				);
				$joinFacDet = array(
						array(
								"tabla" => "usuario",
								"condicion" =>"corteTurno.idUsuarioCorteTurno = usuario.idUsuario",
								"tipo" => "inner",
								"campos" => "usuario.nombreUsuario"
						),
				);
				$turnos = TraerDatosJoin("corteTurno", $condicionExiste,'',$joinFacDet);
				$titulo = "Turnos";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-exchange",
					"controlador"=> $this->controlador,
					"proceso"=> "Ver",
					"turnos" => $turnos,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/corteAdmin.js"
					),
				);
				GblPlantilla("corte/CorteTurnoDetalle",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreCliente = $this->input->post("nombreCliente");
				$telefonoCliente = $this->input->post("telefonoCliente");
				$direccionCliente = $this->input->post("direccionCliente");
				$referenciaCliente = $this->input->post("referenciaCliente");
				$idCategoriaCliente = $this->input->post("idCategoriaCliente");
				$condicionExiste = array('nombreCliente'=>$nombreCliente,'telefonoCliente' => $telefonoCliente);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCliente = array(
						"idSucursalCliente"=>$this->session->idSucursal,
						"nombreCliente"=>$nombreCliente,
						"telefonoCliente"=>$telefonoCliente,
						"direccionCliente"=>$direccionCliente,
						"referenciaCliente"=>$referenciaCliente,
						"idCategoriaCliente"=>$idCategoriaCliente
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCliente);
					if($guardar){
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					} else {
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 402;
					}
				} else {
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function RealizarCorte($idCorte = "", $idTurno = "", $idCaja = ""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET")
			{
				$condicionExiste = array(
						"md5(caja.idCaja)" => $idCaja,
				);
				$joinFacDet = array(
						array(
								"tabla" => "cajaDocumento",
								"condicion" =>"caja.idCaja = cajaDocumento.idCajaCajaDocumento AND cajaDocumento.estadoCajaDocumento = 'Activo'",
								"tipo" => "inner",
								"campos" => "cajaDocumento.idCajaDocumento"
						),
						array(
								"tabla" => "documento",
								"condicion" =>"cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento",
								"tipo" => "",
								"campos" => "documento.aliasDocumento, documento.nombreDocumento"
						),
				);
				$documentos = TraerDatosJoin("caja", $condicionExiste,'',$joinFacDet);

				$condicionMesa = array(
					'estadoPedido' => 'Pendiente',
				);
				$nMesas = TraerTotalDatos("pedido", $condicionMesa);
				// print_r($documentos);
				$arrayTotales = array();
				foreach ($documentos as $fila)
				{
					$aliasDocumento = $fila->aliasDocumento;
					$condicionDoc = array(
						'tipoDocumentoFactura' => $aliasDocumento,
						'md5(idCorte)' => $idCorte,
						'md5(idTurno)' => $idTurno,
					);
					$total = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",$condicionDoc)[0]["dato"];
					$maximo = TraerUnDatoIndividual("factura","MAX(numeroDocumentoFactura) as maximo",$condicionDoc)[0]["maximo"];
					$minimo = TraerUnDatoIndividual("factura","MIN(numeroDocumentoFactura) as minimo",$condicionDoc)[0]["minimo"];
					$nDatos = TraerUnDatoIndividual("factura","count(*) as nDatos",$condicionDoc)[0]["nDatos"];
					if($maximo == "")
					{
						$maximo = 0;
					}
					if($minimo == "")
					{
						$minimo = 0;
					}
					if($total == "")
					{
						$total = 0;
					}
					$arrayLista = array(
						'nombreDocumento' => $fila->nombreDocumento,
						'total' => $total,
						'maximo' => $maximo,
						'minimo' => $minimo,
						'nDatos' => $nDatos,
						'aliasDocumento' => $fila->aliasDocumento,
					);

					array_push($arrayTotales, $arrayLista);
				}

				$condicionDocMovEntrada = array(
					'tipoCajaMovimiento' => "Entrada",
					'md5(idCorte)' => $idCorte,
					'md5(idTurnoCajaMovimiento)' => $idTurno,
				);
				$entrada = TraerUnDatoIndividual("cajaMovimiento","SUM(montoCajaMovimiento) as entrada",$condicionDocMovEntrada)[0]["entrada"];

				$condicionDocMovSalida = array(
					'tipoCajaMovimiento' => "Salida",
					'md5(idCorte)' => $idCorte,
					'md5(idTurnoCajaMovimiento)' => $idTurno,
				);
				$salida = TraerUnDatoIndividual("cajaMovimiento","SUM(montoCajaMovimiento) as salida",$condicionDocMovSalida)[0]["salida"];

				$montoApertura = TraerUnDatoIndividual("corteTurno","montoTurnoCorteCaja",array("md5(idTurno)" => $idTurno))[0]["montoTurnoCorteCaja"];
				$revisionInsumo = TraerUnDatoIndividual("corteCaja","revisionInsumo",array("md5(idCorteCaja)" => $idCorte))[0]["revisionInsumo"];
				// print_r($arrayTotales);
				$titulo = "Realizar Corte";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-exchange",
					"controlador"=> $this->controlador,
					"proceso"=> "Corte",
					"datosTotales"=> $arrayTotales,
					"entrada"=> $entrada,
					"salida"=> $salida,
					"montoApertura"=> $montoApertura,
					"idCorte"=> $idCorte,
					"idTurno"=> $idTurno,
					"revisionInsumo"=> $revisionInsumo,
					"nMesas"=> $nMesas,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/corteAdmin.js"
					),
				);
				GblPlantilla("corte/RealizarCorte",$datosVista,$extras,$titulo);
			}
			else if($this->input->method(TRUE) == "POST")
			{
				$datosTotales = $this->input->post("datosTotales");
				$idCorte = $this->input->post("idCorte");
				$idTurno = $this->input->post("idTurno");
				$idCorteReal = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("md5(idCorteCaja)" => $idCorte))[0]["idCorteCaja"];
				$idCajaReal = TraerUnDatoIndividual("corteCaja","idCaja",array("md5(idCorteCaja)" => $idCorte))[0]["idCaja"];
				$idTurnoReal = TraerUnDatoIndividual("corteTurno","idTurno",array("md5(idTurno)" => $idTurno))[0]["idTurno"];
				$idUsuarioTurno = TraerUnDatoIndividual("corteTurno","idUsuarioCorteTurno",array("md5(idTurno)" => $idTurno))[0]["idUsuarioCorteTurno"];
				$montoEfectivoCaja = $this->input->post("montoEfectivoCaja");
				$montoEfectivoDiferencia = $this->input->post("montoEfectivoDiferencia");
				$montoEfectivo = $this->input->post("montoEfectivo");
				$entrada = $this->input->post("entrada");
				$salida = $this->input->post("salida");
				$totalDocumentos = $this->input->post("totalDocumentos");
				$montoApertura = $this->input->post("montoApertura");
				$tipoCorte = $this->input->post("tipoCorte");
				if($tipoCorte == "Z")
				{
					$estadoCorte = "Finalizado";
					$datosLiberarCaja = array(
						'aperturaCaja' => 0,
						'turnoCaja' => 0,
					);
					$condicionLiberarCaja = array('idCaja' => $idCajaReal);
					$liberarCaja = EditarDatos("caja", $datosLiberarCaja, $condicionLiberarCaja);
				}
				else
				{
					$estadoCorte = "Vigente";
				}
				$datosCorteCaja = array(
					"totalCorte"=>$montoEfectivo,
					"fechaHoraCorteCierre"=>date("Y-m-d H:i:s"),
					"diferenciaCorte"=>$montoEfectivoDiferencia,
					"montoCorte"=>$montoEfectivoCaja,
					// "idTurnoVigente"=>0,
					"estadoCorte"=>$estadoCorte,
				);
				IniciarTransaccion();
				$condicionCorte = array('md5(idCorteCaja)' => $idCorte,);
				$editarCorte = EditarDatos("corteCaja", $datosCorteCaja, $condicionCorte);
				if($editarCorte){
					$datosCorteTurno = array(
						'montoTurnoCierreCorteCaja' => $montoEfectivoCaja,
						'montoTurnoCierreCorteCajaDiferencia' => $montoEfectivoDiferencia,
						'totalTurnoCierreCorteCaja' => $montoEfectivoDiferencia,
						'fechaHoraCierreCorteTurno' => date("Y-m-d H:i:s"),
						"estadoCorteTurno"=>$estadoCorte,
					);
					$condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
					$editarCorte = EditarDatos("corteTurno", $datosCorteTurno, $condicionCorteTurno);
					if($editarCorte)
					{
						$datosCorteHistorial = array(
							'idCorte' => $idCorteReal,
							'idUsuarioCorteHistorial' => $idUsuarioTurno,
							'idSucursalCorteHistorial' => $this->session->idSucursal,
							'idTurnoCorteHistorial' => $idTurnoReal,
							'idCajaCorteHistorial' => $idCajaReal,
							'diferenciaTurnoCorteHistorial' => $montoEfectivoDiferencia,
							'montoAperturaTurnoCorteHistorial' => $montoApertura,
							'totalCorteHistorial' => $montoEfectivo,
							'tipoCorteHistorial' => $tipoCorte,
						);
						// $condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
						$guardarCorteHistorial = GuardarDatos("corteHistorial", $datosCorteHistorial);
						if ($guardarCorteHistorial)
						{
							$datosTotales = json_decode($datosTotales, true);
							// print_r($datosTotales);

							foreach ($datosTotales as $fila)
							{
								$nombreDocumento = $fila["nombreDocumento"];
								$minimo = $fila["minimo"];
								$maximo = $fila["maximo"];
								$nDatos = $fila["nDatos"];
								$total = $fila["total"];
								$aliasDocumento = $fila["aliasDocumento"];
								$datosCorteHistorialDoc = array(
									'idCorte' => $idCorteReal,
									'idTurno' => $idTurnoReal,
									'idCorteHistorial' => $guardarCorteHistorial,
									'tipoCorteDocumento' => $aliasDocumento,
									'inicioDocumento' => $minimo,
									'finDocumento' => $maximo,
									'totalNumeroDocumento' => $nDatos,
									'totalDocumento' => $total,
									'tipoDocumentoNombre' => $nombreDocumento,
									'estadoDocumento' => "Activo",
								);
								// $condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
								$guardarCorteHistorialDoc = GuardarDatos("corteHistorialDocumento", $datosCorteHistorialDoc);
								// $totalDocumentos += $fila["total"];
							}
						}
					}
					
					if($tipoCorte == "Z")
					{
						$datosZonaMesa = array(
							"estadoZonaMesa"=>"Activo",
						);
						$condicionZonaMesa = array('estadoZonaMesa' => "Ocupada");
						$editarZonaMesa = EditarDatos("zonaMesa", $datosZonaMesa, $condicionZonaMesa);
					}
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}

				echo json_encode($datosRespuesta);
			}
		}
	}

	function RealizarCierreTurno($idCorte = "", $idTurno = "", $idCaja = ""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET")
			{
				$condicionExiste = array(
						"md5(caja.idCaja)" => $idCaja,
				);
				$joinFacDet = array(
						array(
								"tabla" => "cajaDocumento",
								"condicion" =>"caja.idCaja = cajaDocumento.idCajaCajaDocumento AND cajaDocumento.estadoCajaDocumento = 'Activo'",
								"tipo" => "inner",
								"campos" => "cajaDocumento.idCajaDocumento"
						),
						array(
								"tabla" => "documento",
								"condicion" =>"cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento",
								"tipo" => "",
								"campos" => "documento.aliasDocumento, documento.nombreDocumento"
						),
				);
				$documentos = TraerDatosJoin("caja", $condicionExiste,'',$joinFacDet);
				// print_r($documentos);
				$arrayTotales = array();
				foreach ($documentos as $fila)
				{
					$aliasDocumento = $fila->aliasDocumento;
					$condicionDoc = array(
						'tipoDocumentoFactura' => $aliasDocumento,
						'md5(idCorte)' => $idCorte,
						'md5(idTurno)' => $idTurno,
					);
					$total = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",$condicionDoc)[0]["dato"];
					$maximo = TraerUnDatoIndividual("factura","MAX(numeroDocumentoFactura) as maximo",$condicionDoc)[0]["maximo"];
					$minimo = TraerUnDatoIndividual("factura","MIN(numeroDocumentoFactura) as minimo",$condicionDoc)[0]["minimo"];
					$nDatos = TraerUnDatoIndividual("factura","count(*) as nDatos",$condicionDoc)[0]["nDatos"];
					if($maximo == "")
					{
						$maximo = 0;
					}
					if($minimo == "")
					{
						$minimo = 0;
					}
					if($total == "")
					{
						$total = 0;
					}
					$arrayLista = array(
						'nombreDocumento' => $fila->nombreDocumento,
						'total' => $total,
						'maximo' => $maximo,
						'minimo' => $minimo,
						'nDatos' => $nDatos,
						'aliasDocumento' => $fila->aliasDocumento,
					);

					array_push($arrayTotales, $arrayLista);
				}

				$condicionDocMovEntrada = array(
					'tipoCajaMovimiento' => "Entrada",
					'md5(idCorte)' => $idCorte,
					'md5(idTurnoCajaMovimiento)' => $idTurno,
				);
				$entrada = TraerUnDatoIndividual("cajaMovimiento","SUM(montoCajaMovimiento) as entrada",$condicionDocMovEntrada)[0]["entrada"];

				$condicionDocMovSalida = array(
					'tipoCajaMovimiento' => "Salida",
					'md5(idCorte)' => $idCorte,
					'md5(idTurnoCajaMovimiento)' => $idTurno,
				);
				$salida = TraerUnDatoIndividual("cajaMovimiento","SUM(montoCajaMovimiento) as salida",$condicionDocMovSalida)[0]["salida"];

				$montoApertura = TraerUnDatoIndividual("corteTurno","montoTurnoCorteCaja",array("md5(idTurno)" => $idTurno))[0]["montoTurnoCorteCaja"];
				// print_r($arrayTotales);
				$titulo = "Realizar Corte";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-exchange",
					"controlador"=> $this->controlador,
					"proceso"=> "Corte",
					"datosTotales"=> $arrayTotales,
					"entrada"=> $entrada,
					"salida"=> $salida,
					"montoApertura"=> $montoApertura,
					"idCorte"=> $idCorte,
					"idTurno"=> $idTurno,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/corteAdmin.js"
					),
				);
				GblPlantilla("corte/RealizarCierreTurno",$datosVista,$extras,$titulo);
			}
			else if($this->input->method(TRUE) == "POST")
			{
				$datosTotales = $this->input->post("datosTotales");
				$idCorte = $this->input->post("idCorte");
				$idTurno = $this->input->post("idTurno");
				$idCorteReal = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("md5(idCorteCaja)" => $idCorte))[0]["idCorteCaja"];
				$idCajaReal = TraerUnDatoIndividual("corteCaja","idCaja",array("md5(idCorteCaja)" => $idCorte))[0]["idCaja"];
				$idTurnoReal = TraerUnDatoIndividual("corteTurno","idTurno",array("md5(idTurno)" => $idTurno))[0]["idTurno"];
				$idUsuarioTurno = TraerUnDatoIndividual("corteTurno","idUsuarioCorteTurno",array("md5(idTurno)" => $idTurno))[0]["idUsuarioCorteTurno"];
				$montoEfectivoCaja = $this->input->post("montoEfectivoCaja");
				$montoEfectivoDiferencia = $this->input->post("montoEfectivoDiferencia");
				$montoEfectivo = $this->input->post("montoEfectivo");
				$entrada = $this->input->post("entrada");
				$salida = $this->input->post("salida");
				$totalDocumentos = $this->input->post("totalDocumentos");
				$montoApertura = $this->input->post("montoApertura");
				$tipoCorte = $this->input->post("tipoCorte");
				if($tipoCorte == "Z")
				{
					$estadoCorte = "Finalizado";
				}
				else
				{
					$estadoCorte = "Vigente";
				}
				$datosLiberarCaja = array(
					// 'aperturaCaja' => 0,
					'turnoCaja' => 0,
				);
				$condicionLiberarCaja = array('idCaja' => $idCajaReal);
				$liberarCaja = EditarDatos("caja", $datosLiberarCaja, $condicionLiberarCaja);
				$datosCorteCaja = array(
					// "totalCorte"=>$montoEfectivo,
					// "fechaHoraCorteCierre"=>date("Y-m-d H:i:s"),
					// "diferenciaCorte"=>$montoEfectivoDiferencia,
					// "montoCorte"=>$montoEfectivoCaja,
					"idTurnoVigente"=>0,
					// "estadoCorte"=>$estadoCorte,
				);
				IniciarTransaccion();
				$condicionCorte = array('md5(idCorteCaja)' => $idCorte,);
				$editarCorte = EditarDatos("corteCaja", $datosCorteCaja, $condicionCorte);
				if($editarCorte){
					$datosCorteTurno = array(
						'montoTurnoCierreCorteCaja' => $montoEfectivoCaja,
						'montoTurnoCierreCorteCajaDiferencia' => $montoEfectivoDiferencia,
						'totalTurnoCierreCorteCaja' => $montoEfectivoDiferencia,
						'fechaHoraCierreCorteTurno' => date("Y-m-d H:i:s"),
						"estadoCorteTurno"=>"Finalizado",
					);
					$condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
					$editarCorte = EditarDatos("corteTurno", $datosCorteTurno, $condicionCorteTurno);
					if($editarCorte)
					{
						$datosCorteHistorial = array(
							'idCorte' => $idCorteReal,
							'idUsuarioCorteHistorial' => $idUsuarioTurno,
							'idSucursalCorteHistorial' => $this->session->idSucursal,
							'idTurnoCorteHistorial' => $idTurnoReal,
							'idCajaCorteHistorial' => $idCajaReal,
							'diferenciaTurnoCorteHistorial' => $montoEfectivoDiferencia,
							'montoAperturaTurnoCorteHistorial' => $montoApertura,
							'totalCorteHistorial' => $montoEfectivo,
							'tipoCorteHistorial' => $tipoCorte,
						);
						// $condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
						$guardarCorteHistorial = GuardarDatos("corteHistorial", $datosCorteHistorial);
						if ($guardarCorteHistorial)
						{
							$datosTotales = json_decode($datosTotales, true);
							// print_r($datosTotales);

							foreach ($datosTotales as $fila)
							{
								$nombreDocumento = $fila["nombreDocumento"];
								$minimo = $fila["minimo"];
								$maximo = $fila["maximo"];
								$nDatos = $fila["nDatos"];
								$total = $fila["total"];
								$aliasDocumento = $fila["aliasDocumento"];
								$datosCorteHistorialDoc = array(
									'idCorte' => $idCorteReal,
									'idTurno' => $idTurnoReal,
									'idCorteHistorial' => $guardarCorteHistorial,
									'tipoCorteDocumento' => $aliasDocumento,
									'inicioDocumento' => $minimo,
									'finDocumento' => $maximo,
									'totalNumeroDocumento' => $nDatos,
									'totalDocumento' => $total,
									'tipoDocumentoNombre' => $nombreDocumento,
									'estadoDocumento' => "Activo",
								);
								// $condicionCorteTurno = array('md5(idCorte)' => $idCorte, "md5(idTurno) => $idTurno");
								$guardarCorteHistorialDoc = GuardarDatos("corteHistorialDocumento", $datosCorteHistorialDoc);
								// $totalDocumentos += $fila["total"];
							}
						}
					}
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}

				echo json_encode($datosRespuesta);
			}
		}
	}

	function AperturaTurno($idCorte = ""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$idSucursal = $this->session->idSucursal;
				$condicionExiste = array(
						"idSucursalCaja" => $idSucursal,
						"aperturaCaja" => 1,
						"turnoCaja" => 0,
				);
				$cajas = TraerDatos('caja', $condicionExiste);
				$condicionUser = array("idSucursalUsuario" => $idSucursal,);
				$usuarios = TraerDatos("usuario", $condicionUser);

				$titulo = "Apertura Turno";
				$datosVista = array(
						"titulo" => $titulo,
						"icono" => "fas fa-plus",
						"controlador" => $this->controlador,
						"proceso" => "Apertura",
						"caja" => $cajas,
						"usuarios" => $usuarios,
						"admin" => $this->session->admin,
						"usuarioid" => $this->session->idUsuario,
						"idCorte" => $idCorte,
				);
				$extras = array(
						'css' => array(),
						'js' => array(
								"scripts/corteAdmin.js"
						),
				);
				$this->load->view("corte/AperturaTurno",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$montoApertura = $this->input->post("montoApertura");
				$usuarioApertura = $this->input->post("usuarioApertura");
				$cajaApertura = $this->input->post("cajaApertura");
				$idCorte = $this->input->post("idCorte");

				$idCorteReal = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("md5(idCorteCaja)" => $idCorte))[0]["idCorteCaja"];
				$ultimoTurno = TraerMaxValor("corteTurno", "corteTurno", array("md5(idCorte)" => $idCorte));
				$turnoVigente = $ultimoTurno+1;
				$datosTurno = array(
					"idCorte"=>$idCorteReal,
					"montoTurnoCorteCaja"=>$montoApertura,
					"idUsuarioCorteTurno"=>$usuarioApertura,
					"corteTurno" => $turnoVigente,
					// "idSucursalCorte"=>$this->session->idSucursal,
					"fechaCorteTurno"=>date("Y-m-d"),
					"horaCorteTurno"=>date("H:i:s"),
					"estadoCorteTurno"=>"Vigente",
				);
				$guardarTurno = GuardarDatos("corteTurno",$datosTurno);
				if($guardarTurno)
				{
					$dataCorte = array(
						'idTurnoVigente' => $guardarTurno,
						'idUsuarioCorte' => $this->session->idUsuario,
					);
					$condicionCorte = array('idCorteCaja' => $idCorteReal,);
					$editarCorte = EditarDatos($this->tabla, $dataCorte, $condicionCorte);
					if ($editarCorte)
					{
						$datosCaja = array(
							'aperturaCaja' => 1,
							'turnoCaja' => 1,
						);
						$condicionCaja = array('idCaja' => $cajaApertura,);
						$editarCorte = EditarDatos("caja", $datosCaja, $condicionCaja);
					}

					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				}
				else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function AperturaTurnoUsuario(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$idSucursal = $this->session->idSucursal;
				$condicionExiste = array(
						"idSucursalCaja" => $idSucursal,
						"aperturaCaja" => 1,
						"turnoCaja" => 0,
				);
				$cajas = TraerDatos('caja', $condicionExiste);
				$condicionUser = array("idSucursalUsuario" => $idSucursal,);
				$usuarios = TraerDatos("usuario", $condicionUser);

				$titulo = "Apertura Turno";
				$datosVista = array(
						"titulo" => $titulo,
						"icono" => "fas fa-plus",
						"controlador" => $this->controlador,
						"proceso" => "Apertura",
						"caja" => $cajas,
						"usuarios" => $usuarios,
						"admin" => $this->session->admin,
						"usuarioid" => $this->session->idUsuario,
						// "idCorte" => $idCorte,
				);
				$extras = array(
						'css' => array(),
						'js' => array(
								"scripts/corteAdmin.js"
						),
				);
				$this->load->view("corte/AperturaTurnoUsuario",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$montoApertura = $this->input->post("montoApertura");
				$usuarioApertura = $this->input->post("usuarioApertura");
				$cajaApertura = $this->input->post("cajaApertura");
				// $idCorte = $this->input->post("idCorte");

				$idCorteReal = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("idCaja" => $cajaApertura, "estadoCorte" => "Vigente"))[0]["idCorteCaja"];
				$ultimoTurno = TraerMaxValor("corteTurno", "corteTurno", array("idCorte" => $idCorteReal));
				$turnoVigente = $ultimoTurno+1;
				$datosTurno = array(
					"idCorte"=>$idCorteReal,
					"montoTurnoCorteCaja"=>$montoApertura,
					"idUsuarioCorteTurno"=>$usuarioApertura,
					"corteTurno" => $turnoVigente,
					// "idSucursalCorte"=>$this->session->idSucursal,
					"fechaCorteTurno"=>date("Y-m-d"),
					"horaCorteTurno"=>date("H:i:s"),
					"estadoCorteTurno"=>"Vigente",
				);
				$guardarTurno = GuardarDatos("corteTurno",$datosTurno);
				if($guardarTurno)
				{
					$dataCorte = array(
						'idTurnoVigente' => $guardarTurno,
						'idUsuarioCorte' => $this->session->idUsuario,
					);
					$condicionCorte = array('idCorteCaja' => $idCorteReal,);
					$editarCorte = EditarDatos($this->tabla, $dataCorte, $condicionCorte);
					if ($editarCorte)
					{
						$datosCaja = array(
							'aperturaCaja' => 1,
							'turnoCaja' => 1,
						);
						$condicionCaja = array('idCaja' => $cajaApertura,);
						$editarCorte = EditarDatos("caja", $datosCaja, $condicionCaja);
					}

					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				}
				else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function RevisionInventario($idCorte = "")
	{
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionExiste = array(
						"insumo.revisarInsumo" => 1,
						"insumoPresentacion.unidadInventarioInsumoPresentacion" => 1,
				);
				$joinFacDet = array(
						array(
								"tabla" => "insumoStock",
								"condicion" =>"insumo.idInsumo = insumoStock.idInsumo",
								"tipo" => "",
								"campos" => "insumoStock.cantidadInsumoStock"
						),
						array(
								"tabla" => "insumoPresentacion",
								"condicion" =>"insumo.idInsumo = insumoPresentacion.idInsumo",
								"tipo" => "",
								"campos" => "insumoPresentacion.descripcionInsumoPresentacion, insumoPresentacion.unidadInsumoPresentacion, insumoPresentacion.idInsumoPresentacion, insumoPresentacion.costoInsumoPresentacion, insumoPresentacion.precioInsumoPresentacion"
						),
						array(
								"tabla" => "presentacion",
								"condicion" =>"insumoPresentacion.idPresentacion = presentacion.idPresentacion",
								"tipo" => "",
								"campos" => "presentacion.nombrePresentacion"
						),
				);
				$insumo = TraerDatosJoin("insumo", $condicionExiste,'',$joinFacDet);

				$condicionCategoria = array('estadoInsumoCategoria' => 'Activo', );
				$categorias = TraerDatos("insumoCategoria", $condicionCategoria);
				$titulo = "Revision de Inventario";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-exchange",
					"controlador"=> $this->controlador,
					"proceso"=> "Ver",
					"insumo" => $insumo,
					"idCorte" => $idCorte,
					"categorias" => $categorias,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/corteAdmin.js"
					),
				);
				GblPlantilla("corte/RevisionInventario",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$datos = json_decode($this->input->post("datos"), true);
				$idCorte = $this->input->post("idCorte");
				$cuantos = $this->input->post("cuantos");
				$ajustar = $this->input->post("ajustar");
				$n = 0;
				if ($ajustar > 0)
				{
					$tabla_movimiento='insumoMovimiento';
					$form_data_movimiento = array(
						'categoriaInsumoMovimiento' => 'Ajuste',
						'tipoMovimientoInsumo' => "Ajuste",
						'descripcionInsumoMovimiento' => "Ajuste por apertura de caja",
						'fechaHoraInsumoMovimiento' => date("Y-m-d H:i:s"),
						'fechaRegistroInsumoMovimiento' => date("Y-m-d H:i:s"),
						'idUsuarioInsumoMovimiento' => $this->session->idUsuario,
						'idSucursalInsumoMovimiento' => $this->session->idSucursal,
						"estadoInsumoMovimiento" => "Activo",
					);
					$guardarMovimiento = GuardarDatos($tabla_movimiento,$form_data_movimiento);
					if($guardarMovimiento)
					{
						foreach ($datos as $fila)
						{
							$idInsumo = $fila["idInsumo"];
							$nombreInsumo = $fila["nombreInsumo"];
							$descripcionInsumo = $fila["descripcionInsumo"];
							$cantidadInsumoStock = $fila["cantidadInsumoStock"];
							$unidadInsumoPresentacion = $fila["unidadInsumoPresentacion"];
							$idInsumoPresentacion = $fila["idInsumoPresentacion"];
							$descripcionInsumoPresentacion = $fila["descripcionInsumoPresentacion"];
							$existenciaMinima = $fila["existenciaMinima"];
							$existenciaReal = $fila["existenciaReal"];
							$costoInsumoPresentacion = $fila["costoInsumoPresentacion"];
							$precioInsumoPresentacion = $fila["precioInsumoPresentacion"];

							if ($existenciaReal != "")
							{
								$datosDetalle = array(
									'idInsumoMovimiento' => $guardarMovimiento,
									'idInsumo' => $idInsumo,
									'cantidadInsumoMovimientoDetalle' => number_format($existenciaReal*$unidadInsumoPresentacion, 2,'.',','),
									'stockAnteriorInsumoMovimientoDetalle' => $existenciaMinima,
									'stockActualInsumoMovimientoDetalle' => number_format($existenciaReal*$unidadInsumoPresentacion, 2,'.',','),
									'descripcionInsumoMovimientoDetalle' => "",
									'costoInsumoMovimientoDetalle' => $costoInsumoPresentacion,
									'precioInsumoMovimientoDetalle' => $precioInsumoPresentacion,
									'idPresentacionInsumoMovimientoDetalle' => $idInsumoPresentacion,
									'idUsuarioInsumoMovimientoDetalle' => $this->session->idUsuario,
									'estadoInsumoMovimientoDetalle' => 'Activo',
								);
								$guardarDetalle = GuardarDatos("insumoMovimientoDetalle",$datosDetalle);
								if ($guardarDetalle)
								{
									$condicionStock = array(
										'idSucursalInsumoStock' => $this->session->idSucursal,
										'idInsumo' => $idInsumo,
									);
									$datosStock = array(
										'cantidadInsumoStock' => $existenciaReal*$unidadInsumoPresentacion,
									);
									$guardarStock = EditarDatos("insumoStock",$datosStock,$condicionStock);
									if($guardarStock)
									{
										$datosRevision = array(
											"idCorte"=>$idCorte,
											"idInsumo"=>$idInsumo,
											"existenciaInicioInsumo"=>$existenciaReal,
											"idInsumoPresentacion"=>$idInsumoPresentacion,
											"descripcionInsumoPresentacion"=>$descripcionInsumoPresentacion,
											"unidadInsumoPresentacion"=>$unidadInsumoPresentacion,
											"existenciaMinima"=>$existenciaMinima,
											"existenciaReal"=>$existenciaReal,
										);
										$guardarInsumos = GuardarDatos("corteRevisionInsumo",$datosRevision);
										if($guardarInsumos)
										{
											$n+=1;
										}
									}
								}
							}
							else
							{
								$datosRevision = array(
									"idCorte"=>$idCorte,
									"idInsumo"=>$idInsumo,
									"existenciaInicioInsumo"=>$cantidadInsumoStock,
									"idInsumoPresentacion"=>$idInsumoPresentacion,
									"descripcionInsumoPresentacion"=>$descripcionInsumoPresentacion,
									"unidadInsumoPresentacion"=>$unidadInsumoPresentacion,
									"existenciaMinima"=>$existenciaMinima,
									"existenciaReal"=>$existenciaReal,
								);
								$guardar = GuardarDatos("corteRevisionInsumo",$datosRevision);
								if($guardar)
								{
									$n+=1;
								}
							}
						}
					}
				}
				else
				{
					foreach ($datos as $fila)
					{
						$idInsumo = $fila["idInsumo"];
						$nombreInsumo = $fila["nombreInsumo"];
						$descripcionInsumo = $fila["descripcionInsumo"];
						$cantidadInsumoStock = $fila["cantidadInsumoStock"];
						$unidadInsumoPresentacion = $fila["unidadInsumoPresentacion"];
						$idInsumoPresentacion = $fila["idInsumoPresentacion"];
						$descripcionInsumoPresentacion = $fila["descripcionInsumoPresentacion"];
						$existenciaMinima = $fila["existenciaMinima"];
						$existenciaReal = $fila["existenciaReal"];
						$datosRevision = array(
							"idCorte"=>$idCorte,
							"idInsumo"=>$idInsumo,
							"existenciaInicioInsumo"=>$cantidadInsumoStock,
							"idInsumoPresentacion"=>$idInsumoPresentacion,
							"descripcionInsumoPresentacion"=>$descripcionInsumoPresentacion,
							"unidadInsumoPresentacion"=>$unidadInsumoPresentacion,
							"existenciaMinima"=>$existenciaMinima,
						);
						$guardar = GuardarDatos("corteRevisionInsumo",$datosRevision);
						if($guardar)
						{
							$n+=1;
						}
					}
				}
				if($cuantos == $n)
				{
					$datosRC = array(
						'revisionInsumo' => 1,
					);
					$condicionRC = array('idCorteCaja' => $idCorte,);
					$editarRC = EditarDatos("corteCaja", $datosRC, $condicionRC);

					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				}
				else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function RevisionInventarioFinal($idCorte = "")
	{
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$datosMostrar = array();
				$condicionLista = array(
					'md5(idCorte)' => $idCorte,
				);
				$lista = TraerDatos("corteRevisionInsumo", $condicionLista);
				foreach ($lista as $key)
				{
					$idInsumo = $key->idInsumo;
					$existenciaInicioInsumo = $key->existenciaInicioInsumo;
					$idRevisionInsumo = $key->idRevisionInsumo;
					$descripcionInsumoPresentacion = $key->descripcionInsumoPresentacion;
					$unidadInsumoPresentacion = $key->unidadInsumoPresentacion;
					$idInsumoPresentacion = $key->idInsumoPresentacion;
					$existenciaMinima = $key->existenciaMinima;
					$condicionExiste = array(
							"insumo.idInsumo" => $idInsumo,
					);
					$joinFacDet = array(
							array(
									"tabla" => "insumoStock",
									"condicion" =>"insumo.idInsumo = insumoStock.idInsumo",
									"tipo" => "",
									"campos" => "insumoStock.cantidadInsumoStock"
							),
					);
					$insumo = TraerDatosJoin("insumo", $condicionExiste,'',$joinFacDet);
					foreach ($insumo as $fila)
					{
						$nombreInsumo = $fila->nombreInsumo;
						$descripcionInsumo = $fila->descripcionInsumo;
						$cantidadInsumoStock = $fila->cantidadInsumoStock;
						$idCategoriaInsumo = $fila->idCategoriaInsumo;
					}

					$diferencia = $existenciaInicioInsumo - (number_format($cantidadInsumoStock/$unidadInsumoPresentacion, 2, '.',','));

					$final = array(
						'idInsumo' => $idInsumo,
						'descripcionInsumo' => $descripcionInsumo,
						'nombreInsumo' => $nombreInsumo,
						'cantidadInsumoStock' => $cantidadInsumoStock,
						'idRevisionInsumo' => $idRevisionInsumo,
						'existenciaInicioInsumo' => $existenciaInicioInsumo,
						'diferencia' => $diferencia,
						'descripcionInsumoPresentacion' => $descripcionInsumoPresentacion,
						'unidadInsumoPresentacion' => $unidadInsumoPresentacion,
						'idInsumoPresentacion' => $idInsumoPresentacion,
						'existenciaMinima' => $existenciaMinima,
						'idCategoriaInsumo' => $idCategoriaInsumo,
					);
					array_push($datosMostrar, $final);
				}
				$condicionCategoria = array('estadoInsumoCategoria' => 'Activo', );
				$categorias = TraerDatos("insumoCategoria", $condicionCategoria);
				$titulo = "Revision de Inventario";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-exchange",
					"controlador"=> $this->controlador,
					"proceso"=> "Ver",
					"insumo" => $insumo,
					"idCorte" => $idCorte,
					"datosMostrar" => $datosMostrar,
					"categorias" => $categorias,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/corteAdmin.js"
					),
				);
				GblPlantilla("corte/RevisionInventarioFinal",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$datos = json_decode($this->input->post("datos"), true);
				$idCorte = $this->input->post("idCorte");
				$cuantos = $this->input->post("cuantos");
				$ajustar = $this->input->post("ajustar");
				$n = 0;
				if ($ajustar > 0)
				{
					$tabla_movimiento='insumoMovimiento';
					$form_data_movimiento = array(
						'categoriaInsumoMovimiento' => 'Ajuste',
						'tipoMovimientoInsumo' => "Ajuste",
						'descripcionInsumoMovimiento' => "Ajuste por apertura de caja",
						'fechaHoraInsumoMovimiento' => date("Y-m-d H:i:s"),
						'fechaRegistroInsumoMovimiento' => date("Y-m-d H:i:s"),
						'idUsuarioInsumoMovimiento' => $this->session->idUsuario,
						'idSucursalInsumoMovimiento' => $this->session->idSucursal,
						"estadoInsumoMovimiento" => "Activo",
					);
					$guardarMovimiento = GuardarDatos($tabla_movimiento,$form_data_movimiento);
					if ($guardarMovimiento)
					{
						foreach ($datos as $fila)
						{
							$idInsumo = $fila["idInsumo"];
							$nombreInsumo = $fila["nombreInsumo"];
							$descripcionInsumo = $fila["descripcionInsumo"];
							$unidadInsumoPresentacion = $fila["unidadInsumoPresentacion"];
							$idInsumoPresentacion = $fila["idInsumoPresentacion"];
							// $cantidadInsumoStock = $fila["cantidadInsumoStock"];
							$diferencia = $fila["diferencia"];
							$idRevisionInsumo = $fila["idRevisionInsumo"];
							$insumoActual = $fila["insumoActual"];
							$existenciaReal = $fila["existenciaReal"];
							if ($existenciaReal != "")
							{
								$datosDetalle = array(
									'idInsumoMovimiento' => $guardarMovimiento,
									'idInsumo' => $idInsumo,
									'cantidadInsumoMovimientoDetalle' => number_format($existenciaReal*$unidadInsumoPresentacion, 2,'.',','),
									'stockAnteriorInsumoMovimientoDetalle' => $insumoActual,
									'stockActualInsumoMovimientoDetalle' => number_format($existenciaReal*$unidadInsumoPresentacion, 2,'.',','),
									'descripcionInsumoMovimientoDetalle' => "",
									// 'costoInsumoMovimientoDetalle' => $costoInsumoPresentacion,
									// 'precioInsumoMovimientoDetalle' => $precioInsumoPresentacion,
									'idPresentacionInsumoMovimientoDetalle' => $idInsumoPresentacion,
									'idUsuarioInsumoMovimientoDetalle' => $this->session->idUsuario,
									'estadoInsumoMovimientoDetalle' => 'Activo',
								);
								$guardarDetalle = GuardarDatos("insumoMovimientoDetalle",$datosDetalle);
								if ($guardarDetalle)
								{

									$condicionStock = array(
										'idSucursalInsumoStock' => $this->session->idSucursal,
										'idInsumo' => $idInsumo,
									);
									$datosStock = array(
										'cantidadInsumoStock' => $existenciaReal*$unidadInsumoPresentacion,

									);
									$guardarStock = EditarDatos("insumoStock",$datosStock,$condicionStock);
									if($guardarStock)
									{
										$datosRevision = array(
											"existenciaFinInsumo"=>$existenciaReal,
											"diferenciaInsumo" => $diferencia,
											"aleatorioCorteRevisionInsumo" => uniqid(),
										);
										$condicionRevision = array('idRevisionInsumo' => $idRevisionInsumo,);
										$editarRevision = EditarDatos("corteRevisionInsumo", $datosRevision, $condicionRevision);
										if($editarRevision)
										{
											$n+=1;
										}
									}
								}
							}
							else
							{

								$datosRevision = array(
									// "idCorte"=>$idCorte,
									// "idInsumo"=>$idInsumo,
									"existenciaFinInsumo"=>$insumoActual,
									"diferenciaInsumo" => $diferencia,
									"aleatorioCorteRevisionInsumo" => uniqid(),
								);
								$condicionRevision = array('idRevisionInsumo' => $idRevisionInsumo,);
								$editarRevision = EditarDatos("corteRevisionInsumo", $datosRevision, $condicionRevision);
								if($editarRevision){
									$n+=1;
								}
							}
						}
					}
				}
				else
				{
					foreach ($datos as $fila)
					{
						$idInsumo = $fila["idInsumo"];
						$nombreInsumo = $fila["nombreInsumo"];
						$descripcionInsumo = $fila["descripcionInsumo"];
						$unidadInsumoPresentacion = $fila["unidadInsumoPresentacion"];
						// $cantidadInsumoStock = $fila["cantidadInsumoStock"];
						$diferencia = $fila["diferencia"];
						$idRevisionInsumo = $fila["idRevisionInsumo"];
						$insumoActual = $fila["insumoActual"];
						$datosRevision = array(
							// "idCorte"=>$idCorte,
							// "idInsumo"=>$idInsumo,
							"existenciaFinInsumo"=>$insumoActual,
							"diferenciaInsumo" => $diferencia,
							"aleatorioCorteRevisionInsumo" => uniqid(),
						);
						$condicionRevision = array('idRevisionInsumo' => $idRevisionInsumo,);
						$editarRevision = EditarDatos("corteRevisionInsumo", $datosRevision, $condicionRevision);
						if($editarRevision){
							$n+=1;
						}
					}
				}
				// echo $n;
				if($cuantos == $n)
				{
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				}
				else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 500;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function ReporteRevisionInsumo($idCorte=""){
    if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
      GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
    }
    else
    {

      $this->load->add_package_path(APPPATH . 'third_party/fpdf');
  		$this->load->library('pdf');
  		$this->fpdf = new Pdf();
  		$this->fpdf->SetTopMargin(-5);
  		$this->fpdf->SetLeftMargin(10);
  		//Numeracion de paginas
  		$this->fpdf->AliasNbPages();
  		//Salto automatico de pagina margen de 20 mm
  		$this->fpdf->SetAutoPageBreak(true, 10);
  		//Agrega la pagina a trabajar
  		$this->fpdf->AddPage('P','A4',0);
  		//Seteo de fuente Times New Roman 12
  		$this->fpdf->SetFont('Helvetica', 'B', 14   );

  		// $path = base_url("vendors/core/img/dms.png");
  		// $this->fpdf->Image($path, 10, 5, 30, 25);
  		$this->fpdf->Cell(192, 7,utf8_decode("DIGITALS POS"), 0, 1, "C");
  		$this->fpdf->SetFont('Helvetica', 'B', 10);
  		$this->fpdf->Cell(192, 5, utf8_decode("REPORTE DE REVISION DE INSUMOS"), 0, 1, "C");

      $this->fpdf->Ln(15);

      $array_data = array(
        // 0 => array($n,5,10,"C"),
        1 => array(utf8_decode("N°"),17,"L"),
        2 => array("NOMBRE",50,"L"),
        3 => array("PRESENTACION",35,"L"),
        4 => array("EXIST. INICIO",30,"C"),
        5 => array("EXIST. FINAL",30,"C"),
        6 => array("DIFERENCIA",30,"C"),
      );
      $this->fpdf->LineWriteB($array_data, 0);
      $getX = $this->fpdf->GetX();
      $getY = $this->fpdf->GetY();
      $this->fpdf->Line($getX, $getY, 202, $getY);
      $this->fpdf->SetFont('Helvetica', '', 12   );

			$n =1;
			$condicionLista = array(
				'md5(idCorte)' => $idCorte,
			);
			$lista = TraerDatos("corteRevisionInsumo", $condicionLista);
			foreach ($lista as $key)
			{
				$idInsumo = $key->idInsumo;
				$existenciaInicioInsumo = $key->existenciaInicioInsumo;
				$existenciaFinInsumo = $key->existenciaFinInsumo;
				$idRevisionInsumo = $key->idRevisionInsumo;
				$descripcionInsumoPresentacion = $key->descripcionInsumoPresentacion;
				$unidadInsumoPresentacion = $key->unidadInsumoPresentacion;
				$idInsumoPresentacion = $key->idInsumoPresentacion;
				$existenciaMinima = $key->existenciaMinima;
				$condicionExiste = array(
						"insumo.idInsumo" => $idInsumo,
				);
				$joinFacDet = array(
						array(
								"tabla" => "insumoStock",
								"condicion" =>"insumo.idInsumo = insumoStock.idInsumo",
								"tipo" => "",
								"campos" => "insumoStock.cantidadInsumoStock"
						),
				);
				$insumo = TraerDatosJoin("insumo", $condicionExiste,'',$joinFacDet);
				foreach ($insumo as $fila)
				{
					$nombreInsumo = $fila->nombreInsumo;
					$descripcionInsumo = $fila->descripcionInsumo;
					$cantidadInsumoStock = $fila->cantidadInsumoStock;
					$idCategoriaInsumo = $fila->idCategoriaInsumo;
				}

				$diferencia = $existenciaInicioInsumo - (number_format($cantidadInsumoStock/$unidadInsumoPresentacion, 2, '.',','));

				$array_data = array(
	        // 0 => array($n,5,10,"C"),
	        1 => array(utf8_decode($n),17,"C"),
	        2 => array($nombreInsumo,50,"L"),
	        3 => array($descripcionInsumoPresentacion,35,"L"),
	        4 => array($existenciaInicioInsumo,30,"C"),
	        5 => array($existenciaFinInsumo,30,"C"),
	        6 => array($diferencia,30,"C"),
	      );
	      $this->fpdf->LineWriteB($array_data, 0);
				$n+=1;
			}
      $getX = $this->fpdf->GetX();
      $getY = $this->fpdf->GetY();
      $this->fpdf->Line($getX, $getY, 202, $getY);
      ob_clean();
  		$this->fpdf->Output("reporte_pedido.pdf", "I");
    }
  }
}
/* End of file Usuarios.php */
