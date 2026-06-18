<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function AlertaPersonalizada($tipo, $mensaje){
    $CI =& get_instance();
    $CI->session->set_flashdata('alerta', [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ]);
}


class ActivoFijo extends CI_Controller {

    private $tabla = "activoFijo";
    private $controlador = "ActivoFijo";

    function __construct(){
        parent::__construct();
        $this->load->Model('CoreModel',"core");
    }

    /* ============================================================
     * INDEX
     * ============================================================ */
    public function index(){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",[],[],"No autorizado");
            return;
        }

        $titulo = "Activos Fijo";

        $datosVista = [
            "titulo"=> $titulo,
            "icono"=> "fa fa-building",
            "botones" => [
                [
                    "icono"=> "fa fa-building",
                    'controlador' => $this->controlador,
                    'url' => 'ActivoFijoAgregar',
                    'txt' => 'Agregar Activo',
                    'posicion' => 'right',
                    'tipo' => GblTraerConfiguracion('colorComponentes'),
                    'modal' => false,
                    'id'=>'ActivoFijoAgregar'
                ],
            ],
            "encabezados"=>[
                "ID"=>1,
                "Nombre"=>3,
                "Marca"=>2,
                "Modelo"=>2,
                "Precio"=>2,
                "Vida Util"=>2,
                "Estado"=>1,
                "Acciones"=>1,
            ],
            "admin"=>$this->session->admin,
            "idSucursal"=>$this->session->idSucursal,
            "sucursales"=>TraerDatos('sucursal'),
        ];

        $extras = [
            'css' => [],
            'js' => ["scripts/activofijo.js"],
        ];

        GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
    }

    /* ============================================================
     * ACTIVOFIJOMOSTRAR
     * ============================================================ */
    function ActivoFijoMostrar() {

        $draw = intval($this->input->post("draw"));
        $desdeFilas = intval($this->input->post("start"));
        $cantidadFilas = intval($this->input->post("length"));

        $order = $this->input->post("order");
        $busquedaAreglo = $this->input->post("search");
        $busquedaParametro = $busquedaAreglo['value'];

        $col = 0;
        $ordenDireccion = "desc";

        if (!empty($order)){
            foreach ($order as $o){
                $col = $o['column'];
                $ordenDireccion = $o['dir'];
            }
        }

        $columnasValidas = [
            0 => 'idActivoFijo',
            1 => 'nombreActivoFijo',
            2 => 'marcaActivoFijo',
            3 => 'modeloActivoFijo',
        ];

        $ordenCampos = isset($columnasValidas[$col]) ? $columnasValidas[$col] : null;

        $sucursal = $this->input->post("sucursal");
        $this->session->idSucursal = $sucursal;

        $condicion = [
            'estadoActivoFijo !=' => "Borrado",
            'idSucursalActivoFijo' => $sucursal
        ];

        $activoFijos = TraerDatosTabla(
            $this->tabla,
            $ordenCampos,
            $busquedaParametro,
            $columnasValidas,
            $cantidadFilas,
            $desdeFilas,
            $ordenDireccion,
            $condicion
        );

        if ($activoFijos != 0){
            $datosMostrar = [];

            foreach ($activoFijos as $activoFijo){

                $estado = $activoFijo->estadoActivoFijo;

                if($estado=="Inactivo"){
                    $estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo</span>";
                } elseif($estado=="Activo") {
                    $estadoSpan = "<span class='badge badge-primary font-bold'>Activo</span>";
                } else {
                    $estadoSpan = "<span class='badge badge-warning font-bold'>Depreciado</span>";
                }

                $menu = "<div class='input-group-prepend'>
                    <button data-toggle='dropdown'
                        class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle'>
                        <i class='mdi mdi-menu'></i> Menu
                    </button>
                    <div class='dropdown-menu dropdown-menu-right'>";

                if(GblPermisos($this,"ActivoFijoEditar",$this->controlador)){
                    $menu .= "<a class='dropdown-item' href='". base_url()."ActivoFijoEditar/".md5($activoFijo->idActivoFijo)."'>
                        <i class='fa fa-edit'></i> Editar</a>";
                }

                if(GblPermisos($this,"ActivoFijoDepreciacion",$this->controlador)){
                    $menu .= "<a class='dropdown-item' href='". base_url()."ActivoFijoDepreciacion/".md5($activoFijo->idActivoFijo)."'>
                        <i class='fas fa-chart-line'></i> Depreciación</a>";
                }

                $menu .= "</div></div>";

                $datosMostrar[] = [
                    $activoFijo->idActivoFijo,
                    $activoFijo->nombreActivoFijo,
                    $activoFijo->modeloActivoFijo,
                    $activoFijo->marcaActivoFijo,
                    $activoFijo->precioActivoFijo,
                    $activoFijo->vidaActivoFijo,
                    $estadoSpan,
                    $menu
                ];
            }

            $total = TraerTotalDatos($this->tabla,$condicion);

            $output = [
                "draw" => $draw,
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => $datosMostrar
            ];
        } else{
            $output = [
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ];
        }

        echo json_encode($output);
        exit();
    }

    /* ============================================================
     * AGREGAR
     * ============================================================ */
    function ActivoFijoAgregar(){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",[],[],"No autorizado");
            return;
        }

        if($this->input->method(TRUE) == "GET"){

            $condicionCat = ["estadoCategoria" => "Activo"];
            $datosVista = [
                "titulo"=> "Agregar Activo",
                "icono"=> "fa fa-building",
                "proceso"=> "Agregar",
				"controlador" => $this->controlador,
                "roles"=> TraerDatos('usuarioRoles'),
                "categorias"=> TraerDatos('categoriaActivoFijo',$condicionCat),
                "sucursales"=> TraerDatos('sucursal'),
                "superAdmin" => $this->session->superAdmin
            ];

            $extras = ['css'=>[],'js'=>["scripts/activofijo.js"]];

            GblPlantilla("activoFijo/ActivoFijoAgregar",$datosVista,$extras,"Agregar Activo");
            return;
        }

        if($this->input->method(TRUE) == "POST"){

            $data = [
                "nombreActivoFijo"=>$this->input->post("nombreActivoFijo"),
                "marcaActivoFijo"=>$this->input->post("marcaActivoFijo"),
                "modeloActivoFijo"=>$this->input->post("modeloActivoFijo"),
                "precioActivoFijo"=>$this->input->post("precioActivoFijo"),
                "vidaActivoFijo"=>$this->input->post("vidaActivoFijo"),
                "depreciacionActivoFijo"=>$this->input->post("depreciacionActivoFijo"),
                "categoriaActivoFijo"=>$this->input->post("categoriaActivoFijo"),
                "idSucursalActivoFijo"=>$this->input->post("sucursalActivoFijo")
            ];

            IniciarTransaccion();
            $guardar = GuardarDatos($this->tabla,$data);

            if($guardar){
                EjecutarTransaccion();
                echo json_encode(["codigo"=>200]);
            } else {
                DeshacerTransaccion();
                echo json_encode(["codigo"=>500]);
            }
        }
    }

    /* ============================================================
     * EDITAR
     * ============================================================ */
    function ActivoFijoEditar($id=""){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",[],[],"No autorizado");
            return;
        }

        if($this->input->method(TRUE) == "GET"){

            if(!$id){
                AlertaPersonalizada('error','ID inválido');
                redirect('ActivoFijo');
            }

            $cond = ['md5(idActivoFijo)' => $id];
            $datos = TraerUnDato($this->tabla,$cond);

            if(!$datos){
                AlertaPersonalizada('error','Activo no encontrado');
                redirect('ActivoFijo');
            }

            $datosVista = [
                "titulo"=> "Editar Activo",
                "icono"=> "fa fa-building",
                "proceso"=> "Editar",
				"controlador" => $this->controlador,
                "roles"=> TraerDatos('usuarioRoles'),
                "categorias"=> TraerDatos('categoriaActivoFijo'),
                "sucursales"=> TraerDatos('sucursal'),
                "datosActivoFijo"=>$datos,
                "idActivoFijo"=>$id,
                "superAdmin"=>$this->session->superAdmin
            ];

            $extras = ['css'=>[],'js'=>["scripts/activofijo.js"]];

            GblPlantilla("activoFijo/ActivoFijoEditar",$datosVista,$extras,"Editar Activo");
            return;
        }

        if($this->input->method(TRUE) == "POST"){

            $id = $this->input->post("idActivoFijo");
            $cond = ['md5(idActivoFijo)' => $id];

            $data = [
                "nombreActivoFijo"=>$this->input->post("nombreActivoFijo"),
                "marcaActivoFijo"=>$this->input->post("marcaActivoFijo"),
                "modeloActivoFijo"=>$this->input->post("modeloActivoFijo"),
                "precioActivoFijo"=>$this->input->post("precioActivoFijo"),
                "vidaActivoFijo"=>$this->input->post("vidaActivoFijo"),
                "depreciacionActivoFijo"=>$this->input->post("depreciacionActivoFijo"),
                "categoriaActivoFijo"=>$this->input->post("categoriaActivoFijo"),
                "idSucursalActivoFijo"=>$this->input->post("sucursalActivoFijo"),
                "aleatorioActivoFijo"=>uniqid()
            ];

            IniciarTransaccion();
            $ok = EditarDatos($this->tabla,$data,$cond);

            if($ok){
                EjecutarTransaccion();
                echo json_encode(["codigo"=>200]);
            } else {
                DeshacerTransaccion();
                echo json_encode(["codigo"=>500]);
            }
        }
    }

    /* ============================================================
     * DEPRECIACIÓN
     * ============================================================ */
    function ActivoFijoDepreciacion($id=""){
        
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",[],[],"No autorizado");
            return;
        }

        if(!$id){
            AlertaPersonalizada('error','ID inválido');
            redirect('ActivoFijo');
        }

        $cond = ['md5(idActivoFijo)' => $id];
        $datos = TraerUnDato($this->tabla,$cond);

        if(!$datos){
            AlertaPersonalizada('error','Activo no encontrado');
            redirect('ActivoFijo');
        }

        $datosVista = [
            "titulo"=> "Depreciación de Activo",
            "icono"=> "fa fa-building",
            "proceso"=> "Depreciacion",
			"controlador" => $this->controlador,
            "roles"=> TraerDatos('usuarioRoles'),
            "datosActivoFijo"=>$datos,
            "idActivoFijo"=>$id,
            "superAdmin"=>$this->session->superAdmin
        ];

        $extras = ['css'=>[],'js'=>["scripts/activofijo.js"]];

        GblPlantilla("activoFijo/ActivoFijoDepreciacion",$datosVista,$extras,"Depreciación");
    }

    /* ============================================================
     * IMPRIMIR REPORTE
     * ============================================================ */
    function ActivoFijoImprimir($id=""){

        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",[],[],"No autorizado");
            return;
        }

        if(!$id || strlen($id) < 5){
            AlertaPersonalizada('error','ID inválido');
            redirect('ActivoFijo');
            return;
        }

        $cond = ['md5(idActivoFijo)' => $id];
        $datos = TraerUnDato($this->tabla,$cond);

        if(!$datos){
            AlertaPersonalizada('error','Activo no encontrado');
            redirect('ActivoFijo');
            return;
        }

        /* --- Calculos --- */
        $vida = max(1, floatval($datos->vidaActivoFijo));
        $precio = floatval($datos->precioActivoFijo);
        $cargo = $precio / $vida;

        /* --- Cargar PDF --- */
        $this->load->add_package_path(APPPATH.'third_party/fpdf');
        $this->load->library('pdf');
        $this->fpdf = new Pdf();

        $this->fpdf->SetTopMargin(10);
        $this->fpdf->AddPage('P','A4');
        $this->fpdf->SetFont('Helvetica','B',14);
        $this->fpdf->Cell(192,10,"DIGITALS POS",0,1,"C");

        $this->fpdf->SetFont('Helvetica','B',12);
        $this->fpdf->Cell(192,8,"REPORTE DE DEPRECIACIÓN",0,1,"C");

        $this->fpdf->Ln(10);

        /* --- Encabezado tabla --- */
        $header = [
            1 => ["AÑO",20,"L"],
            2 => ["DEPRECIACION ANUAL",60,"L"],
            3 => ["ACUMULADA",55,"L"],
            4 => ["VALOR LIBROS",55,"L"],
        ];

        $this->fpdf->LineWriteB($header,0,6);

        $acumulado = 0;
        $valorLibro = $precio;

        for ($i=0; $i <= $vida; $i++){
            if($i > 0){
                $acumulado += $cargo;
                $valorLibro -= $cargo;
            }

            $line = [
                1=>[$i,20,"L"],
                2=>[$i==0 ? "" : number_format($cargo,2,'.',','),60,"L"],
                3=>[$i==0 ? "" : number_format($acumulado,2,'.',','),55,"L"],
                4=>[number_format($valorLibro,2,'.',','),55,"L"],
            ];

            $this->fpdf->LineWriteB($line,0,6);
        }

        ob_clean();
        $this->fpdf->Output("reporte_activo.pdf","I");
    }

}
