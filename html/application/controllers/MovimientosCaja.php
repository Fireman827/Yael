<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MovimientosCaja extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "cajaMovimiento";
	private $controlador = "MovimientosCaja";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
      $campos = array(
				"idCaja" => "idCategoria",
				"nombreCaja" => "nombreCategoria"
			);
			$categoria = TraerDatosRenombrados('caja',$campos ,array("estadoCaja" => 'Activo'));
			$titulo = "Movimientos Caja";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-trash",
				"botones" => array(
					array(
						"icono"=> "fa fa-minus",
						'controlador' => $this->controlador,
						'url' => 'MovimientosCajaSalida',
						'txt' => 'Salida de caja',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'cajaMovimientoSalida'
					),
					array(
						"icono"=> "fa fa-plus ",
						'controlador' => $this->controlador,
						'url' => 'MovimientosCajaIngreso',
						'txt' => 'Ingreso a caja',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'cajaMovimientoIngreso'
					),
				),
        "buscador" => true,
				"categorias" => $categoria,
				"encabezados"=>array(
					"ID"=>1,
					"Concepto"=>4,
					"Fecha"=>2,
					"Monto"=>1,
					"Empleado"=>2,
					"Tipo"=>1,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/movimientoscaja.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}
	function MovimientosCajaMostrar(){
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));
   
    $buscador = $this->input->post("buscador"); 
		$buscadorTexto = $this->input->post("busqueda"); 
		
		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = ($buscador == "1") ? $buscadorTexto :$busquedaAreglo['value'];
		$categoria = $this->input->post("categoria");

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
			0 => 'cajaMovimiento.idCajaMovimiento',
			1 => 'cajaMovimiento.conceptoCajaMovimiento',
			2 => 'cajaMovimiento.fechaRegistroCajaMovimiento',
			3 => 'cajaMovimiento.tipoCajaMovimiento',
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

    $condicionExiste = array(
      'cajaMovimiento.estadoCajaMovimiento !=' => "Borrado", 
      'cajaMovimiento.idSucursalCajaMovimiento' => $sucursal, 
      'cajaMovimiento.idCaja'=>$categoria
    );
    if ($categoria != "All") {
      $condicionExiste['cajaMovimiento.idCaja'] = $categoria;
    }

    $join = array(
      array(
        "tabla" => "usuario",
			  "condicion" => "usuario.idUsuario = cajaMovimiento.idUsuarioCajaMovimiento",
        "tipo" => "left",
        "campos" => "nombreUsuario"
			),
      array(
        "tabla" => "corteTurno",
        "condicion" =>"corteTurno.idTurno = cajaMovimiento.idTurnoCajaMovimiento",
        "tipo" => "left",
        "campos" => "corteTurno.estadoCorteTurno, corteTurno.idUsuarioCorteTurno"
      ),
		);
    
    $cajaMovimientos = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicionExiste, $join);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($cajaMovimientos != 0){
			$datosMostrar = array();
			foreach ($cajaMovimientos as $cajaMovimiento){

				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

        $funcion = "MovimientosCajaEditar";
        if (GblPermisos($this, $funcion, $this->controlador) && $cajaMovimiento->estadoCorteTurno == "Vigente") {
          if($cajaMovimiento->idUsuarioCorteTurno == $this->session->idUsuario || $this->session->admin == '1'){
            $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idCajaMovimiento=" . md5($cajaMovimiento->idCajaMovimiento) . "><i class='fa fa-edit' ></i> Editar</a>";
          }
        }
        $funcion = "MovimientosCajaBorrar";
        if (GblPermisos($this, $funcion, $this->controlador) && $cajaMovimiento->estadoCorteTurno == "Vigente") {
          if($cajaMovimiento->idUsuarioCorteTurno == $this->session->idUsuario || $this->session->admin == '1'){
            $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Borrar' idCajaMovimiento=" . md5($cajaMovimiento->idCajaMovimiento) . "><i class='fa fa-trash' ></i> Borrar</a>";
          }
        }
        $funcion = "MovimientosCajaReimprimir";
        $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Imprimir' idCajaMovimiento=" . $cajaMovimiento->idCajaMovimiento . "><i class='fa fa-print' ></i> Reimprimir</a>";

				$menuOpciones .= "
				</div>
				</div>";
				$fecha = date_create($cajaMovimiento->fechaRegistroCajaMovimiento);
				$fecha = date_format($fecha,"d-m-Y  h:i:s A");
				$datosMostrar[] = array(
					$cajaMovimiento->idCajaMovimiento,
					$cajaMovimiento->conceptoCajaMovimiento,
          $fecha,
					// $cajaMovimiento->fechaRegistroCajaMovimiento,
					"$".$cajaMovimiento->montoCajaMovimiento,
					$cajaMovimiento->nombreUsuario,
					$cajaMovimiento->tipoCajaMovimiento,
					$menuOpciones,
				);
			}
      $condicion = array(
        'cajaMovimiento.estadoCajaMovimiento !=' => "Borrado", 
        'cajaMovimiento.idSucursalCajaMovimiento' => $sucursal, 
        'cajaMovimiento.idCaja'=>$categoria
      );
      if ($categoria != "All") {
        $condicion['cajaMovimiento.idCaja'] = $categoria;
      }
			$totalCajaMovimientos = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalCajaMovimientos,
				"recordsFiltered" => $totalCajaMovimientos,
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
  function MovimientosCajaIngreso() {
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)){
        GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if($this->input->method(TRUE) == "GET") {
          if($this->session->admin == '1'){
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idSucursalCorte" => $this->session->idSucursal
            );
          } else {
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idUsuarioCorteTurno" => $this->session->idUsuario,
              "idSucursalCorte" => $this->session->idSucursal
            );
          }
          $joinFacDet = array(
            array(
              "tabla" => "corteTurno",
              "condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente AND corteTurno.estadoCorteTurno = 'Vigente'",
              "tipo" => "left",
              "campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
            ),
          );
          $apertura = TraerUnDatoJoin("corteCaja", $condicionExiste,$joinFacDet,"","corteCaja.idCaja");
          if($apertura){
            $idCorteCaja = $apertura->idCorteCaja;
            $idCaja = $apertura->idCaja;
            $idTurnoVigente = $apertura->idTurnoVigente;
          } else {
            $idCorteCaja = 0;
            $idCaja = 0;
            $idTurnoVigente = 0;
          }
          $titulo = "Ingreso Caja";
          $datosVista = array(
              "titulo" => $titulo,
              "icono" => "fas fa-plus",
              "controlador" => $this->controlador,
              "proceso" => "Ingreso",
              "idCorteCaja" => $idCorteCaja,
              "idCaja" => $idCaja,
              "idTurnoVigente" => $idTurnoVigente,
          );
          $extras = array(
              'css' => array(),
              'js' => array(
                  "scripts/movimientoscaja.js"
              ),
          );
          $this->load->view("cajaMovimientos/MovimientosCajaIngreso",$datosVista);
          // GblPlantilla("Impresoras/ImpresorasAgregar", $datosVista, $extras, $titulo);
      } else if ($this->input->method(TRUE) == "POST")
      {
        $montoIngreso = $this->input->post("montoIngreso");
        $conceptoIngreso = $this->input->post("conceptoIngreso");
        $idCorteCaja = $this->input->post("idCorteCaja");
        $idCaja = $this->input->post("idCaja");
        $idTurnoVigente = $this->input->post("idTurnoVigente");
        $entrega = $this->input->post("entregaMovimiento");
        $recibe = $this->input->post("recibeMovimiento");

        $datosImpresoras = array(
            "idSucursalCajaMovimiento" => $this->session->idSucursal,
            "idUsuarioCajaMovimiento" => $this->session->idUsuario,
            "entregaCajaMovimiento" => $entrega,
            "recibeCajaMovimiento" => $recibe,
            "montoCajaMovimiento" => $montoIngreso,
            "conceptoCajaMovimiento" => $conceptoIngreso,
            "idCaja" => $idCaja,
            "tipoCajaMovimiento" => "Entrada",
            "idTurnoCajaMovimiento" => $idTurnoVigente,
            "idCorte" => $idCorteCaja,
            "estadoCajaMovimiento" => 'Activo'
        );
        IniciarTransaccion();
        $guardar = GuardarDatos($this->tabla, $datosImpresoras);
        ($guardar == false) ? $error = true : $error = false;
        if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 402;
        } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
            $datosRespuesta["idCajaMovimiento"] = $guardar;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }
  function MovimientosCajaSalida() {
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)){
        GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if($this->input->method(TRUE) == "GET") {
          if($this->session->admin == '1'){
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idSucursalCorte" => $this->session->idSucursal
            );
          } else {
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idUsuarioCorteTurno" => $this->session->idUsuario,
              "idSucursalCorte" => $this->session->idSucursal
            );
          }
          $joinFacDet = array(
            array(
              "tabla" => "corteTurno",
              "condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente AND corteTurno.estadoCorteTurno = 'Vigente'",
              "tipo" => "left",
              "campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
            ),
          );
          $apertura = TraerUnDatoJoin("corteCaja", $condicionExiste,$joinFacDet,"","corteCaja.idCaja");
          if($apertura){
            $idCorteCaja = $apertura->idCorteCaja;
            $idCaja = $apertura->idCaja;
            $idTurnoVigente = $apertura->idTurnoVigente;
          } else {
            $idCorteCaja = 0;
            $idCaja = 0;
            $idTurnoVigente = 0;
          }
          $titulo = "Salida Caja";
          $datosVista = array(
              "titulo" => $titulo,
              "icono" => "fas fa-minus",
              "controlador" => $this->controlador,
              "proceso" => "Salida",
              "idCorteCaja" => $idCorteCaja,
              "idCaja" => $idCaja,
              "idTurnoVigente" => $idTurnoVigente,
          );
          $extras = array(
              'css' => array(),
              'js' => array(
                  "scripts/movimientoscaja.js"
              ),
          );
          $this->load->view("cajaMovimientos/MovimientosCajaSalida",$datosVista);
          // GblPlantilla("Impresoras/ImpresorasAgregar", $datosVista, $extras, $titulo);
      } else if ($this->input->method(TRUE) == "POST")
      {
        $montoIngreso = $this->input->post("montoIngreso");
        $conceptoIngreso = $this->input->post("conceptoIngreso");
        $idCorteCaja = $this->input->post("idCorteCaja");
        $idCaja = $this->input->post("idCaja");
        $idTurnoVigente = $this->input->post("idTurnoVigente");
        $entrega = $this->input->post("entregaMovimiento");
        $recibe = $this->input->post("recibeMovimiento");

        $datosImpresoras = array(
            "idSucursalCajaMovimiento" => $this->session->idSucursal,
            "idUsuarioCajaMovimiento" => $this->session->idUsuario,
            "entregaCajaMovimiento" => $entrega,
            "recibeCajaMovimiento" => $recibe,
            "montoCajaMovimiento" => $montoIngreso,
            "conceptoCajaMovimiento" => $conceptoIngreso,
            "idCaja" => $idCaja,
            "tipoCajaMovimiento" => "Salida",
            "idTurnoCajaMovimiento" => $idTurnoVigente,
            "idCorte" => $idCorteCaja,
            "estadoCajaMovimiento" => 'Activo'
        );
        IniciarTransaccion();
        $guardar = GuardarDatos($this->tabla, $datosImpresoras);
        ($guardar == false) ? $error = true : $error = false;
        if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 402;
        } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
            $datosRespuesta["idCajaMovimiento"] = $guardar;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }
  function MovimientosCajaEditar($idCajaMovimiento = "") {
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if($this->input->method(TRUE) == "GET") {
          $condicionMovimiento = array(
            'md5(idCajaMovimiento)' => $idCajaMovimiento ,
          );
          $datosMovimiento = TraerUnDato($this->tabla, $condicionMovimiento);
          
          if($this->session->admin == '1'){
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idSucursalCorte" => $this->session->idSucursal
            );
          } else {
            $condicionExiste = array(
              "estadoCorte" => "Vigente",
              "idUsuarioCorteTurno" => $this->session->idUsuario,
              "idSucursalCorte" => $this->session->idSucursal
            );
          }
          $joinFacDet = array(
            array(
              "tabla" => "corteTurno",
              "condicion" =>"corteTurno.idTurno = corteCaja.idTurnoVigente AND corteTurno.estadoCorteTurno = 'Vigente'",
              "tipo" => "left",
              "campos" => "corteTurno.montoTurnoCorteCaja, corteTurno.corteTurno"
            ),
          );
          $apertura = TraerUnDatoJoin("corteCaja", $condicionExiste,$joinFacDet,"","corteCaja.idCaja");
          if($apertura){
            //$apertura = TraerUnDato("corteCaja", $condicionExiste);
            $idCorteCaja = $apertura->idCorteCaja;
            $idCaja = $apertura->idCaja;
            $idTurnoVigente = $apertura->idTurnoVigente;
          } else {
            $idCorteCaja = 0;
            $idCaja = 0;
            $idTurnoVigente = 0;
          }
          $titulo = "Editar Movimiento Caja";
          $datosVista = array(
              "titulo" => $titulo,
              "icono" => "fas fa-minus",
              "controlador" => $this->controlador,
              "proceso" => "Salida",
              "idCorteCaja" => $idCorteCaja,
              "idCaja" => $idCaja,
              "idTurnoVigente" => $idTurnoVigente,
              "datosMovimiento" => $datosMovimiento,
              'idCajaMovimiento' => $idCajaMovimiento,
          );
          $extras = array(
              'css' => array(),
              'js' => array(
                  "scripts/movimientoscaja.js"
              ),
          );
          $this->load->view("cajaMovimientos/MovimientosCajaEditar",$datosVista);
          // GblPlantilla("Impresoras/ImpresorasAgregar", $datosVista, $extras, $titulo);
      } else if ($this->input->method(TRUE) == "POST")
      {
        $idCajaMovimiento = $this->input->post("idCajaMovimiento");
        $entrega = $this->input->post("entregaMovimiento");
        $recibe = $this->input->post("recibeMovimiento");
        $montoIngreso = $this->input->post("montoIngreso");
        $conceptoIngreso = $this->input->post("conceptoIngreso");
        $idCorteCaja = $this->input->post("idCorteCaja");
        $idCaja = $this->input->post("idCaja");
        $idTurnoVigente = $this->input->post("idTurnoVigente");

        $datosImpresoras = array(
            "entregaCajaMovimiento" => $entrega,
            "recibeCajaMovimiento" => $recibe,
            "montoCajaMovimiento" => $montoIngreso,
            "conceptoCajaMovimiento" => $conceptoIngreso,
            'aleatorioCajaMovimiento' => uniqid()
        );
        IniciarTransaccion();
        $condicion = array("md5(idCajaMovimiento)" => $idCajaMovimiento);
        $guardar = EditarDatos($this->tabla, $datosImpresoras, $condicion);
        ($guardar == false) ? $error = true : $error = false;
        if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 402;
        } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }
  function MovimientosCajaBorrar($idCajaMovimiento = "") {
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
       if ($this->input->method(TRUE) == "POST") {
        $idCajaMovimiento = $this->input->post("idCajaMovimiento");

        $datosImpresoras = array(
            'aleatorioCajaMovimiento' => uniqid(),
            "estadoCajaMovimiento" => 'Borrado'
        );
        IniciarTransaccion();
        $condicion = array("md5(idCajaMovimiento)" => $idCajaMovimiento);
        $guardar = EditarDatos($this->tabla, $datosImpresoras, $condicion);
        ($guardar == false) ? $error = true : $error = false;
        if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 500;
        } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }

}
/* End of file Usuarios.php */
