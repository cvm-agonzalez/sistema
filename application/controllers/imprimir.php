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
        	$ent_nombre = $this->session->userdata('ent_nombre');

                $this->load->library('PHPExcel');
                $this->phpexcel->getProperties()->setCreator($ent_nombre)
                                             ->setLastModifiedBy($ent_nombre)
                                             ->setTitle(substr($titulo,0,30))
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


		$f=$cont;
                foreach ($datos as $fila) {
			$c=0;
                	foreach ($fila as $columna) {
                		$this->phpexcel->getActiveSheet()->setCellValueByColumnAndRow($c, $f, $columna);
                        	$c++;
                	}
			$f++;
		}

                // Renombramos la hoja de trabajo
                $this->phpexcel->getActiveSheet()->setTitle("substr($titulo,0,30)");

		$col = 0;
	 	while ( $col <= $cant_col ) {
			$columnID=$letra[$col++];
                    	$this->phpexcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                }
                // configuramos el documento para que la hoja
                // de trabajo número 0 sera la primera en mostrarse
                // al abrir el documento
                $this->phpexcel->setActiveSheetIndex(0);

                // redireccionamos la salida al navegador del cliente (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$archivo.'.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                $objWriter->save('php://output');
        }


    public function listado($action='',$id=false)
    {
        $id_entidad = $this->session->userdata('id_entidad');
        $data = array();
        $this->load->view('imprimir/index_listado',$data);

        switch ($action) {
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

            case 'tutores':
                $this->load->model('socios_model');
                $data['socios'] = $this->socios_model->get_tutores($id_entidad);                
                $this->load->view('imprimir/tutores',$data);
                break;

            case 'morosos':                
                $data['baseurl'] = base_url();                
                $this->load->model('pagos_model');
                $actividad = $this->input->post('morosos_activ'); 
                if($actividad){                           
                    $data['morosos'] = $this->pagos_model->get_morosos($id_entidad, $actividad);
                }else{
                    $data['morosos'] = false;
                }
                $this->load->model('actividades_model');
                $data['actividad_sel'] = $actividad;
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
		if ( $actividad < 0 ) {
			$arr_activ = array( 'nombre' => 'Cuota Social');
                	$data['actividad_info'] = (object) $arr_activ;
		} else {
                	$data['actividad_info'] = $this->actividades_model->get_actividad($actividad);
		}
                $this->load->view('imprimir/actividades-cobros', $data, FALSE);
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

            case 'cooperativa':
                $data['ingresos'] = false;
                if($fecha1 && $fecha2){
                    $data['ingresos'] = $this->pagos_model->get_ingresos_cooperativa($id_entidad, $fecha1,$fecha2);
                }
                $data['fecha1'] = $fecha1;
                $data['fecha2'] = $fecha2;
                $this->load->view('imprimir/cooperativa', $data, FALSE);
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

    public function carnets(){
        $hoja = $this->uri->segment(3);
        $id_entidad = $this->session->userdata('id_entidad');
        $categoria = $this->input->post('cat_sel');
        $foto = $this->input->post('foto_sel');
        $actividad = $this->input->post('act_sel');
        $this->load->model('socios_model');
	$socios = $this->socios_model->get_carnets($id_entidad, $categoria, $foto, $actividad);
        $data['id_entidad'] = $id_entidad;
        $this->load->model('pagos_model');
        if(!$socios){die;}
	$soc_carnets=array();
	$cont=1;
	foreach ( $socios as $socio ) {
		if ( $cont >= ($hoja*5)-4 && $cont <= ($hoja*5) ) {
                	$cupon = $this->pagos_model->get_cupon($socio->id, $id_entidad);
			$monto = $this->pagos_model->get_monto_socio($socio->id)['total'];
			$soc_carnets[] = array('socio'=>$socio, 'cupon'=>$cupon, 'monto'=>$monto);
		}
		$cont++;
	}
	$data['socios'] = $soc_carnets;
        $this->load->view('imprimir-carnets-lote',$data);
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
                $result['codlink'] = substr($xml->INVOICE->PAYMENTCODE2,-10);
                //$result = $xml->INVOICE->INVOICEURL;

            }        
            if ($count > 5) { $repetir = false; };

        } while ( $repetir );    
            return $result;
    }


    public function cuentadigital_excel($fecha1='',$fecha2=''){

                $id_entidad = $this->session->userdata('id_entidad');
                $ent_abrev = $this->session->userdata('ent_abreviatura');
                $ent_nombre = $this->session->userdata('ent_nombre');
                $this->load->model('pagos_model');
        	$cobros = $this->pagos_model->get_ingresos_cuentadigital($id_entidad,$fecha1,$fecha2);

        	$fila1 = $ent_nombre." - Ingresos de Cuenta Digital del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - generado el ".date('d-m-Y');
        	$titulo = $ent_abrev."_IngresosCD";

                $archivo="Ingresos_CuentaDigital_".date('Ymd');

                $headers=array();
                $headers[]='Fecha';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Socio';
                $headers[]='Importe';

        	$datos = array();
        	foreach ($cobros as $cobro) {
                	$dato = array (
                        	'fecha' =>  date('d/m/Y',strtotime($cobro->fecha)),                     
                        	'id' => "# ".$cobro->sid,
                        	'nro_socio' => $cobro->socio->nro_socio,
                        	'socio' => $cobro->socio->nombre.", ".$cobro->socio->apellido,
                        	'monto' => $cobro->monto
                	);
                	$datos[]=$dato;
        	}
    
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    }

    public function cooperativa_excel($fecha1='',$fecha2=''){

                $id_entidad = $this->session->userdata('id_entidad');
                $ent_abrev = $this->session->userdata('ent_abreviatura');
                $ent_nombre = $this->session->userdata('ent_nombre');
                $this->load->model('pagos_model');
        	$cobros = $this->pagos_model->get_ingresos_cooperativa($id_entidad,$fecha1,$fecha2);

        	$fila1 = $ent_nombre." - Ingresos de Cooperativa del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - generado el ".date('d-m-Y');
        	$titulo = $ent_abrev."_IngresosCoope";

                $archivo="Ingresos_Cooperativa_".date('Ymd');

                $headers=array();
                $headers[]='Fecha';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Socio';
                $headers[]='Importe';

        	$datos = array();
        	foreach ($cobros as $cobro) {
                	$dato = array (
                        	'fecha' =>  date('d/m/Y',strtotime($cobro->fecha_pago)),                     
                        	'id' => "# ".$cobro->sid,
                        	'nro_socio' => $cobro->socio->nro_socio,
                        	'socio' => $cobro->socio->nombre.", ".$cobro->socio->apellido,
                        	'monto' => $cobro->importe
                	);
                	$datos[]=$dato;
        	}
    
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    }

    public function exportar()
    {
        	$data = array();
                $this->load->view('imprimir/index_exportar',$data);
                $this->load->view('imprimir/exportar',$data);
                $this->load->view('imprimir/foot');
    }

    public function exportar_cuenta_corriente() {
        $id_entidad = $this->session->userdata('id_entidad');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
                $this->load->model('pagos_model');
                $facturaciones = $this->pagos_model->get_facturacion_all($id_entidad);

                $ent_abrev = $this->session->userdata('ent_abreviatura');
                $ent_nombre = $this->session->userdata('ent_nombre');

                $fila1 = $ent_nombre." - Cuentas Corrientes - generado el".date('d-m-Y');
                $titulo = "CtaCte";

                $archivo="Listado_Cuenta_Corriente"."_".date('Ymd');
                $headers=array();
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='ID Facturacion #';
                $headers[]='Fecha';
                $headers[]='Descripcion';
                $headers[]='Tipo (D/H)';
                $headers[]='Importe Debe';
                $headers[]='Importe Haber';

                $datos=array();
                foreach ( $facturaciones as $fact ) {
                        $dato = array (
				'sid' => $fact->sid,
				'nro_socio' => $fact->nro_socio,
				'id' => $fact->id,
                        	'fecha' =>  date('d/m/Y',strtotime($fact->date)),                     
				'descripcion' => $fact->descripcion,
				'tipo' => $fact->tipo,
				'debe' => $fact->debe,
				'haber' => $fact->haber
			);
			$datos[] = $dato;
		}
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);

    }

    public function exportar_actividades() {
        $id_entidad = $this->session->userdata('id_entidad');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
                $this->load->model('actividades_model');
                $this->load->model('socios_model');
                $actividades = $this->actividades_model->get_act_asoc_all($id_entidad);
                $ent_abrev = $this->session->userdata('ent_abreviatura');
                $ent_nombre = $this->session->userdata('ent_nombre');

                $titulo = $ent_abrev."_Actividades";
                $fila1 = $ent_nombre." - Actividades - generado el".date('d-m-Y');

                $archivo="Listado_Actividades"."_".date('Ymd');
                $headers=array();
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Apellido';
                $headers[]='Nombre';
                $headers[]='Actividad #';
                $headers[]='Descripcion Actividad';
                $headers[]='Descuento';
                $headers[]='Precio';

                $datos=array();
                foreach ( $actividades as $actividad ) {
                        $dato = array (
				'sid' => $actividad->sid,
				'nro_socio' => $actividad->nro_socio,
				'apellido' => $actividad->socio_apellido,
				'nombre' => $actividad->socio_nombre,
				'act_n' => $actividad->aid,
				'act_x' => $actividad->actividad_nombre,
				'descuento' => $actividad->descuento,
				'precio' => $actividad->precio
			);
                	$datos[] = $dato;
		}
                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    }

    public function exportar_socios() {
        	$id_entidad = $this->session->userdata('id_entidad');
        	$ent_abrev = $this->session->userdata('ent_abreviatura');
        	$ent_nombre = $this->session->userdata('ent_nombre');
                $this->load->model('socios_model');                
                $clientes = $this->socios_model->get_socios_export($id_entidad);
                    
                $titulo = $ent_abrev."_Socios";
		$fila1 = $ent_nombre." - Listado de Socios - generado el ".date('d-m-Y');

		$archivo="Listado_Socios"."_".date('Ymd');

		$headers=array();
		$headers[]='SID';
		$headers[]='Nro Socio';
		$headers[]='Apellido';
		$headers[]='Nombre';
		$headers[]='DNI';
		$headers[]='Domicilio';
		$headers[]='Localidad';
		$headers[]='Nacionalidad';
		$headers[]='Fecha de Nacimiento';
		$headers[]='Telefono';
		$headers[]='Email';
		$headers[]='Celular';
		$headers[]='Tutor de grupo Familiar';
		$headers[]='Categoría de Socio';
		$headers[]='Descuento';
		$headers[]='Fecha de Ingreso';
		$headers[]='Suspendido';
		$headers[]='Observaciones';
		$headers[]='Saldo en Cuenta Corriente';

                $datos=array();
                foreach ( $clientes as $cliente ) {
                        $dato = array (
                                "id"=> $cliente->id,
                                "nro_socio"=> $cliente->nro_socio,
                                "apellido"=> $cliente->apellido,
                                "nombre"=> $cliente->nombre,
                                "dni"=> $cliente->dni,
                                "domicilio"=> $cliente->domicilio,
                                "localidad"=> $cliente->localidad,
                                "nacionalidad"=> $cliente->nacionalidad,
                                "nacimiento"=> $cliente->nacimiento,
                                "telefono"=> $cliente->telefono,
                                "mail"=> $cliente->mail,
                                "celular"=> $cliente->celular,
                                "tutor"=> $cliente->apynom_tutor,
                                "categoria"=> $cliente->categ_nombre,
                                "descuento"=> $cliente->descuento,
                        	'ingreso' =>  date('d/m/Y',strtotime($cliente->alta)),                     
                                "estado"=> $cliente->suspendido,
                                "observaciones"=> $cliente->observaciones,
                                "saldo"=> $cliente->saldo
                        );
                        $datos[]=$dato;
                }

		$this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    }

    public function socios_excel($type=''){
		$id_entidad = $this->session->userdata('id_entidad');
		$this->load->model('pagos_model'); 
		$ent_abrev = $this->session->userdata('ent_abreviatura');
		$ent_nombre = $this->session->userdata('ent_nombre');
		if( $type=='suspendidos' ) {    
			$clientes = $this->pagos_model->get_usuarios_suspendidos($id_entidad);
			$fila1 = $ent_nombre." - Socios Suspendidos - generado el ".date('d-m-Y');
			$titulo = $ent_abrev."SocSusp";
		} else {
			$clientes = $this->pagos_model->get_socios_activos($id_entidad);
			$fila1 = $ent_nombre." - Socios Activos - generado el ".date('d-m-Y');
			$titulo = $ent_abrev."SocActiv";
		}

                $archivo="Listado de Socios"."_".date('Ymd');

                $headers=array();
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Nombre y Apellido';
                $headers[]='Telefono';
                $headers[]='Domicilio';
                $headers[]='DNI';
                $headers[]='Fecha de Alta';
                $headers[]='Monto Adeudado';
                $headers[]='Meses Adeudados';

                $datos=array();
                foreach ( $clientes as $cliente ) {
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

                        $dato = array (
                                "id"=> $cliente->id,
                                "nro_socio"=> $cliente->nro_socio,
                                "apynom"=> $cliente->nombre.", ".$cliente->apellido,
                                "telefono"=> $cliente->telefono,
                                "domicilio"=> $cliente->domicilio,
                                "dni"=> $cliente->dni,
                                "fch_alta"=> $cliente->alta,
                                "deuda_pesos"=> $cliente->deuda_monto,
                                "deuda_meses"=> $adeudados
                        );
                        $datos[]=$dato;
                }

                $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);


    }

    public function actividades_excel($id=''){

        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('actividades_model');
        $this->load->model('pagos_model');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');

        $actividad = $this->actividades_model->get_actividad($id);        
        $clientes = $this->pagos_model->get_pagos_actividad($id_entidad,$id);        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        $fila1 = $ent_nombre." - ".$actividad->nombre." - generado el ".date('d-m-Y');
        $titulo = $ent_abrev."_".$actividad->nombre;

	$archivo="Socios_Actividades"."_".date('Ymd');

        $headers=array();
        $headers[]='Nombre y Apellido';
        $headers[]='#ID';
        $headers[]='Nro Socio';
        $headers[]='DNI';
        $headers[]='Fecha Nacimiento';
        $headers[]='Observaciones';
        $headers[]='Meses Adeudados';
        $headers[]='Monto Adeudado';
        $headers[]='Estado';

	$datos=array();
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

		$dato = array (
                        'socio' => $cliente->socio,
                        'id' => $cliente->id,
                        'nro_socio' => $cliente->nro_socio,
                        'dni' => $cliente->dni,
                        'nacimiento' => $cliente->nacimiento,
                        'observaciones' => $cliente->observaciones,
                        'meses' => $adeudados,
                        'deuda' => $cliente->monto_adeudado*-1,
                        'estado' => $estado
		);
		$datos[] = $dato;
        } 

	$this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    }


    public function categorias_excel($id=''){
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('general_model');
        $categoria = $this->general_model->get_cat($id);               
        $clientes = $this->pagos_model->get_pagos_categorias($id_entidad,$id);
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        $fila1 = $ent_nombre." - ".$categoria->nombre." - generado el ".date('d-m-Y');
        $titulo = $ent_abrev."_".$categoria->nombre;
        $archivo = "Cat_".$categoria->nombre;
        
        // agregamos información a las celdas
                $headers=array();
                $headers[]='Nombre y Apellido';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Telefono';
                $headers[]='DNI';
                $headers[]='Fecha de Ingreso';
                $headers[]='Monto Deuda';
                $headers[]='Meses Adeudados';
         
        $datos = array();
        foreach ($clientes as $cliente) {   

		$dato = array (
                        'apynom' => $cliente->nombre.' '.$cliente->apellido,
                        'sid' => '#'.$cliente->id,
                        'nro_socio' => $cliente->nro_socio,
                        'telefono' => $cliente->telefono,
                        'dni' => $cliente->dni,
                        'alta' => $cliente->alta,
                        'deuda' => $cliente->deuda_monto*-1,
                        'meses' => $cliente->meses
		);
		$datos[]=$dato;

        } 
         
        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    
    // end: setExcel
    }
    
    public function ingresos_excel($fecha1='',$fecha2=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $clientes = $data['ingresos'] = $this->pagos_model->get_ingresos($id_entidad,$fecha1,$fecha2);
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        $fila1 = $ent_nombre." - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - generado el ".date('d-m-Y');
        $titulo = $ent_abrev."_Ingresos";
        $archivo = "Ingresos_".date('Ymd');
        
        // agregamos información a las celdas
                $headers=array();
                $headers[]='Facturado El';
                $headers[]='Pagado El';
                $headers[]='Descripcion';
                $headers[]='Monto';
                $headers[]='Pagado';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Socio/Tutor';
                $headers[]='Observaciones';
         
        
        $datos = array();
        foreach ($clientes as $cliente) {        
		$dato = array (
                        'generadoel' => date('d/m/Y',strtotime($cliente->generadoel)),
                        'pagadoel' => date('d/m/Y',strtotime($cliente->pagadoel)),
                        'descripcion' => strip_tags($cliente->descripcion),
                        'monto' => $cliente->monto,
                        'pagado' => $cliente->pagado,
                        'id' => '#'.$cliente->sid,
                        'nro_socio' => $cliente->socio->nro_socio,
                        'apynom' => $cliente->socio->nombre.' '.$cliente->socio->apellido,
                        'observaciones' => $cliente->socio->observaciones
		);
		$datos[]=$dato;
        } 

        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    
    // end: setExcel
    }

    public function cobros_actividad_excel($fecha1='',$fecha2='',$actividad='',$categoria=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');

        if($actividad != '-1'){
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_actividad($id_entidad,$fecha1,$fecha2,$actividad,$categoria);
            $data['actividad_s'] = $actividad;                        
            $actividad = $this->actividades_model->get_actividad($actividad);
        }else{
            $clientes = $data['ingresos'] = $this->pagos_model->get_cobros_cuota($id_entidad,$fecha1,$fecha2,$categoria);
            $data['actividad_s'] = '-1';
            $actividad = (object)array("id"=>'-1',"nombre"=>"Cuota Social");
        }
        
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        $fila1 = $ent_nombre." - Ingresos del ".date('d-m-Y',strtotime($fecha1))." al ".date('d-m-Y',strtotime($fecha2))." - ".$actividad->nombre." - generado el ".date('d-m-Y');
        $titulo = $ent_abrev."_Ingresos_".$actividad->nombre;
        $archivo = "Ingresos_".$actividad->nombre."_".date('Ymd');
         
        // agregamos información a las celdas
                $headers=array();
                $headers[]='Facturado El';
                $headers[]='Pagado El';
                $headers[]='Monto';
                $headers[]='Activ/Seguro';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Socio';
                $headers[]='Fecha de Nacimiento';
                $headers[]='Observaciones';
                $headers[]='Deuda';

	$datos=array();
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
		$dato = array (
                        'generadoel' => date('d/m/Y',strtotime($cliente->generadoel)),
                        'pagadoel' => date('d/m/Y',strtotime($cliente->pagadoel)),
                        'pagado' => $cliente->pagado,
                        'concepto' => $concepto,
                        'id' => '# '.$cliente->sid,
                        'nro_socio' => $cliente->socio->nro_socio,
                        'apynom' => $cliente->socio->nombre.' '.$cliente->socio->apellido,
                        'nacimiento' => date('d/m/Y',strtotime($cliente->socio->nacimiento)),
                        'observaciones' => $cliente->socio->observaciones,
                        'deuda' => $deuda
		);
		$datos[]=$dato;
        } 
        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    
    // end: setExcel
    }

    public function morosos_excel($actividad=''){
                    
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
                
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
	if($actividad){
		$clientes = $this->pagos_model->get_morosos($id_entidad,$actividad);
            	$fila1 = $ent_nombre." - Morosos - generado el ".date('d-m-Y');
            	$titulo = $ent_abrev."_Morosos";
	} else {
		return false;
	}
	$archivo="Listado_Morosos"."_".date('Ymd');

        // agregamos información a las celdas
                $headers=array();
                $headers[]='DNI';
                $headers[]='#ID';
                $headers[]='Nro Socio';
                $headers[]='Nombre';
                $headers[]='Telefonos';
                $headers[]='Domicilio';
                $headers[]='Actividad';
                $headers[]='Estado';
                $headers[]='Deuda Cta Social';
                $headers[]='Ult Pago Cta Social';
                $headers[]='Deuda Actividad';
                $headers[]='Ult Pago Actividad';
        
	$datos=array();
        foreach ($clientes as $cliente) {        

		switch ( $cliente['estado'] ) {
			case 1: $xestado="SUSP"; break;
			case 0: $xestado="ACTI"; break;
		}

		$dato = array (
                        'dni' => $cliente['dni'],
                        'sid' => $cliente['sid'],
                        'nro_socio' => $cliente['nro_socio'],
                        'apynom' => $cliente['apynom'],
                        'telefono' => $cliente['telefono'],
                        'domicilio' => $cliente['domicilio'],
                        'actividad' => $cliente['actividad'],
                        'estado' => $xestado,
                        'deuda_cs' => $cliente['deuda_cuota']*-1,
                        'ult_cs' => date('d/m/Y',strtotime($cliente['gen_cuota'])),
                        'deuda_act' => $cliente['deuda_activ']*-1,
                        'ult_act' => date('d/m/Y',strtotime($cliente['gen_activ']))
		);
		$datos[]=$dato;
        } 
        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    
    // end: setExcel
    }

    public function becas_excel($actividad=false){       

        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');
        $this->load->model('actividades_model');
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        if($actividad){
            $clientes = $this->pagos_model->get_becas($id_entidad,$actividad);
            if($actividad != '-1'){
                $a = $this->actividades_model->get_actividad($actividad);
            }else{
                $a = new STDClass();
                $a->nombre = 'Cuota Social';
            }
            $titulo = $ent_abrev."_Becas_".$a->nombre;
            $fila1 = $ent_nombre." - Becados - ".$a->nombre." - generado el ".date('d-m-Y');
        }else{
            die();
        }
        $archivo = "Becas_".$a->nombre."_".date('Ymd');
        
        // agregamos información a los titulos
        $headers=array();
        $headers[]='Nombre y Apellido';
        $headers[]='SID';
        $headers[]='Nro Socio';
        $headers[]='Telefono';
        $headers[]='DNI';
        $headers[]='Fecha de Nacimiento';
        $headers[]='Fecha de Ingreso';
        $headers[]='% Becado';
       
        $datos = array();
        foreach ($clientes as $cliente) {            
                $dato = array (
                        'apynom' => $cliente->nombre.", ".$cliente->apellido,
                        'sid' => $cliente->id,
                        'nro_socio' => $cliente->nro_socio,
                        'telefono' => $cliente->telefono,
                        'dni' => $cliente->dni,
                        'nacimiento' => date('d/m/Y',strtotime($cliente->nacimiento)),
                        'alta' => date('d/m/Y',strtotime($cliente->alta)),
                        'beca' => $cliente->descuento."%"
                );
                $datos[]=$dato;
        } 
         
        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
    
    // end: setExcel
    }

    public function sin_actividad_excel(){       
        $id_entidad = $this->session->userdata('id_entidad');
        $this->load->model('pagos_model');                
        $clientes = $this->pagos_model->get_sin_actividades($id_entidad);
            
        $ent_abrev = $this->session->userdata('ent_abreviatura');
        $ent_nombre = $this->session->userdata('ent_nombre');
        $fila1 = $ent_nombre." - Socios Sin Actividades Asociadas - generado el ".date('d-m-Y');
        $titulo = $ent_abrev."_SocSinActiv";
        $archivo = "SociosSinActividad_".date('Ymd');
        
        // agregamos información a los titulos
        $headers=array();
        $headers[]='Nombre y Apellido';
        $headers[]='SID';
        $headers[]='Nro Socio';
        $headers[]='Telefono';
        $headers[]='DNI';
        $headers[]='Fecha de Nacimiento';
        $headers[]='Fecha de Ingreso';
        

        $datos = array();
        foreach ($clientes as $cliente) {            
                $dato = array (
                        'apynom' => $cliente->nombre.", ".$cliente->apellido,
                        'sid' => $cliente->id,
                        'nro_socio' => $cliente->nro_socio,
                        'telefono' => $cliente->telefono,
                        'dni' => $cliente->dni,
                        'nacimiento' => date('d/m/Y',strtotime($cliente->nacimiento)),
                        'alta' => date('d/m/Y',strtotime($cliente->alta))
                );
                $datos[]=$dato;
        } 

        $this->gen_EXCEL($headers, $datos, $titulo, $archivo, $fila1);
        
    // end: setExcel
    }
}

?>
