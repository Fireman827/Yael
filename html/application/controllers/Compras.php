<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compras extends CI_Controller {
    private $tabla = "compra";
    private $tablaDetalle = "detallecompra";
    private $controlador = "Compras";

    function __construct(){
        parent::__construct();
        $this->load->Model('CoreModel',"core");
    }
	

    public function index(){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
            return;
        }
        $titulo = "Compras";
        $datosVista = array(
            "titulo"=> $titulo,
            "icono"=> "fa fa-shopping-cart",
            "botones" => array(
                array(
                    "icono"=> "fa fa-plus",
                    'controlador' => $this->controlador,
                    'url' => 'CompraAgregar',
                    'txt' => 'Agregar Compra',
                    'posicion' => 'right',
                    'tipo' => GblTraerConfiguracion('colorComponentes'),
                    'modal' => false,
                    'id' => 'CompraAgregar'
                ),
            ),
            "encabezados"=>array(
                "ID"=>1,
                "Proveedor"=>5,
                "Total"=>2,
                "Fecha"=>1,
                "Estado"=>1,
                "Acciones"=>1,
            ),
            "admin"=>$this->session->admin,
            "idSucursal"=>$this->session->idSucursal,
            "sucursales"=>TraerDatos('sucursal'),
        );
        $extras = array('css'=>array(),'js'=>array("scripts/compras.js"));
        GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
    }

    public function CompraMostrar(){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            echo json_encode([]); exit;
        }
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $order = $this->input->post("order");
        $searchA = $this->input->post("search");
        $search = isset($searchA['value']) ? $searchA['value'] : '';
        $col = 0; $dir = "desc";
        if(!empty($order)){ foreach($order as $o){ $col = $o['column']; $dir = $o['dir']; }}
        $columns = array(0=>'compra.idCompra',1=>'proveedor.nombreProveedor',2=>'compra.total',3=>'compra.fechaCompra');
        $orderField = isset($columns[$col]) ? $columns[$col] : null;

        $sucursal = $this->input->post("sucursal"); if(!empty($sucursal)) $this->session->idSucursal = $sucursal;
        $condicion = array('compra.estadoCompra !=' => "Borrado", 'compra.idSucursal' => $this->session->idSucursal);
        $joins = array(array('tabla'=>'proveedor','condicion'=>'proveedor.idProveedor = compra.idProveedor'));
        $compras = TraerDatosTablaJoin('compra',$orderField,$search,$columns,$length,$start,$dir,$condicion,$joins);

        if($compras != 0){
            $datos = array();
            foreach($compras as $c){
                $menu = "<div class='input-group-prepend'>
                    <button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold'><i class='mdi mdi-menu'></i> Menu</button>
                    <div class='dropdown-menu dropdown-menu-right'>";
				if ($c->estadoCompra == 'Activo' && GblPermisos($this,"CompraAnular",$this->controlador)) {
					$menu .= "<a class='dropdown-item text-danger anularCompra'
					data-id='".md5($c->idCompra)."'>
					<i class='fa fa-times'></i> Anular
					</a>";
}				
                if(GblPermisos($this,"CompraVer",$this->controlador)) $menu .= "<a class='dropdown-item' href='".base_url()."Compras/CompraVer/".md5($c->idCompra)."'><i class='fa fa-eye'></i> Ver</a>";
                if(GblPermisos($this,"CompraImprimir",$this->controlador)) $menu .= "<a class='dropdown-item' target='_blank' href='".base_url()."Compras/CompraImprimir/".md5($c->idCompra)."'><i class='fa fa-print'></i> Imprimir</a>";
                $menu .= "</div></div>";
                $datos[] = array($c->idCompra, isset($c->nombreProveedor)?$c->nombreProveedor:'', number_format($c->total,2), $c->fechaCompra, $menu);
            }
            $total = TraerTotalDatos('compra',$condicion);
            $out = array("draw"=>$draw,"recordsTotal"=>$total,"recordsFiltered"=>$total,"data"=>$datos);
        } else {
            $out = array("draw"=>$draw,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>[]);
        }
        echo json_encode($out); exit();
    }

    public function CompraAgregar()
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        GblPlantilla("plantilla/permiso", [], [], "No autorizado");
        return;
    }

    $datosVista = [
        'titulo'      => 'Agregar Compra',
        'icono'       => 'fa fa-shopping-cart',
        'proveedores' => TraerDatos(
            'proveedor',
            [
                'idSucursalProveedor' => $this->session->idSucursal,
                'estadoProveedor !='  => 'Borrado'
            ]
        ),
    ];

    $extras = [
        'js' => ['scripts/compras.js']
    ];

    GblPlantilla(
        'compras/CompraAgregar',
        $datosVista,
        $extras,
        'Agregar Compra'
    );
}



    public function CompraGuardar(){
		
		$tipo = $this->input->post('tipoDocumento');

if (in_array($tipo, ['FACTURA','CREDITO_FISCAL'])) {

    if (
        empty($_POST['nitProveedor']) ||
        empty($_POST['nrcProveedor'])
    ) {
        echo json_encode([
            "codigo" => 400,
            "mensaje" => "Datos fiscales incompletos"
        ]);
        return;
    }

    if (empty($_FILES['archivoFactura']['name'])) {
        echo json_encode([
            "codigo" => 400,
            "mensaje" => "Documento fiscal obligatorio"
        ]);
        return;
    }
}
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            echo json_encode(array("codigo"=>403,"mensaje"=>"No autorizado")); return;
        }
        if($this->input->method(TRUE) != "POST"){
            echo json_encode(array("codigo"=>405,"mensaje"=>"Metodo no permitido")); return;
        }

        $idProveedor = $this->input->post("idProveedor");
		$fecha = $this->input->post("fechaCompra");
		$idSucursal = $this->input->post("idSucursalCompra") ?: $this->session->idSucursal;

		$tipoDocumento = $this->input->post("tipoDocumento");
		$numeroDocumento = $this->input->post("numeroDocumento");
		
		// ================= MAPEO DOCUMENTO → INVENTARIO =================
		switch ($tipoDocumento) {
			case "FACTURA":
				$tipoDocInv = "FAC";
			break;

			case "CREDITO_FISCAL":
				$tipoDocInv = "COF";
			break;

			case "TICKET":
			default:
			$tipoDocInv = "TCK";
			break;
		}



		if (in_array($tipoDocumento, ["FACTURA", "CREDITO_FISCAL"])) {
			if ($numeroDocumento === "") {
				echo json_encode([
            "codigo" => 400,
            "mensaje" => "Debe ingresar el número de documento"
				]);
				return;
			}
		} else {
			$numeroDocumento = null;
		}
		
		$datos_items = $this->input->post("items"); // JSON string
        $subtotal = floatval($this->input->post("subtotal"));
        $iva = floatval($this->input->post("iva"));
        $retencion = floatval($this->input->post("retencion"));
        $total = floatval($this->input->post("total"));
        $hora = date("H:i:s");
        $usuario = $this->session->idUsuario;
        $items = is_string($datos_items) ? json_decode($datos_items, true) : $datos_items;

        if(!$idProveedor || !$fecha || empty($items) || !is_array($items)){
            echo json_encode(array("codigo"=>400,"mensaje"=>"Datos incompletos")); return;
        }

        // correlativo interno (simple)
        $prefijo = 'C-'.$idSucursal.'-'.date('Ym');
        $condPref = array('idSucursal' => $idSucursal, 'numeroInterno LIKE' => $prefijo.'%');
        $countPref = TraerTotalDatos('compra',$condPref);
        $secuencia = str_pad($countPref + 1, 6, '0', STR_PAD_LEFT);
        $numeroInterno = $prefijo.'-'.$secuencia;

        IniciarTransaccion();
        $datosCompra = array(
            'idProveedor' => $idProveedor,
            'idSucursal' => $idSucursal,
            'fechaCompra' => $fecha,
            'tipoDocumento' => $tipoDocumento,
            'numeroDocumento' => $numeroDocumento,
            'numeroInterno' => $numeroInterno,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'retencion' => $retencion,
            'total' => $total,
            'totalPagar' => $total,
            'observaciones' => '',
            'estadoCompra' => 'Activo',
            'usuarioRegistro' => $usuario,
            'fechaRegistro' => date('Y-m-d H:i:s')
        );
		
		$rutaArchivo = null;

		if (
			in_array($this->input->post('tipoDocumento'), ['FACTURA','CREDITO_FISCAL']) &&
			isset($_FILES['archivoFactura']) &&
			$_FILES['archivoFactura']['error'] == 0
		) {

			$dir = FCPATH . "uploads/compras/" . $idSucursal;
			if (!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}

			$ext = pathinfo($_FILES['archivoFactura']['name'], PATHINFO_EXTENSION);
			$nombreArchivo = $this->input->post('tipoDocumento') . "_" . date('Ymd_His') . "." . $ext;

			$destino = $dir . "/" . $nombreArchivo;

			if (!move_uploaded_file($_FILES['archivoFactura']['tmp_name'], $destino)) {
				DeshacerTransaccion();
				echo json_encode(["codigo"=>500,"mensaje"=>"No se pudo guardar archivo"]);
				return;
			}

			$rutaArchivo = "uploads/compras/$idSucursal/$nombreArchivo";
		}

		
        $idCompra = GuardarDatos('compra',$datosCompra);
        if(!$idCompra){ DeshacerTransaccion(); echo json_encode(array("codigo"=>500,"mensaje"=>"No se pudo guardar la compra (master).")); return; }
		
		// ================= NUMERO DOCUMENTO PARA INVENTARIO =================
		if ($tipoDocumento === 'TICKET') {
		// Para inventario SIEMPRE debe existir un número
		$numeroDocumentoInv = $numeroInterno;
		} else {
		$numeroDocumentoInv = $numeroDocumento;
		}

		
        // Crear movimiento insumoMovimiento (categoria Carga, tipo Compra)
        $tabla_movimiento = 'insumoMovimiento';
        $form_data_movimiento = array(
			//'archivoCompra' => $rutaArchivo,
            'categoriaInsumoMovimiento' => 'Carga',
            'tipoMovimientoInsumo' => 'Compra',
            'descripcionInsumoMovimiento' => 'Compra #'.$numeroInterno,
            'idProveedorInsumoMovimiento' => $idProveedor,
            'fechaHoraInsumoMovimiento' => $fecha." ".$hora,
            "tipoDocumentoInsumoMovimiento"   => $tipoDocInv, // FAC / COF / TCK
			"numeroDocumentoInsumoMovimiento" => $numeroDocumentoInv,
            'totalInsumoMovimiento' => $total,
            'fechaRegistroInsumoMovimiento' => $fecha." ".$hora,
            'idUsuarioInsumoMovimiento' => $usuario,
            'idSucursalInsumoMovimiento' => $idSucursal,
            'estadoInsumoMovimiento' => 'Activo',
        );
        $guardarMov = GuardarDatos($tabla_movimiento, $form_data_movimiento);
        if(!$guardarMov){ DeshacerTransaccion(); echo json_encode(array("codigo"=>500,"mensaje"=>"No se pudo crear movimiento de inventario.")); return; }
        $idMovimiento = $guardarMov;

        $error = false;
        $respuesta = array("codigo"=>200);

        foreach($items as $it){
            $idInsumo = isset($it['idInsumo']) ? $it['idInsumo'] : null;
            $cantidad = isset($it['cantidad']) ? floatval($it['cantidad']) : 0;
            $costo = isset($it['costo']) ? floatval($it['costo']) : 0;
            $fechaV = isset($it['fechaVencimiento']) ? $it['fechaVencimiento'] : null;
            $lote = isset($it['lote']) ? $it['lote'] : null;
            $idPresentacion = isset($it['idPresentacion']) ? $it['idPresentacion'] : null;
            $unidad = isset($it['unidad']) ? floatval($it['unidad']) : 1;
            $subtotalItem = isset($it['subtotal']) ? floatval($it['subtotal']) : ($cantidad * $costo);

            if(!$idInsumo || $cantidad <= 0){
                $error = true; $respuesta['codigo'] = 400; $respuesta['mensaje'] = 'Item inválido'; break;
            }

            // detallecompra
            $datosDetalleCompra = array(
                'idCompra' => $idCompra,
                'idInsumoDetalle' => $idInsumo,
                'cantidadInsumoDetalle' => $cantidad,
                'costoInsumoDetalle' => $costo,
                'exentoDetalle' => 0,
                'idPresentacionDetalle' => $idPresentacion,
                'idUsuarioDetalle' => $usuario,
                'subtotalInsumoDetalle' => $subtotalItem,
                'fechaDetalle' => $fecha,
                'horaDetalle' => $hora,
                'fechaRegistoDetalle' => date('Y-m-d'),
                'estadoCompraDetalle' => 'Activo'
            );
            $gd = GuardarDatos('detallecompra', $datosDetalleCompra);
            if(!$gd){ $error = true; $respuesta['codigo']=501; $respuesta['mensaje']="No se pudo guardar detallecompra"; break; }

            // insumoMovimientoDetalle
            $datosMovDet = array(
                'idInsumoMovimiento' => $idMovimiento,
                'idInsumo' => $idInsumo,
                'cantidadInsumoMovimientoDetalle' => $cantidad,
                'descripcionInsumoMovimientoDetalle' => isset($it['nombre']) ? $it['nombre'] : '',
                'costoInsumoMovimientoDetalle' => $costo,
                'precioInsumoMovimientoDetalle' => isset($it['precio']) ? $it['precio'] : 0,
                'idPresentacionInsumoMovimientoDetalle' => $idPresentacion,
                'idUsuarioInsumoMovimientoDetalle' => $usuario,
                'estadoInsumoMovimientoDetalle' => 'Activo',
            );
            $guardarDet = GuardarDatos("insumoMovimientoDetalle", $datosMovDet);
            if(!$guardarDet){ $error = true; $respuesta['codigo']=502; $respuesta['mensaje']="No se pudo guardar movimiento detalle"; break; }

            // insumoLote (si aplica)
            if(!empty($fechaV) || !empty($lote)){
                $datosLote = array(
                    'idInsumoMovimientoLote' => $idMovimiento,
                    'idProductoInsumoLote' => $idInsumo,
                    'cantidadInsumoLote' => ($cantidad * $unidad),
                    'fechaVencimientoInsumoLote' => $fechaV,
                    'estadoInsumoLote' => 'Activo'
                );
                $guardarLote = GuardarDatos("insumoLote",$datosLote);
                if(!$guardarLote){ $error = true; $respuesta['codigo']=503; $respuesta['mensaje']="No se pudo guardar lote"; break; }
            }

            // actualizar costo promedio y stock (mismo algoritmo que MovimientosInventario)
            $condStock = array('idSucursalInsumoStock' => $idSucursal, 'idInsumo' => $idInsumo);
            $condInsumo = array('idInsumo' => $idInsumo);

            $insumoObj = TraerUnDato("insumo", $condInsumo);
            $costoPromedioActual = isset($insumoObj->costoPromedioInsumo) ? $insumoObj->costoPromedioInsumo : 0;
            $stockActual = 0;
            if(TraerUnDato("insumoStock",$condStock) !== false){
                $stockActual = TraerUnDato("insumoStock",$condStock)->cantidadInsumoStock;
            }

            $valorAnterior = floatval($costoPromedioActual) * floatval($stockActual);
            $valorNuevo = floatval($cantidad * $costo);
            $denominador = ($stockActual + ($cantidad * $unidad));
            $nuevoPromedio = 0;
            if($denominador > 0){
                $nuevoPromedio = ($valorAnterior + $valorNuevo) / $denominador;
            } else {
                $nuevoPromedio = ($cantidad>0) ? ($costo) : $costoPromedioActual;
            }

            $actualizarPromedio = EditarDatos("insumo", array("costoPromedioInsumo" => $nuevoPromedio, "aleatorioInsumo" => uniqid()), $condInsumo);
            if(!$actualizarPromedio){ $error = true; $respuesta['codigo']=504; $respuesta['mensaje']="No se actualizó promedio"; break; }

            if(TraerDatos("insumoStock", $condStock) !== false){
                $guardarStock = ActualizarCorrelativo("insumoStock", $condStock, "cantidadInsumoStock", ($cantidad * $unidad));
            } else {
                $guardarStock = GuardarDatos("insumoStock", array("cantidadInsumoStock" => ($cantidad * $unidad), "idInsumo" => $idInsumo, "idSucursalInsumoStock" => $idSucursal));
            }
            if(!$guardarStock){ $error = true; $respuesta['codigo']=505; $respuesta['mensaje']="No se actualizó stock"; break; }

            $datosCosto = array("idInsumo" => $idInsumo, "costoPromedioInsumoCosto" => $nuevoPromedio);
            $historialCostoPromedio = GuardarDatos("insumoCosto", $datosCosto);
            if(!$historialCostoPromedio){ $error = true; $respuesta['codigo']=506; $respuesta['mensaje']="No se guardó historial costo"; break; }
        } // foreach items

        if($error){ DeshacerTransaccion(); echo json_encode($respuesta); return; }

        EjecutarTransaccion();
        echo json_encode(array("codigo"=>200,"mensaje"=>"Compra guardada","idCompra"=>$idCompra));
        return;
    }

    public function CompraVer($idHash = ""){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",array(),array(),"No autorizado"); return;
        }
        $cond = array('md5(idCompra)' => $idHash);
        $compra = TraerUnDato('compra', $cond);
        if(!$compra){ GblPlantilla("plantilla/permiso",array(),array(),"Compra no encontrada"); return; }
        $detalles = TraerDatos('detallecompra', array('idCompra' => $compra->idCompra));
        $datos = array("titulo"=>"Detalle Compra","icono"=>"fa fa-shopping-cart","compra"=>$compra,"detalles"=>$detalles);
        GblPlantilla("compras/CompraVer",$datos,array(), "Detalle Compra");
    }

    public function CompraImprimir($idHash = ""){
        if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
            GblPlantilla("plantilla/permiso",array(),array(),"No autorizado"); return;
        }
        $cond = array('md5(idCompra)' => $idHash);
        $compra = TraerUnDato('compra', $cond);
        if(!$compra){ GblPlantilla("plantilla/permiso",array(),array(),"Compra no encontrada"); return; }
        $detalles = TraerDatos('detallecompra', array('idCompra' => $compra->idCompra));
        $this->load->add_package_path(APPPATH . 'third_party/fpdf');
        $this->load->library('pdf');
        $this->fpdf = new Pdf();
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Helvetica','B',14);
        $this->fpdf->Cell(0,7,utf8_decode(GblTraerConfiguracion('nombreEstablecimiento')),0,1,"C");
        $this->fpdf->SetFont('Helvetica','',10);
        $prov = TraerUnDato('proveedor', array('idProveedor' => $compra->idProveedor));
        $this->fpdf->Cell(0,6,utf8_decode("Proveedor: ".(isset($prov->nombreProveedor)?$prov->nombreProveedor:'')),0,1);
        $this->fpdf->Ln(4);
        $this->fpdf->SetFont('Helvetica','B',9);
        $this->fpdf->Cell(100,6,"Insumo",1);
        $this->fpdf->Cell(25,6,"Cant.",1,0,"C");
        $this->fpdf->Cell(35,6,"P.Unit",1,0,"R");
        $this->fpdf->Cell(35,6,"Subtotal",1,1,"R");
        $this->fpdf->SetFont('Helvetica','',9);
        foreach($detalles as $d){
            $insumo = TraerUnDato('insumo', array('idInsumo' => $d->idInsumoDetalle));
            $nombre = isset($insumo->nombreInsumo) ? $insumo->nombreInsumo : "Item ".$d->idCompraDetalle;
            $this->fpdf->Cell(100,6,utf8_decode(substr($nombre,0,40)),1);
            $this->fpdf->Cell(25,6,$d->cantidadInsumoDetalle,1,0,"C");
            $this->fpdf->Cell(35,6,number_format($d->costoInsumoDetalle,2),1,0,"R");
            $this->fpdf->Cell(35,6,number_format($d->subtotalInsumoDetalle,2),1,1,"R");
        }
        $this->fpdf->Ln(3);
        $this->fpdf->Cell(150,6,"",0);
        $this->fpdf->Cell(35,6,"Total: ",0,0,"R");
        $this->fpdf->Cell(35,6,number_format($compra->total,2),0,1,"R");
        $this->fpdf->Output();
    }
	
	public function CompraAnular($idCompra = null)
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        echo json_encode(["codigo" => 403, "mensaje" => "No autorizado"]);
        return;
    }

    if (!$idCompra) {
        echo json_encode(["codigo" => 400, "mensaje" => "Compra inválida"]);
        return;
    }

    // ================== OBTENER COMPRA ==================
    $compra = TraerUnDato('compra', ['idCompra' => $idCompra]);
    if (!$compra || $compra->estadoCompra !== 'Activo') {
        echo json_encode(["codigo" => 404, "mensaje" => "Compra no válida o ya anulada"]);
        return;
    }

    $detalles = TraerDatos('detallecompra', [
        'idCompra' => $idCompra,
        'estadoCompraDetalle' => 'Activo'
    ]);

    if (!$detalles) {
        echo json_encode(["codigo" => 400, "mensaje" => "La compra no tiene detalles válidos"]);
        return;
    }

    $usuario   = $this->session->idUsuario;
    $idSucursal = $compra->idSucursal;
    $fechaHora = date('Y-m-d H:i:s');

    IniciarTransaccion();

    // ================== ANULAR COMPRA ==================
    $ok = EditarDatos(
        'compra',
        ['estadoCompra' => 'Anulado'],
        ['idCompra' => $idCompra]
    );

    if (!$ok) {
        DeshacerTransaccion();
        echo json_encode(["codigo" => 500, "mensaje" => "No se pudo anular la compra"]);
        return;
    }

    // ================== ANULAR DETALLES ==================
    EditarDatos(
        'detallecompra',
        ['estadoCompraDetalle' => 'Anulado'],
        ['idCompra' => $idCompra]
    );

    // ================== MOVIMIENTO KARDEX (REVERSO) ==================
    $movimiento = [
        'categoriaInsumoMovimiento' => 'Descarga',
        'tipoMovimientoInsumo' => 'Anulación Compra',
        'descripcionInsumoMovimiento' => 'Anulación Compra #' . $compra->numeroInterno,
        'idProveedorInsumoMovimiento' => $compra->idProveedor,
        'fechaHoraInsumoMovimiento' => $fechaHora,
        'tipoDocumentoInsumoMovimiento' => $compra->tipoDocumento,
        'numeroDocumentoInsumoMovimiento' => $compra->numeroDocumento,
        'totalInsumoMovimiento' => ($compra->total * -1),
        'fechaRegistroInsumoMovimiento' => $fechaHora,
        'idUsuarioInsumoMovimiento' => $usuario,
        'idSucursalInsumoMovimiento' => $idSucursal,
        'estadoInsumoMovimiento' => 'Activo'
    ];

    $idMovimiento = GuardarDatos('insumoMovimiento', $movimiento);
    if (!$idMovimiento) {
        DeshacerTransaccion();
        echo json_encode(["codigo" => 500, "mensaje" => "No se pudo crear movimiento de reverso"]);
        return;
    }

    // ================== RECORRER INSUMOS ==================
    foreach ($detalles as $d) {

        $cantidadBase = $d->cantidadInsumoDetalle;
        $costo = $d->costoInsumoDetalle;
        $idInsumo = $d->idInsumoDetalle;
        $idPresentacion = $d->idPresentacionDetalle;

        // ================== DETALLE MOVIMIENTO ==================
        $movDet = [
            'idInsumoMovimiento' => $idMovimiento,
            'idInsumo' => $idInsumo,
            'cantidadInsumoMovimientoDetalle' => ($cantidadBase * -1),
            'descripcionInsumoMovimientoDetalle' => 'Reverso compra',
            'costoInsumoMovimientoDetalle' => $costo,
            'precioInsumoMovimientoDetalle' => 0,
            'idPresentacionInsumoMovimientoDetalle' => $idPresentacion,
            'idUsuarioInsumoMovimientoDetalle' => $usuario,
            'estadoInsumoMovimientoDetalle' => 'Activo'
        ];

        if (!GuardarDatos('insumoMovimientoDetalle', $movDet)) {
            DeshacerTransaccion();
            echo json_encode(["codigo" => 501, "mensaje" => "Error en movimiento detalle"]);
            return;
        }

        // ================== STOCK ==================
        $condStock = [
            'idInsumo' => $idInsumo,
            'idSucursalInsumoStock' => $idSucursal
        ];

        $stockObj = TraerUnDato('insumoStock', $condStock);
        $stockActual = $stockObj ? $stockObj->cantidadInsumoStock : 0;
        $nuevoStock = $stockActual - $cantidadBase;

        if ($nuevoStock < 0) {
            DeshacerTransaccion();
            echo json_encode([
                "codigo" => 400,
                "mensaje" => "Stock insuficiente para anular compra"
            ]);
            return;
        }

        EditarDatos(
            'insumoStock',
            ['cantidadInsumoStock' => $nuevoStock],
            $condStock
        );

        // ================== COSTO PROMEDIO ==================
        $insumo = TraerUnDato('insumo', ['idInsumo' => $idInsumo]);
        $costoPromActual = $insumo->costoPromedioInsumo ?? 0;

        if ($nuevoStock > 0) {
            $valorAnterior = $costoPromActual * $stockActual;
            $valorRevertido = $cantidadBase * $costo;
            $nuevoPromedio = ($valorAnterior - $valorRevertido) / $nuevoStock;
        } else {
            $nuevoPromedio = $costoPromActual;
        }

        EditarDatos(
            'insumo',
            ['costoPromedioInsumo' => $nuevoPromedio],
            ['idInsumo' => $idInsumo]
        );

        GuardarDatos('insumoCosto', [
            'idInsumo' => $idInsumo,
            'costoPromedioInsumoCosto' => $nuevoPromedio
        ]);

        // ================== LOTES ==================
        EditarDatos(
            'insumoLote',
            ['estadoInsumoLote' => 'Inactivo'],
            [
                'idProductoInsumoLote' => $idInsumo,
                'idInsumoMovimientoLote' => $idMovimiento
            ]
        );
    }

    EjecutarTransaccion();

    echo json_encode([
        "codigo" => 200,
        "mensaje" => "Compra anulada correctamente"
    ]);
}

	
}
