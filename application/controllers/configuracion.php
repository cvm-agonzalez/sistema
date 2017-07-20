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
            	
    }
    

	
}

?>