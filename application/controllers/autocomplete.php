<? 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Autocomplete extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));        
        if(!$this->session->userdata('is_logued_in')){          
            //redirect(base_url().'admin');
        }              
    }
	function index()
    {
    	return false;
    }
    function get()
    {       // esta funcion busca todos socios almacenados en la base de datos dependiendo de varios parametros
    	$query = $this->input->get('query'); // para generar sugerencias en el momento de completar un input
    	$gets = explode('-',$this->uri->segment(3)); // <-- parametro a buscar
    	$this->load->model("autocomplete_model");
        $params = explode('%7C',$gets[1]);
        foreach ($params as $p) {
            if($ret=$this->autocomplete_model->get($gets[0],$p,$query)){                
                $suggestions = $ret;
            }else{

            }
        }
        $data = array("suggestions"=> $suggestions);
        $json = json_encode($data);
        echo $json;                		    	
    }
    function search()
    {       // esta funcion busca todos los datos almacenados en la base de datos para un parametro dado
        $query = $this->input->get('query'); // para generar sugerencias en el momento de completar un input
        $gets = explode('-',$this->uri->segment(3)); // <-- parametro a buscar
        $this->load->model("autocomplete_model");
        $params = explode('%7C',$gets[1]);
        foreach ($params as $p) {
            if($ret=$this->autocomplete_model->search($gets[0],$p,$query)){                
                $suggestions = $ret;
            }else{

            }
        }
        $data = array("suggestions"=> $suggestions);
        $json = json_encode($data);
        echo $json;                             
    }


    public function buscar_socio()
    {               // esta funcion busca socios, recibe 2 parametros de la URL
        $id_entidad=$this->session->userdata('id_entidad')){          
        $this->load->model('socios_model'); 
        $param = $this->uri->segment(3);  // 1. el parametro que buscar
        $value = $this->uri->segment(4); // 2. el parametro a buscar
        if($value && $param){
            $by = array("$param" => $value);
            $socio = $this->socios_model->get_socio_by($id_entidad, $by);
		var_dump($id_entidad);
		var_dump($by);
            if($socio){
                $json = json_encode($socio);
                echo $json;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }    
	
}

?>
