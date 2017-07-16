
<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Autocomplete_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function get($table,$field,$query){ 
        $this->db->like($field, $query, 'after');
    	$this->db->distinct();
        if($table == 'socios'){
            $query = $this->db->where("estado",'1');
        }
    	$query = $this->db->get($table); 
           	
    	if ($query->num_rows() > 0) {
    		$data = array();
    		$suggestions = array();
			foreach($query->result() as $row) {
                $ret[] = array("value" =>$row->nombre.' '.$row->apellido.' ('.$row->dni.')', "data" =>$row->dni);
		    }		
            
    		return $ret;
		}else{
			return false;
		}
		
    }
    public function search($table,$field,$query){ 
        $this->db->like($field, $query);
        $this->db->distinct();
        $query = $this->db->get($table);        
        if ($query->num_rows() > 0) {
            $data = array();
            $suggestions = array();
            foreach($query->result() as $row) {
                $ret[] = array("value" =>$row->$field);
            }       
            
            return $ret;
        }else{
            return false;
        }
        
    }    

}

