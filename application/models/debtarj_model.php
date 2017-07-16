<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Debtarj_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }

/* Funciones de la tabla socios_debito_tarj */
    
    public function grabar($datos)
    {
        $this->db->insert('socios_debito_tarj', $datos);
        return $this->db->insert_id();   
    }

    public function stopdebit($id){
        $this->db->where('Id', $id); 
	$debtarj=$this->db->get('socios_debito_tarj');
	if ( $debtarj->estado == 1 ) {
        	$this->db->update('socios_debito_tarj',array("estado"=>'2'));
	} else {
        	$this->db->update('socios_debito_tarj',array("estado"=>'1'));
	}
    }

    public function borrar($id){
        $this->db->where('Id', $id); 
        $this->db->update('socios_debito_tarj',array("estado"=>'0'));
    }

    public function actualizar($id, $datos){
        $this->db->where('Id', $id);
        $this->db->update('socios_debito_tarj', $datos); 
    }

    public function get($id)
    {
        if (!$id || $id == '0'){
            $debtarj = new stdClass();
            $debtarj->sid=0;
            $debtarj->id_marca=0;
            $debtarj->nro_tarjeta=0;
            $debtarj->fecha_adhesion=0;
            $debtarj->ult_periodo_generado=0;
            $debtarj->ult_fecha_generacion=0;
            $debtarj->estado=0;
            $debtarj->id=0;
            return $debtarj;
        } else {
            $query = $this->db->get_where('socios_debito_tarj',array('Id' => $id),1);
            if($query->num_rows() == 0) {return false;}
            return $query->row();
        }
    }

    public function get_debtarjs()
    {
        $this->db->where('estado >','0');
        $query = $this->db->get('socios_debito_tarj');
        return $query->result();
    }

    public function get_debitos_gen()
    {
        $this->db->where('estado >','0');
        $this->db->limit(6);
        $this->db->order_by('periodo','desc');
        $query = $this->db->get('socios_debitos_gen');
        return $query->result();
    }

    public function get_periodo_marca($periodo, $id_marca)
    {
        $query = $this->db->get_where('socios_debitos_gen',array('periodo'=>$periodo, 'id_marca'=>$id_marca, 'estado'=>1));
   	if ($query->num_rows() > 0) {
        	return $query->row();
    	} else {
    		return FALSE;
	}
    }

    public function exist_periodo_marca($periodo, $id_marca)
    {
        $query = $this->db->get_where('socios_debitos_gen',array('periodo'=>$periodo, 'id_marca'=>$id_marca));
   	if ($query->num_rows() > 0) {
        	return TRUE;
    	} else {
    		return FALSE;
	}
    }

    public function upd_gen($periodo, $id_marca, $cantidad, $total)
    {
        $this->db->where('periodo', $periodo);
        $this->db->where('id_marca', $id_marca);
        $this->db->update('socios_debitos_gen', array("cant_acreditada"=>$cantidad, "total_acreditado"=>$total)); 
    }

    public function insert_periodo_marca($datos)
    {
        $this->db->insert('socios_debitos_gen', $datos);
        return $this->db->insert_id();
    }

    public function anula_periodo_marca($periodo, $id_marca)
    {
        $this->db->where('periodo', $periodo);
        $this->db->where('id_marca', $id_marca);
        $this->db->update('socios_debitos_gen', array("estado"=>0)); 

	$datos['id_marca']=$id_marca;
	$datos['estado']=1;
	$debtarjs = $this->get_debtarj_by($datos);
	foreach ( $debtarjs as $debtarj ) {
		if ( $debtarj->ult_periodo_generado == $periodo ) {
        		$this->db->where('id_debito', $debtarj->id);
        		$this->db->where('fecha_debito', $debtarj->ult_fecha_generacion);
        		$this->db->update('socios_debitos',array("fecha_debito"=>null,"estado"=>0));
		}

	}
    }

    public function get_debtarjs_anul($sid)
    {
        $query = $this->db->get_where('socios_debito_tarj',array('estado'=>'0', 'sid'=>$sid));
        return $query->result();
    }

    public function get_debtarj_by($by)
    {
        $by['estado'] = 1;
        $query = $this->db->get_where('socios_debito_tarj',$by);
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    public function get_debtarj($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('socios_debito_tarj');
        if( $query->num_rows() == 0 ){ return false; }
        $debtarj = $query->row();
        $query->free_result();
        return $debtarj;
    }

    public function get_debtarj_by_sid($sid)
    {
        $this->db->where('sid', $sid);
        $this->db->where('estado', 1);
        $query = $this->db->get('socios_debito_tarj');
        if( $query->num_rows() == 0 ){ return false; }
        $debtarj = $query->row();
        $query->free_result();
        return $debtarj;
    }

/* Funciones de la tabla socios_debitos */

    public function insert_debito($id,$fecha,$importe){
        $insert = array(
            'id_debito'=> $id,
            'fecha_debito'=>$fecha,
            'fecha_acreditacion'=> null,
            'importe' => $importe,
            'estado' => 1,
	    'nro_renglon' => 0,
	    'id' => 0 
            );
        $this->db->insert('socios_debitos',$insert);
    }

    public function upd_noacred($id_marca, $periodo) {
        $this->db->where('id_marca', $id_marca);
        $this->db->where('periodo', $periodo);
        $this->db->where('estado','1');
        $query = $this->db->get('socios_debitos_gen',1);
	$row=$query->row();
	if ( $row ) {
		$fecha_deb=$row->fecha_debito;

        	$this->db->where('fecha_debito', $fecha_deb);
        	$this->db->where('fecha_acreditacion',null);
        	$query = $this->db->get('socios_debitos');
		$debitos=$query->result();

		if ( $debitos ) {
			foreach ( $debitos as $debito ) {
        			$this->db->where('id', $debito->id);
				$this->db->update('socios_debitos',array("estado"=>0));
			}
		}
	}
    }

    public function upd_acred($id_debito, $fecha_acred){
        $this->db->where('Id', $id_debito);
        $this->db->update('socios_debitos',array("fecha_acreditacion"=>$fecha_acred));
    }

    public function upd_debito_rng($id_debito,$nro_renglon){
        $this->db->where('Id', $id_debito);
        $this->db->update('socios_debitos',array("nro_renglon"=>$nro_renglon));
    }

    public function get_debito_rng($id_marca, $fecha_debito, $nro_renglon){

        $this->db->select('sd.*, socios_debito_tarj.id_marca as id_marca, socios_debito_tarj.nro_tarjeta as nro_tarjeta');
        $this->db->where('fecha_debito', $fecha_debito);
        $this->db->where('nro_renglon', $nro_renglon);
        $this->db->join('socios_debito_tarj', 'socios_debito_tarj.id = sd.id_debito AND socios_debito_tarj.id_marca = '.$id_marca, 'left');
        $query=$this->db->get('socios_debitos as sd');
        if( $query->num_rows() == 0 ){ return false; }
        $debito = $query->row();
        return $debito;
    }


    public function get_fchult_debito()
    {
        $this->db->where('estado', 1);
        $this->db->order_by('fecha_debito','desc');
        $query = $this->db->get('socios_debitos',1);
        if( $query->num_rows() == 0 ){ return false; }
        $debitos = $query->row();
        $query->free_result();
        return $debitos->fecha_debito;
    }

    public function get_debito_by_marca_nrotarj($id_marca, $fecha, $nro_tarjeta) {
        $this->db->where('id_marca', $id_marca);
        //$this->db->where('ult_fecha_generacion', $fecha);
        $this->db->where('nro_tarjeta-TRUNCATE(nro_tarjeta,-4)', $nro_tarjeta);
        //$this->db->where('estado', 1);
        $query = $this->db->get('socios_debito_tarj');
        if( $query->num_rows() == 0 ){ return false; }
	    $debtarjs=$query->row();
        $id_debito=$debtarjs->id;
        $ult_fecha=$debtarjs->ult_fecha_generacion;
        $debito=$this->get_debito_by_id($id_debito, $ult_fecha);
        return $debito;
    }

    public function get_debitos_by_marca_periodo($id_marca, $periodo) {
        $this->db->where('id_marca', $id_marca);
        $this->db->where('ult_periodo_generado', $periodo);
        $this->db->where('estado', 1);
        $query = $this->db->get('socios_debito_tarj');
        if( $query->num_rows() == 0 ){ return false; }
	$debtarjs=$query->result();
	$debitos=array();
    	$fila=1;
	foreach ($debtarjs as $debtarj) {
            $id_debito=$debtarj->id;
            $ult_fecha=$debtarj->ult_fecha_generacion;

            $this->db->where('id_debito', $id_debito);
            $this->db->where('fecha_debito', $ult_fecha);
            $this->db->where('estado', 1);
            $query1 = $this->db->get('socios_debitos',1);
	        if ( $query1->num_rows() > 0 ) {
                	$debito=$query1->row();
            		$debitos[$fila]['nro_tarjeta']=$debtarj->nro_tarjeta;
            		$debitos[$fila]['importe']=$debito->importe;
            		$debitos[$fila]['sid']=$debtarj->sid;
            		$debitos[$fila]['id_debito']=$debito->id;
	        }
	        $query1->free_result();
            $fila++;
            
        }
        return $debitos;
    }

    public function get_deberr_by_marca_periodo($id_marca, $periodo) {
        $this->db->where('id_marca', $id_marca);
        $this->db->where('periodo', $periodo);
        $this->db->where('estado','1');
        $query = $this->db->get('socios_debitos_gen',1);
	$row=$query->row();
	$fecha_deb=$row->fecha_debito;

        $this->db->where('sd.fecha_debito', $fecha_deb);
        $this->db->where('sd.fecha_acreditacion',null);
        $this->db->where('sd.estado',0);
        $this->db->select('s.id sid, TRIM(s.apellido) apellido, TRIM(s.nombre) nombre, t.descripcion marca, sd.nro_renglon, sdt.nro_tarjeta, sd.importe');
        $this->db->join('socios_debito_tarj sdt','sdt.id = sd.id_debito AND sdt.id_marca = '.$id_marca);
        $this->db->join('socios s','sdt.sid = s.id');
        $this->db->join('tarj_marca t','sdt.id_marca = t.id');
        $query = $this->db->get('socios_debitos sd');
	$debitos=$query->result();

        return $debitos;

    }

    public function get_debito_by_id($id_debito, $fecha, $estado='')
    {
        $this->db->where('id_debito', $id_debito);
        $this->db->where('fecha_debito', $fecha);
	if ( $estado ) {
        	$this->db->where('estado', $estado);
	} else {
        	$this->db->where('estado', 1);
	}
        $query = $this->db->get('socios_debitos',1);
        if( $query->num_rows() == 0 ){ return false; }
        $debitos = $query->row();
        $query->free_result();
        return $debitos;
    }

    public function get_debitos_by_socio($id_debito)
    {
        $this->db->where('id_debito', $id_debito);
        $this->db->where('estado >', 0);
        $this->db->order_by('fecha_debito','desc');
        $query = $this->db->get('socios_debitos',1);
        if( $query->num_rows() == 0 ){ return false; }
        $debitos = $query->row();
        $query->free_result();
        return $debitos;
    }


}  
?>
