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
	protected $log = null;

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


		//log
        	$file = './application/logs/wsapi.log';
        	if( !file_exists($file) ){
                	$this->log = fopen($file,'w');
        	} else {
                	$this->log = fopen($file,'a');
        	}

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
                		fwrite($this->log, "Logeo exitoso-".date('Y-m-d G:i:s')."-".$login." parametros: ".$login_user."-".$dni_user."-".$dni." CVM=".$this->sistemaCVM." datos BD: ".$this->nivel."-".$this->entidad."\n");
            		} else {
                		//No existe la combinacion token - usuario
				echo json_encode($this->array_to_utf8(array("estado" => "100", "result" => array(), "msg" => "No existe usuario-token")));
                		fwrite($this->log, "Error 100-".date('Y-m-d G:i:s')."-".$login." parametros: ".$login_user."-".$dni_user."\n");
            		}
        	}  else {
                		//No tiene permiso para esta funcion
				echo json_encode($this->array_to_utf8(array("estado" => "101", "result" => array(), "msg" => "No vino token de validacion")));
                		fwrite($this->log, "Error 101-".date('Y-m-d G:i:s')."-".$login." parametros: ".$login_user."-".$dni_user."\n");
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
			$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => $arr_ret, "msg" => "Acceso permitido y validado")));
                	fwrite($this->log, "Usuario generico OK-".date('Y-m-d G:i:s')."-".$this->login." entidad: ".$this->entidad."- nivel ".$this->nivel."\n");
			echo $result;
		}
	}

	function get_user() { // esta funcion devuelve datos de un usuario una vez validado token de userapp y pasando por POST login y DNI
		if ( $this->token_ok ) {
			$this->load->model('admins_model');
			$usuario = $this->admins_model->get_user_app_dni($this->login_user, $this->dni_user);
			if ( $usuario ) {
				$arr_ret = array ( 'nivel' => $usuario->nivel, 'token' => $usuario->token, 'entidad' => $usuario->id_entidad, 'email' => $usuario->email );
				$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => $arr_ret, "msg" => "Usuario permitido y validado")));
                		fwrite($this->log, "Obtuvo Usuario OK-".date('Y-m-d G:i:s')."-".$this->login_user." entidad: ".$usuario->id_entidad."- nivel ".$usuario->nivel."\n");
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "107", "result" => null, "msg" => "Usuario inexistente".$this->login_user."--".$this->dni_user)));
                		fwrite($this->log, "Error 107-".date('Y-m-d G:i:s')."-".$this->login_user."\n");
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
						$result = json_encode($this->array_to_utf8(array("estado" => "102", "result" => null, "msg" => "No se pudo procesar padron CVM")));
					}
				} else {
					$this->load->model('socios_model');
					$padron = $this->socios_model->get_padron_app($this->entidad);
					if ( $padron ) {
						$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => $padron, "msg" => "Proceso OK")));
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "103", "result" => null, "msg" => "No se pudo procesar padron entidad")));
					}
				}
                		fwrite($this->log, "Obtuvo padron OK -".date('Y-m-d G:i:s')."-".count($padron)." socios"."\n");
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => null, "msg" => "Usuario sin nivel para esta funcion")));
                		fwrite($this->log, "Error 106 -".date('Y-m-d G:i:s')."-".$this->login."\n");
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
                				fwrite($this->log, "Error 104 -".date('Y-m-d G:i:s')."-".$this->login."\n");
					}
				} else {
					$this->load->model('socios_model');
					$socio = $this->socios_model->get_socio_by_dni($this->entidad, $this->dni);
					if ( $socio ) {
						$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => $socio, "msg" => "Proceso OK")));
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "105", "result" => null, "msg" => "No se pudo obtener el socio entidad")));
                				fwrite($this->log, "Error 105 -".date('Y-m-d G:i:s')."-".$this->login."\n");
					}
				}
		
                		fwrite($this->log, "Obtuvo socio OK -".date('Y-m-d G:i:s')."-".$this->dni."\n");
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => null, "msg" => "Usuario sin nivel para esta funcion")));
                		fwrite($this->log, "Error 106 -".date('Y-m-d G:i:s')."-".$this->login."\n");
				echo $result;
			}
		}
	}

	function get_carnet() { // esta funcion devuelve los datos de un socio puntual a partir de su DNI y en base a la entidad correspondiente al usuario logueado
		// Si no esta validado el usuario-token devuelvo false
		if ( $this->token_ok ) {
			// Si es nivel 0 y tiene id_entidad -2 es el caso de Carbono14 !!!!
			if ( $this->nivel > 0 || ( $this->nivel == 0 && $this->entidad == -2 ) ) {
				if ( $this->nivel == 0 ) {
					if ( $this->input->get_post('id_entidad') > 0 ) {
        					$id_entidad = $this->input->get_post('id_entidad');
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "111", "result" => null, "msg" => "No se pudo obtener entidad")));
                				fwrite($this->log, "Error 111 -".date('Y-m-d G:i:s')."-".$this->login."\n");
						echo $result;
						exit;
					}
	 			} else {
        				$id_entidad = $this->entidad;
				}
				if ( $this->sistemaCVM ) {
					// Conecto con el sitio de CVM y traigo datos del socio desde ahi
					$socio = $this->url_CVM('get_carnet', $this->dni);
					if ( $socio ) {
						$result = $socio;
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "104", "result" => null, "msg" => "No se pudo obtener el socio CVM")));
                				fwrite($this->log, "Error 104 -".date('Y-m-d G:i:s')."-".$this->login."\n");
					}
				} else {
					$this->load->model('socios_model');

					$socio = $this->socios_model->get_carnet_by_dni($id_entidad, $this->dni);
					if ( $socio ) {
						$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => $socio, "msg" => "Proceso OK")));
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "105", "result" => null, "msg" => "No se pudo obtener el socio entidad")));
                				fwrite($this->log, "Error 105 -".date('Y-m-d G:i:s')."-".$this->login."\n");
					}
				}
		
                		fwrite($this->log, "Obtuvo socio OK -".date('Y-m-d G:i:s')."-".$this->dni."\n");
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => null, "msg" => "Usuario sin nivel para esta funcion")));
                		fwrite($this->log, "Error 106 -".date('Y-m-d G:i:s')."-".$this->login."\n");
				echo $result;
			}
		}
	}

	function check_estado() { // esta funcion devuelve el estado de deuda de un socio puntual a partir de su DNI y en base a la entidad correspondiente al usuario logueado
		// Si no esta validado el usuario-token devuelvo false
		if ( $this->token_ok ) {
			if ( $this->nivel > 0 ) {
				if ( $this->sistemaCVM ) {
					// Conecto con el sitio de CVM y traigo datos del socio desde ahi
					$socio = $this->url_CVM('check_estado', $this->dni);
					$socio_decode = json_decode($socio);
					if ( $socio_decode->estado == 0 ) {
						if ( $socio_decode->result->suspendido == 0 ) {
							if ( $socio_decode->result->semaforo == 1  || $socio_decode->result->semaforo == 10 ) {
								$result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => null, "msg" => "Socio OK")));
                						fwrite($this->log, "Socio al dia -".date('Y-m-d G:i:s')."-".$this->dni."\n");
							} else {
								$result = json_encode($this->array_to_utf8(array("estado" => "108", "result" => null, "msg" => "Socio con deuda")));
                						fwrite($this->log, "Error 108 -".date('Y-m-d G:i:s')."-".$socio_decode->dni."-".$socio_decode->apellido.", ".$socio_decode->nombre."\n");
							}
						} else {
							$result = json_encode($this->array_to_utf8(array("estado" => "109", "result" => null, "msg" => "Socio suspendido")));
                					fwrite($this->log, "Error 109 -".date('Y-m-d G:i:s')."-".$socio_decode->dni."-".$socio_decode->apellido.", ".$socio_decode->nombre."\n");
						}
					} else {
						$result = json_encode($this->array_to_utf8(array("estado" => "110", "result" => null, "msg" => "Socio inexistente")));
                				fwrite($this->log, "Error 110 -".date('Y-m-d G:i:s')."-".$socio_decode->dni."-".$socio_decode->apellido.", ".$socio_decode->nombre."\n");
					}
				} else {
					$this->load->model('socios_model');
					$socio = $this->socios_model->get_status_by_dni($this->entidad, $this->dni);
                                        if ( $socio ) {
                                                if ( $socio->suspendido == 0 ) {
                                                        if ( $socio->semaforo == 1 || $socio->semaforo == 10 ) {
                                                                $result = json_encode($this->array_to_utf8(array("estado" => "0", "result" => null, "msg" => "Socio OK")));
                						fwrite($this->log, "Socio al dia -".date('Y-m-d G:i:s')."-".$this->dni."\n");
                                                        } else {
                                                                $result = json_encode($this->array_to_utf8(array("estado" => "108", "result" => null, "msg" => "Socio con deuda")));
                						fwrite($this->log, "Error 108 -".date('Y-m-d G:i:s')."-".$socio->dni."-".$socio->apellido.", ".$socio->nombre."\n");
                                                        }
                                                } else {
                                                        $result = json_encode($this->array_to_utf8(array("estado" => "109", "result" => null, "msg" => "Socio suspendido")));
                					fwrite($this->log, "Error 109 -".date('Y-m-d G:i:s')."-".$socio->dni."-".$socio->apellido.", ".$socio->nombre."\n");
                                                }
                                        } else {
                                                $result = json_encode($this->array_to_utf8(array("estado" => "110", "result" => null, "msg" => "Socio inexistente")));
                				fwrite($this->log, "Error 110 -".date('Y-m-d G:i:s')."-".$socio->dni."-".$socio->apellido.", ".$socio->nombre."\n");
                                        }
				}
		
				echo $result;
			} else {
				$result = json_encode($this->array_to_utf8(array("estado" => "106", "result" => null, "msg" => "Usuario sin nivel para esta funcion")));
				echo $result;
			}
		}
	}

	function url_CVM($funcion, $dni='') {
    		$url = "localhost/CVM_online/ws_api/".$funcion;
    		//$url = "http://clubvillamitre.com/ws_api/".$funcion;
    
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
