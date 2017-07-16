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
        $this->load->view('imprimir/index',$data);
        $this->load->view('imprimir/foot');
    }

    public function exportar($action='')
    {
        $data['listado'] = false;
        switch ($action) {
            case 'socios':
                $this->load->model('socios_model');                
                $this->load->model('general_model');                
                $this->load->model('pagos_model');                
                $clientes = $this->socios_model->get_socios();
                    
                $titulo = "CVM - Socios - ".date('d-m-Y');
                
                
                
                $this->load->library('PHPExcel');                
                $this->phpexcel->getProperties()->setCreator("Club Villa Mitre")
                                             ->setLastModifiedBy("Club Villa Mitre")
                                             ->setTitle("Listado")
                                             ->setSubject("Listado de Socios");
                
                $this->phpexcel->getActiveSheet()->getStyle('A1:R1')->getFill()->applyFromArray(
                    array(
                        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => 'E9E9E9'),
                    )
                );
                 

                // agregamos información a las celdas
                $this->phpexcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', '#')
                            ->setCellValue('B1', 'Apellido')
                            ->setCellValue('C1', 'Nombre')
                            ->setCellValue('D1', 'DNI')
                            ->setCellValue('E1', 'Domicilio')
                            ->setCellValue('F1', 'Localidad')
                            ->setCellValue('G1', 'Nacionalidad')
                            ->setCellValue('H1', 'Fecha de Nacimiento')
                            ->setCellValue('I1', 'Teléfono')
                            ->setCellValue('J1', 'Email')
                            ->setCellValue('K1', 'Celular')
                            ->setCellValue('L1', 'Tutor de grupo Familiar')
                            ->setCellValue('M1', 'Categoría de Socio')
                            ->setCellValue('N1', 'Descuento')
                            ->setCellValue('O1', 'Fecha de Ingreso')                            
                            ->setCellValue('P1', 'Estado')
                            ->setCellValue('Q1', 'Observaciones')
                            ->setCellValue('R1', 'Saldo en Cuenta Corriente');                 
                
                $cont = 2;
                foreach ($clientes as $cliente) {
                    //tutor
                    if($cliente->tutor != 0){
                        $tutor = $this->socios_model->get_socio($cliente->tutor);
                        $tutor->Id = '#'.$tutor->Id;
                    }else{
                        $tutor = new STDClass();
                        $tutor->Id = '';
                        $tutor->nombre = '';
                        $tutor->apellido = '';
                    }
                    //categoria
                    $categoria = $this->general_model->get_cat($cliente->categoria);
                    //estado
                    if($cliente->suspendido == 0){
                        $estado = 'Activo';
                    }else{
                        $estado = 'Suspendido';
                    }
                    //saldo
                    $saldo = $this->pagos_model->get_socio_total($cliente->Id);

                    $this->phpexcel->setActiveSheetIndex(0)
                                ->setCellValue('A'.$cont, $cliente->Id)
                                ->setCellValue('B'.$cont, $cliente->apellido)  
                                ->setCellValue('C'.$cont, trim($cliente->nombre))  
                                ->setCellValue('D'.$cont, $cliente->dni)  
                                ->setCellValue('E'.$cont, $cliente->domicilio)  
                                ->setCellValue('F'.$cont, $cliente->localidad)  
                                ->setCellValue('G'.$cont, $cliente->nacionalidad)  
                                ->setCellValue('H'.$cont, $cliente->nacimiento)
                                ->setCellValue('I'.$cont, $cliente->telefono)
                                ->setCellValue('J'.$cont, $cliente->mail)
                                ->setCellValue('K'.$cont, $cliente->celular)
                                ->setCellValue('L'.$cont, $tutor->Id.' - '.$tutor->apellido.' '.$tutor->nombre)
                                ->setCellValue('M'.$cont, $categoria->nomb)
                                ->setCellValue('N'.$cont, $cliente->descuento)
                                ->setCellValue('O'.$cont, $cliente->alta)
                                ->setCellValue('P'.$cont, $estado)
                                ->setCellValue('Q'.$cont, trim($cliente->observaciones))
                                ->setCellValue('R'.$cont, $saldo);                               
                                $cont ++;
                } 
                // Renombramos la hoja de trabajo
                $this->phpexcel->getActiveSheet()->setTitle('Clientes');
                 
                foreach(range('A','R') as $columnID) {
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
                break;

            case 'actividades':
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $actividades = $this->actividades_model->get_act_asoc_all();
                    
                $titulo = "CVM - Actividades - ".date('d-m-Y');
                
                
                
                $this->load->library('PHPExcel');                
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
                            ->setCellValue('A1', 'Socio #')
                            ->setCellValue('B1', 'Apellido')
                            ->setCellValue('C1', 'Nombre')
                            ->setCellValue('D1', 'Actividad #')
                            ->setCellValue('E1', 'Actividad')
                            ->setCellValue('F1', 'Descuento');

                $cont = 2;
                foreach ($actividades as $actividad) {                    

                    $this->phpexcel->setActiveSheetIndex(0)
                                ->setCellValue('A'.$cont, $actividad->sid)
                                ->setCellValue('B'.$cont, $actividad->socio_apellido)  
                                ->setCellValue('C'.$cont, trim($actividad->socio_nombre))  
                                ->setCellValue('D'.$cont, $actividad->aid)  
                                ->setCellValue('E'.$cont, $actividad->actividad_nombre)  
                                ->setCellValue('F'.$cont, $actividad->descuento);                                                        
                                $cont ++;
                } 
                // Renombramos la hoja de trabajo
                $this->phpexcel->getActiveSheet()->setTitle('Actividades');
                 
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
                break;

            case 'cuenta_corriente':
                $this->load->model('pagos_model');                
                $facturaciones = $this->pagos_model->get_facturacion_all();
                    
                $titulo = "CVM - Cuentas Corrientes - ".date('d-m-Y');
                
                
                
                $this->load->library('PHPExcel');                
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
                            ->setCellValue('A1', 'Socio #')
                            // ->setCellValue('B1', 'Apellido')
                            // ->setCellValue('C1', 'Nombre')
                            ->setCellValue('B1', 'Facturación #')
                            ->setCellValue('C1', 'Fecha')
                            ->setCellValue('D1', 'Descripcion')
                            ->setCellValue('E1', 'Tipo (D/H)')
                            ->setCellValue('F1', 'Importe');

                $cont = 2;
                foreach ($facturaciones as $facturacion) {                    
                    if($facturacion->debe == 0){
                        $tipo = "H";
                        $importe = $facturacion->haber;
                    }else if($facturacion->haber == 0){
                        $tipo = "D";
                        $importe = $facturacion->debe;
                    }

                    $this->phpexcel->setActiveSheetIndex(0)
                                ->setCellValue('A'.$cont, $facturacion->sid)
                                // ->setCellValue('B'.$cont, $facturacion->apellido)  
                                // ->setCellValue('C'.$cont, trim($facturacion->nombre))  
                                ->setCellValue('B'.$cont, $facturacion->Id)  
                                ->setCellValue('C'.$cont, $facturacion->date)  
                                ->setCellValue('D'.$cont, str_replace('Detalle',' Detalle',strip_tags($facturacion->descripcion)))
                                ->setCellValue('E'.$cont, $tipo)  
                                ->setCellValue('F'.$cont, $importe);                                                        
                                $cont ++;
                } 
                // Renombramos la hoja de trabajo
                $this->phpexcel->getActiveSheet()->setTitle('Cuentas Corrientes');
                 
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
                break;
            
            default:                
                $this->load->view('imprimir/index',$data);
                $this->load->view('imprimir/exportar',$data);
                $this->load->view('imprimir/foot');
                break;
        }
    }

    public function listado($listado,$id=false)
    {
        $data['listado'] = $listado;
        $this->load->view('imprimir/index',$data);
        switch ($listado) {
            case 'actividades':
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['render_cat'] = false;
                if($id){ $data['render_cat'] = $id; }                
                $this->load->view('imprimir/actividades',$data);
                break;

            case 'profesores':
                $this->load->model('actividades_model');
                $data['profesores'] = $this->actividades_model->get_profesores();
                $this->load->view('imprimir/profesores',$data);
                break;

            case 'usuarios_suspendidos':
                $this->load->model('pagos_model');
                $data['socios'] = $this->pagos_model->get_usuarios_suspendidos();
                $this->load->view('imprimir/usuarios_suspendidos',$data);
                break;

            case 'socios':
                //$this->load->model('actividades_model');
                //$data['actividades'] = $this->actividades_model->get_actividades();
                $this->load->view('imprimir/socios',$data);
                break;

            case 'categorias':
                $this->load->model('general_model');
                $data['actividades'] = $this->general_model->get_cats();                
                $this->load->view('imprimir/categorias',$data);
                break;

            case 'morosos':                
                $data['baseurl'] = base_url();                
                $this->load->model('pagos_model');
                $comision = $this->input->post('comisiones');
                $actividad = $this->input->post('morosos_activ'); 
                if($comision || $actividad){                           
                    	$data['morosos'] = $this->pagos_model->get_morosos($comision, $actividad);
                }else{
                    $data['morosos'] = false;
                }
                $this->load->model('actividades_model');
                $data['actividad_sel'] = $actividad;
                $data['comision_sel'] = $comision;
                $data['comisiones'] = $this->actividades_model->get_comisiones();
                $data['actividades'] = $this->actividades_model->get_actividades();
                $this->load->view('imprimir/morosos',$data);
                break;

            case 'financiacion':
                $this->load->model('pagos_model');
                $data['socios'] = $this->pagos_model->get_socios_financiados();
                $this->load->view('imprimir/financiacion',$data);                
                break;

             case 'becas':
                $this->load->model('actividades_model');
                $this->load->model('pagos_model');
                $actividad = false;                
                $actividad = $this->uri->segment(4);
                $data['socios'] = false;
                if($actividad){
                    $data['socios'] = $this->pagos_model->get_becas($actividad);
                }
                $data['a_actual'] = $actividad;
                $data['actividades'] = $this->actividades_model->get_actividades();
                $this->load->view('imprimir/becas',$data);             
                break;
            
            case 'sin_actividades':
                $this->load->model('pagos_model');
                $data['socios'] = $this->pagos_model->get_sin_actividades();
                $this->load->view('imprimir/sin_actividades',$data);
                break;

            default:

                break;
        }
        $this->load->view('imprimir/foot');
    }

    public function cobros($action='',$fecha1=false,$fecha2=false)
    {        
        $data = array();
        $this->load->model('pagos_model');
        $this->load->view('imprimir/index',$data);
        switch ($action) {
            case 'ingresos':
                $data['ingresos'] = false;
                if($fecha1 && $fecha2){
                    $data['ingresos'] = $this->pagos_model->get_ingresos($fecha1,$fecha2);
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
                        $data['cobros'] = $this->pagos_model->get_cobros_actividad($fecha1,$fecha2,$actividad,$categoria);
                        $data['actividad_s'] = $actividad;                        
                    }else{
                        $data['cobros'] = $this->pagos_model->get_cobros_cuota($fecha1,$fecha2,$categoria);
                        $data['actividad_s'] = '-1';
                    }
                }                
                $data['fecha1'] = $fecha1;
                $data['fecha2'] = $fecha2;
                $this->load->model('actividades_model');
                $data['actividades'] = $this->actividades_model->get_actividades();
                $data['actividad_info'] = $this->actividades_model->get_actividad($actividad);
                $this->load->view('imprimir/actividades-cobros', $data, FALSE);
                break;

            case 'anterior':
                $this->load->model('actividades_model');
                $id = $this->uri->segment(4);
                $data['actividad'] = new STDClass();
                $data['actividad']->Id = false;
                if($id){
                    $data['socios'] = $this->pagos_model->get_pagos_actividad_anterior($id);
                    $data['actividad'] = $this->actividades_model->get_actividad($id);
                }else{
                    $data['socios'] = false;                    
                }
                $data['actividades'] = $this->actividades_model->get_actividades();
                $this->load->view('imprimir/anterior',$data);
                break;

            case 'cuentadigital':
                $data['ingresos'] = false;
                if($fecha1 && $fecha2){
                    $data['ingresos'] = $this->pagos_model->get_ingresos_cuentadigital($fecha1,$fecha2);
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
        switch ($listado) {           
            case 'actividades':
                $this->load->model('pagos_model');
                $this->load->model('actividades_model');
                $data['actividad'] = $this->actividades_model->get_actividad($id);
                $data['profesor'] = $this->actividades_model->get_profesor($data['actividad']->profesor);
                $data['socios'] = $this->pagos_model->get_pagos_actividad($id);
                $this->load->view('imprimir/actividades_listado',$data);
                break;

            case 'profesores':
                $this->load->model('pagos_model');
                $this->load->model('actividades_model');
                $data['profesor'] = $this->actividades_model->get_profesor($id);
                $data['socios'] = $this->pagos_model->get_pagos_profesor($id);
                $this->load->view('imprimir/profesores_listado',$data);
                break;

            case 'socios':
                $this->load->model('pagos_model');
                if($id == 'activos'){
                    $data['id'] = $id;
                    $data['titulo'] = "Socios Activos";
                    $data['socios'] = $socios = $this->pagos_model->get_socios_activos();
                    foreach ($socios as $socio) {
                        $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($socio->Id);
                        /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
			            $array_ahg = $this->pagos_model->get_monto_socio($socio->Id);
                        $socio->cuota = $array_ahg['total'];
                        /* Fin Modificacion AHG */
                    }
                    $this->load->view('imprimir/socios_listado',$data); 
                }else if($id == 'suspendidos'){
                    $data['id'] = $id;
                    $data['titulo'] = "Socios Suspendidos";                    
                    $data['socios'] = $socios = $this->pagos_model->get_usuarios_suspendidos();
                    foreach ($socios as $socio) {
                        $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($socio->Id);
                        /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
			            $array_ahg = $this->pagos_model->get_monto_socio($socio->Id);
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
                $data['socios'] = $this->pagos_model->get_pagos_categorias($id);
                $this->load->view('imprimir/categorias_listado',$data);
                break;

            case 'socios':

                break;
            
            default:

                break;
        }
    }

/*
AHG Comentado 20170105 porque no se usa..... creo
    function morosos(){
        $this->load->model('pagos_model');
        $meses = $this->uri->segment(3);
        $act = $this->uri->segment(4) ?: null;
        if(!$meses){ $meses = 6; }
        if($meses){
        $data['morosos'] = $this->pagos_model->get_morosos($meses, $act);
            if($act){
                $this->load->model('actividades_model');
                $actividad = $this->actividades_model->get_actividad($this->uri->segment(4))->nombre;                        
            }else{
                $actividad = "Todas";
            }
        $data['meses'] = $meses;
        $data['actividad'] = $actividad;
        $this->load->view('imprimir-morosos',$data);
        }

    }
*/

    public function actividades($id=false){
        /*$this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $act = $this->uri->segment(3) ?: null;        
        if(!$act){die;}
        $actividad = $this->actividades_model->get_actividad($this->uri->segment(3))->nombre;
        $data['actividad'] = $actividad;
        $data['socios'] = $this->pagos_model->get_pagos_actividad($act);
        $this->load->view('imprimir-actividades',$data);        */
        if(!$id){ return false; }
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $data['actividad'] = $this->actividades_model->get_actividad($id);
        $data['profesor'] = $this->actividades_model->get_profesor($data['actividad']->profesor);
        $data['socios'] = $this->pagos_model->get_pagos_actividad($id);
        $this->load->view('imprimir/actividades_listado',$data);    
    }
    public function carnet(){
        $this->load->model('socios_model');        
        $this->load->model('pagos_model');        
        $id = $this->uri->segment(3) ?: null;        
        if(!$id){die;}
        $socio = $data['socio'] = $this->socios_model->get_socio($id);
        $data['cupon'] = $this->pagos_model->get_cupon($id);
        $monto = $this->pagos_model->get_monto_socio($id);
        $data['monto'] = $monto = $monto['total'];
        if($data['cupon']->monto == 0 || $data['cupon']->monto != $monto){
            //$this->load->model('socios_model');
            //$socio = $this->socios_model->get_socio($_POST['id']);
            $cupon = $this->cuentadigital($id,$socio->nombre.' '.$socio->apellido,$monto);
            if($cupon){                                    
                $this->load->model('pagos_model');
                $cupon_id = $this->pagos_model->generar_cupon($id,$monto,$cupon);
                $data = base64_decode($cupon['image']);
                $img = imagecreatefromstring($data);
                    if ($img !== false) {
                        @header('Content-Type: image/png');
                        imagepng($img,'images/cupones/'.$cupon_id.'.png',0);
                        imagedestroy($img);
                        redirect(base_url().'imprimir/carnet/'.$id);

                    }
                    else {
                        echo 'Ocurrió un error.';
                    }                
            }
        $data['cupon'] = $this->pagos_model->get_cupon($id);
        }
        $this->load->view('imprimir-carnet',$data);
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
        $this->load->model('pagos_model'); 
        if($type=='suspendidos'){    
            $clientes = $this->pagos_model->get_usuarios_suspendidos();
            $titulo = "CVM - Socios Suspendidos - ".date('d-m-Y');
        }else{
            $clientes = $this->pagos_model->get_socios_activos();
            $titulo = "CVM - Socios Activos - ".date('d-m-Y');
        }
        foreach ($clientes as $cliente) {
            $cliente->deuda = $this->pagos_model->get_ultimo_pago_socio($cliente->Id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
            $array_ahg = $this->pagos_model->get_monto_socio($cliente->Id);
            $cliente->cuota = $array_ahg['total'];
            /* Fin Modificacion AHG */
        }
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
                    ->setCellValue('B1', 'Teléfono')
                    ->setCellValue('C1', 'Domicilio')
                    ->setCellValue('D1', 'DNI')
                    ->setCellValue('E1', 'Fecha de Alta')                   
                    ->setCellValue('F1', 'Monto Adeudado')                   
                    ->setCellValue('G1', 'Meses Adeudados');




        $cont = 2;
        foreach ($clientes as $cliente) {        
            if($cliente->deuda){                      
                $hoy = new DateTime();
                $d2 = new DateTime($cliente->deuda->generadoel);                
                $interval = $d2->diff($hoy);
                $meses = $interval->format('%m');
                if($meses > 0){
                    $meses_a = "Debe ".$meses;
                    if($meses > 1){ 
                        $meses_a .= ' Meses';
                    }else{
                        $meses_a .= ' Mes';
                    }                    
                }else{
                    if( $hoy->format('%m') != $d2->format('%m') && $cliente->deuda->monto != '0.00' ){
                    $meses_a = "Saldo del mes anterior";
                    }else{                
                    $meses_a = "Cuota al Día";                
                    }
                }
            }else{            
                $meses_a = "Aún no se registró ningun pago";
            }                    
            

            if($cliente->deuda_monto < 0){
                $monto_a = "$ ".$cliente->deuda_monto*-1;        
            }else{    
                $monto_a = "Cuota al Día";        
            }
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->nombre.' '.$cliente->apellido)
                        ->setCellValue('B'.$cont, $cliente->telefono)
                        ->setCellValue('C'.$cont, $cliente->domicilio)
                        ->setCellValue('D'.$cont, $cliente->dni)
                        ->setCellValue('E'.$cont, date('d/m/Y',strtotime($cliente->alta)))
                        ->setCellValue('F'.$cont, $monto_a)
                        ->setCellValue('G'.$cont, $meses_a);
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

    public function actividades_excel($id=''){
        
        $this->load->model('actividades_model');
        $this->load->model('pagos_model');

        $actividad = $this->actividades_model->get_actividad($id);        
        $clientes = $this->pagos_model->get_pagos_actividad($id);        
        $titulo = "CVM - ".$actividad->nombre." - ".date('d-m-Y');
        
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
        
        $this->load->model('pagos_model');
        $this->load->model('general_model');
        $categoria = $this->general_model->get_cat($id);               
        $clientes = $this->pagos_model->get_pagos_categorias($id);
        
        $titulo = "CVM - ".$categoria->nomb." - ".date('d-m-Y');
        
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
                        ->setCellValue('B'.$cont, '# '.$cliente->Id)
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
                    
        $this->load->model('pagos_model');
        $clientes = $data['ingresos'] = $this->pagos_model->get_ingresos($fecha1,$fecha2);
        
        $titulo = "CVM - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - ".date('d-m-Y');
        
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
                    
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');

        if($actividad != '-1'){
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_actividad($fecha1,$fecha2,$actividad,$categoria);
            $data['actividad_s'] = $actividad;                        
        }else{
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_cuota($fecha1,$fecha2,$categoria);
            $data['actividad_s'] = '-1';
        }
        //$clientes = $data['ingresos'] = $this->pagos_model->get_cobros_actividad($fecha1,$fecha2,$actividad,$categoria);
        $actividad = $this->actividades_model->get_actividad($actividad);
        
        $titulo = "CVM - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - ".$actividad->nombre." - ".date('d-m-Y');
        
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
                    ->setCellValue('D1', 'Socio')                    
                    ->setCellValue('E1', 'Fecha de Nacimiento')
                    ->setCellValue('F1', 'Observaciones')
                    ->setCellValue('G1', 'Deuda');
        
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

            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, date('d/m/Y',strtotime($cliente->generadoel)))
                        ->setCellValue('B'.$cont, date('d/m/Y',strtotime($cliente->pagadoel)))
                        ->setCellValue('C'.$cont, $cliente->pagado)
                        ->setCellValue('D'.$cont, '#'.$cliente->sid.' '.$cliente->socio->nombre.' '.$cliente->socio->apellido)              
                        ->setCellValue('E'.$cont, $cliente->socio->nacimiento)
                        ->setCellValue('F'.$cont, $cliente->socio->observaciones)
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

    public function morosos_excel($comision='',$actividad=''){
                    
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
                
	if($comision || $actividad){
		$clientes = $this->pagos_model->get_morosos($comision, $actividad);
            	$titulo = "CVM - Morosos - ".date('d-m-Y');
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
            
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        if($id != ''){
            $clientes = $this->pagos_model->get_pagos_actividad_anterior($id);
            $actividad = $this->actividades_model->get_actividad($id);       
            $titulo = "CVM - Deuda Anterior - ".$actividad->nombre." - ".date('d-m-Y');
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
                        ->setCellValue('B'.$cont, $cliente->Id)  
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
    public function financiacion_excel(){
            
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        
            $clientes = $this->pagos_model->get_socios_financiados();            
            $titulo = "CVM - Financiación  - ".date('d-m-Y');
      
        
        
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
                    ->setCellValue('C1', 'Detalle')
                    ->setCellValue('D1', 'Cuotas')
                    ->setCellValue('E1', 'Cuota Actual')
                    ->setCellValue('F1', 'Monto')                 
                    ->setCellValue('G1', 'Inicio - Fin');
        
        $cont = 2;
        foreach ($clientes as $cliente) {            
            $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$cont, $cliente->nombre.' '.$cliente->apellido)
                        ->setCellValue('B'.$cont, $cliente->sid)  
                        ->setCellValue('C'.$cont, $cliente->detalle)  
                        ->setCellValue('D'.$cont, $cliente->cuotas)  
                        ->setCellValue('E'.$cont, $cliente->actual)  
                        ->setCellValue('F'.$cont, $cliente->monto)  
                        ->setCellValue('G'.$cont, $cliente->inicio.' | '.$cliente->fin);
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
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        if($actividad){
            $clientes = $this->pagos_model->get_becas($actividad);
            if($actividad != '-1'){
                $a = $this->actividades_model->get_actividad($actividad);
            }else{
                $a = new STDClass();
                $a->nombre = 'Cuota Social';
            }
            $titulo = "CVM - Becados - ".$a->nombre." - ".date('d-m-Y');
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
                        ->setCellValue('B'.$cont, $cliente->Id)  
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
        $this->load->model('pagos_model');                
        $clientes = $this->pagos_model->get_sin_actividades();
            
        $titulo = "CVM - Socios Sin Actividades Asociadas - ".date('d-m-Y');
        
        
        
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
                        ->setCellValue('B'.$cont, $cliente->Id)  
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
