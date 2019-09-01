<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Tarjeta_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }

/* Funciones de la tabla tarj_marca */
    
    public function grabar($datos)
    {
        $this->db->insert('tarj_marca', $datos);
        return $this->db->insert_id();   
    }

    public function borrar($id){
        $this->db->where('id', $id); 
        $this->db->update('tarj_marca',array("estado"=>'0'));
    }

    public function actualizar($id, $datos){
        $this->db->where('id', $id);
        $this->db->update('tarj_marca', $datos); 
    }

    public function get($id)
    {
        if (!$id || $id == '0'){
            $tarjeta = new stdClass();
            $tarjeta->id=0;
            $tarjeta->id_entidad=0;
            $tarjeta->descripcion=0;
            $tarjeta->id_cuenta_banco=0;
            $tarjeta->fecha_firma_convenio=0;
            $tarjeta->estado=0;
            return $tarjeta;
        } else {
            $query = $this->db->get_where('tarj_marca',array('id' => $id),1);
            if($query->num_rows() == 0) {return false;}
            return $query->row();
        }
    }

    public function get_tarjetas($id_entidad)
    {
        $query = $this->db->get_where('tarj_marca',array('id_entidad'=>$id_entidad,'estado'=>1));
        return $query->result();
    }

    public function get_debtarj_by($id_entidad, $by)
    {
        $by['id_entidad'] = $id_entidad;
        $by['estado'] = 1;
        $query = $this->db->get_where('tarj_marca',$by,1);
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }


}  
?>
