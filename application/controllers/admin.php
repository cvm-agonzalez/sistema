<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session','form_validation'));
        $this->load->helper(array('url','form','date'));
        $this->load->model('login_model');
        $this->load->database('default');
        $this->date_facturacion = 25;
        if( !$this->session->userdata('is_logued_in') && ( $this->uri->segment(2) != 'login' && $this->uri->segment(2) ) ){
            redirect(base_url().'admin');
        }
    }

	private function carga_data() {
		$data = array();
		$data['ent_nombre'] = $this->session->userdata('ent_nombre');
		$data['ent_directorio'] = $this->session->userdata('ent_directorio');
		$data['ent_abreviatura'] = $this->session->userdata('abreviatura');
		$data['id_entidad'] = $this->session->userdata('id_entidad');
		$data['username'] = $this->session->userdata('username');
		$data['rango'] = $this->session->userdata('rango');
		$data['baseurl'] = base_url();
		return $data;
	}
 
    public function no_facturado($value='')
    {
	$id_entidad = $this->session->userdata('id_entidad');
        $this->db->where('socios.id_entidad', $id_entidad);
        $this->db->where('socios.suspendido', 0);
        $this->db->where('socios.estado', 1);
        $this->db->where('socios.tutor', 0);
        $query = $this->db->get('socios');
        if( $query->num_rows() == 0 ){ return false; }
        $socios = $query->result();
        $no_facturados = $facturados = array();
        foreach ($socios as $socio) {
            $this->db->where('id_entidad', $id_entidad);
            $this->db->where('sid', $socio->id);
            $this->db->like('date', '2016-03-01','after');
            $query = $this->db->get('facturacion');

            if( $query->num_rows() == 0 ){
                $no_facturados[] = $socio->id;
            }else{
                $facturados[] = $socio->id;
            }
        }
        echo '<strong>facturados</strong><br>';
        foreach ($facturados as $facturado) {
            echo $facturado.'<br>';
            $this->db->where('id', $facturado);
            $this->db->update('socios', array('facturado'=>1));
        }
        echo '<strong>no_facturados</strong><br>';
        foreach ($no_facturados as $facturado) {
            echo $facturado.'<br>';
            $this->db->where('id', $facturado);
            $this->db->update('socios', array('facturado'=>0));
        }
    }

	private function gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1) {
		include('gen_EXCEL.php');
	}


    public function arma_listdebitos() {
		$id_entidad = $this->session->userdata('id_entidad');
        	$this->load->model("debtarj_model");
        	$this->load->model("socios_model");
        	$this->load->model("tarjeta_model");
        	$debtarjs = $this->debtarj_model->get_debtarjs($id_entidad);
        	foreach ($debtarjs as $debtarj){
			$socio=$this->socios_model->get_socio($debtarj->sid);
			$tarjeta=$this->tarjeta_model->get($debtarj->id_marca);
			if ( $socio ) {
                      		$nombre = $socio->nombre.", ".$socio->apellido;
				$debito = $this->debtarj_model->get_debitos_by_socio($id_entidad, $debtarj->id);
				if ( $debito ) {
					$precio = $debito->importe;
					$fecha = $debito->fecha_debito;
				} else {
					$precio = 0.00;
					$fecha = "0000-00-00";
				}
                                switch ( $debtarj->estado ) {
                                        case 0: $estado = "BAJA"; break;
                                        case 1: $estado = "ACTIVO"; break;
                                        case 2: $estado = "STOP DEBIT"; break;
                                        default: $estado = "XYZ"; break;
                                }

                		$datos[] = array (
                      			'id' => $debtarj->id,
                      			'sid' => $debtarj->sid,
                      			'id_entidad' => $debtarj->id_entidad,
                      			'dni' => $socio->dni,
                      			'name' => $nombre,
                      			'id_marca' => $debtarj->id_marca,
                      			'tarjeta' => $tarjeta->descripcion,
                      			'nro_tarjeta' => $debtarj->nro_tarjeta,
                      			'fecha' => $fecha,
		      			'price' => $precio,
                                        'estado' => $estado
                		);
        		}
		}
		return $datos;
    }

    public function sube_asociados($id_entidad, $id_actividad, $dato1col) {
	//datos del arhivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];

	$socios=false;
	//compruebo si las características del archivo son las que deseo
	if (!((strpos($nombre_archivo, "csv") || strpos($nombre_archivo, "txt")) && ($tamano_archivo < 100000))) {
    		echo "La extensión o el tamaño de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .txt o .csv<br><li>se permiten archivos de 100 Kb máximo.</td></tr></table>";
	}else{
		$this->load->model("socios_model");
		$this->load->model("actividades_model");


    		$lineas=file($_FILES['userfile']['tmp_name']);
		$cont=0;
		$serial=0;
		$socios=array();
		foreach ($lineas as $num_linea => $linea) {
			$campos = explode(',', $linea);

			$col1=trim($campos[0],"\n\t\r");
			var_dump($col1);

                	// Con los datos del archivo busco en la BD
			if ( $dato1col == "sid" ) {
				$socio=$this->socios_model->get_socio($col1);
			} else {
				$socio=$this->socios_model->get_socio_by_dni($id_entidad,$col1);
			}


			if ( $socio ) {
				$sid=$socio->id;
				$existe=0;
				$act_asoc=$this->actividades_model->get_act_asoc_puntual($id_entidad,$sid,$id_actividad);
				if ( $act_asoc ) {
					$socios[] = array(
            					'sid' => $sid,
            					'apynom' => $socio->nombre.' '.$socio->apellido,
            					'estado_asoc' => $socio->suspendido,
            					'dni'=>$socio->dni,
            					'actividad' => 1
            					);
					$existe=1;
				} else {
					$socios[] = array(
            					'sid' => $sid,
            					'apynom' => $socio->nombre.' '.$socio->apellido,
            					'estado_asoc' => $socio->suspendido,
            					'dni'=>$socio->dni,
            					'actividad' => 0
            					);
				}

			} else {
					$socios[] = array(
            					'sid' => $col1,
            					'apynom' => $dato1col.' - No existe en la base de datos ',
            					'estado_asoc' => 0,
            					'dni'=> 0,
            					'actividad' => 0
            					);
			}
			$serial++;

		}
	}
	return $socios;
    }

    public function sube_coopeplus($id_entidad, $periodo,$id_marca,$fecha_debito) {
	$result = false;
	//datos del arhivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	//compruebo si las características del archivo son las que deseo
	if (!((strpos($nombre_archivo, "csv") || strpos($nombre_archivo, "txt")) && ($tamano_archivo < 100000))) {
    		echo "La extensión o el tamaño de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .txt o .csv<br><li>se permiten archivos de 100 Kb máximo.</td></tr></table>";
	}else{
		$this->load->model("debtarj_model");
    		$lineas=file($_FILES['userfile']['tmp_name']);
// Recorrer nuestro array, mostrar el código fuente HTML como tal y mostrar tambíen los números de línea.
		$cont=0;
		$serial=0;
		$total=0;
		foreach ($lineas as $num_linea => $linea) {
			if ( $cont++ > 3 ) {
				$campos = explode(',', $linea);
				// Fecha Log
				$campos[0] = substr($linea,0,10);
				// Nro Tarjeta
				$campos[1] = substr($linea,12,18);
				if ( $campos[1] == "------------------" ) {
					break;
				}
				// Apellido y Nombre del TarjetaHabiente
				$campos[2] = substr($linea,32,30);
				// Importe
				$campos[3] = substr($linea,128,10);
				// Mensaje
				$campos[4] = substr($linea,148,25);

				// Fecha informada
				$dd=substr($campos[0],0,2);
				$mm=substr($campos[0],3,2);
				$aa=substr($campos[0],6,4);
				$fecha_acred=$aa."-".$mm."-".$dd;
				$nro_tarjeta=$campos[1];
				$importe=$campos[3];
				$apynom=$campos[2];
				$mensaje=$campos[4];


				if ( trim($mensaje) == "DEBITO EXITOSO" ||  trim(substr($mensaje,0,15)) == "DEBITO ACEPTADO" ) {
					$by=array("id_marca"=>$id_marca, "nro_tarjeta"=>$nro_tarjeta, "id_entidad"=>$id_entidad);
					$debtarj=$this->debtarj_model->get_debtarj_by($by);

					foreach ( $debtarj as $debtj) {
						if ( $debtj ) {
							$id_debito=$debtj->id;
							$debito=$this->debtarj_model->get_debito_by_id($id_entidad, $id_debito, $fecha_debito);
							if ( $debito && $debito->importe == $importe ) {
								$serial++;
								$total=$total+$importe;

								$id_debito = $debito->id;
								$this->debtarj_model->upd_acred($id_debito, $fecha_acred);
							}
						}
					}
				}
			}
		}
	}
	if ( $serial > 0 ) {
		$this->debtarj_model->upd_noacred($id_entidad, $id_marca, $periodo);
		$this->debtarj_model->upd_gen($id_entidad, $periodo, $id_marca, $serial, $total);
		$result=true;
	}
	return $result;
    }

    public function sube_visa($id_entidad, $periodo,$fecha_debito) {

	$result = false;
	$id_marca=1;
	//datos del arhivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	//compruebo si las características del archivo son las que deseo
	if (!((strpos($nombre_archivo, "csv") || strpos($nombre_archivo, "txt")) && ($tamano_archivo < 100000))) {
    		echo "La extensión o el tamaño de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .txt o .csv<br><li>se permiten archivos de 100 Kb máximo.</td></tr></table>";
	}else{
		$this->load->model("debtarj_model");
    		$lineas=file($_FILES['userfile']['tmp_name']);
// Recorrer nuestro array, mostrar el código fuente HTML como tal y mostrar tambíen los números de línea.
		$cont=0;
		$serial=0;
		$total=0;
		foreach ($lineas as $num_linea => $linea) {
			if ( $cont++ > 3 ) {

				$campos = explode(',', $linea);

				$fecha1=$campos[0];
				$fecha2=$campos[4];
				$serie=$campos[5];
				$xnro_tarjeta=$campos[6];
				$importe=$campos[10];

				while ( substr_count($importe, '.') > 1 ) {
					$pos=strpos($importe,'.');
					$importe=substr($importe,0,$pos).substr($importe,$pos+1);
				}

				// Conversion fecha
				$dxx=date('d M Y',strtotime($fecha2));
				$fecha_debito=date('Y-m-d',strtotime($dxx));
				$dxx=date('d M Y',strtotime($fecha1));
				$fecha_acred=date('Y-m-d',strtotime($dxx));

                		// Con los datos del archivo busco en la BD
                		$nro_tarjeta=substr($xnro_tarjeta,14,4);

				//echo $fecha_debito."-".$fecha_acred."-".$nro_tarjeta."-".$serie."-".$serial."-".$serie."-".$importe."\n";
				$debito=$this->debtarj_model->get_debito_rng($id_entidad, $id_marca, $fecha_debito, $serie);

				// print_r($debito);
				if ( $debito ) {
					$serial++;
					$total=$total+$importe;
					$id_debito = $debito->id;
					$this->debtarj_model->upd_acred($id_debito, $fecha_acred);
				}
			}
		}
	}
	if ( $serial > 0 ) {
		$this->debtarj_model->upd_noacred($id_entidad, $id_marca, $periodo);
		$this->debtarj_model->upd_gen($id_entidad, $periodo, $id_marca, $serial, $total);
		$result=true;
	}
	return $result;
    }


    public function listado(){
	$id_entidad = $this->session->userdata('id_entidad');
        $this->load->model("socios_model");
        $this->load->model("pagos_model");
        $data['socios'] = $this->socios_model->listar($id_entidad);


        foreach ($data['socios'] as $socio){
	    $xestado = "XXXX";
	    if ( $socio['datos']->suspendido == 1 ) {
		$xestado = "SUSP";
	    } else {
		$xestado = "ACTI";
 	    }
            $datos[] = array(
            'id' => $socio['datos']->id,
            'name' => $socio['datos']->nombre.' '.$socio['datos']->apellido,
            'dni'=>$socio['datos']->dni,
            'price' => $socio['cuota']['total'],
	    'estado' => $xestado,
	    'deuda' => $socio['datos']->deuda,
            'actividades' => $socio['datos']->actividades
            );
        }

        $datos = json_encode($datos);
        echo $datos;
    }

    public function listado_act(){
	$id_entidad = $this->session->userdata('id_entidad');
        $this->load->model("actividades_model");
        $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
        foreach ($data['actividades'] as $actividad){
            switch ( $actividad->estado ) {
                case 0: $estado="BAJA"; break;
                case 1: $estado="ACTIVA"; break;
                case 2: $estado="SUSPENDIDA"; break;
                default: $estado="XYZ"; break;
            }
            switch ( $actividad->solo_socios ) {
                case 0: $solo_socio="NO"; break;
                case 1: $solo_socio="SI"; break;
                default: $solo_socio="XYZ"; break;
            }

            $datos[] = array (
            'aid' => $actividad->aid,
            'id' => $actividad->id,
            'name' => $actividad->nombre,
            'price' => $actividad->precio,
            'cta_inic' => $actividad->cuota_inicial,
            'seguro' => $actividad->seguro,
            'estado' => $estado,
            'solo_socios' => $solo_socio
            );
        }
        $datos = json_encode($datos);
        echo $datos;
    }

    public function index() {
	if(!$this->session->userdata('is_logued_in')){
		$data = $this->carga_data();
		$data['token'] = $this->token();
		$this->load->view('login-form',$data);
	}else{
		if($this->session->userdata('prox_vto') == 1 ){
			$data = $this->carga_data();
			$data['mensaje1'] = "La contraseña actual se vence en 10 dias recuerde cambiarla";
			$data['section'] = 'ppal-mensaje';
			$this->load->view('admin',$data);
		} else {
                	if($this->session->userdata('prox_vto') == -1 ){
            			redirect(base_url()."admin/admins/chgpwd");
			} else {
            			redirect(base_url()."admin/socios");
			}
		}
	}
    }

    public function morosos(){
	$data = $this->carga_data();
        $data['section'] = 'morosos';
        $this->load->view('admin',$data);
    }

	public function login()
	{
		if ( !$this->session->userdata('id_entidad') ) {
			$this->form_validation->set_rules('entidad', 'entidad', 'required|trim|min_length[1]|max_length[50]|xss_clean');
		}
		$this->form_validation->set_rules('username', 'nombre de usuario', 'required|trim|min_length[2]|max_length[150]|xss_clean');
		$this->form_validation->set_rules('password', 'password', 'required|trim|min_length[5]|max_length[150]|xss_clean');
	
		//lanzamos mensajes de error si es que los hay
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('usuario_incorrecto','Falta ingresar algún dato.');
			redirect(base_url().'admin');
		}else{
			if ( !$this->session->userdata('id_entidad') ) {
				$entidad = $this->input->post('entidad');
			} else {	
				$entidad = $this->session->userdata('id_entidad');
			}
			$username = $this->input->post('username');
			$password = sha1($this->input->post('password'));
			$check_user = $this->login_model->login_user($entidad, $username,$password);
			if($check_user == TRUE)
			{
				// Valido ultimo cambio de contraseña
                        	if ( $check_user->ult_cambio > 90 ) {
                                	$prox_vto = -1;
                        	} else {
                                	if ( $check_user->ult_cambio > 80 ) {
                                        	$prox_vto = 1;
                                	} else {
                                        	$prox_vto = 0;
                                	}
                        	}

				$hoy=new DateTime(date('Y-m-d'));
				$ult_cambio=new DateTime($check_user->last_chgpwd);
				$dias = $hoy->diff($ult_cambio);
				if ( $dias->days > 90 ) {
					$prox_vto = -1;
				} else { 
					if ( $dias->days > 80 ) {
						$prox_vto = 1;
					} else {
						$prox_vto = 0;
					}
				}
				// Busco datos Entidad
        			$this->load->model('general_model');
				$entidad = $this->general_model->get_ent_dir($check_user->id_entidad);

				// Seteo variables de sesion
				$data = array( 'is_logued_in'     =>         TRUE,
						'id_entidad'     =>         $check_user->id_entidad,
						'id_usuario'     =>         $check_user->id,
						'rango'        =>        $check_user->rango,
						'mail'        =>        $check_user->mail,
						'username'         =>         $check_user->user,
						'ent_abreviatura' => $entidad->abreviatura,
						'ent_nombre' => $entidad->descripcion,
						'ent_directorio' => $entidad->dir_name,
						'prox_vto'	=> $prox_vto,
						'last_chgpwd'         =>         $check_user->last_chgpwd);
				$this->session->set_userdata($data);
				$this->login_model->update_lCon();
	
				// Grabo log de cambios
				$id_entidad = $this->session->userdata('id_entidad');
				$login = $this->session->userdata('username');
				$nivel_acceso = $this->session->userdata('rango');
				$tabla = "login";
				$operacion = 0;
				$llave = $this->session->userdata('id_usuario');
				$observ = "Logueo exitoso";
				$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

				$data = $this->carga_data();
                                redirect(base_url().'admin');

			}
		}
    	}

	public function token()
    {
        $token = md5(uniqid(rand(),true));
        $this->session->set_userdata('token',$token);
        return $token;
    }
    public function img_token()
    {
        $token = md5(uniqid(rand(),true));
        $this->session->set_userdata('img_token',$token);
        return $token;
    }
      public function logout()
    {
        $this->session->sess_destroy();
	// Grabo log de cambios
	$id_entidad = $this->session->userdata('id_entidad');
	$login = $this->session->userdata('username');
	$nivel_acceso = $this->session->userdata('rango');
	$tabla = "login";
	$operacion = 0;
	$llave = $this->session->userdata('id_usuario');
	$observ = "Logout exitoso";
	$this->log_cambios($id_entidad,$login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
	redirect(base_url().'admin');
    }

    public function admins($action='',$id='')
    {
        $this->load->model('admins_model');
        $this->login_model->update_lCon();
        $data['id_entidad'] = $this->session->userdata('id_entidad');
        $data['username'] = $this->session->userdata('username');
        $data['rango'] = $this->session->userdata('rango');
        $data['baseurl'] = base_url();
        switch ($action) {
            case 'agregar':
                $admin = $this->input->post(null, true);
                $admin['pass'] == sha1($admin['pass']);
                $id = $this->admins_model->insert_admin($admin);

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "admin";
                $operacion = 1;
                $llave = $id;
                $observ = substr(json_encode($admin),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/admins','refresh');
                break;

            case 'chgpwd':
		$data = $this->carga_data();
        	$id = $this->session->userdata('id_usuario');
                $data['admin'] = $this->admins_model->get_admin($id);
                $data['action'] = "chgpwd";
                $data['section'] = 'admins-editar';
                $this->load->view('admin',$data);
                break;

            case 'editar':
		$data = $this->carga_data();
                $data['admin'] = $this->admins_model->get_admin($id);
                $data['action'] = "edit";
                $data['section'] = 'admins-editar';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $admin = $this->input->post(null, true);
		if ( $admin['pass_old'] ) {
			$pwd_old = sha1($admin['pass_old']);
                	$rtdo = $this->admins_model->chk_pwd($id,$pwd_old);
			if ( !$rtdo ) {
				$data = $this->carga_data();
				$data['mensaje1'] = "La contraseña actual es incorrecta ";
				$data['section'] = 'ppal-mensaje';
				$this->load->view('admin',$data);
				break;
			}
                	if( $admin['pass1'] != ''){
				if ( strlen($admin['pass1']) < 8 ) {
					$data = $this->carga_data();
					$data['mensaje1'] = "Las contraseñas deben tener al menos 8 caracteres";
					$data['section'] = 'ppal-mensaje';
					$this->load->view('admin',$data);
					break;
				}
				if ( $admin['pass1'] == $admin ['pass_old'] ) {
					$data = $this->carga_data();
					$data['mensaje1'] = "La nueva contraseña no puede ser igual a la actual";
					$data['section'] = 'ppal-mensaje';
					$this->load->view('admin',$data);
					break;
				}
                		if($admin['pass1'] == $admin['pass2'] ){
                    			$new_pwd = sha1($admin['pass1']);
                			unset($admin['pass_old']);
                			$this->admins_model->update_pwd($id,$new_pwd);

					$data = $this->carga_data();
					$data['mensaje1'] = "La nueva contraseña fue correctamente actualizada. Cierre sesion y vuelva a ingresar.";
					$data['section'] = 'ppal-mensaje';
					$data['msj_boton2'] = 'Cerrar Sesion y volver a loguearse';
					$data['url_boton2'] = base_url().'admin/logout';
					$this->load->view('admin',$data);
					break;
				} else {
					$data = $this->carga_data();
					$data['mensaje1'] = "Las contraseñas nuevas no coinciden";
					$data['section'] = 'ppal-mensaje';
					$this->load->view('admin',$data);
				}
				break;
                	} else {
				$data = $this->carga_data();
				$data['mensaje1'] = "La contraseña nuevas no puede estar vacia";
				$data['section'] = 'ppal-mensaje';
				$this->load->view('admin',$data);
				break;
			}
		} else {
                	if($admin['pass1'] == $admin['pass2'] && $admin['pass1'] != ''){
                    	$admin['pass'] = sha1($admin['pass1']);
                	}
                	unset($admin['pass1']);
                	unset($admin['pass2']);
		}

                $this->admins_model->update_admin($id,$admin);

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "admin";
                $operacion = 2;
                $llave = $id;
                $observ = substr(json_encode($admin),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/admins','refresh');

                break;

            case 'eliminar':
                $admin = array('estado' => 0 );
                $this->admins_model->update_admin($id,$admin);

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "admin";
                $operacion = 3;
                $llave = $id;
                $observ = substr(json_encode($admin),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/admins','refresh');
                break;

            default:
		$data = $this->carga_data();
		$id_entidad  = $this->session->userdata('id_entidad');
                $data['listaAdmin'] = $this->admins_model->get_admins($id_entidad);
                $data['section'] = 'admins';
                $this->load->view('admin',$data);
                break;
        }
    }

    public function entidades($action='',$id='')
    {
        $this->load->model('general_model');
	$data = $this->carga_data();
        switch ($action) {
            case 'agregar':
                $entidad = $this->input->post(null, true);
                $id = $this->general_model->insert_ent($entidad);

		// TODO agregar todas las configuraciones default y creacion de directorios

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "entidades";
                $operacion = 1;
                $llave = $id;
                $observ = substr(json_encode($entidad),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/entidades','refresh');
                break;

            case 'editar':
		$data = $this->carga_data();
		$id_ent =  $this->uri->segment(4);
		$data['entidad'] = $this->general_model->get_ent($id_ent);
                $data['action'] = "edit";
                $data['section'] = 'entidades-editar';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $entidad = $this->input->post(null, true);
                $this->general_model->update_ent($id,$entidad);

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "entidades";
                $operacion = 2;
                $llave = $id;
                $observ = substr(json_encode($admin),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/entidades','refresh');
                break;

            case 'eliminar':
                $entidad = array('estado' => 0 );
                $this->admins_model->update_ent($id,$entidad);

                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "entidades";
                $operacion = 3;
                $llave = $id;
                $observ = substr(json_encode($admin),0,255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/entidades','refresh');
                break;

            default:
		$data = $this->carga_data();
		$id_entidad  = $this->session->userdata('id_entidad');
                $data['listaEntidades'] = $this->general_model->get_ents();
                $data['section'] = 'entidades';
                $this->load->view('admin',$data);
                break;
        }
    }
    public function socios()
    {
        switch ($this->uri->segment(3)) {
            /**

            **/
            case 'categorias':
		if ( $this->uri->segment(4) ) {
			switch ( $this->uri->segment(4) ) {
				case 'editar':
					$idcateg = $this->uri->segment(5);
                			$this->load->model('general_model');
					$categ=$this->general_model->get_cat($idcateg);
					$data = $this->carga_data();
                			$data['categoria'] = $categ;
                			$data['action'] = 'agregar';
                			$data['section'] = 'categorias-editar';
                			$this->load->view('admin',$data);
					break;
				case 'editar-do':
					$idcateg = $this->uri->segment(5);
                			$this->load->model('general_model');
       			                $datos['id'] = $idcateg;
       			                $datos['nombre'] = $this->input->post('nombre');
       			                $datos['precio'] = $this->input->post('precio');
       			                $datos['estado'] = $this->input->post('estado');
					if ( $datos['precio'] == '' ) { $datos['precio'] = 0; }
                        		$this->general_model->update_cat($idcateg,$datos);

					// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                        		$nivel_acceso = $this->session->userdata('rango');
					$tabla = "categorias";
					$operacion = 2;
					$llave = $idcateg;
					$observ = substr(json_encode($datos),0,255);
    					$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                                	redirect(base_url().'admin/socios/categorias');
					break;
				case 'eliminar':
					$idcateg = $this->uri->segment(5);
                			$this->load->model('general_model');
					$categ=$this->general_model->delete_cat($idcateg);

					// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                        		$nivel_acceso = $this->session->userdata('rango');
					$tabla = "categorias";
					$operacion = 3;
					$llave = $idcateg;
					$observ = "Borrado de la categoria $idcateg";
    					$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                                	redirect(base_url().'admin/socios/categorias');
					break;
				case 'agregar':
					$data = $this->carga_data();
                			$data['action'] = 'agregar';
                			$data['section'] = 'categorias-agregar';
                			$this->load->view('admin',$data);
					break;
				case 'agregar-do':
                			$this->load->model('general_model');
       			                $datos['id'] = 0;
                			$datos['id_entidad'] = $this->session->userdata('id_entidad');
       			                $datos['nombre'] = $this->input->post('nombre');
       			                $datos['precio'] = $this->input->post('precio');
       			                $datos['estado'] = 1;
					if ( $datos['precio'] == '' ) { $datos['precio'] = 0; }
                        		$this->general_model->insert_cat($datos);

					// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                        		$nivel_acceso = $this->session->userdata('rango');
					$tabla = "categorias";
					$operacion = 1;
					$llave = $idcateg;
					$observ = substr(json_encode($datos), 0, 255);
    					$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                                	redirect(base_url().'admin/socios/categorias');
					break;
				case 'listcateg':
                			$this->load->model('general_model');
                			$id_entidad = $this->session->userdata('id_entidad');
					$result=$this->general_model->get_cats($id_entidad);
                			$datos = json_encode($result);
                			echo $datos;
					break;
			}
		} else {
			$data = $this->carga_data();
                	$id_entidad = $this->session->userdata('id_entidad');
                	$data['action'] = 'nuevo';
                	$data['section'] = 'categorias';
                	$this->load->model("general_model");
                	$data['categorias'] = $this->general_model->get_cats($id_entidad);
                	$this->load->view('admin',$data);
		}
		break;

            case 'get':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
                $socio = $this->socios_model->get_socio_full($id_socio);
		echo json_encode($socio);
                break;


            case 'suspender':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
		$socio = $this->socios_model->get_socio($id_socio);
		$msj = 0;
		// Verifico si ya esta suspendido
		if ( $socio->suspendido == 1 ) {
			$data = $this->carga_data();
			$data['mensaje1'] = "Este socio ya esta Suspendido";
			$data['section'] = 'ppal-mensaje';
			$this->load->view('admin',$data);
		} else {
			// Verifico si tiene deuda
                	$id_entidad = $this->session->userdata('id_entidad');
                	$this->load->model('pagos_model');
			$deuda = $this->pagos_model->get_deuda_monto($id_entidad, $id_socio);
                	if ( $deuda ) {
				if ( $deuda > 0 ) {
					$msj = 1;
					$data['mensaje2'] = "Este socio tiene deuda por $ ".$deuda;
				}
			}

			// Verifico si tiene Debito Automatico
                	$this->load->model('debtarj_model');
			$debtarj = $this->debtarj_model->get_debtarj_by_sid($id_entidad, $id_socio);
                	if ( $debtarj ) {
				$msj = 1;
				$data['mensaje3'] = "Este socio tiene Debito Automatico de Tarjeta ";
			}

                        $financiacion = $this->pagos_model->get_financiado_mensual($id_entidad, $id_socio);
                        if ( $financiacion ) {
				$fin=$financiacion[0];
                                $msj = 1;
                                $data['mensaje4'] = "Este socio tiene un plan de financiacion con ".($fin->cuotas-$fin->actual)." cuotas pendientes";
                        }

			// Si tiene Deuda o Debito Automativo aviso
			if ($msj > 0) {
				$data = $this->carga_data();
				$data['mensaje1'] = "Esta seguro de SUSPENDER a este socio ???";
				$data['msj_boton2'] = 'Igual Suspende';
				$data['url_boton2'] = base_url().'admin/socios/suspender-do/'.$id_socio;
				$data['section'] = 'ppal-mensaje';
				$this->load->view('admin',$data);
			} else {
                		redirect(base_url().'admin/socios/suspender-do/'.$id_socio);
			}
		}

                break;


            case 'suspender-do':
                $id_socio = $this->uri->segment(4);
                $id_entidad = $this->session->userdata('id_entidad');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$observ="Suspendo manualmente a $id_socio. ";

                $this->socios_model->suspender($id_socio);
                $this->pagos_model->registrar_pago($id_entidad,'debe',$id_socio,0.00,'Suspensión Manual desde el Sistema', 'cs', 1);
		
                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "socios";
                $llave = $id_socio;
                $operacion = 2;
                $this->log_cambios($id_entidad, login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                redirect(base_url().'admin/socios/resumen/'.$id_socio);
                break;

            case 'desuspender':
                $id_socio = $this->uri->segment(4);
                $id_entidad = $this->session->userdata('id_entidad');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$socio = $this->socios_model->get_socio($id_socio);
		// Verifico si ya esta suspendido
		if ( $socio->suspendido == 0 ) {
			$data = $this->carga_data();
			$data['mensaje1'] = "Este socio NO esta Suspendido";
			$data['mensaje2'] = var_dump($socio);
			$data['section'] = 'ppal-mensaje';
			$this->load->view('admin',$data);
		} else {
                	$this->socios_model->suspender($this->uri->segment(4),'no');
                	$this->pagos_model->registrar_pago($id_entidad, 'debe',$id_socio,0.00,'Des-suspensión por Sistema');

                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "socios";
                	$llave = $id_socio;
			$observ = "Des-suspendo ".substr(json_encode($socio),235);
                	$operacion = 2;
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                	$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                	redirect(base_url().'admin/socios/resumen/'.$id_socio);
		}
                break;

            case 'enviar_resumen':
//TODO llamar a rutina del CRON
                if(!$this->uri->segment(4)) {
			return false;	
		} else {
			$id_socio = $this->uri->segment(4);
		}
		$id_entidad = $this->session->userdata('id_entidad');

                $this->load->model('pagos_model');
                $deuda = $this->pagos_model->get_deuda($id_socio);

                $this->load->model('cron');
                $cuerpo = $this->cron->armo_emails($id_socio, $deuda);

                $this->email->from('pagos@clubvillamitre.com');
                $this->email->to($mail['mail']);

                $this->email->subject('Resumen de Cuenta');
                $this->email->message($cuerpo);

                $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

                if(preg_match($regex, $mail['mail'])){
                    $this->email->send();
                    $data['enviado'] = 'ok';
                }else{
                    $data['enviado'] = 'no_mail';
                }
		    $data = $this->carga_data();
                    $data['section'] = 'socios-resumen_enviado';
                    $this->load->view('admin',$data);
                break;
            /**

            **/
            case 'buscar':
                if($_GET['dni']){
                    $id_entidad = $this->session->userdata('username');
                    $data['id_entidad'] = $id_entidad;
                    $data['username'] = $this->session->userdata('username');
                    $data['rango'] = $this->session->userdata('rango');
                    $this->load->model('socios_model');
                    $socio = $this->socios_model->get_socio_by($id_entidad, array('dni'=>$_GET['dni']));
                    if($socio){
                        redirect(base_url().'admin/socios/resumen/'.$socio[0]->id);
                    }else{
                        redirect(base_url().'admin/socios');
                    }
                }else{
                    redirect(base_url().'admin/socios');
                }
                break;
            case 'agregar':
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['action'] = 'nuevo';
                $data['section'] = 'socios-nuevo';
                $data['socio'] = '';
                $this->load->model("general_model");
                $data['categorias'] = $this->general_model->get_cats($id_entidad);

                $this->load->model("socios_model");
                //$data['socios'] = $this->socios_model->get_socios($id_entidad);
		$prox_nsocio = $this->socios_model->get_prox_nsocio($id_entidad);
                $data['prox_nsocio'] = (int)$prox_nsocio;

                $this->load->view('admin',$data);
                break;

            case 'nuevo':
                $id_entidad = $this->session->userdata('id_entidad');
                $data['id_entidad'] = $id_entidad;
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $datos = array();
                foreach($_POST as $key => $val)
                {
                    $datos[$key] = $this->input->post($key);
                }

		$datos['id_entidad'] = $id_entidad;
                if(isset($datos['deuda'])){
                    $deuda = $datos['deuda'];
                    unset($datos['deuda']);
                }
                $this->load->model("socios_model");

                if($prev_user = $this->socios_model->checkDNI($id_entidad, $datos['dni'])){
                    //el dni esta repetido, incluimos la vista de listado con el usuario coincidente
		    $data = $this->carga_data();
                    $data['prev_user'] = $prev_user;
                    $data['section'] = 'socio-dni-repetido';
                    $this->load->view('admin',$data);
                }else{
                    //llamamos al modelo en insertamos los datos
                    //$fecha = explode('-',$datos['nacimiento']);
                    //$datos['nacimiento'] = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
                    unset($datos['files']);
                    unset($datos['tutor_dni']);
                    $tutor = $datos['tutor_sid'];
                    unset($datos['tutor_sid']);
                    $datos['tutor']=$tutor;
                    if ( $datos['tutor'] == '' ) { $datos['tutor'] = 0; }
                    $uid = $this->socios_model->register($datos);

                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "socios";
                	$operacion = 1;
                	$llave = $uid;
                	$observ = substr(json_encode($datos),0,255);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                    $ent_dir = $this->session->userdata('ent_directorio');
                    $token = $this->session->userdata('img_token');
                    if(file_exists(BASEPATH."../images/temp/".$token.".jpg")){
                           $old_name = BASEPATH."../images/temp/".$token.".jpg";
                           $new_name = BASEPATH."../entidades/".$ent_dir."/socios/".$uid.".jpg";
                           rename($old_name,$new_name);
                    }
                    //guardamos la variable con la data de la foto en una imagen

                    if($deuda){
                        //llamamos a la vista de financiar deuda para este usuario con el monto ingresado
                        $this->socios_model->insert_deuda($id_entidad,$uid,$deuda);
                    }

                    if(date('d') < $this->date_facturacion){ //si la fecha es anterior a la definida
                        if($datos['tutor'] == 0){ // y no es un integrante de grupo familiar
                            $this->load->model('pagos_model');
                            $cuota = $this->pagos_model->get_monto_socio($uid);

                            $descripcion = '<strong>Categoría:</strong> '.$cuota['categoria'];
                            if($cuota['categoria'] == 'Grupo Familiar'){
                                $descripcion .= '<br><strong>Integrantes:</strong> ';
                                foreach ($cuota['familiares'] as $familiar) {
                                    $descripcion .= "<li>".$familiar['datos']->nombre." ".$familiar['datos']->apellido."</li>";
                                }
                            }
                            $descripcion .= '<br><strong>Detalles</strong>:<br>';
                            $descripcion .= 'Cuota Mensual '.$cuota['categoria'].' -';
                            if($cuota['descuento'] > 0.00){
                                $descripcion .= "$ ".$cuota['cuota_neta']." &nbsp;<label class='label label-info'>".$cuota['descuento']."% BECADO</label>";
                            }
                            $descripcion .= '$ '.$cuota['cuota'].'<br>';

                            $pago = array(
                                'sid' => $uid,
                                'id_entidad' => $id_entidad,
                                'tutor_id' => $uid,
                                'aid' => 0,
                                'generadoel' => date('Y-m-d'),
                                'descripcion' => $descripcion,
                                'monto' => $cuota['cuota'],
                                'tipo' => 1,
                                );

	                        // Si tiene la cuota social bonificada la doy por paga (estado=0)
				if($pago['monto'] <= 0){
                                	$pago['estado'] = 0;
                                	$pago['pagadoel'] = $xahora;
                        	}
                            	$this->pagos_model->insert_pago_nuevo($pago);

                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "pagos";
                		$operacion = 1;
                		$llave = $uid;
                		$observ = substr(json_encode($pago),0,255);

                            	$facturacion = array(
                                	'sid' => $uid,
                                	'id_entidad' => $id_entidad,
                                	'descripcion'=>$descripcion,
                                	'debe' => $cuota['cuota'],
                                	'haber' => 0,
                                	'total' => $cuota['cuota']*-1
                                	);
                            	$this->pagos_model->insert_facturacion($facturacion);

                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "facturacion";
                		$operacion = 1;
                		$llave = $uid;
                		$observ = substr(json_encode($facturacion),0,255);
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        }
                    }


                    redirect(base_url()."admin/socios/registrado/".$uid);

                }
                break;

            case 'nuevo-tutor':
                $id_entidad = $this->session->userdata('id_entidad');
                $data['id_entidad'] = $id_entidad;
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $tutor['nombre'] = $this->input->get("tutor-nombre");
                $tutor['apellido'] = $this->input->get("tutor-apellido");
                $tutor['dni'] = $this->input->get("tutor-dni");
                $tutor['telefono'] = $this->input->get("tutor-telefono");
                $tutor['mail'] = $this->input->get("tutor-mail");
                $this->load->model("socios_model");
                if(!$tutor['dni'] || $prev_user = $this->socios_model->checkDNI($id_entidad,$tutor['dni'])){
                    //el dni esta repetido, enviamos DNI para que jquery se encargue
                    echo "DNI";
                }else{
                    $uid = $this->socios_model->register($tutor);

                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "socios-tutor";
                		$operacion = 1;
                		$llave = $uid;
                		$observ = substr(json_encode($tutor),0,255);
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                    $data = array("id"=>$uid,"nombre"=>$tutor['nombre'],"apellido"=>$tutor['apellido'],"dni"=>$tutor['dni']);
                    echo (json_encode($data));
                }
                break;

            case 'agregar_imagen':
		$ent_dir = $this->session->userdata('ent_directorio');
                $token = $this->img_token();
                if(move_uploaded_file($_FILES['webcam']['tmp_name'], BASEPATH.'../images/temp/'.$token.'.jpg')){
                    echo $token;
                }
                break;

            case 'subir_imagen':
                $token = $this->img_token();
                $this->load->library('UploadHandler');
                break;

            case 'registrado':
		$data = $this->carga_data();
                $data['uid'] = $this->uri->segment(4);
                $data['section'] = 'socios-registrado';
                $this->load->view('admin',$data);
                break;

            case 'editar':
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['section'] = 'socios-editar';
                $this->load->model("general_model");
                $data['categorias'] = $this->general_model->get_cats($id_entidad);
                $this->load->model("socios_model");
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                if($data['socio']){
                    $data['tutor'] = $this->socios_model->get_socio($data['socio']->tutor);
                }else{

                }
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $id = $this->uri->segment(4); // id del socio
                foreach($_POST as $key => $val)
                {
                    $datos[$key] = $this->input->post($key);
                }


                $id_entidad = $this->session->userdata('id_entidad');
                $datos['id_entidad'] = $id_entidad;
		$data = $this->carga_data();
		if ( $datos['tutor_sid'] == $id ) {
			$data['mensaje1'] = "No puede ponerse como tutor al mismo socio....";
                    	$data['section'] = 'ppal-mensaje';
                    	$this->load->view('admin',$data);
		} else {
                	$this->load->model("socios_model");

                	if( $datos['tutor_sid'] > 0 ) {
				$soctut = $this->socios_model->get_socio($datos['tutor_sid']);
				if ( (int)$soctut->tutor > 0 ) {
					$data['mensaje1'] = "No puede ponerse como tutor a un tutoreado....";
                    			$data['section'] = 'ppal-mensaje';
                    			$this->load->view('admin',$data);
				}
			} else if($prev_user = $this->socios_model->checkDNI($id_entidad,$datos['dni'],$id)){
                    		//el dni esta repetido, incluimos la vista de listado con el usuario coincidente
                    		$data['prev_user'] = $prev_user;
                    		$data['section'] = 'socio-dni-repetido';
                    		$this->load->view('admin',$data);
                		} else {
					$ent_dir = $this->session->userdata('ent_directorio');
                    			$token = $this->session->userdata('img_token');
	
                    			if(file_exists(BASEPATH."../images/temp/".$token.".jpg")){
						$old_name = BASEPATH."../images/temp/".$token.".jpg";
						$new_name = BASEPATH."../entidades/".$ent_dir."/socios/".$id.".jpg";
                        			rename($old_name,$new_name);
					}
                    			unset($datos['files']);
                    			unset($datos['tutor_dni']);
					$tutor = $datos['tutor_sid'];
                    			unset($datos['tutor_sid']);
					$datos['tutor']=$tutor;
					if ( $datos['tutor'] == '' ) { $datos['tutor'] = 0; }
                    			$this->socios_model->update_socio($id_entidad,$id,$datos);
	
                			// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                			$nivel_acceso = $this->session->userdata('rango');
                			$tabla = "socios";
                			$operacion = 2;
                			$llave = $id;
                			$observ = substr(json_encode($datos),0,255);
                			$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
	
                    			if(!isset($error)){
                        			$error = '';
                    			}
                    			redirect(base_url()."admin/socios/registrado/".$id.$error);
                		}
			}
	
                break;

            case 'borrar':
                $data['baseurl'] = base_url();
                $this->load->model("socios_model");
                $this->socios_model->borrar_socio($this->uri->segment(4));
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
               	// Grabo log de cambios
               	$id_entidad = $this->session->userdata('id_entidad');
               	$login = $this->session->userdata('username');
               	$nivel_acceso = $this->session->userdata('rango');
               	$tabla = "socios";
               	$operacion = 3;
               	$llave = $this->uri->segment(4);
               	$observ = "borrado";
               	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                redirect(base_url()."admin/socios");
                break;
            case 'resumen':
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                $data['facturacion'] = $this->pagos_model->get_facturacion($id_entidad,$this->uri->segment(4));
                $data['cuota'] = $this->pagos_model->get_monto_socio($this->uri->segment(4));
		if ( $this->uri->segment(5) ) {
			if ( $this->uri->segment(5) == "excel" ) {
				$archivo="Resument_Cuenta_Asoc_".$this->uri->segment(4)."_".date('Ymd');
				$fila1="ID#".$this->uri->segment(4)."-".trim($data['socio']->apellido).", ".trim($data['socio']->nombre);
				$titulo="ID#".$this->uri->segment(4);
				$headers=array();
				$headers[]="ID_Mov";
				$headers[]="SID";
				$headers[]="Fecha";
				$headers[]="Observacion";
				$headers[]="Debe";
				$headers[]="Haber";
				$headers[]="Saldo";
				$datos=$data['facturacion'];
				$this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
				break;
			}
		}
                $data['section'] = 'socios-resumen';
                $this->load->view('admin',$data);
                break;
            case 'resumen2':
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                $data['facturacion'] = $this->pagos_model->get_facturacion($id_entidad,$this->uri->segment(4));
/* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
            	$array_ahg = $this->pagos_model->get_monto_socio($this->uri->segment(4));
                $data['cuota'] = $array_ahg['total'];
/* Fin Modificacion AHG */
                $data['section'] = 'socios-resumen2';
                $this->load->view('socios-resumen2',$data);
                break;
             case 'resumen-deuda':
		$data = $this->carga_data();
                $data['deuda'] = 'only';
                $data['section'] = 'socios-resumen';
                $this->load->view('admin',$data);
                break;

             case 'resumen-sindeuda':
		$data = $this->carga_data();
                $data['deuda'] = 'no';
                $data['section'] = 'socios-resumen';
                $this->load->view('admin',$data);
                break;


            default:
		$data = $this->carga_data();
                $data['section'] = 'socios';
                $this->load->view('admin',$data);
                break;
        }
    }

    public function log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ) {
		$this->load->model("general_model");
			$datos['log_ts'] = date('Y-m-d H:i:s');
			$datos['id_entidad'] = $id_entidad;
			$datos['login'] = $login;
			$datos['nivel_acceso'] = $nivel_acceso;
			$datos['tabla'] = $tabla;
			$datos['operacion'] = $operacion;
			$datos['clave'] = $llave;
			$datos['observacion'] = $observ;
        	$this->general_model->write_log($datos);
    }

    public function debtarj() {
        switch ($this->uri->segment(3)) {
                case 'subearchivo':

                       $this->load->model("debtarj_model");
                       $id_entidad = $this->session->userdata('id_entidad');
                       $id_marca = $this->uri->segment(4);
                       $fecha_debito = $this->uri->segment(5);
			// Armo el periodo
			$anio=substr($fecha_debito,0,4);
			$anio1=$anio+1;
			$mes=substr($fecha_debito,5,2);
			$mes1=$mes+1;
			if ( $mes1 > 12 ) {
				$periodo=$anio1."01";
			} else {
				if ( $mes1 < 10 ) {
					$periodo=$anio.'0'.$mes1;
				} else {
					$periodo=$anio.$mes1;
				}
			}

                       if ( $this->uri->segment(6) == "excel" ) {
				$result = $this->debtarj_model->get_deberr_by_marca_periodo($id_entidad,$id_marca, $periodo);
				$archivo="Errores_Debito_Tarj_".$id_marca."_".date('Ymd');
                                $fila1=null;
                                $titulo="Marca#".$id_marca."_".$fecha_debito;
                                $headers=array();
                                $headers[]="SID";
                                $headers[]="Apellido";
                                $headers[]="Nombre";
                                $headers[]="Marca";
                                $headers[]="Renglon";
                                $headers[]="Nro Tarjeta";
                                $headers[]="Importe";
                                $datos=$result;
                                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
                                break;
                        }

			$result=false;
			switch ( $id_marca ) {
				case 1: $result = $this->sube_visa($id_entidad, $periodo, $fecha_debito); break;
				case 2: $result = $this->sube_coopeplus($id_entidad, $periodo, $id_marca,$fecha_debito); break;
				case 3: $result = $this->sube_coopeplus($$id_entidad, periodo, $id_marca,$fecha_debito); break;
			}

			$data = $this->carga_data();
			if ( $result ) {
                                $data['mensaje1'] = "Archivo procesado correctamente";
				$data['datos_gen'] = $this->debtarj_model->get_periodo_marca($periodo, $id_marca);
				$data['debitos_error'] = $this->debtarj_model->get_deberr_by_marca_periodo($id_marca, $periodo);
                        	$data['id_marca'] = $id_marca;
                        	$data['fecha_debito'] = $fecha_debito;
                        	$data['url_boton'] = base_url()."admin/debtarj";
                        	$data['msj_boton'] = "Vuelve Listado de Debitos";
                        	$data['section'] = 'load-debtarj-result';
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "debtarj";
                		$operacion = 5;
                		$llave = $id_marca;
                		$observ = "subio archivo de $id_marca para el periodo $periodo con fecha debito = $fecha_debito";
                		$this->log_cambios($id_entidad,$login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                        } else {
                                $data['mensaje1'] = "No se pudo procesar archivo";
                        	$data['ection'] = 'ppal-mensaje';
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "debtarj";
                		$operacion = 5;
                		$llave = $id_marca;
                		$observ = "no pudo subir archivo de $id_marca para el periodo $periodo con fecha debito = $fecha_debito";
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        }
                        $this->load->view("admin",$data);
			break;
                case 'gen_nvo':
                        $id_entidad = $this->session->userdata('id_entidad');
                        $id_marca = $this->uri->segment(4);
                        $periodo = $this->uri->segment(5);
                        $this->load->model("debtarj_model");
                        $this->load->model("pagos_model");
			// Si me viene el parametro de forzar ...
			if ( $this->uri->segment(6) ) {
				$this->debtarj_model->anula_periodo_marca($id_entidad, $periodo, $id_marca);
			} else {
			// Chequeo si el periodo esta generado y quiero volver a generarlo
				if ( $this->debtarj_model->exist_periodo_marca($id_entidad,$periodo, $id_marca) ) {
                        		redirect(base_url()."admin/debtarj/gen-debtarj/1/".$periodo);
		        	}
			}
                        $debtarjs = $this->debtarj_model->get_debtarjs($id_entidad);
			$result=array();
			$renglon=1;
			$asoc_gen=0;
			$total_gen=0;
                        foreach ($debtarjs as $debtarj){
                                // Solo genero los que son de la marca pasado por parametro y que esten en estado (descarta baja(estado=0) y stop debit(estado=2))
                                if ( $debtarj->id_marca == $id_marca ) {
					$mensaje="";
					if ( $debtarj->estado == 1 ) {
                                        	// Busco la cuota social del mes
                                        	$cuota_socio = $this->pagos_model->get_monto_socio($debtarj->sid);
                                        	// Busco el saldo del asociado
                                        	$saldo = $this->pagos_model->get_saldo($debtarj->sid);
                                        	// Si tiene saldo a favor lo descuento, sino la cuota mensual
                                        	if ( $saldo != 0 ) {
                                                	$importe = $cuota_socio['total'] + $saldo;
                                        	} else {
                                                	$importe = $cuota_socio['total'] ;
                                        	}

                                        	// Busco si tiene financiacion activa
                        			$financiacion = $this->pagos_model->get_financiado_mensual($id_entidad, $id_socio);
						$cuota_fin=0;
                        			if ( $financiacion ) {
                                			$fin=$financiacion[0];
							$cuota_fin=($fin->monto/$fin->cuotas);
							// Sumo al importe a debitar la cuota de la financiacion
							$importe = $importe + $cuota_fin;
						}
                                        	// Si quedo algo a pagar lo debito
						$cta=$cuota_socio['total'];
                                        	if ( $importe > 0 ) {
							if ( $saldo != 0 ) {
								if ( $cuota_fin > 0 ) {
									$mensaje="Tiene cuota mensual de $ $cta y se le descuenta $ $importe porque tiene diferencia anterior de $ $saldo y cuota de financiacion de $cuota_fin\n";
								} else {
									$mensaje="Tiene cuota mensual de $ $cta y se le descuenta $ $importe porque tiene diferencia anterior de $ $saldo\n";
								}
							} else {
								$mensaje="Tiene cuota mensual de $ $cta \n";
							}
                                                	$id_debito = $debtarj->id;
                                                	$fecha = date('Y-m-d');
                                                	$ts = date('Y-m-d H:i:s');
                                                	// Inserto el debito del mes
                                                	$this->debtarj_model->insert_debito($id_entidad, $id_debito, $fecha, $importe );

                                                	// Actualizo el ultimo periodo y fecha de generacion
                                                	$debtarj->ult_periodo_generado=$periodo;
                                                	$debtarj->ult_fecha_generacion=$fecha;
                                                	$debtarj->id_entidad=$id_entidad;
                                                	$this->debtarj_model->actualizar($debtarj->id, $debtarj);

							$asoc_gen++;
							$total_gen = $total_gen + $importe;
                                        	} else {
							$mensaje="Tiene cuota mensual de $ $cta pero no se le descuenta porque tiene diferencia anterior de $ $saldo\n";
						}
					} else {
						switch ( $debtarj->estado ) {
							case 0: $estado = "BAJA"; break;
							case 2: $estado = "STOP DEBIT"; break;
							default: $estado = "INDEFINIDO"; break;
						}
						$mensaje="No se genera porque tiene estado $estado \n";
					}
					$result[]=array('renglon'=>$renglon++,'sid'=>$debtarj->sid,'mensaje'=>$mensaje);
                                }
                        }

                        $datos['id_entidad'] = $id_entidad;
                        $datos['id_marca'] = $id_marca;
			$datos['periodo'] = $periodo;
			$datos['fecha_debito'] = date('Y-m-d');
			$datos['cant_generada'] = $asoc_gen;
			$datos['total_generado'] = $total_gen;
			$datos['cant_acreditada'] = 0;
			$datos['total_acreditado'] = 0;
			$datos['estado'] = 1;
			$datos['id'] = 0;

			$this->debtarj_model->insert_periodo_marca($datos);

			$data = $this->carga_data();
               	 	$data['result'] = $result;

               		// Grabo log de cambios
               		$id_entidad = $this->session->userdata('id_entidad');
               		$login = $this->session->userdata('username');
               		$nivel_acceso = $this->session->userdata('rango');
               		$tabla = "debtarj";
               		$operacion = 5;
               		$llave = $id_marca;
               		$observ = "Genero debitos de $id_marca para el periodo $periodo con fecha debito = $fecha_debito por un total de $total_gen para $asoc_gen socios";
               		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

	                $data['section'] = "gen-debtarj-result";
                 	$this->load->view('admin',$data);
                        break;

		case 'baja_arch':
               		$id_entidad = $this->session->userdata('id_entidad');
        		$id_marca = $this->input->post('id_marca');
        		$periodo = $this->input->post('periodo');
        		$totales = $this->input->get('tot');

			$resultado=false;
			switch ( $id_marca ) {
				case 1:
					$ok=$this->_genera_VISA($id_entidad,$id_marca, $periodo);
					break;
				case 2:
                    			if ( $totales ) {
					    $ok=$this->_genera_COOPEPLUS_TOTAL($id_entidad,$id_marca, $periodo);
                    			} else {
					    $ok=$this->_genera_COOPEPLUS($id_entidad,$id_marca, $periodo);
                    			}
					break;
				case 3:
                    			if ( $totales ) {
					    $ok=$this->_genera_BBPS_TOTAL($id_entidad,$id_marca, $periodo);
                    			} else {
					    $ok=$this->_genera_BBPS($id_entidad,$id_marca, $periodo);
                    			}
					break;
			}
               		// Grabo log de cambios
               		$id_entidad = $this->session->userdata('id_entidad');
               		$login = $this->session->userdata('username');
               		$nivel_acceso = $this->session->userdata('rango');
               		$tabla = "debtarj";
               		$operacion = 5;
               		$llave = $id_marca;
               		$observ = "Bajo archivo de $id_marca para el periodo $periodo ";
               		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
			break;
		case 'print':
			break;
	    case 'listdebitos':
		$result=$this->arma_listdebitos();
        	$datos = json_encode($result);
        	echo $datos;
		break;

            case 'gen-debtarj':
                 $this->load->model('debtarj_model');
                 $this->load->model('tarjeta_model');
                 $id_entidad = $this->session->userdata('id_entidad');
		 $data = $this->carga_data();
               	 $data['tarjetas'] = $this->tarjeta_model->get_tarjetas($id_entidad);
               	 $data['debitos_gen'] = $this->debtarj_model->get_debitos_gen($id_entidad);
               	 $mes = date('m');
               	 $anio = date('Y');
		 $a1=$anio+1;
		 $ultd = date('Ym') + 1;
		 if ( $mes == 12 ) {
               	 	$data['ult_debito'] = $a1.'01';
		 } else {
               	 	$data['ult_debito'] = $ultd;
		 }

                 if ( $this->uri->segment(4) ) {
			$data['flag'] = 1;
			$data['ult_debito'] = $this->uri->segment(5);
		 } else {
			$data['flag'] = 0;
		 }
                 $data['section'] = "gen-debtarj";
                 $this->load->view('admin',$data);
                break;
            case 'load-debtarj':
                 $this->load->model('debtarj_model');
                 $this->load->model('tarjeta_model');
                 $id_entidad = $this->session->userdata('id_entidad');
		 $data = $this->carga_data();
               	 $data['tarjetas'] = $this->tarjeta_model->get_tarjetas($id_entidad);
                 $data['section'] = "load-debtarj";
                 $this->load->view('admin',$data);
                break;
            case 'list-debtarj':
                 $this->load->model('debtarj_model');
		 $data = $this->carga_data();
                 if ( $this->uri->segment(4) ) {
                 	if ( $this->uri->segment(4) == "excel" ) {
				$result = $this->arma_listdebitos();
                                $archivo="Debitos_Tarjeta_".date('Ymd');
                                $fila1=null;
                                $titulo="DebitosTarj#".date('Ymd');
                                $headers=array();
                                $headers[]="id Debito";
                                $headers[]="SID";
                                $headers[]="DNI";
                                $headers[]="Apellido Nombre";
                                $headers[]="Ultimo Debito";
                                $headers[]="Marca";
                                $headers[]="Nro Tarjeta";
                                $headers[]="Importe";
                                $headers[]="Estado";
                                $datos=$result;
                                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
			}
		 } else {
                 	$data['section'] = "list-debtarj";
                 	$this->load->view('admin',$data);
		 }
                break;
		case 'imprimir':
               		$this->load->model('socios_model');
               		$this->load->model('debtarj_model');
		 	$data = $this->carga_data();
			$debtarj = $this->debtarj_model->get_debtarj($this->uri->segment(4));
			$data['debtarj'] = $debtarj;
               		$socio = $this->socios_model->get_socio($debtarj->sid);
               		$data['socio'] = $socio;
			$data['post'] = $this->input->post('id_marca');
               		$data['section'] = 'debtarj-print';
               		$this->load->view('admin',$data);
			break;
		case 'grabar':
                	$datos['id'] = $this->input->post('id_debito');
               		$datos['id_entidad'] = $this->session->userdata('id_entidad');
                	$datos['sid'] = $this->input->post('sid');
                	$datos['id_marca'] = $this->input->post('id_marca');
                	$datos['nro_tarjeta'] = $this->input->post('nro_tarjeta');
                	$fecha_view = $this->input->post('fecha_adhesion');
                	$datos['fecha_adhesion'] = substr($fecha_view,6,4)."-".substr($fecha_view,3,2)."-".substr($fecha_view,0,2);
                	$datos['estado'] = 1;

                	$this->load->model('debtarj_model');
                	if($datos['sid']){
                    		if ( $datos['id'] == 0 )  {
                    			// Alta
                            		$aid = $this->debtarj_model->grabar($datos);
                			// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                			$nivel_acceso = $this->session->userdata('rango');
                			$tabla = "debtarj";
                			$operacion = 1;
                			$llave = $aid;
					$observ = substr(json_encode($datos), 0, 255);
                			$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                    		} else {
                    			// Modificacion
                            		$id = $datos['id'];
                            		$this->debtarj_model->actualizar($id, $datos);
                			// Grabo log de cambios
                			$id_entidad = $this->session->userdata('id_entidad');
                			$login = $this->session->userdata('username');
                			$nivel_acceso = $this->session->userdata('rango');
                			$tabla = "debtarj";
                			$operacion = 2;
                			$llave = $id;
					$observ = substr(json_encode($datos), 0, 255);
                			$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                    		}
			}
			break;

                case 'contracargo':
			$id_entidad = $this->session->userdata('id_entidad');
                        if ( !$this->uri->segment(4) ) {
				$this->load->model('debtarj_model');
				$this->load->model('tarjeta_model');
				$data = $this->carga_data();
				$data['tarjetas'] = $this->tarjeta_model->get_tarjetas($id_entidad);
				$data['section'] = "contracargos";
				$this->load->view('admin',$data);
			} else  {
				$accion=$this->uri->segment(4);
                                switch ( $accion ) {
                                        case 'getcab':
                				$id_marca = $this->uri->segment(5);
                				$periodo = $this->uri->segment(6);
	                                        $this->load->model('debtarj_model');
                                        	$gen = $this->debtarj_model->get_periodo_marca($id_entidad,$periodo, $id_marca);
						if ($gen) { 
							$salida=json_encode($gen); 
						} else { 
							$salida=null; 
						}
						echo $salida;
						break;
                                        case 'do':
                				$id_marca = $this->uri->segment(5);
                				$periodo = $this->uri->segment(6);
                                                $fecha_debito = $this->input->post('fecha_debito');
                                                $nrotarjeta = $this->input->post('nrotarjeta');
                                                $importe = $this->input->post('importe');
                                                $this->load->model('debtarj_model');
                                                $retact = $this->debtarj_model->mete_contracargo($id_entidad,$periodo, $id_marca, $nrotarjeta, $importe);


                                                if ( $retact ) {
                					// Grabo log de cambios
                					$id_entidad = $this->session->userdata('id_entidad');
                					$login = $this->session->userdata('username');
                					$nivel_acceso = $this->session->userdata('rango');
                					$tabla = "debtarj";
                					$operacion = 2;
                					$llave = $retact['id'];
							$observ = substr(json_encode($retact), 0, 255);
                					$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                                                        redirect(base_url()."admin/debtarj/contracargo/view/".$id_marca."/".$periodo);
                                                } else {
							$data = $this->carga_data();
                                                        $data['mensaje1'] = "No se encuentra esa tarjeta importe para hacer contracargo";
                                                        $data['msj_boton'] = "Volver a contracargo manual";
                                                        $data['section'] = 'ppal-mensaje';
                                                        $this->load->view("admin",$data);
                                                }
                                                break;
                                        case 'view':
                				$id_entidad = $this->session->userdata('id_entidad');
                				$id_marca = $this->uri->segment(5);
                				$periodo = $this->uri->segment(6);
	                                        $this->load->model('debtarj_model');
                                        	$this->load->model('tarjeta_model');
                                        	$data['tarjeta'] = $this->tarjeta_model->get($id_marca);
                                        	$gen = $this->debtarj_model->get_periodo_marca($id_entidad,$periodo, $id_marca);
                                        	if ( $gen ) {
                                                	// Si es la primera vez que entro actualizo masivamente socios_debitos y actualizo cabecera socios_debitos_gen
                                                	if ( $gen->cant_acreditada == 0 ) {
                                                        	$this->debtarj_model->inicializa_contra($id_entidad, $periodo, $id_marca);
                                                	}
                                                	// Si encuentro contracargos ya realizados los traigo sino arranco con un array vacio
                                                	$contras = $this->debtarj_model->get_contracargos($id_entidad, $periodo, $id_marca);
                                                	if ( $contras ) {
                                                        	$tabla=$contras;
                                                	} else {
                                                        	$tabla=array();
                                                	}
							$data = $this->carga_data();
                                                	$data['id_marca'] = $id_marca;
                                                	$data['periodo'] = $periodo;
                                                	$data['fecha_debito'] = $gen->fecha_debito;
                                                	$data['cant_generada'] = $gen->cant_generada;
                                                	$data['total_generado'] = $gen->total_generado;
                                                	$data['tabla'] = $tabla;
                                                	$data['section'] = 'contracargos-get';
                                                	$this->load->view('admin',$data);
						} else {
							$data = $this->carga_data();
                                                        $data['mensaje1'] = "No existe ese periodo para esa marca";
                                                        $data['msj_boton'] = "Volver a contracargo manual";
                                                        $data['url_boton'] = base_url()."admin/debtarj/contracargo/";
                                                        $data['section'] = 'ppal-mensaje';
                                                        $this->load->view("admin",$data);
						}
						break;
                                }

			}
			break;
                case 'stopdebit':
                        $this->load->model('debtarj_model');
                        $this->debtarj_model->stopdebit($this->uri->segment(4));
                        $debtarj=$this->debtarj_model->get_debtarj($this->uri->segment(4));
                        $id_socio=$debtarj->sid;
                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "debtarj";
                	$operacion = 2;
                	$llave = $debtarj->id;
			$observ = substr(json_encode($debtarj), 0, 255);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
			$data = $this->carga_data();
                        $data['id_socio'] = $id_socio;
                        $data['id_debito'] = $this->uri->segment(4);
                        if ( $debtarj->estado == 0 ) {
                                $data['mensaje'] = "El debito se actualizo correctamente...";
                        } else {
                                $data['mensaje'] = "El debito no se modifico .... ERROR!!!";
                        }
                        $data['section'] = 'debtarj-mensaje';
                        $this->load->view('admin',$data);
                        break;


		case 'eliminar':
                	$this->load->model('debtarj_model');
                    	$this->debtarj_model->borrar($this->uri->segment(4));
			$debtarj=$this->debtarj_model->get_debtarj($this->uri->segment(4));
                    	$id_socio=$debtarj->sid;
                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "debtarj";
                	$operacion = 3;
                	$llave = $debtarj->id;
			$observ = substr(json_encode($debtarj), 0, 255);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
			$data = $this->carga_data();
               		$data['id_socio'] = $id_socio;
               		$data['id_debito'] = $this->uri->segment(4);
                    	if ( $debtarj->estado == 0 ) {
                        	$data['mensaje'] = "El debito de dio de baja correctamente...";
                    	} else {
                        	$data['mensaje'] = "El debito no se dio de baja.... ERROR!!!";
                    	}
               		$data['section'] = 'debtarj-mensaje';
               		$this->load->view('admin',$data);
                	break;

		default:
               		$this->load->model('socios_model');
               		$this->load->model('debtarj_model');
               		$this->load->model('tarjeta_model');
			$fecha=date('d-m-Y');
			$data = $this->carga_data();
               		$data['fecha'] = $fecha;
               		$id_entidad = $this->session->userdata('id_entidad');
               		$data['socio'] = $this->socios_model->get_socio($this->uri->segment(3));
               		$data['tarjetas'] = $this->tarjeta_model->get_tarjetas($id_entidad);
			$debtarj = $this->debtarj_model->get_debtarj_by_sid($id_entidad, $this->uri->segment(3));
			$data['debtarj'] = $debtarj;
			if ($debtarj) {
				$fdb=$debtarj->fecha_adhesion;
				$fecha_db=substr($fdb,8,2)."-".substr($fdb,5,2)."-".substr($fdb,0,4);
               			$data['fecha_db'] = $fecha_db;
			} else {
               			$data['fecha_db'] = "";
			}
               		$data['section'] = 'debtarj-edit';
               		$data['js'] = 'debtarj';
               		$this->load->view('admin',$data);
               		break;
	}
    }


    function _genera_VISA($id_entidad, $id_marca, $periodo) {
	try {
       		$this->load->model("tarjeta_model");
		$tarjeta=$this->tarjeta_model->get($id_marca);
		$nro_comercio=$tarjeta->nro_comercio_presentacion;
		$fecha=date('Ymd');
		$hora=date('Hi');

        	$this->load->model("debtarj_model");
        	$debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_entidad,$id_marca, $periodo);
        	if ( $debitos ) {
		    header('Content-Type: application/text');
		    header('Content-Disposition: attachment;filename="DEBLIQC.TXT"');
		    echo "0DEBLIQC ".$nro_comercio."900000    ".$fecha.$hora."0                                                         *\r\n";
		    $total=0;
		    $fila=1;
		    $serial="";
		    foreach ( $debitos as $debito ) {
			    $nro_tarjeta=$debito['nro_tarjeta'];
			    if ( $fila < 10 ) {
				    $serial="   0000000".$fila;
			    } elseif ( $fila < 100 ) {
				    $serial="   000000".$fila;
			    } else {
				    $serial="   00000".$fila;
			    }

			    $importe=$debito['importe'];
// Si el debito se genero en 0 no grabamos en ASCII
                            if ( $importe > 0 ) {
			         $total=$total+$importe;
			         $importe=intval($importe*100);
			         if ( $importe < 1000 ) {
				      $impo="0000000".$importe;
			         } elseif ( $importe < 10000 ) {
				      $impo="000000".$importe;
			         } elseif ( $importe < 100000 ) {
				      $impo="00000".$importe;
			         } elseif ( $importe < 1000000 ) {
				      $impo="0000".$importe;
			         } elseif ( $importe < 10000000 ) {
				      $impo="000".$importe;
			         } elseif ( $importe < 100000000 ) {
				      $impo="00".$importe;
			         }

			         $nro_soc=$debito['sid'];
			         if ( $nro_soc < 10000 ) {
				      $nro_socio="00000000000".$nro_soc;
			         } elseif ( $nro_soc < 100000 ) {
				      $nro_socio="0000000000".$nro_soc;
			         } elseif ( $nro_soc < 1000000 ) {
				      $nro_socio="000000000".$nro_soc;
			         }

			         echo "1".$nro_tarjeta.$serial.$fecha."000500000".$impo.$nro_socio."                             *\r\n";
        			 $this->debtarj_model->upd_debito_rng($debito['id_debito'], $fila);
			         $fila++;

                            }
		    }
		    $totalx=intval($total*100);
		    $largo=strlen($totalx);
		    $filler="";
		    for ($i = 1; $i < 16-$largo ; $i++) {
			    $filler=$filler."0";
    		    }
		    echo "9DEBLIQC ".$nro_comercio."900000    ".$fecha.$hora.substr($serial,4,8).$filler.$totalx."                                    *\r\n";
        }
	} catch ( Exception $ex ) {
		return false;
	}
	return true;
    }

    function _genera_COOPEPLUS($id_entidad,$id_marca, $periodo) {
        $exitoso=FALSE;
        $this->config->load("nuevacard");
        $this->load->model('debtarj_model');
        $this->load->model('socios_model');
        $nro_comercio=$this->config->item('nc_negocio');

        $cont=0;
        $total=0;
        $fecha = date('d/m/Y');
        $mes = date('m');
        $ano = date('y');

        $fl = './application/logs/nuevacard-'.date('Y').'-'.date('m').'.log';
        if( !file_exists($fl) ){
            $log = fopen($fl,'w');
        }else{
            $log = fopen($fl,'a');
        }

        $this->load->model("debtarj_model");
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_entidad, $id_marca, $periodo);

        if ( $debitos ) {
// Armo archivo de detalles
	        header('Content-Type: application/text');
                header('Content-Disposition: attachment;filename="CVMCOOP'.$periodo.'.TXT"');
                $total=0;
	        $cont=0;
                $serial="";
                foreach ( $debitos as $debito ) {
                      $nro_tarjeta=$debito['nro_tarjeta'];

                      $socio=$this->socios_model->get_socio($debito['sid']);
                      $importe=$debito['importe'];
                      if ( $importe > 0 ) {

                           $linea=$nro_comercio.",".$nro_tarjeta.",".$socio->apellido." ".$socio->nombre.",0,".$fecha.",".$importe.",DAU\r\n";
                           echo $linea;
                           $total=$total+$importe;
                           $cont++;
                       }
                 }
          }

          return true;
    }


    function _genera_BBPS($id_entidad, $id_marca, $periodo) {
        $exitoso=FALSE;
        $this->config->load("nuevacard");
        $this->load->model('debtarj_model');
        $this->load->model('socios_model');
        $nro_comercio=$this->config->item('nc_negocio_bbps');

        $cont=0;
        $total=0;
        $fecha = date('d/m/Y');
        $mes = date('m');
        $ano = date('y');

        $fl = './application/logs/bbps-'.date('Y').'-'.date('m').'.log';
        if( !file_exists($fl) ){
            $log = fopen($fl,'w');
        }else{
            $log = fopen($fl,'a');
        }

        $this->load->model("debtarj_model");
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_entidad, $id_marca, $periodo);

        if ( $debitos ) {
                // Armo archivo de detalles
                header('Content-Type: application/text');
                header('Content-Disposition: attachment;filename="CVMBBPS'.$periodo.'.TXT"');
                $total=0;
                $cont=0;
                $serial="";
                foreach ( $debitos as $debito ) {
                        $nro_tarjeta=$debito['nro_tarjeta'];

                        $socio=$this->socios_model->get_socio($debito['sid']);
                        $importe=$debito['importe'];

                        if ( $importe > 0 ) {
                             $linea=$nro_comercio.",".$nro_tarjeta.",".$socio->apellido." ".$socio->nombre.",0,".$fecha.",".$importe.",DAU\r\n";
                             echo $linea;
                             $total=$total+$importe;
                             $cont++;
                        }
                }
        }

	return true;
    }

    function _genera_COOPEPLUS_TOTAL($id_entidad, $id_marca, $periodo) {
        $exitoso=FALSE;
        $this->config->load("nuevacard");
        $this->load->model('debtarj_model');
        $this->load->model('socios_model');
        $nro_comercio=$this->config->item('nc_negocio');

        $cont=0;
        $total=0;
        $fecha = date('d/m/Y');
        $mes = date('m');
        $ano = date('y');

        $fl = './application/logs/nuevacard-'.date('Y').'-'.date('m').'.log';
        if( !file_exists($fl) ){
            $log = fopen($fl,'w');
        }else{
            $log = fopen($fl,'a');
        }

        $this->load->model("debtarj_model");
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_entidad, $id_marca, $periodo);

        if ( $debitos ) {
	        // Armo archivo de detalles
	        header('Content-Type: application/text');
	        header('Content-Disposition: attachment;filename="CVMCOOP'.$periodo.'TOT.TXT"');
            $total=0;
	        $cont=0;
            $serial="";
            foreach ( $debitos as $debito ) {
                $nro_tarjeta=$debito['nro_tarjeta'];

                $socio=$this->socios_model->get_socio($debito['sid']);
                $importe=$debito['importe'];

                $total=$total+$importe;
		        $cont++;
            }

            echo "FECHA :".$fecha."\r\n";
            echo "CANTIDAD DE REGISTROS :".$cont."\r\n";
            echo "TOTAL($) :".$total."\r\n";
        }

	    return true;
    }


    function _genera_BBPS_TOTAL($id_entidad, $id_marca, $periodo) {
        $exitoso=FALSE;
        $this->config->load("nuevacard");
        $this->load->model('debtarj_model');
        $this->load->model('socios_model');
        $nro_comercio=$this->config->item('nc_negocio_bbps');

        $cont=0;
        $total=0;
        $fecha = date('d/m/Y');
        $mes = date('m');
        $ano = date('y');

        $fl = './application/logs/bbps-'.date('Y').'-'.date('m').'.log';
        if( !file_exists($fl) ){
            $log = fopen($fl,'w');
        }else{
            $log = fopen($fl,'a');
        }

        $this->load->model("debtarj_model");
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_entidad, $id_marca, $periodo);

        if ( $debitos ) {
                // Armo archivo de detalles
                header('Content-Type: application/text');
                header('Content-Disposition: attachment;filename="CVMBBPS'.$periodo.'TOT.TXT"');
            $total=0;
                $cont=0;
            $serial="";
            foreach ( $debitos as $debito ) {
                $nro_tarjeta=$debito['nro_tarjeta'];

                $socio=$this->socios_model->get_socio($debito['sid']);
                $importe=$debito['importe'];

                $total=$total+$importe;
                        $cont++;
            }

            echo "FECHA :".$fecha."\r\n";
            echo "CANTIDAD DE REGISTROS :".$cont."\r\n";
            echo "TOTAL($) :".$total."\r\n";
        }


    	return true;
    }

    public function actividades()
    {
        switch ($this->uri->segment(3)) {
            case 'baja':
                $id_entidad = $this->session->userdata('id_entidad');
                $sid = $this->uri->segment(4);
                $aid = $this->uri->segment(5);
                $this->load->model("actividades_model");
                $act = $this->actividades_model->act_baja($id_entidad,$sid, $aid);
                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades_asociadas";
                $operacion = 3;
                $llave = $aid;
		$observ = "Di de baja la actividad $llave para el asociado $sid";
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                echo $act;
                break;

            case 'alta':
                $id_entidad = $this->session->userdata('id_entidad');
                $data['id_entidad'] = $id_entidad;
                $data['sid'] = $this->uri->segment(4);
                $data['aid'] = $this->uri->segment(5);
                $facturar = $this->uri->segment(6);
                $federado = $this->uri->segment(7);
                $data['federado'] = $federado;
                $this->load->model("actividades_model");
                $this->load->model("socios_model");
                $act = $this->actividades_model->act_alta($data);
                // Grabo log de cambios
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades_asociadas";
                $operacion = 1;
                $llave = $data['sid'];
                $nactiv = $data['aid'];
		$observ = "Relacione actividad $nactiv al socio $llave";
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                if(date('d') < $this->date_facturacion && $facturar == 'true'){ //si la fecha es anterior a la definida

                    $actividad = $this->actividades_model->get_actividad($data['aid']);
                    $this->load->model('pagos_model');

                    $socio = $this->socios_model->get_socio($data['sid']);
                    if($socio->tutor != 0){
                        $tutor_id = $socio->tutor;
                    }else{
                        $tutor_id = $data['sid'];
                    }

		    // Si la actividad tiene cuota inicial la registro primero
		    if ( $actividad->cuota_inicial > 0 ) {
                    	$descripcion = 'Cuota Inicial '.$actividad->nombre.' - $ '.$actividad->cuota_inicial;
                    	$this->pagos_model->registrar_pago($id_entidad, 'debe',$tutor_id,$actividad->cuota_inicial,'Cuota Inicial '.$actividad->nombre,$actividad->id);
		    }

                    $descripcion = 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;

                    $this->pagos_model->registrar_pago($id_entidad, 'debe',$tutor_id,$actividad->precio,'Facturacion '.$actividad->nombre,$actividad->id);
                    // Grabo log de cambios
                    $login = $this->session->userdata('username');
                    $nivel_acceso = $this->session->userdata('rango');
                    $tabla = "actividades_asociadas";
                    $operacion = 1;
                    $llave = $tutor_id;
		    $observ = "facturo actividad del mes ".$descripcion;
                    $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

		    // Si la actividad tiene seguro y no es federado de la actividad lo registro
		    if ( $actividad->seguro > 0 && $federado == 0 ) {
                    	$descripcion = 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
                    	$this->pagos_model->registrar_pago($id_entidad, 'debe',$tutor_id,$actividad->seguro,'Seguro '.$actividad->nombre,$actividad->id);
		    }

                }
                echo $act;
                break;

            case 'load-asoc-activ':
                $this->load->model('actividades_model');
		$data = $this->carga_data();
                $data['section'] = 'load-asoc-activ';
                $id_entidad = $this->session->userdata('id_entidad');
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $this->load->view("admin",$data);
                break;

            case 'subearchivo':
                $this->load->model('actividades_model');
		$id_actividad = $this->uri->segment(4);
		$fuente = $this->uri->segment(5);
		$userfile = $this->input->post('userfile');
                $id_entidad = $this->session->userdata('id_entidad');
		if ( $fuente == "txt" ) {
			$dato1col = $this->uri->segment(6);
			$data['asociados'] = $this->sube_asociados($id_entidad, $id_actividad, $dato1col);
		} else {
			$data['asociados'] = $this->actividades_model->get_socios_act($id_actividad);
		}

		if ( $data['asociados'] ) {
			// Limpio la tabla temporal
			$this->actividades_model->trunc_asoc_act();
			foreach ( $data['asociados'] as $asoc ) {
				// Inserto en la temporal de tmp_asoc_activ
				$asocact = array ( 'id_entidad' => $id_entidad,
						'sid' => $asoc['sid'],
						'aid' => $id_actividad,
						'existe_relacion' => $asoc['actividad']);
				$this->actividades_model->insert_asoc_act($asocact);
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "actividades_asociadas";
                		$operacion = 1;
                		$llave = $asoc['sid'];
				$observ = "inserto desde planilla".substr(json_encode($asocact), 0, 255);
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
			}
			$data = $this->carga_data();
                	$data['section'] = 'actividades-check';
                	$this->load->view("admin",$data);
		} else {
			$data = $this->carga_data();
			if ( $fuente == "txt" ) {
				$data['mensaje1'] = "No se pudo procesar el archivo";
			} else {
				$data['mensaje1'] = "No existen asociados en BD con esa actividad relacionada";
			}
			$data['msj_boton'] = "Volver a cargar planilla";
			$data['url_boton'] = base_url()."admin/actividades/load-asoc-activ";
                	$data['section'] = 'ppal-mensaje';
                	$this->load->view("admin",$data);
		}
                break;

            case 'alta-sin-relacionar':
                $id_entidad = $this->session->userdata('id_entidad');
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$asociados = $this->actividades_model->get_asocact_exist($id_entidad, 0);
		$asoc_relac=array();
		foreach ( $asociados as $asociado ) {
			$sid=$asociado->sid;
			$aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);

                	$data['id_entidad'] = $id_entidad;
                	$data['sid'] = $sid;
                	$data['aid'] = $aid;
                	$act = $this->actividades_model->act_alta($data);

                    	$actividad = $this->actividades_model->get_actividad($data['aid']);

                    	$descripcion = 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;
                    	if($socio->tutor != 0){
                        	$tutor_id = $socio->tutor;
                    	} else {
                        	$tutor_id = $data['sid'];
                    	}

                    	$this->pagos_model->registrar_pago($id_entidad,'debe',$tutor_id,$actividad->precio,'Facturacion '.$actividad->nombre,$actividad->id);

                    	if($socio->tutor == 0){
                        	$total = $this->pagos_model->get_socio_total($id_entidad, $data['sid']);
                    	} else {
                        	$total = $this->pagos_model->get_socio_total($id_entidad, $socio->tutor );          		      
			}

                    	$facturacion = array(
                        	'sid' => $tutor_id,
                        	'id_entidad'=>$id_entidad,
                        	'descripcion'=>$descripcion,
                        	'debe' => $actividad->precio,
                        	'haber' => 0,
                        	'total' => $total - $actividad->precio
                        	);
                    	$this->pagos_model->insert_facturacion($facturacion);
			
			if ( $activdad->seguro > 0 ) {
                    		$descripcion = 'Seguro '.$actividad->seguro.' - $ '.$actividad->seguro;
                    		$this->pagos_model->registrar_pago($id_entidad,'debe',$tutor_id,$actividad->seguro,'Seguro '.$actividad->nombre,$actividad->id);
                    		if($socio->tutor == 0){
                        		$total = $this->pagos_model->get_socio_total($id_entidad, $data['sid']);
                    		} else {
                        		$total = $this->pagos_model->get_socio_total($id_entidad, $socio->tutor );          		      
				}
                    		$facturacion = array(
                        		'sid' => $tutor_id,
                        		'id_entidad'=>$id_entidad,
                        		'descripcion'=>$descripcion,
                        		'debe' => $actividad->seguro,
                        		'haber' => 0,
                        		'total' => $total - $actividad->seguro
                        	);
                    		$this->pagos_model->insert_facturacion($facturacion);
			}

			$relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'Relacione' );
			$asoc_relac[]=$relac;
		}

		$data = $this->carga_data();
                $data['asociados'] = $asoc_relac;
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);
                break;

            case 'baja-relacionadas':
                $id_entidad = $this->session->userdata('id_entidad');
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $asociados = $this->actividades_model->get_asocact_exist($id_entidad, 1);
                $asoc_relac=array();
                foreach ( $asociados as $asociado ) {
                        $sid=$asociado->sid;
                        $aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);
                	$this->actividades_model->act_baja_asoc($id_entidad, $sid, $aid);
                	
			// Grabo log de cambios
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "actividades_asociadas";
                	$operacion = 3;
                	$llave = $sid;
                	$observ = "doy de baja masivamente actividad ".$aid." del socio ".$sid;
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);

                        $relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'Borre Relacion' );
                        $asoc_relac[]=$relac;
                }


		$data = $this->carga_data();
                $data['asociados'] = $asoc_relac;
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);
                break;

            case 'bajarel-contrafact':
                $id_entidad = $this->session->userdata('id_entidad');
