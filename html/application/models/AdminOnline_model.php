<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminOnline_model extends CI_Model {
    public function TraerOrdenes($f=array()){
        $this->db->select('po.*,p.totalPedido,p.fechaPedido,p.horaPedido,p.estadoPedido,p.tipoCuentaPedido,c.nombreCliente,c.telefonoCliente,c.emailCliente,c.direccionCliente');
        $this->db->from('pedidoonline po');
        $this->db->join('pedido p','p.idPedido=po.idPedidoRef','left');
        $this->db->join('clienteacceso ca','ca.idClienteAcceso=po.idClienteAccesoRef','left');
        $this->db->join('cliente c','c.idCliente=ca.idClienteRef','left');
        if(!empty($f['estado'])) $this->db->where('po.estadoOnline',$f['estado']);
        if(!empty($f['codigo'])) $this->db->like('po.codigoSeguimientoOnline',$f['codigo']);
        if(!empty($f['desde'])) $this->db->where('DATE(po.fechaHoraOnline)>=',$f['desde']);
        if(!empty($f['hasta'])) $this->db->where('DATE(po.fechaHoraOnline)<=',$f['hasta']);
        $this->db->order_by('po.fechaHoraOnline','DESC');
        $q=$this->db->get();
        return ($q->num_rows()>0)?$q->result():array();
    }
    public function TraerClientesWeb($buscar=''){
        $this->db->select('ca.*,c.nombreCliente,c.telefonoCliente,c.emailCliente,c.direccionCliente,COUNT(po.idPedidoOnline) as totalPedidos,MAX(po.fechaHoraOnline) as ultimoPedido');
        $this->db->from('clienteacceso ca');
        $this->db->join('cliente c','c.idCliente=ca.idClienteRef','left');
        $this->db->join('pedidoonline po','po.idClienteAccesoRef=ca.idClienteAcceso','left');
        if(!empty($buscar)){
            $this->db->group_start();
            $this->db->like('c.nombreCliente',$buscar);
            $this->db->or_like('c.emailCliente',$buscar);
            $this->db->or_like('c.telefonoCliente',$buscar);
            $this->db->group_end();
        }
        $this->db->group_by('ca.idClienteAcceso');
        $this->db->order_by('ca.fechaRegistroAcceso', 'DESC');
        $q=$this->db->get();
        return ($q->num_rows()>0)?$q->result():array();
    }
    public function TraerIdClientePorAcceso($idCA){
        $row=TraerUnDato('clienteacceso',"idClienteAcceso=".(int)$idCA);
        return $row?$row->idClienteRef:0;
    }
    public function ResumenHoy(){
        $hoy=date('Y-m-d');
        $this->db->select("COUNT(*) as totalHoy,SUM(p.totalPedido) as ventaHoy,SUM(CASE WHEN po.estadoOnline='Recibido' THEN 1 ELSE 0 END) as pendientes,SUM(CASE WHEN po.estadoOnline='EnPreparacion' THEN 1 ELSE 0 END) as enPreparacion,SUM(CASE WHEN po.estadoOnline='Entregado' THEN 1 ELSE 0 END) as entregados,SUM(CASE WHEN po.estadoOnline='Cancelado' THEN 1 ELSE 0 END) as cancelados");
        $this->db->from('pedidoonline po');
        $this->db->join('pedido p','p.idPedido=po.idPedidoRef','left');
        $this->db->where('DATE(po.fechaHoraOnline)',$hoy);
        $q=$this->db->get();
        return ($q->num_rows()>0)?$q->row():false;
    }

    // Categorías con productos activos del POS, junto con la bandera
    // visibleOnlineProducto de cada producto, para la pantalla de
    // selección de "Productos en Menú Web".
    public function TraerCategoriasProductosAdmin($idSucursal=1){
        $categorias = TraerDatos('productocategoria',array('estadoProductoCategoria'=>'Activo'),'nombreProductoCategoria ASC');
        $resultado = array();
        if($categorias){
            foreach($categorias as $cat){
                $this->db->select('idProducto,nombreProducto,imagenProducto,precioVentaProducto,visibleOnlineProducto,estadoProducto');
                $this->db->from('producto');
                $this->db->where('idSucursalProducto',(int)$idSucursal);
                $this->db->where('estadoProducto','Activo');
                $this->db->where('idProductoCategoria',$cat->idProductoCategoria);
                $this->db->order_by('nombreProducto','ASC');
                $q=$this->db->get();
                $productos = ($q->num_rows()>0) ? $q->result() : array();
                if(!empty($productos)){
                    $cat->productos = $productos;
                    $resultado[] = $cat;
                }
            }
        }
        return $resultado;
    }
}
