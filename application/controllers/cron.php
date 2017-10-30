<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Cron extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        if($_GET['order'] != 'asdqwe'){exit('No Permitido');}
        $this->load->helper('url');
    }

	function index()
	{
		return false;
	}

    public function decode($value='')
    {
        $this->load->database();
        $this->db->where('barcode', '');
        $query = $this->db->get('cupones',10,0);        
        if( $query->num_rows() == 0 ){ return false; }
        $cupones = $query->result();
        $query->free_result();
        foreach ($cupones as $cupon) {
            var_dump($cupon);
            $content = file_get_contents("https://zxing.org/w/decode?u=http%3A%2F%2Fclubvillamitre.com%2Fimages%2Fcupones%2F".$cupon->Id.".png");
            list($a,$estado1) = explode('<title>',$content);
            $estado = explode('</title>',$estado1);
            if($estado[0] == "Decode Succeeded"){
                list($a,$pre1) = explode('<pre>',$estado[1]);
                $pre = explode('</pre>',$pre1);                
                $cup = array('barcode' => $pre[0]);
                $this->db->where('Id', $cupon->Id);
                $this->db->update('cupones', $cup);
            }
        }
    }

    function facturacion(){ // esta funcion genera la facturacion del mes el dia 1
        $this->load->model("pagos_model");
	$this->load->model("socios_model");
	$this->load->model("debtarj_model");
	$this->load->model("tarjeta_model");

        if ($this->uri->segment(3)) {
		$xhoy = $this->uri->segment(3);
	} else {
		$xhoy=date('Y-m-d');
	}

	// Periodo y fechas del proceso.....
	$xanio=date('Y', strtotime($xhoy));
	$xmes=date('m', strtotime($xhoy));
	$xperiodo=date('Ym', strtotime($xhoy));
	$xahora=date('Y-m-d G:i:s', strtotime($xhoy));
	$xlim1=date('Y-m-',strtotime($xhoy)).'25';
	$xlim2=date('Y-m-t', strtotime($xhoy));

        //log
        $file = './application/logs/facturacion-'.$xanio.'-'.$xmes.'.log';        
        $file_col = './application/logs/cobranza_col-'.$xanio.'-'.$xmes.'.csv';        
        if( !file_exists($file) ){
            $log = fopen($file,'w');
            $col = fopen($file_col,'w');
        }else{
            $log = fopen($file,'a');
            $col = fopen($file_col,'a');
        }
        //chequeamos el estado del cron
        if(!$cron_state = $this->pagos_model->check_cron($xperiodo)){
            //el cron ya finalizó
            $txt = date('H:i:s').": Intento de ejecución de Cron Finalizado! \n";
            fwrite($log, $txt);            
            exit();
        }
        if($cron_state == 'iniciado'){
            $txt = date('H:i:s').": Inicio de Cron... \n";
            fwrite($log, $txt);                        

            $soc_susp=$this->suspender($log); // suspendemos socios que deban mas de 4 meses de cuota social
	    // Actualizo el registro de facturacion_cron con los socios suspendidos
	    $this->pagos_model->update_facturacion_cron($xperiodo,1,$soc_susp,0);
            $this->db->truncate('facturacion_mails'); //limpiamos la db de mails
            fwrite($log, date('H:i:s').' - Truncate mails');                        
            //$this->email_a_suspendidos();
            //fwrite($log, date('H:i:s').' - Emails suspendidos');                        
            $this->db->update('socios',array('facturado'=>0)); //establecemos todos los socios como no facturados
            fwrite($log, date('H:i:s').' - Indicador facturado en 0');                        
    		$cumpleanios = $this->socios_model->get_cumpleanios(); //buscamos los que cumplen 18 años
		$cump=0;
    		foreach ($cumpleanios as $menor) {
    			$this->socios_model->actualizar_menor($menor->Id); //los quitamos del grupo familiar y cambiamos la categoria a mayor
                $txt = date('H:i:s').": Actualización de categoría socio #".$menor->Id." \n";
                fwrite($log, $txt);   
			$cump++;
    		}
            fwrite($log, date('H:i:s').' - Cambio de categoria mayor');                        
	    // Actualizo el registro de facturacion_cron con los socios que cambiaron de categoria por mayoria de edad
	    $this->pagos_model->update_facturacion_cron($xperiodo,2,$cump,0);

        }else if($cron_state == 'en_curso'){
            $txt = date('H:i:s').": Reanudando Cron... \n";
            fwrite($log, $txt);
        }

	// Busco los socios que tienen que pagar
	$socios = $this->socios_model->get_socios_pagan(true);
	// Si no encontre ninguno logeo y corto
        if(!$socios){ 
            $txt = date('H:i:s').": No se encontraron socios a facturar \n";
            fwrite($log, $txt);
            exit(); 
        }else{
	// Logeo la cantidad de total de asociados encontrados para facturar
            $txt = date('H:i:s').": Se encontraron ".count($socios)." socios a facturar \n";
            fwrite($log, $txt);            
        }
	// Ciclo los asociados a facturar
	foreach ($socios as $socio) {		
		// Busco el valor de la cuota social a pagar
		$cuota = $this->pagos_model->get_monto_socio($socio->Id);
		// Si tiene categoria de NO SOCIO no genero cuota
		$descripcion = '<strong>Categoría:</strong> '.$cuota['categoria'];
		if ( $cuota['categoria'] != 'No Socio' ) {
			// Si es un grupo familiar detallo los integrantes
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

			// Inserto el pago de la cuota (tipo=1)
            		$pago = array(
                		'sid' => $socio->Id, 
                		'tutor_id' => $socio->Id,
                		'aid' => 0, 
                		'generadoel' => $xhoy,
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
		}

		// Ciclo las actividades que tiene relacionadas el asociado
		foreach ($cuota['actividades']['actividad'] as $actividad) {	       
			// Por ahora no facturamos la primer cuota en la facturacion
			// Si la actividad tiene cuota inicial y es la primer cuota a facturar le ponemos cuota inicial
			// TODO AHG lo comento hasta definir bien si se va a facturar con el mes, por ahora solo con el alta de la relacion
			/*
			if ($actividad->cuota_inicial > 0) {
				// TODO AHG Falta condicionar solo a la primer vez - usar $actividad->alta y date
                		$descr_inicial = 'Cuota Inicial '.$actividad->nombre.' - $ '.$actividad->cuota_inicial.'<br>';
	                        // Inserto el pago de la actividad (tipo=4)
                        	$pago = array(
                                	'sid' => $socio->Id,
                                	'tutor_id' => $socio->Id,
                                	'aid' => $actividad->Id,
                                	'generadoel' => $xhoy,
                                	'descripcion' => $descr_inicial,
                                	'monto' => $actividad->cuota_inicial,
                                	'tipo' => 4,
                        	);
                        	$this->pagos_model->insert_pago_nuevo($pago);
			}
			// Fin comentario TODO AHG
			*/

			// Facturamos el valor mensual de la actividad
                	$descripcion .= 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;
                    	$valor = $actividad->precio;
                	if($actividad->descuento > 0){
				if ( $actividad->monto_porcentaje == 0 ) {
                    			$valor = $actividad->precio - $actividad->descuento;
                    			$descripcion .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'$ BECADOS</label> $ '.$valor;                    
				} else {
                    			$valor = $actividad->precio - ($actividad->precio * $actividad->descuento / 100);
                    			$descripcion .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'% BECADO</label> $ '.$valor;                    
				}
	 		} 
                	$descripcion .= '<br>';
	        	$des = 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;
                	if($actividad->descuento > 0){
				if ( $actividad->monto_porcentaje == 0 ) {
                    			$des .= '<label class="label label-info">'.$actividad->descuento.'$ BECADOS</label> $ '.$valor;
				} else {
                    			$des .= '<label class="label label-info">'.$actividad->descuento.'% BECADO</label> $ '.$valor;
				}
                	}
                	$des .= '<br>';

			// Inserto el pago de la actividad (tipo=4)
                	$pago = array(
                    		'sid' => $socio->Id,
                    		'tutor_id' => $socio->Id,
                    		'aid' => $actividad->Id,
                 		'generadoel' => $xhoy,
                    		'descripcion' => $des,
                    		'monto' => $valor,
                    		'tipo' => 4,
                    	);
			// Si tiene la actividad bonificada la doy por paga (estado=0)
                	if($pago['monto'] <= 0){                    
				$pago['estado'] = 0;
                    		$pago['pagadoel'] = $xahora;
                	}
                	$this->pagos_model->insert_pago_nuevo($pago);
	        } 

		// Si tiene familiares a cargo
	        if($cuota['familiares'] != 0){
			// Ciclo cada familiar
               		foreach ($cuota['familiares'] as $familiar) {
				// Busco las actividades de ese familiar
               			foreach($familiar['actividades']['actividad'] as $actividad){		               		
                    			$descripcion .= 'Cuota Mensual '.$actividad->nombre.' ['.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.'] - $ '.$actividad->precio;
					$valor = $actividad->precio;
                    			if($actividad->descuento > 0){
                    				if($actividad->monto_porcentaje == 0){
                        				$valor = $actividad->precio - $actividad->descuento;
                        				$descripcion .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'$ BECADOS</label> $ '.$valor;                    
						} else {
                        				$valor = $actividad->precio - ($actividad->precio * $actividad->descuento / 100);
                        				$descripcion .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'% BECADO</label> $ '.$valor;                    
						}
                    			}
                    			$descripcion .= '<br>';
	               			$des = 'Cuota Mensual '.$actividad->nombre.' ['.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.'] - $ '.$actividad->precio;
                    			if($actividad->descuento > 0){
                    				if($actividad->monto_porcentaje == 0){
                        				$des .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'$ BECADOS</label> $ '.$valor;                    
						} else {
                        				$des .= '&nbsp; <label class="label label-info">'.$actividad->descuento.'% BECADO</label> $ '.$valor;                    
						}
                    			}
                    			$des = '<br>';	 

					// Inserto el pago de la actividad del familia (tipo=4)
                    			$pago = array(
                        			'sid' => $familiar['datos']->Id,
                        			'tutor_id' => $socio->Id,
                        			'aid' => $actividad->Id,
                        			'generadoel' => $xhoy,
                        			'descripcion' => $des,
                        			'monto' => $valor,
                        			'tipo' => 4,
                        			);
                    			$this->pagos_model->insert_pago_nuevo($pago);
               			}
               		}
           	}

		// Cuota Excedente
           	if($cuota['excedente'] >= 1){
                	$descripcion .= 'Socio Extra (x'.$cuota['excedente'].') - $ '.$cuota['monto_excedente'].'<br>';
	         	$des = 'Socio Extra (x'.$cuota['excedente'].') - $ '.$cuota['monto_excedente'].'<br>';
			// Inserto el pago de la cuota excedente
                	$pago = array(
                    		'sid' => $socio->Id,    
                    		'tutor_id' => $socio->Id,                
                    		'aid' => 0,
                    		'generadoel' => $xhoy,
                    		'descripcion' => $des,
                    		'monto' => $cuota['monto_excedente'],
                    		'tipo' => 1,
                    		);
                		$this->pagos_model->insert_pago_nuevo($pago);
		}

		//financiacion de deuda
		$deuda_financiada = 0;
		$planes = $this->pagos_model->get_financiado_mensual($socio->Id);
		// Si tiene planes de financiacion activos
            	if($planes){
			// Ciclo cada plan
    			foreach ($planes as $plan) {                
                    		$descripcion .= 'Financiación de Deuda ('.$plan->detalle.' - Cuota: '.$plan->actual.'/'.$plan->cuotas.') - $ '.round($plan->monto/$plan->cuotas,2).'<br>';
                    		$des = 'Financiación de Deuda ('.$plan->detalle.' - Cuota: '.$plan->actual.'/'.$plan->cuotas.') - $ '.round($plan->monto/$plan->cuotas,2).'<br>';
				// Inserto el pago del plan de financiacion (tipo=3)
                    		$pago = array(
                        		'sid' => $socio->Id,  
                        		'tutor_id' => $socio->Id,                  
                        		'aid' => 0,
                        		'generadoel' => $xhoy,
                        		'descripcion' => $des,
                        		'monto' => round($plan->monto/$plan->cuotas,2),
                        		'tipo' => 3,
                        		);
                    		$this->pagos_model->insert_pago_nuevo($pago);

    				$deuda_financiada = $deuda_financiada + round($plan->monto/$plan->cuotas,2);
                		$this->pagos_model->update_cuota($plan->Id);
    			}
                	$deuda_financiada = round($deuda_financiada,2);
            	}else{
                	$deuda_financiada = 0;                
            	}
            	//end financiacion de deuda	

		// Obtiene el saldo total de la ultima fila de facturacion!!!
	        $total = $this->pagos_model->get_socio_total($socio->Id);
		// Le agrega la cuota facturada este mes al total del saldo
	        $total = $total - ($cuota['total']);
		$data = array(
			"sid" => $socio->Id,
			"date" => $xhoy,
			"descripcion" => $descripcion,
			"debe" => $cuota['total'],
			"haber" => '0',
			"total" => $total
		);

            	$deuda = $this->pagos_model->get_deuda($socio->Id);

		// Inserta el registro de facturacion del mes
		$this->pagos_model->insert_facturacion($data);

		// Actualizo en facturacion_cron el asociado facturado
		$this->pagos_model->update_facturacion_cron($xperiodo,3, 1, $cuota['total']);

		// Si el asociado tiene cargado debito automatico de tarjeta inserto el pago 
	
		$debtarj=$this->debtarj_model->get_debtarj_by_sid($socio->Id);
		if ( $debtarj ) {
			$id_debito = $debtarj->id;
			$ult_periodo = $debtarj->ult_periodo_generado;
			$ult_fecha = $debtarj->ult_fecha_generacion;
			if ( $ult_periodo == $xperiodo ) {
				$debito = $this->debtarj_model->get_debitos_by_socio($id_debito);
				if ( $debito ) {
					$fecha_deb = $debito->fecha_debito;
					$importe = $debito->importe;
					if ( $fecha_deb == $ult_fecha ) {
	
                				// Le resta el pago debitado a la tarjeta al saldo 
                				$saldo_cc = $total + $importe;
						$tarjeta=$this->tarjeta_model->get($debtarj->id_marca);
						$descripcion = "Pago por Debito en Tarjeta $tarjeta->descripcion";
                				$data = array(
                        				"sid" => $socio->Id,
							"date" => $xhoy,
                        				"descripcion" => $descripcion,
                        				"debe" => '0',
                        				"haber" => $importe,
                        				"total" => $saldo_cc
                				);

                                		$this->pagos_model->insert_facturacion($data);
                                		$this->pagos_model->registrar_pago2($socio->Id,$importe);
            					$txt = date('H:i:s')." Registre debito tarjeta para el asociado $socio->Id por un monto de $importe \n";
            					fwrite($log, $txt);            
						// Actualizo en facturacion_cron con el asociado debitado
						$this->pagos_model->update_facturacion_cron($xperiodo,4, 1, $importe);
					} else {
            					$txt = date('H:i:s')." El asociado $socio->Id tiene debito en tarjeta pero la fecha de ultimo debito no coincide con el movimiento \n";
            					fwrite($log, $txt);            
			}
				} else {
            				$txt = date('H:i:s')." El asociado $socio->Id tiene debito en tarjeta pero no encontre el registro en socios_debitos \n";
            				fwrite($log, $txt);            
				}
				
			} else {
            			$txt = date('H:i:s')." El asociado $socio->Id tiene debito en tarjeta pero no coincide el ultimo periodo generado \n";
            			fwrite($log, $txt);            
			
			}
		}
			

		// armo mail
		$mail = $this->socios_model->get_resumen_mail($socio->Id);

            	$cuota3 = $mail['resumen'];            
            	$cuerpo = '<p><img src="http://clubvillamitre.com/images/vm-head.png" alt="" /></p>';
            	$cuerpo .= '<h3><strong>Titular:</strong> '.$cuota3['titular'].'</h3>';

            	if($deuda != 0){
                	if($deuda < 0 ){
                    		$cuerpo .= "Su deuda actual es de $ ".abs($deuda);
                	}else{
                    		$cuerpo .= "Usted posee un saldo a favor de $ ".abs($deuda);                
                	}                
            	}
            	$cuerpo .= "<h4>Cuota Mensual</h4>";
            	$cuerpo .= '<h5><strong>Categor&iacute;a:</strong> '.$cuota3['categoria'].'</h5>';
            
            	if($cuota3['categoria'] == 'Grupo Familiar'){
           
                	$cuerpo .= '<h5><strong>Integrantes</strong></h5><ul>';
                	foreach ($cuota3['familiares'] as $familiar) {          
                    		$cuerpo .= '<li>'.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.'</li>';                    
                	}                    
                	$cuerpo .= '</ul>';            
            	}
            
            	$cuerpo .= '<table class="table table-hover" width="100%;" border="0" style="font-family: "Arial";">
                		<thead>
                    		  <tr style="background-color: #666 !important; color:#FFF;">                        
                        	    <th style="padding:5px;" align="left">Descripci&oacute;n</th>
                        	    <th style="padding:5px;" align="left">Monto</th>                        
                    		  </tr>
                		</thead>
                	    <tbody>
                    	    <tr style="background: #CCC;">
                        	<td style="padding: 5px;">Cuota Mensual '.$cuota3['categoria'].'</td>
                        	<td style="padding: 5px;">$ '.$cuota3['cuota'].'</td>
                    	    </tr>';
	
		foreach ($cuota3['actividades']['actividad'] as $actividad) {
                    $cuerpo .= '<tr style="background: #CCC;">
                        	  <td style="padding: 5px;">Cuota Mensual '.$actividad->nombre.'</td>
                        	  <td style="padding: 5px;">$ '.$actividad->precio.'</td>
                    		</tr>';                        
                } 
		if($cuota3['familiares'] != 0){
			foreach ($cuota3['familiares'] as $familiar) {
				foreach($familiar['actividades']['actividad'] as $actividad){                           
                            		$cuerpo .= '<tr style="background: #CCC;">                    
                                			<td style="padding: 5px;">Cuota Mensual '.$actividad->nombre.' ['.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.' ]</td>
                                			<td style="padding: 5px;">$ '.$actividad->precio.'</td>
                            			    </tr>';
                            	}                                   
                        }
		}
		if($cuota3['excedente'] >= 1){
			$cuerpo .='<tr style="background: #CCC;">                    
                                	<td style="padding: 5px;">Socio Extra (x'.$cuota3['excedente'].')</td>
                                	<td style="padding: 5px;">$ '.$cuota3['monto_excedente'].'</td>
                                   </tr>';                        
		}
		if($cuota3['financiacion']){
			foreach ($cuota3['financiacion'] as $plan) {                 
				$cuerpo .= '<tr style="background: #CCC;">                    
                                		<td style="padding: 5px;">Financiación de Deuda ('.$plan->detalle.')</td>
                                		<td style="padding: 5px;">$ '.round($plan->monto/$plan->cuotas,2).'</td>
                            		</tr>';
                        }
		}
		if($cuota3['descuento'] != 0.00){                        
                        $cuerpo .= '<tr style="background: #CCC;">                    
                                	<td style="padding: 5px;">Descuento sobre cuota social</td>
                                	<td style="padding: 5px;">'.$cuota3['descuento'].'%</td>
                            	</tr>';                        
		}
                $cuerpo .= '
                	</tbody>
                	<tfoot>
                    	<tr>                        
                        	<th>Total</th>
                        	<th>$ '.$cuota3['total'].'</th>                        
                    	</tr>
                	</tfoot>
            		</table>';
            
            	// genero cupon para cuenta digital
            	$this->load->model('pagos_model');
            	$cupon = $this->pagos_model->get_cupon($mail['sid']);
            	if($cupon->monto == $cuota3['total']){
                	$cupon = base_url().'images/cupones/'.$cupon->Id.'.png';
            	}else{
                	$cupon = $this->cuentadigital($mail['sid'],$cuota3['titular'],$cuota3['total']);
                	if($cupon && $mail['sid'] != 0){
                    		$cupon_id = $this->pagos_model->generar_cupon($mail['sid'],$cuota3['total'],$cupon);
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

            	if($mail['deuda'] < 0){
                	$total = ($mail['deuda']*-1);
                	$cuerpo .= '<h3>Su deuda total con el Club es de: $ '.$total.'</h3>';

                    	// Aca grabo el archivo para mandar a cobrar a COL
			$col_periodo=$xperiodo;
			$col_socio=$socio->Id;
			$col_dni=$socio->dni;
			$col_apynom=$socio->apellido." ".$socio->nombre;
			$col_importe=$mail['deuda']*(-1);
			$col_fecha_lim=$xlim1;
			$col_recargo="0";
			$col_fecha_lim2=$xlim2;
            		$txt = '"'.$col_periodo.'","'.$col_socio.'","'.$col_dni.'","'.$col_apynom.'","'.$col_importe.'","'.$col_fecha_lim.'","'.$col_recargo.'","'.$col_fecha_lim2.'"'."\r\n";
            		fwrite($col, $txt);            

			// Actualizo en facturacion_cron el asociado facturado
			$this->pagos_model->update_facturacion_cron($xperiodo,5, 1, $col_importe);

			// Grabo en el archivo de facturacion_col
			$facturacion_col = array(
				'id' => 0,
                        	'sid' => $col_socio,
                        	'periodo' => $col_periodo,
                        	'importe' => $col_importe,
                        	'cta_socio' => 0,
                        	'actividades' => 0
                    	);
                    	$this->pagos_model->insert_facturacion_col($facturacion_col);

            	}else{
			if ( $mail['deuda'] > 0 ) {
                		$total = ($mail['deuda']);
                		$cuerpo .= '<h3>Usted posee un saldo a favor de: $ '.$total.'</h3>';                
			} else {
                		$cuerpo .= '<h3>Usted esta al día con sus pagos </h3>';                
			}
            	}

            	$email = array(
                    'email' => $mail['mail'],
                    'body' => $cuerpo
                );
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            	if(preg_match($regex, $mail['mail'])){
                	$this->db->insert('facturacion_mails',$email);
            	}

		// fin armado mail

	 
		// Registro pago2 verificar.....
            	$this->pagos_model->registrar_pago2($socio->Id,0);
	
		// Actualizado el estado de socios como facturado (facturado=1)
            	$this->db->where('Id', $socio->Id);
            	$this->db->update('socios', array('facturado'=>1));

		// Registro en el log que asociado facture
            	$txt = date('H:i:s').": Socio #".$socio->Id." DNI=".$socio->dni."-".TRIM($socio->apellido).", ".TRIM($socio->nombre)." facturado \n";
            	fwrite($log, $txt);            

	}
	// Actualizo en la tabla facturacion_cron que termino el proceso de facturacion
        $this->db->like('date',$xhoy,'after');
        $this->db->update('facturacion_cron', array('en_curso'=>0));
	// Registro en el log que el proceso de facturacion termino
        $txt = date('H:i:s').": Cron Finalizado \n";
        fwrite($log, $txt);            
        fclose($log);      
        fclose($col);      

	$totales=$this->pagos_model->get_facturacion_cron($xperiodo);
	if ( $totales ) {
		$info_total="Los totales facturados son: <br> Socios Suspendidos: $totales->socios_suspendidos <br> Socios Pasados a Mayores: $totales->socios_cambio_mayor <br> Socios Facturados: $totales->socios_facturados por un total de $ $totales->total_facturado <br> Socios en Debito Tarjeta: $totales->socios_debito por un total de $ $totales->total_debito <br> Mandado a Cobranza COL: $totales->socios_col socios por un total de $ $totales->total_col";
	} else {
		$info_total="No encontre registro en facturacion_cron !!!!";
	}

	// Me mando email de aviso que el proceso termino OK
        mail('agonzalez.lacoope@gmail.com', "El proceso de Facturación Finalizó correctamente.", "Este es un mensaje automático generado por el sistema para confirmar que el proceso de facturación finalizó correctamente ".$xahora."\n".$info_total);
	}

    public function email_a_suspendidos()
    {
        $this->load->model('socios_model');
        $this->db->where('estado',1);
        $this->db->where('suspendido',1);
        $query = $this->db->get('socios');
        $socios_suspendidos = $query->result();
        //var_dump($socios_suspendidos);die;
        foreach ($socios_suspendidos as $socio) {
            $mail = $this->socios_model->get_resumen_mail($socio->Id);
            $total = ($mail['deuda']*-1);

            if($total <= 0){ continue; }

            $cuerpo = '<meta charset="UTF-8"><p><img src="http://clubvillamitre.com/images/vm-head.png" alt="" /></p>';
            $cuerpo .= '<h3><strong>Titular:</strong> '.$socio->nombre.' '.$socio->apellido.'</h3>';
            $cuerpo .= '<div style="padding:20px;background-color: #fdefee; border-color: #fad7db; color: #b13d31;">USUARIO SUSPENDIDO POR FALTA DE PAGO</div>';

            $this->db->where('sid', $socio->Id);
            $this->db->order_by('date', 'asc');
            $query = $this->db->get('facturacion');
            if( $query->num_rows() == 0 ){ continue; }
            $facturacion = $query->result();
            
            $cuerpo .= '<table width="100%" border="1">';
            $cuerpo .= '<thead><tr><th>Fecha</th><th>Descripcion</th><th>Debe</th><th>Haber</th><th>Total</th></tr></thead><tbody>';


            foreach ($facturacion as $f) {            
                $cuerpo .= '<tr><td>'.date('d-m-Y',strtotime($f->date)).'</td><td>'.$f->descripcion.'</td><td align="center">$ '.$f->debe.'</td><td align="center">$ '.$f->haber.'</td><td align="center">$ '.$f->total*(-1).'</td></tr>';
            }

            $cuerpo .= '</tbody></table>';
            $cuerpo .= '';

            $cuerpo .= '<h3>Su deuda total con el Club es de: $ '.$total.'</h3>';

            $cupon = $this->cuentadigital($socio->Id,$socio->nombre.' '.$socio->apellido,$total);
            if($cupon && $mail['sid'] != 0){
                $cupon_id = $this->pagos_model->generar_cupon($socio->Id,$total,$cupon);
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

            $cuerpo .= '<br><br><img src="'.$cupon.'">';

            $email = array(
                    'email' => $socio->mail,
                    'body' => $cuerpo
                );
            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            if(preg_match($regex, $socio->mail)){
                $this->db->insert('facturacion_mails',$email);
            }
        }
    }

    public function suspender($log)
    {
        $this->load->model('socios_model');
        $socios = $this->socios_model->get_socios_pagan();
	$cant = 0 ;
        foreach ($socios as $socio) {
            $this->db->where('tutor_id', $socio->Id);
            $this->db->where('tipo', 1);
            $this->db->where('estado', 1);
            $query = $this->db->get('pagos');
            if( $query->num_rows() >= 5 ){ 
		$meses_atraso=$query->num_rows();
            	$this->db->where('tutor_id', $socio->Id);
            	$this->db->where('tipo', 1);
            	$this->db->where('pagadoel is not NULL');
            	$this->db->select('tutor_id, MAX(pagadoel) maxfch, DATEDIFF(MAX(pagadoel),CURDATE()) dias_ultpago');
    		$this->db->group_by('tutor_id');
            	$query = $this->db->get('pagos');
		$isusp=0;
		if ( $query->num_rows() > 0 ) {
                	$ult_pago = $query->row();
			$ds_ult = $ult_pago->dias_ultpago;
                	$query->free_result();                
			if ( $ds_ult < -150 ) {
				$isusp=1;
			}
		} else {	
			$isusp=1;
		}
		if ( $isusp == 1 ) {
                	$this->db->where('Id',$socio->Id);
                	$this->db->update('socios', array('suspendido'=>1));
                	$txt = date('H:i:s').": Socio Suspendido #".$socio->Id." ".TRIM($socio->apellido).", ".TRIM($socio->nombre)." DNI= ".$socio->dni." atraso de ".$meses_atraso." ultimo pago ".$ds_ult. " \n";
                	fwrite($log, $txt);   
			$cant++;
		}
            }
        }        
	return $cant;
    }

	function pagos(){
		$this->load->model("pagos_model");

		// Si me vino una fecha en el URL fuerzo la generacion de esa fecha en particular sin controlar cron
        	if ($this->uri->segment(3)) {
			echo "asigno fecha de parametro \n";
			$ayer = $this->uri->segment(3);
			echo "ayer = $ayer \n";
		} else {
			echo "tomo el date\n";
			$ayer = date('Ymd',strtotime("-1 day"));
			$fecha = date('Y-m-d');
			if($this->pagos_model->check_cron_pagos()){exit('Esta tarea ya fue ejecutada hoy.');}	 
		}


		// Veo si tiene algun condicional enviado en la URL para hacer o no generacion
		// Sino viene segmento 4 (default) genera todo
		// Si viene en segmento 4 CD o TODO genero Cuenta Digital
		$ctrl_gen="";
		if ( $this->uri->segment(4) ) {
			$ctrl_gen=$this->uri->segment(4);
			echo "Controlo generacion vino -> $ctrl_gen";
			if ( !($ctrl_gen == "TODO" || $ctrl_gen == "CD" || $ctrl_gen = "COL") ) {
				echo "EL PARAMETRO PARA GENERAR ES INCORRECTO";
				exit;
			}
		} else {
			echo "Generacion default busca TODO\n";
			$ctrl_gen="TODO";
		}
		
		if ( $ctrl_gen == "TODO" || $ctrl_gen == "CD" ) {
			// Busco los pagos del sitio de Cuenta Digital
			$pagos = $this->get_pagos($ayer);
			// Si bajo algo del sitio
			if($pagos) {
				// Ciclo los pagos encontrados
				foreach ($pagos as $pago) {
					$data = $this->pagos_model->insert_pago($pago);
					$this->pagos_model->registrar_pago2($pago['sid'],$pago['monto']);
				}
			}
		}

		
		if ( $ctrl_gen == "TODO" || $ctrl_gen == "COL" ) {
			echo "genero COL";
			if ( $this->uri->segment(5) ) {
				echo "vino parametro 5 = ".$this->uri->segment(5)." \n";
				$suc_filtro=$this->uri->segment(5);
			} else {
				$suc_filtro=0;
			}
			// Busco los pagos registrados en COL
			$pagos_COL = $this->get_pagos_COL($ayer,$suc_filtro);
			// Si bajo algo del sitio
			if($pagos_COL) {
				// Ciclo los pagos encontrados
				foreach ($pagos_COL as $pago) {
					// Si vino en la URL que genera solo un local descarto el resto
					$data = $this->pagos_model->insert_pago_col($pago);
					$this->pagos_model->registrar_pago2($pago['sid'],$pago['monto']);
				}
			}
		}

		if (!$this->uri->segment(3)) {
			$this->pagos_model->insert_pagos_cron($fecha); 
		}
	}

	function get_pagos($fecha) {           
        	$this->config->load('cuentadigital');    
        	$url = 'http://www.cuentadigital.com/exportacion.php?control='.$this->config->item('cd_control');;
        	$url .= '&fecha='.$fecha;	    
		if($a = file_get_contents($url)){
			$data = explode("\n",$a);
			$pago = array();
			foreach ($data as $d) {		   	  		 
				if($d){
					$entrantes = explode('/', $d);
					$dia = substr($entrantes[0], 0,2);
					$mes = substr($entrantes[0], 2,2);
					$anio = substr($entrantes[0], 4,4);
					$hora = substr($entrantes[1], 0,2);
					$min = substr($entrantes[1], 2,2);
					$pago[] = array(
			   			"fecha" => date('d-m-Y',strtotime($entrantes[0])),
			   			"hora" => $hora.':'.$min,
			   			"monto" => $entrantes[2],
			   			"sid" => $entrantes[3],
			   			"pid" => $entrantes[4]
			   		);
                    			$p = array(
                            			"fecha" => date('Y-m-d',strtotime($entrantes[0])),
                            			"hora" => $hora.':'.$min,
                            			"monto" => $entrantes[2],
                            			"sid" => $entrantes[3],
                            			"pid" => $entrantes[4]
                        			);
                    			$this->pagos_model->insert_cuentadigital($p);
				}
			}
			return $pago;
		} else {
			if($a === FALSE) {
                		mail("soporte@hostingbahia.com.ar","Fallo en Cron VM",date('Y-m-d H:i:s'));
                		mail("agonzalez.lacoope@gmail.com","Fallo en Cron VM",date('Y-m-d H:i:s'));
                		exit();
			}
			return false;
		}
	}

	function get_pagos_COL($fecha,$suc_filtro) {           
                $url = 'https://extranet.cooperativaobrera.coop/xml/Consorcios/index/30553537602/13809/'.$fecha;
                if($a = file_get_contents($url)){
			$data = explode("\n",$a);
			$cont=0;
			$serial=0;
			$pago = array();
			foreach ($data as $linea) {		   	  		 
				if ( $linea ) {
					$campos = explode(',', $linea);

					$xnro_cupon=str_replace('"','',$campos[2]);
					$suc=substr($xnro_cupon,0,4);
					$nro_cupon=substr($xnro_cupon,4);
					$nro_socio=str_replace('"','',$campos[3]);
					$importe=str_replace('"','',$campos[6]);
					$importe=$importe/100;
					$xfecha1=str_replace('"','',$campos[5]);
					$xfecha=substr($xfecha1,0,10);
					$fecha_pago=substr($xfecha,0,4)."-".substr($xfecha,5,2)."-".substr($xfecha,8,2);
					$fecha_pago2=substr($xfecha,8,2)."-".substr($xfecha,5,2)."-".substr($xfecha,0,4);
			 		$periodo=substr($xfecha,0,4).substr($xfecha,5,2);
					$hora=date('H:m');
            
					//echo $xfecha1."#".$xfecha."#".$periodo."#".$fecha_pago2."#".$nro_cupon."#".$nro_socio."#".$fecha_pago."#".$suc."#".$hora."#".$importe."\n";
					// Si viene una sucursal de filtro salteo las sucursales distintas
					if ( $suc_filtro > 0 ) {
						if ( $suc != $suc_filtro ) {
							continue;
						}
					}

					$pago[] = array(
						"fecha" => date('d-m-Y',strtotime($fecha_pago2)),
						"hora" => $hora,
						"monto" => $importe,
						"sid" => $nro_socio,
						"pid" => $nro_cupon
					);
					$p = array(
						"sid" => $nro_socio,
						"periodo" => $periodo,
						"fecha_pago" => date('Y-m-d',strtotime($fecha_pago2)),
						"suc_pago" => $suc,
						"nro_cupon" => $nro_cupon,
						"importe" => $importe
					);

					$this->pagos_model->insert_cobranza_col($p);
				}
			}
			return $pago;
		} else {
			return false;
		}
	}

    function debito_nuevacard() {
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
        $file_tot = '/tmp/CVMCOOP'.$mes.$ano.'TOT.TXT';
        $ft=fopen($file_tot,'w');
        $file = '/tmp/CVMCOOP'.$mes.$ano.'.TXT';
        $f=fopen($file,'w');

//TODO generar facturacion del mes siguiente para los que tienen debito...
        $debtarjs = $this->debtarj_model->get_debtarjs();
	foreach ( $debtarjs AS $debtarj ) {
		$id_marca=$debtarj->id_marca;
		if ( $id_marca == 2 || $id_marca == 3 ) {
			$socio=$this->socios_model->get_socio($debtarj->sid);
// TODO tomar el importe de la facturacion que le corresponde
			$importe="100.00";
			$linea=$nro_comercio.",".$debtarj->nro_tarjeta.",".$socio->apellido.", ".$socio->nombre.",0,".$fecha.",".$importe.",DAU\n";
			fwrite($f,$linea);
			$cont++;
			$total=$total+$importe;
			fwrite($log,$socio->Id." ".$socio->apellido.", ".$socio->nombre." monto :".$importe."\n");
		}
	}
	$linea="FECHA :".$fecha."\n";
	fwrite($ft,$linea);
	$linea="CANTIDAD DE REGISTROS :".$cont."\n";
	fwrite($ft,$linea);
	$linea="TOTAL($) :".$total."\n";
	fwrite($ft,$linea);

	fwrite($log,"Se genero un archivo con ".$cont." debitos por un total de $ ".$total."\n");

	}

    function debito_visa() {
	$exitoso=FALSE;
        $this->load->model('tarjeta_model');
        $this->load->model('debtarj_model');
        $this->load->model('socios_model');
	// Visa esta grabada con id=1
	$nro_comercio=$this->tarjeta_model->get(1);

	$cont=0;
	$total=0;
	$fecha = date('d/m/Y');
        $mes = date('m');
        $ano = date('y');

        $fl = './application/logs/visa-'.date('Y').'-'.date('m').'.log';
        if( !file_exists($fl) ){
            $log = fopen($fl,'w');
        }else{
            $log = fopen($fl,'a');
        }
        $file_tot = '/tmp/CVMVISA'.$mes.$ano.'TOT.TXT';
        $ft=fopen($file_tot,'w');
        $file = '/tmp/CVMCOOP'.$mes.$ano.'.TXT';
        $f=fopen($file,'w');

//TODO generar facturacion del mes siguiente para los que tienen debito...
        $debtarjs = $this->debtarj_model->get_debtarjs();
	foreach ( $debtarjs AS $debtarj ) {
		$id_marca=$debtarj->id_marca;
		if ( $id_marca == 2 || $id_marca == 3 ) {
			$socio=$this->socios_model->get_socio($debtarj->sid);
// TODO tomar el importe de la facturacion que le corresponde
			$importe="100.00";
			$linea=$nro_comercio.",".$debtarj->nro_tarjeta.",".$socio->apellido.", ".$socio->nombre.",0,".$fecha.",".$importe.",DAU\n";
			fwrite($f,$linea);
			$cont++;
			$total=$total+$importe;
			fwrite($log,$socio->Id." ".$socio->apellido.", ".$socio->nombre." monto :".$importe."\n");
		}
	}
	$linea="FECHA :".$fecha."\n";
	fwrite($ft,$linea);
	$linea="CANTIDAD DE REGISTROS :".$cont."\n";
	fwrite($ft,$linea);
	$linea="TOTAL($) :".$total."\n";
	fwrite($ft,$linea);

	fwrite($log,"Se genero un archivo con ".$cont." debitos por un total de $ ".$total."\n");

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

    public function intereses()
    {
        if(date('d') != 20){ die(); }
        $this->load->model('general_model');
        $config = $this->general_model->get_config();
        if($config->interes_mora > 0){
            $this->load->model("socios_model");            
            $this->load->model('pagos_model');
            $socios = $this->socios_model->get_socios_pagan();
            foreach ($socios as $socio) {
                $cuota = $this->pagos_model->get_monto_socio($socio->Id);
                $total = $this->pagos_model->get_socio_total($socio->Id);
                if($total*-1 > $cuota['total']){
                    $debe = $cuota['total'] * $config->interes_mora /100;
                    
                    $total = $total - $debe;
                    $facturacion = array(
                        'sid' => $socio->Id,
                        'descripcion'=>'Intereses por Mora',
                        'debe'=>$debe,
                        'haber'=>0,
                        'total'=>$total
                    );
                    $this->pagos_model->insert_facturacion($facturacion);

                    $pago = array(
                        'sid' => $socio->Id, 
                        'tutor_id' => $socio->Id,
                        'aid' => 0, 
                        'generadoel' => date('Y-m-d'),
                        'descripcion' => "Intereses por Mora",
                        'monto' => $debe,
                        'tipo' => 2,
                        );                    
                    $this->pagos_model->insert_pago_nuevo($pago);
                }
            }            
        }
    }

    public function facturacion_mails()
    {
        $this->load->database();
        error_log( date('d/m/Y G:i:s').": Buscando correos para enviar... \n", 3, "cron_envios.log");
        $this->db->where('estado',0);
        $query = $this->db->get('facturacion_mails');
        if($query->num_rows() == 0){ 
            error_log( date('d/m/Y G:i:s').": No se encontraron correos \n", 3, "cron_envios.log");
            return false;
        }else{
            error_log( date('d/m/Y G:i:s').": Se encontraron ".$query->num_rows()." correos. Enviando... \n", 3, "cron_envios.log");
            $this->load->library('email');
            foreach ($query->result() as $email) {
                $this->email->from('pagos@clubvillamitre.com','Club Villa Mitre');
                $this->email->to($email->email);                 

                $this->email->subject('Resumen de Cuenta');                
                $this->email->message($email->body);  

                if($this->email->send()){
                    error_log( date('d/m/Y G:i:s').": Enviado: ".$email->email." \n", 3, "cron_envios.log");
                    $this->db->where('Id',$email->Id);
                    $this->db->update('facturacion_mails',array('estado'=>1));
                }
                //if(preg_match($regex, $mail['mail'])){
                //}
                //mail
            }
            error_log( date('d/m/Y G:i:s').": Envio Finalizado \n", 3, "cron_envios.log");
            
        }
    }

    public function pagos_nuevos($value='')
    {
        return false; die;
        $this->load->database();
        $this->load->model('pagos_model');
        $this->db->where('estado',1);        
        $query = $this->db->get('socios');
        $socios = $query->result();
        foreach ($socios as $socio) {            
            $total = $this->pagos_model->get_socio_total($socio->Id);            
            $pago = array(
                'sid' => $socio->Id, 
                'tutor_id' => $sid,
                'aid' => 0, 
                'generadoel' => date('Y-m-d'),
                'descripcion' => "Deuda Anterior",
                'monto' => $total*-1,
                'tipo' => 1,
                );
            if($total*-1 < 0){
                $pago['descripcion'] = 'A favor';
                $pago['tipo'] = 5;
            }
            $this->pagos_model->insert_pago_nuevo($pago);
            var_dump($pago);
            echo '<hr>';
        }
    }

    public function correccion()
    {
        return false; die();
        $this->load->database();
        $this->load->model('pagos_model');
        $this->db->where('estado',1);
        $this->db->where('actual >',1);
        $query = $this->db->get('financiacion');
        $f = $query->result();
        foreach ($f as $ff) {
            $haber = ($ff->actual - 1) * ($ff->monto/$ff->cuotas);
            $total = $this->pagos_model->get_socio_total($ff->sid);
            $total = $total + $haber;
            $facturacion = array(
                'sid' => $ff->sid, 
                'descripcion' => 'Corrección de Financiación de Deuda', 
                'debe'=>0,
                'haber'=>$haber,
                'total'=>$total
                );

            $this->db->insert('facturacion',$facturacion);
            var_dump($facturacion);
            echo '<hr>';
        }
    }  

    public function control()
    {
        $this->load->model("pagos_model");
        $this->load->model("socios_model");
        $socios = $this->socios_model->listar(); //listamos todos los socios activos
        foreach ($socios as $socio) {    
            $total = $this->pagos_model->get_socio_total($socio['datos']->Id);
            $total2 = $this->pagos_model->get_socio_total2($socio['datos']->Id);
            if($total + $total2 != 0 && $total <= 0){
                echo $socio['datos']->Id.' | '.$total.' | '.$total2.'<br>';            
            }
        }
    }  
}
