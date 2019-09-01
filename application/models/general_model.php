<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class General_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }

    public function write_log($log)
    {
        $this->db->insert('log_cambios',$log);
    }

    public function get_cats($id_entidad){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado > 0');
        $query = $this->db->get("categorias");
        return $query->result();
    }

    public function get_cat($id){
        $this->db->where('id',$id);
        $query = $this->db->get("categorias");
        return $query->row();
    }

    public function update_cat($idcateg,$categ='')
    {
        $this->db->where('id',$idcateg);
        $this->db->update('categorias',$categ);
    }

    public function insert_cat($categ='')
    {
	$id_entidad = $categ['id_entidad'];
	$qry = "SELECT MAX(cid) max_id FROM categorias WHERE id_entidad = $id_entidad; ";
	$resultado = $this->db->query($qry)->row();
	if ( $resultado->max_id ) {
		$categ['cid'] = $resultado->max_id+1;
	} else {
		$categ['cid'] = 1;
	}

        $this->db->insert('categorias',$categ);
        return $this->db->insert_id();
    }

    public function delete_cat($idcateg)
    {
    	$this->db->where('id',$idcateg);
	$this->db->update('categorias',array('estado'=>0));
    }

    public function save_cat_config($precios,$fam){
        for ($i=1; $i < count($precios)+1; $i++) { 
            $this->db->where('id',$i);
            $this->db->update('categorias',array('precio'=>$precios[$i-1]));
	
        }
        $this->db->where('id','4');
        $this->db->update('categorias',array('precio_unit'=>$fam));
    }

/* entidades */

    public function get_ents(){
        $this->db->where('estado > 0');
        $query = $this->db->get("entidades");
        return $query->result();
    }

    public function get_ent($id){
        $this->db->where('id',$id);
        $query = $this->db->get("entidades");
        return $query->row();
    }

    public function update_ent($id,$entidad='')
    {
        $this->db->where('id',$id);
        $this->db->update('entidades',$entidad);
    }

    public function insert_ent($entidad='')
    {
        $this->db->insert('entidades',$entidad);
        return $this->db->insert_id();
    }

    public function get_ent_dir($id)
    {
	$qry = "SELECT *, CONCAT(CONCAT(REPEAT('0',4 - LENGTH(id)),id),'_',TRIM(abreviatura)) dir_name FROM entidades WHERE id = $id;";
	$resultado = $this->db->query($qry)->row();
	return $resultado;
    }

    public function delete_ent($id)
    {
    	$this->db->where('id',$id);
	$this->db->update('entidades',array('estado'=>0));
    }
