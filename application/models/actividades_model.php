<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Actividades_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
/* Funciones para trabajar con la tabla profesores */
    public function reg_profesor($datos){
        $this->db->insert('profesores', $datos);
        return $this->db->insert_id();   
    }
    public function update_profesor($datos,$id){
        $this->db->where("id",$id);
        $this->db->update('profesores', $datos);
        return true;   
    }    
    public function get_profesores($id_entidad){
    	$this->db->order_by("apellido", "asc"); 
    	$this->db->where("estado", "1"); 
	if ( $id_entidad > 0 ) {
    		$this->db->where("id_entidad", $id_entidad); 
	}
        $query = $this->db->get("profesores");
        return $query->result();
    }    
    public function get_profesor($id){
        $this->db->where("id", $id); 
        $query = $this->db->get("profesores");
        if($query->num_rows() == '0'){
            return false;
        }else{        
            return $query->row();
        }
    }
    public function del_profesor($id){
        $this->db->where("id", $id); 
        $query = $this->db->update("profesores",array("estado"=>'0'));
        return true;
    } 
/* Fin funciones para trabajar con la tabla profesores */

/* Funciones para trabajar con la tabla actividades */
    public function reg_actividad($datos){
        $id_entidad = $datos['id_entidad'];
        $qry = "SELECT MAX(aid) max_id FROM actividades WHERE id_entidad = $id_entidad; ";
        $resultado = $this->db->query($qry)->row();
        if ( $resultado->max_id ) {
                $datos['aid'] = $resultado->max_id+1;
        } else {
                $datos['aid'] = 1;
        }


        $this->db->insert('actividades', $datos);
        return $this->db->insert_id();   
    }
    public function update_actividad($datos,$id){
        $this->db->where("id",$id);
        $this->db->update('actividades', $datos);
        return true;   
    }    
    public function get_actividades_list($id_entidad){
        $this->db->where("id_entidad",$id_entidad);
        $this->db->order_by("nombre", "asc");
        $query = $this->db->get("actividades");
        return $query->result();
    }
    public function get_actividades($id_entidad){
        $this->db->order_by("nombre", "asc"); 
        $this->db->where("id_entidad",$id_entidad);
        $this->db->where("estado",'1');
        $query = $this->db->get("actividades");
        return $query->result();
    }    
    public function get_actividad($id){
        $this->db->where("id", $id); 
        $query = $this->db->get("actividades");
        return $query->row();
    }
    public function del_actividad($id){
        $this->db->where("id", $id); 
        $query = $this->db->update("actividades", array("estado"=>'0'));
        return true;
    }
/* Fin de funciones para trabajar con la tabla actividades */

