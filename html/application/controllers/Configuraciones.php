<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuraciones extends CI_Controller
{

  private $tabla = "configuraciones";
  private $controlador = "Configuraciones";
  function __construct()
  {
    parent::__construct();
    $this->load->Model('CoreModel', "core");
  }

  public function index(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      $idSucursal = $this->session->idSucursal;
      $emisor = array(
        'nit' => GblTraerConfiguracionFe('nitEmisor',"","prueba"),
        'nrc' => GblTraerConfiguracionFe('nrcEmisor',"","prueba"),
        'nombre' => GblTraerConfiguracionFe('nombreEmisor',"","prueba"),
        'cod_giro' => GblTraerConfiguracionFe('codGiroEmisor',"","prueba"),
        'giro' => GblTraerConfiguracionFe('giroEmisor',"","prueba"),
        'nombre_comercial' => GblTraerConfiguracionFe('nombreComercialEmisor',"","prueba"),
        'tipo_establecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor',"","prueba"),
        'departamento' => GblTraerConfiguracionFe('departamentoEmisor',"","prueba"),
        'municipio' => GblTraerConfiguracionFe('municipioEmisor',"","prueba"),
        'complemento' => GblTraerConfiguracionFe('direccionEmisor',"","prueba"),
        'telefono' => GblTraerConfiguracionFe('telefonoEmisor',"","prueba"),
        'correo' => GblTraerConfiguracionFe('correoEmisor',"","prueba"),
        'usuario' => GblTraerConfiguracionFe('usuario',"","prueba"),
        'clave' => GblTraerConfiguracionFe('clave',"","prueba"),
        'claveApi' => GblTraerConfiguracionFe('claveApi',"","prueba"),
        'usuarioP' => GblTraerConfiguracionFe('usuario',"","produccion"),
        'claveP' => GblTraerConfiguracionFe('clave',"","produccion"),
        'claveApiP' => GblTraerConfiguracionFe('claveApi',"","produccion"),
        'facturacionElectronica' => GblTraerConfiguracion('facturacion_electronica'),
        'entornoFE' => GblTraerConfiguracion('entornoFE'),
        'logo' => GblTraerConfiguracion('logoEmpresa'),
        'cobroPropina' => GblTraerConfiguracion('cobroPropina'),
        'valorPropina' => GblTraerConfiguracion('valorPropina'),
      );
      $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$emisor["departamento"]));
      $emisor["departamentoNombre"] = $departamento->valores;
      $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$emisor["municipio"],"departamento" => $emisor["departamento"]));
      $emisor["municipioNombre"] = $municipio->valores;

      $datosDepartamentos = TraerDatos('FE_CAT_012_Departamento');
      $departamentosOption = "<option value='' >Seleccione</option>";

      foreach ($datosDepartamentos as $departamentos):
        $departamentosOption .= "<option value='".$departamentos->codigo."' ";
        if($departamentos->codigo == $emisor["departamento"]){
          $departamentosOption .= " selected ";
        }
        $departamentosOption .= ">".$departamentos->valores."</option>";
      endforeach;

      $datosMunicipios = TraerDatos('FE_CAT_013_Municipio',array("departamento" => $emisor["departamento"]));
      $municipiosOption = "<option value='' >Seleccione</option>";
      foreach ($datosMunicipios as $municipios):
        $municipiosOption .= "<option value='".$municipios->codigo."' ";
        if($municipios->codigo == $emisor["municipio"]){
          $municipiosOption .= " selected ";
        }
        $municipiosOption .= ">".$municipios->valores."</option>";
      endforeach;

      //TRAER GIROS
      $datosGiros = TraerDatos('FE_CAT_019_CodigodeActividadEco');
      $girosOption = "<option value='' >Seleccione un giro</option>";

      foreach ($datosGiros as $giros):
        $girosOption .= "<option value='".$giros->codigo."' ";
        if($giros->codigo == $emisor["cod_giro"]){
          $girosOption.= " selected ";
        }
        $girosOption .=">".$giros->codigo." - ".$giros->valores."</option>";
      endforeach;

      $datosSucursal = TraerUnDato('sucursal',array("idSucursal"=>$idSucursal));

      $titulo = "Configuraciones - Sucursal: ".$datosSucursal->nombreSucursal;
      $datosVista = array(
        "titulo" => $titulo,
        "icono" => "fas fa-cogs",
        "controlador" => $this->controlador,
        "emisor" =>  $emisor,
        "departamentos"=> $departamentosOption,
        "municipios"=> $municipiosOption,
        "giros"=> $girosOption,
        "idSucursal"=> $idSucursal,
        "proceso" => "Agregar",
      );
      $extras = array(
        'css' => array(),
        'js' => array(
          "scripts/configuraciones.js?q=".uniqid(),
        ),
      );
      GblPlantilla("configuraciones/ConfiguracionAgregar", $datosVista, $extras, $titulo);
    }
  }
  function ConfiguracionesEditar(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if ($this->input->method(TRUE) == "POST") {
        $idConfiguracion = $this->input->post("idSucursal");
        $nombreEmisor = $this->input->post("nombreEmisor");
        $nombreComercialEmisor = $this->input->post("nombreComercialEmisor");
        $nitEmisor = $this->input->post("nitEmisor");
        $nrcEmisor = $this->input->post("nrcEmisor");
        $telefonoEmisor = $this->input->post("telefonoEmisor");
        $correoEmisor = $this->input->post("correoEmisor");
        $giroEmisor = $this->input->post("giroEmisor");
        $departamentoEmisor = $this->input->post("departamentoEmisor");
        $municipioEmisor = $this->input->post("municipioEmisor");
        $direccionEmisor = $this->input->post("direccionEmisor");
        $facturacionElectronica = $this->input->post("facturacionElectronica");
        $entornoFE = $this->input->post("entornoFE");
        $usuario = $this->input->post("usuario");
        $clave = $this->input->post("clave");
        $claveApi = $this->input->post("claveApi");
        $claveApiP = $this->input->post("claveApiP");
        $claveP = $this->input->post("claveP");
        $usuarioP = $this->input->post("usuarioP");
        $cobroPropina = $this->input->post("cobroPropina");
        $valorPropina = $this->input->post("valorPropina");
        $error = false;
        $giro = TraerUnDato('FE_CAT_019_CodigodeActividadEco',array("codigo"=>$giroEmisor));
        $giroEmisorNombre = $giro->valores;
        IniciarTransaccion();

        $arrayConf = array("valorConfiguracion" => $facturacionElectronica, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "facturacion_electronica");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $entornoFE, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "entornoFE");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        /*-----------------------------------------------------*/
        /*-----------------------------------------------------*/
        $arrayConf = array("valorConfiguracion" => $nombreComercialEmisor, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "nombreEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $direccionEmisor, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "direccionEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $telefonoEmisor, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "telefonoEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $nitEmisor, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "nitEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $nrcEmisor, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "nrcEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $giroEmisorNombre, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "giroEmpresa");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $cobroPropina, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "cobroPropina");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("valorConfiguracion" => $valorPropina, 'aleatorioConfiguracion' => uniqid());
        $condicion = array("parametroConfiguracion" => "valorPropina");
        $guardar = EditarDatos("configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;
        /*-----------------------------------------------------*/
        /*-----------------------------------------------------*/

        ////////////////////////////////////////
        $arrayConf = array("prueba" => $usuario, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "usuario");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $clave, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "clave");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $claveApi, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "claveApi");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $nitEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nitEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $nrcEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nrcEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $nombreEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nombreEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $giroEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "codGiroEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $giroEmisorNombre, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "giroEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $nombreComercialEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nombreComercialEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $departamentoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "departamentoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $municipioEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "municipioEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $direccionEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "direccionEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $telefonoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "telefonoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("prueba" => $correoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "correoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;


        ////////////////////////////////////////////////
        $arrayConf = array("produccion" => $usuarioP, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "usuario");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $claveP, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "clave");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $claveApiP, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "claveApi");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $nitEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nitEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $nrcEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nrcEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $nombreEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nombreEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $giroEmisorNombre, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "giroEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $giroEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "codGiroEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $nombreComercialEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "nombreComercialEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $departamentoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "departamentoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $municipioEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "municipioEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $direccionEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "direccionEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $telefonoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "telefonoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

        $arrayConf = array("produccion" => $correoEmisor, 'comentario' => uniqid());
        $condicion = array("idConfiguracion" => $idConfiguracion, "parametro" => "correoEmisor");
        $guardar = EditarDatos("FE_Configuraciones", $arrayConf,$condicion);
        ($guardar === false) ? $error = true : $error = false;

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
/* End of file Configuraciones.php */