/**
ENVIOS
**/
    public function insert_envio($envio='')
    {
        $this->db->insert('envios',$envio);
        return $this->db->insert_id();
    }

    public function get_socios_by($id_entidad, $grupo,$data='',$activ)
    {
        if($grupo == 1){
            $this->db->where('id_entidad',$id_entidad);            
            $this->db->where('estado',1);            
	    if ( $activ == 1 ) {
		$this->db->where('suspendido',0);
	    } 
            $query = $this->db->get('socios');
            if($query->num_rows() == 0){return false;}
            $socios = $query->result();
        }else{
            switch ($grupo) {
                case 'categorias':
                    foreach ($data as $id) {
                        $this->db->or_where('categoria',$id);
                    }
                    $this->db->where('id_entidad',$id_entidad);            
                    $this->db->where('estado',1);            
		    if ( $activ == 1 ) {
			$this->db->where('suspendido',0);
	    	    } 
                    $query = $this->db->get('socios');
                    if($query->num_rows() == 0){return false;}
                    $socios = $query->result();
                    break;
                
                case 'actividades':
                    foreach ($data as $id) {        
                        $this->db->or_where('aid',$id);
                    }
                    $this->db->where('id_entidad',$id_entidad);            
                    $this->db->where('estado',1);
                    $query = $this->db->get('actividades_asociadas');
                    if($query->num_rows() == 0){return false;}
                    $actividades = $query->result();
                    $socios = array();
                    $this->load->model('socios_model');
                    foreach ($actividades as $actividad) {
                        $soc = $this->socios_model->get_socio($actividad->sid);
			if ( $activ == 1 ) {
				if ( $soc->suspendido == 0 ) {
                        		$socios[] = $soc;
				}
			} else {
                        		$socios[] = $soc;
			}
                    }
                    break;

                case 'socconactiv':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_conact($id_entidad, $activ);
                    break;

                case 'socsinactiv':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_sinact($id_entidad, $activ);
                    break;

                case 'soccomision':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_comision($$id_entidad, data, $activ);
                    break;

                case 'titcomision':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_titu_comision($id_entidad, $data, $activ);
                    break;

            }
        }
        return $socios;
        
    }

    public function insert_envios_data($envio_data)
    {
        $this->db->insert('envios_data',$envio_data);
    }

    public function update_envio($id='',$envio)
    {
        $this->db->where('id',$id);
        $this->db->update('envios',$envio);
    }

    public function get_envios($id_entidad)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->order_by('id','desc');
        $query = $this->db->get('envios');
        if($query->num_rows() == 0){ return false; }
        $envios = $query->result();
        foreach ($envios as $envio) {
            $this->db->where('eid',$envio->id);
            $query = $this->db->get('envios_data');
            $envio->total = $query->num_rows();

            $this->db->where('eid',$envio->id);
            $this->db->where('estado',1);
            $query = $this->db->get('envios_data');
            $envio->enviados = $query->num_rows();
        }
        $query->free_result();
        return $envios;
    }

    public function get_envio($id='')
    {
        $this->db->where('id',$id);
        $query = $this->db->get('envios');
        if($query->num_rows() == 0){ return false; }
        $envio = $query->row();

        $this->db->where('eid',$envio->id);
        $query = $this->db->get('envios_data');
        $envio->total = $query->num_rows();

        $this->db->where('eid',$envio->id);
        $this->db->where('estado',1);
        $query = $this->db->get('envios_data');
        $envio->enviados = $query->num_rows();
        $query->free_result();

        return $envio;
    }

    public function get_envios_data($id)
    {
        $this->db->where('eid',$id);
        $query = $this->db->get('envios_data');        
        if($query->num_rows() == 0){ return false; }
        $envios_data = $query->result();
        $query->free_result();
        return $envios_data;
    }

    public function clear_envio_data($id)
    {
        $this->db->where('eid',$id);
        $this->db->delete('envios_data');
    }

    public function get_envio_data($id='')
    {
        $this->db->where('eid',$id);
        $this->db->where('estado',0);
        $query = $this->db->get('envios_data',1);
        if($query->num_rows() == 0){ return false; }
        $envio = $query->row();
        $query->free_result();
        return $envio;
    }

    public function enviado($id='')
    {
        $this->db->where('id',$id);
        $this->db->update('envios_data',array('estado'=>1));
    }

    public function get_enviados($id='')
    {
        $this->db->where('eid',$id);
        $this->db->where('estado',1);
        $query = $this->db->get('envios_data');
        $enviados = $query->num_rows();
        $query->free_result();
        return $enviados;
    }
/**
COMISIONES
**/

    public function get_actividades_comision($id_entidad)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('id',$this->session->userdata('id'));
        $query = $this->db->get('profesores');
        if($query->num_rows() == 0){return false;}
        $profesor = $query->row();

        $this->db->where('comision',$profesor->comision);
        $this->db->where('id_entidad',$id_entidad);
        $query = $this->db->get('actividades');
        if($query->num_rows() == 0){return false;}
        $actividades = $query->result();
        $query->free_result();
        return $actividades;

    }

    public function get_reporte($id_entidad, $aid='')
    {
        $this->load->model('actividades_model');
        $this->load->model('socios_model');
        $this->load->model('pagos_model');
        $reporte = new STDClass();
        $reporte->actividad = $this->actividades_model->get_actividad($aid);

        $this->db->select('sid');
        $this->db->distinct();
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->where('aid',$aid);
        $query = $this->db->get('actividades_asociadas');
        if($query->num_rows() == 0){ $socios = false; }
        $socios = $query->result();
        if($socios){
            foreach ($socios as $socio) {
                $socio->info = $this->socios_model->get_socio($socio->sid);
                $socio->deuda = $this->pagos_model->get_deuda_monto($socio->sid);
                
                $this->db->order_by('date','desc');
                $this->db->where('haber >',0);
                $this->db->where('sid',$socio->sid);
                $query = $this->db->get('facturacion',1);
                if($query->num_rows() == 0){
                    $socio->ultimo_pago = 'No se registran pagos.';
                }else{
                    $socio->ultimo_pago = $query->row()->date;
                }
            }
        }
        $reporte->socios = $socios;        
        return $reporte;
    }
}
?>