/* Funciones para trabajar con la tabla actividades_asociadas */
    public function get_act_asoc_puntual($id_entidad,$sid, $aid){
        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $this->db->where("estado", 1);
        $query = $this->db->get("actividades_asociadas");
        if ($query->num_rows() == 0){
		return false;
	} else {
		$asoc = $query->row();
                $actividad = $this->get_actividad($asoc->aid);
                $actividad->estado = $asoc->estado;
                $actividad->asoc_id = $asoc->id;
                if($actividad->estado == '1'){
                    $actividad->alta = $this->show_date($asoc->date);
                }else{
                    $actividad->alta = $this->show_date($asoc->date_alta);
                    $actividad->baja = $this->show_date($asoc->date);
                }
                $actividad->federado = $asoc->federado;
                $actividad->descuento = $asoc->descuento;
                $actividad->monto_porcentaje = $asoc->monto_porcentaje;
                $actividad->id_entidad = $id_entidad;
                return $actividad;
	}
    }

    public function get_act_asoc($id_entidad, $sid){
        $this->db->order_by("estado", "desc");
        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("sid", $sid);
        $query = $this->db->get("actividades_asociadas");
        if ($query->num_rows() == 0){
            $act_asoc = new stdClass();
            return $act_asoc;
        }else{
            foreach ($query->result() as $asoc) {
                $actividad = $this->get_actividad($asoc->aid);
                $actividad->estado = $asoc->estado;
                $actividad->asoc_id = $asoc->id;
                if($actividad->estado == '1'){
                    $actividad->alta = $this->show_date($asoc->date);
                }else{
                    $actividad->alta = $this->show_date($asoc->date_alta);
                    $actividad->baja = $this->show_date($asoc->date);
                }
                $actividad->federado = $asoc->federado;
                $actividad->descuento = $asoc->descuento;
                $actividad->monto_porcentaje = $asoc->monto_porcentaje;
                $actividad->id_entidad = $id_entidad;
                $act_asoc[] = $actividad;
            }            
            return $act_asoc;
        } 
    }

    public function get_act_asoc_tutor($id_entidad, $tutor_id){
        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("tutor", $tutor_id);
        $query = $this->db->get("socios");
        if ($query->num_rows() == 0){
            $act_asoc = new stdClass();
            return $act_asoc;
	    } else {
            $act_asoc = array();
            foreach ($query->result() as $asoc) {
		        $sid=$asoc->id;
        	    $this->db->order_by("estado", "desc");
        	    $this->db->where("sid", $sid);
        	    $query = $this->db->get("actividades_asociadas");
        	    if ($query->num_rows() > 0) {
            		foreach ($query->result() as $asoc) {
                		$actividad = $this->get_actividad($asoc->aid);
                		$actividad->estado = $asoc->estado;
                		$actividad->asoc_id = $asoc->id;
                		if($actividad->estado == '1') {
                    			$actividad->alta = $this->show_date($asoc->date);
                		} else {
                    			$actividad->alta = $this->show_date($asoc->date_alta);
                    			$actividad->baja = $this->show_date($asoc->date);
                		}
                		$actividad->federado = $asoc->federado;
                		$actividad->descuento = $asoc->descuento;
                		$actividad->monto_porcentaje = $asoc->monto_porcentaje;
                		$actividad->id_entidad = $id_entidad;
                		$act_asoc[] = $actividad;
            		}
		        }
	        }
            return $act_asoc;
        }
    }

    public function act_baja_asoc($id_entidad,$sid,$aid){

        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $this->db->where("estado", '1');
        $query = $this->db->get('actividades_asociadas',1);
        $fecha = $query->row();
        $fecha = $fecha->date;

        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $this->db->where("estado", '1');
        $query = $this->db->update("actividades_asociadas",array('estado'=>'0','date_alta'=>$fecha));
    }

    public function act_baja($id_entidad,$sid,$aid){
        
        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("id", $aid);
        $query = $this->db->get('actividades_asociadas');
        $fecha = $query->row();
        $fecha = $fecha->date;

        $this->db->where("id_entidad", $id_entidad);
        $this->db->where("sid", $sid);
        $this->db->where("id", $aid);
        $query = $this->db->update("actividades_asociadas",array('estado'=>'0','date_alta'=>$fecha));
        $alta = $this->show_date($fecha);
        $fecha = array();
        $fecha['alta'] = $alta;
        $fecha['baja'] = date('d/m/Y');
        $fecha['asoc_id'] = $aid;
        $fecha = json_encode($fecha);
        return $fecha;
    }

    public function act_alta($data){        
        $query = $this->db->insert("actividades_asociadas",$data);
        $iid = $this->db->insert_id(); 
        $actividad = $this->get_actividad($data['aid']);
        $actividad->asoc_id = $iid;
        $actividad->alta = date("d/m/Y");
        $actividad = json_encode($actividad);
        return $actividad;
    } 

    public function show_date($fecha){
        $fecha = explode('-', $fecha);
        $fecha2 = explode(' ', $fecha[2]);
        return $fecha2[0].'/'.$fecha[1].'/'.$fecha[0];
    }

    public function act_federado($id_entidad,$aid){        
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('id',$aid);
        $query = $this->db->get('actividades_asociadas');
        $actual = $query->row();
        $this->db->where('id',$aid);
	    if ( $actual->federado == 0 ) {
        	    $query = $this->db->update("actividades_asociadas",array('federado'=>'1'));
	    } else {
        	    $query = $this->db->update("actividades_asociadas",array('federado'=>'0'));
	    }
        return true;
    } 

    public function act_peso($id_entidad,$aid){        
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('id',$aid);
        $query = $this->db->update("actividades_asociadas",array('monto_porcentaje'=>'0'));
        return true;
    } 

    public function act_porc($id_entidad,$aid){        
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('id',$aid);
        $query = $this->db->update("actividades_asociadas",array('monto_porcentaje'=>'1'));
        return true;
    } 

    public function get_socactiv($id_entidad, $id_actividad=-1,$id_comision=0,$mora=0,$id_estado=-1){
        $qry = "DROP TEMPORARY TABLE IF EXISTS tmp_socios_activos;";
        $this->db->query($qry);

        if ( $id_comision > 0 ) {
                $qry1 ="DROP TEMPORARY TABLE IF EXISTS tmp_actividades; ";
                $this->db->query($qry1);
                $qry1 ="CREATE TEMPORARY TABLE tmp_actividades ( INDEX ( aid ) )
                        SELECT a.id as aid FROM actividades a WHERE a.id_entidad = $id_entidad AND a.comision = $id_comision; ";
                $this->db->query($qry1);
        }

        $qry = "CREATE TEMPORARY TABLE tmp_socios_activos
                SELECT aa.aid, a.nombre descr_act, IF(aa.descuento>0, IF(aa.monto_porcentaje=1, CONCAT(aa.descuento,' % becado'), CONCAT(aa.descuento, ' $ becados')), 'normal') beca, aa.federado, s.*
                FROM actividades_asociadas aa 
			        JOIN socios s ON aa.sid = s.id 
			        JOIN actividades a ON aa.aid = a.id ";
        if ( $id_comision > 0 ) {
                $qry .= "       JOIN tmp_actividades t ON aa.aid = t.aid ";
        }
        $qry .= "WHERE aa.id_entidad = $id_entidad AND aa.estado = 1 ";
        if ( $id_actividad >= 0 && $id_comision == 0 ) {
                $qry .= "AND aa.aid = $id_actividad ";
        }
        if ( $id_estado > 0 ) {
		if ( $id_estado == 99 ) {
                	$qry .= "AND s.suspendido = 0 ";
		} else {
                	$qry .= "AND s.suspendido = $id_estado ";
		}
        }

        $qry .= "ORDER BY aa.aid, s.id; ";
        $this->db->query($qry);

        $qry = "DROP TEMPORARY TABLE IF EXISTS tmp_pagos;";
        $this->db->query($qry);

       $qry = "CREATE TEMPORARY TABLE tmp_pagos
                SELECT ta.id sid, p.tipo, SUM(pagado-monto) saldo, MAX(pagadoel) ult_pago
                FROM tmp_socios_activos ta
                        JOIN pagos p ON ( p.id_entidad = $id_entidad AND ta.id = p.tutor_id )
                GROUP BY 1,2; ";
        $this->db->query($qry);

        $qry = "SELECT ta.*, SUM(IF(tp.tipo=1,tp.saldo,0)) mora_cs, SUM(IF(tp.tipo=4,tp.saldo,0)) mora_act, SUM(IF(tp.tipo=6,tp.saldo,0)) mora_seg, SUM(IF(tp.tipo NOT IN (1,4,6),tp.saldo,0)) mora_otro, 
                                SUM(tp.saldo) saldo, IFNULL(tp.ult_pago,'') ult_pago
                FROM tmp_socios_activos ta              
                        LEFT JOIN tmp_pagos tp ON ta.id = tp.sid ";
        if ( $mora != 0 ) {
                $qry .= "WHERE tp.saldo < 0 AND tp.saldo is NOT NULL ";
        }
        $qry .= "GROUP BY ta.id; ";

	
        $socactiv = $this->db->query($qry);
        return $socactiv->result();

    }


    public function get_socios_act($id){
        $this->load->model('socios_model');
        $this->db->where('aid',$id);
        $this->db->where('estado',1);
        $query = $this->db->get('actividades_asociadas');
        $socios = $query->result();
	$datos=array();
        foreach ($socios as $socio) {
        $datos_socio = $this->socios_model->get_socio($socio->sid);
	$datos[] = array ( 'sid' => $datos_socio->id,
                   'apynom' => $datos_socio->nombre.' '.$datos_socio->apellido,
                   'estado_asoc' => $datos_socio->suspendido,
                   'dni'=>$datos_socio->dni,
                   'actividad' => 1 );
        }
        return $datos;

    } 

    public function becar($id='',$beca)
    {    
        $this->db->where('id',$id);
	    if ( $beca > 100 ) {
        	$this->db->update('actividades_asociadas',array("descuento"=>$beca, "monto_porcentaje"=>0 ));
	    } else {
        	$this->db->update('actividades_asociadas',array("descuento"=>$beca, "monto_porcentaje"=>1 ));
	    }
    }

    public function tiene_asocrel($id_entidad, $aid)
    {
        $qry = "SELECT COUNT(*) asoc_rel FROM actividades_asociadas WHERE  id_entidad = $id_entidad AND aid = $aid AND estado = 1; ";
        $resultado = $this->db->query($qry)->row();
	if ( $resultado->asoc_rel > 0 ) {
		return true;
	} else {
		return false;
	}
    }

    public function get_act_asoc_all($id_entidad)
    {
        $this->db->select('aa.*, socios.nombre as socio_nombre, socios.apellido as socio_apellido, actividades.nombre as actividad_nombre, actividades.precio as precio');
        $this->db->where('aa.estado', 1);
        $this->db->where('aa.id_entidad', $id_entidad);
        $this->db->join('socios', 'socios.id = aa.sid', 'left');
        $this->db->join('actividades', 'actividades.id = aa.aid', 'left');
        $query = $this->db->get('actividades_asociadas as aa');
        if( $query->num_rows() == 0 ){ return false; }
        $asoc = $query->result();
        $query->free_result();
        return $asoc;
    }