/* TODO - hacer que baje la relacion y que busque lo facturado para anularlo y ajustar el pago */
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$periodo=date('Ym');
                $asociados = $this->actividades_model->get_asocact_exist($id_entidad, 1);
                $asoc_relac=array();
                foreach ( $asociados as $asociado ) {
                        $sid=$asociado->sid;
                        $aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);
                	$this->actividades_model->act_baja_asoc($id_entidad, $sid, $aid);

			// Si el socio esta activo revierto facturacion
			if ( $socio->suspendido == 0 ) {
                		$this->pagos_model->revertir_fact($id_entidad, $sid, $aid, $periodo);

                        	$relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'Borre Relacion y reverti facturacion' );
                        	$asoc_relac[]=$relac;
			} else {
                        	$relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'SOLO Borre Relacion-Estaba SUSPENDIDO' );
                        	$asoc_relac[]=$relac;
			}
                }

		$data = $this->carga_data();
                $data['asociados'] = $asoc_relac;
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);

                break;

            case 'get':
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['sid'] = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $data['actividades_asoc'] = $this->actividades_model->get_act_asoc($id_entidad, $data['sid']);
                $this->load->view('actividades-lista',$data);
                break;

            case 'pone_peso':
                $id_entidad = $this->session->userdata('id_entidad');
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_peso($id_entidad,$aid);
                // Grabo log de cambios
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades_asociadas";
                $operacion = 2;
                $llave = $aid;
		$observ = "pone_peso".substr(json_encode($act), 0, 255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                echo $act;
                break;

            case 'pone_porc':
                $id_entidad = $this->session->userdata('id_entidad');
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_porc($id_entidad,$aid);
                // Grabo log de cambios
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades_asociadas";
                $operacion = 2;
                $llave = $aid;
		$observ = "pone_porc".substr(json_encode($act), 0, 255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                echo $act;
                break;

            case 'federado':
                $id_entidad = $this->session->userdata('id_entidad');
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_federado($id_entidad,$aid);
                // Grabo log de cambios
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades_asociadas";
                $operacion = 2;
                $llave = $aid;
		$observ = "federado".substr(json_encode($act), 0, 255);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                echo $act;
                break;

            case 'asociar':
		$data = $this->carga_data();
                $data['sid'] = $this->uri->segment(4);
                $this->load->model('socios_model');
                $data['socio'] = $this->socios_model->get_socio($data['sid']);
                $data['section'] = 'actividades-asociar';
                $this->load->view("admin",$data);
                break;

            case 'agregar':
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['section'] = 'actividades-agregar';
                $this->load->model("actividades_model");
                $data['profesores'] = $this->actividades_model->get_profesores($id_entidad);
                $data['comisiones'] = $this->actividades_model->get_comisiones($id_entidad);
                $this->load->view('admin',$data);
                break;

            case 'nueva':
                foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                $id_entidad = $this->session->userdata('id_entidad');
		$datos['id_entidad'] = $id_entidad;
		
                if($datos['nombre']){
                    $this->load->model('actividades_model');
                    $aid = $this->actividades_model->reg_actividad($datos);
                    // Grabo log de cambios
                    $id_entidad = $this->session->userdata('id_entidad');
                    $login = $this->session->userdata('username');
                    $nivel_acceso = $this->session->userdata('rango');
                    $tabla = "actividades";
                    $operacion = 1;
                    $llave = $aid;
		    $observ = substr(json_encode($datos), 0, 255);
                    $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                }
                redirect(base_url()."admin/actividades");
                break;

            case 'editar':
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $this->load->model('actividades_model');
                $data['actividad'] = $this->actividades_model->get_actividad($this->uri->segment(4));
                $data['profesores'] = $this->actividades_model->get_profesores($id_entidad);
                $data['comisiones'] = $this->actividades_model->get_comisiones($id_entidad);
                $data['section'] = 'actividades-editar';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                foreach($_POST as $key => $val)
                {
                    $datos[$key] = $this->input->post($key);
                }
                $id_entidad = $this->session->userdata('id_entidad');
		$datos['id_entidad'] = $id_entidad;

                if($datos['nombre']){
                    	$this->load->model("actividades_model");
                    	$this->actividades_model->update_actividad($datos,$this->uri->segment(4));
                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "actividades";
                	$operacion = 2;
                	$llave = $this->uri->segment(4);
			$observ = substr(json_encode($datos), 0, 255);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                }
                redirect(base_url()."admin/actividades");
                break;

            case 'eliminar':
                $this->load->model("actividades_model");
                $this->actividades_model->del_actividad($this->uri->segment(4));
                // Grabo log de cambios
                $id_entidad = $this->session->userdata('id_entidad');
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "actividades";
                $operacion = 3;
                $llave = $this->uri->segment(4);
		$observ = "Borro actividad". $this->uri->segment(4);
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                redirect(base_url()."admin/actividades");
                break;

            case 'comisiones':
                $id_entidad = $this->session->userdata('id_entidad');
                if($this->uri->segment(4) == 'nuevo'){
                    	foreach($_POST as $key => $val)
                    	{
                        	$datos[$key] = $this->input->post($key);
                    	}
			$datos['id_entidad'] = $id_entidad;
                    	if($datos['descripcion'] ){
                        	$this->load->model("actividades_model");
                        	$pid = $this->actividades_model->grabar_comision($datos);
                		// Grabo log de cambios
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "comisiones";
                		$operacion = 1;
                		$llave = $pid;
				$observ = substr(json_encode($datos), 0, 255);
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        	redirect(base_url()."admin/actividades/comisiones/guardado/".$pid);
                    	}else{
                        	$data['comisiones'] = $this->actividades_model->get_comisiones($id_entidad);
                        	redirect(base_url()."admin/actividades/comisiones");
                    	}
                } else if($this->uri->segment(4) == 'guardar'){
                    	foreach($_POST as $key => $val)
                    	{
                        	$datos[$key] = $this->input->post($key);
                    	}
			$datos['id_entidad'] = $id_entidad;
                    	if($datos['descripcion']){
                        	$this->load->model("actividades_model");
                        	$this->actividades_model->actualizar_comision($datos,$this->uri->segment(5));
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "comisiones";
                		$operacion = 2;
                		$llave = $this->uri->segment(5);
				$observ = substr(json_encode($datos), 0, 255);
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        	redirect(base_url()."admin/actividades/comisiones/");
                    	}
                } else if($this->uri->segment(4) == 'editar'){
			$data = $this->carga_data();
                    	$data['section'] = 'comisiones-editar';
                    	$this->load->model('actividades_model');
                    	$data['comision'] = $this->actividades_model->get_comision($this->uri->segment(5));
                    	$this->load->view('admin',$data);
                } else if($this->uri->segment(4) == 'eliminar'){
                    	$this->load->model("actividades_model");
                    	$this->actividades_model->borrar_comision($this->uri->segment(5));
                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "comisiones";
                	$operacion = 3;
                	$llave = $this->uri->segment(5);
			$observ = "borre comision ".$this->uri->segment(5);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                    	redirect(base_url()."admin/actividades/comisiones");
                } else {
			$data = $this->carga_data();
                    	$data['section'] = 'actividades-comisiones';
                    	$this->load->model('actividades_model');
                    	$data['comisiones'] = $this->actividades_model->get_comisiones($this->session->userdata('id_entidad'));
                    	$this->load->view('admin',$data);
                }
		break;


            case 'becar':
                $id = $this->input->post('id');
                $beca = $this->input->post('beca');
                $this->load->model('actividades_model');
		$this->actividades_model->becar($id,$beca);
                break;

            default:
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['section'] = 'actividades';
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $this->load->view('admin',$data);
                break;
        }
    }

    public function pagos()
    {
        switch ($this->uri->segment(3)) {
            case 'registrar':
                switch($this->uri->segment(4)){
                    case 'do':
                        $this->load->model("pagos_model");
                	// Grabo log de cambios
                	$id_entidad = $this->session->userdata('id_entidad');
                	$login = $this->session->userdata('username');
                	$nivel_acceso = $this->session->userdata('rango');
                	$tabla = "pagos";
                	$operacion = 1;
                	$llave = $_POST['sid'];
                        $comentario = $_POST['tipo']."-".$_POST['sid']."-".$_POST['monto']."-".$_POST['des']."-".$_POST['actividad']."-".$_POST['ajuing'];
			$observ = substr($comentario, 0, 255);
                	$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        $data = $this->pagos_model->registrar_pago($id_entidad, $_POST['tipo'],$_POST['sid'],$_POST['monto'],$_POST['des'],$_POST['actividad'],$_POST['ajuing']);
                        echo $data;
                    	break;
                    case 'get':
			$sid=$this->uri->segment(5);
                        $this->load->model('socios_model');
                        $this->load->model('pagos_model');
                        $this->load->model('actividades_model');
                        $id_entidad = $this->session->userdata('id_entidad');
			$data = $this->carga_data();
                        $data['socio'] = $this->socios_model->get_socio($sid);
                        $data['facturacion'] = $this->pagos_model->get_facturacion($id_entidad,$sid);

			if ( $this->socios_model->es_tutor($id_entidad, $sid) ) {
                        	$data['activ_asoc'] = $this->actividades_model->get_act_asoc_tutor($id_entidad, $sid);
			} else {
                        	$data['activ_asoc'] = $this->actividades_model->get_act_asoc($id_entidad, $sid);
			}
                        $this->load->view('pagos-registrar-get',$data);
                    break;

                    default:
                        $id_entidad = $this->session->userdata('id_entidad');
			$data = $this->carga_data();
                        $data['section'] = 'pagos-registrar';
                        $data['sid'] = $this->uri->segment(4);
                        $this->load->model('socios_model');
                            if($data['sid']){
                                $this->load->model('pagos_model');
                                $data['cuota'] = $this->pagos_model->get_monto_socio($data['sid']);
                            }
                        $data['socio'] = $this->socios_model->get_socio($data['sid']);
                        $this->load->view('admin',$data);
                    break;
                }
                break;

            case 'facturacion':
		$data = $this->carga_data();
                $data['section'] = 'pagos-facturacion';
                $this->load->view('admin',$data);
                break;

            case 'cupon':
                switch($this->uri->segment(4)){
                    case 'imprimir':
                        $this->load->model('pagos_model');
			$data = $this->carga_data();
                        $id_entidad = $this->session->userdata('id_entidad');
                        $data['cupon'] = $this->pagos_model->get_cupon_by_id($this->uri->segment(5));
                        $this->load->view('cupon-imprimir',$data);
                        break;
                    case 'get':
                	$id_entidad = $this->session->userdata('id_entidad');
			$data = $this->carga_data();
                        $data['sid'] = $this->uri->segment(5);
                        $this->load->model('pagos_model');
                        $data['cupon'] = $this->pagos_model->get_cupon($data['sid'], $id_entidad);
                        $data['cuota'] = $this->pagos_model->get_monto_socio($data['sid']);
                        $this->load->view('pagos-cupon-get',$data);
                        break;
                    case 'generar':
                        if($_POST['id'] && $_POST['monto']){
                	    $ent_dir = $this->session->userdata('ent_directorio');
                	    $id_entidad = $this->session->userdata('id_entidad');
                            $this->load->model('socios_model');
                            $socio = $this->socios_model->get_socio($_POST['id']);
                            $cupon = $this->cuentadigital($_POST['id'],$socio->nombre.' '.$socio->apellido,$_POST['monto']);
                            if($cupon){
                                $this->load->model('pagos_model');
                                $cupon_id = $this->pagos_model->generar_cupon($id_entidad,$_POST['id'],$_POST['monto'],$cupon);
                                $data = base64_decode($cupon['image']);
                                $img = imagecreatefromstring($data);
                		// Grabo log de cambios
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "cuentadigital";
                		$operacion = 1;
                		$llave = $_POST['id'];
				$observ = "genere el cupon ".$cupon_id." con data ".$data;
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                                    if ($img !== false) {
                                        @header('Content-Type: image/png');
                                        imagepng($img,'entidades/'.$ent_dir.'/images/cupones/'.$cupon_id.'.png',0);
                                        imagedestroy($img);
                                    }
                                    else {
                                        echo 'Ocurrió un error.';
                                    }
                                echo $_POST['id'];
                            }
                        }
                        break;
                    default:
                        $id_entidad = $this->session->userdata('id_entidad');
			$data = $this->carga_data();
                        $data['section'] = 'pagos-cupon';
                        $data['sid'] = $this->uri->segment(4);
                        $this->load->model('socios_model');
                        if($data['sid']){
                            $this->load->model('pagos_model');
                            $data['cuota'] = $this->pagos_model->get_monto_socio($data['sid']);
                        }
                        $data['socio'] = $this->socios_model->get_socio($data['sid']);
                        $this->load->view('admin',$data);
                        break;
                    }
                    break;

            case 'deuda':
                $id_entidad = $this->session->userdata('id_entidad');
                switch ($this->uri->segment(4)) {
                    case 'get':
                        $this->load->model('pagos_model');
			$data = $this->carga_data();
                        $data['deuda'] = $this->pagos_model->get_deuda($this->uri->segment(5));
                        $data['planes'] = $this->pagos_model->get_planes($id_entidad, $this->uri->segment(5));
                        $this->load->view('pagos-deuda-get',$data);
                        break;

                    case 'financiar':
                        $socio = $this->input->post('sid');
                        $monto = $this->input->post('monto');
                        $cuotas = $this->input->post('cuotas');
                        $detalle = $this->input->post('detalle');
                        if($socio && $monto && $cuotas){
                            	$this->load->model('pagos_model');
                            	$this->pagos_model->financiar_deuda($id_entidad,$socio,$monto,$cuotas,$detalle);
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "financiacion";
                		$operacion = 1;
                		$llave = $socio;
				$observ = "genere financiacion socio ".$socio." en $cuotas cuotas ".$detalle;
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        }
                        break;

                    case 'cancelar_plan':
                        $id = $this->input->post('id');
                        if($id){
                            	$this->load->model('pagos_model');
                            	$this->pagos_model->cancelar_plan($id);
                		// Grabo log de cambios
                		$id_entidad = $this->session->userdata('id_entidad');
                		$login = $this->session->userdata('username');
                		$nivel_acceso = $this->session->userdata('rango');
                		$tabla = "financiacion";
                		$operacion = 3;
                		$llave = $id;
				$observ = "cancele financiacion ".$id;
                		$this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                        }
                        break;

                    default:
                    	$id_entidad = $this->session->userdata('id_entidad');
                	$id_socio = $this->uri->segment(4);
	                $this->load->model('socios_model');
                	$socio = $this->socios_model->get_socio_full($id_socio);
                        $this->load->model('pagos_model');
			// Verifico si ya esta reinscripto
                        $financiacion = $this->pagos_model->get_financiado_mensual($id_entidad, $id_socio);
			if ( $financiacion ) {
				$data = $this->carga_data();
                                $data['mensaje1'] = "Ese socio ya tiene plan de financiacion activo ";
                                $data['section'] = 'ppal-mensaje';
                                $this->load->view('admin',$data);
				break;
			} else {
                    		$id_entidad = $this->session->userdata('id_entidad');
                        	$deuda = $this->pagos_model->get_deuda_monto($id_entidad, $id_socio);
                        	if ( $deuda ) {
					if ( $deuda <= 0 ) {
						$data = $this->carga_data();
                                		$data['mensaje1'] = "Ese socio no tiene deuda....";
                                		$data['section'] = 'ppal-mensaje';
                                		$this->load->view('admin',$data);
						break;
					}
                        	} else {
					$data = $this->carga_data();
                                	$data['mensaje1'] = "Ese socio no tiene deuda....";
                                	$data['section'] = 'ppal-mensaje';
                                	$this->load->view('admin',$data);
					break;
                        	}
			}


			$data = $this->carga_data();
                        $this->load->model('socios_model');
                        $data['sid'] = $id_socio;
                        $data['socio'] = $this->socios_model->get_socio($data['sid']);
                        $data['section'] = 'pagos-deuda';
                        $this->load->view('admin',$data);
                        break;
                }
                break;

            case 'deuda-socio':
		$data = $this->carga_data();
                $data['section'] = 'pagos-deuda';
                $data['socio'] = 'socio';
                $this->load->view('admin',$data);
                break;

            case 'editar':
		$data = $this->carga_data();
                $data['section'] = 'pagos-editar';
                $data['socio'] = 'socio';
                $data['sid'] = $this->uri->segment(4);
                $this->load->model('socios_model');
                $data['socio'] = $this->socios_model->get_socio($data['sid']);
                $this->load->view('admin',$data);
                break;

            case 'get_pagos':
                $socio_id = $this->uri->segment(4);
                $this->load->model('pagos_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['pagos'] = $this->pagos_model->get_pagos_edit($id_entidad,$socio_id);
                $this->load->view('pagos-get-edit', $data, FALSE);
                break;

            case 'eliminar':
                $id_entidad = $this->session->userdata('id_entidad');
                $id = $this->uri->segment(4);
                $this->load->model('pagos_model');
                $socio_id = $this->pagos_model->eliminar_pago($id_entidad, $id);
                // Grabo log de cambios
                $login = $this->session->userdata('username');
                $nivel_acceso = $this->session->userdata('rango');
                $tabla = "pagos";
                $operacion = 3;
                $llave = $socio_id;
		$observ = "elimine pago";
                $this->log_cambios($id_entidad, $login, $nivel_acceso, $tabla, $operacion, $llave, $observ);
                redirect(base_url().'admin/pagos/editar/'.$socio_id,'refresh');
                break;

        }

    }

    public function versocio() {
        $sid=$this->uri->segment(3);
        $this->load->model('socios_model');
	$socio=$this->socios_model->get_socio($debtarj->sid);
	if ( $socio ) {
		return $socio;
	} else {	
		return null;
	}
    }

    public function reportes() {
	$funcion = $this->uri->segment(3);
	$data = $this->carga_data();
	switch ( $funcion ) {
		case 'socios':
                	$data['section'] = 'listado_imprimir';
                	$this->load->view('admin',$data);
			break;
		case 'cobros':
                	$data['section'] = 'cobros_imprimir';
                	$this->load->view('admin',$data);
			break;
		case 'exportar':
                	$data['section'] = 'export_imprimir';
                	$this->load->view('admin',$data);
			break;
		default:
                	redirect(base_url().'admin','refresh');
			
	}
    }

    public function estadisticas()
    {
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('estadisticas_model');
        $opcion=$this->uri->segment(3);
	switch ( $opcion ) {
        	case 'facturacion':
			$data = $this->carga_data();
			$data['facturacion_mensual'] = $this->estadisticas_model->facturacion_mensual($id_entidad);
			$data['facturacion_anual'] = $this->estadisticas_model->facturacion_anual($id_entidad);
			$data['section'] = 'estadisticas-facturacion';
			$this->load->view('admin',$data);
			break;
        	case 'cobranza':
			if ( $this->uri->segment(4) ) {
				$id_actividad = $this->uri->segment(4);
                		$this->load->model('actividades_model');
				$data = $this->carga_data();
                		$data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
				$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla($id_entidad,$id_actividad);
				$data['section'] = 'estadisticas-cobranza';
				$data['id_actividad'] = $id_actividad;
				$this->load->view('admin',$data);
				break;
			} else {
                		$this->load->model('actividades_model');
				$data = $this->carga_data();
                		$data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
				$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla($id_entidad);
				$data['id_actividad'] = -1;
				$data['section'] = 'estadisticas-cobranza';
				$this->load->view('admin',$data);
				break;
			}
        }
    }

    function mostrar_fecha($fecha)
    {
        $fecha = explode('-', $fecha);
        return $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
    }

    function cuentadigital($sid, $nombre, $precio, $venc=null)
    {
        $this->config->load("cuentadigital");
        $cuenta_id = $this->config->item('cd_id');
        $nombre = substr($nombre,0,40);
        $concepto  = $nombre.' ('.$sid.')';
        $repetir = true;
        $count = 0;
        $result = false;
        if(!$venc){
            $url = 'http://www.CuentaDigital.com/api.php?id='.$cuenta_id.'&codigo='.urlencode($sid).'&precio='.urlencode($precio).'&concepto='.urlencode($concepto).'&xml=1';
        }else{
            $url = 'http://www.CuentaDigital.com/api.php?id='.$cuenta_id.'&venc='.$venc.'&codigo='.urlencode($sid).'&precio='.urlencode($precio).'&concepto='.urlencode($concepto).'&xml=1';
        }

        do{
            $count++;
            $a = file_get_contents($url);
            $a = trim($a);
            $xml = simplexml_load_string($a);
            // $xml = simplexml_import_dom($xml->REQUEST);
            if (($xml->ACTION) != 'INVOICE_GENERATED') {
                $repetir = true;
                echo('Error al generarlo: ');
                sleep(1);
                //echo '<a href="'.$url.'" target="_blank"><strong>Reenviar</strong></a>';
            } else {
                $repetir = false;
                //echo('<p>El cupon de aviso se ha enviado correctamente</p>');
                $result = array();
                $result['image'] = $xml->INVOICE->BARCODEBASE64;
                $result['barcode'] = $xml->INVOICE->PAYMENTCODE1;
                //$result = $xml->INVOICE->INVOICEURL;

            }
            if ($count > 5) { $repetir = false; };

        } while ( $repetir );
            return $result;
    }

    public function envios($action='',$id='')
    {
        switch ($action) {
            case 'enviar':
                $this->load->model('general_model');
		$data = $this->carga_data();
                $data['envio'] = $this->general_model->get_envio($id);
                $data['section'] = 'envios-enviar';
                $this->load->view('admin',$data);
                break;

            case 'send':
                $this->load->model('general_model');
                $envio_info = $this->general_model->get_envio($id);
                $envio = $this->general_model->get_envio_data($id);
                if($envio){
                    $this->load->library('email');
                    $this->email->from('avisos@clubvillamitre.com', 'Club Villa Mitre');
                    $this->email->to($envio->email);
                    $this->email->subject($envio_info->titulo);
                    $this->email->message($envio_info->body);
                    $this->email->send();

                    $this->general_model->enviado($envio->id);
                    $data['estado'] = date('H:i:s').' - '.$envio->email.' <i class="fa fa-check" style="color:#1e9603"></i>';
                    $data['enviados'] = $this->general_model->get_enviados($id);
                    $data = json_encode($data);
                    echo $data;
                }else{
                    echo 'end';
                }
                break;

            case 'nuevo':
                $this->load->model('general_model');
                $this->load->model('actividades_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['categorias'] = $this->general_model->get_cats($id_entidad);
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $data['comisiones'] = $this->actividades_model->get_comisiones($id_entidad);
                $data['section'] = 'envios-nuevo';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $id_entidad = $this->session->userdata('id_entidad');
                $dir_ent = $this->session->userdata('ent_directorio');
                $this->load->model('general_model');
// AHG  mover imagen de temp a directorio attach
                $envio = array('id_entidad' => $id_entidad, 'body' => $this->input->post('text') );
                $this->general_model->update_envio($id,$envio);
                if(file_exists("images/temp/".$this->session->userdata('img_token').".jpg")){
                        rename("images/temp/".$this->session->userdata('img_token').".jpg","entidades/".$dir_ent."/emails/".$id.".jpg");
                        unlink("images/temp/".$this->session->userdata('img_token').".jpg");
                }
                break;


                $this->general_model->update_envio($id,$envio);
                break;

            case 'agregar':
                $id_entidad = $this->session->userdata('id_entidad');
                $titulo = $this->input->post('titulo');
                $grupo = $this->input->post('grupo');
                $data = $this->input->post('data');
                $activ = $this->input->post('activ');
                $envio = array(
                    'titulo' => $titulo,
                    'id_entidad' => $id_entidad,
                    'grupo' => $grupo,
                    'data' => json_encode($data),
                    'activos' => $activ
                    );
                $this->load->model('general_model');
                $id = $this->general_model->insert_envio($envio);
                $socios = $this->general_model->get_socios_by($id_entidad,$grupo,$data,$activ);
                $this->load->helper('email');
                if($socios){
                    $emails = array();
                    foreach ($socios as $socio) {
                        if (valid_email(@$socio->mail)){
                            $emails[] = $socio->mail;
                        }
                    }
                    $emails = array_unique($emails);
                    foreach ($emails as $email) {
                        $envio_data = array(
                            'eid' => $id,
                            'email' => $email
                            );
                        $this->general_model->insert_envios_data($envio_data);
                    }
                    if(count($emails) <= 0){
                        echo 'no_mails';
                    }else{
			$data = $this->carga_data();
                        $data['id'] = $id;
                        $data['body'] = false;
                        $data['titulo'] = $titulo;
                        $data['total'] = count($emails);
                        $this->load->view("envios-text",$data);
                    }
                }else{
                    echo 'no_mails';
                }
                break;

            case 'subir_imagen':
                $token = $this->img_token();
                $this->load->library('UploadHandler');
                break;

            case 'eliminar':
                $this->load->model('general_model');
                $envio = array('estado' => 0);
                $this->general_model->update_envio($id,$envio);
                redirect(base_url().'admin/envios');
                break;

            case 'editar':
                $this->load->model('general_model');
                $this->load->model('actividades_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['envio'] = $this->general_model->get_envio($id);
                $data['categorias'] = $this->general_model->get_cats($id_entidad);
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $data['profesores'] = $this->actividades_model->get_profesores($id_entidad);
                $data['section'] = 'envios-editar';
                $this->load->view('admin',$data);
                break;

            case 'edicion':
                $id_entidad = $this->session->userdata('id_entidad');
                $titulo = $this->input->post('titulo');
                $grupo = $this->input->post('grupo');
                $data = $this->input->post('data');
                $envio = array(
                    'titulo' => $titulo,
                    'id_entidad' => $id_entidad,
                    'grupo' => $grupo,
                    'data' => json_encode($data),
                    );
                $this->load->model('general_model');
                $old_envio = $this->general_model->get_envio($id);
                $this->general_model->update_envio($id,$envio);
                if($old_envio->grupo != $grupo){
                    $this->general_model->clear_envio_data($id);
                    $socios = $this->general_model->get_socios_by($id_entidad,$grupo,$data);
                    $this->load->helper('email');
                    if($socios){
                        $emails = array();
                        foreach ($socios as $socio) {
                            if (valid_email(@$socio->mail)){
                                $emails[] = $socio->mail;
                            }
                        }
                        $emails = array_unique($emails);
                        foreach ($emails as $email) {
                            $envio_data = array(
                                'eid' => $id,
                                'email' => $email
                                );
                            $this->general_model->insert_envios_data($envio_data);
                        }
                        if(count($emails) <= 0){
                            echo 'no_mails';
                        }else{
			    $data = $this->carga_data();
                            $data['id'] = $id;
                            $data['titulo'] = $titulo;
                            $data['body'] = $old_envio->body;
                            $data['total'] = count($emails);
                            $this->load->view("envios-text",$data);
                        }
                    }else{
                        echo 'no_mails';
                    }
                }else{
                    $envios_data = $this->general_model->get_envios_data($id);
		    $data = $this->carga_data();
                    $data['id'] = $id;
                    $data['titulo'] = $titulo;
                    $data['body'] = $old_envio->body;
                    $data['total'] = count($envios_data);
                    $this->load->view("envios-text",$data);
                }
                break;

            default:
                $this->load->model('general_model');
                $id_entidad = $this->session->userdata('id_entidad');
		$data = $this->carga_data();
                $data['envios'] = $this->general_model->get_envios($id_entidad);
                $data['section'] = 'envios';
                $this->load->view('admin',$data);
                break;
        }
    }
}
