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

    function depuracion_files(){ // esta funcion depura archivos viejos
        $this->load->model("pagos_model");
	$cupones = $this->pagos_model->get_cupones_old();
	$cant=0;
	foreach ( $cupones as $cupon ) {
		$cant++;
		if ( $cant < 30 ) {
                	$cupon = 'images/cupones/'.$cupon->id.'.png';
			unlink($cupon);
		}
        }
	echo "Hay $cant cupones para depurar";
    }

	function facturacion() { // esta funcion genera la facturacion del mes el dia
        	if ($this->uri->segment(3)) {
			$xhoy = $this->uri->segment(3);
		} else {
			$xhoy=date('Y-m-d');
		}
		echo $xhoy;


		$this->load->model("general_model");
		// Ciclo las entidades para hacer la facturacion de cada una
	 	$entidades = $this->general_model->get_ents();
		foreach ($entidades as $entidad) {		
			$this->factura_entidad($xhoy, $entidad);
		}
	}
	

	function factura_entidad($xhoy, $entidad) { // esta funcion factura una entidad

		$id_entidad = $entidad->id;
		$this->load->model("general_model");
		$ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;

		// Periodo y fechas del proceso.....
		$xanio=date('Y', strtotime($xhoy));
		$xmes=date('m', strtotime($xhoy));
		$xperiodo=date('Ym', strtotime($xhoy));
		$xahora=date('Y-m-d G:i:s', strtotime($xhoy));
		$xlim1=date('Y-m-',strtotime($xhoy)).'25';
		$xlim2=date('Y-m-t', strtotime($xhoy));

		$this->load->model("pagos_model");
		$this->load->model("socios_model");
		$this->load->model("debtarj_model");
		$this->load->model("tarjeta_model");

        	//log

        	$base_ent = './entidades/'.$ent_dir;        
        	$file = './entidades/'.$ent_dir.'/logs/facturacion-'.$xanio.'-'.$xmes.'.log';        
        	$file_col = './entidades/'.$ent_dir.'/logs/cobranza_col-'.$xanio.'-'.$xmes.'.csv';        

        	if( !file_exists($base_ent) ){
            		// No existe el directorio base de la entidad a facturar
            		echo  date('H:i:s').": El directorio base de la entidad no EXISTE! $base_ent \n";
			exit();
		}

        	if( !file_exists($file) ){
            		echo "creo";
            		$log = fopen($file,'w');
            		$col = fopen($file_col,'w');
        	} else {
            		echo "existe";
            		$log = fopen($file,'a');
            		$col = fopen($file_col,'a');
        	}

            	fwrite($log, date('H:i:s').' - Procesando '.$id_entidad.'-'.$entidad->descripcion.'\n');                        
        	//chequeamos el estado del cron
        	if(!$cron_state = $this->pagos_model->check_cron($id_entidad, $xperiodo)){
            		//el cron ya finalizó
            		$txt = date('H:i:s').": Intento de ejecución de Cron Finalizado! \n";
            		fwrite($log, $txt);            
            		exit();
        	}
  
		if($cron_state == 'iniciado'){
            		$txt = date('Y-m-d H:i:s').": Inicio de Cron... \n";
            		fwrite($log, $txt);                        

            		$soc_susp=$this->suspender($id_entidad, $log); // suspendemos socios que deban mas de 4 meses de cuota social

	    		// Actualizo el registro de facturacion_cron con los socios suspendidos
	    		$this->pagos_model->update_facturacion_cron($id_entidad,$xperiodo,1,$soc_susp,0);

			// Limpio tabla de envio de emails
            		$this->db->where('id_entidad', $id_entidad); 
            		$this->db->delete('facturacion_mails');
            		fwrite($log, date('H:i:s').' - Truncate mails\n');                        

            		$this->db->where('id_entidad', $id_entidad); 
            		$this->db->update('socios',array('facturado'=>0)); //establecemos todos los socios como no facturados
            		fwrite($log, date('H:i:s').' - Indicador facturado en 0 \n');                        

			$this->load->model('general_model');
        		$cat_menor = $this->general_model->get_cat_tipo($id_entidad, "m");

			if ( $cat_menor ) {
    				$cumpleanios = $this->socios_model->get_cumpleanios($id_entidad); //buscamos los que cumplen 18 años
				$cump=0;
				if ( $cumpleanios ) {
    					foreach ($cumpleanios as $menor) {
    						if ( $this->socios_model->actualizar_menor($id_entidad, $menor->id) ) {
							//los quitamos del grupo familiar y cambiamos la categoria a mayor
                					$txt = date('H:i:s').": Actualización de categoría socio a mayor #".$menor->id.'-'.$menor->apellido.', '.$menor->nombre." \n";
                					fwrite($log, $txt);   
							$cump++;
						} else {
                					$txt = date('H:i:s').": ERROR NO EXISTE CATEGORIA M para mayores";
						}
    					}
				}
            			fwrite($log, date('H:i:s').' - Cambio de categoria mayor \n');                        
			} else {
            			fwrite($log, date('H:i:s').' - ERROR NO EXISTE CATEGORIA m para menores \n');
			}

	    		// Actualizo el registro de facturacion_cron con los socios que cambiaron de categoria por mayoria de edad
	    		$this->pagos_model->update_facturacion_cron($id_entidad,$xperiodo,2,$cump,0);

        	} else if($cron_state == 'en_curso'){
            		$txt = date('H:i:s').": Reanudando Cron... \n";
            		fwrite($log, $txt);
        	}
  

		// Busco los socios que tienen que pagar
		$socios = $this->socios_model->get_socios_pagan($id_entidad, true);
		// Si no encontre ninguno logeo y corto
        	if(!$socios){ 
            		$txt = date('H:i:s').": No se encontraron socios a facturar \n";
            		fwrite($log, $txt);
            		exit(); 
        	} else {
			// Logeo la cantidad de total de asociados encontrados para facturar
            		$txt = date('H:i:s').": Se encontraron ".count($socios)." socios a facturar \n";
            		fwrite($log, $txt);            
        	}

		// Ciclo los asociados a facturar
		foreach ($socios as $socio) {		
			// Busco el valor de la cuota social a pagar
			$cuota = $this->pagos_model->get_monto_socio($socio->id);

			// Si tiene categoria de NO SOCIO no genero cuota
			$descripcion = '<strong>Categoría:</strong> '.$cuota['categoria'];
			if ( $cuota['categ_tipo'] != 'N' ) {
				// Si es un grupo familiar detallo los integrantes
				if($cuota['categoria'] == 'Grupo Familiar' || $cuota['categoria'] == 'Tutor'){
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
                			'sid' => $socio->id, 
                			'tutor_id' => $socio->id,
                			'id_entidad' => $id_entidad,
                			'aid' => 0, 
                			'generadoel' => $xahora,
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

				// Facturamos el valor mensual de la actividad
                		$descripcion .= 'Cuota Mensual '.$actividad->nombre.' - $ '.$actividad->precio;
                    		$valor = $actividad->precio;
                		if($actividad->descuento > 0){
					if ( $actividad->monto_porcentaje == 0 ) {
						if ( $actividad->precio > 0 ) {
                    					$valor = $actividad->precio - $actividad->descuento;
						} else {
							$valor = 0;
						}
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
                    			'sid' => $socio->id,
                    			'tutor_id' => $socio->id,
                    			'id_entidad' => $id_entidad,
                    			'aid' => $actividad->id,
                 			'generadoel' => $xahora,
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
	
				// Si la actividad tiene seguro y el socio no es federado de la actividad facturo el seguro
				if ( $actividad->seguro > 0 && $actividad->federado == 0 ) {
                			$descripcion .= 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
					$des = 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
	
					// Inserto el pago del seguro
                			$pago = array(
                    				'sid' => $socio->id,
                    				'tutor_id' => $socio->id,
                    				'id_entidad' => $id_entidad,
                    				'aid' => $actividad->id,
                 				'generadoel' => $xahora,
                    				'descripcion' => $des,
                    				'monto' => $actividad->seguro,
                    				'tipo' => 6,
                    			);
                			$this->pagos_model->insert_pago_nuevo($pago);
				}
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
								if ( $actividad->precio > 0 ) {
                        						$valor = $actividad->precio - $actividad->descuento;
								} else { 
									$valor = 0;
								}
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
                        				'sid' => $familiar['datos']->id,
                        				'tutor_id' => $socio->id,
                        				'id_entidad' => $id_entidad,
                        				'aid' => $actividad->id,
                        				'generadoel' => $xahora,
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
	
                        			// Si la actividad tiene seguro y el socio no es federado de la actividad facturo el seguro
                        			if ( $actividad->seguro > 0 && $actividad->federado == 0 ) {
                                			$descripcion .= 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
                                			$des = 'Seguro '.$actividad->nombre.' - $ '.$actividad->seguro;
			
                                			// Inserto el pago del seguro
                                			$pago = array(
                                        			'sid' => $socio->id,
                                        			'tutor_id' => $socio->id,
                                        			'id_entidad' => $id_entidad,
                                        			'aid' => $actividad->id,
                                        			'generadoel' => $xahora,
                                        			'descripcion' => $des,
                                        			'monto' => $actividad->seguro,
                                        			'tipo' => 6,
                                			);
                                			$this->pagos_model->insert_pago_nuevo($pago);
                        			}
	
               				}
               			}
           		}
	
			// Cuota Excedente
           		if($cuota['excedente'] >= 1){
                		$descripcion .= 'Socio Extra (x'.$cuota['excedente'].') - $ '.$cuota['monto_excedente'].'<br>';
	         		$des = 'Socio Extra (x'.$cuota['excedente'].') - $ '.$cuota['monto_excedente'].'<br>';
				// Inserto el pago de la cuota excedente
                		$pago = array(
                    			'sid' => $socio->id,    
                    			'tutor_id' => $socio->id,                
                    			'id_entidad' => $id_entidad,                
                    			'aid' => 0,
                    			'generadoel' => $xahora,
                    			'descripcion' => $des,
                    			'monto' => $cuota['monto_excedente'],
                    			'tipo' => 1,
                    			);
                			$this->pagos_model->insert_pago_nuevo($pago);
			}
	
	
			// Obtiene el saldo total de la ultima fila de facturacion!!!
	        	$total = $this->pagos_model->get_socio_total($socio->id);
			// Le agrega la cuota facturada este mes al total del saldo
	        	$total = $total - ($cuota['total']);
			$data = array(
				"sid" => $socio->id,
				"id_entidad" => $id_entidad,
				"date" => $xahora,
				"descripcion" => $descripcion,
				"debe" => $cuota['total'],
				"haber" => '0',
				"total" => $total
			);
	
            		$deuda = $this->pagos_model->get_deuda($socio->id);
	
			// Inserta el registro de facturacion del mes
			$this->pagos_model->insert_facturacion($data);
	
			// Actualizo en facturacion_cron el asociado facturado
			$this->pagos_model->update_facturacion_cron($id_entidad,$xperiodo,3, 1, $cuota['total']);
	
			// armo mail
			$respuesta = $this->general_model->armo_cuerpo_email($socio->id);

			$acobrar = $respuesta['acobrar'];
			$cuerpo = $respuesta['cuerpo'];
			$mail_destino = $respuesta['mail_destino'];

			if ( $acobrar > 0 ) {
				// Aca grabo el archivo para mandar a cobrar a COL
				$col_periodo=$xperiodo;
				$col_socio=$socio->id;
				$col_dni=$socio->dni;
				$col_apynom=$socio->apellido." ".$socio->nombre;
				$col_importe=$acobrar;
				$col_fecha_lim=$xlim1;
				$col_recargo="0";
				$col_fecha_lim2=$xlim2;
				$txt = '"'.$col_periodo.'","'.$col_socio.'","'.$col_dni.'","'.$col_apynom.'","'.$col_importe.'","'.$col_fecha_lim.'","'.$col_recargo.'","'.$col_fecha_lim2.'"'."\r\n";
				fwrite($col, $txt);
	
				// Actualizo en facturacion_cron el asociado facturado
				$this->pagos_model->update_facturacion_cron($id_entidad,$xperiodo,5, 1, $col_importe);
	
				// Grabo en el archivo de facturacion_col
				$facturacion_col = array(
                                        	'id' => 0,
                                        	'id_entidad' => $id_entidad,
                                        	'sid' => $col_socio,
                                        	'periodo' => $col_periodo,
                                        	'importe' => $col_importe,
                                        	'cta_socio' => 0,
                                        	'actividades' => 0
				);
				$this->pagos_model->insert_facturacion_col($facturacion_col);

			}

			// Grabo el email en la base de datos para su posterior envio
        		$email = array(
                		'email' => $mail_destino,
                		'id_entidad' => $id_entidad,
                		'body' => $cuerpo
        		);
        		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        		if(preg_match($regex, $mail_destino)){
                		$this->db->insert('facturacion_mails',$email);
        		}

		 
			// Registro pago2 verificar.....
            		$this->pagos_model->registrar_pago2($id_entidad, $socio->id,0);
	
			// Actualizado el estado de socios como facturado (facturado=1)
            		$this->db->where('id', $socio->id);
            		$this->db->update('socios', array('facturado'=>1));
	
			// Registro en el log que asociado facture
            		$txt = date('H:i:s').": Socio #".$socio->id." DNI=".$socio->dni."-".TRIM($socio->apellido).", ".TRIM($socio->nombre)." facturado \n";
            		fwrite($log, $txt);            
	
		}
		// Actualizo en la tabla facturacion_cron que termino el proceso de facturacion
        	$this->db->where('DATE(date) =',$xhoy);
        	$this->db->where('id_entidad',$id_entidad);
        	$this->db->update('facturacion_cron', array('id_entidad'=>$id_entidad,'en_curso'=>0));
		// Registro en el log que el proceso de facturacion termino
        	$txt = date('H:i:s').": Cron Finalizado \n";
        	fwrite($log, $txt);            
        	fclose($log);      
        	fclose($col);      

		$totales=$this->pagos_model->get_facturacion_cron($id_entidad, $xperiodo);
		if ( $totales ) {
			$info_total="Los totales facturados son: <br> Socios Suspendidos: $totales->socios_suspendidos <br> Socios Pasados a Mayores: $totales->socios_cambio_mayor <br> Socios Facturados: $totales->socios_facturados por un total de $ $totales->total_facturado <br> Socios en Debito Tarjeta: $totales->socios_debito por un total de $ $totales->total_debito <br> Mandado a Cobranza COL: $totales->socios_col socios por un total de $ $totales->total_col";
		} else {
			$info_total="No encontre registro en facturacion_cron !!!!";
		}
	
		// Me mando email de aviso que el proceso termino OK
        	mail('gsoc.agonzalez@gmail.com', "El proceso de Facturación Finalizó correctamente.", "Este es un mensaje automático generado por el sistema para confirmar que el proceso de facturación finalizó correctamente ".$xahora."\n".$info_total);
	}


    public function debitos_tarjetas($xperiodo, $log) {

		$anio=substr($xperiodo,0,4);
        	$mes=substr($xperiodo,4,2);
        	$xhoy=date('Y-m-d', strtotime($anio.'-'.$mes.'-01'));
		
		$this->load->model("debtarj_model");
		$debitos=$this->debtarj_model->get_debitos_by_periodo($xperiodo);

		$cant=0;
		$totdeb=0;

		foreach ( $debitos as $debito ) {

			$id_debito = $debito->id_debito;
			$fecha_debito = $debito->fecha_debito;
			$fecha_acreditacion = $debito->fecha_acreditacion;
			$importe = $debito->importe;
			$estado = $debito->estado;
			$nro_renglon = $debito->nro_renglon;
		
			$debtarj = $this->debtarj_model->get_debtarj($id_debito);

			$id_socio = $debtarj->sid;
			$ult_periodo = $debtarj->ult_periodo_generado;
			$ult_fecha = $debtarj->ult_fecha_generacion;

			// Busco el saldo actual del socio
			$total = $this->pagos_model->get_socio_total($id_socio);
                        $saldo_cc = $total + $importe;

                        // Le resta el pago debitado a la tarjeta al saldo 
                        $tarjeta=$this->tarjeta_model->get($debtarj->id_marca);
                        $descripcion = "Pago por Debito en Tarjeta $tarjeta->descripcion";
                        $data = array(
				"sid" => $id_socio,
				"date" => $xhoy,
				"descripcion" => $descripcion,
				"debe" => '0',
				"haber" => $importe,
				"total" => $saldo_cc,
				"origen" => 5
			);


                        $this->pagos_model->insert_facturacion($data);
                        $this->pagos_model->registrar_pago2($id_entidad, $id_socio, $importe);

			$cant=$cant+1;
			$totdeb=$totdeb+$importe;

                        $socio = $this->socios_model->get_socio($id_socio);
			if ( $ult_periodo == $xperiodo ) {
				if ( $fecha_debito == $ult_fecha ) {
	
            				$txt = date('H:i:s')." Registre debito tarjeta para el asociado $id_socio - $socio->apellido, $socio->nombre por un monto de $importe \n";
            				fwrite($log, $txt);            
				} else {
            				$txt = date('H:i:s')." Registre debito pero el asociado $id_socio - $socio->apellido, $socio->nombre tiene la fecha de ultimo debito no coincide con el movimiento \n";
            				fwrite($log, $txt);            
				}
			} else {
            			$txt = date('H:i:s')." El asociado $id_socio - $socio->apellido, $socio->nombre tiene debito en tarjeta pero no coincide el ultimo periodo generado \n";
            			fwrite($log, $txt);            
			
			}
		}
		
	$totales = array( "cant" => $cant, "importe" => $totdeb );
	return $totales;

    }
    public function suspender($id_entidad, $log)
    {
        $this->load->model('socios_model');
	$this->load->model('pagos_model');
	$this->load->model('general_model');
        $socios = $this->socios_model->get_socios_pagan($id_entidad);
	$cant = 0 ;
        foreach ($socios as $socio) {
	    $cat = $this->general_model->get_cat($socio->categoria);
            // Excluyo del analisis a los vitalicios 
	    if ( $cat->tipo != "V" ) {
		$this->db->where('tutor_id', $socio->id);
            	$this->db->where('tipo', 1);
            	$this->db->where('estado', 1);
            	$query = $this->db->get('pagos');
            	if( $query->num_rows() >= 5 ){ 
			$meses_atraso=$query->num_rows();
            		$this->db->where('tutor_id', $socio->id);
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
                		$this->db->where('id',$socio->id);
                		$this->db->update('socios', array('suspendido'=>1));
	
	
                		$txt = date('H:i:s').": Socio Suspendido #".$socio->id." ".TRIM($socio->apellido).", ".TRIM($socio->nombre)." DNI= ".$socio->dni." atraso de ".$meses_atraso." ultimo pago ".$ds_ult. " \n";
                		fwrite($log, $txt);   
	
        			$this->pagos_model->registrar_pago($id_entidad, 'debe',$socio->id,0.00,'Suspension Proceso Facturacion por atraso de'.$meses_atraso.' con ultimo pago hace '.$ds_ult.' dias',0,0,0);
	
				$cant++;
			}
            	}
	     }
        }        
	return $cant;
    }

    function aviso_deuda(){ // esta funcion genera emails de aviso a todos los deudores
                $this->load->model("general_model");
                // Ciclo las entidades para hacer la facturacion de cada una
                $entidades = $this->general_model->get_ents();
                foreach ($entidades as $entidad) {
                        $this->aviso_deuda_entidad($entidad);
                }
    }

    function aviso_deuda_entidad($entidad){ // esta funcion genera emails de aviso a todos los deudores de una entidad

                $id_entidad = $entidad->id;
                $this->load->model("general_model");
                $ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;

		$fecha=date('Ymd');
                $file = './entidades/'.$ent_dir.'/logs/avisodeuda-'.$fecha.'.log';
        	if( !file_exists($file) ){
            		echo "creo log";
            		$log = fopen($file,'w');
        	} else {
            		echo "existe log";
            		$log = fopen($file,'a');
        	}

        	$this->load->model('general_model');
		$this->load->model("pagos_model");
		$this->load->model("debtarj_model");

		// busco los socios con deuda
		$deudores=$this->pagos_model->get_deuda_aviso($id_entidad);
		if ( $deudores ) {

			// vacio la tabla de envios detallados de facturacion
                	$this->db->where('id_entidad',$id_entidad);
                	$this->db->delete('facturacion_mails');
                	$txt = "Truncate de mails de \n";
                	fwrite($log, $txt);
	
			// ciclo cada deudor y armo/grabo los emails en envios
			foreach ( $deudores as $deudor ) {
				// si tiene debito automatico activo no lo mando
				$debito=$this->debtarj_model->get_debtarj_by_sid($deudor->sid);
				if ( !$debito ) {
					$txt_mail="";
	
                			// Armo encabezado con escudo y datos de cabecera
                			$txt_mail  = "<table class='table table-hover' style='font-family:verdana' width='100%' >";
                			$txt_mail .= "<thead>";
                			$txt_mail .= "<tr style='background-color: #105401 ;'>";
                			$txt_mail .= "<th> Imagen de la Entidad ABC  ></th>";
                			$txt_mail .= "<th style='font-size:30; background-color: #105401; color:#FFF' align='center'>ENTIDAD ABC</th>";
                			//$txt_mail .= "<th> <img src='http://clubvillamitre.com/images/Escudo-CVM_100.png' alt='' ></th>";
                			//$txt_mail .= "<th style='font-size:30; background-color: #105401; color:#FFF' align='center'>CLUB VILLA MITRE</th>";
                			$txt_mail .= "</tr>";
                			$txt_mail .= "</thead>";
                			$txt_mail .= "</table>";
			
                			// Datos del Titular
                			$txt_mail .= '<h3 style="font-family:verdana"><strong>Titular:</strong> '.$deudor->sid.'-'.$deudor->nombre.', '.$deudor->apellido.'</h3>';
		
		
					$txt_mail .= "<h1>AVISO DE DEUDA</h1>";
					$txt_mail .= "<h2>Generado el ".date('d-m-Y')."</h2>";
					$txt_mail .= "<br>";
					$txt_mail .= "<h1>Al dia de hoy ud. tiene una deuda de $ ".$deudor->deuda."</h1>";
					$txt_mail .= "<br>";
					$txt_mail .= '<p style="font-family:verdana; ">Si ud. realizo alg&uacuten pago en el d&iacutea de ayer puede que no este reflejado en este resumen </p>';
					$txt_mail .= "<br>";
					$txt_mail .= '<p style="font-family:verdana; font-style:italic;">Ponganse en contacto con la secretaria del Club para regularizar su situaci&oacuten. Existen diferentes formas para financiar su deuda </p>';
					$txt_mail .= "<br>";
					$txt_mail .= '<p style="font-family:verdana; ">Recuerde que al no estar al d&iacutea con sus pagos ud. no puede aprovechar nuestra RED de Beneficios </p>';
					$txt_mail .= "<br>";
					$txt_mail .= '<p style="font-family:verdana; ">Al club lo hacemos entre todos y es de suma importancia su aporte </p>';
					$txt_mail .= "<br>";
		
                			$txt_mail .= "<p style='font-family:verdana'> <b>ADMINISTRACION</b></p>";
	                		$txt_mail .= "<p style='font-family:verdana'> <b>ENTIDAD ABC - BAHIA BLANCA</b></p>";
                			$txt_mail .= "<p style='font-family:verdana'> <b>Domicilio     - (291)-Telefono</b> </p>";
                			$txt_mail .= "<br> <br>";
		
                			//$txt_mail .= "<img src='http://clubvillamitre.com/images/2doZocalo3.png' alt=''>";
		
		
					// grabo el detalle del email
	                		$email = array(
                    				'email' => $deudor->mail,
                    				'id_entidad' => $id_entidad,
                    				'body' => $txt_mail
                			);
                			$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
                			if(preg_match($regex, $deudor->mail)){
                        			$this->db->insert('facturacion_mails',$email);
						// Logueo datos registrados de aviso de deuda
						if ( $deudor->sid != $deudor->tutoreado ) {
                					$txt = "El socio $deudor->sid es TUTOR y tiene Deuda de $deudor->deuda y se lo mandamos al email $deudor->mail \n";
						} else {
                					$txt = "El socio $deudor->sid tiene Deuda de $deudor->deuda y se lo mandamos al email $deudor->mail \n";
						}
                				fwrite($log, $txt);
          	      			} else {
						// Logueo datos descartados por no tener email registrado
                				$txt = "El socio $deudor->sid tiene Deuda de $deudor->deuda y no se lo podemos mandar al email $deudor->mail \n";
                				fwrite($log, $txt);
					}
				} else {
                			$txt = "El socio $deudor->sid tiene Deuda de $deudor->deuda y no lo mandamos porque tiene Debito Automatico \n";
                			fwrite($log, $txt);
				}
	
			}
	
		}
    	}
	
	function controles() {
                $this->load->model("general_model");
                // Ciclo las entidades para hacer la facturacion de cada una
                $entidades = $this->general_model->get_ents();
                foreach ($entidades as $entidad) {
			echo "Controlando Entidad ".$entidad->descripcion."\n";
                        $this->control_entidad($entidad);
                }
	}

	function control_entidad($entidad) {
        
		$id_entidad = $entidad->id;
		$this->load->database('default');

		$txt_ctrl=$entidad->descripcion." - CONTROLES CORRIDOS EL ".date('Y-m-d H:i:s')."\n";

/* Control de que el saldo de facturacion sea igual al de pagos */
		$txt_ctrl=$txt_ctrl."CONTROL DE SALDOS DE FACTURACION VS PAGOS \n";
		$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_saldo_fact;";
        	$this->db->query($qry);
		$qry = "CREATE TEMPORARY TABLE tmp_saldo_fact ( INDEX ( sid ) )
			SELECT sid, SUM( debe - haber ) saldo, sum(debe) debe, sum(haber) haber
			FROM facturacion 
			WHERE id_entidad = $id_entidad
			GROUP BY 1;";
        	$this->db->query($qry);

		$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_saldo_pago;";
        	$this->db->query($qry);
		$qry = "CREATE TEMPORARY TABLE tmp_saldo_pago ( INDEX ( sid ) )
			SELECT tutor_id sid, SUM(monto-pagado) saldo, sum(if(tipo<>5,monto,0)) generado, sum(if(tipo=5,monto,0)) afavor, sum(pagado) pagado, SUM(if(tipo<>5 AND estado=1,1,0)) sin_imputar
			FROM pagos
			WHERE id_entidad = $id_entidad
			GROUP BY 1;";
        	$this->db->query($qry);
			
		$qry = "SELECT s.id sid, s.dni, s.nombre, s.apellido, f.saldo saldo_fact, f.debe, f.haber, p.saldo saldo_pago, p.generado, p.afavor, p.pagado, p.sin_imputar, sdt.id_marca
			FROM tmp_saldo_fact f
        			LEFT JOIN socios s ON ( f.sid = s.id )
        			LEFT JOIN tmp_saldo_pago p ON ( f.sid = p.sid )
        			LEFT JOIN socios_debito_tarj sdt ON ( f.sid = sdt.sid )
			WHERE f.saldo <> p.saldo; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."Los saldos de facturacion y pagos COINCIDEN \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t Saldo Fact \t Debe \t Haber \t Saldo Pago \t Generado \t A Favor \t Pagado \t Sin Imputar \t idMarca \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->sid."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->saldo_fact."\t".$fila->debe."\t".$fila->haber."\t".$fila->saldo_pago."\t".$fila->generado."\t".$fila->afavor."\t".$fila->pagado."\t".$fila->sin_imputar."\t".$fila->id_marca."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

/* Control de que el saldo del ultimo renglon de facturacion sea igual a la suma de movimientos */
		$txt_ctrl=$txt_ctrl."CONTROL DE SALDOS DE FACTURACION VS ULTIMA FILA DE FACTURACION \n";
		$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_ultid; ";
        	$this->db->query($qry);
		$qry = "CREATE TEMPORARY TABLE tmp_ultid ( INDEX ( sid ) )
			SELECT sid, MAX(id) max_id
			FROM facturacion
			WHERE id_entidad = $id_entidad
			GROUP BY 1; ";
        	$this->db->query($qry);

		$qry = "SELECT t.sid, s.dni, s.nombre, s.apellido, t.saldo saldo_fact, t.debe, t.haber, f.total ult_fila
			FROM tmp_saldo_fact t
        			JOIN socios s ON ( t.sid = s.id )
        			JOIN tmp_ultid u USING (sid)
        			JOIN facturacion f ON ( f.id = u.max_id )
			WHERE t.saldo <> -f.total; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."Los saldos de facturacion y el ultimo renglon COINCIDEN \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t Saldo Fact \t Debe \t Haber \t Ultima Fila \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->sid."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->saldo_fact."\t".$fila->debe."\t".$fila->haber."\t".$fila->ult_fila."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

/* Control de que no haya socios con registros impagos y saldo a favor */
		$txt_ctrl=$txt_ctrl."CONTROL DE SALDOS A FAVOR Y REGISTROS IMPAGOS \n";
		$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_afavor; ";
        	$this->db->query($qry);
		$qry = "CREATE TEMPORARY TABLE tmp_afavor ( INDEX ( tutor_id ) )
			SELECT p.tutor_id , p.monto
			FROM pagos p
			WHERE id_entidad = $id_entidad AND
				p.tipo = 5 AND 
				p.monto < 0; ";
        	$this->db->query($qry);

		$qry = "SELECT s.id tutor_id, s.dni, s.nombre, s.apellido, p.id id_pago, p.sid, p.monto, p.generadoel, p.pagado, p.pagadoel, p.estado
			FROM pagos p
        			JOIN tmp_afavor a USING ( tutor_id )
        			JOIN socios s ON ( p.tutor_id = s.id )
			WHERE p.id_entidad = $id_entidad AND 
				p.estado = 1 AND 
				p.tipo <> 5; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."No existen socios con saldo a favor y pagos pendientes \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t id_Pago \t Tutor \t Socio \t Monto \t GeneradoEl \t Pagado \t PagadoEl \t Estado \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->tutor_id."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->id_pago."\t".$fila->sid."\t".$fila->monto."\t".$fila->generadoel."\t".$fila->pagado."\t".$fila->pagadoel."\t".$fila->estado."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

/* Control de que no haya socios con registros estado=1 y todo pagado */
		$txt_ctrl=$txt_ctrl."CONTROL DE PAGOS PENDIENTES Y TODO PAGADO \n";
		$qry = "SELECT s.id tutor_id, s.dni, s.nombre, s.apellido, p.id id_pago, p.sid, p.monto, p.generadoel, p.pagado, p.pagadoel, p.estado
			FROM pagos p
				JOIN socios s ON ( p.tutor_id = s.id )
			WHERE p.id_entidad = $id_entidad AND 
				p.estado = 1 AND 
				p.pagado >= p.monto AND 
				p.tipo <> 5 AND 
				p.monto > 0; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."No existen pagos pendientes de socios con todo pagado \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t id_Pago \t Tutor \t Socio \t Monto \t GeneradoEl \t Pagado \t PagadoEl \t Estado \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->tutor_id."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->id_pago."\t".$fila->sid."\t".$fila->monto."\t".$fila->generadoel."\t".$fila->pagado."\t".$fila->pagadoel."\t".$fila->estado."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

/* Control de que no haya socios con registros estado=0 y sin todo pagado */
		$txt_ctrl=$txt_ctrl."CONTROL DE PAGOS con ESTADO=0 Y SIN TODO PAGADO \n";
		$qry = "SELECT s.id tutor_id, s.dni, s.nombre, s.apellido, p.id id_pago, p.sid, p.monto, p.generadoel, p.pagado, p.pagadoel, p.estado
			FROM pagos p
				JOIN socios s ON ( p.tutor_id = s.id )
			WHERE p.id_entidad = $id_entidad AND
				p.estado = 0 AND 
				p.pagado < p.monto AND 
				p.tipo <> 5 AND 
				p.monto > 0; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."No existen pagos con estado=0 y sin todo pagado \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t id_Pago \t Tutor \t Socio \t Monto \t GeneradoEl \t Pagado \t PagadoEl \t Estado \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->tutor_id."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->id_pago."\t".$fila->sid."\t".$fila->monto."\t".$fila->generadoel."\t".$fila->pagado."\t".$fila->pagadoel."\t".$fila->estado."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}


/* Control de que no haya socios con pagado > monto */
		$txt_ctrl=$txt_ctrl."CONTROL DE PAGOS MAYORES AL MONTO \n";
		$qry = "SELECT s.id tutor_id, s.dni, s.nombre, s.apellido, p.id id_pago, p.sid, p.monto, p.generadoel, p.pagado, p.pagadoel, p.estado
			FROM pagos p
				JOIN socios s ON ( p.tutor_id = s.id )
			WHERE p.id_entidad = $id_entidad AND
				p.estado = 0 AND 
				pagado > monto;";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."No existen pagos con mayor pagado que el monto \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t id_Pago \t Tutor \t Socio \t Monto \t GeneradoEl \t Pagado \t PagadoEl \t Estado \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->tutor_id."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->id_pago."\t".$fila->sid."\t".$fila->monto."\t".$fila->generadoel."\t".$fila->pagado."\t".$fila->pagadoel."\t".$fila->estado."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

/* Control de que no haya registros estado=1 y monto=pagado=0 */
		$txt_ctrl=$txt_ctrl."CONTROL DE PAGOS PENDIENTES PERO QUE TIENEN TODO PAGADO \n";
		$qry = "SELECT s.id tutor_id, s.dni, s.nombre, s.apellido, p.id id_pago, p.sid, p.monto, p.generadoel, p.pagado, p.pagadoel, p.estado
			FROM pagos p
        			JOIN socios s ON ( p.tutor_id = s.id )
			WHERE p.id_entidad = $id_entidad AND
				p.estado = 1 AND 
				p.pagado = p.monto AND 
				p.tipo <> 5; ";
        	$resultado = $this->db->query($qry);

		if ( $resultado->num_rows() == 0 ) {
			$txt_ctrl=$txt_ctrl."No existen pagos pendientes con todo pagado \n";
			$txt_ctrl=$txt_ctrl."\n";
		} else {
			$txt_ctrl=$txt_ctrl. "SID \t DNI \t Nombre \t Apellido \t id_Pago \t Tutor \t Socio \t Monto \t GeneradoEl \t Pagado \t PagadoEl \t Estado \n";
			foreach ( $resultado->result() as $fila ) {
				$txt_ctrl=$txt_ctrl.$fila->tutor_id."\t".$fila->dni."\t".$fila->nombre."\t".$fila->apellido."\t".$fila->id_pago."\t".$fila->sid."\t".$fila->monto."\t".$fila->generadoel."\t".$fila->pagado."\t".$fila->pagadoel."\t".$fila->estado."\n";
        		}
			$txt_ctrl=$txt_ctrl."\n";
		}

		//echo $txt_ctrl;
		$xahora=date('Y-m-d G:i:s');
		mail('gsoc.agonzalez@gmail.com', "El proceso de Controles Diarios finalizó correctamente.", "Este es un mensaje automático generado por el sistema para confirmar que el proceso de imputacion de pagos finalizó correctamente ".$xahora."\n".$txt_ctrl);

	}
       
	function pagos() {
                $this->load->model("general_model");
                // Ciclo las entidades para hacer la facturacion de cada una
                $entidades = $this->general_model->get_ents();
                foreach ($entidades as $entidad) {
                        $this->pago_entidad($entidad);
                }
	}

	function pago_entidad($entidad) {

                $id_entidad = $entidad->id;
                $this->load->model("general_model");
                $ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;

                $fecha=date('Ymd');
                $file = './entidades/'.$ent_dir.'/logs/cronpago-'.$fecha.'.log';
                if( !file_exists($file) ){
                        echo "creo log";
                        $log = fopen($file,'w');
                } else {
                        echo "existe log";
                        $log = fopen($file,'a');
                }

                $txt=$entidad->descripcion." - PROCESO DE PAGOS ".date('Y-m-d H:i:s')."\n";
		fwrite($log, $txt);

		$this->load->model("pagos_model");
		$this->load->model("socios_model");

		// Si me vino una fecha en el URL fuerzo la generacion de esa fecha en particular sin controlar cron
        	if ($this->uri->segment(3)) {
			echo "asigno fecha de parametro \n";
			$ayer = $this->uri->segment(3);
			echo "ayer = $ayer \n";
                	$txt="Asigne fecha parametro".$ayer."\n";
			fwrite($log, $txt);
		} else {
			echo "tomo el date\n";
			$ayer = date('Ymd',strtotime("-1 day"));
			$fecha = date('Y-m-d');
			if($this->pagos_model->check_cron_pagos($id_entidad)){
                		$txt="Asigne date y salgo porque ya se ejecuto el cron con anterioridad".$ayer."\n";
				fwrite($log, $txt);
				exit('Esta tarea ya fue ejecutada hoy.');
			}	 
                	$txt="Asigne date y ejecuto".$ayer."\n";
			fwrite($log, $txt);
		}


		// Veo si tiene algun condicional enviado en la URL para hacer o no generacion
		// Sino viene segmento 4 (default) genera todo
		// Si viene en segmento 4 CD o TODO genero Cuenta Digital
		$ctrl_gen="";
		if ( $this->uri->segment(4) ) {
			$ctrl_gen=$this->uri->segment(4);
			$txt = "Controlo generacion vino -> $ctrl_gen";
			echo $txt;
			if ( !($ctrl_gen == "TODO" || $ctrl_gen == "CD" || $ctrl_gen = "COL") ) {
				$txt=$txt. " Ese valor no esta previsto...TERMINO EJECUCION";
				fwrite($log, $txt);
				echo "EL PARAMETRO PARA GENERAR ES INCORRECTO";
				exit;
			}
		} else {
			$txt = "Generacion default busca TODO\n";
			echo $txt;
			fwrite($log, $txt);
			$ctrl_gen="TODO";
		}
		
		$reactivados=array();
		$cant_react=0;
		$cant_cd = 0;
		$total_cd = 0;
		$cant_col = 0;
		$total_col = 0;

		if ( $ctrl_gen == "TODO" || $ctrl_gen == "CD" ) {
			// Busco los pagos del sitio de Cuenta Digital
			$pagos = $this->get_pagos($entidad, $ayer);
			$txt = "Generacion pagos de Cuenta Digital\n";
			fwrite($log, $txt);

			// Si bajo algo del sitio
			if($pagos) {
				// Ciclo los pagos encontrados
				foreach ($pagos as $pago) {
					$data = $this->pagos_model->insert_pago($id_entidad,$pago);
					$this->pagos_model->registrar_pago2($id_entidad,$pago['sid'],$pago['monto']);

					// Me fijo si esta suspendido y con el pago se queda con saldo a favor para reactivar
					$saldo=$this->pagos_model->get_saldo($pago['sid']);
					$socio=$this->socios_model->get_socio($pago['sid']);
					if ( $socio->suspendido == 1 && $saldo < 0 ) {
						$this->socios_model->suspender($pago['sid'],'no');
						$reactivados[]=$socio->id."-".$socio->apellido.", ".$socio->nombre."\n";
						$cant_react++;
					}

					// Acumulo para email
					$cant_cd++;
					$total_cd=$total_cd+$pago['monto'];
				}
			}
			$txt = "Termine Generacion pagos de Cuenta Digital- Reactive: ".$cant_react." Procese: ".$cant_cd." por un total de $ ".$total_cd."\n";
			fwrite($log, $txt);
		}

		
		if ( $ctrl_gen == "TODO" || $ctrl_gen == "COL" ) {
			$txt = "Generacion pagos de Cooperativa\n";
			fwrite($log, $txt);

			echo "genero COL";
			if ( $this->uri->segment(5) ) {
				$txt = "vino parametro 5 = ".$this->uri->segment(5)." filtro pagos de ese local \n";
				echo $txt;
				fwrite($log, $txt);
				$suc_filtro=$this->uri->segment(5);
			} else {
				$suc_filtro=0;
			}
			// Busco los pagos registrados en COL
			$pagos_COL = $this->get_pagos_COL($entidad, $ayer,$suc_filtro);


			// Si bajo algo del sitio
			if($pagos_COL) {
				// Ciclo los pagos encontrados
				foreach ($pagos_COL as $pago) {
					// Si vino en la URL que genera solo un local descarto el resto
					$data = $this->pagos_model->insert_pago_col($id_entidad,$pago);
					$this->pagos_model->registrar_pago2($id_entidad,$pago['sid'],$pago['monto']);

					// Me fijo si esta suspendido y con el pago se queda con saldo a favor para reactivar
					$saldo=$this->pagos_model->get_saldo($pago['sid']);
					$socio=$this->socios_model->get_socio($pago['sid']);
					if ( $socio->suspendido == 1 && $saldo < 0 ) {
						$this->socios_model->suspender($pago['sid'],'no');
						$reactivados[]=$socio->id."-".$socio->apellido.", ".$socio->nombre."\n";
						$cant_react++;
					}

					// Acumulo para email
					$cant_col++;
					$total_col=$total_col+$pago['monto'];
				}
			}
			$txt = "Termine Generacion pagos de La Coope- Reactive: ".$cant_react." Procese: ".$cant_col." por un total de $ ".$total_col."\n";
			fwrite($log, $txt);
		}

		if (!$this->uri->segment(3)) {
			$this->pagos_model->insert_pagos_cron($id_entidad, $fecha); 
		}

        // Me mando email de aviso que el proceso termino OK
	$info_total="Procese fecha de cobro = $ayer para la entidad $entidad->descripcion \n Procese $cant_cd pagos de CuentaDigital por un total de $ $total_cd \n Procese $cant_col pagos de LaCoope por un total de $ $total_col.\n Reactive $cant_react socios. \n";
	foreach ( $reactivados as $r ) {
		$info_total.=$r."\n";
	}
	$xahora=date('Y-m-d G:i:s');
        mail('gsoc.agonzalez@gmail.com', "El proceso de Imputación de Pagos finalizó correctamente.", "Este es un mensaje automático generado por el sistema para confirmar que el proceso de imputacion de pagos finalizó correctamente ".$xahora."\n".$info_total);

	}

	function get_pagos($entidad, $fecha) {           
  
		$id_entidad = $entidad->id;
		$cd_control = $entidad->cd_control;

        	$url = 'http://www.cuentadigital.com/exportacion.php?control='.$cd_control;
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
						"id_entidad" => $id_entidad,
			   			"sid" => $entrantes[3],
			   			"pid" => $entrantes[4]
			   		);
                    			$p = array(
                            			"fecha" => date('Y-m-d',strtotime($entrantes[0])),
                            			"hora" => $hora.':'.$min,
                            			"monto" => $entrantes[2],
						"id_entidad" => $id_entidad,
                            			"sid" => $entrantes[3],
                            			"pid" => $entrantes[4]
                        			);
                    			$this->pagos_model->insert_cuentadigital($p);
				}
			}
			return $pago;
		} else {
			if($a === FALSE) {
                		mail("gsoc.agonzalez@gmail.com","Fallo en Cron Pagos ".$id_entidad,date('Y-m-d H:i:s'));
                		exit();
			}
			return false;
		}
	}

	function get_pagos_COL($entidad,$fecha,$suc_filtro) {           

		$id_entidad = $entidad->id;
		$cuit = $entidad->cuit;
		$nprov = $entidad->nprov_col;

                $url = 'https://extranet.cooperativaobrera.coop/xml/Consorcios/index/'.$cuit.'/'.$nprov.'/'.$fecha;
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
						"id_entidad" => $id_entidad,
						"sid" => $nro_socio,
						"pid" => $nro_cupon
					);
					$p = array(
						"id_entidad" => $id_entidad,
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
			fwrite($log,$socio->id." ".$socio->apellido.", ".$socio->nombre." monto :".$importe."\n");
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
			fwrite($log,$socio->id." ".$socio->apellido.", ".$socio->nombre." monto :".$importe."\n");
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
// TODO NO SE SI SE USA
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
// TODO NO SE SI SE USA
        if(date('d') != 20){ die(); }
        $this->load->model('general_model');
        $config = $this->general_model->get_config();
        if($config->interes_mora > 0){
            $this->load->model("socios_model");            
            $this->load->model('pagos_model');
            $socios = $this->socios_model->get_socios_pagan();
            foreach ($socios as $socio) {
                $cuota = $this->pagos_model->get_monto_socio($socio->id);
                $total = $this->pagos_model->get_socio_total($socio->id);
                if($total*-1 > $cuota['total']){
                    $debe = $cuota['total'] * $config->interes_mora /100;
                    
                    $total = $total - $debe;
                    $facturacion = array(
                        'sid' => $socio->id,
                        'descripcion'=>'Intereses por Mora',
                        'debe'=>$debe,
                        'haber'=>0,
                        'total'=>$total
                    );
                    $this->pagos_model->insert_facturacion($facturacion);

                    $pago = array(
                        'sid' => $socio->id, 
                        'tutor_id' => $socio->id,
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

    public function facturacion_mails() {
                $this->load->model("general_model");
                // Ciclo las entidades para hacer la facturacion de cada una
                $entidades = $this->general_model->get_ents();
                foreach ($entidades as $entidad) {
                        $this->fact_mail_entidad($entidad);
                }
        }

    function fact_mail_entidad($entidad) {

                $id_entidad = $entidad->id;
                $this->load->model("general_model");
                $ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;
		$email_from = 'aviso@gestionsocios.com.ar';
		$email_nombre = $entidad->descripcion;
		$reply_to = $entidad->email_sistema;

                $fecha=date('Ymd');
                $file = './entidades/'.$ent_dir.'/logs/enviomail-'.$fecha.'.log';
                if( !file_exists($file) ){
                        echo "creo log";
                        $log = fopen($file,'w');
                } else {
                        echo "existe log";
                        $log = fopen($file,'a');
                }

                $txt=$entidad->descripcion." - Envio de Emails ".date('Y-m-d H:i:s')."\n";
                fwrite($log, $txt);

        	$this->load->database();
        	$txt=date('d/m/Y G:i:s').": Buscando correos para enviar... \n";
                fwrite($log, $txt);

        	$this->db->where('id_entidad', $id_entidad);
        	$this->db->where('estado', 0);
        	$query = $this->db->get('facturacion_mails');

        	if($query->num_rows() == 0){ 
            		$txt = date('d/m/Y G:i:s').": No se encontraron correos - Termino Ejecucion\n";
                	fwrite($log, $txt);
            		return false;
        	} else {
            		$txt = date('d/m/Y G:i:s').": Se encontraron ".$query->num_rows()." correos. Enviando... \n";
                	fwrite($log, $txt);
			$this->load->library('email');
			$enviados=0;
			foreach ($query->result() as $email) {
                		$this->email->reply_to($reply_to);
                		$this->email->from($email_from,$email_nombre);
                		$this->email->to($email->email);
                		$this->email->cco('gsoc.agonzalez@gmail.com');                 

                		$asunto='Resumen de Cuenta al '.date('d/m/Y');
                		$this->email->subject($asunto);                
                		$this->email->message($email->body); 
                		$txt = date('d/m/Y G:i:s').": Enviando: ".$email->email;

                		if($this->email->send()){
                    			$txt=$txt." ----> Enviado OK \n";
                    			$this->db->where('id',$email->id);
                    			$this->db->update('facturacion_mails',array('estado'=>1));
		    			$enviados++;
                		} else {
                    			$msg_error=substr($this->email->print_debugger(),0,200);
                    			$txt = $txt." ----> Error de Envio:".$msg_error." \n";
				}
                		fwrite($log, $txt);
            		}

            		$txt = date('d/m/Y G:i:s').": Envio Finalizado \n";
                	fwrite($log, $txt);

            		// Me mando email de aviso que el proceso termino OK
            		mail('gsoc.agonzalez@gmail.com', "El proceso de Envio de Emails de $entidad->descripcion finalizo correctamente.", "Este es un mensaje automático generado por el sistema para confirmar que el proceso de envios de email finalizó correctamente y se enviaron $enviados emails.....".date('d/m/Y G:i:s')."\n");

            
        	}
    }

    public function control()
    {
        $this->load->model("pagos_model");
        $this->load->model("socios_model");
        $socios = $this->socios_model->listar(); //listamos todos los socios activos
        foreach ($socios as $socio) {    
            $total = $this->pagos_model->get_socio_total($socio['datos']->id);
            $total2 = $this->pagos_model->get_socio_total2($socio['datos']->id);
            if($total + $total2 != 0 && $total <= 0){
                echo $socio['datos']->id.' | '.$total.' | '.$total2.'<br>';            
            }
        }
    }  
}
