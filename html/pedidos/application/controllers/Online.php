<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online.php — Controlador de pedidos en línea — FHB POS
 * Rutas públicas bajo /pedidos/*, AJAX bajo /Online/*
 *
 * CAMBIOS vs versión anterior:
 *   1. OTP se envía de verdad via Fonnte (cURL descomentado)
 *   2. Al confirmar pedido se envía correo via SendGrid
 *   3. VerificarOnlinePendientes() — nueva ruta para cocina.js
 *   4. Menú carga por categoría (sin "Todos"), primera cat activa por defecto
 *   5. tipoCuentaPedido mapea correctamente domicilio/llevar/local
 *   6. Nombre del pedido lleva prefijo [WEB] para distinguirlo en Touch
 */
class Online extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Online_model');
        $this->load->helper(array('url', 'form', 'Whatsapp', 'Horario'));
        $this->load->library(array('session', 'form_validation'));
    }

    // ----------------------------------------------------------
    // Helper: sesión online activa
    // ----------------------------------------------------------
    private function _verificarSesion() {
        $sesion = $this->session->userdata('online_cliente');
        if (!$sesion || empty($sesion['idClienteAcceso'])) {
            redirect('pedidos/login');
            return false;
        }
        return $sesion;
    }

    // ----------------------------------------------------------
    // Helper: JSON estándar
    // ----------------------------------------------------------
    private function _json($codigo, $datos = array(), $mensaje = '') {
        $respuesta = array('codigo' => $codigo);
        if (!empty($datos))   $respuesta = array_merge($respuesta, $datos);
        if (!empty($mensaje)) $respuesta['mensaje'] = $mensaje;
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($respuesta));
    }

    // ----------------------------------------------------------
    // Helper: modo prueba activo?
    // ----------------------------------------------------------
    private function _modoPrueba() {
        $cfg = TraerUnDato('configuraciones',
            "parametroConfiguracion = 'WA_MODO_PRUEBA' AND estadoConfiguracion = 'Activo'");
        if (!$cfg) return true;
        return strtoupper(trim($cfg->valorConfiguracion)) === 'TRUE';
    }

    // ----------------------------------------------------------
    // Helper: enviar OTP via Fonnte o mostrar en pantalla
    // ----------------------------------------------------------
    private function _enviarOtp($telefono, $otp) {
        if ($this->_modoPrueba()) {
            $this->session->set_flashdata('otp_debug', $otp);
            return true;
        }
        $cfgToken = TraerUnDato('configuraciones',
            "parametroConfiguracion = 'WA_API_KEY_REST' AND estadoConfiguracion = 'Activo'");
        if (!$cfgToken || empty($cfgToken->valorConfiguracion)) {
            $this->session->set_flashdata('otp_debug', $otp);
            return false;
        }
        $tel = preg_replace('/[^0-9]/', '', $telefono);
        if (strlen($tel) === 8) $tel = '503' . $tel;
        $msg = "🔐 Firehouse Burger\nTu código de verificación: *{$otp}*\nVálido por 10 minutos.";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(array(
                'target'      => $tel,
                'message'     => $msg,
                'countryCode' => '503',
            )),
            CURLOPT_HTTPHEADER     => array('Authorization: ' . $cfgToken->valorConfiguracion),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        log_message('debug', 'Fonnte OTP HTTP:'.$code.' | '.$resp);
        return ($code === 200);
    }

    // ----------------------------------------------------------
    // Helper: correo de confirmación de pedido (SMTP/SendGrid configurable)
    // ----------------------------------------------------------
    private function _enviarEmailConfirmacion($email, $nombre, $codigo, $total, $items) {
        // Construir tabla de items
        $itemsHtml = '';
        foreach ($items as $item) {
            $sub = number_format($item['precio'] * $item['cantidad'], 2);
            $itemsHtml .= "<tr>
                <td style='padding:6px 0;border-bottom:1px solid #f0f0f0'>{$item['nombre']}</td>
                <td style='padding:6px 0;border-bottom:1px solid #f0f0f0;text-align:center'>{$item['cantidad']}</td>
                <td style='padding:6px 0;border-bottom:1px solid #f0f0f0;text-align:right'>\${$sub}</td>
            </tr>";
        }

        $html = "<!DOCTYPE html><html><body style='font-family:Segoe UI,sans-serif;background:#111;margin:0;padding:20px'>
        <div style='max-width:520px;margin:0 auto;background:#1e1e1e;border-radius:16px;overflow:hidden;border:1px solid #2a2a2a'>
          <div style='background:linear-gradient(135deg,#C0392B,#922B21);padding:24px;text-align:center'>
            <h1 style='color:#fff;margin:0;font-size:22px;letter-spacing:1px'>🔥 Firehouse Burger</h1>
            <p style='color:rgba(255,255,255,.8);margin:4px 0 0;font-size:14px'>Pedidos en línea</p>
          </div>
          <div style='padding:24px'>
            <p style='color:#e8e8e8;font-size:15px'>Hola <strong>{$nombre}</strong>,</p>
            <p style='color:#aaa;font-size:14px'>¡Tu pedido fue recibido y está en preparación!</p>
            <div style='background:#252525;border-radius:10px;padding:16px;text-align:center;margin:16px 0'>
              <div style='color:#888;font-size:12px;letter-spacing:2px;text-transform:uppercase'>Número de orden</div>
              <div style='color:#C0392B;font-size:28px;font-weight:700;letter-spacing:3px;margin-top:4px'>{$codigo}</div>
            </div>
            <table style='width:100%;border-collapse:collapse;margin-bottom:12px'>
              <thead>
                <tr>
                  <th style='text-align:left;font-size:11px;color:#888;padding-bottom:6px;text-transform:uppercase;letter-spacing:.5px'>Producto</th>
                  <th style='text-align:center;font-size:11px;color:#888;padding-bottom:6px;text-transform:uppercase;letter-spacing:.5px'>Cant.</th>
                  <th style='text-align:right;font-size:11px;color:#888;padding-bottom:6px;text-transform:uppercase;letter-spacing:.5px'>Subtotal</th>
                </tr>
              </thead>
              <tbody style='color:#e8e8e8;font-size:13px'>{$itemsHtml}</tbody>
            </table>
            <div style='display:flex;justify-content:space-between;border-top:1.5px solid #2a2a2a;padding-top:10px'>
              <span style='color:#e8e8e8;font-weight:600'>Total</span>
              <span style='color:#C0392B;font-size:18px;font-weight:700'>\${$total}</span>
            </div>
            <p style='color:#666;font-size:12px;margin-top:20px;text-align:center'>
              Te notificaremos por WhatsApp cuando tu pedido esté listo.<br>
              ¡Gracias por elegir Firehouse Burger! 🍔🔥
            </p>
          </div>
        </div>
        </body></html>";

        return EnviarCorreoConConfig('EMAIL_', [$email], "✅ Pedido {$codigo} confirmado — Firehouse Burger", $html);
    }

    // ==============================================================
    // LOGIN — solo login, registro va a /pedidos/registro
    // ==============================================================
    public function login() {
        if ($this->session->userdata('online_cliente')) {
            redirect('pedidos/menu'); return;
        }
        if ($this->input->method(TRUE) == 'POST') {
            $accion = $this->input->post('accion') ?: 'login';
            $email  = trim(strtolower($this->input->post('email')));
            $pass   = $this->input->post('password');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('error', 'Correo electrónico no válido.');
                redirect('pedidos/login'); return;
            }

            $acceso = $this->Online_model->TraerAccesoPorEmail($email);
            if (!$acceso || !password_verify($pass, $acceso->passwordAcceso)) {
                $this->session->set_flashdata('error', 'Correo o contraseña incorrectos.');
                redirect('pedidos/login'); return;
            }
            if ($acceso->estadoAcceso !== 'Activo') {
                $this->session->set_flashdata('error', 'Tu cuenta está inactiva. Contacta al restaurante.');
                redirect('pedidos/login'); return;
            }

            $cliente = $this->Online_model->TraerClientePOS($acceso->idClienteRef);
            $this->Online_model->ActualizarAcceso(
                array('ultimoLoginAcceso' => date('Y-m-d H:i:s')),
                $acceso->idClienteAcceso
            );
            $this->session->set_userdata('online_cliente', array(
                'idClienteAcceso' => $acceso->idClienteAcceso,
                'idClienteRef'    => $acceso->idClienteRef,
                'nombre'          => $cliente ? $cliente->nombreCliente : $email,
                'email'           => $email,
                'telefono'        => $cliente ? $cliente->telefonoCliente : '',
                'direccion'       => $cliente ? $cliente->direccionCliente : '',
            ));
            $this->session->set_userdata('online_carrito', array());
            redirect('pedidos/menu'); return;
        }

        $this->load->view('online/login', array(
            'titulo' => 'Pedidos en línea',
            'error'  => $this->session->flashdata('error'),
        ));
    }

    // ==============================================================
    // REGISTRO — crea cliente en POS con todos los campos correctos
    // Igual que ClienteAgregar del POS
    // ==============================================================
    public function registro() {
        if ($this->session->userdata('online_cliente')) {
            redirect('pedidos/menu'); return;
        }

        if ($this->input->method(TRUE) == 'POST') {
            $nombre    = trim($this->input->post('nombreCliente'));
            $apellido  = trim($this->input->post('apellidoCliente'));
            $telefono  = trim($this->input->post('telefonoCliente'));
            $email     = trim(strtolower($this->input->post('emailCliente')));
            $direccion = trim($this->input->post('direccionCliente'));
            $depto     = trim($this->input->post('departamentoCliente'));
            $municipio = trim($this->input->post('municipioCliente'));
            $ubicacionFe = $this->normalizarUbicacionFiscal($depto, $municipio);
            $deptoFiscal = $ubicacionFe['departamento'];
            $municipioFiscal = $ubicacionFe['municipio'];
            $direccionEntrega = $this->direccionConDistrito($direccion, $ubicacionFe['distrito']);
            $password  = $this->input->post('password');
            $password2 = $this->input->post('password2');
            // Facturación (opcionales)
            $dui  = trim($this->input->post('duiCliente') ?: '');
            $nit  = trim($this->input->post('nitCliente') ?: '');
            $nrc  = trim($this->input->post('nrcCliente') ?: '');
            $giro = trim($this->input->post('giroCliente') ?: '');
            $dui  = ($dui !== '') ? $dui : '00000000-0';
            $nit  = ($nit !== '') ? $nit : '0000-000000-000-0';
            $nrc  = ($nrc !== '') ? $nrc : '000000-0';
            $documentoFacturacion = (!empty($nit) && !empty($nrc) && !empty($giro)) ? 'CCF' : 'FAC';
            $facturarCon = ($this->input->post('nitCliente')) ? 'NIT' : 'DUI';

            // Validaciones
            $error = '';
            if (empty($nombre) || empty($apellido))  $error = 'Nombre y apellido son obligatorios.';
            elseif (empty($telefono))                 $error = 'El teléfono es obligatorio.';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Correo electrónico no válido.';
            elseif (empty($direccion))                $error = 'La dirección es obligatoria.';
            elseif (empty($depto) || empty($municipio)) $error = 'Selecciona departamento y municipio.';
            elseif (strlen($password) < 6)            $error = 'La contraseña debe tener al menos 6 caracteres.';
            elseif ($password !== $password2)         $error = 'Las contraseñas no coinciden.';

            if (!$error && $this->Online_model->TraerAccesoPorEmail($email)) {
                $this->session->set_flashdata('info', 'Ya tienes una cuenta. Inicia sesión o recupera tu contraseña para continuar tu pedido.');
                redirect('pedidos/login');
                return;
            }

            if ($error) {
                $data = $this->_getDatosRegistro();
                $data['error'] = $error;
                $this->load->view('online/registro', $data);
                return;
            }

            // Nombre completo igual que el POS: "NOMBRE APELLIDO" en mayúsculas
            $nombreCompleto = strtoupper($nombre . ' ' . $apellido);

            // Buscar si ya existe cliente con ese email en el POS
            $clientePOS = $this->Online_model->BuscarClientePorEmail($email);
            if ($clientePOS) {
                $idClienteRef = $clientePOS->idCliente;
                // Actualizar datos por si acaso
                EditarDatos('cliente', array(
                    'nombreCliente'      => $nombreCompleto,
                    'telefonoCliente'    => $telefono,
                    'direccionCliente'   => $direccionEntrega,
                    'departamentoCliente'=> $deptoFiscal,
                    'municipioCliente'   => $municipioFiscal,
                    'documentoFacturacionCliente' => $documentoFacturacion,
                    'facturarConCliente' => $facturarCon,
                    'duiCliente'         => $dui,
                    'nitCliente'         => $nit,
                    'nrcCliente'         => $nrc,
                    'giroCliente'        => $giro,
                    'emailCliente'       => $email,
                ), array('idCliente' => $idClienteRef));
            } else {
                // Crear cliente nuevo en el POS con todos los campos
                // Igual que ClienteAgregar del POS
                // Obtener sucursal principal del sistema (no hardcodeado)
                $idSucursalPrincipal = (int) $this->db
                    ->select('idSucursal')
                    ->order_by('idSucursal', 'ASC')
                    ->limit(1)
                    ->get('sucursal')
                    ->row()
                    ->idSucursal;

                $idClienteRef = GuardarDatos('cliente', array(
                    'idSucursalCliente'           => $idSucursalPrincipal,
                    'nombreCliente'               => $nombreCompleto,
                    'nombreComercialCliente'       => '',
                    'direccionCliente'             => $direccionEntrega,
                    'documentoFacturacionCliente'  => $documentoFacturacion,
                    'departamentoCliente'          => $deptoFiscal,
                    'municipioCliente'             => $municipioFiscal,
                    'telefonoCliente'              => $telefono,
                    'referenciaCliente'            => 'CLIENTE WEB',
                    'facturarConCliente'           => $facturarCon,
                    'duiCliente'                   => $dui,
                    'nitCliente'                   => $nit,
                    'nrcCliente'                   => $nrc,
                    'giroCliente'                  => $giro,
                    'retieneIvaCliente'            => 0,
                    'retieneRentaCliente'          => 0,
                    'emailCliente'                 => $email,
                    'idCategoriaCliente'           => 2,
                    'avanzadoCliente'              => (!empty($nit) || !empty($dui)) ? 1 : 0,
                    'aleatorioCliente'             => uniqid(),
                    'estadoCliente'                => 'Activo',
                ));
                if (!$idClienteRef) {
                    $data = $this->_getDatosRegistro();
                    $data['error'] = 'Error al crear la cuenta. Intenta de nuevo.';
                    $this->load->view('online/registro', $data);
                    return;
                }
            }

            // Crear acceso web
            $idClienteAcceso = $this->Online_model->CrearAccesoWeb(array(
                'idClienteRef'        => (int)$idClienteRef,
                'emailAcceso'         => $email,
                'passwordAcceso'      => password_hash($password, PASSWORD_BCRYPT),
                'otpVerificadoAcceso' => 0, // Requiere verificación por WhatsApp
                'estadoAcceso'        => 'Activo',
                'aleatorioAcceso'     => uniqid(),
            ));

            if (!$idClienteAcceso) {
                $data = $this->_getDatosRegistro();
                $data['error'] = 'Error al crear el acceso. Intenta de nuevo.';
                $this->load->view('online/registro', $data);
                return;
            }

            // Enviar OTP de verificación por WhatsApp
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $this->Online_model->GuardarOTP($idClienteAcceso, $otp);
            $this->_enviarOtp($telefono, $otp);

            // Pendiente de verificación de teléfono
            $this->session->set_userdata('online_otp_pending', array(
                'idClienteAcceso' => $idClienteAcceso,
                'idClienteRef'    => $idClienteRef,
                'nombre'          => $nombreCompleto,
                'email'           => $email,
                'telefono'        => $telefono,
                'direccion'       => $direccionEntrega,
            ));
            redirect('pedidos/verificar');
            return;
        }

        $this->load->view('online/registro', $this->_getDatosRegistro());
    }

    // Helper privado: cargar departamentos y municipios
    
    private function _getDatosRegistro() {
        // departamento: usar el catálogo fiscal FE_CAT_012_Departamento (códigos '01'-'14')
        // para que los códigos coincidan con FE_CAT_013_Municipio.departamento.
        // (la tabla local `departamento` tiene los IDs de La Libertad/Chalatenango y
        // San Miguel/Morazán intercambiados respecto al catálogo oficial de Hacienda)
        $deptos = TraerDatos('FE_CAT_012_Departamento', "codigo != '00'", 'codigo');
        $departamentos = array();
        if ($deptos) {
            foreach ($deptos as $d) {
                $departamentos[] = (object)array(
                    'codigo' => $d->codigo,
                    'valores'=> $d->valores,
                );
            }
        }
 
        // municipio: usar el catálogo fiscal FE_CAT_013_Municipio (codigo, valores, departamento)
        // para que municipioCliente quede guardado con un código válido para Hacienda
        // y coincida con el JOIN que usa Touch::BuscarCliente / ClienteAutocomplete
        $munis = TraerDatos('FE_CAT_013_Municipio', array());
        $municipiosPorDepto = array();
        if ($munis) {
            foreach ($munis as $m) {
                $municipiosPorDepto[$m->departamento][] = array(
                    'codigo'  => $m->codigo,
                    'valores' => $m->valores,
                );
            }
        }
 
        return array(
            'titulo'            => 'Crear cuenta',
            'error'             => '',
            'departamentos'     => $departamentos,
            'municipiosPorDepto'=> $municipiosPorDepto,
        );
    }

    // ===========================================================
    // VERIFICACIÓN OTP
    // ===========================================================
    public function verificar() {
        $pending = $this->session->userdata('online_otp_pending');
        if (!$pending) { redirect('pedidos/login'); return; }

        if ($this->input->method(TRUE) == "POST") {
            $otp = trim($this->input->post('otp'));
            if (!$this->Online_model->VerificarOTP($pending['idClienteAcceso'], $otp)) {
                $this->session->set_flashdata('error', 'Código incorrecto o expirado.');
                redirect('pedidos/verificar');
                return;
            }
            $this->Online_model->MarcarVerificado($pending['idClienteAcceso']);
            $this->session->unset_userdata('online_otp_pending');
            $this->session->set_userdata('online_cliente', array(
                'idClienteAcceso' => $pending['idClienteAcceso'],
                'idClienteRef'    => $pending['idClienteRef'],
                'nombre'          => $pending['nombre'],
                'email'           => $pending['email'],
                'telefono'        => $pending['telefono'],
                'direccion'       => $pending['direccion'],
            ));
            $this->session->set_userdata('online_carrito', array());
            redirect('pedidos/menu');
            return;
        }

        $this->load->view('online/verificar', array(
            'titulo'    => 'Verificar teléfono',
            'telefono'  => $pending['telefono'],
            'error'     => $this->session->flashdata('error'),
            'otp_debug' => $this->session->flashdata('otp_debug'),
        ));
    }

    // ===========================================================
    // REENVIAR OTP
    // ===========================================================
    public function reenviar_otp() {
        $pending = $this->session->userdata('online_otp_pending');
        if (!$pending) { redirect('pedidos/login'); return; }
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $this->Online_model->GuardarOTP($pending['idClienteAcceso'], $otp);
        $this->_enviarOtp($pending['telefono'], $otp);
        $this->session->set_flashdata('info', 'Código reenviado.');
        redirect('pedidos/verificar');
    }

    // ===========================================================
    // MENÚ — carga por categoría, sin "Todos"
    // Si no viene ?cat= toma la primera categoría activa
    // ===========================================================
    public function menu() {
        $sesion     = $this->_verificarSesion();
        $idSucursal = 1;
        $categorias = $this->Online_model->TraerCategoriasMenu($idSucursal);

        // Si no se especifica categoría, usar la primera disponible
        $idCategoria = (int)$this->input->get('cat');
        if ($idCategoria === 0 && $categorias) {
            $idCategoria = (int)$categorias[0]->idProductoCategoria;
        }

        $productos = $this->Online_model->TraerProductosMenu($idSucursal, $idCategoria);
        $carrito   = $this->session->userdata('online_carrito') ?: array();

        $this->load->view('online/menu_online', array(
            'titulo'     => 'Menú',
            'sesion'     => $sesion,
            'categorias' => $categorias,
            'productos'  => $productos,
            'carrito'    => $carrito,
            'catActiva'  => $idCategoria,
        ));
    }

    // ===========================================================
    // CARRITO — AJAX
    // ===========================================================
    public function agregar_carrito() {
        $this->_verificarSesion();
        $idProducto = (int)$this->input->post('idProducto');
        $accion     = $this->input->post('accion');
        $comentario = $this->input->post('comentario') ?: '';

        $producto = $this->Online_model->TraerProducto($idProducto);
        if (!$producto) {
            $this->_json(500, array(), 'Producto no encontrado.');
            return;
        }

        $carrito = $this->session->userdata('online_carrito') ?: array();
        $key     = 'p_' . $idProducto;

        if ($accion === 'add') {
            if (isset($carrito[$key])) {
                $carrito[$key]['cantidad']++;
            } else {
                $carrito[$key] = array(
                    'idProducto' => $idProducto,
                    'nombre'     => $producto->nombreProducto,
                    'precio'     => (float)$producto->precioVentaProducto,
                    'imagen'     => $producto->imagenProducto,
                    'cantidad'   => 1,
                    'comentario' => $comentario,
                );
            }
        } elseif ($accion === 'remove') {
            if (isset($carrito[$key])) {
                $carrito[$key]['cantidad']--;
                if ($carrito[$key]['cantidad'] <= 0) unset($carrito[$key]);
            }
        } elseif ($accion === 'delete') {
            unset($carrito[$key]);
        }

        $this->session->set_userdata('online_carrito', $carrito);

        $totalItems  = array_sum(array_column($carrito, 'cantidad'));
        $totalPrecio = array_sum(array_map(function($i) {
            return $i['precio'] * $i['cantidad'];
        }, $carrito));

        $this->_json(200, array(
            'carrito'     => array_values($carrito),
            'totalItems'  => $totalItems,
            'totalPrecio' => number_format($totalPrecio, 2),
        ));
    }

    public function carrito() {
        $this->_verificarSesion();
        $carrito = $this->session->userdata('online_carrito') ?: array();
        $total   = array_sum(array_map(function($i) {
            return $i['precio'] * $i['cantidad'];
        }, $carrito));
        $this->_json(200, array(
            'carrito'     => array_values($carrito),
            'totalItems'  => array_sum(array_column($carrito, 'cantidad')),
            'totalPrecio' => number_format($total, 2),
        ));
    }

    // ===========================================================
    // TÉRMINOS Y PRIVACIDAD — pública, no requiere sesión
    // ===========================================================
    public function terminos() {
        $sesion = $this->session->userdata('online_cliente') ?: false;
        $this->load->view('online/terminos', array(
            'titulo' => 'Términos y Privacidad',
            'sesion' => $sesion,
            'horario' => HorarioOnlineEstado(),
        ));
    }

    // ===========================================================
    // CHECKOUT
    // ===========================================================
    public function checkout() {
        $sesion  = $this->_verificarSesion();
        $carrito = $this->session->userdata('online_carrito') ?: array();
        if (empty($carrito)) { redirect('pedidos/menu'); return; }

        $total = array_sum(array_map(function($i) {
            return $i['precio'] * $i['cantidad'];
        }, $carrito));

        $cfgMaps = TraerUnDato('configuraciones', "parametroConfiguracion='GOOGLE_MAPS_API_KEY'");

        $this->load->view('online/checkout', array(
            'titulo'  => 'Confirmar pedido',
            'sesion'  => $sesion,
            'carrito' => array_values($carrito),
            'total'   => number_format($total, 2),
            'googleMapsApiKey' => $cfgMaps ? $cfgMaps->valorConfiguracion : '',
        ));
    }

    // ===========================================================
    // VALIDAR ZONA — AJAX, dado lat/lng devuelve si está cubierto
    // ===========================================================
    public function validarZona() {
        $lat = $this->input->post('latitud');
        $lng = $this->input->post('longitud');

        if ($lat === null || $lat === '' || $lng === null || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
            $this->_json(500, array(), 'Coordenadas no válidas.');
            return;
        }

        $zona = EncontrarZonaDelivery((float)$lat, (float)$lng);
        if ($zona) {
            $this->_json(200, array(
                'cubierto' => true,
                'idZonaDelivery'    => (int)$zona->idZonaDelivery,
                'nombreZonaDelivery'=> $zona->nombreZonaDelivery,
            ));
        } else {
            $this->_json(200, array(
                'cubierto' => false,
            ));
        }
    }

    // ===========================================================
    // CONFIRMAR PEDIDO — POST AJAX
    // ===========================================================
    public function confirmar() {
        // Capturar cualquier output accidental (warnings PHP) que rompería el JSON
        ob_start();

        $sesion = $this->_verificarSesion();

        // Carrito desde sesión PHP
        $carrito = $this->session->userdata('online_carrito');

        // Respaldo desde POST si la sesión se perdió
        if (empty($carrito)) {
            $carritoPost = $this->input->post('carrito');
            if (!empty($carritoPost)) {
                $carritoDecoded = json_decode($carritoPost, true);
                if (is_array($carritoDecoded) && !empty($carritoDecoded)) {
                    $carritoReconstruido = array();
                    foreach ($carritoDecoded as $item) {
                        $idProducto = (int)($item['idProducto'] ?? 0);
                        if ($idProducto <= 0) continue;
                        $producto = $this->Online_model->TraerProducto($idProducto);
                        if (!$producto) continue;
                        $key = 'p_' . $idProducto;
                        $carritoReconstruido[$key] = array(
                            'idProducto' => $idProducto,
                            'nombre'     => $producto->nombreProducto,
                            'precio'     => (float)$producto->precioVentaProducto,
                            'imagen'     => $producto->imagenProducto,
                            'cantidad'   => max(1, (int)($item['cantidad'] ?? 1)),
                            'comentario' => $item['comentario'] ?? '',
                        );
                    }
                    if (!empty($carritoReconstruido)) {
                        $carrito = $carritoReconstruido;
                        $this->session->set_userdata('online_carrito', $carrito);
                    }
                }
            }
        }

        if (empty($carrito)) {
            ob_end_clean();
            $this->_json(500, array(), 'El carrito está vacío. Regresa al menú y vuelve a agregar los productos.');
            return;
        }

        // Validar horario de atención — no se aceptan pedidos fuera de horario
        $horario = HorarioOnlineEstado();
        if (!$horario['abierto']) {
            ob_end_clean();
            $this->_json(403, array('horario' => $horario), $horario['mensaje']);
            return;
        }

        $tipoPedido    = $this->input->post('tipoPedido') ?: 'domicilio';
        $metodoPago    = $this->input->post('metodoPago') ?: 'efectivo';
        $notas         = substr($this->input->post('notas') ?: '', 0, 500);

        // Geolocalización (opcional) — solo tiene sentido para domicilio
        $latitud  = null;
        $longitud = null;
        $idZonaDelivery     = null;
        $nombreZonaDelivery = null;
        if ($tipoPedido === 'domicilio') {
            $latPost = $this->input->post('latitud');
            $lngPost = $this->input->post('longitud');
            if ($latPost !== null && $latPost !== '' && $lngPost !== null && $lngPost !== ''
                && is_numeric($latPost) && is_numeric($lngPost)) {
                $latitud  = round((float)$latPost, 7);
                $longitud = round((float)$lngPost, 7);

                $zona = EncontrarZonaDelivery($latitud, $longitud);
                if ($zona) {
                    $idZonaDelivery     = (int)$zona->idZonaDelivery;
                    $nombreZonaDelivery = $zona->nombreZonaDelivery;
                } else {
                    // Ubicación fuera de todas las zonas de cobertura: no se acepta el pedido a domicilio.
                    ob_end_clean();
                    $this->_json(422, array(), 'Lo sentimos, tu ubicación está fuera de nuestras zonas de cobertura de delivery. Llámanos para verificar al 7621-2908, o ajusta tu ubicación / elige "Recoger en restaurante".');
                    return;
                }
            }
        }

        $mapaTipo = array(
            'domicilio' => 'domicilio',
            'recoger'   => 'llevar',
            'local'     => 'local',
        );
        $tipoCuentaPOS = isset($mapaTipo[$tipoPedido]) ? $mapaTipo[$tipoPedido] : 'domicilio';

        $items = array();
        foreach ($carrito as $item) {
            $items[] = array(
                'idProducto' => $item['idProducto'],
                'cantidad'   => $item['cantidad'],
                'comentario' => $item['comentario'] ?? '',
            );
        }

        $resultado = $this->Online_model->CrearPedidoOnline(
            array(
                'nombre'        => $sesion['nombre'],
                'direccion'     => $sesion['direccion'],
                'idCliente'     => $sesion['idClienteRef'],
                'tipoCuentaPOS' => $tipoCuentaPOS,
                'tipoPedido'    => $tipoPedido,
            ),
            $items,
            array(
                'tipoPedidoOnline' => $tipoPedido,
                'metodoPagoOnline' => $metodoPago,
                'notasOnline'      => $notas,
                'latitudOnline'    => $latitud,
                'longitudOnline'   => $longitud,
                'idZonaDeliveryOnline'     => $idZonaDelivery,
                'nombreZonaDeliveryOnline' => $nombreZonaDelivery,
            ),
            $sesion['idClienteAcceso']
        );

        if (!$resultado['ok']) {
            ob_end_clean();
            $this->_json(500, array(), $resultado['mensaje']);
            return;
        }

        $this->session->set_userdata('online_carrito', array());
        $this->session->set_userdata('online_ultimo_pedido', array(
            'idPedido' => $resultado['idPedido'],
            'idOnline' => $resultado['idOnline'],
            'codigo'   => $resultado['codigo'],
            'total'    => $resultado['total'],
        ));

        // WhatsApp — usando TraerUnDato directo, sin GblTraerConfiguracion
        $cliente      = $this->Online_model->TraerClientePOS($sesion['idClienteRef']);
        $pedidoOnline = $this->Online_model->TraerPedidoOnline($resultado['idOnline']);
        $detalles     = $this->Online_model->TraerDetallePedido($resultado['idPedido']);

        if ($cliente && $pedidoOnline) {
            WaEnviarRestaurante($cliente, $pedidoOnline, $detalles ?: array());
            $this->Online_model->MarcarWaEnviado($resultado['idOnline'], 'restaurante');
            WaEnviarCliente($cliente, $pedidoOnline);
            $this->Online_model->MarcarWaEnviado($resultado['idOnline'], 'cliente');
        }

        // Correo de confirmación
        if (!empty($sesion['email'])) {
            $this->_enviarEmailConfirmacion(
                $sesion['email'],
                $sesion['nombre'],
                $resultado['codigo'],
                number_format($resultado['total'], 2),
                $resultado['items']
            );
        }

        // Limpiar cualquier output accidental antes de enviar JSON
        ob_end_clean();

        $this->_json(200, array(
            'codigo'   => $resultado['codigo'],
            'idOnline' => $resultado['idOnline'],
        ));
    }


    // ===========================================================
    // CONFIRMACIÓN
    // ===========================================================
    public function confirmacion() {
        $sesion       = $this->_verificarSesion();
        $ultimoPedido = $this->session->userdata('online_ultimo_pedido');
        if (!$ultimoPedido) { redirect('pedidos/menu'); return; }

        $this->load->view('online/confirmacion', array(
            'titulo'      => '¡Pedido confirmado!',
            'sesion'      => $sesion,
            'pedido'      => $ultimoPedido,
            'pedidoOnline'=> $this->Online_model->TraerPedidoOnline($ultimoPedido['idOnline']),
            'detalles'    => $this->Online_model->TraerDetallePedido($ultimoPedido['idPedido']),
        ));
    }

    // ===========================================================
    // ESTADO DEL PEDIDO — AJAX polling
    // ===========================================================
    public function estado_pedido() {
        $this->_verificarSesion();
        $idOnline = (int)$this->input->post('idOnline');
        $pedido   = $this->Online_model->TraerPedidoOnline($idOnline);
        if (!$pedido) {
            $this->_json(500, array(), 'Pedido no encontrado.');
            return;
        }
        $this->_json(200, array(
            'estado'      => $pedido->estadoOnline,
            'estadoPOS'   => $pedido->estadoPedido,
            'trackingUrl' => $pedido->trackingUrlOnline,
        ));
    }

    // ===========================================================
    // MI CUENTA
    // ===========================================================
    public function mi_cuenta() {
        $sesion  = $this->_verificarSesion();
        $pedidos = $this->Online_model->TraerPedidosCliente($sesion['idClienteAcceso'], 15);
        $this->load->view('online/mi_cuenta', array(
            'titulo'  => 'Mis pedidos',
            'sesion'  => $sesion,
            'pedidos' => $pedidos,
        ));
    }

    // ===========================================================
    // RECUPERAR CONTRASEÑA
    // ===========================================================
    public function recuperar() {
        if ($this->input->method(TRUE) == "POST") {
            $email  = trim(strtolower($this->input->post('email')));
            $acceso = $this->Online_model->TraerAccesoPorEmail($email);
            if ($acceso) {
                $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $this->Online_model->GuardarOTP($acceso->idClienteAcceso, $otp);
                $this->session->set_userdata('online_recuperar_id', $acceso->idClienteAcceso);
                $clientePOS = $this->Online_model->TraerClientePOS($acceso->idClienteRef);
                if ($clientePOS) {
                    $this->_enviarOtp($clientePOS->telefonoCliente, $otp);
                }
            }
            $this->session->set_flashdata('info', 'Si el correo existe, recibirás un código en WhatsApp.');
            redirect('pedidos/recuperar');
            return;
        }
        $this->load->view('online/recuperar', array(
            'titulo'    => 'Recuperar contraseña',
            'error'     => $this->session->flashdata('error'),
            'info'      => $this->session->flashdata('info'),
            'otp_debug' => $this->session->flashdata('otp_debug'),
            'paso'      => $this->session->userdata('online_recuperar_id') ? 2 : 1,
        ));
    }

    public function nueva_password() {
        if ($this->input->method(TRUE) != "POST") { redirect('pedidos/recuperar'); return; }
        $idAcceso  = (int)$this->session->userdata('online_recuperar_id');
        $otp       = trim($this->input->post('otp'));
        $password  = $this->input->post('password');
        $password2 = $this->input->post('password2');
        if (!$idAcceso) { redirect('pedidos/recuperar'); return; }
        if ($password !== $password2 || strlen($password) < 6) {
            $this->session->set_flashdata('error', 'Las contraseñas no coinciden o son muy cortas.');
            redirect('pedidos/recuperar');
            return;
        }
        if (!$this->Online_model->VerificarOTP($idAcceso, $otp)) {
            $this->session->set_flashdata('error', 'Código incorrecto o expirado.');
            redirect('pedidos/recuperar');
            return;
        }
        $this->Online_model->ActualizarAcceso(
            array('passwordAcceso' => password_hash($password, PASSWORD_BCRYPT),
                  'otpAcceso' => '', 'otpExpiraAcceso' => NULL),
            $idAcceso
        );
        $this->session->unset_userdata('online_recuperar_id');
        $this->session->set_flashdata('info', '✅ Contraseña cambiada. Ya puedes iniciar sesión.');
        redirect('pedidos/login');
    }

    // ===========================================================
    // LOGOUT
    // ===========================================================
    public function logout() {
        $this->session->unset_userdata('online_cliente');
        $this->session->unset_userdata('online_carrito');
        $this->session->unset_userdata('online_ultimo_pedido');
        $this->session->unset_userdata('online_otp_pending');
        redirect('pedidos/login');
    }

	// ==========================================================
    // PERFIL DEL CLIENTE — ver y editar
    // ==========================================================
    public function perfil() {
        $sesion = $this->_verificarSesion();
        $cliente= $this->Online_model->TraerClientePOS($sesion['idClienteRef']);
        $pedidos= $this->Online_model->TraerPedidosCliente($sesion['idClienteAcceso'], 10);

        $this->load->view('online/perfil', array(
            'titulo'  => 'Mi Perfil',
            'sesion'  => $sesion,
            'cliente' => $cliente,
            'pedidos' => $pedidos,
        ));
    }

    // Guardar cambios del perfil — AJAX POST
    public function guardarPerfil() {
        $sesion = $this->_verificarSesion();

        $nombre   = trim($this->input->post('nombre'));
        $telefono = trim($this->input->post('telefono'));
        $direccion= trim($this->input->post('direccion'));

        if (empty($nombre) || empty($telefono) || empty($direccion)) {
            $this->_json(500, array(), 'Todos los campos son obligatorios.');
            return;
        }

        // Actualizar en tabla cliente del POS
        EditarDatos('cliente',
            array(
                'nombreCliente'    => strtoupper($nombre),
                'telefonoCliente'  => $telefono,
                'direccionCliente' => $direccion,
            ),
            array('idCliente' => $sesion['idClienteRef'])
        );

        // Actualizar sesión activa
        $sesionActual = $this->session->userdata('online_cliente');
        $sesionActual['nombre']   = $nombre;
        $sesionActual['telefono'] = $telefono;
        $sesionActual['direccion']= $direccion;
        $this->session->set_userdata('online_cliente', $sesionActual);

        $this->_json(200, array(), 'Datos actualizados correctamente.');
    }

    // Cambiar contraseña — AJAX POST
    public function cambiarPassword() {
        $sesion = $this->_verificarSesion();

        $passActual = $this->input->post('password_actual');
        $passNueva  = $this->input->post('password_nueva');

        if (empty($passActual) || empty($passNueva)) {
            $this->_json(500, array(), 'Completa todos los campos.');
            return;
        }
        if (strlen($passNueva) < 6) {
            $this->_json(500, array(), 'La contraseña debe tener al menos 6 caracteres.');
            return;
        }

        // Verificar contraseña actual
        $acceso = $this->Online_model->TraerAccesoPorId($sesion['idClienteAcceso']);
        if (!$acceso || !password_verify($passActual, $acceso->passwordAcceso)) {
            $this->_json(500, array(), 'La contraseña actual es incorrecta.');
            return;
        }

        // Actualizar contraseña
        $this->Online_model->ActualizarAcceso(
            array('passwordAcceso' => password_hash($passNueva, PASSWORD_BCRYPT)),
            $sesion['idClienteAcceso']
        );

        $this->_json(200, array(), 'Contraseña cambiada correctamente.');
    }
	
	public function guardarFacturacion() {
        $sesion = $this->_verificarSesion();

        $dui            = trim($this->input->post('dui'));
        $nit            = trim($this->input->post('nit'));
        $nrc            = trim($this->input->post('nrc'));
        $giro           = trim($this->input->post('giro'));
        $nombreComercial= trim($this->input->post('nombreComercial'));

        EditarDatos('cliente',
            array(
                'duiCliente'              => $dui,
                'nitCliente'              => $nit,
                'nrcCliente'              => $nrc,
                'giroCliente'             => $giro,
                'nombreComercialCliente'  => $nombreComercial,
            ),
            array('idCliente' => $sesion['idClienteRef'])
        );

        $this->_json(200, array(), 'Datos de facturación guardados.');
    }

    /* =====================================================
       NORMALIZAR UBICACIÓN FISCAL
       Recibe el código de departamento y municipio que
       viene del select del formulario web (ej: '08', '25')
       y devuelve:
         - departamento : código para cliente.departamentoCliente
         - municipio    : código para cliente.municipioCliente
         - distrito     : texto del municipio para la dirección
       Consulta fe_cat_012_departamento y fe_cat_013_municipio
    ===================================================== */
    private function normalizarUbicacionFiscal($codigoDepto, $codigoMunicipio)
    {
        // Asegurar formato string con ceros a la izquierda
        $codigoDepto     = str_pad(trim($codigoDepto),     2, '0', STR_PAD_LEFT);
        $codigoMunicipio = str_pad(trim($codigoMunicipio), 2, '0', STR_PAD_LEFT);

        // Buscar nombre del municipio para concatenar a la dirección
        $rowMunicipio = $this->db
            ->select('valores')
            ->from('fe_cat_013_municipio')
            ->where('CAST(codigo AS UNSIGNED) = ' . (int)$codigoMunicipio, null, false)
            ->where('CAST(departamento AS UNSIGNED) = ' . (int)$codigoDepto, null, false)
            ->limit(1)
            ->get()
            ->row();

        $distritoTexto = $rowMunicipio ? $rowMunicipio->valores : '';

        return array(
            'departamento' => $codigoDepto,      // código numérico: '08'
            'municipio'    => $codigoMunicipio,   // código numérico: '25'
            'distrito'     => $distritoTexto,     // texto: 'OLOCUILTA'
        );
    }

    /* =====================================================
       DIRECCIÓN CON DISTRITO
       Concatena el nombre del municipio/distrito a la
       dirección si no está ya incluido, para que la
       dirección de entrega sea más descriptiva.
       Ej: "Col. Las Flores #5" → "Col. Las Flores #5, OLOCUILTA"
    ===================================================== */
    private function direccionConDistrito($direccion, $distrito)
    {
        $direccion = trim($direccion);
        $distrito  = trim($distrito);

        if (empty($distrito)) {
            return $direccion;
        }

        // No duplicar si el cliente ya escribió el municipio en la dirección
        if (stripos($direccion, $distrito) !== false) {
            return $direccion;
        }

        return $direccion . ', ' . $distrito;
    }

}
/* End of file Online.php */



