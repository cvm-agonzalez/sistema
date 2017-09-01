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
		$data = $this->get_data();
		$data['reporte'] = false;
		$data['actividad_id'] = false;
		$actividades = $this->general_model->get_actividades_comision();
		if($actividades){
			if(count($actividades) == 1 ){
				foreach ($actividades as $actividad) {
					$data['reporte'] = $this->general_model->get_reporte($actividad->Id);					
					$data['actividades'] = false;
				}
			}else{
				$data['actividades'] = $actividades;
				if($this->input->post('actividad_id')){
					$data['actividad_id'] = $this->input->post('actividad_id');
					$data['reporte'] = $this->general_model->get_reporte($this->input->post('actividad_id'));					
				}
			}
		}else{
			$data['actividades'] = false;
			$data['reporte'] = false;
		}
		$this->load->view('comisiones/head', $data, FALSE);
		$this->load->view('comisiones/index', $data, FALSE);
		$this->load->view('comisiones/foot', $data, FALSE);
	}


	public function get_data()
	{
		return false;
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
                        ->setCellValue('B'.$cont, $cliente->info->Id)
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
		$data['error'] = $error;
		$this->load->view('comisiones/login',$data);
	}

	public function log($action='')
	{
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
		if( !$user = $this->login_model->log_comision($email,$pass) ){
			$this->form_validation->set_message('username_check', 'El E-Mail y/o Contraseña ingresados son incorrectos');
			return false;
		}else{
			$array = array(
				'Id' => $user->Id,
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

        $old_pwd=$_POST['old_pwd'];
        $new_pwd1=$_POST['new_pwd1'];
        $new_pwd2=$_POST['new_pwd2'];

        $user = $this->session->userdata('username');
        $mail = $this->session->userdata('email');

        if ( !$user = $this->Login_model->log_comision($mail,$old_pwd) ) {
            redirect(base_url().'comisiones/cambio_pwd/2');
        }

        if ( $new_pwd1 != $new_pwd2 ) {
            redirect(base_url().'comisiones/cambio_pwd/1');
        }

        if ( strlen($new_pwd1) < 7 ) {
            redirect(base_url().'comisiones/cambio_pwd/3');
        }

        if ( $user ) {
            $this->Login_model->upd_pwd_comision($mail, $old_pwd, $new_pwd1);
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
