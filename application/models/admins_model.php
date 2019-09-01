<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Admins_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
	public function insert_admin($admin='')
	{
		$this->db->insert('admin', $admin);
		return $this->db->insert_id();
	}

	public function get_admins($id_entidad)
	{
		$this->db->where('id_entidad', $id_entidad);
		$this->db->where('estado', 1);
		$query = $this->db->get('admin');
		if( $query->num_rows() == 0 ){ return false; }
		$admins = $query->result();
		$query->free_result();
		return $admins;
	}

	public function get_admin($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('admin');
		if( $query->num_rows() == 0 ){ return false; }
		$admin = $query->row();
		$query->free_result();
		return $admin;
	}

	public function chk_pwd($id,$old_pwd)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('admin');
		if( $query->num_rows() == 0 ){ return false; }
		$user = $query->row();
		var_dump($user);
		if ( $user ) {
			var_dump($user->pass);
			var_dump($old_pwd);
			var_dump("hola");

			if ( $user->pass == $old_pwd ) {
				var_dump("true");
				return true;
			} else {
				var_dump("false");
				return false;
			}
		} else {
			var_dump("false");
			return false;
		}
	}

	public function update_pwd($id,$new_pwd)
	{
		$now=date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		$this->db->update('admin', array("pass"=>$new_pwd, "last_chgpwd"=>$now));
	}

	public function update_admin($id='',$admin)
	{
		$this->db->where('id', $id);
		$this->db->update('admin', $admin);
	}
}
?>
