<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class ws_api extends CI_Controller {

	protected $dni = null;
	protected $dni_user = null;
	protected $login = null;
	protected $login_user = null;
	protected $nivel = null;
	protected $token = null;
	protected $token_ok = FALSE;
	protected $sistemaCVM = FALSE;
	protected $entidad = null;

	public function __construct()
    	{
		parent::__construct();
        	$this->load->helper(array('url', 'form', 'html', 'string'));

        	//Controlo si el pedido tiene un token y es valido
        	$authorization_str = explode(' ', $this->input->get_request_header('Authorization'));
        	$login = $this->input->get_post('login');
        	$login_user = $this->input->get_post('login_user');
        	$dni = $this->input->get_post('dni');
        	$dni_user = $this->input->get_post('dni_user');


		//echo "\n\nLogin: ".$login." Authorization token: ".$authorization_str[0]." \n" ;


		//Login del usuario con token
		if (count($authorization_str) > 0 && $authorization_str[0] != null) {
            		$token = $authorization_str[0];

            		if ($this->token_login($login, $token)) {
                		$this->token_ok = TRUE;
                		$this->token = $token;
                		$this->dni = $dni;
                		$this->login_user = $login_user;
                		$this->dni_user = $dni_user;
            		} else {
                		//No existe la combinacion token - usuario
				echo json_encode($this->array_to_utf8(array("estado" => "100", "result" => (object) array(), "msg" => "No existe usuario-token")));
            		}
        	}  else {
                		//No tiene permiso para esta funcion
				echo json_encode($this->array_to_utf8(array("estado" => "101", "result" => (object) array(), "msg" => "No vino token de validacion")));
		}
            
    	}

	function token_login($login, $token)
	{
		$this->load->model('admins_model');
		$user_app = $this->admins_model->get_user_app($login, $token);
		if ( $user_app ) {
			$this->nivel = $user_app->nivel;
			$this->entidad = $user_app->id_entidad;
			if ( $this->entidad == -1 ) {	
				$this->sistemaCVM = TRUE;
			} else {
				$this->sistemaCVM = FALSE;
			}
			$this->login = $login;
			return true;
		} else {
			return false;
		}
	}

	function valid_app() { // esta funcion Valida el usuario y devuelve nivel del mismo
		if ( $this->token_ok ) {
			$arr_ret = array ( 'nivel' => $this->nivel, 'login' => $this->login, 'entidad' => $this->entidad );
			$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => (object) $arr_ret, "msg" => "Acceso permitido y validado")));
			echo $result;
		}
	}

	function get_user() { // esta funcion devuelve datos de un usuario una vez validado token de userapp y pasando por POST login y DNI
		if ( $this->token_ok ) {
			$this->load->model('admins_model');
			$usuario = $this->admins_model->get_user_app_dni($this->login_user, $this->dni_user);
			if ( $usuario ) {
				$arr_ret = array ( 'nivel' => $usuario->nivel, 'token' => $usuario->token, 'entidad' => $usuario->id_entidad, 'email' => $usuario->email );
				$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => (object) $arr_ret, "msg" => "Usuario permitido y validado")));
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "107", "result" => (object) null, "msg" => "Usuario inexistente".$this->login_user."--".$this->dni_user)));
				echo $result;
			}
		}
	}

	function get_padron() { // esta funcion devuelve el padron para chequeo OFF en base a la entidad correspondiente al usuario logueado
		// Si no esta validado el usuario-token devuelvo false
		if ( $this->token_ok ) {
			if ( $this->nivel > 0 ) {
				$padron=array();
				$estado=0;
				if ( $this->sistemaCVM ) { 
					// Conecto con el sitio de CVM y traigo padron desde ahi
					$padron = $this->url_CVM('get_padron');
					if ( $padron ) {
						$result = $padron;
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "102", "result" => (object) null, "msg" => "No se pudo procesar padron CVM")));
					}
				} else {
					$this->load->model('socios_model');
					$padron = $this->socios_model->get_padron_app($this->entidad);
					if ( $padron ) {
						$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => (object) $padron, "msg" => "Proceso OK")));
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "103", "result" => null, "msg" => "No se pudo procesar padron entidad")));
					}
				}
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => (object) null, "msg" => "Usuario sin nivel para esta funcion")));
				echo $result;
			}
		}
	}

	function get_socio() { // esta funcion devuelve los datos de un socio puntual a partir de su DNI y en base a la entidad correspondiente al usuario logueado
		// Si no esta validado el usuario-token devuelvo false
		if ( $this->token_ok ) {
			if ( $this->nivel > 0 ) {
				if ( $this->sistemaCVM ) {
					// Conecto con el sitio de CVM y traigo datos del socio desde ahi
					$socio = $this->url_CVM('get_socio', $this->dni);
					if ( $socio ) {
						$result = $socio;
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "104", "result" => null, "msg" => "No se pudo obtener el socio CVM")));
					}
				} else {
					$this->load->model('socios_model');
					$socio = $this->socios_model->get_socio_by_dni($this->entidad, $this->dni);
					if ( $socio ) {
						$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => (object) $socio, "msg" => "Proceso OK")));
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "105", "result" => null, "msg" => "No se pudo obtener el socio entidad")));
					}
				}
		
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => (object) null, "msg" => "Usuario sin nivel para esta funcion")));
				echo $result;
			}
		}
	}

	function url_CVM($funcion, $dni='') {
    		$url = "http://clubvillamitre.com/ws_api/".$funcion;
    
    		//Prueba villa mitre
    		$login = $this->login;
    		$token = $this->token;
    		$ya_validado = 1;

    		$headers= array("Content-Type: multipart/form-data","Authorization: $token");
    		$post = array('login' => $login, 'dni' => $dni, 'ya_validado' => $ya_validado);


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

		$obj_resultado = json_decode($resultado);

    		return $resultado;
	}

	function array_to_utf8($array) {
		$result = array();
		if (is_array($array)) {
			foreach ($array as $nro => $fields) {
				$field_aux = array();
				// Si es un arreglo llamo de forma recursiva
				if (is_array($fields)) {
					$field_aux = $this->array_to_utf8($fields);
				} else {
				// Si no es un arreglo chequeo la codificación
					if (is_string($fields) ) {
					//Si no es utf-8 lo codifico
						$field_aux = utf8_encode($fields);
					} else {
						$field_aux = $fields;
					}
				}
				/* Se agrega el campo al resultado */
				$result[utf8_encode($nro)] = $field_aux;
			}
		} else {
			if (is_string($fields) ) {
			//Si no es utf-8 lo codifico
				$result = utf8_encode($array);
			} else {
				$result = $array;
			}
		}
		return $result;
	}

}
