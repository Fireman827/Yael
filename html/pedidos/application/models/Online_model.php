<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online_model extends CI_Model {

    // =========================================================
    // CLIENTES - LOGIN / REGISTRO
    // =========================================================

    /**
     * Busca cliente por email en tabla cliente
     * Retorna objeto cliente o false
     */
    public function BuscarClientePorEmail($email) {
        $condicion = "emailCliente = " . $this->db->escape($email) .
                     " AND estadoCliente = 'Activo'";
        return TraerUnDato('cliente', $condicion);
    }

    /**
     * Verifica si ya existe acceso web para este email
     */
    public function ExisteAccesoWeb($email) {
        $condicion = "emailAcceso = " . $this->db->escape($email);
        return ExistenDatos('clienteacceso', $condicion);
    }

    /**
     * Trae el acceso web por email
     */
    public function TraerAccesoPorEmail($email) {
        $condicion = "emailAcceso = " . $this->db->escape($email) .
                     " AND estadoAcceso = 'Activo'";
        return TraerUnDato('clienteacceso', $condicion);
    }

    /**
     * Trae el acceso web por idClienteAcceso
     */
    public function TraerAccesoPorId($idClienteAcceso) {
        $condicion = "idClienteAcceso = " . (int)$idClienteAcceso;
        return TraerUnDato('clienteacceso', $condicion);
    }

    /**
     * Crea nuevo registro en clienteacceso
     * Retorna insert_id o false
     */
    public function CrearAccesoWeb($datos) {
        return GuardarDatos('clienteacceso', $datos);
    }

    /**
     * Actualiza datos de acceso (OTP, token, ultimo login, etc.)
     */
    public function ActualizarAcceso($datos, $idClienteAcceso) {
        $condicion = array('idClienteAcceso' => (int)$idClienteAcceso);
        return EditarDatos('clienteacceso', $datos, $condicion);
    }

    /**
     * Crea nuevo cliente en tabla cliente del POS
     * Usa valores por defecto seguros para campos requeridos
     * Retorna insert_id o false
     */
    public function CrearClientePOS($nombre, $email, $telefono, $direccion, $idSucursal = 1) {
        $datos = array(
            'idSucursalCliente'             => (int)$idSucursal,
            'nombreCliente'                 => strtoupper($this->db->escape_str($nombre)),
            'nombreComercialCliente'        => '',
            'direccionCliente'              => $this->db->escape_str($direccion),
            'documentoFacturacionCliente'   => 'FAC',
            'departamentoCliente'           => '06',
            'municipioCliente'              => '23',
            'telefonoCliente'               => $this->db->escape_str($telefono),
            'referenciaCliente'             => 'CLIENTE WEB',
            'facturarConCliente'            => 'DUI',
            'duiCliente'                    => '',
            'nitCliente'                    => '',
            'nrcCliente'                    => '',
            'giroCliente'                   => '',
            'retieneIvaCliente'             => 0,
            'retieneRentaCliente'           => 0,
            'emailCliente'                  => $this->db->escape_str($email),
            'idCategoriaCliente'            => 2, // REGULAR por defecto
            'avanzadoCliente'               => 0,
            'aleatorioCliente'              => uniqid(),
            'estadoCliente'                 => 'Activo',
        );
        return GuardarDatos('cliente', $datos);
    }

    /**
     * Trae datos completos del cliente POS
     */
    public function TraerClientePOS($idCliente) {
        $condicion = "idCliente = " . (int)$idCliente . " AND estadoCliente = 'Activo'";
        return TraerUnDato('cliente', $condicion);
    }

    // =========================================================
    // OTP / VERIFICACION
    // =========================================================

    /**
     * Guarda OTP y su fecha de expiración (10 minutos)
     */
    public function GuardarOTP($idClienteAcceso, $otp) {
        $expira = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $datos = array(
            'otpAcceso'       => $otp,
            'otpExpiraAcceso' => $expira,
        );
        $condicion = array('idClienteAcceso' => (int)$idClienteAcceso);
        return EditarDatos('clienteacceso', $datos, $condicion);
    }

    /**
     * Verifica OTP: correcto y no expirado
     * Retorna true/false
     */
    public function VerificarOTP($idClienteAcceso, $otp) {
        $condicion = "idClienteAcceso = " . (int)$idClienteAcceso .
                     " AND otpAcceso = " . $this->db->escape($otp) .
                     " AND otpExpiraAcceso >= NOW()";
        return ExistenDatos('clienteacceso', $condicion);
    }

    /**
     * Marca teléfono como verificado y limpia el OTP
     */
    public function MarcarVerificado($idClienteAcceso) {
        $datos = array(
            'otpVerificadoAcceso' => 1,
            'otpAcceso'           => '',
            'otpExpiraAcceso'     => NULL,
            'ultimoLoginAcceso'   => date('Y-m-d H:i:s'),
            'tokenSesionAcceso'   => bin2hex(random_bytes(32)),
        );
        $condicion = array('idClienteAcceso' => (int)$idClienteAcceso);
        return EditarDatos('clienteacceso', $datos, $condicion);
    }

    // =========================================================
    // MENÚ - PRODUCTOS Y CATEGORÍAS
    // =========================================================

    /**
     * Trae categorías activas con productos activos
     */
    public function TraerCategoriasMenu($idSucursal = 1) {
        $condicion = "pc.idSucursalProductoCategoria = " . (int)$idSucursal .
                     " AND pc.estadoProductoCategoria = 'Activo'";
        $join = array(
            array(
                'tabla'     => 'producto p',
                'condicion' => 'p.idProductoCategoria = pc.idProductoCategoria AND p.estadoProducto = \'Activo\' AND p.visibleOnlineProducto = \'Si\'',
                'tipo'      => 'inner',
            )
        );
        $this->db->select('pc.*');
        $this->db->from('productocategoria pc');
        $this->db->where($condicion);
        $this->db->join('producto p', 'p.idProductoCategoria = pc.idProductoCategoria AND p.estadoProducto = \'Activo\' AND p.visibleOnlineProducto = \'Si\'', 'inner');
        $this->db->group_by('pc.idProductoCategoria');
        $this->db->order_by('pc.nombreProductoCategoria', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    /**
     * Trae productos activos — todos o por categoría
     */
    public function TraerProductosMenu($idSucursal = 1, $idCategoria = 0) {
        $condicion = "p.idSucursalProducto = " . (int)$idSucursal .
                     " AND p.estadoProducto = 'Activo'" .
                     " AND p.visibleOnlineProducto = 'Si'";
        if ($idCategoria > 0) {
            $condicion .= " AND p.idProductoCategoria = " . (int)$idCategoria;
        }
        $this->db->select('p.*, pc.nombreProductoCategoria');
        $this->db->from('producto p');
        $this->db->join('productocategoria pc', 'pc.idProductoCategoria = p.idProductoCategoria', 'left');
        $this->db->where($condicion);
        $this->db->order_by('pc.nombreProductoCategoria, p.nombreProducto', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    /**
     * Trae un producto por id (para validar precio antes de insertar)
     */
    public function TraerProducto($idProducto) {
        $condicion = "idProducto = " . (int)$idProducto . " AND estadoProducto = 'Activo'";
        return TraerUnDato('producto', $condicion);
    }

    // =========================================================
    // PEDIDO - CREAR ORDEN ONLINE
    // Inserta en pedido + pedidodetalle + pedidoonline en transacción
    // =========================================================

    /**
     * Crea el pedido completo en una transacción
     * $datosCliente: ['nombre', 'direccion', 'idCliente']
     * $carrito: array de ['idProducto', 'cantidad', 'comentario']
     * $datosPedidoOnline: ['tipoPedidoOnline', 'metodoPagoOnline', 'notasOnline']
     * $idClienteAcceso: int
     * $idSucursal: int
     * Retorna ['ok'=>true,'idPedido'=>X,'codigo'=>'FHB-XXXXX'] o ['ok'=>false,'mensaje'=>'...']
     */
    public function CrearPedidoOnline($datosCliente, $carrito, $datosPedidoOnline, $idClienteAcceso, $idSucursal = 1) {

        if (empty($carrito)) {
            return array('ok' => false, 'mensaje' => 'El carrito está vacío.');
        }

        // -- Calcular total verificando precios en BD (nunca confiar en el POST) --
        $total = 0;
        $itemsValidados = array();
        foreach ($carrito as $item) {
            $producto = $this->TraerProducto($item['idProducto']);
            if (!$producto) {
                return array('ok' => false, 'mensaje' => 'Producto no válido: ' . (int)$item['idProducto']);
            }
            $precio = (float)$producto->precioVentaProducto;
            $cant   = max(1, (int)$item['cantidad']);
            $total += $precio * $cant;
            $itemsValidados[] = array(
                'idProducto'  => (int)$producto->idProducto,
                'nombre'      => $producto->nombreProducto,
                'precio'      => $precio,
                'cantidad'    => $cant,
                'comentario'  => isset($item['comentario']) ? substr($item['comentario'], 0, 500) : '',
            );
        }

        IniciarTransaccion();

        try {
            // 1. Insertar en pedido
            $aleatorioPedido = uniqid();
            $clientePOS = $this->TraerClientePOS((int)$datosCliente['idCliente']);
            $nombrePedido = ($clientePOS && trim($clientePOS->nombreCliente) !== '') ? $clientePOS->nombreCliente : $datosCliente['nombre'];
            $datosPedido = array(
                'idSucursalPedido'       => (int)$idSucursal,
                // Mapear tipo online a tipo POS para que aparezca en Touch
                // 'llevar' y 'domicilio' aparecen en sus pestañas del cajero
                // Se agrega prefijo [WEB] al nombre del cliente para identificarlo
                'tipoCuentaPedido'       => ($datosPedidoOnline['tipoPedidoOnline'] === 'domicilio') ? 'domicilio' : 'llevar',
                'idZonaPedido'           => 0,
                'zonaPedido'             => '',
                'tipoAumentoPedido'      => 'Ninguno',
                'aumentoPedido'          => 0.00,
                'idMesaPedido'           => 0,
                'totalPedido'            => round($total, 2),
                'nombreClientePedido'    => '[WEB] ' . strtoupper($nombrePedido),
                'direccionClientePedido' => ($clientePOS && trim($clientePOS->direccionCliente) !== '') ? $clientePOS->direccionCliente : $datosCliente['direccion'],
                'personasPedido'         => 1,
                'idUsuarioPedido'        => 1, // usuario sistema online
                'fechaPedido'            => date('Y-m-d'),
                'horaPedido'             => date('H:i:s'),
                'idCortePedido'          => 0,
                'estadoPedido'           => 'Pendiente',
				'origenPedido'           => 'web',
                'aleatorioPedido'        => $aleatorioPedido,
                'idCliente'              => (int)$datosCliente['idCliente'],
            );
            $idPedido = GuardarDatos('pedido', $datosPedido);
            if (!$idPedido) { throw new Exception('Error al crear pedido.'); }

            // 2. Insertar cada item en pedidodetalle
            foreach ($itemsValidados as $item) {
                $detalle = array(
                    'idPedido'                      => $idPedido,
                    'tipoPedido'                    => 'Producto',
                    'idCorte'                       => 0,
                    'idReferenciaPedidoDetalle'     => 0,
                    'idProductoPedidoDetalle'       => $item['idProducto'],
                    'cantidadPedidoDetalle'         => $item['cantidad'],
                    'precioPedidoDetalle'           => $item['precio'],
                    'precioOriginalPedidoDetalle'   => $item['precio'],
                    'comentarioPedidoDetalle'       => $item['comentario'],
                    'regaliaPedidoDetalle'          => 0,
                    'senoritaPedidoDetalle'         => 0,
                    'grupoPedidoDetalle'            => 0,
                    'fechaHoraPedidoDetalle'        => date('Y-m-d H:i:s'),
                    'estadoPedidoDetalle'           => 'Activo',
                    'llevarLocalPedidoDetalle'      => 0,
                    'impreso'                       => 0,
                    'motivoPedidoDetalle'           => '',
                    'usuarioAutorizaPedidoDetalle'  => 0,
                    'idUsuarioPedidoDetalle'        => 0,
                    'aleatorioPedidoDetalle'        => uniqid(),
                );
                $ok = GuardarDatos('pedidodetalle', $detalle);
                if (!$ok) { throw new Exception('Error al guardar detalle del pedido.'); }
            }

            // 3. Insertar en pedidoonline
            $codigo = 'FHB-' . str_pad($idPedido, 5, '0', STR_PAD_LEFT);
            $datosOnline = array(
                'idPedidoRef'             => $idPedido,
                'idClienteAccesoRef'      => (int)$idClienteAcceso,
                'tipoPedidoOnline'        => $datosPedidoOnline['tipoPedidoOnline'],
                'metodoPagoOnline'        => $datosPedidoOnline['metodoPagoOnline'],
                'notasOnline'             => $datosPedidoOnline['notasOnline'],
                'latitudOnline'           => $datosPedidoOnline['latitudOnline'] ?? null,
                'longitudOnline'          => $datosPedidoOnline['longitudOnline'] ?? null,
                'idZonaDeliveryOnline'    => $datosPedidoOnline['idZonaDeliveryOnline'] ?? null,
                'nombreZonaDeliveryOnline'=> $datosPedidoOnline['nombreZonaDeliveryOnline'] ?? null,
                'codigoSeguimientoOnline' => $codigo,
                'trackingUrlOnline'       => '',
                'waMensajeClienteEnviado' => 0,
                'waMensajeRestEnviado'    => 0,
                'estadoOnline'            => 'Recibido',
                'fechaHoraOnline'         => date('Y-m-d H:i:s'),
                'aleatorioOnline'         => uniqid(),
            );
            $idOnline = GuardarDatos('pedidoonline', $datosOnline);
            if (!$idOnline) { throw new Exception('Error al registrar pedido online.'); }

            EjecutarTransaccion();

            return array(
                'ok'          => true,
                'idPedido'    => $idPedido,
                'idOnline'    => $idOnline,
                'codigo'      => $codigo,
                'total'       => round($total, 2),
                'items'       => $itemsValidados,
            );

        } catch (Exception $e) {
            DeshacerTransaccion();
            return array('ok' => false, 'mensaje' => $e->getMessage());
        }
    }

    // =========================================================
    // PEDIDOS ONLINE - CONSULTAS
    // =========================================================

    /**
     * Trae pedidoonline por idPedidoOnline con JOIN a pedido y cliente
     */
    public function TraerPedidoOnline($idPedidoOnline) {
        $this->db->select('po.*, p.totalPedido, p.fechaPedido, p.horaPedido, p.estadoPedido,
                           c.nombreCliente, c.telefonoCliente, c.emailCliente, c.direccionCliente');
        $this->db->from('pedidoonline po');
        $this->db->join('pedido p',   'p.idPedido = po.idPedidoRef', 'left');
        $this->db->join('clienteacceso ca', 'ca.idClienteAcceso = po.idClienteAccesoRef', 'left');
        $this->db->join('cliente c',  'c.idCliente = ca.idClienteRef', 'left');
        $this->db->where('po.idPedidoOnline', (int)$idPedidoOnline);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    /**
     * Trae historial de pedidos de un cliente
     */
    public function TraerPedidosCliente($idClienteAcceso, $limite = 10) {
        $this->db->select('po.*, p.totalPedido, p.fechaPedido, p.horaPedido, p.estadoPedido');
        $this->db->from('pedidoonline po');
        $this->db->join('pedido p', 'p.idPedido = po.idPedidoRef', 'left');
        $this->db->where('po.idClienteAccesoRef', (int)$idClienteAcceso);
        $this->db->order_by('po.fechaHoraOnline', 'DESC');
        $this->db->limit($limite);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    /**
     * Trae detalle de items de un pedido (para confirmación y WhatsApp)
     */
    public function TraerDetallePedido($idPedido) {
        $this->db->select('pd.*, pr.nombreProducto, pr.imagenProducto');
        $this->db->from('pedidodetalle pd');
        $this->db->join('producto pr', 'pr.idProducto = pd.idProductoPedidoDetalle', 'left');
        $this->db->where('pd.idPedido', (int)$idPedido);
        $this->db->where('pd.estadoPedidoDetalle !=', 'Borrado');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    /**
     * Actualiza estado del pedido online
     */
    public function ActualizarEstadoOnline($idPedidoOnline, $estado) {
        $estadosValidos = array('Recibido','EnPreparacion','Listo','EnCamino','Entregado','Cancelado');
        if (!in_array($estado, $estadosValidos)) return false;
        $datos    = array('estadoOnline' => $estado);
        $condicion = array('idPedidoOnline' => (int)$idPedidoOnline);
        return EditarDatos('pedidoonline', $datos, $condicion);
    }

    /**
     * Marca como enviados los mensajes de WhatsApp
     */
    public function MarcarWaEnviado($idPedidoOnline, $tipo = 'ambos') {
        $datos = array();
        if ($tipo === 'cliente' || $tipo === 'ambos') $datos['waMensajeClienteEnviado'] = 1;
        if ($tipo === 'restaurante' || $tipo === 'ambos') $datos['waMensajeRestEnviado'] = 1;
        $condicion = array('idPedidoOnline' => (int)$idPedidoOnline);
        return EditarDatos('pedidoonline', $datos, $condicion);
    }

    /**
     * Guarda la URL de tracking de OLS
     */
    public function GuardarTrackingUrl($idPedidoOnline, $trackingUrl) {
        $datos    = array('trackingUrlOnline' => $trackingUrl);
        $condicion = array('idPedidoOnline'   => (int)$idPedidoOnline);
        return EditarDatos('pedidoonline', $datos, $condicion);
    }

    /**
     * Para el polling de cocina: cuenta pedidos online pendientes
     * (lo usa VerificarCocina para activar el badge y sonido)
     */
    public function ContarOnlinePendientes($idSucursal = 1) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('pedidoonline po');
        $this->db->join('pedido p', 'p.idPedido = po.idPedidoRef');
        $this->db->where('po.estadoOnline', 'Recibido');
        $this->db->where('p.idSucursalPedido', (int)$idSucursal);
        $this->db->where('p.estadoPedido', 'Pendiente');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $fila = $q->row();
            return (int)$fila->total;
        }
        return 0;
    }
}
/* End of file Online_model.php */
