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
    
    public function reg_profesor($datos){
        $this->db->insert('profesores', $datos);
        return $this->db->insert_id();   
    }
    public function update_profesor($datos,$id){
        $this->db->where("Id",$id);
        $this->db->update('profesores', $datos);
        return true;   
    }    
    public function get_profesores(){
    	$this->db->order_by("apellido", "asc"); 
        $query = $this->db->get_where("profesores",array('estado' => '1'));
        return $query->result();
    }    
    public function get_profesor($id){
        $this->db->where("Id", $id); 
        $query = $this->db->get("profesores");
        if($query->num_rows() == '0'){
            return false;
        }else{        
            return $query->row();
        }
    }
    public function del_profesor($id){
        $this->db->where("Id", $id); 
        $query = $this->db->update("profesores",array("estado"=>'0'));
        return true;
    } 

    public function reg_lugar($datos){
        $this->db->insert('lugares', $datos);
        return $this->db->insert_id();   
    }
    public function update_lugar($datos,$id){
        $this->db->where("Id",$id);
        $this->db->update('lugares', $datos);
        return true;   
    }    
    public function get_lugares(){
        $this->db->order_by("nombre", "asc"); 
        $query = $this->db->get_where("lugares",array("estado"=>'1'));
        return $query->result();
    }    
    public function get_lugar($id){
        $this->db->where("Id", $id); 
        $query = $this->db->get("lugares");
        if($query->num_rows() == '0'){
            return false;
        }else{        
            return $query->row();
        }
    }
    public function del_lugar($id){
        $this->db->where("Id", $id); 
        $query = $this->db->update("lugares",array("estado"=>'0'));
        return true;
    } 

    public function reg_actividad($datos){
        $this->db->insert('actividades', $datos);
        return $this->db->insert_id();   
    }
    public function update_actividad($datos,$id){
        $this->db->where("Id",$id);
        $this->db->update('actividades', $datos);
        return true;   
    }    
    public function get_actividades_list(){
        $this->db->order_by("nombre", "asc"); 
        $query = $this->db->get("actividades");
        return $query->result();
    }    
    public function get_actividades(){
        $this->db->order_by("nombre", "asc"); 
        $this->db->where("estado >",'0');
        $query = $this->db->get("actividades");
        return $query->result();
    }    
    public function get_actividad($id){
        $this->db->where("Id", $id); 
        $query = $this->db->get("actividades");
        return $query->row();
    }
    public function del_actividad($id){
        $this->db->where("Id", $id); 
        $query = $this->db->update("actividades", array("estado"=>'0'));
        return true;
    }
    public function get_act_asoc_puntual($sid, $aid){
        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $query = $this->db->get("actividades_asociadas");
        if ($query->num_rows() == 0){
		return false;
	} else {
		$asoc = $query->row();
                $actividad = $this->get_actividad($asoc->aid);
                $actividad->estado = $asoc->estado;
                $actividad->asoc_id = $asoc->Id;
                if($actividad->estado == '1'){
                    $actividad->alta = $this->show_date($asoc->date);
                }else{
                    $actividad->alta = $this->show_date($asoc->date_alta);
                    $actividad->baja = $this->show_date($asoc->date);
                }
                $actividad->federado = $asoc->federado;
                $actividad->descuento = $asoc->descuento;
                $actividad->monto_porcentaje = $asoc->monto_porcentaje;
                return $actividad;
	}
    }

    public function get_act_asoc($sid){
        $this->db->order_by("estado", "desc");
        $this->db->where("sid", $sid);
        $query = $this->db->get("actividades_asociadas");
        if ($query->num_rows() == 0){
            $act_asoc = new stdClass();
            return $act_asoc;
        }else{
            foreach ($query->result() as $asoc) {
                $actividad = $this->get_actividad($asoc->aid);
                $actividad->estado = $asoc->estado;
                $actividad->asoc_id = $asoc->Id;
                if($actividad->estado == '1'){
                    $actividad->alta = $this->show_date($asoc->date);
                }else{
                    $actividad->alta = $this->show_date($asoc->date_alta);
                    $actividad->baja = $this->show_date($asoc->date);
                }
                $actividad->federado = $asoc->federado;
                $actividad->descuento = $asoc->descuento;
                $actividad->monto_porcentaje = $asoc->monto_porcentaje;
                $act_asoc[] = $actividad;
            }            
            return $act_asoc;
        } 
    }

    public function get_act_asoc_tutor($tutor_id){
        $this->db->where("tutor", $tutor_id);
        $query = $this->db->get("socios");
        if ($query->num_rows() == 0){
            $act_asoc = new stdClass();
            return $act_asoc;
	} else {
            $act_asoc = array();
            foreach ($query->result() as $asoc) {
		$sid=$asoc->Id;
        	$this->db->order_by("estado", "desc");
        	$this->db->where("sid", $sid);
        	$query = $this->db->get("actividades_asociadas");
        	if ($query->num_rows() > 0){
            		foreach ($query->result() as $asoc) {
                		$actividad = $this->get_actividad($asoc->aid);
                		$actividad->estado = $asoc->estado;
                		$actividad->asoc_id = $asoc->Id;
                		if($actividad->estado == '1') {
                    			$actividad->alta = $this->show_date($asoc->date);
                		} else {
                    			$actividad->alta = $this->show_date($asoc->date_alta);
                    			$actividad->baja = $this->show_date($asoc->date);
                		}
                		$actividad->federado = $asoc->federado;
                		$actividad->descuento = $asoc->descuento;
                		$actividad->monto_porcentaje = $asoc->monto_porcentaje;
                		$act_asoc[] = $actividad;
            		}
		}
	    }
            return $act_asoc;
        }
    }

    public function act_baja_asoc($sid,$aid){

        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $this->db->where("estado", '1');
        $query = $this->db->get('actividades_asociadas',1);
        $fecha = $query->row();
        $fecha = $fecha->date;

        $this->db->where("sid", $sid);
        $this->db->where("aid", $aid);
        $this->db->where("estado", '1');
        $query = $this->db->update("actividades_asociadas",array('estado'=>'0','date_alta'=>$fecha));
    }
    public function act_baja($sid,$aid){
        
        $this->db->where("Id", $aid);
        $query = $this->db->get('actividades_asociadas');
        $fecha = $query->row();
        $fecha = $fecha->date;

        $this->db->where("sid", $sid);
        $this->db->where("Id", $aid);
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

    public function act_peso($aid){        
        $this->db->where('Id',$aid);
        $query = $this->db->update("actividades_asociadas",array('monto_porcentaje'=>'0'));
        return true;
    } 
    public function act_porc($aid){        
        $this->db->where('Id',$aid);
        $query = $this->db->update("actividades_asociadas",array('monto_porcentaje'=>'1'));
        return true;
    } 

    public function get_socactiv($id_actividad=-1,$id_comision=0,$mora=0){
        $qry = "DROP TEMPORARY TABLE IF EXISTS tmp_socios_activos;";
        $this->db->query($qry);

        if ( $id_comision > 0 ) {
                $qry1 ="DROP TEMPORARY TABLE IF EXISTS tmp_actividades; ";
                $this->db->query($qry1);
                $qry1 ="CREATE TEMPORARY TABLE tmp_actividades ( INDEX ( aid ) )
                        SELECT a.Id as aid FROM actividades a WHERE a.comision = $id_comision; ";
                $this->db->query($qry1);
        }

        $qry = "CREATE TEMPORARY TABLE tmp_socios_activos
		SELECT aa.aid, a.nombre descr_act, s.*
                FROM actividades_asociadas aa 
			JOIN socios s ON aa.sid = s.Id 
			JOIN actividades a ON aa.aid = a.Id ";
        if ( $id_comision > 0 ) {
                $qry .= "       JOIN tmp_actividades USING ( aid ) ";
        }
        $qry .= "WHERE aa.estado = 1 ";
        if ( $id_actividad >= 0 && $id_comision == 0 ) {
                $qry .= "AND aa.aid = $id_actividad ";
        }
	$qry .= "ORDER BY aa.aid, s.Id; ";
        $this->db->query($qry);

        $qry = "DROP TEMPORARY TABLE IF EXISTS tmp_pagos;";
        $this->db->query($qry);

        $qry = "CREATE TEMPORARY TABLE tmp_pagos
		SELECT ta.Id sid, SUM(monto-pagado) saldo, MAX(pagadoel) ult_pago
                FROM tmp_socios_activos ta
			JOIN pagos p ON ( ta.Id = p.tutor_id )
		GROUP BY 1; ";
        $this->db->query($qry);

        $qry = "SELECT ta.*, IFNULL(tp.saldo,0) mora, IFNULL(tp.ult_pago,'') ult_pago
		FROM tmp_socios_activos ta		
			LEFT JOIN tmp_pagos tp ON ta.Id = tp.sid ";
	if ( $mora == 0 ) {
		$qry .= "; ";
	} else {
		$qry .= "WHERE tp.saldo > 0; ";
	}
	
        $socactiv = $this->db->query($qry);
        return $socactiv->result();

    }

    public function get_socios_actividad($id){
        $this->load->model('socios_model');
        $this->db->where('aid',$id);
        $this->db->where('estado',1);
        $query = $this->db->get('actividades_asociadas');
        $socios = $query->result();
        foreach ($socios as $socio) {
            $socio->info = $this->socios_model->get_socio($socio->sid);
        }
        return $socios;
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
	    $datos[] = array ( 'sid' => $datos_socio->Id,
                               'apynom' => $datos_socio->nombre.' '.$datos_socio->apellido,
                               'estado_asoc' => $datos_socio->suspendido,
                               'dni'=>$datos_socio->dni,
                               'actividad' => 1 );
        }
        return $datos;

    } 

    public function becar($id='',$beca)
    {    
        $this->db->where('Id',$id);
	if ( $beca > 100 ) {
        	$this->db->update('actividades_asociadas',array("descuento"=>$beca, "monto_porcentaje"=>0 ));
	} else {
        	$this->db->update('actividades_asociadas',array("descuento"=>$beca, "monto_porcentaje"=>1 ));
	}
    }

    public function get_act_asoc_all()
    {
        $this->db->select('aa.*, socios.nombre as socio_nombre, socios.apellido as socio_apellido, actividades.nombre as actividad_nombre');
        $this->db->where('aa.estado', 1);
        $this->db->join('socios', 'socios.Id = aa.sid', 'left');
        $this->db->join('actividades', 'actividades.Id = aa.aid', 'left');
        $query = $this->db->get('actividades_asociadas as aa');
        if( $query->num_rows() == 0 ){ return false; }
        $asoc = $query->result();
        $query->free_result();
        return $asoc;
    }


    public function grabar_comision($datos)
    {
        $this->db->insert('comisiones', $datos);
        return $this->db->insert_id();   
    }

    public function borrar_comision($id){
        $this->db->where('Id', $id); 
        $this->db->update('comisiones',array("estado"=>'0'));
    }

    public function actualizar_comision($id, $datos){
        $this->db->where('Id', $id);
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
            $query = $this->db->get_where('comisiones',array('Id' => $id),1);
            if($query->num_rows() == 0) {return false;}
            return $query->row();
        }
    }

    public function get_comisiones()
    {
        $this->db->where('estado >','0');
        $query = $this->db->get('comisiones');
        return $query->result();
    }

    /* Funciones de la tabla tmp_asoc_activ */
    public function trunc_asoc_act(){ 
	$this->db->truncate('tmp_asoc_activ');
    }

    public function insert_asoc_act($datos){ 
        $this->db->insert('tmp_asoc_activ', $datos);
    }

    public function get_asocact_exist($existe){ 
        $this->db->where("existe_relacion =",$existe);
        $query = $this->db->get("tmp_asoc_activ");
        return $query->result();
    }

}
?>
