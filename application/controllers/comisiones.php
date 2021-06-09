<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comisiones extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		if($this->uri->segment(2) != 'log' && $this->uri->segment(2) != 'login'){
			if( !$this->session->userdata('c_logged') ){
				redirect(base_url().'comisiones/login');
			}
		}
		$this->load->database();
		$this->load->model('general_model');
	}

	public function index()
	{
                $data = array();
                $data['ent_nombre'] = $this->session->userdata('ent_nombre');
                $data['ent_directorio'] = $this->session->userdata('ent_directorio');
                $data['ent_abreviatura'] = $this->session->userdata('abreviatura');
                $data['email_reply'] = $this->session->userdata('email_sistema');
                $data['id_entidad'] = $this->session->userdata('id_entidad');
                $data['grupo'] = $this->session->userdata('grupo');
                $data['username'] = $this->session->userdata('username');
                $data['rango'] = $this->session->userdata('rango');
                $data['baseurl'] = base_url();

		$id_entidad = $this->session->userdata('id_entidad');
                $id_comision = $this->session->userdata('id_comision');
		$actividades = $this->general_model->get_actividades_comision($id_entidad);
		if($actividades){
			$data['actividades'] = $actividades;
		}else{
			$data['actividades'] = false;
		}
		$data['section'] = 'principal';

		$this->load->model('comisiones_model');

		$comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);
		$data['nombre_comision'] = $comision->descripcion;
                $dia=date('d');
                if ( $dia < 20 ) {
                        $mes=date('m');
                        if ( $mes > 1 ) {
                                $periodo=date('Ym')-1;
                        } else {
                                $periodo=(date('Y')-1).'12';
                        } 
                } else {
                        $periodo=date('Ym');
                }
                $anio_corte=2017;

                $data['resumen1'] = $this->comisiones_model->resumen($id_entidad, $comision->id, $periodo, $anio_corte, 0);
                $data['resumen2'] = $this->comisiones_model->resumen($id_entidad, $comision->id, $periodo, $anio_corte, 1);

		$this->load->view('comisiones/index', $data, FALSE);

	}

	public function lista_socios_act()
	{
		$id_entidad = $this->session->userdata('id_entidad');
                $accion=$this->uri->segment(3);
                $id_actividad = $this->uri->segment(4);
                $id_estado = $this->uri->segment(5);
                if ( $id_estado != 0 ) { 
                        $data['estado'] = $id_estado;
                } else {
                        $data['estado'] = -1;
                }

                $this->load->model('general_model');
                $this->load->model('comisiones_model');
		$this->load->model('actividades_model');
                $entidad = $this->general_model->get_ent_dir($id_entidad);
                $ent_directorio = $entidad->dir_name;
                $data['ent_nombre'] = $entidad->descripcion;
                $data['ent_directorio'] = $ent_directorio;
                $data['baseurl'] = base_url();
                $data['username'] = $this->session->userdata('username');
                $id_comision = $this->session->userdata('id_comision');
                $comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);
                $data['actividades'] = $this->general_model->get_actividades_comision($id_entidad);


                if ( $id_actividad == 0 ) {
                        $data['id_actividad'] = 0;
                        $data['socioact_tabla'] = $this->actividades_model->get_socactiv($id_entidad, -1, $id_comision, 0, $data['estado']);
                } else {
                        $data['id_actividad'] = $id_actividad;
                        $data['socioact_tabla'] = $this->actividades_model->get_socactiv($id_entidad, $id_actividad, 0, 0, $data['estado']);
                }

		switch ( $accion ) {
			case 'excel':
				foreach ( $data['socioact_tabla'] as $socio ) {
					$socact = array(
                                		'Actividad' => $socio->aid."-".$socio->descr_act,
                                		'sid' => $socio->id,
                                		'Apellido y Nombre' => $socio->apellido.", ".$socio->nombre,
                                		'DNI' => $socio->dni,
                                		'domicilio' => $socio->domicilio,
                                		'email' => $socio->mail,
                                		'estado' => $socio->suspendido,
                                                'deuda_cs' => $socio->mora_cs,
                                                'deuda_act' => $socio->mora_act,
                                                'deuda_seg' => $socio->mora_seg,
                                		'ult_pago' => $socio->ult_pago,
                                		);
					$result[]=$socact;

				}
                                $archivo="Socios_Actividad_".date('Ymd');
                                $fila1=null;
                                $titulo="Socios_Actividad#".date('Ymd');
                                $headers=array();
                                $headers[]="Actividad";
                                $headers[]="SID";
                                $headers[]="Apellido Nombre";
                                $headers[]="DNI";
                                $headers[]="Domicilio";
                                $headers[]="Email";
                                $headers[]="Estado";
                                $headers[]="Deuda Cuota Social";
                                $headers[]="Deuda Actividad";
                                $headers[]="Deuda Seguro";
                                $headers[]="Ult Pago";
                                $datos=$result;
                                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
				break;

			case 'view':
                		$data['nombre_comision'] = $comision->descripcion;
                		$data['mora'] = 0;
                		$data['section'] = 'lista_socios_act';
                		$this->load->view('comisiones/index', $data, FALSE);
				break;
		}
	}

	public function lista_morosos()
	{
		$id_entidad = $this->session->userdata('id_entidad');
                $accion=$this->uri->segment(3);
                $id_actividad = $this->uri->segment(4);
                $id_estado = $this->uri->segment(5);
                if ( $id_estado != 0 ) {
                        $data['estado'] = $id_estado;
                } else {
                        $data['estado'] = -1;
                }

                $this->load->model('general_model');
                $this->load->model('comisiones_model');
		$this->load->model('actividades_model');
                $entidad = $this->general_model->get_ent_Dir($id_entidad);
                $data['ent_nombre'] = $entidad->descripcion;
                $data['ent_directorio'] = $entidad->dir_name;
                $data['baseurl'] = base_url();
                $data['username'] = $this->session->userdata('username');
                $id_comision = $this->session->userdata('id_comision');
                $comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);

                $data['actividades'] = $this->general_model->get_actividades_comision($id_entidad);
                if ( $id_actividad == 0 ) {
                        $data['id_actividad'] = 0;
                        $data['socioact_tabla'] = $this->actividades_model->get_socactiv($id_entidad, -1, $id_comision, 1, $data['estado']);
                } else {
                        $data['id_actividad'] = $id_actividad;
                        $data['socioact_tabla'] = $this->actividades_model->get_socactiv($id_entidad, $id_actividad, 0, 1, $data['estado']);
                }

		switch ( $accion ) {
			case 'excel':
				foreach ( $data['socioact_tabla'] as $socio ) {
					$socact = array(
                                		'Actividad' => $socio->aid."-".$socio->descr_act,
                                		'sid' => $socio->id,
                                		'Apellido y Nombre' => $socio->apellido.", ".$socio->nombre,
                                		'DNI' => $socio->dni,
                                		'domicilio' => $socio->domicilio,
                                		'email' => $socio->mail,
                                		'estado' => $socio->suspendido,
                                                'deuda_cs' => $socio->mora_cs,
                                                'deuda_act' => $socio->mora_act,
                                                'deuda_seg' => $socio->mora_seg,
                                		'ult_pago' => $socio->ult_pago,
                                		);
					$result[]=$socact;

				}
                                $archivo="Socios_Mora_Actividad_".date('Ymd');
                                $fila1=null;
                                $titulo="Socios_Mora_Actividad#".date('Ymd');
                                $headers=array();
                                $headers[]="Actividad";
                                $headers[]="SID";
                                $headers[]="Apellido Nombre";
                                $headers[]="DNI";
                                $headers[]="Domicilio";
                                $headers[]="Email";
                                $headers[]="Estado";
                                $headers[]="Deuda Cuota Social";
                                $headers[]="Deuda Actividad";
                                $headers[]="Deuda Seguro";
                                $headers[]="Ult Pago";
                                $datos=$result;
                                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
				break;

			case 'view':
                		$data['nombre_comision'] = $comision->descripcion;
                		$data['mora'] = 1;
                		$data['section'] = 'lista_socios_act';
                		$this->load->view('comisiones/index', $data, FALSE);
				break;
		}
	}

	public function facturacion()
	{
		$id_entidad = $this->session->userdata('id_entidad');
                $accion=$this->uri->segment(3);
                $id_actividad = $this->uri->segment(4);

		$this->load->model('estadisticas_model');
		$this->load->model('general_model');
		$this->load->model('comisiones_model');
		$this->load->model('actividades_model');
		$entidad = $this->general_model->get_ent_dir($id_entidad);
		$ent_directorio = $entidad->dir_name;
		$data['ent_nombre'] = $entidad->descripcion;
		$data['ent_directorio'] = $ent_directorio;
	        $data['baseurl'] = base_url();
		$data['username'] = $this->session->userdata('username');
                $id_comision = $this->session->userdata('id_comision');
                $comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);
		$id_comision = $comision->id;
		$data['actividades'] = $this->general_model->get_actividades_comision($id_entidad);
        	if ( $id_actividad == 0 ) {
			$data['id_actividad'] = 0;
			$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla($id_entidad, -1, $id_comision);
			$xact="Todas";
		} else {
			$data['id_actividad'] = $id_actividad;
			$data['cobranza_tabla'] = $this->estadisticas_model->cobranza_tabla($id_entidad, $id_actividad, 0);
			$xact=$this->actividades_model->get_actividad($id_actividad)->nombre;
		}

                switch ( $accion ) {
                        case 'excel':
                                foreach ( $data['cobranza_tabla'] as $mes ) {
                                        $socact = array(
                                                'Periodo' => $mes->periodo,
                                                'Actividad' => $xact,
                                                'Socios' => $mes->socios,
                                                'Cuotas' => $mes->cuotas,
                                                'Facturado' => $mes->facturado,
                                                'Cobrado Mes' => $mes->pagado_mes,
                                                'Efectividad' => $mes->porc_cobranza,
                                                'Cobrado Atrasado' => $mes->pagado_mora,
                                                '% Mora' => $mes->porc_mora,
                                                'Pago Parcial' => $mes->pago_parcial,
                                                'Impago' => $mes->impago,
                                                '% Impago' => $mes->porc_impago
                                                );
                                        $result[]=$socact;

                                }
                                $archivo="Socios_Actividad_".date('Ymd');
                                $fila1=null;
                                $titulo="Socios_Actividad#".date('Ymd');
                                $headers=array();
                                $headers[]="Periodo";
                                $headers[]="Actividad";
                                $headers[]="Socios";
                                $headers[]="Cuotas";
                                $headers[]="Facturado";
                                $headers[]="Cobrado Mes";
                                $headers[]="% Efectividad";
                                $headers[]="Cobrado Atrasado";
                                $headers[]="% Mora";
                                $headers[]="Pago Parcial";
                                $headers[]="Impago";
                                $headers[]="% Impago";
                                $datos=$result;
                                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
                                break;

                        case 'view':
				$data['nombre_comision'] = $comision->descripcion;
				$data['section'] = 'facturacion';
				$this->load->view('comisiones/index', $data, FALSE);
                                break;
                }


	}

	public function resumen() 
	{
		$id_entidad = $this->session->userdata('id_entidad');
		$id_socio=$this->uri->segment(3);
		$accion=$this->uri->segment(4);

		$this->load->model('general_model');
		$this->load->model('socios_model');
		$this->load->model('pagos_model');
		$entidad = $this->general_model->get_ent_dir($id_entidad);
		$data['ent_nombre'] = $entidad->descripcion;
		$data['ent_directorio'] = $entidad->dir_name;
		$data['username'] = $this->session->userdata('username');
		$data['baseurl'] = base_url();
		$data['socio'] = $this->socios_model->get_socio($id_socio);
		$data['facturacion'] = $this->pagos_model->get_facturacion($id_entidad, $id_socio);
		$data['cuota'] = $this->pagos_model->get_monto_socio($id_socio);
                if ( $accion ) {
                        if ( $accion == "excel" ) {
                                $archivo="Resument_Cuenta_Asoc_".$id_socio."_".date('Ymd');
                                $fila1="ID#".$id_socio."-".trim($data['socio']->apellido).", ".trim($data['socio']->nombre);
                                $titulo="ID#".$id_socio;
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
                $this->load->view('comisiones/index',$data);
	}

	public function liquidacion_mes()
	{
		$id_entidad = $this->session->userdata('id_entidad');
		$this->load->model('comisiones_model');
	        $data['baseurl'] = base_url();
		$data['id_entidad'] = $id_entidad;
		$data['section'] = 'liquidacion_mes';
                $id_comision = $this->session->userdata('id_comision');
                $comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);
		$data['nombre_comision'] = $comision->descripcion;
		$this->load->view('comisiones/index', $data, FALSE);
	}

	public function liquidaciones_ant()
	{
		$id_entidad = $this->session->userdata('id_entidad');
		$this->load->model('comisiones_model');
	        $data['baseurl'] = base_url();
		$data['id_entidad'] = $id_entidad;
		$data['section'] = 'liquidaciones_ant';
                $id_comision = $this->session->userdata('id_comision');
                $comision = $this->comisiones_model->get_comision($id_entidad, $id_comision);
		$data['nombre_comision'] = $comision->descripcion;
		$this->load->view('comisiones/index', $data, FALSE);
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

	public function excel($id=''){
        
        $this->load->model('actividades_model');
        $this->load->model('general_model');

        $actividad = $this->actividades_model->get_actividad($id);        
        $clientes = $this->general_model->get_reporte($id);

        $titulo = "CVM - ".$actividad->nombre." - ".date('d-m-Y');
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:F1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         
        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Apellido/Nombre')
                    ->setCellValue('B1', 'Socio #')
                    ->setCellValue('C1', 'DNI')
                    ->setCellValue('D1', 'Fecha de Nacimiento')               
                    ->setCellValue('E1', 'Deuda')        
                    ->setCellValue('F1', 'Último Pago');                    
        
        $cont = 2;
        foreach ($clientes->socios as $cliente) {        	
			if($cliente->deuda >= 0){
				$deuda = "Socio sin Deuda";
			}else{				
				$deuda = '$'.number_format($cliente->deuda*-1,2);
			}
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->info->apellido.' '.$cliente->info->nombre)
                        ->setCellValue('B'.$cont, $cliente->info->id)
                        ->setCellValue('C'.$cont, $cliente->info->dni)
                        ->setCellValue('D'.$cont, date('d/m/Y',strtotime($cliente->info->nacimiento)))
                        ->setCellValue('E'.$cont, $deuda)
                        ->setCellValue('F'.$cont, $cliente->ultimo_pago);                        
                        $cont ++;
        } 
        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Clientes');
         
        foreach(range('A','G') as $columnID) {
            $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        // configuramos el documento para que la hoja
        // de trabajo número 0 sera la primera en mostrarse
        // al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);
         
         
        // redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$titulo.'.xlsx"');
        header('Cache-Control: max-age=0');
         
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');
         
    
    // end: setExcel
    }
  //////////////////////
 /// LOGIN/LOGOUT /////
//////////////////////

	public function login($error=false)
	{
		if ( $this->session->userdata('id_entidad') ) {
			$data['id_entidad'] = $this->session->userdata('id_entidad');
			$data['ent_nombre'] = $this->session->userdata('ent_nombre');
		}
		$data['error'] = $error;
		$this->load->view('comisiones/login',$data);
	}

	public function log($action='')
	{
		if ( !$this->session->userdata('id_entidad') ) {
			$id_entidad = $this->input->post('entidad');
		} else {
			$id_entidad = $this->session->userdata('id_entidad');
		}

		$this->load->model('login_model');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'E-Mail', 'required|valid_email');
		$this->form_validation->set_rules('pass', 'Contraseña', 'required|callback_username_check['.$this->input->post('email').']');

		if ($this->form_validation->run() == FALSE){
			$error = validation_errors();
			$this->login($error);
		}else{				
			redirect(base_url().'comisiones');				
		}
	}

	public function username_check($pass,$email)
	{		
		if ( !$this->session->userdata('id_entidad') ) {
			$id_entidad = $this->input->post('entidad');
		} else {
			$id_entidad = $this->session->userdata('id_entidad');
		}
		if( !$user = $this->login_model->log_comision($id_entidad,$email,$pass) ){
			$this->form_validation->set_message('username_check', 'El E-Mail y/o Contraseña ingresados son incorrectos');
			return false;
		}else{
			$array = array(
				'id' => $user->id,
				'id_entidad' => $user->id_entidad,
				'id_comision' => $user->comision,
				'email' => $user->email,
				'nombre'=>	$user->nombre,				
				'apellido'=>	$user->apellido,				
				'c_logged'=>'ok'
			);			
			$this->session->set_userdata( $array );			
			return true;
		}
	}

    public function cambio_pwd()
    {

        if ( $this->uri->segment(3) ) {
            $data['flag'] = 1;
            switch ( $this->uri->segment(3) ) {
                case 1: $data['mensaje'] = "Diferencia entre las dos nuevas contraseñas"; break;
                case 2: $data['mensaje'] = "Ingreso mal la vieja contraseña"; break;
                case 3: $data['mensaje'] = "La nueva contraseña tiene que tener al menos 6 caracteres"; break;
            }
        } else {
            $data['flag'] = 0;
        }

        $this->load->view('comisiones/cambio_pwd', $data);
    }

    public function upd_pwd()
    {
        $this->load->model('Login_model');
	$id_entidad = $this->session->userdata('id_entidad');

        $old_pwd=$_POST['old_pwd'];
        $new_pwd1=$_POST['new_pwd1'];
        $new_pwd2=$_POST['new_pwd2'];

        $user = $this->session->userdata('username');
        $mail = $this->session->userdata('email');

        if ( !$user = $this->Login_model->log_comision($id_entidad,$mail,$old_pwd) ) {
            redirect(base_url().'comisiones/cambio_pwd/2');
        }

        if ( $new_pwd1 != $new_pwd2 ) {
            redirect(base_url().'comisiones/cambio_pwd/1');
        }

        if ( strlen($new_pwd1) < 7 ) {
            redirect(base_url().'comisiones/cambio_pwd/3');
        }

        if ( $user ) {
            $this->Login_model->upd_pwd_comision($id_entidad,$mail, $old_pwd, $new_pwd1);
            $this->logout();
            return true;
        }
   

    }



	public function logout($value='')
	{
		$this->session->sess_destroy();	
		redirect(base_url().'comisiones');	
	}

}

/* End of file comisiones.php */
/* Location: ./application/controllers/comisiones.php */
