<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class libertad_comision extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

	
        	$this->load->model('login_model');
        	$this->load->library(array('session','form_validation'));
        	$this->load->helper(array('url','form'));
        	$this->load->database('default');        
        	$data['id_entidad'] = 11;
		$this->load->model('general_model');
		$entidad = $this->general_model->get_ent_dir($data['id_entidad']);

		$data['ent_abreviatura'] = $entidad->abreviatura;
		$data['ent_nombre'] = $entidad->descripcion;
		$data['ent_directorio'] = $entidad->dir_name;
var_dump($data);

		$this->session->set_userdata($data);

        	redirect(base_url().'comisiones');


    }
    


}
?>
