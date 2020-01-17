<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Imprimir extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));        
        if(!$this->session->userdata('is_logued_in')){          
            redirect(base_url().'admin');
        }              
    }
    function index()
    {
        $data['listado'] = false;
        $this->load->view('imprimir/foot',$data);
    }

        private function gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1) {
	        $ent_abrev = $this->session->userdata('ent_abreviatura');
        	$ent_nombre = $this->session->userdata('ent_ent_nombre');

                $this->load->library('PHPExcel');
                $this->phpexcel->getProperties()->setCreator($ent_nombre)
                                             ->setLastModifiedBy($ent_nombre)
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

    public function exportar($action='')
    {
        $id_entidad = $this->session->userdata('id_entidad');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $data['listado'] = false;
        switch ($action) {
            case 'socios':
                $this->load->model('socios_model');                
                $this->load->model('general_model');                
                $this->load->model('pagos_model');                
                $clientes = $this->socios_model->get_socios($id_entidad);
                    
                $titulo = $ent_abrev." - Socios - ".date('d-m-Y');
		$fila1 = false;

		$archivo="Listado de Socios"."_".date('Ymd');
		$headers=array();
		$headers[]='#';
		$headers[]='Apellido';
		$headers[]='Nombre';
		$headers[]='DNI';
		$headers[]='Domicilio';
		$headers[]='Localidad';
		$headers[]='Nacionalidad';
		$headers[]='Fecha de Nacimiento';
		$headers[]='Teléfono';
		$headers[]='Email';
		$headers[]='Celular';
		$headers[]='Tutor de grupo Familiar';
		$headers[]='Categoría de Socio';
		$headers[]='Descuento';
		$headers[]='Fecha de Ingreso';
		$headers[]='Estado';
		$headers[]='Observaciones';
		$headers[]='Saldo en Cuenta Corriente';

		$datos=$clientes;
		$this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
                break;

            case 'actividades':
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $actividades = $this->actividades_model->get_act_asoc_all($id_entidad);
        	$ent_abrev = $this->session->userdata('ent_abreviatura');
        	$ent_nombre = $this->session->userdata('ent_ent_nombre');
                    
                $titulo = $ent_abrev." - Actividades - ".date('d-m-Y');
		$fila1 = false;

                $archivo="Listado de Actividades"."_".date('Ymd');
                $headers=array();
                $headers[]='Socio #';
                $headers[]='Apellido';
                $headers[]='Nombre';
                $headers[]='Actividad #';
                $headers[]='Descripcion Actividad';
                $headers[]='Descuento';

                $datos=$actividades;
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);

                break;

            case 'cuenta_corriente':
                $this->load->model('pagos_model');                
                $facturaciones = $this->pagos_model->get_facturacion_all($id_entidad);
                    
        	$ent_abrev = $this->session->userdata('ent_abreviatura');
        	$ent_nombre = $this->session->userdata('ent_ent_nombre');

                $titulo = $ent_abrev." - Cuentas Corrientes - ".date('d-m-Y');
		$fila1 = false;
                
                $archivo="Listado de Cuenta Corriente"."_".date('Ymd');
                $headers=array();
                $headers[]='Socio #';
                $headers[]='Facturacion #';
                $headers[]='Fecha';
                $headers[]='Descripcion';
                $headers[]='Tipo (D/H)';
                $headers[]='Importe';

                $datos=$actividades;
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);

                break;
            
            default:                
                $this->load->view('imprimir/index_exportar',$data);
                $this->load->view('imprimir/exportar',$data);
                $this->load->view('imprimir/foot');
                break;
        }
        $this->load->view('imprimir/foot');
    }

    public function listado($listado,$id=false)
    {
        $id_entidad = $this->session->userdata('id_entidad');
        $data['listado'] = $listado;
        $this->load->view('imprimir/index_listado',$data);
        switch ($listado) {
            case 'actividades':
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $data['render_cat'] = false;
                if($id){ $data['render_cat'] = $id; }                
                $this->load->view('imprimir/actividades',$data);
                break;

            case 'profesores':
                $this->load->model('actividades_model');
                $data['profesores'] = $this->actividades_model->get_profesores($id_entidad);
                $this->load->view('imprimir/profesores',$data);
                break;

            case 'usuarios_suspendidos':
                $this->load->model('pagos_model');
                $data['socios'] = $this->pagos_model->get_usuarios_suspendidos($id_entidad);
                $this->load->view('imprimir/usuarios_suspendidos',$data);
                break;

            case 'socios':
                $this->load->view('imprimir/socios',$data);
                break;

            case 'categorias':
                $this->load->model('general_model');
                $data['actividades'] = $this->general_model->get_cats($id_entidad);                
                $this->load->view('imprimir/categorias',$data);
                break;

            case 'morosos':                
                $data['baseurl'] = base_url();                
                $this->load->model('pagos_model');
                $comision = $this->input->post('comisiones');
                $actividad = $this->input->post('morosos_activ'); 
                if($comision || $actividad){                           
                    	$data['morosos'] = $this->pagos_model->get_morosos($id_entidad, $comision, $actividad);
                }else{
                    $data['morosos'] = false;
                }
                $this->load->model('actividades_model');
                $data['actividad_sel'] = $actividad;
                $data['comision_sel'] = $comision;
                $data['comisiones'] = $this->actividades_model->get_comisiones($id_entidad);
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $this->load->view('imprimir/morosos',$data);
                break;

             case 'becas':
                $this->load->model('actividades_model');
                $this->load->model('pagos_model');
                $actividad = false;                
                $actividad = $this->uri->segment(4);
                $data['socios'] = false;
                if($actividad){
                    $data['socios'] = $this->pagos_model->get_becas($id_entidad,$actividad);
                }
                $data['a_actual'] = $actividad;
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $this->load->view('imprimir/becas',$data);             
                break;
            
            case 'sin_actividades':
                $this->load->model('pagos_model');
                $data['socios'] = $this->pagos_model->get_sin_actividades($id_entidad);
                $this->load->view('imprimir/sin_actividades',$data);
                break;

            default:

                break;
        }
        $this->load->view('imprimir/foot');
    }

    public function cobros($action='',$fecha1=false,$fecha2=false)
    {        
        $id_entidad = $this->session->userdata('id_entidad');
        $data = array();
        $this->load->model('pagos_model');
        $this->load->view('imprimir/index_cobros',$data);
        switch ($action) {
            case 'ingresos':
                $data['ingresos'] = false;
                if($fecha1 && $fecha2){
                    $data['ingresos'] = $this->pagos_model->get_ingresos($id_entidad,$fecha1,$fecha2);
                }
                $data['fecha1'] = $fecha1;
                $data['fecha2'] = $fecha2;
                $this->load->view('imprimir/ingresos', $data, FALSE);
                break;

            case 'actividades':
                $data['cobros'] = false;
                $data['actividad_s'] = false;
                $fechas = $this->input->post('daterange');
                $actividad = $this->input->post('actividad');                
                $categoria = $this->input->post('categoria'); 
                $data['categoria'] = $categoria;

                if($fechas){
                    $fecha = explode(' - ', $fechas);
                    $fecha1 = $fecha[0];
                    $fecha2 = $fecha[1];
                }
                if($fecha1 && $fecha2){
                    if($actividad != '-1'){
                        $data['cobros'] = $this->pagos_model->get_cobros_actividad($id_entidad, $fecha1,$fecha2,$actividad,$categoria);
                        $data['actividad_s'] = $actividad;                        
                    }else{
                        $data['cobros'] = $this->pagos_model->get_cobros_cuota($id_entidad, $fecha1,$fecha2,$categoria);
                        $data['actividad_s'] = '-1';
                    }
                }                
                $data['fecha1'] = $fecha1;
                $data['fecha2'] = $fecha2;
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $data['actividad_info'] = $this->actividades_model->get_actividad($actividad);
                $this->load->view('imprimir/actividades-cobros', $data, FALSE);
                break;

            case 'anterior':
                $this->load->model('actividades_model');
                $id = $this->uri->segment(4);
                $data['actividad'] = new STDClass();
                $data['actividad']->id = false;
                if($id){
                    $data['socios'] = $this->pagos_model->get_pagos_actividad_anterior($id_entidad, $id);
                    $data['actividad'] = $this->actividades_model->get_actividad($id);
                }else{
                    $data['socios'] = false;                    
                }
                $data['actividades'] = $this->actividades_model->get_actividades($id_entidad);
                $this->load->view('imprimir/anterior',$data);
                break;

            case 'cuentadigital':
                $data['ingresos'] = false;
                if($fecha1 && $fecha2){
                    $data['ingresos'] = $this->pagos_model->get_ingresos_cuentadigital($id_entidad, $fecha1,$fecha2);
                }
                $data['fecha1'] = $fecha1;
                $data['fecha2'] = $fecha2;
                $this->load->view('imprimir/cuentadigital', $data, FALSE);
                break;
            
            default:
                # code...
                break;
        }
        $this->load->view('imprimir/foot');

    }

    function generar($listado,$id){
        $id_entidad = $this->session->userdata('id_entidad');
        switch ($listado) {           
            case 'actividades':
                $this->load->model('pagos_model');
                $this->load->model('actividades_model');
                $data['actividad'] = $this->actividades_model->get_actividad($id);
                $data['socios'] = $this->pagos_model->get_pagos_actividad($id_entidad, $id);
                $this->load->view('imprimir/actividades_listado',$data);
                break;

            case 'profesores':
                $this->load->model('pagos_model');
                $this->load->model('actividades_model');
                $data['profesor'] = $this->actividades_model->get_profesor($id);
                $data['socios'] = $this->pagos_model->get_pagos_profesor($id_entidad, $id);
                $this->load->view('imprimir/profesores_listado',$data);
                break;

            case 'socios':
                $this->load->model('pagos_model');
                if($id == 'activos'){
                    $data['id'] = $id;
                    $data['titulo'] = "Socios Activos";
                    $data['socios'] = $socios = $this->pagos_model->get_socios_activos($id_entidad);
                    foreach ($socios as $socio) {
                        $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad, $socio->id);
                        /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
			$array_ahg = $this->pagos_model->get_monto_socio($socio->id);
                        $socio->cuota = $array_ahg['total'];
                        /* Fin Modificacion AHG */
                    }
                    $this->load->view('imprimir/socios_listado',$data); 
                }else if($id == 'suspendidos'){
                    $data['id'] = $id;
                    $data['titulo'] = "Socios Suspendidos";                    
                    $data['socios'] = $socios = $this->pagos_model->get_usuarios_suspendidos($id_entidad);
                    foreach ($socios as $socio) {
                        $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad,$socio->id);
                        /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
			            $array_ahg = $this->pagos_model->get_monto_socio($socio->id);
                        $socio->cuota = $array_ahg['total'];
                        /* Fin Modificacion AHG */
                    }
                    $this->load->view('imprimir/socios_listado',$data); 
                }                
                break;

            case 'categorias':
                $this->load->model('pagos_model');
                $this->load->model('general_model');
                $data['categoria'] = $this->general_model->get_cat($id);               
                $data['socios'] = $this->pagos_model->get_pagos_categorias($id_entidad, $id);
                $this->load->view('imprimir/categorias_listado',$data);
                break;

            case 'socios':

                break;
            
            default:

                break;
        }
    }


    public function actividades($id=false){
        if(!$id){ return false; }
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $data['actividad'] = $this->actividades_model->get_actividad($id);
        $data['profesor'] = $this->actividades_model->get_profesor($data['actividad']->profesor);
        $data['socios'] = $this->pagos_model->get_pagos_actividad($id_entidad, $id);
        $this->load->view('imprimir/actividades_listado',$data);    
    }

    public function carnet(){
        $this->load->model('socios_model');        
        $this->load->model('pagos_model');        
        $id = $this->uri->segment(3) ?: null;        
        if(!$id){die;}
        $id_entidad = $this->session->userdata('id_entidad');
        $socio = $data['socio'] = $this->socios_model->get_socio($id);
        $data['cupon'] = $this->pagos_model->get_cupon($id, $id_entidad);
        $monto = $this->pagos_model->get_monto_socio($id);
        $data['monto'] = $monto = $monto['total'];
/* Comentado para probar sin cuentadigital
        if($data['cupon']->monto == 0 || $data['cupon']->monto != $monto){
            //$this->load->model('socios_model');
            //$socio = $this->socios_model->get_socio($_POST['id']);
            $cupon = $this->cuentadigital($id,$socio->nombre.' '.$socio->apellido,$monto);
            if($cupon){                                    
                $this->load->model('pagos_model');
                $cupon_id = $this->pagos_model->generar_cupon($id_entidad,$id,$monto,$cupon);
                $data = base64_decode($cupon['image']);
                $img = imagecreatefromstring($data);
                    if ($img !== false) {
                        @header('Content-Type: image/png');
                	$ent_nombre = $this->session->userdata('ent_nombre');
			$ent_directorio = $this->session->userdata('ent_directorio');
			imagepng($img,base_url().'entidades/'.$ent_directorio.'/images/cupones/'.$cupon_id.'.png',0);
                        imagedestroy($img);
                        redirect(base_url().'imprimir/carnet/'.$id);

                    }
                    else {
                        echo 'Ocurrió un error.';
                    }                
            }
        $data['cupon'] = $this->pagos_model->get_cupon($id, $id_entidad);
        }
*/
        $fmto = $this->uri->segment(4);
	if ( !$fmto ) {
        	$this->load->view('imprimir-carnet',$data);
	} else {
        	$this->load->view('imprimir-carnet2',$data);
	}
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

    public function socios_excel($type=''){
		$id_entidad = $this->session->userdata('id_entidad');
		$this->load->model('pagos_model'); 
		$ent_abrev = $this->session->userdata('ent_abreviatura');
		$ent_nombre = $this->session->userdata('ent_ent_nombre');
		if($type=='suspendidos'){    
			$clientes = $this->pagos_model->get_usuarios_suspendidos($id_entidad);
			$titulo = $ent_abrev." - Socios Suspendidos - ".date('d-m-Y');
		}else{
			$clientes = $this->pagos_model->get_socios_activos($id_entidad);
			$titulo = $ent_abrev." - Socios Activos - ".date('d-m-Y');
		}
                $fila1 = false;

                $archivo="Listado de Socios"."_".date('Ymd');

                $headers=array();
                $headers[]='Nombre y Apellido';
                $headers[]='Telefono';
                $headers[]='Domicilio';
                $headers[]='DNI';
                $headers[]='Fecha de Alta';
                $headers[]='Monto Adeudado';
                $headers[]='Meses Adeudados';

                $datos=$clientes;
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);


    }

    public function actividades_excel($id=''){
        $id_entidad = $this->session->userdata('id_entidad');
        
        $this->load->model('actividades_model');
        $this->load->model('pagos_model');

        $actividad = $this->actividades_model->get_actividad($id);        
        $clientes = $this->pagos_model->get_pagos_actividad($id_entidad,$id);        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $titulo = $ent_abrev." - ".$actividad->nombre." - ".date('d-m-Y');
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:I1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         
        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Nombre y Apellido')
                    ->setCellValue('B1', 'Teléfono')
                    ->setCellValue('C1', 'DNI')
                    ->setCellValue('D1', 'Fecha de Nacimiento')
                    ->setCellValue('E1', 'Fecha de Alta')                  
                    ->setCellValue('F1', 'Observaciones')                  
                    ->setCellValue('G1', 'Monto Adeudado')                  
                    ->setCellValue('H1', 'Meses Adeudados')
                    ->setCellValue('I1', 'Estado');                    
        
        $cont = 2;
        foreach ($clientes as $cliente) {

            if($cliente->deuda){                      
                    $hoy = new DateTime();
                    $d2 = new DateTime($cliente->deuda->generadoel);                
                    $interval = $d2->diff($hoy);
                    $meses = $interval->format('%m');
                    if($meses > 0){
                        $adeudados = "Debe ".$meses;
                        if($meses > 1){ 
                            $adeudados .= ' Meses';
                        }else{
                            $adeudados .= ' Mes';
                        }                    
                    }else{
                        if( $hoy->format('%m') == $d2->format('%m')){
                            $adeudados = "Mes Actual";     
                        }else{                    
                            $adeudados = "Cuota al Día";           
                        }                       
                    }
                }else{                    
                    $adeudados = "Cuota al Día";                    
                }
                if($cliente->suspendido == 1){ 
                    $estado = 'SUSPENDIDO'; 
                }else{ 
                    $estado = 'ACTIVO'; 
                }

            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->socio)
                        ->setCellValue('B'.$cont, $cliente->telefono)
                        ->setCellValue('C'.$cont, $cliente->dni)
                        ->setCellValue('D'.$cont, $cliente->nacimiento)
                        ->setCellValue('E'.$cont, date('d/m/Y',strtotime($cliente->date)))                     
                        ->setCellValue('F'.$cont, $cliente->observaciones)
                        ->setCellValue('G'.$cont, number_format($cliente->monto_adeudado*-1,2))
                        ->setCellValue('H'.$cont, $adeudados)                    
                        ->setCellValue('I'.$cont, $estado);                        
                        $cont ++;
        } 
        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Clientes');
         
        foreach(range('A','H') as $columnID) {
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


    public function categorias_excel($id=''){
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('general_model');
        $categoria = $this->general_model->get_cat($id);               
        $clientes = $this->pagos_model->get_pagos_categorias($id_entidad,$id);
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $titulo = $ent_abrev." - ".$categoria->nombre." - ".date('d-m-Y');
        
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
                    ->setCellValue('A1', 'Nombre y Apellido')
                    ->setCellValue('B1', 'Socio')
                    ->setCellValue('C1', 'Teléfono')
                    ->setCellValue('D1', 'DNI')
                    ->setCellValue('E1', 'Fecha de Alta')                    
                    ->setCellValue('F1', 'Meses Adeudados');                    
        
        $cont = 2;
        foreach ($clientes as $cliente) {   

            $meses = @round($cliente->deuda/$cliente->cuota);
            if($meses < 0){
                $meses = $meses * -1;
                if($meses == 1){
                    $adeudados = "Debe ".$meses." mes";                        
                }else{
                    $adeudados = "Debe ".$meses." meses";                        
                }
            }else if($meses > 0){
                if($meses == 1){
                    $adeudados = $meses." mes pagado por adelantado";
                }else{
                    $adeudados = $meses." meses pagados por adelantado";                
                }
            }else if($meses == 0){
                $adeudados = "Socio sin Deuda";
            }

            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->nombre.' '.$cliente->apellido)
                        ->setCellValue('B'.$cont, '# '.$cliente->id)
                        ->setCellValue('C'.$cont, $cliente->telefono)
                        ->setCellValue('D'.$cont, $cliente->dni)
                        ->setCellValue('E'.$cont, date('d/m/Y',strtotime($cliente->alta)))
                        ->setCellValue('F'.$cont, $adeudados);
                        $cont ++;
        } 
        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Clientes');
         
        foreach(range('A','F') as $columnID) {
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
    
    public function ingresos_excel($fecha1='',$fecha2=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $clientes = $data['ingresos'] = $this->pagos_model->get_ingresos($id_entidad,$fecha1,$fecha2);
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $titulo = $ent_abrev." - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - ".date('d-m-Y');
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         
        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Facturado El')
                    ->setCellValue('B1', 'Pagado El')                                   
                    ->setCellValue('C1', 'Descripción')
                    ->setCellValue('D1', 'Monto')
                    ->setCellValue('E1', 'Pagado')
                    ->setCellValue('F1', 'Socio/Tutor')                                
                    ->setCellValue('G1', 'Observaciones');
        
        $cont = 2;
        foreach ($clientes as $cliente) {        
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, date('d/m/Y',strtotime($cliente->generadoel)))
                        ->setCellValue('B'.$cont, date('d/m/Y',strtotime($cliente->pagadoel)))
                        ->setCellValue('C'.$cont, strip_tags($cliente->descripcion))
                        ->setCellValue('D'.$cont, $cliente->monto)
                        ->setCellValue('E'.$cont, $cliente->pagado)              
                        ->setCellValue('F'.$cont, '#'.$cliente->sid.' - '.$cliente->socio->nombre.' '.$cliente->socio->apellido)             
                        ->setCellValue('G'.$cont, $cliente->socio->observaciones);
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

    public function cobros_actividad_excel($fecha1='',$fecha2='',$actividad='',$categoria=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');

        if($actividad != '-1'){
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_actividad($id_entidad,$fecha1,$fecha2,$actividad,$categoria);
            $data['actividad_s'] = $actividad;                        
        }else{
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_cuota($id_entidad,$fecha1,$fecha2,$categoria);
            $data['actividad_s'] = '-1';
        }
        $actividad = $this->actividades_model->get_actividad($actividad);
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $titulo = $ent_abrev." - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - ".$actividad->nombre." - ".date('d-m-Y');
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         
        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Facturado El')
                    ->setCellValue('B1', 'Pagado El')                    
                    ->setCellValue('C1', 'Monto')
                    ->setCellValue('D1', 'Activ/Seguro')
                    ->setCellValue('E1', 'Socio')                    
                    ->setCellValue('F1', 'Fecha de Nacimiento')
                    ->setCellValue('G1', 'Observaciones')
                    ->setCellValue('H1', 'Deuda');
        
        $cont = 2;        

        foreach ($clientes as $cliente) {

            if($cliente->deuda){                      
                $hoy = new DateTime();
                $d2 = new DateTime($cliente->deuda->generadoel);                
                $interval = $d2->diff($hoy);
                $meses = $interval->format('%m');
                if($meses > 0){
                    $deuda = 'Debe '.$meses;
                    if($meses > 1){ 
                        $deuda .= 'Meses';
                    }else{
                        $deuda .= 'Mes';
                    }
                    $deuda .= ' - $ '.$meses*$cliente->deuda->monto;                
                }else{
                    if( $hoy->format('%m') != $d2->format('%m') && $cliente->deuda->monto != '0.00' ){                
                        $deuda = 'Saldo del mes anterior';                    
                    }else{                                        
                        $deuda = 'Cuota al Día';                    
                    }
                }
            }else{
                $deuda = 'Cuota al Día';
            }

            if($cliente->tipo == 6){                      
		$concepto = "Seguro";
	    } else {
		$concepto = "Actividad";
	    }

            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, date('d/m/Y',strtotime($cliente->generadoel)))
                        ->setCellValue('B'.$cont, date('d/m/Y',strtotime($cliente->pagadoel)))
                        ->setCellValue('C'.$cont, $cliente->pagado)
                        ->setCellValue('D'.$cont, $concepto)              
                        ->setCellValue('E'.$cont, '#'.$cliente->sid.' '.$cliente->socio->nombre.' '.$cliente->socio->apellido)              
                        ->setCellValue('F'.$cont, $cliente->socio->nacimiento)
                        ->setCellValue('G'.$cont, $cliente->socio->observaciones)
                        ->setCellValue('H'.$cont, $deuda);                     
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

    public function morosos_excel($comision='',$actividad=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
                
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
	if($comision || $actividad){
		$clientes = $this->pagos_model->get_morosos($id_entidad,$comision, $actividad);
            	$titulo = $ent_abrev." - Morosos - ".date('d-m-Y');
	} else {
		return false;
	}

        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:C1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         
        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'DNI')
                    ->setCellValue('B1', 'ID')
                    ->setCellValue('C1', 'Nombre')
                    ->setCellValue('D1', 'Teléfonos')
                    ->setCellValue('E1', 'Domicilio')
                    ->setCellValue('F1', 'Actividad')                   
                    ->setCellValue('G1', 'Estado')                   
                    ->setCellValue('H1', 'Deuda Cta Social')                   
                    ->setCellValue('I1', 'Último Pago Cta Social')
                    ->setCellValue('J1', 'Deuda Actividad')
                    ->setCellValue('K1', 'Último Pago Actividad');
        
        $cont = 2;
        foreach ($clientes as $cliente) {        

		switch ( $cliente['estado'] ) {
			case 1: $xestado="SUSP"; break;
			case 0: $xestado="ACTI"; break;
		}

            	$this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente['dni'])
                        ->setCellValue('B'.$cont, $cliente['sid'])
                        ->setCellValue('C'.$cont, $cliente['apynom'])
                        ->setCellValue('D'.$cont, $cliente['telefono'])
                        ->setCellValue('E'.$cont, $cliente['domicilio'])
                        ->setCellValue('F'.$cont, $cliente['actividad'])
                        ->setCellValue('G'.$cont, $xestado)
                        ->setCellValue('H'.$cont, $cliente['deuda_cuota']*-1)
                        ->setCellValue('I'.$cont, $cliente['gen_cuota'])
                        ->setCellValue('J'.$cont, $cliente['deuda_activ']*-1)
                        ->setCellValue('K'.$cont, $cliente['gen_activ']);                       
                        $cont ++;
        } 
        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Clientes');
         
        foreach(range('A','E') as $columnID) {
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

    public function anterior_excel($id=''){
            
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        if($id != ''){
            $clientes = $this->pagos_model->get_pagos_actividad_anterior($id_entidad,$id);
            $actividad = $this->actividades_model->get_actividad($id);       
            $titulo = $ent_abrev." - Deuda Anterior - ".$actividad->nombre." - ".date('d-m-Y');
        }else{
            die();
        }
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         

        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Nombre y Apellido')
                    ->setCellValue('B1', 'Socio')
                    ->setCellValue('C1', 'Teléfono')
                    ->setCellValue('D1', 'DNI')
                    ->setCellValue('E1', 'Fecha de Nacimiento')
                    ->setCellValue('F1', 'Fecha de Alta')                 
                    ->setCellValue('G1', 'Sin deuda hasta el 30/04/2015');
        
        $cont = 2;
        foreach ($clientes as $cliente) {        
        if($cliente->deuda == 0){
            $deuda = "Si";
        }else{
            $deuda = "No";
        }
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->socio)
                        ->setCellValue('B'.$cont, $cliente->id)  
                        ->setCellValue('C'.$cont, $cliente->telefono)  
                        ->setCellValue('D'.$cont, $cliente->dni)  
                        ->setCellValue('E'.$cont, $cliente->nacimiento)  
                        ->setCellValue('F'.$cont, $cliente->date)  
                        ->setCellValue('G'.$cont, $deuda);
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

    public function becas_excel($actividad=false){       
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        if($actividad){
            $clientes = $this->pagos_model->get_becas($id_entidad,$actividad);
            if($actividad != '-1'){
                $a = $this->actividades_model->get_actividad($actividad);
            }else{
                $a = new STDClass();
                $a->nombre = 'Cuota Social';
            }
            $titulo = $ent_abrev." - Becados - ".$a->nombre." - ".date('d-m-Y');
        }else{
            die();
        }
        
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         

        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Nombre y Apellido')
                    ->setCellValue('B1', 'Socio')
                    ->setCellValue('C1', 'Teléfono')
                    ->setCellValue('D1', 'DNI')
                    ->setCellValue('E1', 'Fecha de Nacimiento')
                    ->setCellValue('F1', 'Fecha de Alta')                 
                    ->setCellValue('G1', 'Porcentaje Becado');
        
        $cont = 2;
        foreach ($clientes as $cliente) {            
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->nombre.' '.$cliente->apellido)
                        ->setCellValue('B'.$cont, $cliente->id)  
                        ->setCellValue('C'.$cont, $cliente->telefono)  
                        ->setCellValue('D'.$cont, $cliente->dni)  
                        ->setCellValue('E'.$cont, $cliente->nacimiento)  
                        ->setCellValue('F'.$cont, $cliente->alta)  
                        ->setCellValue('G'.$cont, $cliente->descuento.'%');
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

    public function sin_actividad_excel(){       
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');                
        $clientes = $this->pagos_model->get_sin_actividades($id_entidad);
            
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_ent_nombre');
        $titulo = $ent_abrev." - Socios Sin Actividades Asociadas - ".date('d-m-Y');
        
        
        
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/IOFactory');
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                     ->setLastModifiedBy("Club Villa Mitre")
                                     ->setTitle("Listado")
                                     ->setSubject("Listado de Socios");
        
        $this->phpexcel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(
            array(
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
            )
        );
         

        // agregamos información a las celdas
        $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Nombre y Apellido')
                    ->setCellValue('B1', 'Socio')
                    ->setCellValue('C1', 'Teléfono')
                    ->setCellValue('D1', 'DNI')
                    ->setCellValue('E1', 'Fecha de Nacimiento')
                    ->setCellValue('F1', 'Fecha de Alta');                 
        
        $cont = 2;
        foreach ($clientes as $cliente) {            
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->nombre.' '.$cliente->apellido)
                        ->setCellValue('B'.$cont, $cliente->id)  
                        ->setCellValue('C'.$cont, $cliente->telefono)  
                        ->setCellValue('D'.$cont, $cliente->dni)  
                        ->setCellValue('E'.$cont, $cliente->nacimiento)  
                        ->setCellValue('F'.$cont, $cliente->alta);                        
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
}

?>
