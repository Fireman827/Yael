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
    $this->db->set($campo, "".$campo." + ".$incremento."", FALSE);
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
    $this->db->update($tabla);
    //Para pruebas retorna la ultima query
    //return $this->db->last_query();
    if($this->db->affected_rows() > 0){
      return true;
    }
    return false;
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
    if(!empty($joins))
    {
      foreach ($joins as $joi)
      {
        $this->db->join($joi["tabla"],$joi["condicion"]);
      }
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
  function TraerDatosTablaJoinGroup($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion, $joins,$select, $group){
    if($select!=""){
      $this->db->select($select);
    }
    $this->db->from($tabla);
    if($condicion!=""){
      $this->db->where($condicion);
    }
    if(!empty($joins)){
      foreach ($joins as $joi){
        $this->db->join($joi["tabla"],$joi["condicion"]);
      }
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
    if($group!=""){
      $this->db->group_by($group);
    }
    if ($ordenCampos !=	 null){
      $this->db->order_by($ordenCampos, $ordenDirecion);
    }
		$this->db->limit($cantidad, $desde);
		$consulta = $this->db->get();
    //return $this->db->last_query();
		if ($consulta->num_rows() > 0){
      //return $this->db->query($query);
			return $consulta->result();
		} else {
			return false;
		}
  }

  //Funcion que trae datos de una tabla
  function TraerTotalDatos($tabla,$condicion=""){
    if($condicion != ""){
      $this->db->where($condicion);
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

  //Funcion que trae datos de una tabla
  function TraerDatosJoin($tabla, $condicion = "", $ordenCampos = "", $join=[], $group=""){

    if($condicion != ""){
      $this->db->where($condicion);
    }

    if ($ordenCampos !=	 ""){
      $this->db->order_by($ordenCampos);
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
    if($campos != 0){ $this->db->select($tabla.".*"); }
    if($group != ""){
      $this->db->group_by($group);
    }
    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    // return $this->db->last_query();
    if($consulta->num_rows() > 0){
      return $consulta->result();
    }
    return false;
  }

    //Funcion que trae datos de una tabla
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

  //Funcion que traer un dato especifico de una tabla
  function TraerUnDato($tabla,$condicion){
    $this->db->where($condicion);

    $consulta = $this->db->get($tabla);
    //Para pruebas retorna la ultima query
    if($consulta->num_rows() > 0){
      //var_dump($this->db->last_query());
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

}

/* End of file CoreModel.php */
