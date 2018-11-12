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

    public function no_facturado($value='')
    {
        $this->db->where('socios.suspendido', 0);
        $this->db->where('socios.estado', 1);
        $this->db->where('socios.tutor', 0);
        $query = $this->db->get('socios');
        if( $query->num_rows() == 0 ){ return false; }
        $socios = $query->result();
        $no_facturados = $facturados = array();
        foreach ($socios as $socio) {
            $this->db->where('sid', $socio->Id);
            $this->db->like('date', '2016-03-01','after');
            $query = $this->db->get('facturacion');

            if( $query->num_rows() == 0 ){
                $no_facturados[] = $socio->Id;
            }else{
                $facturados[] = $socio->Id;
            }
        }
        echo '<strong>facturados</strong><br>';
        foreach ($facturados as $facturado) {
            echo $facturado.'<br>';
            $this->db->where('Id', $facturado);
            $this->db->update('socios', array('facturado'=>1));
        }
        echo '<strong>no_facturados</strong><br>';
        foreach ($no_facturados as $facturado) {
            echo $facturado.'<br>';
            $this->db->where('Id', $facturado);
            $this->db->update('socios', array('facturado'=>0));
        }
    }

    public function gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1) {
                $this->load->library('PHPExcel');
                $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                             ->setLastModifiedBy("Club Villa Mitre")
                                             ->setTitle($titulo)
                                             ->setSubject($titulo);

		$letras="A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
		$letras=$letras."AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ";
		$letras=$letras."BA,BB,BC,BD,BE,BF,BG,BH,BI,BJ,BK,BL,BM,BN,BO,BP,BQ,BR,BS,BT,BU,BV,BW,BX,BY,BZ";

		$letra=explode(",",$letras);
		$cant_col=count($headers);
		$letra_ini=$letra[0];
		$letra_fin=$letra[$cant_col];

		$str_style=$letra_ini."1:".$letra_fin."1";

                $this->phpexcel->getActiveSheet()->getStyle("$str_style")->getFill()->applyFromArray(
                    array(
                        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => 'E9E9E9'),
                    )
                );


		if ( $fila1 ) {
                	$this->phpexcel->setActiveSheetIndex(0)
                        	->setCellValue('A1', $fila1);
			$cont = 3;
			$inicio="A2";
		} else {
                	$cont = 2;
			$inicio="A1";
		}

                // agregamos información a las celdas
                $this->phpexcel->setActiveSheetIndex(0);

		$this->phpexcel->getActiveSheet()->fromArray(
        		$headers,   	// The data to set
        		NULL,        	// Array values with this value will not be set
        		"$inicio"       // Top left coordinate of the worksheet range where
                     			//    we want to set these values (default is A1)
    		);


                foreach ($datos as $dato) {
                	$this->phpexcel->setActiveSheetIndex(0);

			$this->phpexcel->getActiveSheet()->fromArray(
        			(array)$dato,   	// The data to set
        			NULL,        	// Array values with this value will not be set
        			'A'.$cont       // Top left coordinate of the worksheet range where
                     				//    we want to set these values (default is A1)
    			);
                        $cont ++;
                }
                // Renombramos la hoja de trabajo
                $this->phpexcel->getActiveSheet()->setTitle("$titulo");

                foreach(range('A',"$letra_fin") as $columnID) {
                    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                // configuramos el documento para que la hoja
                // de trabajo número 0 sera la primera en mostrarse
                // al abrir el documento
                $this->phpexcel->setActiveSheetIndex(0);

                // redireccionamos la salida al navegador del cliente (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$archivo.'.xlsx"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
                $objWriter->save('php://output');
    }

    public function arma_listdebitos() {
        	$this->load->model("debtarj_model");
        	$this->load->model("socios_model");
        	$this->load->model("tarjeta_model");
        	$debtarjs = $this->debtarj_model->get_debtarjs();
        	foreach ($debtarjs as $debtarj){
			$socio=$this->socios_model->get_socio($debtarj->sid);
			$tarjeta=$this->tarjeta_model->get($debtarj->id_marca);
			if ( $socio ) {
                      		$nombre = $socio->nombre.", ".$socio->apellido;
				$debito = $this->debtarj_model->get_debitos_by_socio($debtarj->id);
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

    public function sube_asociados($id_actividad, $dato1col) {
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
				$socio=$this->socios_model->get_socio_by_dni($col1);
			}


			if ( $socio ) {
				$sid=$socio->Id;
				$existe=0;
				$act_asoc=$this->actividades_model->get_act_asoc_puntual($sid,$id_actividad);
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

    public function sube_coopeplus($periodo,$id_marca,$fecha_debito) {
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
					$by=array("id_marca"=>$id_marca, "nro_tarjeta"=>$nro_tarjeta);
					$debtarj=$this->debtarj_model->get_debtarj_by($by);

					foreach ( $debtarj as $debtj) {
						if ( $debtj ) {
							$id_debito=$debtj->id;
							$debito=$this->debtarj_model->get_debito_by_id($id_debito, $fecha_debito);
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
		$this->debtarj_model->upd_noacred($id_marca, $periodo);
		$this->debtarj_model->upd_gen($periodo, $id_marca, $serial, $total);
		$result=true;
	}
	return $result;
    }

    public function sube_visa($periodo,$fecha_debito) {

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
				$debito=$this->debtarj_model->get_debito_rng($id_marca, $fecha_debito, $serie);

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
		$this->debtarj_model->upd_noacred($id_marca, $periodo);
		$this->debtarj_model->upd_gen($periodo, $id_marca, $serial, $total);
		$result=true;
	}
	return $result;
    }


    public function listado(){
        $this->load->model("socios_model");
        $this->load->model("pagos_model");
        $data['socios'] = $this->socios_model->listar();


        foreach ($data['socios'] as $socio){
//		var_dump($socio['cuota']);
	    $xestado = "XXXX";
	    if ( $socio['datos']->suspendido == 1 ) {
		$xestado = "SUSP";
	    } else {
		$xestado = "ACTI";
 	    }
            $datos[] = array(
            'id' => $socio['datos']->Id,
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
        $this->load->model("actividades_model");
        $data['actividades'] = $this->actividades_model->get_actividades_list();
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
            'id' => $actividad->Id,
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
		$data['token'] = $this->token();
           	$data['baseurl'] = base_url();
		$this->load->view('login-form',$data);
	}else{
            	redirect(base_url()."admin/socios");
	}
    }

    public function morosos(){
        $data['username'] = $this->session->userdata('username');
        $data['rango'] = $this->session->userdata('rango');
        $data['baseurl'] = base_url();
        $data['section'] = 'morosos';
        $this->load->view('admin',$data);
    }

	public function login()
	{

        //if($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token'))
        //{
            $this->form_validation->set_rules('username', 'nombre de usuario', 'required|trim|min_length[2]|max_length[150]|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'required|trim|min_length[5]|max_length[150]|xss_clean');
             //lanzamos mensajes de error si es que los hay
            if($this->form_validation->run() == FALSE)
            {
            	$this->session->set_flashdata('usuario_incorrecto','Falta ingresar algún dato.');
                redirect(base_url().'admin');
            }else{
                $username = $this->input->post('username');
                $password = sha1($this->input->post('password'));
                $check_user = $this->login_model->login_user($username,$password);
                if($check_user == TRUE)
                {
                    $data = array(
                    'is_logued_in'     =>         TRUE,
                    'id_usuario'     =>         $check_user->Id,
                    'rango'        =>        $check_user->rango,
                    'mail'        =>        $check_user->mail,
                    'username'         =>         $check_user->user
                    );
                    $this->session->set_userdata($data);
                    $this->login_model->update_lCon();
                    redirect(base_url().'admin');
                }
            }
        // }else{
        //     redirect(base_url().'admin');
        // }
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
        redirect(base_url().'admin');
    }

    public function admins($action='',$id='')
    {
        $this->load->model('admins_model');
        $this->login_model->update_lCon();
        $data['username'] = $this->session->userdata('username');
        $data['rango'] = $this->session->userdata('rango');
        $data['baseurl'] = base_url();
        switch ($action) {
            case 'agregar':
                $admin = $this->input->post(null, true);
                $admin['pass'] == sha1($admin['pass']);
                $id = $this->admins_model->insert_admin($admin);
                redirect(base_url().'admin/admins','refresh');
                break;

            case 'editar':
                $data['admin'] = $this->admins_model->get_admin($id);
                $data['section'] = 'admins-editar';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $admin = $this->input->post(null, true);
                if($admin['pass1'] == $admin['pass2'] && $admin['pass1'] != ''){
                    $admin['pass'] = sha1($admin['pass1']);
                }
                unset($admin['pass1']);
                unset($admin['pass2']);

                $this->admins_model->update_admin($id,$admin);
                redirect(base_url().'admin/admins','refresh');

                break;

            case 'eliminar':
                $admin = array('estado' => 0 );
                $this->admins_model->update_admin($id,$admin);
                redirect(base_url().'admin/admins','refresh');
                break;

            default:
                $data['listaAdmin'] = $this->admins_model->get_admins();
                $data['section'] = 'admins';
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
				case 'listcateg':
                			$this->load->model('general_model');
					$result=$this->general_model->get_cats();
                			$datos = json_encode($result);
                			echo $datos;
					break;
			}
		} else {
                	$data['username'] = $this->session->userdata('username');
                        $data['rango'] = $this->session->userdata('rango');
                	$data['baseurl'] = base_url();
                	$data['action'] = 'nuevo';
                	$data['section'] = 'categorias';
                	$this->load->model("general_model");
                	$data['categorias'] = $this->general_model->get_cats();
                	$this->load->view('admin',$data);
		}
		break;

            case 'get':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
                $socio = $this->socios_model->get_socio_full($id_socio);
		echo json_encode($socio);
                break;

            case 'reinscribir':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
                $socio = $this->socios_model->get_socio_full($id_socio);
                if ( $socio->categoria == 1 || $socio->categoria == 4 || $socio->categoria == 6 || $socio->categoria == 7 ) {
                        $data['mensaje1'] = "La categoria de ese socio no admite reinscripcion....";
                        $data['baseurl'] = base_url();
                        $data['section'] = 'ppal-mensaje';
                        $this->load->view('admin',$data);
                } else {
                        $this->load->model('pagos_model');
			// Verifico si ya esta reinscripto
                        $reinscripcion = $this->pagos_model->get_reinscripcion($id_socio);
			if ( $reinscripcion ) {
                                $data['mensaje1'] = "Ese socio ya se reinscribio el ".$reinscripcion->ts;
                                $data['baseurl'] = base_url();
                                $data['section'] = 'ppal-mensaje';
                                $this->load->view('admin',$data);
			} else {
                        	$deuda = $this->pagos_model->get_deuda_monto($id_socio);
                        	if ( $deuda ) {
					if ( $deuda > 0 ) {
                                		// Armar el movimiento de condonacion
						$pago_deuda=$deuda*0.50;
                        			$this->pagos_model->registrar_pago('haber',$id_socio,$pago_deuda,'Reinscripcion Mar2018',0,1);

						// Si estaba suspendido lo reactivo
						if ( $socio->suspendido == 1 ) {
							$this->socios_model->suspender($id_socio,'no');
						}

						// Verifico si tiene la cuota del mes y sino la facturo
						$cta_mes = $this->pagos_model->busca_fact_mes($id_socio);
						if ( !$cta_mes ) {
                					$this->load->model('general_model');
							$categoria = $this->general_model->get_cat($socio->categoria);
							$mes=date('Ym');
                        				$this->pagos_model->registrar_pago('debe',$id_socio,$categoria->precio,'Cuota Mes '.$mes,'cs',0);
						}

						// Grabo el registro de reinscripcion
						$this->pagos_model->reinscripcion($id_socio);
	
                                		redirect(base_url().'admin/socios/resumen/'.$id_socio);

					} else {
                                		$data['mensaje1'] = "Ese socio no tiene deuda....";
                                		$data['baseurl'] = base_url();
                                		$data['section'] = 'ppal-mensaje';
                                		$this->load->view('admin',$data);
					}
                        	} else {
                                	$data['mensaje1'] = "Ese socio no tiene deuda....";
                                	$data['baseurl'] = base_url();
                                	$data['section'] = 'ppal-mensaje';
                                	$this->load->view('admin',$data);
                        	}
			}
                }
                break;


            case 'suspender':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
		$socio = $this->socios_model->get_socio($id_socio);
		$msj = 0;
		// Verifico si ya esta suspendido
		if ( $socio->suspendido == 1 ) {
			$data['mensaje1'] = "Este socio ya esta Suspendido";
			$data['baseurl'] = base_url();
			$data['section'] = 'ppal-mensaje';
			$this->load->view('admin',$data);
		} else {
			// Verifico si tiene deuda
                	$this->load->model('pagos_model');
			$deuda = $this->pagos_model->get_deuda_monto($id_socio);
                	if ( $deuda ) {
				if ( $deuda > 0 ) {
					$msj = 1;
					$data['mensaje2'] = "Este socio tiene deuda por $ ".$deuda;
				}
			}

			// Verifico si tiene Debito Automatico
                	$this->load->model('debtarj_model');
			$debtarj = $this->debtarj_model->get_debtarj_by_sid($id_socio);
                	if ( $debtarj ) {
				$msj = 1;
				$data['mensaje3'] = "Este socio tiene Debito Automatico de Tarjeta ";
			}

                        $financiacion = $this->pagos_model->get_financiado_mensual($id_socio);
                        if ( $financiacion ) {
				$fin=$financiacion[0];
                                $msj = 1;
                                $data['mensaje4'] = "Este socio tiene un plan de financiacion con ".($fin->cuotas-$fin->actual)." cuotas pendientes";
                        }

			// Si tiene Deuda o Debito Automativo aviso
			if ($msj > 0) {
				$data['mensaje1'] = "Esta seguro de SUSPENDER a este socio ???";
				$data['baseurl'] = base_url();
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
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
                        $financiacion = $this->pagos_model->get_financiado_mensual($id_socio);
                        if ( $financiacion ) {
				$fin=$financiacion[0];
				$deuda_fin=$fin->monto-($fin->actual*($fin->monto/$fin->cuotas));
                		$this->pagos_model->registrar_pago('debe',$id_socio,$deuda_fin,'Plan Financiacion Cuotas Impagas');
                		$this->pagos_model->cancelar_plan($fin->Id);
                        }

                $this->socios_model->suspender($id_socio);
                $this->pagos_model->registrar_pago('debe',$id_socio,0.00,'Suspensión Manual desde el Sistema');
                redirect(base_url().'admin/socios/resumen/'.$id_socio);
                break;

            case 'desuspender':
                $id_socio = $this->uri->segment(4);
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$socio = $this->socios_model->get_socio($id_socio);
		// Verifico si ya esta suspendido
		if ( $socio->suspendido == 0 ) {
			$data['mensaje1'] = "Este socio NO esta Suspendido";
			$data['mensaje2'] = var_dump($socio);
			$data['baseurl'] = base_url();
			$data['section'] = 'ppal-mensaje';
			$this->load->view('admin',$data);
		} else {
                	$this->socios_model->suspender($this->uri->segment(4),'no');
                	$this->pagos_model->registrar_pago('debe',$id_socio,0.00,'Des-suspensión por Sistema');
                	redirect(base_url().'admin/socios/resumen/'.$id_socio);
		}
                break;

            case 'enviar_resumen':
                if(!$this->uri->segment(4)){return false;}
                $this->load->library('email');

                $config['charset'] = 'utf-8';
                $config['mailtype'] = 'html';

                $this->email->initialize($config);

                $this->load->model('socios_model');
                $mail = $this->socios_model->get_resumen_mail($this->uri->segment(4));

                $cuota = $mail['resumen'];

                $cuerpo = '<h3><strong>Titular:</strong> '.$cuota['titular'].'</h3>';
                $cuerpo .= '<h5><strong>Categor&iacute;a:</strong> '.$cuota['categoria'].'</h5>';

                if($cuota['categoria'] == 'Grupo Familiar'){

                    $cuerpo .= '<h5><strong>Integrantes</strong></h5><ul>';
                    foreach ($cuota['familiares'] as $familiar) {
                        $cuerpo .= '<li>'.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.'</li>';
                    }
                    $cuerpo .= '</ul>';
                }

                $cuerpo .= '<table class="table table-hover" width="50%;" border="1">
                    <thead>
                        <tr>
                            <th align="left">Descripci&oacute;n</th>
                            <th align="left">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cuota Mensual '.$cuota['categoria'].'</td>
                            <td>$'.$cuota['cuota'].'</td>
                        </tr>';
                        foreach ($cuota['actividades']['actividad'] as $actividad) {
                        $cuerpo .= '<tr>
                            <td>Cuota Mensual '.$actividad->nombre.'</td>
                            <td>$'.$actividad->precio.'</td>
                        </tr>';
                        }
                        if($cuota['familiares'] != 0){
                            foreach ($cuota['familiares'] as $familiar) {
                                foreach($familiar['actividades']['actividad'] as $actividad){

                                $cuerpo .= '<tr>
                                    <td>Cuota Mensual '.$actividad->nombre.' ['.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.' ]</td>
                                    <td>$ '.$actividad->precio.'</td>
                                </tr>';
                                }
                            }
                        }
                        if($cuota['excedente'] >= 1){

                        $cuerpo .='<tr>
                                    <td>Socio Extra (x'.$cuota['excedente'].')</td>
                                    <td>$'.$cuota['monto_excedente'].'</td>
                                </tr>';
                        }
                        if($cuota['financiacion']){
                            foreach ($cuota['financiacion'] as $plan) {

                                $cuerpo .= '<tr>
                                    <td>Financiación de Deuda ('.$plan->detalle.')</td>
                                    <td>$'.round($plan->monto/$plan->cuotas,2).'</td>
                                </tr>';

                            }
                        }
                        if($cuota['descuento'] != 0.00){
                            $cuerpo .= '<tr>
                                    <td>Descuento</td>
                                    <td>$'.$cuota['descuento'].'</td>
                                </tr>';
                        }
                        $cuerpo .= '
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>$'.$cuota['total'].'</th>
                        </tr>
                    </tfoot>
                </table>';

                // cupon
                $this->load->model('pagos_model');
                $cupon = $this->pagos_model->get_cupon($mail['sid']);
                if($cupon->monto == $cuota['total']){
                    $cupon = base_url().'images/cupones/'.$cupon->Id.'.png';
                }else{
                    $cupon = $this->cuentadigital($mail['sid'],$cuota['titular'],$cuota['total']);
                    if($cupon && $mail['sid'] != 0){

                        $cupon_id = $this->pagos_model->generar_cupon($mail['sid'],$cuota['total'],$cupon);
                        $data = base64_decode($cupon['image']);
                        $img = imagecreatefromstring($data);
                        if ($img !== false) {
                            //@header('Content-Type: image/png');
                            imagepng($img,'images/cupones/'.$cupon_id.'.png',0);
                            imagedestroy($img);
                            $cupon = base_url().'images/cupones/'.$cupon_id.'.png';
                        }else {
                            echo 'Ocurrió un error.';
                            $cupon = '';
                        }

                    }
                }

                if($cupon){
                    $cuerpo .= '<br><br><img src="'.$cupon.'">';
                }


                $cuerpo .= '';

                $total = ($mail['deuda']*-1);

                $cuerpo .= '<h3>Su deuda total con el Club es de: $ '.$total.'</h3>';

                //echo($cuerpo);
                //die;
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
                    $data['baseurl'] = base_url();
                    $data['section'] = 'socios-resumen_enviado';
                    $this->load->view('admin',$data);
                break;
            /**

            **/
            case 'buscar':
                if($_GET['dni']){
                    $this->load->model('socios_model');
                    $socio = $this->socios_model->get_socio_by(array('dni'=>$_GET['dni']));
                    if($socio){
                        redirect(base_url().'admin/socios/resumen/'.$socio[0]->Id);
                    }else{
                        redirect(base_url().'admin/socios');
                    }
                }else{
                    redirect(base_url().'admin/socios');
                }
                break;
            case 'agregar':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['action'] = 'nuevo';
                $data['section'] = 'socios-nuevo';
                $data['socio'] = '';
                $this->load->model("general_model");
                $data['categorias'] = $this->general_model->get_cats();

                $this->load->model("socios_model");
                $data['socios'] = $this->socios_model->get_socios();
                $this->load->view('admin',$data);
                break;

            case 'nuevo':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $datos = array();
                foreach($_POST as $key => $val)
                {
                    $datos[$key] = $this->input->post($key);
                }
                if($datos['socio_n'] >= 28852){
                    $datos['socio_n'] = '';
                    $error = "?e=socio_n";
                }
                $datos['r1'] = $datos['r1-id'];
                $datos['r2'] = $datos['r2-id'];
                $datos['tutor'] = $datos['r3-id'];
                unset($datos['r1-id']);
                unset($datos['r2-id']);
                unset($datos['r3-id']);
                unset($datos['r3']);
                if(isset($datos['deuda'])){
                    $deuda = $datos['deuda'];
                    unset($datos['deuda']);
                }
                $this->load->model("socios_model");

                if($prev_user = $this->socios_model->checkDNI($datos['dni'])){
                    //el dni esta repetido, incluimos la vista de listado con el usuario coincidente
                    $data['username'] = $this->session->userdata('username');
                    $data['rango'] = $this->session->userdata('rango');
                    $data['prev_user'] = $prev_user;
                    $data['baseurl'] = base_url();
                    $data['section'] = 'socio-dni-repetido';
                    $this->load->view('admin',$data);
                }else{
                    //llamamos al modelo en insertamos los datos
                    //$fecha = explode('-',$datos['nacimiento']);
                    //$datos['nacimiento'] = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
                    unset($datos['files']);
                    $uid = $this->socios_model->register($datos);
                    if(file_exists("images/temp/".$this->session->userdata('img_token').".jpg")){
                        rename("images/temp/".$this->session->userdata('img_token').".jpg","images/socios/".$uid.".jpg");
                    }
                    //guardamos la variable con la data de la foto en una imagen
                    if($deuda){
                        //llamamos a la vista de financiar deuda para este usuario con el monto ingresado
                        $this->socios_model->insert_deuda($uid,$deuda);
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

                            $facturacion = array(
                                'sid' => $uid,
                                'descripcion'=>$descripcion,
                                'debe' => $cuota['cuota'],
                                'haber' => 0,
                                'total' => $cuota['cuota']*-1
                                );
                            $this->pagos_model->insert_facturacion($facturacion);
                        }
                    }
                    redirect(base_url()."admin/socios/registrado/".$uid);

                }
                break;

            case 'nuevo-tutor':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                //if(!$this->session->userdata('username')){ redirect(base_url()."admin"); }
                $tutor['nombre'] = $this->input->get("tutor-nombre");
                $tutor['apellido'] = $this->input->get("tutor-apellido");
                $tutor['dni'] = $this->input->get("tutor-dni");
                $tutor['telefono'] = $this->input->get("tutor-telefono");
                $tutor['mail'] = $this->input->get("tutor-mail");
                $this->load->model("socios_model");
                //echo $tutor['dni']; die;
                if(!$tutor['dni'] || $prev_user = $this->socios_model->checkDNI($tutor['dni'])){
                    //el dni esta repetido, enviamos DNI para que jquery se encargue
                    echo "DNI";
                }else{
                    $uid = $this->socios_model->register($tutor);
                    $data = array("Id"=>$uid,"nombre"=>$tutor['nombre'],"apellido"=>$tutor['apellido'],"dni"=>$tutor['dni']);
                    echo (json_encode($data));
                }
                break;

            case 'agregar_imagen':
                $token = $this->img_token();
                if(move_uploaded_file($_FILES['webcam']['tmp_name'], 'images/temp/'.$token.'.jpg')){
                    echo $token;
                }
                break;

            case 'subir_imagen':

                $token = $this->img_token();
                $this->load->library('UploadHandler');
                die;
                if(move_uploaded_file($_FILES['webcam']['tmp_name'], 'images/temp/'.$token.'.jpg')){
                    echo $token;
                }
                break;

            case 'registrado':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['uid'] = $this->uri->segment(4);
                $data['section'] = 'socios-registrado';
                $this->load->view('admin',$data);
                break;

            case 'editar':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'socios-editar';
                $this->load->model("general_model");
                $data['categorias'] = $this->general_model->get_cats();
               // $data['localidad'] = $this->general_model->get_ciudades();
                $this->load->model("socios_model");
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                if($data['socio']){
                    $data['contacto1'] = $this->socios_model->get_socio($data['socio']->r1);
                    $data['contacto2'] = $this->socios_model->get_socio($data['socio']->r2);
                    $data['tutor'] = $this->socios_model->get_socio($data['socio']->tutor);
                    if(!$data['socio']->socio_n){
                        $data['socio']->socio_n = $this->uri->segment(4);
                    }
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


		if ( $datos['r3-id'] == $id ) {
			$data['mensaje1'] = "No puede ponerse como tutor al mismo socio....";
                    	$data['baseurl'] = base_url();
                    	$data['section'] = 'ppal-mensaje';
                    	$this->load->view('admin',$data);
		} else {
                	$this->load->model("socios_model");
                	$socio_n = $this->socios_model->check_u_n($datos['socio_n']);
                	if($datos['socio_n'] == 0){
				$datos['socio_n'] = '';
                	}
                	$socio_n = false;
                	if(($datos['socio_n'] >= 28852 && $datos['socio_n'] != $id )|| $socio_n == true){
                    		$datos['socio_n'] = '';
                    		$error = "?e=socio_n";
                	}
                	$datos['r1'] = $datos['r1-id'];
                	$datos['r2'] = $datos['r2-id'];
                	$datos['tutor'] = $datos['r3-id'];
                	unset($datos['r1-id']);
                	unset($datos['r2-id']);
                	unset($datos['r3-id']);
                	unset($datos['r3']);

                	if($prev_user = $this->socios_model->checkDNI($datos['dni'],$id)){
                    		//el dni esta repetido, incluimos la vista de listado con el usuario coincidente
                    		$data['username'] = $this->session->userdata('username');
                		$data['rango'] = $this->session->userdata('rango');
                    		$data['prev_user'] = $prev_user;
                    		$data['baseurl'] = base_url();
                    		$data['section'] = 'socio-dni-repetido';
                    		$this->load->view('admin',$data);
                	} else {
                    		$token = $this->session->userdata('img_token');
                    		if(file_exists("images/temp/".$token.".jpg")){
                        		rename("images/temp/".$this->session->userdata('img_token').".jpg","images/socios/".$id.".jpg");
                    		}
                    		unset($datos['files']);
                    		$this->socios_model->update_socio($id,$datos);

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
                redirect(base_url()."admin/socios");
                break;
            case 'resumen':
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                $data['facturacion'] = $this->pagos_model->get_facturacion($this->uri->segment(4));
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
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['socio'] = $this->socios_model->get_socio($this->uri->segment(4));
                $data['facturacion'] = $this->pagos_model->get_facturacion($this->uri->segment(4));
/* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
            	$array_ahg = $this->pagos_model->get_monto_socio($this->uri->segment(4));
                $data['cuota'] = $array_ahg['total'];
/* Fin Modificacion AHG */
                $data['section'] = 'socios-resumen2';
                $this->load->view('socios-resumen2',$data);
                break;
             case 'resumen-deuda':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['deuda'] = 'only';
                $data['section'] = 'socios-resumen';
                $this->load->view('admin',$data);
                break;

             case 'resumen-sindeuda':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['deuda'] = 'no';
                $data['section'] = 'socios-resumen';
                $this->load->view('admin',$data);
                break;

            case 'categorias':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'socios-categorias';
                $this->load->view('admin',$data);
                break;

            default:
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'socios';
                //$this->load->model("socios_model");
                //$data['socios'] = $this->socios_model->listar();
                $this->load->view('admin',$data);
                break;
        }
    }

    public function rifas() {
        switch ($this->uri->segment(3)) {


		case 'listado_rifas':
		        $this->load->model("rifas_model");
        		$data['rifas'] = $this->rifas_model->get_rifas();
        		foreach ($data['rifas'] as $rifa){
            		$datos[] = array (
            		'id' => $rifa->id,
            		'name' => $rifa->descripcion,
            		'sorteo' => $rifa->fecha_sorteo,
            		'cuotas' => $rifa->cuotas,
            		'price' => $rifa->valor_cuota,
            		'nums' => $rifa->cant_numeros,
            		'actividad' => $rifa->actividad_destino
            		);
        		}
        		$datos = json_encode($datos);
        		echo $datos;
			break;
		case 'eliminar':
                        $this->load->model('rifas_model');
               		$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
                        $this->rifas_model->borrar_rifas($this->uri->segment(4));
                        $data['section'] = 'rifas-mensaje';
			$data['mensaje'] = "Rifa ".$this->uri->segment(4)." borrada correctamente";
                        $this->load->view('admin',$data);
                        break;
		case 'editar':
                        $this->load->model('rifas_model');
               		$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
			$rifa = $this->rifas_model->get_rifas_by_id($this->uri->segment(4));
               		$data['rifa'] = $rifa;
                        $data['section'] = 'rifas-editar';
                        $this->load->view('admin',$data);
                        break;
		case 'editada':
                        $this->load->model('rifas_model');
               		$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $this->rifas_model->actualizar_rifas($this->uri->segment(4));
                        $data['section'] = 'rifas-editada';
                        $this->load->view('admin',$data);
                        break;
		case 'guardada';
               		$data['section'] = 'rifas-mensaje';
			$data['mensaje'] = "Rifa ". $this->uri->segment(4)." correctamente guardada";
               		$data['baseurl'] = base_url();
               		$this->load->view('admin',$data);
			break;
		case 'agregar':
                        $this->load->model('rifas_model');
               		$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
                        $data['section'] = 'rifas-nueva';
                        $this->load->view('admin',$data);
			break;
		case 'nueva':
                	$datos['id'] = 0;
                	$datos['descripcion'] = $this->input->post('nombre');
                	$datos['fecha_inicio'] = $this->input->post('fecha_inicio');
                	$datos['fecha_sorteo'] = $this->input->post('fecha_sorteo');
                	$datos['cuotas'] = $this->input->post('cuotas');
                	$datos['valor_cuota'] = $this->input->post('valor_cuota');
                	$datos['cant_numeros'] = $this->input->post('numeros');
                	$datos['actividad_destino'] = $this->input->post('actividad');
			        $datos['estado'] = 1;
               		$data['datos'] = $datos;

                	if($datos['descripcion']){
                    		$this->load->model('rifas_model');
                    		$aid = $this->rifas_model->grabar_rifas($datos);
                    		redirect(base_url()."admin/rifas/guardada/".$aid);
                	}else{
                    		redirect(base_url()."admin/rifas");
                	}
			break;


		default:
               		$this->load->model('rifas_model');
               		$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
               		$data['baseurl'] = base_url();
			$rifas = $this->rifas_model->get_rifas();
               		$data['rifas'] = $rifas;
               		$data['section'] = 'rifas-listado';
               		$this->load->view('admin',$data);
			break;
	}
    }

    public function debtarj() {
        switch ($this->uri->segment(3)) {
                case 'subearchivo':

                       $this->load->model("debtarj_model");
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
				$result = $this->debtarj_model->get_deberr_by_marca_periodo($id_marca, $periodo);
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
				case 1: $result = $this->sube_visa($periodo, $fecha_debito); break;
				case 2: $result = $this->sube_coopeplus($periodo, $id_marca,$fecha_debito); break;
				case 3: $result = $this->sube_coopeplus($periodo, $id_marca,$fecha_debito); break;
			}

                        $data['baseurl'] = base_url();
			if ( $result ) {
                                $data['mensaje1'] = "Archivo procesado correctamente";
				$data['datos_gen'] = $this->debtarj_model->get_periodo_marca($periodo, $id_marca);
				$data['debitos_error'] = $this->debtarj_model->get_deberr_by_marca_periodo($id_marca, $periodo);
                        	$data['id_marca'] = $id_marca;
                        	$data['fecha_debito'] = $fecha_debito;
                        	$data['url_boton'] = base_url()."admin/debtarj";
                        	$data['msj_boton'] = "Vuelve Listado de Debitos";
                        	$data['section'] = 'load-debtarj-result';
                        } else {
                                $data['mensaje1'] = "No se pudo procesar archivo";
                        	$data['section'] = 'ppal-mensaje';
                        }
                        $this->load->view("admin",$data);
			break;
                case 'gen_nvo':
                        $id_marca = $this->uri->segment(4);
                        $periodo = $this->uri->segment(5);
                        $this->load->model("debtarj_model");
                        $this->load->model("pagos_model");
			// Si me viene el parametro de forzar ...
			if ( $this->uri->segment(6) ) {
				$this->debtarj_model->anula_periodo_marca($periodo, $id_marca);
			} else {
			// Chequeo si el periodo esta generado y quiero volver a generarlo
				if ( $this->debtarj_model->exist_periodo_marca($periodo, $id_marca) ) {
                        		redirect(base_url()."admin/debtarj/gen-debtarj/1/".$periodo);
		        	}
			}
                        $debtarjs = $this->debtarj_model->get_debtarjs();
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
                                        	$cuota_socio = $this->pagos_model->get_monto_socio2($debtarj->sid);
                                        	// Busco el saldo del asociado
                                        	$saldo = $this->pagos_model->get_saldo($debtarj->sid);
                                        	// Si tiene saldo a favor lo descuento, sino la cuota mensual
                                        	if ( $saldo != 0 ) {
                                                	$importe = $cuota_socio['total'] + $saldo;
                                        	} else {
                                                	$importe = $cuota_socio['total'] ;
                                        	}

                                        	// Busco si tiene financiacion activa
                        			$financiacion = $this->pagos_model->get_financiado_mensual($id_socio);
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
                                                	$this->debtarj_model->insert_debito($id_debito, $fecha, $importe );

                                                	// Actualizo el ultimo periodo y fecha de generacion
                                                	$debtarj->ult_periodo_generado=$periodo;
                                                	$debtarj->ult_fecha_generacion=$fecha;
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

                 	$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                 	$data['baseurl'] = base_url();
               	 	$data['result'] = $result;

	                $data['section'] = "gen-debtarj-result";
                 	$this->load->view('admin',$data);
                        break;

		case 'baja_arch':
        		$id_marca = $this->input->post('id_marca');
        		$periodo = $this->input->post('periodo');
        		$totales = $this->input->get('tot');

			$resultado=false;
			switch ( $id_marca ) {
				case 1:
					$ok=$this->_genera_VISA($id_marca, $periodo);
					break;
				case 2:
                    if ( $totales ) {
					    $ok=$this->_genera_COOPEPLUS_TOTAL($id_marca, $periodo);
                    } else {
					    $ok=$this->_genera_COOPEPLUS($id_marca, $periodo);
                    }
					break;
				case 3:
                    if ( $totales ) {
					    $ok=$this->_genera_BBPS_TOTAL($id_marca, $periodo);
                    } else {
					    $ok=$this->_genera_BBPS($id_marca, $periodo);
                    }
					break;
			}
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
                 $data['username'] = $this->session->userdata('username');
                 $data['rango'] = $this->session->userdata('rango');
                 $data['baseurl'] = base_url();
               	 $data['tarjetas'] = $this->tarjeta_model->get_tarjetas();
               	 $data['debitos_gen'] = $this->debtarj_model->get_debitos_gen();
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
               	 $data['tarjetas'] = $this->tarjeta_model->get_tarjetas();
                 $data['username'] = $this->session->userdata('username');
                 $data['rango'] = $this->session->userdata('rango');
                 $data['baseurl'] = base_url();
                 $data['section'] = "load-debtarj";
                 $this->load->view('admin',$data);
                break;
            case 'list-debtarj':
                 $this->load->model('debtarj_model');
                 $data['username'] = $this->session->userdata('username');
                 $data['rango'] = $this->session->userdata('rango');
                 $data['baseurl'] = base_url();
                 if ( $this->uri->segment(4) ) {
                 	if ( $this->uri->segment(4) == "excel" ) {
				$result = $this->arma_listdebitos();
                                $archivo="Debitos_Tarjeta_".date('Ymd');
                                $fila1=null;
                                $titulo="DebitosTarj#".date('Ymd');
                                $headers=array();
                                $headers[]="Id Debito";
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
               		$data['baseurl'] = base_url();
               		$data['username'] = $this->session->userdata('username');
                        $data['rango'] = $this->session->userdata('rango');
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
                    		} else {
                    			// Modificacion
                            		$id = $datos['id'];
                            		$this->debtarj_model->actualizar($id, $datos);
                    		}
			}
			break;

                case 'contracargo':
                        if ( !$this->uri->segment(4) ) {
				$this->load->model('debtarj_model');
				$this->load->model('tarjeta_model');
				$data['tarjetas'] = $this->tarjeta_model->get_tarjetas();
				$data['username'] = $this->session->userdata('username');
                  		$data['rango'] = $this->session->userdata('rango');
				$data['baseurl'] = base_url();
				$data['section'] = "contracargos";
				$this->load->view('admin',$data);
			} else  {
				$accion=$this->uri->segment(4);
                                switch ( $accion ) {
                                        case 'getcab':
                				$id_marca = $this->uri->segment(5);
                				$periodo = $this->uri->segment(6);
	                                        $this->load->model('debtarj_model');
                                        	$gen = $this->debtarj_model->get_periodo_marca($periodo, $id_marca);
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
                                                $retact = $this->debtarj_model->mete_contracargo($periodo, $id_marca, $nrotarjeta, $importe);
                                                if ( $retact ) {
                                                        redirect(base_url()."admin/debtarj/contracargo/view/".$id_marca."/".$periodo);
                                                } else {
                                                        $data['baseurl'] = base_url();
                                                        $data['mensaje1'] = "No se encuentra esa tarjeta importe para hacer contracargo";
                                                        $data['msj_boton'] = "Volver a contracargo manual";
                                                        //$data['url_boton'] = base_url()."admin/debtarj/contracargo/view/".$id_marca."/".$periodo;
                                                        $data['section'] = 'ppal-mensaje';
                                                        $this->load->view("admin",$data);
                                                }
                                                break;
                                        case 'view':
                				$id_marca = $this->uri->segment(5);
                				$periodo = $this->uri->segment(6);
	                                        $this->load->model('debtarj_model');
                                        	$this->load->model('tarjeta_model');
                                        	$data['tarjeta'] = $this->tarjeta_model->get($id_marca);
                                        	$gen = $this->debtarj_model->get_periodo_marca($periodo, $id_marca);
                                        	if ( $gen ) {
                                                	// Si es la primera vez que entro actualizo masivamente socios_debitos y actualizo cabecera socios_debitos_gen
                                                	if ( $gen->cant_acreditada == 0 ) {
                                                        	$this->debtarj_model->inicializa_contra($periodo, $id_marca);
                                                	}
                                                	// Si encuentro contracargos ya realizados los traigo sino arranco con un array vacio
                                                	$contras = $this->debtarj_model->get_contracargos($periodo, $id_marca);
                                                	if ( $contras ) {
                                                        	$tabla=$contras;
                                                	} else {
                                                        	$tabla=array();
                                                	}
                                                	$data['id_marca'] = $id_marca;
                                                	$data['periodo'] = $periodo;
                                                	$data['fecha_debito'] = $gen->fecha_debito;
                                                	$data['cant_generada'] = $gen->cant_generada;
                                                	$data['total_generado'] = $gen->total_generado;
                                                	$data['tabla'] = $tabla;
                                                	$data['baseurl'] = base_url();
                                                	$data['section'] = 'contracargos-get';
                                                	$this->load->view('admin',$data);
						} else {
                                                        $data['baseurl'] = base_url();
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
                        $data['id_socio'] = $id_socio;
                        $data['id_debito'] = $this->uri->segment(4);
                        $data['baseurl'] = base_url();
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
               		$data['id_socio'] = $id_socio;
               		$data['id_debito'] = $this->uri->segment(4);
               		$data['baseurl'] = base_url();
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
               		$data['fecha'] = $fecha;
               		$data['username'] = $this->session->userdata('username');
                  	$data['rango'] = $this->session->userdata('rango');
               		$data['socio'] = $this->socios_model->get_socio($this->uri->segment(3));
               		$data['tarjetas'] = $this->tarjeta_model->get_tarjetas();
               		$data['baseurl'] = base_url();
			$debtarj = $this->debtarj_model->get_debtarj_by_sid($this->uri->segment(3));
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


    function _genera_VISA($id_marca, $periodo) {
	try {
       	$this->load->model("tarjeta_model");
		$tarjeta=$this->tarjeta_model->get($id_marca);
		$nro_comercio=$tarjeta->nro_comercio_presentacion;
		$fecha=date('Ymd');
		$hora=date('Hi');

        $this->load->model("debtarj_model");
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_marca, $periodo);
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

    function _genera_COOPEPLUS($id_marca, $periodo) {
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
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_marca, $periodo);

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


    function _genera_BBPS($id_marca, $periodo) {
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
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_marca, $periodo);

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

    function _genera_COOPEPLUS_TOTAL($id_marca, $periodo) {
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
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_marca, $periodo);

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


    function _genera_BBPS_TOTAL($id_marca, $periodo) {
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
        $debitos = $this->debtarj_model->get_debitos_by_marca_periodo($id_marca, $periodo);

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
                $sid = $this->uri->segment(4);
                $aid = $this->uri->segment(5);
                $this->load->model("actividades_model");
                $act = $this->actividades_model->act_baja($sid, $aid);
                echo $act;
                break;

            case 'alta':
                $data['sid'] = $this->uri->segment(4);
                $data['aid'] = $this->uri->segment(5);
                $facturar = $this->uri->segment(6);
                $federado = $this->uri->segment(7);
                $data['federado'] = $federado;
                $this->load->model("actividades_model");
                $this->load->model("socios_model");
                $act = $this->actividades_model->act_alta($data);
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
                    	$this->pagos_model->registrar_pago('debe',$tutor_id,$actividad->cuota_inicial,'Cuota Inicial '.$actividad->nombre,$actividad->Id);
		    }


                    $descripcion = 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;

                    $this->pagos_model->registrar_pago('debe',$tutor_id,$actividad->precio,'Facturacion '.$actividad->nombre,$actividad->Id);

		    // Si la actividad tiene seguro y no es federado de la actividad lo registro
		    if ( $actividad->seguro > 0 && $federado == 0 ) {
                    	$descripcion = 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
                    	$this->pagos_model->registrar_pago('debe',$tutor_id,$actividad->seguro,'Seguro '.$actividad->nombre,$actividad->Id);
		    }

                }
                echo $act;
                break;

            case 'load-asoc-activ':
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['baseurl'] = base_url();
                $data['section'] = 'load-asoc-activ';
                $this->load->view("admin",$data);
                break;

            case 'subearchivo':
                $this->load->model('actividades_model');

		$id_actividad = $this->uri->segment(4);
		$fuente = $this->uri->segment(5);
		$userfile = $this->input->post('userfile');

		if ( $fuente == "txt" ) {
			$dato1col = $this->uri->segment(6);
			$data['asociados'] = $this->sube_asociados($id_actividad, $dato1col);
		} else {
			$data['asociados'] = $this->actividades_model->get_socios_act($id_actividad);
		}

		if ( $data['asociados'] ) {
			// Limpio la tabla temporal
			$this->actividades_model->trunc_asoc_act();
			foreach ( $data['asociados'] as $asoc ) {
				// Inserto en la temporal de tmp_asoc_activ
				$asocact = array ( 'sid' => $asoc['sid'],
						'aid' => $id_actividad,
						'existe_relacion' => $asoc['actividad']);
				$this->actividades_model->insert_asoc_act($asocact);
			}
                	$data['baseurl'] = base_url();
                	$data['section'] = 'actividades-check';
                	$this->load->view("admin",$data);
		} else {
                	$data['baseurl'] = base_url();
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
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$asociados = $this->actividades_model->get_asocact_exist(0);
		$asoc_relac=array();
		foreach ( $asociados as $asociado ) {
			$sid=$asociado->sid;
			$aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);

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

                    	$this->pagos_model->registrar_pago('debe',$tutor_id,$actividad->precio,'Facturacion '.$actividad->nombre,$actividad->Id);

                    	if($socio->tutor == 0){
                        	$total = $this->pagos_model->get_socio_total($data['sid']);
                    	} else {
                        	$total = $this->pagos_model->get_socio_total($socio->tutor );          		      
			}

                    	$facturacion = array(
                        	'sid' => $tutor_id,
                        	'descripcion'=>$descripcion,
                        	'debe' => $actividad->precio,
                        	'haber' => 0,
                        	'total' => $total - $actividad->precio
                        	);
                    	$this->pagos_model->insert_facturacion($facturacion);
			
			if ( $activdad->seguro > 0 ) {
                    		$descripcion = 'Seguro '.$actividad->seguro.' - $ '.$actividad->seguro;
                    		$this->pagos_model->registrar_pago('debe',$tutor_id,$actividad->seguro,'Seguro '.$actividad->nombre,$actividad->Id);
                    		if($socio->tutor == 0){
                        		$total = $this->pagos_model->get_socio_total($data['sid']);
                    		} else {
                        		$total = $this->pagos_model->get_socio_total($socio->tutor );          		      
				}
                    		$facturacion = array(
                        		'sid' => $tutor_id,
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

                $data['asociados'] = $asoc_relac;
                $data['baseurl'] = base_url();
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);
                break;

            case 'baja-relacionadas':
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $asociados = $this->actividades_model->get_asocact_exist(1);
                $asoc_relac=array();
                foreach ( $asociados as $asociado ) {
                        $sid=$asociado->sid;
                        $aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);
                	$this->actividades_model->act_baja_asoc($sid, $aid);

                        $relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'Borre Relacion' );
                        $asoc_relac[]=$relac;
                }

                $data['asociados'] = $asoc_relac;
                $data['baseurl'] = base_url();
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);
                break;

            case 'bajarel-contrafact':
/* TODO - hacer que baje la relacion y que busque lo facturado para anularlo y ajustar el pago */
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $this->load->model('pagos_model');
		$periodo=date('Ym');
                $asociados = $this->actividades_model->get_asocact_exist(1);
                $asoc_relac=array();
                foreach ( $asociados as $asociado ) {
                        $sid=$asociado->sid;
                        $aid=$asociado->aid;
                        $socio=$this->socios_model->get_socio($sid);
                	$this->actividades_model->act_baja_asoc($sid, $aid);

			// Si el socio esta activo revierto facturacion
			if ( $socio->suspendido == 0 ) {
                		$this->pagos_model->revertir_fact($sid, $aid, $periodo);

                        	$relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'Borre Relacion y reverti facturacion' );
                        	$asoc_relac[]=$relac;
			} else {
                        	$relac = array ( 'sid' => $sid, 'apynom' => $socio->nombre.' '.$socio->apellido, 'dni'=>$socio->dni, 'accion' => 'SOLO Borre Relacion-Estaba SUSPENDIDO' );
                        	$asoc_relac[]=$relac;
			}
                }

                $data['asociados'] = $asoc_relac;
                $data['baseurl'] = base_url();
                $data['section'] = 'actividades-relacion';
                $this->load->view("admin",$data);

                break;

            case 'get':
                $data['sid'] = $this->uri->segment(4);
                $data['baseurl'] = base_url();
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['actividades_asoc'] = $this->actividades_model->get_act_asoc($data['sid']);
                $this->load->view('actividades-lista',$data);
                break;

            case 'pone_peso':
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_peso($aid);
                echo $act;
                break;
            case 'pone_porc':
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_porc($aid);
                echo $act;
                break;
            case 'federado':
                $aid = $this->uri->segment(4);
                $this->load->model('actividades_model');
                $act = $this->actividades_model->act_federado($aid);
                echo $act;
                break;
            case 'asociar':
                $data['sid'] = $this->uri->segment(4);
                $this->load->model('socios_model');
                $data['socio'] = $this->socios_model->get_socio($data['sid']);
                $data['section'] = 'actividades-asociar';
                $data['baseurl'] = base_url();
                $this->load->view("admin",$data);
                break;
            case 'agregar':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'actividades-agregar';
                $this->load->model("actividades_model");
                $data['profesores'] = $this->actividades_model->get_profesores();
                $data['lugares'] = $this->actividades_model->get_lugares();
                $data['comisiones'] = $this->actividades_model->get_comisiones();
                $this->load->view('admin',$data);
                break;

            case 'nueva':
                foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                if($datos['nombre']){
                    $this->load->model('actividades_model');
                    $aid = $this->actividades_model->reg_actividad($datos);
                    redirect(base_url()."admin/actividades/guardada/".$aid);
                }else{
                    redirect(base_url()."admin/actividades");
                }
                break;

            case 'guardada':
                $data['aid'] = $this->uri->segment(4);
                $data['section'] = 'actividades-guardada';
                $data['baseurl'] = base_url();
                $this->load->view("admin",$data);
                break;
            case 'editar':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $this->load->model('actividades_model');
                $data['actividad'] = $this->actividades_model->get_actividad($this->uri->segment(4));
                $data['profesores'] = $this->actividades_model->get_profesores();
                $data['lugares'] = $this->actividades_model->get_lugares();
                $data['comisiones'] = $this->actividades_model->get_comisiones();
                $data['section'] = 'actividades-editar';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                foreach($_POST as $key => $val)
                {
                    $datos[$key] = $this->input->post($key);
                }
                if($datos['nombre']){
                    $this->load->model("actividades_model");
                    $this->actividades_model->update_actividad($datos,$this->uri->segment(4));
                    redirect(base_url()."admin/actividades/guardada/".$this->uri->segment(4));
                }
                break;
            case 'eliminar':
                $this->load->model("actividades_model");
                $this->actividades_model->del_actividad($this->uri->segment(4));
                redirect(base_url()."admin/actividades");
                break;

            case 'comisiones':
                if($this->uri->segment(4) == 'nuevo'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['descripcion'] ){
                        $this->load->model("actividades_model");
                        $pid = $this->actividades_model->grabar_comision($datos);
                        redirect(base_url()."admin/actividades/comisiones/guardado/".$pid);
                    }else{
                        $data['comisiones'] = $this->actividades_model->get_comisiones();
                        redirect(base_url()."admin/actividades/comisiones");
                    }
                }else if($this->uri->segment(4) == 'guardar'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['descripcion']){
                        $this->load->model("actividades_model");
                        $this->actividades_model->actualizar_comisiones($datos,$this->uri->segment(5));
                        redirect(base_url()."admin/actividades/comisiones/guardado/".$this->uri->segment(5));
                    }
                }else if($this->uri->segment(4) == 'editar'){
                    $data['baseurl'] = base_url();
                    $data['section'] = 'comisiones-editar';
                    $this->load->model('actividades_model');
                    $data['comision'] = $this->actividades_model->get_comision($this->uri->segment(5));
                    $this->load->view('admin',$data);
                }else if($this->uri->segment(4) == 'guardado'){
                    $data['pid'] = $this->uri->segment(5);
                    $data['section'] = 'comisiones-guardado';
                    $data['baseurl'] = base_url();
                    $this->load->view("admin",$data);
                }else if($this->uri->segment(4) == 'eliminar'){
                    $this->load->model("actividades_model");
                    $this->actividades_model->borrar_comision($this->uri->segment(5));
                    redirect(base_url()."admin/actividades/comisiones");
                }else{
                    $data['username'] = $this->session->userdata('username');
                    $data['rango'] = $this->session->userdata('rango');
                    $data['baseurl'] = base_url();
                    $data['section'] = 'actividades-comisiones';
                    $this->load->model('actividades_model');
                    $data['comisiones'] = $this->actividades_model->get_comisiones();
                    $this->load->view('admin',$data);
                }
		break;
            case 'profesores':
                if($this->uri->segment(4) == 'nuevo'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['nombre'] && $datos['apellido']){
                        $this->load->model("actividades_model");
                        $pid = $this->actividades_model->reg_profesor($datos);
                        redirect(base_url()."admin/actividades/profesores/guardado/".$pid);
                    }else{
                        $data['comisiones'] = $this->actividades_model->get_comisiones();
                        redirect(base_url()."admin/actividades/profesores");
                    }
                }else if($this->uri->segment(4) == 'guardar'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['nombre'] && $datos['apellido']){
                        $this->load->model("actividades_model");
                        $this->actividades_model->update_profesor($datos,$this->uri->segment(5));
                        redirect(base_url()."admin/actividades/profesores/guardado/".$this->uri->segment(5));
                    }
                }else if($this->uri->segment(4) == 'editar'){
                    $data['baseurl'] = base_url();
                    $data['section'] = 'profesores-editar';
                    $this->load->model('actividades_model');
                    $data['profesor'] = $this->actividades_model->get_profesor($this->uri->segment(5));
                    $data['comisiones'] = $this->actividades_model->get_comisiones();
                    $this->load->view('admin',$data);
                }else if($this->uri->segment(4) == 'guardado'){
                    $data['pid'] = $this->uri->segment(5);
                    $data['section'] = 'profesores-guardado';
                    $data['baseurl'] = base_url();
                    $this->load->view("admin",$data);
                }else if($this->uri->segment(4) == 'eliminar'){
                    $this->load->model("actividades_model");
                    $this->actividades_model->del_profesor($this->uri->segment(5));
                    redirect(base_url()."admin/actividades/profesores");
                }else{
                    $data['username'] = $this->session->userdata('username');
                    $data['rango'] = $this->session->userdata('rango');
                    $data['baseurl'] = base_url();
                    $data['section'] = 'actividades-profesores';
                    $this->load->model('actividades_model');
                    $data['profesores'] = $this->actividades_model->get_profesores();
                    $data['comisiones'] = $this->actividades_model->get_comisiones();
                    $this->load->view('admin',$data);
                }
                break;

            case 'lugares':
                if($this->uri->segment(4) == 'nuevo'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['nombre']){
                        $this->load->model("actividades_model");
                        $pid = $this->actividades_model->reg_lugar($datos);
                        redirect(base_url()."admin/actividades/lugares/guardado/".$pid);
                    }else{
                        redirect(base_url()."admin/actividades/lugares");
                    }
                }else if($this->uri->segment(4) == 'guardar'){
                    foreach($_POST as $key => $val)
                    {
                        $datos[$key] = $this->input->post($key);
                    }
                    if($datos['nombre']){
                        $this->load->model("actividades_model");
                        $this->actividades_model->update_lugar($datos,$this->uri->segment(5));
                        redirect(base_url()."admin/actividades/lugares/guardado/".$this->uri->segment(5));
                    }
                }else if($this->uri->segment(4) == 'editar'){
                    $data['baseurl'] = base_url();
                    $data['section'] = 'lugares-editar';
                    $this->load->model('actividades_model');
                    $data['lugar'] = $this->actividades_model->get_lugar($this->uri->segment(5));
                    $this->load->view('admin',$data);
                }else if($this->uri->segment(4) == 'guardado'){
                    $data['pid'] = $this->uri->segment(5);
                    $data['section'] = 'lugares-guardado';
                    $data['baseurl'] = base_url();
                    $this->load->view("admin",$data);
                }else if($this->uri->segment(4) == 'eliminar'){
                    $this->load->model("actividades_model");
                    $this->actividades_model->del_lugar($this->uri->segment(5));
                    redirect(base_url()."admin/actividades/lugares");
                }else{
                    $data['username'] = $this->session->userdata('username');
                    $data['rango'] = $this->session->userdata('rango');
                    $data['baseurl'] = base_url();
                    $data['section'] = 'actividades-lugares';
                    $this->load->model('actividades_model');
                    $data['lugares'] = $this->actividades_model->get_lugares();
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
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'actividades';
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades();
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
                        $data = $this->pagos_model->registrar_pago($_POST['tipo'],$_POST['sid'],$_POST['monto'],$_POST['des'],$_POST['actividad'],$_POST['ajuing']);
                        echo $data;
                    break;
                    case 'get':
			$sid=$this->uri->segment(5);
                        $this->load->model('socios_model');
                        $this->load->model('pagos_model');
                        $this->load->model('actividades_model');
                        $data['username'] = $this->session->userdata('username');
                        $data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
                        $data['socio'] = $this->socios_model->get_socio($sid);
                        $data['facturacion'] = $this->pagos_model->get_facturacion($sid);

			if ( $this->socios_model->es_tutor($sid) ) {
                        	$data['activ_asoc'] = $this->actividades_model->get_act_asoc_tutor($sid);
			} else {
                        	$data['activ_asoc'] = $this->actividades_model->get_act_asoc($sid);
			}
                        $this->load->view('pagos-registrar-get',$data);
                    break;

                    default:
                        $data['username'] = $this->session->userdata('username');
                        $data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
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
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'pagos-facturacion';
                $this->load->view('admin',$data);
                break;

            case 'cupon':
                switch($this->uri->segment(4)){
                    case 'imprimir':
                        $this->load->model('pagos_model');
                        $data['baseurl'] = base_url();
                        $data['cupon'] = $this->pagos_model->get_cupon_by_id($this->uri->segment(5));
                        $this->load->view('cupon-imprimir',$data);
                        break;
                    case 'get':
                        $data['sid'] = $this->uri->segment(5);
                        $this->load->model('pagos_model');
                        $data['baseurl'] = base_url();
                        $data['cupon'] = $this->pagos_model->get_cupon($data['sid']);
                        $data['cuota'] = $this->pagos_model->get_monto_socio($data['sid']);
                        $this->load->view('pagos-cupon-get',$data);
                        break;
                    case 'generar':
                        if($_POST['id'] && $_POST['monto']){
                            $this->load->model('socios_model');
                            $socio = $this->socios_model->get_socio($_POST['id']);
                            $cupon = $this->cuentadigital($_POST['id'],$socio->nombre.' '.$socio->apellido,$_POST['monto']);
                            if($cupon){
                                $this->load->model('pagos_model');
                                $cupon_id = $this->pagos_model->generar_cupon($_POST['id'],$_POST['monto'],$cupon);
                                $data = base64_decode($cupon['image']);
                                $img = imagecreatefromstring($data);
                                    if ($img !== false) {
                                        @header('Content-Type: image/png');
                                        imagepng($img,'images/cupones/'.$cupon_id.'.png',0);
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
                        $data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
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
                switch ($this->uri->segment(4)) {
                    case 'get':
                        $this->load->model('pagos_model');
                        $data['deuda'] = $this->pagos_model->get_deuda($this->uri->segment(5));
                        $data['planes'] = $this->pagos_model->get_planes($this->uri->segment(5));
                        $this->load->view('pagos-deuda-get',$data);
                        break;

                    case 'financiar':
                        $socio = $this->input->post('sid');
                        $monto = $this->input->post('monto');
                        $cuotas = $this->input->post('cuotas');
                        $detalle = $this->input->post('detalle');
                        if($socio && $monto && $cuotas){
                            $this->load->model('pagos_model');
                            $this->pagos_model->financiar_deuda($socio,$monto,$cuotas,$detalle);
                        }
                        break;

                    case 'cancelar_plan':
                        $id = $this->input->post('id');
                        if($id){
                            $this->load->model('pagos_model');
                            $this->pagos_model->cancelar_plan($id);
                        }
                        break;

                    default:

                	$id_socio = $this->uri->segment(4);
	                $this->load->model('socios_model');
                	$socio = $this->socios_model->get_socio_full($id_socio);
                        $this->load->model('pagos_model');
			// Verifico si ya esta reinscripto
                        $financiacion = $this->pagos_model->get_financiado_mensual($id_socio);
			if ( $financiacion ) {
                                $data['mensaje1'] = "Ese socio ya tiene plan de financiacion activo ";
                                $data['baseurl'] = base_url();
                                $data['section'] = 'ppal-mensaje';
                                $this->load->view('admin',$data);
				break;
			} else {
                        	$deuda = $this->pagos_model->get_deuda_monto($id_socio);
                        	if ( $deuda ) {
					if ( $deuda <= 0 ) {
                                		$data['mensaje1'] = "Ese socio no tiene deuda....";
                                		$data['baseurl'] = base_url();
                                		$data['section'] = 'ppal-mensaje';
                                		$this->load->view('admin',$data);
						break;
					}
                        	} else {
                                	$data['mensaje1'] = "Ese socio no tiene deuda....";
                                	$data['baseurl'] = base_url();
                                	$data['section'] = 'ppal-mensaje';
                                	$this->load->view('admin',$data);
					break;
                        	}
			}


                        $data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
                        $data['baseurl'] = base_url();
                        $this->load->model('socios_model');
                        $data['sid'] = $id_socio;
                        $data['socio'] = $this->socios_model->get_socio($data['sid']);
                        $data['section'] = 'pagos-deuda';
                        $this->load->view('admin',$data);
                        break;
                }
                break;
            case 'deuda-socio':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'pagos-deuda';
                $data['socio'] = 'socio';
                $this->load->view('admin',$data);
                break;

            case 'editar':
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
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
                $data['pagos'] = $this->pagos_model->get_pagos_edit($socio_id);
                $this->load->view('pagos-get-edit', $data, FALSE);
                break;

            case 'eliminar':
                $id = $this->uri->segment(4);
                $this->load->model('pagos_model');
                $socio_id = $this->pagos_model->eliminar_pago($id);
                redirect(base_url().'admin/pagos/editar/'.$socio_id,'refresh');
                break;

            default:
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'listado_imprimir';
/*
                $this->load->model('pagos_model');
                $comision = null;
                $actividad = null;
                $data['morosos'] = $this->pagos_model->get_morosos($comision, $actividad);
                $this->load->model('actividades_model');
                $data['comision_sel'] = $comision;
                $data['actividad_sel'] = $actividad;
                $data['comisiones'] = $this->actividades_model->get_comisiones();
                $data['actividades'] = $this->actividades_model->get_actividades();
*/
                $this->load->view('admin',$data);
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

    public function estadisticas()
    {
        $this->load->model('estadisticas_model');
        $opcion=$this->uri->segment(3);
	switch ( $opcion ) {
        	case 'facturacion':
			$data['username'] = $this->session->userdata('username');
                	$data['rango'] = $this->session->userdata('rango');
			$data['baseurl'] = base_url();
			$data['facturacion_mensual'] = $this->estadisticas_model->facturacion_mensual();
			$data['facturacion_anual'] = $this->estadisticas_model->facturacion_anual();
			$data['section'] = 'estadisticas-facturacion';
			$this->load->view('admin',$data);
			break;
        	case 'cobranza':
			if ( $this->uri->segment(4) ) {
				$id_actividad = $this->uri->segment(4);
                		$this->load->model('actividades_model');
                		$data['actividades'] = $this->actividades_model->get_actividades();
				$data['username'] = $this->session->userdata('username');
                		$data['rango'] = $this->session->userdata('rango');
				$data['baseurl'] = base_url();
				$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla($id_actividad);
				$data['section'] = 'estadisticas-cobranza';
				$data['id_actividad'] = $id_actividad;
				$this->load->view('admin',$data);
				break;
			} else {
                		$this->load->model('actividades_model');
                		$data['actividades'] = $this->actividades_model->get_actividades();
				$data['username'] = $this->session->userdata('username');
                		$data['rango'] = $this->session->userdata('rango');
				$data['baseurl'] = base_url();
				$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla();
				$data['id_actividad'] = -1;
				$data['section'] = 'estadisticas-cobranza';
				$this->load->view('admin',$data);
				break;
			}
        	case 'ingresos':
			break;
        }
    }
    public function configuracion()
    {
        switch($this->uri->segment(3)){
            case 'categorias':
                $precios = $this->input->post('precios');
                $fam = $this->input->post('fam');
                $this->load->model('general_model');
                $this->general_model->save_cat_config($precios,$fam);
                break;

            case 'guardar':
                $this->load->model('general_model');
                $interes_mora = $this->input->post('interes_mora');
                $config = array('interes_mora' => $interes_mora );
                $this->general_model->update_config($config);
                redirect(base_url().'admin/configuracion/guardada');
                break;

            default:
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'configuracion';
                $this->load->model("general_model");
                $data['config'] = $this->general_model->get_config();
                $data['cats'] = $this->general_model->get_cats();
                $this->load->view('admin',$data);
                break;
        }
    }
    public function soporte()
    {
        if($this->uri->segment(3) == 'enviar' && $_POST['consulta']){
            $this->load->library('email');

            $this->email->from($this->session->userdata('mail'), $this->session->userdata('username'));
            $this->email->to('cvm.agonzalez@gmail.com');

            $this->email->subject('Solicitud de Soporte: CVM');
            $this->email->message($_POST['consulta']);

            $this->email->send();

            $data['username'] = $this->session->userdata('username');
            $data['rango'] = $this->session->userdata('rango');
            $data['baseurl'] = base_url();
            $data['section'] = 'soporte-enviado';
            $this->load->view('admin',$data);
        }else{
            $data['username'] = $this->session->userdata('username');
            $data['rango'] = $this->session->userdata('rango');
            $data['baseurl'] = base_url();
            $data['section'] = 'soporte';
            $this->load->view('admin',$data);
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
                $data['envio'] = $this->general_model->get_envio($id);
                $data['username'] = $this->session->userdata('username');
            $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
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

                    //echo $this->email->print_debugger();

                    $this->general_model->enviado($envio->Id);
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
                $data['categorias'] = $this->general_model->get_cats();
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['comisiones'] = $this->actividades_model->get_comisiones();
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'envios-nuevo';
                $this->load->view('admin',$data);
                break;

            case 'guardar':
                $this->load->model('general_model');
                $envio = array('body' => $this->input->post('text') );
                $this->general_model->update_envio($id,$envio);
                break;

            case 'agregar':
                $titulo = $this->input->post('titulo');
                $grupo = $this->input->post('grupo');
                $data = $this->input->post('data');
                $activ = $this->input->post('activ');
                $envio = array(
                    'titulo' => $titulo,
                    'grupo' => $grupo,
                    'data' => json_encode($data),
                    'activos' => $activ
                    );
                $this->load->model('general_model');
                $id = $this->general_model->insert_envio($envio);
                $socios = $this->general_model->get_socios_by($grupo,$data,$activ);
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

            case 'eliminar':
                $this->load->model('general_model');
                $envio = array('estado' => 0);
                $this->general_model->update_envio($id,$envio);
                redirect(base_url().'admin/envios');
                break;

            case 'editar':
                $this->load->model('general_model');
                $this->load->model('actividades_model');
                $data['envio'] = $this->general_model->get_envio($id);
                $data['categorias'] = $this->general_model->get_cats();
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['profesores'] = $this->actividades_model->get_profesores();
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'envios-editar';
                $this->load->view('admin',$data);
                break;

            case 'edicion':
                $titulo = $this->input->post('titulo');
                $grupo = $this->input->post('grupo');
                $data = $this->input->post('data');
                $envio = array(
                    'titulo' => $titulo,
                    'grupo' => $grupo,
                    'data' => json_encode($data),
                    );
                $this->load->model('general_model');
                $old_envio = $this->general_model->get_envio($id);
                $this->general_model->update_envio($id,$envio);
                if($old_envio->grupo != $grupo){
                    $this->general_model->clear_envio_data($id);
                    $socios = $this->general_model->get_socios_by($grupo,$data);
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
                    $data['id'] = $id;
                    $data['titulo'] = $titulo;
                    $data['body'] = $old_envio->body;
                    $data['total'] = count($envios_data);
                    $this->load->view("envios-text",$data);
                }
                break;

            default:
                $this->load->model('general_model');
                $data['envios'] = $this->general_model->get_envios();
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();
                $data['section'] = 'envios';
                $this->load->view('admin',$data);
                break;
        }
    }
}
