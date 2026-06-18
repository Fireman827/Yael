<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modificadores extends CI_Controller{

	private $tabla = "modificador";
	private $tablaProductoModificador = "productoModificador";
	private $controlador = "Modificadores";
	function __construct()	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$campos = array(
                "idModificadorTipo" => "idCategoria",
                "nombreModificadorTipo" => "nombreCategoria"
            );
            $categoria = TraerDatosRenombrados('modificadorTipo', $campos, array("estadoModificadorTipo" => 'Activo'));
			$titulo = "Modificadores";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-outdent",
				"botones" => array(
					array(
						"icono" => "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'ModificadoresAgregar',
						'txt' => 'Agregar Modificadores',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => false,
						'id'=>''
					),
				),
				"buscador" => true,
                "categorias" => $categoria,
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 3,
					"Relacionado" => 1,
					"Estado" => 1,
					"Acciones" => 1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/modificadores.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function ModificadoresMostrar()	{
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$buscador = $this->input->post("buscador");
        $buscadorTexto = $this->input->post("busqueda");

        $order = $this->input->post("order");
        $busquedaAreglo = $this->input->post("search");
        $busquedaParametro = ($buscador == "1") ? $buscadorTexto : $busquedaAreglo['value'];
        $categoria = $this->input->post("categoria");

		$col = 0;
		$ordenDireccion = "";
		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$ordenDireccion = $o['dir'];
			}
		}
		if ($ordenDireccion != "asc" && $ordenDireccion != "desc") {
			$ordenDireccion = "desc";
		}
		//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		$columnasValidas = array(
			0 => 'idModificador',
			1 => 'nombreModificador',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])) {
			$ordenCampos = null;
		} else {
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$condicion = array('idSucursalModificador' => $sucursal,'estadoModificador !='=> "Borrado");
		if ($categoria != "All") {
            $condicion = array('idSucursalModificador' => $sucursal, "estadoModificador !=" => "Borrado", "modificador.idModificadorTipo" => $categoria);
			$join = array(
				array(
					"tabla" => "modificadorTipo",
					"condicion" => "modificadorTipo.idModificadorTipo = modificador.idModificadorTipo"
				)
			);
            $campos = "modificadorTipo.idModificadorTipo,modificador.idModificador,modificador.idProducto, modificador.nombreModificador, modificador.nombreModificador,modificador.estadoModificador";
            $modificadores = TraerDatosTablaJoinGroup($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion, $join, $campos, "modificador.idModificador");
        } else {
            $modificadores = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
        }
		//$modificadores = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$join);
		// print_r($modificadores);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($modificadores != 0) {
			$datosMostrar = array();
			foreach ($modificadores as $modificador) {
				$estadoModificador = $modificador->estadoModificador;
				if ($estadoModificador =="Activo") {
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else {
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				}
				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion = "ModificadoresEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item' href='" . base_url() . $funcion . "/" . md5($modificador->idModificador) . "'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ModificadoresCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idModificador=" . md5($modificador->idModificador) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ModificadoresEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idModificador=" . md5($modificador->idModificador) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
					$relacionado = ($modificador->idProducto != 0) ? "<span class='badge badge-primary font-bold'>Si<span>":"<span class='badge badge-danger font-bold'>No<span>";
				$datosMostrar[] = array(
					$modificador->idModificador,
					$modificador->nombreModificador,
					$relacionado,
					//$modificador->precioModificador,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalModificadors = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalModificadors,
				"recordsFiltered" => $totalModificadors,
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
	function ModificadoresAgregar()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$productoCategoria = TraerDatos('productoCategoria', array("estadoProductoCategoria" => "Activo","idSucursalProductoCategoria" =>$this->session->idSucursal));
				$modificadorTipo = TraerDatos('modificadorTipo', array("estadoModificadorTipo" => "Activo","idSucursalModificadorTipo" =>$this->session->idSucursal));
				$titulo = "Agregar Modificador";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-users",
					"controlador" => "Modificadores",
					"proceso" => "Agregar",
					"productoCategoria" => $productoCategoria,
					"modificadorTipo" => $modificadorTipo
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/modificadores.js"
					),
				);
				GblPlantilla("modificadores/ModificadoresAgregar", $datosVista, $extras, $titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$formaModificador = $this->input->post("forma");
				$vueltas = ($formaModificador == 'categoria') ? count(json_decode($this->input->post("datos"))) : 1;
				
				IniciarTransaccion();
				
				for($i = 0 ; $i < $vueltas ;$i++){
					if($formaModificador != 'categoria'){
						$modificadorTipoModificador = $this->input->post("tipo");
						$nombreModificador = $this->input->post("nombre");
						$idProducto = ($formaModificador == "otro") ?  0 :  
										( ($formaModificador == "producto") ? $idProducto = $this->input->post("idProducto") : '');
					}
					else{
						$datos = $this->input->post("datos");
						$datos =  json_decode($datos);
						$modificadorTipoModificador = $datos[$i][0];
						$idProducto = $datos[$i][1];
						$nombreModificador = $datos[$i][2];
					}

					$condicionExiste = array(
						"nombreModificador" => $nombreModificador,
						"idModificadorTipo" => $modificadorTipoModificador,
						"estadoModificador !=" => "Borrado",
					);
					$existe = ExistenDatos($this->tabla, $condicionExiste);
					if ($existe == 0) {
						$datosModificadorTipo = array(
							"nombreModificador" => $nombreModificador,
							"idSucursalModificador"=>$this->session->idSucursal,
							"idProducto" => $idProducto,
							"idModificadorTipo" => $modificadorTipoModificador,
							"estadoModificador" => "Activo",
						);
						$guardar = GuardarDatos($this->tabla, $datosModificadorTipo);
						($guardar == false) ? ($error = true) : $error = false;
						if ($error) {
							$datosRespuesta["codigo"] = 500;
							break;
						} else {
							$datosRespuesta["codigo"] = 200;
						}
					} else {
						$datosRespuesta["codigo"] = 400;
						break;
					}
				}
				
				($error == true) ? DeshacerTransaccion() : EjecutarTransaccion();

				echo json_encode($datosRespuesta);
			}
		}
	}
	function ModificadoresEditar($idModificador = "")	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$join = array(
					array(
						"tabla" => "producto",
						"condicion" => "modificador.idProducto = producto.idProducto",
						"tipo" => "left",
						"campos" => "producto.nombreProducto"
					),
				);
				$datosModificador = TraerDatosJoin($this->tabla, array('md5(idModificador)' => $idModificador) ,"",$join);

				$productoCategoria = TraerDatos('productoCategoria', array("idSucursalProductoCategoria","estadoProductoCategoria" => "Activo"));
				$modificadorTipo = TraerDatos('modificadorTipo', array("estadoModificadorTipo" => "Activo"));
				$titulo = "Editar Modificador";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-users",
					"controlador" => "Modificadores",
					"idModificador" => $idModificador,
					"proceso" => "Editar",
					"productoCategoria" => $productoCategoria,
					"modificadorTipo" => $modificadorTipo,
					"datosModificador" => $datosModificador
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/modificadores.js"
					),
				);
				GblPlantilla("modificadores/ModificadoresEditar", $datosVista, $extras, $titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$modificadorTipoModificador = $this->input->post("tipo");
				$nombreModificador = $this->input->post("nombre");
				$idProducto = $this->input->post("idProducto");
				$idModificador = $this->input->post("idModificador");
				$relacionar = $this->input->post("relacionar");

				$condicionExiste = array(
					"md5(idModificador) !=" => $idModificador,
					"nombreModificador" => $nombreModificador,
					"idModificadorTipo" => $modificadorTipoModificador,
					"estadoModificador" => "Activo"
				);
				($relacionar == "Si") ? $condicionExiste["idProducto"] = $idProducto: '';
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosModificador = array(
						"idProducto" => ($relacionar == "Si") ? $idProducto:0,
						"nombreModificador" => $nombreModificador,
						"idModificadorTipo" => $modificadorTipoModificador,
						"aleatorioModificador" => uniqid(),
					);
					$condicion = array(
						"md5(idModificador)" => $idModificador,
					);
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosModificador,$condicion);
					($guardar == false) ? $error = true : $error = false;
					if ($error) {
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 500;
					} else {
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					}
				} else {
					$datosRespuesta["codigo"] = 400;
				}
				
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ModificadoresEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idModificador = $this->input->post("idModificador");
				$datosModificador = array(
					"estadoModificador" => "Borrado"
				);
				$condicion = array("md5(idModificador)" => $idModificador);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosModificador,$condicion);
				if($editar){
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else{
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 500;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}
	function ModificadoresCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idModificador = $this->input->post("idModificador");
				$condicionDatos = array(
					'md5(idModificador)' => $idModificador,
					'estadoModificador' => "Activo",
				);
				$activoModificador = ExistenDatos($this->tabla,$condicionDatos);
				
				( $activoModificador == 0 ) ? $nuevoEstado = "Activo" : $nuevoEstado = "Inactivo";

				$datosModificador = array(
					"estadoModificador" => $nuevoEstado,
					"aleatorioModificador" => uniqid(),
				);
				$condicion = array("md5(idModificador)" => $idModificador);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosModificador,$condicion);
				if($editar){
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else{
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 500;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}
	function ModificadoresAutocomplete(){
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
        } else {
			if($this->input->method(TRUE) == "POST"){
				$busquedaParametro = $this->input->post("query");
				
				$sucursal = $this->session->idSucursal;
				$condicionWhere = array('producto.idSucursalProducto' => $sucursal,'producto.estadoProducto' => 'Activo');
				$condicionLike = array('producto.nombreProducto' => $busquedaParametro);
				$join=array(
					array(
						"tabla" => "modificador",
						"condicion" => "modificador.idProducto = producto.idProducto",
						"tipo" => "left",
						"campos" => "modificador.idModificador"
					),
				);
				$Modificadors = TraerDatosComo("producto",$condicionWhere,$condicionLike,$join,"producto.idProducto");
				echo json_encode($Modificadors);
			}
            
        }
    }
	function ModificadoresTraerProductoCategoria(){
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
        } else {
			if($this->input->method(TRUE) == "POST"){
				$categoriaP = $this->input->post("categoriaP");
				$categoriaM = $this->input->post("categoriaM");
				
				$sucursal = $this->session->idSucursal;
				$condicionWhere = array(
					'productoCategoriaEspecifica.idProductoCategoria' => $categoriaP,
					'productoCategoriaEspecifica.estadoProductoCategoriaEspecifica' => 'Activo',
					'modificadorTipo.idmodificadorTipo !=' => $categoriaM,
					// 'producto.idSucursalProducto' => $sucursal,
					'producto.estadoProducto' => 'Activo',
					'modificador.estadoModificador' => 'Activo',
				);
				$join=array(
					array(
						"tabla" => "productoCategoriaEspecifica",
						"condicion" => "productoCategoriaEspecifica.idProducto = producto.idProducto",
						"tipo" => "inner",
						"campos" => "modificador.idModificador"
					),
					array(
						"tabla" => "modificador",
						"condicion" => "modificador.idProducto != producto.idProducto",
						"tipo" => "left",
						"campos" => "modificador.idModificador"
					),
					array(
						"tabla" => "modificadorTipo",
						"condicion" => "modificador.idModificadorTipo = modificadorTipo.idModificadorTipo",
						"tipo" => "inner",
						"campos" => "modificadorTipo.idModificadorTipo"
					),
				);
				$productos = TraerDatosJoin("producto",$condicionWhere,"",$join,"producto.idProducto");
				$tbody = '';
				$i = 0;
				if($productos){
					foreach($productos as $p){
						$tbody .= "<tr>";
						$tbody .= "		<td class='align-middle text-center'>";
						$tbody .= "			<div class='icheck-".GblTraerConfiguracion('colorComponentes')." d-inline'>";
						$tbody .= "				<input type='checkbox' class='usar' id='modificador".$i."' name='modificador".$i."'>";
						$tbody .= "				<label for='modificador".$i."'></label>";
						$tbody .= "			</div>";
						$tbody .= "		</td>";
						$tbody .= "		<td>";
						$tbody .= "			<input type='hidden' class='idProd' value='".$p->idProducto."'>".$p->nombreProducto;
						$tbody .= "		</td>";
						$tbody .= "		<td>";
						$tbody .= "			<input type='text' class='form-control nombreMod upper' value='".$p->nombreProducto."' placeholder='Nombre Modificador' >";
						$tbody .= "		</td>";
						// $tbody .= "		<td>";
						// $tbody .= "			<input type='text' class='form-control precio decimal' value='".$p->precioVentaProducto."' placeholder='$0.00'>";
						// $tbody .= "		</td>";
						$tbody .= "</tr>";
						$i++;
					}
					$respuesta["codigo"] = 200;
					$respuesta["tbody"] = $tbody;
				}else{
					$respuesta["codigo"] = 500;
				}

				echo json_encode($respuesta);
			}
            
        }
	}
}
/* End of file Modificadores.php */
