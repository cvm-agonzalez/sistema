<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class ws_api extends CI_Controller {

	protected $dni = null;
	protected $login = null;
	protected $nivel = null;
	protected $token = null;
	protected $token_ok = FALSE;
	protected $sistemaCVM = TRUE;
	protected $entidad = null;

	public function __construct()
    	{
		parent::__construct();
        	$this->load->helper(array('url', 'form', 'html', 'string'));

        	//Controlo si el pedido tiene un token y es valido
        	$authorization_str = explode(' ', $this->input->get_request_header('Authorization'));
        	$login = $this->input->get_post('login');
        	$dni = $this->input->get_post('dni');

		//echo "\n\nLogin: ".$login." Authorization token: ".$authorization_str[0]." \n" ;

		//Login del usuario con token
		if (count($authorization_str) > 0 && $authorization_str[0] != null) {
            		$token = $authorization_str[0];

            		if ($this->token_login($login, $token)) {
                		$this->token_ok = TRUE;
                		$this->token = $token;
                		$this->dni = $dni;
            		} else {
                		//No existe la combinacion token - usuario
				echo "No tiene permiso para esta funcion";
                		return $this->jsonResultStr(100, "No tiene permiso para acceder a esta pagina");
            		}
        	}  else {
				echo "No tiene permiso para esta funcion2";
                		return $this->jsonResultStr(100, "No tiene permiso para acceder a esta pagina2");
		}
            
    	}

	function token_login($login, $token)
	{
		if ( $login == "agonzalez.lacoope" && $token == "ewrewry23k5bc1436lnlahbg23218g12g1h3g1vm" ) {
			// Aca va luego el chequeo correcto - hardcodeado o contra la BD
			echo "OK los permisos chequeados";
			$this->nivel = 0;
			$this->entidad = 1;
// Setear en f() BD si es sistema CVM
			$this->login = $login;
			return true;
		} else {
			return false;
		}
	}

	function valid_user() { // esta funcion Valida el usuario y devuelve nivel del mismo
		if ( $this->token_ok ) {
			$arr_ret = array ( 'nivel' => $this->nivel, 'login' => $this->login, 'entidad' => $this->entidad );
			echo "Usuario Validado : " . $this->login . " - Token: " . $this->token . " - Nivel : " . $this->nivel ."\n";
			return json_encode($arr_ret);
		} else {
			echo "No tiene permiso para esta funcion2";
                	return $this->jsonResultStr(100, "No tiene permiso para acceder a esta pagina2");
		}
	}

	function get_padron() { // esta funcion devuelve el padron para chequeo OFF en base a la entidad correspondiente al usuario logueado
		$padron=array();
		if ( $this->sistemaCVM ) { 
			// Conecto con el sitio de CVM y traigo padron desde ahi
			$padron = $this->url_CVM('get_padron');
		} else {
			$this->load->model('socios_model');
			$padron = $this->socios_model->get_padron_app($this->entidad);
		}
		return json_encode($padron);
	}

	function get_socio() { // esta funcion devuelve los datos de un socio puntual a partir de su DNI y en base a la entidad correspondiente al usuario logueado
		if ( $this->sistemaCVM ) {
			// Conecto con el sitio de CVM y traigo datos del socio desde ahi
			$socio = $this->url_CVM('get_socio', $this->dni);
		} else {
			$this->load->model('socios_model');
			$socio = $this->socios_model->get_socio_by_dni($this->entidad, $dni);
		}
		return json_encode($socio);
	}

	function url_CVM($funcion, $dni='') {
    		$url = "localhost/CVM_online/ws_api/".$funcion;
    
    		//Prueba villa mitre
    		$login = $this->login;
    		$token = $this->token;

    		$headers= array("Content-Type: multipart/form-data","Authorization: $token");
    		$post = array('login' => $login, 'dni' => $dni);


    		$ch = curl_init($url);
    		curl_setopt($ch, CURLOPT_POST, true);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    		//-------------------------------------
    		//solo para testear:
    		//curl_setopt($ch, CURLOPT_HEADER, true);
    		//curl_setopt($ch, CURLOPT_VERBOSE, true);
    		//-------------------------------------


    		$resultado = curl_exec($ch);
    		$errno  = curl_errno($ch);
    		$error  = curl_error($ch);

    		curl_close($ch);

		if($errno !== 0) {
        		throw new Exception($error, $errno);
    		}

    		return json_decode($resultado);
	}

}
