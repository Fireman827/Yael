<?php

if (!function_exists('GblPlantilla')){
	// Funcion para renderizar la plantilla
		// $vista = nombre de la vista, $vistaDatos = array con los datos que se envian a la vista
		// $extras = js o css que se usen especificamente en la vista en un array	
	
	function GblPlantilla($vista,$vistaDatos=array(),$extras=array(),$titulo=""){
		$ci =& get_instance();
		GblValidarSesion($ci);
		$ci->load->model('CoreModel');

		$usuario   = $ci->session->usuario;
		$nombre    = $ci->session->nombre;
		$idUsuario = $ci->session->idUsuario;
		$admin     = $ci->session->admin;
		$super     = $ci->session->superAdmin;

		// ==============================
		// VARIABLES SIEMPRE DEFINIDAS
		// ==============================
		$totalNotificaciones = 0;
		$notificaciones      = [];

		// ==============================
		// RUTA INICIO
		// ==============================
		if ($admin == 1) {
			$iniciourl = 'inicio';
			// ==============================
			// NOTIFICACIONES (SOLO ADMIN)
			// ==============================
			$ci->load->helper('notificaciones');

			if ($idUsuario && function_exists('ContarNotificacionesPendientes')) {
				$totalNotificaciones = ContarNotificacionesPendientes($idUsuario);
				$notificaciones      = ObtenerNotificacionesPendientes($idUsuario);
				$notificaciones      = array_slice($notificaciones, 0, 7);
			}

		} else {
			$condicionExiste    = array('idUsuario' => $idUsuario);
			$datosUsuario       = TraerUnDato('usuario',$condicionExiste);
			$condicionExisteRol = array('idRol' => $datosUsuario->rolUsuario);
			$datosRol           = TraerUnDato('usuarioRoles',$condicionExisteRol);
			$iniciourl          = $datosRol->rutaRol;
		}

			$ci =& get_instance();
			$ci->load->model('CoreModel');

		// ==============================
		// MENÚS (LÓGICA ORIGINAL)
		// ==============================
		$modulos       = array();
		$menusMostrar  = array();

		if ($super == 1) {
			$menus = $ci->CoreModel->TraerMenus();
			if ($menus !== false) {
				foreach ($menus as $menu) {
					$idMenu         = $menu->idMenu;
					$menu->modulos  = $ci->CoreModel->TraerModulos($idMenu);
					$menusMostrar[] = $menu;
				}
			}
		} else {
			$menus = $ci->CoreModel->TraerMenusUsuario($idUsuario,$admin);
			if ($menus !== false) {
				foreach ($menus as $menu) {
					$idMenu         = $menu->idMenu;
					$menu->modulos  = $ci->CoreModel->TraerModulosUsuario($idUsuario,$admin,$idMenu);
					$menusMostrar[] = $menu;
				}
			}
		}
		
		// ==============================
		// DATOS PAGO (ORIGINAL)
		// ==============================
		$condicionPago = array('pagado' => 0);
		$datosPago     = TraerUnDatoJoinCampo(
			'pagos',
			$condicionPago,
			array(),
			'DATEDIFF(Concat(anio,"-",mes,"-",dia),CURDATE()) as numeroDias,mes',
			''
		);

		$numeroDias = $datosPago->numeroDias;
		$mes        = $datosPago->mes;
		
		// ==============================
		// DATOS VISTAS
		// ==============================
		$menuDatos = array(
			'menus'     => $menusMostrar,
			'iniciourl'=> $iniciourl,
		);

		$barraDatos = array(
			'usuario'            => $usuario,
			'nombre'             => $nombre,
			'numeroDias'         => $numeroDias,
			'mes'                => $mes,
			'totalNotificaciones'=> $totalNotificaciones,
			'notificaciones'     => $notificaciones,
		);

		$encabezadoDatos = array('titulo' => $titulo);
		$pieDatos        = array('numeroDias' => $numeroDias);

		// ==============================
		// RENDER
		// ==============================
		$ci->load->view('plantilla/encabezado',$encabezadoDatos);
		$ci->load->view('plantilla/barraSuperior',$barraDatos);
		$ci->load->view('plantilla/menu',$menuDatos);
		$ci->load->view($vista, $vistaDatos);
		$ci->load->view('plantilla/pie',$pieDatos);
		$ci->load->view('plantilla/extras',$extras);

		return true;
	}
}
		
		

if (!function_exists('GblPlantilla2')){
	// Funcion para renderizar la plantilla
		// $vista = nombre de la vista, $vistaDatos = array con los datos que se envian a la vista
		// $extras = js o css que se usen especificamente en la vista en un array
	function GblPlantilla2($vista,$vistaDatos=array(),$extras=array(),$titulo=""){
		
		$ci =& get_instance();
		GblValidarSesion($ci);
		$ci->load->model('CoreModel');
		$usuario = $ci->session->usuario;
		$nombre = $ci->session->nombre;
		$idUsuario = $ci->session->idUsuario;
		$admin = $ci->session->admin;
		$super = $ci->session->superAdmin;
		$ci =& get_instance();
		GblValidarSesion($ci);
		$ci->load->model('CoreModel');

		$usuario   = $ci->session->usuario;
		$nombre    = $ci->session->nombre;
		$idUsuario = $ci->session->idUsuario;
		$admin     = $ci->session->admin;
		$super     = $ci->session->superAdmin;
		
		// ==============================
		// VARIABLES SEGURAS SIEMPRE
		// ==============================
		$totalNotificaciones = 0;
		$notificaciones = [];

		if ($admin == 1) {
			$iniciourl = 'inicio';
		} else {
			$condicionExiste    = array('idUsuario' => $idUsuario);
			$datosUsuario       = TraerUnDato('usuario',$condicionExiste);
			$condicionExisteRol = array('idRol' => $datosUsuario->rolUsuario);
			$datosRol           = TraerUnDato('usuarioRoles',$condicionExisteRol);
			$iniciourl          = $datosRol->rutaRol;
		}
		
				
		$modulos      = array();
		$menusMostrar = array();

		if ($super == 1) {
			$menus = $ci->CoreModel->TraerMenus();
			if ($menus !== false) {
				foreach ($menus as $menu) {
					$idMenu         = $menu->idMenu;
					$menu->modulos  = $ci->CoreModel->TraerModulos($idMenu);
					$menusMostrar[] = $menu;
				}
			}
		} else {
			$menus = $ci->CoreModel->TraerMenusUsuario($idUsuario,$admin);
			if ($menus !== false) {
				foreach ($menus as $menu) {
					$idMenu         = $menu->idMenu;
					$menu->modulos  = $ci->CoreModel->TraerModulosUsuario($idUsuario,$admin,$idMenu);
					$menusMostrar[] = $menu;
				}
			}
		}
		
		$menuDatos = array(
			'menus'=> $menusMostrar,
			'iniciourl'=> $iniciourl,
		);
		
	

   		$encabezadoDatos = array(
			'titulo' => $titulo,
		);
		$ci->load->view('plantilla2/encabezado',$encabezadoDatos);
		// $ci->load->view('plantilla2/barraSuperior',$barraDatos);
		$ci->load->view('plantilla2/menu',$menuDatos);
		$ci->load->view($vista, $vistaDatos);
		$ci->load->view('plantilla2/pie');
		$ci->load->view('plantilla2/extras',$extras);
    return true;
				}

			}
		
	
?>