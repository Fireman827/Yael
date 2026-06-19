<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminOnline extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Online_model');
        $this->load->model('AdminOnline_model');
        $this->load->helper(array('url','form','Whatsapp'));
        if (!$this->session->userdata('existeSesion')) { redirect('InicioSesion'); }
    }
    private function _checkPermiso($fn) {
        $id=$this->session->userdata('idUsuario');
        if($this->session->userdata('admin')||$this->session->userdata('superAdmin')) return true;
        if(!$this->load->is_loaded('CoreModel')) $this->load->model('CoreModel');
        if(!$this->CoreModel->PermisosUsuario($id,$fn,'AdminOnline')){
            GblPlantilla('plantilla/permiso',array(),array(),'Sin permiso'); return false;
        }
        return true;
    }
    private function _json($c,$d=array(),$m=''){
        $r=array('codigo'=>$c);
        if(!empty($d)) $r=array_merge($r,$d);
        if(!empty($m)) $r['mensaje']=$m;
        $this->output->set_content_type('application/json')->set_output(json_encode($r));
    }
    private function _modoPrueba(){
        $cfg=TraerUnDato('configuraciones',"parametroConfiguracion='WA_MODO_PRUEBA' AND estadoConfiguracion='Activo'");
        if(!$cfg) return true;
        return strtoupper(trim($cfg->valorConfiguracion))==='TRUE';
    }
    public function ordenes(){
        if(!$this->_checkPermiso('ordenes')) return;
        $filtros=array(
            'estado'  =>$this->input->get('estado') ?: '',
            'codigo'  =>$this->input->get('codigo') ?: '',
            'desde'   =>$this->input->get('desde')  ?: date('Y-m-d',strtotime('-7 days')),
            'hasta'   =>$this->input->get('hasta')  ?: date('Y-m-d'),
        );
        GblPlantilla('adminonline/ordenes',array(
            'ordenes'      =>$this->AdminOnline_model->TraerOrdenes($filtros),
            'filtroEstado' =>$filtros['estado'],
            'filtroCodigo' =>$filtros['codigo'],
            'fechaDesde'   =>$filtros['desde'],
            'fechaHasta'   =>$filtros['hasta'],
        ),array(),'Pedidos Online — Órdenes');
    }
    public function cambiarEstado(){
        if(!$this->_checkPermiso('cambiarEstado')) return;
        $idOnline=(int)$this->input->post('idOnline');
        $estado=$this->input->post('estado');
        $validos=array('Recibido','EnPreparacion','Listo','EnCamino','Entregado','Cancelado');
        if(!in_array($estado,$validos)){$this->_json(500,array(),'Estado no válido.');return;}
        if(!$this->Online_model->ActualizarEstadoOnline($idOnline,$estado)){
            $this->_json(500,array(),'Error al actualizar.');return;
        }
        if(!$this->_modoPrueba()){
            $pedido=$this->Online_model->TraerPedidoOnline($idOnline);
            if($pedido){
                $idRef=$this->AdminOnline_model->TraerIdClientePorAcceso($pedido->idClienteAccesoRef);
                $cliente=$this->Online_model->TraerClientePOS($idRef);
                if($cliente) WaEnviarEstado($cliente,$pedido,$estado);
            }
        }
        $this->_json(200,array('estado'=>$estado),'Estado actualizado.');
    }
    public function clientes(){
        if(!$this->_checkPermiso('clientes')) return;
        $buscar=$this->input->get('buscar') ?: '';
        GblPlantilla('adminonline/clientes',array(
            'clientes'=>$this->AdminOnline_model->TraerClientesWeb($buscar),
            'buscar'  =>$buscar,
        ),array(),'Pedidos Online — Clientes');
    }
    public function editarCliente(){
        if(!$this->_checkPermiso('editarCliente')) return;
        $idCA=(int)$this->input->post('idClienteAcceso');
        $this->Online_model->ActualizarAcceso(
            array('emailAcceso'=>$this->input->post('email'),'estadoAcceso'=>$this->input->post('estado')),$idCA);
        $acc=$this->Online_model->TraerAccesoPorId($idCA);
        if($acc) EditarDatos('cliente',array(
            'nombreCliente'   =>strtoupper($this->input->post('nombre')),
            'telefonoCliente' =>$this->input->post('telefono'),
            'emailCliente'    =>$this->input->post('email'),
            'direccionCliente'=>$this->input->post('direccion'),
        ),array('idCliente'=>$acc->idClienteRef));
        $this->_json(200,array(),'Cliente actualizado.');
    }
    public function enviarOtpAdmin(){
        if(!$this->_checkPermiso('enviarOtpAdmin')) return;
        $idCA=(int)$this->input->post('idClienteAcceso');
        $tel=trim($this->input->post('telefonoNuevo'));
        if(empty($tel)){$this->_json(500,array(),'Teléfono requerido.');return;}
        $otp=str_pad(rand(0,9999),4,'0',STR_PAD_LEFT);
        $this->Online_model->GuardarOTP($idCA,$otp);
        if(!$this->_modoPrueba()){
            $cfg=TraerUnDato('configuraciones',"parametroConfiguracion='WA_API_KEY_REST' AND estadoConfiguracion='Activo'");
            if($cfg){ $t=preg_replace('/[^0-9]/','',       $tel); if(strlen($t)===8) $t='503'.$t;
                WaSendCurl($t,"🔐 Firehouse Burger\nCódigo: *{$otp}*\nVálido 10 min.",$cfg->valorConfiguracion); }
            $this->_json(200,array(),'OTP enviado a '.$tel);
        } else { $this->_json(200,array('otp_debug'=>$otp),'MODO PRUEBA — Código: '.$otp); }
    }
    public function configuracion(){
        if(!$this->_checkPermiso('configuracion')) return;
        $params=array('PAGO_BANCO_NOMBRE','PAGO_BANCO_CUENTA','PAGO_BANCO_TITULAR','PAGO_BANCO_TIPO',
            'PAGO_BANCO_QR','PAGO_CHIVO_NUMERO','PAGO_CHIVO_QR','WA_MODO_PRUEBA',
            'WA_API_KEY_REST','WA_NUMERO_REST',
            'EMAIL_PROVIDER','EMAIL_FROM','EMAIL_FROM_NAME','SENDGRID_API_KEY',
            'EMAIL_SMTP_HOST','EMAIL_SMTP_PORT','EMAIL_SMTP_USER','EMAIL_SMTP_PASS','EMAIL_SMTP_CRYPTO',
            'NOTIF_EMAIL_PROVIDER','NOTIF_EMAIL_FROM','NOTIF_EMAIL_FROM_NAME','NOTIF_SENDGRID_API_KEY',
            'NOTIF_EMAIL_SMTP_HOST','NOTIF_EMAIL_SMTP_PORT','NOTIF_EMAIL_SMTP_USER','NOTIF_EMAIL_SMTP_PASS','NOTIF_EMAIL_SMTP_CRYPTO',
            'NOTIF_EMAIL_TO',
            'ORION_API_URL','ORION_API_KEY',
            'GOOGLE_MAPS_API_KEY',
            'RESTAURANTE_LAT','RESTAURANTE_LNG');
        $config=array();
        foreach($params as $p){ $row=TraerUnDato('configuraciones',"parametroConfiguracion='{$p}'"); $config[$p]=$row?$row->valorConfiguracion:''; }
        GblPlantilla('adminonline/configuracion',array('config'=>$config),array(),'Configuración de Pagos');
    }
    public function guardarConfig(){
        if(!$this->_checkPermiso('guardarConfig')) return;
        $campos=array('PAGO_BANCO_NOMBRE','PAGO_BANCO_CUENTA','PAGO_BANCO_TITULAR','PAGO_BANCO_TIPO',
            'PAGO_CHIVO_NUMERO','WA_MODO_PRUEBA','WA_API_KEY_REST','WA_NUMERO_REST',
            'EMAIL_PROVIDER','EMAIL_FROM','EMAIL_FROM_NAME','SENDGRID_API_KEY',
            'EMAIL_SMTP_HOST','EMAIL_SMTP_PORT','EMAIL_SMTP_USER','EMAIL_SMTP_PASS','EMAIL_SMTP_CRYPTO',
            'NOTIF_EMAIL_PROVIDER','NOTIF_EMAIL_FROM','NOTIF_EMAIL_FROM_NAME','NOTIF_SENDGRID_API_KEY',
            'NOTIF_EMAIL_SMTP_HOST','NOTIF_EMAIL_SMTP_PORT','NOTIF_EMAIL_SMTP_USER','NOTIF_EMAIL_SMTP_PASS','NOTIF_EMAIL_SMTP_CRYPTO',
            'NOTIF_EMAIL_TO',
            'ORION_API_URL','ORION_API_KEY',
            'GOOGLE_MAPS_API_KEY',
            'RESTAURANTE_LAT','RESTAURANTE_LNG');
        foreach($campos as $c){
            $v=$this->input->post($c); if($v===null) continue;
            $e=TraerUnDato('configuraciones',"parametroConfiguracion='{$c}'");
            if($e) EditarDatos('configuraciones',array('valorConfiguracion'=>$v),array('parametroConfiguracion'=>$c));
            else GuardarDatos('configuraciones',array('idSucursalConfiguracion'=>1,'parametroConfiguracion'=>$c,'valorConfiguracion'=>$v,'comentarioConfiguracion'=>'','aleatorioConfiguracion'=>'','estadoConfiguracion'=>'Activo'));
        }
        $this->_json(200,array(),'Configuración guardada.');
    }
    public function zonas(){
        if(!$this->_checkPermiso('zonas')) return;
        $cfg=TraerUnDato('configuraciones',"parametroConfiguracion='GOOGLE_MAPS_API_KEY'");
        GblPlantilla('adminonline/zonas',array(
            'googleMapsApiKey'=>$cfg?$cfg->valorConfiguracion:'',
        ),array(),'Pedidos Online — Zonas de Delivery');
    }
    public function listarZonas(){
        if(!$this->_checkPermiso('zonas')) return;
        $zonas=TraerDatos('zonadelivery',"estadoZonaDelivery != 'Borrado'");
        $datos=array();
        if($zonas){
            foreach($zonas as $z){
                $datos[]=array(
                    'idZonaDelivery'   =>(int)$z->idZonaDelivery,
                    'nombreZonaDelivery'=>$z->nombreZonaDelivery,
                    'poligonoZonaDelivery'=>json_decode($z->poligonoZonaDelivery),
                    'colorZonaDelivery'=>$z->colorZonaDelivery,
                    'estadoZonaDelivery'=>$z->estadoZonaDelivery,
                );
            }
        }
        $this->_json(200,array('zonas'=>$datos));
    }
    public function guardarZona(){
        if(!$this->_checkPermiso('zonas')) return;
        $idZona   = (int)$this->input->post('idZonaDelivery');
        $nombre   = trim($this->input->post('nombre'));
        $color    = trim($this->input->post('color')) ?: '#C0392B';
        $poligono = $this->input->post('poligono');

        if(empty($nombre)){$this->_json(500,array(),'El nombre de la zona es requerido.');return;}
        $puntos=json_decode($poligono,true);
        if(!is_array($puntos) || count($puntos)<3){$this->_json(500,array(),'El polígono debe tener al menos 3 puntos.');return;}

        $datos=array(
            'nombreZonaDelivery'=>$nombre,
            'poligonoZonaDelivery'=>json_encode($puntos),
            'colorZonaDelivery'=>$color,
        );
        if($idZona>0){
            EditarDatos('zonadelivery',$datos,array('idZonaDelivery'=>$idZona));
            $this->_json(200,array('idZonaDelivery'=>$idZona),'Zona actualizada.');
        } else {
            $datos['idSucursalZonaDelivery']=1;
            $datos['estadoZonaDelivery']='Activo';
            $datos['aleatorioZonaDelivery']=uniqid();
            $idZona=GuardarDatos('zonadelivery',$datos);
            $this->_json(200,array('idZonaDelivery'=>$idZona),'Zona creada.');
        }
    }
    public function eliminarZona(){
        if(!$this->_checkPermiso('zonas')) return;
        $idZona=(int)$this->input->post('idZonaDelivery');
        if($idZona<=0){$this->_json(500,array(),'Zona no válida.');return;}
        EditarDatos('zonadelivery',array('estadoZonaDelivery'=>'Borrado'),array('idZonaDelivery'=>$idZona));
        $this->_json(200,array(),'Zona eliminada.');
    }
    public function productosMenu(){
        if(!$this->_checkPermiso('productosMenu')) return;
        GblPlantilla('adminonline/productosMenu',array(
            'categorias'=>$this->AdminOnline_model->TraerCategoriasProductosAdmin(1),
        ),array(),'Pedidos Online — Productos en Menú Web');
    }
    public function toggleProductoOnline(){
        if(!$this->_checkPermiso('productosMenu')) return;
        $idProducto=(int)$this->input->post('idProducto');
        $visible=$this->input->post('visible')==='1' ? 'Si' : 'No';
        if($idProducto<=0){$this->_json(500,array(),'Producto no válido.');return;}
        EditarDatos('producto',array('visibleOnlineProducto'=>$visible),array('idProducto'=>$idProducto));
        $this->_json(200,array(),'Actualizado.');
    }
    public function subirQR(){
        if(!$this->_checkPermiso('subirQR')) return;
        $tipo=$this->input->post('tipo');
        $map=array('qr_banco'=>'PAGO_BANCO_QR','qr_chivo'=>'PAGO_CHIVO_QR');
        if(!isset($map[$tipo])){$this->_json(500,array(),'Tipo no válido.');return;}
        if(!is_dir(FCPATH.'uploads/qr/')) mkdir(FCPATH.'uploads/qr/',0755,true);
        $this->load->library('upload',array('upload_path'=>FCPATH.'uploads/qr/','allowed_types'=>'jpg|jpeg|png|gif|webp','max_size'=>2048,'file_name'=>$tipo.'_'.time()));
        if(!$this->upload->do_upload('archivo_qr')){$this->_json(500,array(),$this->upload->display_errors('',''));return;}
        $ud=$this->upload->data(); $ruta='uploads/qr/'.$ud['file_name'];
        $p=$map[$tipo]; $e=TraerUnDato('configuraciones',"parametroConfiguracion='{$p}'");
        if($e) EditarDatos('configuraciones',array('valorConfiguracion'=>$ruta),array('parametroConfiguracion'=>$p));
        GuardarDatos('ow_archivos',array('tipoArchivo'=>$tipo,'nombreArchivo'=>$ud['orig_name'],'rutaArchivo'=>$ruta,'activo'=>1));
        $this->_json(200,array('ruta'=>base_url($ruta)),'QR subido.');
    }
}