/* Fin funciones para trabajar con la tabla actividades_asociadas */

/* Funciones para trabajar con la tabla comisiones */
    public function grabar_comision($datos)
    {
        $id_entidad = $datos['id_entidad'];
        $qry = "SELECT MAX(cid) max_id FROM comisiones WHERE id_entidad = $id_entidad; ";
        $resultado = $this->db->query($qry)->row();
        if ( $resultado->max_id ) {
                $datos['cid'] = $resultado->max_id+1;
        } else {
                $datos['cid'] = 1;
        }

        $this->db->insert('comisiones', $datos);
        return $this->db->insert_id();   
    }

    public function borrar_comision($id){
        $this->db->where('id', $id); 
        $this->db->update('comisiones',array("estado"=>'0'));
    }

    public function actualizar_comision($datos, $id){
        $this->db->where('id', $id);
        $this->db->update('comisiones', $datos); 
    }

    public function get_comision($id)
    {
        if (!$id || $id == '0'){
            $comision = new stdClass();
            $comision->id=0;
            $comision->descripcion='';
            return $comision;
        } else {
            $query = $this->db->get_where('comisiones',array('id' => $id),1);
            if($query->num_rows() == 0) {return false;}
            return $query->row();
        }
    }

    public function get_comisiones($id_entidad)
    {
        if ( $id_entidad > 0 ) {
    		$this->db->where("id_entidad", $id_entidad); 
        }
        $this->db->where('estado','1');
        $query = $this->db->get('comisiones');
        if ( $query->num_rows() == 0) { return false; }
        return $query->result();
    }
/* Fin funciones para trabajar con la tabla comisiones */

    /* Funciones de la tabla tmp_asoc_activ */
    public function trunc_asoc_act($id_entidad){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->delete('tmp_asoc_activ');
    }

    public function insert_asoc_act($datos){
        $this->db->insert('tmp_asoc_activ', $datos);
    }

    public function get_asocact_exist($id_entidad, $existe){
        $this->db->where("id_entidad =",$id_entidad);
        $this->db->where("existe_relacion =",$existe);
        $query = $this->db->get("tmp_asoc_activ");
        return $query->result();
    }



}
?>
