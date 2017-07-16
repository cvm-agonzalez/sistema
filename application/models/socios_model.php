<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Socios_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function register($datos)
    {
        $this->db->insert('socios', $datos);
        return $this->db->insert_id();   
    }

    public function checkDNI($dni,$id=null)
    {
        if($id){
            $query = $this->db->get_where('socios', array('dni' => $dni, 'Id !=' =>$id, 'estado' =>'1'),1);
        }else{
            $query = $this->db->get_where('socios', array('dni' => $dni, 'estado' =>'1'),1);
        }
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }
    public function listar(){
        $query = $this->db->get_where('socios', array('estado' =>'1'));
        $socios = array();
        $this->load->model("actividades_model");        
        $cont = 0;
        foreach ($query->result() as $socio){
            $socios[$socio->Id]['datos'] = $socio;
            $socios[$socio->Id]['actividades'] = $this->actividades_model->get_act_asoc($socio->Id);        
        }        
        return $socios;
    }
    public function get_socio($id)
    {
        if(!$id || $id == '0'){
            
            $socio = new stdClass();
            $socio->Id=0;
            $socio->nombre=0;
            $socio->apellido=0;
            $socio->dni=0;
            return $socio;
        }else{
            $query = $this->db->get_where('socios',array('Id' => $id,'estado'=> '1'),1);
            if($query->num_rows() == 0){return false;}
            return $query->row();
        }
    }
    public function get_socios()
    {
        $query = $this->db->get_where('socios',array('estado'=>1));
        return $query->result();
    }
    public function get_socio_by($by)
    {
        $by['estado'] = 1;
        $query = $this->db->get_where('socios',$by,1);
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    public function borrar_socio($id){
        $this->db->where('Id', $id); 
        $this->db->update('socios',array("estado"=>'0'));
    }

    public function update_socio($id,$data){
        $this->db->where('Id', $id);
        $this->db->update('socios', $data); 
    }

    public function update_lCon()
    {   
        $uid = $this->session->userdata('id_usuario');
        $fecha = date("Y-m-d G:i:s");
        $data = array('lCon'=>$fecha);
        $this->db->where('Id',$uid);
        $this->db->update('admin',$data);
    }
    public function es_tutor($sid){    
        $query = $this->db->get_where('socios', array('estado' =>'1','tutor =' => $sid));
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }
    public function listar_tutores(){    
        $this->db->select('tutor');
        $this->db->distinct('tutor');
        $query = $this->db->get_where('socios', array('estado' =>'1','tutor !=' => '0'));
        $tutores = $query->result();
        return $tutores;
    }
    public function get_cumpleanios(){ // esta funcion devuelve los socios que cumplen 18 años entre el 21 del mes pasado y el 20 de este mes
        
/*
        $mes = date('m'); //mes actual
        $mes_ant = date('m')-1; //mes anterior
*/
	$mes = 11;
	$mes_ant = 10;
        if(count($mes_ant) == 1){ 
            $mes_ant = '0'.$mes_ant;
        }
        $anio = date('Y')-18; //18 años antes

        $this->db->where($anio, 'YEAR(nacimiento)' , FALSE);
        $this->db->where($mes_ant, 'MONTH(nacimiento)' , FALSE);
        $this->db->where('21 <=', 'DAY(nacimiento)' , FALSE);
        $this->db->get('socios');
        $query1 = $this->db->last_query(); //buscamos los que nacieron el mes anterior despues del 21 inclusive

        $this->db->where($anio, 'YEAR(nacimiento)' , FALSE);
        $this->db->where($mes, 'MONTH(nacimiento)' , FALSE);
        $this->db->where('20 >=', 'DAY(nacimiento)' , FALSE);
        $this->db->get('socios');
        $query2 = $this->db->last_query(); // buscamos los que nacieron este mes antes del 20 inclusive

        $query = $this->db->query($query1." UNION ".$query2);
        return $query->result();
    }
    public function actualizar_menor($id_menor){
        $this->db->where('Id',$id_menor);
        $this->db->update('socios',array('tutor'=>'0','categoria'=>'2'));
    }
    public function get_socios_pagan($facturado=false){
        if($facturado){
            $this->db->where('facturado', 0);
        }
        $query = $this->db->get_where('socios',array('tutor'=>'0','estado'=>'1','suspendido'=>0));
        return $query->result();
    }
    public function check_u_n($sn){
        $query = $this->db->get_where('socios',array('socio_n'=>$sn,'estado'=>'1'));
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }

    public function get_resumen_mail($sid){
        $this->load->model('pagos_model');
        $resumen = $this->pagos_model->get_monto_socio($sid);
        $res['resumen'] = $resumen;
        $socio = $this->get_socio($sid);
        $res['mail'] = $socio->mail;
        $res['deuda'] = $this->pagos_model->get_deuda($sid);
        $res['sid'] = $sid;
        return $res;
    }

    public function insert_deuda($sid,$deuda){
        $this->load->model('pagos_model');
        $total = $this->pagos_model->get_socio_total($sid);
        $total = $total - $deuda;
        $insert = array(
            'sid'=> $sid,
            'descripcion'=>'Ingreso Manual',
            'debe'=>$deuda,
            'haber' => 0,
            'total' => $total
            );
        $this->db->insert('facturacion',$insert);
    }

    public function suspender($id,$si='si'){
        if($si == 'si'){
            $this->db->where('Id',$id);
            $this->db->update('socios',array('suspendido'=>1));
        }else{
            $this->db->where('Id',$id);
            $this->db->update('socios',array('suspendido'=>0));
        }
    }

    public function get_socio_by_dni($dni)
    {
        $this->db->where('dni', $dni);
        $query = $this->db->get('socios');
        if( $query->num_rows() == 0 ){ return false; }
        $socio = $query->row();
        $query->free_result();
        return $socio;
    }

    public function get_socio_by_barcode($barcode)
    {
        $this->db->where('barcode', $barcode);
        $query = $this->db->get('cupones');
        if( $query->num_rows() == 0 ){ return false; }
        $cupon = $query->row();    
        $socio = $this->get_socio($cupon->sid);
        if(!$socio){ return false; }
        return $socio;
    }
}  
?>
