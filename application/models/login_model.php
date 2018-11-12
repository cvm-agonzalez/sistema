<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Por convencion asignamos los siguientes niveles de acceso con el rango
 * 0 = root
 * 1 = admin
 * 2 = usuario de lectura 
 * 3 = acceso publico de comisiones
 */
class Login_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function login_user($username,$password)
    {
        
        $this->db->where('user',$username);
        $this->db->where('pass',$password);

        $query = $this->db->get('admin');    
        if($query->num_rows() == 1)
        {
            return $query->row();
        }else{
            $this->session->set_flashdata('usuario_incorrecto','Los datos introducidos son incorrectos');
            redirect(base_url().'admin#/pages/signin','refresh');
        }
    }
    public function update_lCon()
    {   
        $uid = $this->session->userdata('id_usuario');
        $fecha = date("Y-m-d G:i:s");
        $data = array('lCon'=>$fecha);
        $this->db->where('Id',$uid);
        $this->db->update('admin',$data);
    }

    public function log_comision($email,$pass)
    {
        $this->db->where('mail',$email);
        $this->db->where('pass',$pass);
        $this->db->where('estado',1);
        $query = $this->db->get('profesores');
        if($query->num_rows() == 0){return false;}
        $comision = $query->row();
        $query->free_result();
        return $comision;
    }    
    public function upd_pwd_comision($email,$old_pass,$new_pass)
    {
        $this->db->where('mail',$email);
        $this->db->where('pass',$old_pass);
        $this->db->where('estado',1);
        $query = $this->db->get('profesores');
        if($query->num_rows() == 0){return false;}

        $qry="UPDATE profesores SET pass='$new_pass' WHERE mail='$email';";
        $this->db->query($qry);
        return true;
    }

}
?>
