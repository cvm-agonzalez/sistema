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
		$admins = $query->result();
		$query->free_result();

		$this->db->where('id_entidad', 0);
		$this->db->where('estado', 1);
		$query = $this->db->get('admin');
		$admins0 = $query->result();
		$query->free_result();

		$ret_admins = array_merge($admins,$admins0);
		return $ret_admins;

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
		if ( $user ) {

			if ( $user->pass == $old_pwd ) {
				return true;
			} else {
				return false;
			}
		} else {
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
