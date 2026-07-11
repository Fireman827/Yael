<?php
 defined('BASEPATH') OR exit('No direct script access allowed');

class CoreModel extends CI_Model{
  // Devuelve la lista de menus/controladores para un super admin
  function TraerMenus(){
    $this->db->select("menu.*");
    $this->db->where("menu.visibleMenu", 1);
    $this->db->where("menuModulos.mostrarModulo", 1);
    $this->db->join('menuModulos', 'menuModulos.idMenu = menu.idMenu', 'left');
    $this->db->order_by('menu.prioridadMenu', 'ASC');;
    $this->db->group_by('menu.idMenu');
    $consulta = $this->db->get('menu');
    if ($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

  // Devuelve la lista de menus/controladores para un usuario
  function TraerMenusUsuario($idUsuario,$admin){
    $this->db->select("menu.*");
    $this->db->where("menu.visibleMenu", 1);
    $this->db->where("menuModulos.mostrarModulo", 1);
    $this->db->where("menu.adminMenu", 0);
    $this->db->join('menuModulos', 'menuModulos.idMenu = menu.idMenu', 'left');
    if($admin==0){
      $this->db->where('usuarioPermisos.idUsuario', $idUsuario);
      $this->db->join('usuarioPermisos', 'usuarioPermisos.idModulo = menuModulos.idMenuModulo','left');
    }
    $this->db->order_by('menu.prioridadMenu', 'ASC');;
    $this->db->group_by('menu.idMenu');
    $consulta = $this->db->get('menu');
    if ($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

  // Devuelve la lista de modulos/funciones para un super admin
  function TraerModulos($idMenu){
    $this->db->where("menuModulos.mostrarModulo", 1);
    $this->db->where("menuModulos.adminModulo", 0);
    $this->db->where("menuModulos.idMenu", $idMenu);
    $consulta = $this->db->get('menuModulos');
    if ($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

  // Devuelve la lista de modulos/funciones para un usuario
  function TraerModulosUsuario($idUsuario,$admin,$idMenu,$todos=0){
    if($admin==0){
      $this->db->where('usuarioPermisos.idUsuario', $idUsuario);
      $this->db->join('usuarioPermisos', 'usuarioPermisos.idModulo = menuModulos.idMenuModulo');
    }
    if(!$todos){
      $this->db->where("menuModulos.mostrarModulo", 1);
    }
    $this->db->where("menuModulos.adminModulo", 0);
    $this->db->where("menuModulos.idMenu", $idMenu);
    $this->db->order_by("menuModulos.controladorModulo","asc");
    $this->db->order_by("menuModulos.idMenuModulo","asc");
    $consulta = $this->db->get('menuModulos');
    if ($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

  // Funcion que verifica si un usuario especifico tiene acceso al controlador
  // y la funcion que intenta ejecutar
  function PermisosUsuario($idUsuario,$funcion,$controlador){
    $this->db->join('menuModulos as mm', 'mm.idMenuModulo = p.idModulo');
    $this->db->join('menu as m', 'm.idMenu = mm.idMenu');
    $this->db->where("mm.funcionModulo", $funcion);
    $this->db->where("mm.controladorModulo", $controlador);
    $this->db->where("p.idUsuario", $idUsuario);
    $consulta = $this->db->get("usuarioPermisos as p");
    if ($consulta->num_rows() > 0){
      return true;
    }
    return false;
  }

  //Funcion que devuelve el valor de un elemento de configuracion segun el parametro solicitado
  function TraerConfiguracion($parametro){
    $this->db->where("parametroConfiguracion", $parametro);
    $consulta = $this->db->get("configuraciones");
    if ($consulta->num_rows() > 0){
      return $consulta->row();
    }
    return false;
  }
  function TraerConfiguracionFe($parametro,$idSucursal){
    $this->db->join('sucursal', 'FE_Configuraciones.idConfiguracion = sucursal.idConfiguracionFe');
    $this->db->where("parametro", $parametro);
    $this->db->where("sucursal.idSucursal", $idSucursal);
    $consulta = $this->db->get("FE_Configuraciones");
    if ($consulta->num_rows() > 0){
      return $consulta->row();
    }
    return false;
  }
  //Funcion que inicia una transaccion en sql
  function IniciarTransaccion(){
      $this->db->trans_begin();
  }
  //Funcion que deshace una transaccion en sql
  function DeshacerTransaccion(){
      $this->db->trans_rollback();
  }
  //Funcion que completa una transaccion en sql
  function EjecutarTransaccion(){
      $this->db->trans_commit();
  }
  //Funcion que ejecuta una Query libre
  function SimpleQuery($datos){
    $consulta = $this->db->query($datos);
    return $consulta;
  }
  //Funcion que almacena datos en una tabla
  function GuardarDatos($tabla,$datos){
    $this->db->insert($tabla, $datos);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($this->db->affected_rows() > 0){
      return $this->db->insert_id();
    } else{
      return false;
    }
  }
  function ActualizarCorrelativo($tabla,$condicion,$campo,$incremento){
    $this->db->set($campo, "(".$campo." + (".$incremento.")) ", FALSE);
    $this->db->where($condicion);
    $this->db->update($tabla); // gives UPDATE mytable SET field = field+1 WHERE id = 2
    if($this->db->affected_rows() > 0){
      return true;
    }
    return false;
  }
  //Funcion que modifica datos en una tabla
  function EditarDatos($tabla,$datos,$condicion){
    $this->db->set($datos);
    $this->db->where($condicion);
    // update() ya regresa true/false segun si la consulta se ejecuto sin error.
    // No usar affected_rows() aqui: MySQL lo deja en 0 cuando el UPDATE corre bien
    // pero los valores nuevos son identicos a los que ya habia (nada que cambiar),
    // lo cual se estaba reportando como un guardado fallido aunque no hubo error real.
    return $this->db->update($tabla);
  }
  //Funcion que elimina datos en una tabla
  function EliminarDatos($tabla,$condicion){
    $this->db->where($condicion);
    $this->db->delete($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($this->db->affected_rows() > 0){
      return true;
    }
    return false;
  }

  //Funcion que trae datos de una tabla
  function TraerDatosTabla($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion){

    $this->db->from($tabla);
    if($condicion!=""){
      $this->db->where($condicion);
    }
		if (!empty($busqueda)){
      $this->db->group_start();
      $x = 0;
			foreach ($columnasValidas as $campo){
        if ($x == 0){
          $this->db->like($campo, $busqueda);
				} else{
          $this->db->or_like($campo, $busqueda);
				}
				$x++;
			}
      $this->db->group_end();
		}
    if ($ordenCampos !=	 null){
      $this->db->order_by($ordenCampos, $ordenDirecion);
    }
		$this->db->limit($cantidad, $desde);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0){
      //return $this->db->query($query);
			return $consulta->result();
		} else{
			return false;
		}
  }

  function TraerDatosTablaJoin($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion, $joins){

    $this->db->from($tabla);
    if($condicion!=""){
      $this->db->where($condicion);
    }
    $campos = 0;
    if(!empty($joins)) {
      foreach ($joins as $joi) {
        if(!empty($joi["tipo"])){
          $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
        } else {
          $this->db->join($joi["tabla"],$joi["condicion"]);
        }
        if(!empty($joi["campos"])){
          $campos = 1;
          $this->db->select($joi["campos"]);
        }
      }
    }
    if($campos != 0){ $this->db->select($tabla.".*"); }

		if (!empty($busqueda)){
      $this->db->group_start();
      $x = 0;
			foreach ($columnasValidas as $campo){
        if ($x == 0){
          $this->db->like($campo, $busqueda);
				} else{
          $this->db->or_like($campo, $busqueda);
				}
				$x++;
			}
      $this->db->group_end();
		}
    if ($ordenCampos!=null || $ordenCampos!=""){
      $this->db->order_by($ordenCampos, $ordenDirecion);
    }
		$this->db->limit($cantidad, $desde);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0){
      //return $this->db->query($query);
			return $consulta->result();
		} else{
			return false;
		}
  }

  //Función que trae datos utilizando join y agrupando por un campo
  function TraerDatosTablaJoinGroup(
    $tabla,
    $ordenCampos,
    $busqueda,
    $columnasValidas,
    $cantidad,
    $desde,
    $ordenDirecion,
    $condicion = "",
    $joins = "",
    $select = "",
    $group = ""
){
    if ($select != "") {
        $this->db->select($select, false);
    }

    $this->db->from($tabla);

    if ($condicion != "") {
        $this->db->where($condicion);
    }

    if (!empty($joins)) {
        foreach ($joins as $joi) {
            if (!empty($joi["tipo"])) {
                $this->db->join($joi["tabla"], $joi["condicion"], $joi["tipo"]);
            } else {
                $this->db->join($joi["tabla"], $joi["condicion"]);
            }
        }
    }

    if (!empty($busqueda)) {
        $this->db->group_start();
        $x = 0;
        foreach ($columnasValidas as $campo) {
            if ($x == 0) {
                $this->db->like($campo, $busqueda);
            } else {
                $this->db->or_like($campo, $busqueda);
            }
            $x++;
        }
        $this->db->group_end();
    }

    if ($group != "") {
        $this->db->group_by($group);
    }

    if ($ordenCampos != null && $ordenCampos != "") {
        $this->db->order_by($ordenCampos, $ordenDirecion);
    }

    $this->db->limit($cantidad, $desde);
    $consulta = $this->db->get();

    if ($consulta->num_rows() > 0) {
        return $consulta->result();
    }

    return false;
}


  //Funcion que trae datos de una tabla
  function TraerTotalDatos($tabla,$condicion="",$busqueda=[], $columnasValidas=[],$join=[],$group=""){
    if($condicion != ""){
      $this->db->where($condicion);
    }
    if (!empty($busqueda)){
      $this->db->group_start();
      $x = 0;
			foreach ($columnasValidas as $campo){
        if ($x == 0){
          $this->db->like($campo, $busqueda);
				} else {
          $this->db->or_like($campo, $busqueda);
				}
				$x++;
			}
      $this->db->group_end();
		}
    if(!empty($join))
    {
      foreach ($join as $joi)
      {
        if(!empty($joi["tipo"])){
          $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
        }
        else{
          $this->db->join($joi["tabla"],$joi["condicion"]);
        }
        if(!empty($joi["campos"])){
          $campos = 1;
          $this->db->select($joi["campos"]);
        }
      }
    }
    if($group!=""){
      $this->db->group_by($group);
    }

    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if ($consulta->num_rows() > 0){
			return $consulta->num_rows();
		} else{
			return false;
		}
  }

  //Funcion que trae datos de una tabla
  function TraerDatos($tabla,$condicion="",$ordenCampos=""){
    if($condicion != ""){
      $this->db->where($condicion);
    }
    if ($ordenCampos !=	 ""){
      $this->db->order_by($ordenCampos);
    }
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    //echo $this->db->last_query();
    if($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

    //Funcion que trae datos de una tabla con otro nombre
    function TraerDatosRenombrados($tabla,$campos="",$condicion="",$ordenCampos="",$group =""){
      if($condicion != ""){
        $this->db->where($condicion);
      }
      if ($ordenCampos !=	 ""){
        $this->db->order_by($ordenCampos);
      }
      if ($campos !=	 ""){
        if(is_array($campos)){
          foreach($campos as $index => $campo){
            $this->db->select($index." AS ".$campo);
          }
        }
        else{
          $this->db->select($ordenCampos);
        }
      }
      if($group!=""){
        $this->db->group_by($group);
      }
      $consulta = $this->db->get($tabla);
      //Para pruebas retorna la ultima query
      //echo $this->db->last_query();
      if($consulta->num_rows() > 0){
        return $consulta->result();
      }
      return false;
    }

    //Funcion que trae datos de una tabla filtrando por coincidencia (Like) - usada en autocompletados
    function TraerDatosComo($tabla,$condicion1="Where", $condicion2="Like",$join = [],$group =""){
      if($condicion1 != ""){
        $this->db->where($condicion1);
      }
      if($condicion2 != ""){
        $this->db->like($condicion2);
      }
      $campos = 0;
      if(!empty($join))
      {
        foreach ($join as $joi)
        {
          if(!empty($joi["tipo"])){
            $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
          }
          else{
            $this->db->join($joi["tabla"],$joi["condicion"]);
          }
          if(!empty($joi["campos"])){
            $campos = 1;
            $this->db->select($joi["campos"]);
          }
        }
      }
      if($group != ""){
        $this->db->group_by($group);
      }
      if($campos != 0){ $this->db->select($tabla.".*"); }
      $consulta = $this->db->get($tabla);
      //Para pruebas retorna la ultima query
      // return $this->db->last_query();
      if($consulta->num_rows() > 0){
        return $consulta->result();
      }
      return false;
    }


  //Funcion que trae datos de una tabla
  function TraerDatosJoin(
    $tabla,
    $condicion = "",
    $ordenCampos = "",
    $join = [],
    $group = ""
) {
    $this->db->from($tabla);

    // =========================
    // JOINS
    // =========================
    // NOTA: el select de los "campos" de cada join se agrega ANTES que
    // "tabla.*" porque algunos controladores (ej. Touch::TraerProductoCategoria)
    // pasan cosas como "DISTINCT(tabla.columna)" en "campos". En MySQL,
    // DISTINCT solo es válido como el primer elemento de la lista del SELECT;
    // si "tabla.*" va primero (SELECT producto.*, DISTINCT(...)) MySQL lanza
    // error de sintaxis. Agregándolo primero se genera
    // "SELECT DISTINCT(...), producto.*", que es válido.
    if (!empty($join)) {
        foreach ($join as $j) {

            // Compatibilidad con tu proyecto (array asociativo)
            if (isset($j["tabla"], $j["condicion"])) {
                $tipo = isset($j["tipo"]) ? $j["tipo"] : 'left';

                if (!empty($j["campos"])) {
                    $this->db->select($j["campos"]);
                }

                $this->db->join($j["tabla"], $j["condicion"], $tipo);
            }
        }
    }

    // SELECT base (va después de los "campos" de los joins; ver nota arriba)
    $this->db->select($tabla . '.*');

    // =========================
    // WHERE
    // =========================
    if (is_array($condicion)) {
        foreach ($condicion as $campo => $valor) {
            if (strpos($campo, '!=') !== false) {
                $campo = trim(str_replace('!=', '', $campo));
                $this->db->where("$campo !=", $valor);
            } else {
                $this->db->where($campo, $valor);
            }
        }
    } elseif ($condicion !== "") {
        $this->db->where($condicion);
    }

    // =========================
    // GROUP BY (SIN MAX)
    // =========================
    //if ($group !== "") {
      //  $this->db->group_by($group);
   // }

    // =========================
    // ORDER BY
    // =========================
    if ($ordenCampos !== "") {
        $this->db->order_by($ordenCampos);
    }

    $query = $this->db->get();

    return ($query->num_rows() > 0) ? $query->result() : false;
}



  //Funcion que traer un dato especifico de una tabla
  function TraerUnDato($tabla,$condicion,$order=""){
    $this->db->where($condicion);
    if($order != ""){
      $this->db->order_by($order);
    }
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // echo $order;
    if($consulta->num_rows() > 0){
      // var_dump($this->db->last_query());
      return $consulta->row();
    }
    return false;
  }
  //Funcion que traer un dato especifico de una tabla
  function TraerUnDatoJoin($tabla,$condicion,$join=[], $ordenCampos = "", $group=""){
    if($condicion != ""){ $this->db->where($condicion); }
    if ($ordenCampos !=	 ""){ $this->db->order_by($ordenCampos); }
    $campos = 0;
    if(!empty($join)){
      foreach ($join as $joi){
        if(!empty($joi["tipo"])){
          $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
        }
        else{
          $this->db->join($joi["tabla"],$joi["condicion"]);
        }
        if(!empty($joi["campos"])){
          $campos = 1;
          $this->db->select($joi["campos"]);
        }
      }
    }
    if($group != ""){ $this->db->group_by($group); }
    if($campos != 0){ $this->db->select($tabla.".*"); }

    //$this->db->where($condicion);
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    if($consulta->num_rows() > 0){
      //var_dump($this->db->last_query());
      return $consulta->row();
    }
    return false;
  }
   //Funcion que traer un dato especifico de una tabla
   function TraerUnDatoIndividual($tabla,$campo,$condicion){
    $this->db->select($campo);
    $this->db->where($condicion);

    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($consulta->num_rows() > 0){
      // foreach ($consulta->result_array() as $fila) {
      //   return $fila[$campo];
      // }
      return $consulta->result_array();
    }
    return false;
  }
  //Funcion que traer un dato especifico de una tabla
  function TraerMaxValor($tabla,$campo,$condicion){
    $this->db->select_max($campo);
    if($condicion != ""){
      $this->db->where($condicion);
    }
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($consulta->num_rows() > 0){
      foreach ($consulta->result_array() as $fila) {
        return $fila[$campo];
      }
      //return $consulta->;
    }
    return false;
  }

  //Funcion que traer un dato especifico de una tabla
  function ExistenDatos($tabla,$condicion){
    $this->db->where($condicion);
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($consulta->num_rows() > 0){
      return true;
    }
    return false;
  }
  //Funcion que modifica datos en una tabla
  function MostrarError(){
    return $this->db->error();
  }
  function TraerUnDatoJoinCampo($tabla,$condicion,$join=[], $campo="", $group="")
  {
    if($condicion != ""){ $this->db->where($condicion); }
    if(!empty($join)){
      foreach ($join as $joi){
        if(!empty($joi["tipo"])){
          $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
        }
        else{
          $this->db->join($joi["tabla"],$joi["condicion"]);
        }
        if(!empty($joi["campos"])){
          $campos = 1;
          $this->db->select($joi["campos"]);
        }
      }
    }
    if($group != ""){ $this->db->group_by($group); }
    if($campo != "") { $this->db->select($campo); }
    // if($campos != 0){ $this->db->select($tabla.".*"); }

    //$this->db->where($condicion);
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    if($consulta->num_rows() > 0){
      // var_dump($this->db->last_query());
      return $consulta->row();
    }
    return false;
  }

  function TraerTotalDatosJoin($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion, $joins,$condicion_in=""){

    $this->db->from($tabla);
    if($condicion!=""){
      $this->db->where($condicion);
    }
    if($condicion_in != ""){
      foreach ($condicion_in as $field => $ins) {
        $this->db->where_in($field, $ins);
      }
    }
    $campos = 0;
    if(!empty($joins)) {
      foreach ($joins as $joi) {
        if(!empty($joi["tipo"])){
          $this->db->join($joi["tabla"],$joi["condicion"],$joi["tipo"]);
        } else {
          $this->db->join($joi["tabla"],$joi["condicion"]);
        }
        if(!empty($joi["campos"])){
          $campos = 1;
          $this->db->select($joi["campos"]);
        }
      }
    }
    if($campos != 0){ $this->db->select($tabla.".*"); }

    if (!empty($busqueda)){
      $this->db->group_start();
      $x = 0;
      foreach ($columnasValidas as $campo){
        if ($x == 0){
          $this->db->like($campo, $busqueda);
        } else{
          $this->db->or_like($campo, $busqueda);
        }
        $x++;
      }
      $this->db->group_end();
    }
    if ($ordenCampos!=null || $ordenCampos!=""){
      $this->db->order_by($ordenCampos, $ordenDirecion);
    }
    //$this->db->limit($cantidad, $desde);
    $consulta = $this->db->get();
    if ($consulta->num_rows() > 0){
      //return $this->db->query($query);
      return $consulta->num_rows();
    } else{
      return false;
    }
  }
/* =====================================================
   NOTIFICACIONES – INSUMOS CON STOCK
===================================================== */
public function obtenerInsumoStock()
{
    return $this->db
        ->select('
            i.idInsumo,
            i.nombreInsumo,
            i.stockMinimoInsumo,
            s.cantidadInsumoStock,
            s.idSucursal
        ')
        ->from('insumo i')
        ->join('insumostock s', 's.idInsumo = i.idInsumo')
        ->where('i.activoInsumo', 1)
        ->get()
        ->result();
}

public function listarNotificacionesUsuario($idUsuario)
    {
        if (!$idUsuario) return [];

        $usuario = $this->db
            ->where('idUsuario', $idUsuario)
            ->get('usuario')
            ->row();

        if (!$usuario) return [];

        $this->db
            ->from('notificaciones')
            ->where('estado', 'pendiente')
            ->where('idSucursal', $usuario->idSucursalUsuario)
            ->order_by('fechaCreacion', 'DESC');

        // 🔐 Si no es admin, solo las propias
        if (!$usuario->adminUsuario && !$usuario->superAdminUsuario) {
            $this->db->where('idUsuario', $idUsuario);
        }

        return $this->db->get()->result();
    }

    /* ==========================================
       CONTADOR DE NOTIFICACIONES (CAMPANA)
       ========================================== */
    public function contarNotificacionesSucursal($idSucursal)
    {
        return (int) $this->db
            ->where('estado', 'pendiente')
            ->where('idSucursal', $idSucursal)
            ->count_all_results('notificaciones');
    }

    /* ==========================================
       EXISTE NOTIFICACIÓN ACTIVA
       ========================================== */
    public function existeNotificacionActiva($tipo, $referenciaId, $idSucursal = null)
    {
        $this->db
            ->where('tipo', $tipo)
            ->where('referencia_id', $referenciaId)
            ->where('estado !=', 'cerrada');

        if ($idSucursal !== null) {
            $this->db->where('idSucursal', $idSucursal);
        }

        return $this->db->count_all_results('notificaciones') > 0;
    }

    /* ==========================================
       MARCAR NOTIFICACIÓN COMO LEÍDA
       ========================================== */
    public function marcarNotificacionLeida($idNotificacion)
    {
        return $this->db
            ->where('idNotificacion', $idNotificacion)
            ->update('notificaciones', [
                'estado' => 'leida',
                'leida'  => 1,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
    }

    /* ==========================================
       MARCAR TODAS COMO LEÍDAS (USUARIO)
       ========================================== */
    public function marcarTodasLeidasUsuario($idUsuario)
    {
        $usuario = $this->db
            ->where('idUsuario', $idUsuario)
            ->get('usuario')
            ->row();

        if (!$usuario) return false;

        $this->db
            ->where('estado', 'pendiente')
            ->where('idSucursal', $usuario->idSucursalUsuario);

        if (!$usuario->adminUsuario && !$usuario->superAdminUsuario) {
            $this->db->where('idUsuario', $idUsuario);
        }

        return $this->db->update('notificaciones', [
            'estado' => 'leida',
            'leida'  => 1,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ]);
    }

    /* ==========================================
       OBTENER NOTIFICACIONES PARA ENVÍO DE CORREO
       ========================================== */
    public function obtenerNotificacionesParaCorreo()
{
    return $this->db
        ->where('estado', 'pendiente')
        ->where('enviarCorreo', 1)
        ->where('correoEnviado', 0)
        ->order_by('fechaCreacion', 'ASC')
        ->get('notificaciones')
        ->result();

    }

    /* ==========================================
       REABRIR NOTIFICACIONES DE PAGOS
       ========================================== */
    public function reabrirNotificacionesPagosPendientes()
    {
        $this->db->query("
            UPDATE notificaciones n
            JOIN pagos p ON p.idPago = n.referencia_id
            SET n.estado = 'pendiente'
            WHERE n.tipo = 'PAGO'
              AND p.estadoPago = 'pendiente'
              AND n.estado = 'cerrada'
        ");
    }

    /* ==========================================
       CERRAR NOTIFICACIONES DE PAGOS PAGADOS
       ========================================== */
    public function cerrarNotificacionesPagosPagados()
    {
        $this->db->query("
            UPDATE notificaciones n
            JOIN pagos p ON p.idPago = n.referencia_id
            SET n.estado = 'cerrada',
                n.fechaActualizacion = NOW()
            WHERE n.tipo = 'PAGO'
              AND p.estadoPago = 'pagado'
              AND n.estado != 'cerrada'
        ");
    }

    /* =====================================================
       ================== INSUMOS ==========================
       ===================================================== */

    public function obtenerInsumosBajoStockPorSucursal($idSucursal)
{
    return $this->db
        ->select('
            i.idInsumo,
            i.nombreInsumo,
            s.cantidadInsumoStock,
            i.stockMinimoInsumo
        ')
        ->from('insumo i')
        ->join('insumostock s', 's.idInsumo = i.idInsumo') // ✅ CORRECTO
        ->where('s.idSucursalInsumoStock', $idSucursal)
        ->where('s.cantidadInsumoStock <= i.stockMinimoInsumo')
        ->get()
        ->result();
}

/* =====================================================
   PAGOS – CALENDARIO + ALERTAS DE VENCIMIENTO
   Retorna pagos clasificados:
   - vencido
   - hoy
   - 3_dias
   - 7_dias
===================================================== */
public function TraerPagosCalendario($idSucursal)
{
    if (!$idSucursal) return [];

    $hoy = date('Y-m-d');

    $pagos = $this->db
        ->select('
            p.idPago,
            p.nombrePago,
            p.fechaPago,
            p.montoPago,
            p.estadoPago,
            p.idSucursalPago
        ')
        ->from('pago p')
        ->where('p.idSucursalPago', $idSucursal)
        ->where('p.estadoPago !=', 'Borrado')
        ->get()
        ->result();

    $resultado = [];

    foreach ($pagos as $pago) {

        // Diferencia en días
        $dias = (int) ((strtotime($pago->fechaPago) - strtotime($hoy)) / 86400);

        // Clasificación por vencimiento
        if ($dias < 0) {
            $tipo = 'vencido';
            $color = '#dc3545'; // rojo
        } elseif ($dias === 0) {
            $tipo = 'hoy';
            $color = '#ffc107'; // amarillo
        } elseif ($dias <= 3) {
            $tipo = '3_dias';
            $color = '#fd7e14'; // naranja
        } elseif ($dias <= 7) {
            $tipo = '7_dias';
            $color = '#0dcaf0'; // celeste
        } else {
            // No entra al calendario de alertas
            continue;
        }

        // Evento compatible con FullCalendar
        $resultado[] = [
            'idPago'      => $pago->idPago,
            'title'       => $pago->nombrePago . ' ($' . number_format($pago->montoPago, 2) . ')',
            'start'       => $pago->fechaPago,
            'allDay'      => true,
            'color'       => $color,
            'tipo'        => $tipo,
            'dias'        => $dias,
            'estadoPago'  => $pago->estadoPago
        ];
    }

    return $resultado;
}

    /* ==========================================
       MARCAR TODAS COMO LEÍDAS (SUCURSAL)
       ========================================== */
    public function marcarTodasLeidasSucursal($idSucursal)
    {
        return $this->db
            ->where('estado', 'pendiente')
            ->where('idSucursal', $idSucursal)
            ->update('notificaciones', [
                'estado' => 'leida',
                'leida'  => 1,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
    }

    /* ==========================================
       LISTAR NOTIFICACIONES POR SUCURSAL (ADMIN)
       ========================================== */
    public function listarNotificacionesSucursal($idSucursal)
    {
        return $this->db
            ->from('notificaciones')
            ->where('estado', 'pendiente')
            ->where('idSucursal', $idSucursal)
            ->order_by('fechaCreacion', 'DESC')
            ->get()
            ->result();
    }

}
/* End of file CoreModel.php */
