<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Admins_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    // public function get_admins()
    // {       
    //     $query = $this->db->get('admin');                 
    //     return $query->result();        
    // }

	public function insert_admin($admin='')
	{
		$this->db->insert('admin', $admin);
		return $this->db->insert_id();
	}

	public function get_admins()
	{
		$this->db->where('estado', 1);
		$query = $this->db->get('admin');
		if( $query->num_rows() == 0 ){ return false; }
		$admins = $query->result();
		$query->free_result();
		return $admins;
	}

	public function get_admin($id)
	{
		$this->db->where('Id', $id);
		$query = $this->db->get('admin');
		if( $query->num_rows() == 0 ){ return false; }
		$admin = $query->row();
		$query->free_result();
		return $admin;
	}

	public function update_admin($id='',$admin)
	{
		$this->db->where('Id', $id);
		$this->db->update('admin', $admin);
	}
}
?>