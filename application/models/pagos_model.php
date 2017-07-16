<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Pagos_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function get_cupon($sid){
        $this->load->model("socios_model"); 
        $socio = $this->socios_model->get_socio($sid);
        if($socio->tutor != 0){
            $grupo_familiar = true;
            //si el socio pertenece a un grupo familiar buscamos el cupon del tutor            
            return $this->get_cupon($socio->tutor);
        }

        $query = $this->db->get_where('cupones',array('sid'=>$sid,'estado'=>'1'));
        if($query->num_rows() == 0){
            $cupon = new stdClass();
            $cupon->monto = '0';
            return $cupon;
        }else{
            return $query->row();
        }
    }

    public function get_cupon_by_id($id='')
    {
        $this->db->where('Id',$id);
        $query = $this->db->get('cupones');
        if($query->num_rows() == 0){return false;}
        $cupon = $query->row();
        $query->free_result();
        return $cupon;
    }

    public function get_monto_socio($sid){ // devuelve el importe que deberá pagar un socio o su tutor, en caso de pertenecer a un grupo familiar        
        $grupo_familiar = $tutor = false;
        $monto = 0;
        //obtenemos el precio de cada categoria
        $this->load->model("general_model");
        $cats = $this->general_model->get_cats();

        $precio_excedente = $cats['3']->precio_unit;

        //buscamos si el socio pertenece a un grupo familiar
        $this->load->model("socios_model"); 
        $socio = $this->socios_model->get_socio($sid);

        if($socio->tutor != 0){
            $grupo_familiar = true;
            //si el usuario pertenece a un grupo familiar buscamos el monto del tutor            
            return $this->get_monto_socio($socio->tutor);
        }   

        //buscamos si el usuario es tutor de grupo familiar
        $query = $this->db->get_where('socios',array('tutor'=>$sid,'estado'=>'1'));
        if($query->num_rows() != 0){
            $tutor = true;
            $familiares_a_cargo = $query->result();  
            $fam_actividades = array();
            $total_familiares = (count($familiares_a_cargo) + 1);  //la cantidad de familiares a cargo mas el tutor
            $familiares = array();
            foreach ($familiares_a_cargo as $familiar) { // buscamos las actividades de cada familiar
                $fam_actividades = $this->get_actividades_socio($familiar->Id);
                $familiares[] = array('datos' => $familiar, 'actividades' => $fam_actividades);
            }

            //buscamos las actividades del socio titular
            $socio_actividades =  $this->get_actividades_socio($sid);
            //buscamos los familiares excedentes
            $excedente = $total_familiares - 4; // 4 = total del grupo familiar
            $monto_excedente = 0;
            for ($i=0; $i < $excedente; $i++) { 
                $monto_excedente = $monto_excedente + $precio_excedente;
            }
                    
            $monto = $cats['3']->precio - ($cats['3']->precio * $socio->descuento / 100); //valor de la cuota de grupo familiar
            $total = $monto + ( $monto_excedente - ($monto_excedente * $socio->descuento / 100) ); //cuota mensual mas el excedente en caso de ser mas socios de lo permitido en el girpo fliar
            foreach ($socio_actividades['actividad'] as $actividad) {                
		// actividades del titular del grupo familiar
		if ( $actividad->monto_porcentaje == 0 ) {
                	$total = $total + ( $actividad->precio - $actividad->descuento ); 
		} else {
                	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100) ); 
		}
            }
            foreach ($familiares as $familiar) {
                foreach($familiar['actividades']['actividad'] as $actividad){
		//actividades del los socios del grupo famlilar
		    if ( $actividad->monto_porcentaje == 0 ) {
                    	$total = $total + ( $actividad->precio - $actividad->descuento ); 
		    } else {
                    	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100) ); 
		    }
                }
            }

            $financiacion = $this->get_financiado_mensual($socio->Id);            
            $f_total = 0;
            if($financiacion){
                foreach ($financiacion as $plan) {
                    $f_total = $f_total + round($plan->monto/$plan->cuotas,2);
                }                
            }

            $total = $total + $f_total;
            $cuota = array(
                "tid" => $sid,
                "titular" => $socio->apellido.' '.$socio->nombre,
                "total" => $total,
                "categoria" => 'Grupo Familiar',
                "cuota" => $monto,
                "familiares" => $familiares,
                "actividades" => $socio_actividades,
                "excedente" => $excedente,
                "monto_excedente" => $monto_excedente- ($monto_excedente * $socio->descuento / 100),
                "financiacion" => $financiacion,
                "descuento" => $socio->descuento,
                "cuota_neta"=>$cats[3]->precio
            );
            return $cuota;

        }else{ //si no esta en un grupo familiar
            $socio_actividades =  $this->get_actividades_socio($sid); //buscamos las actividades del socio
            $socio_cuota = $cats[$socio->categoria-1]->precio - ($cats[$socio->categoria-1]->precio * $socio->descuento / 100); //precio de la cuota
            $total = $socio_cuota; //cuota mensual
            foreach ($socio_actividades['actividad'] as $actividad) {
		//actividades del socio
		if ( $actividad->monto_porcentaje == 0 ) {
                	$total = $total + ( $actividad->precio - $actividad->descuento ) ; 
		} else {
                	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100 ) ) ; 
		}
            }

            $financiacion = $this->get_financiado_mensual($socio->Id);            
            $f_total = 0;
            if($financiacion){
                foreach ($financiacion as $plan) {
                    $f_total = $f_total + round($plan->monto/$plan->cuotas,2);
                }
            }


            $total = $total + $f_total;
            $cuota = array(
                "tid" => $sid,
                "titular" => $socio->apellido.' '.$socio->nombre,
                "total" => $total,
                "categoria" => $cats[$socio->categoria-1]->nomb,
                "cuota" => $socio_cuota,
                "familiares" => '0',
                "actividades" => $socio_actividades,
                "excedente" => '0',
                "monto_excedente" => '0',
                "financiacion" => $financiacion,
                "descuento" => $socio->descuento,
                "cuota_neta"=>$cats[$socio->categoria-1]->precio
            );
        return $cuota;
        }                
    }
    function get_actividades_socio($sid){
        $this->load->model("socios_model");  //buscamos datos del socio
        $socio = $this->socios_model->get_socio($sid);
        $this->load->model("actividades_model"); //buscamos las actividades del socio
        $actividades = $this->actividades_model->get_act_asoc($sid);
        $act = array();
        foreach ($actividades as $actividad) {
                
                    if($actividad->estado != '0'){
                        $act[] = $actividad;
                    }
                }        
        $actividades_socio = array('actividad' =>$act);  
        return $actividades_socio; // devolvemos los datos de la actividad y el usuario correspondiente
    }
    function generar_cupon($sid, $monto,$cupon)
    {
        $this->db->where('sid',$sid); // ponemos en 0 todos los cupones de este socio
        $this->db->update('cupones',array('estado'=>'0'));        
        $data = array(
                'sid' => $sid,
                'monto' => $monto,
                'estado' => '1',
                'barcode' => $cupon['barcode']
            );
        $this->db->insert('cupones',$data);
        return $this->db->insert_id();
    }
    public function get_socio_total($sid)
    {
        $this->db->where('sid',$sid);        
        $this->db->order_by('Id','desc');
        $fact = $this->db->get('facturacion');
        if($fact->num_rows() == 0){return false;}
        /*$total = 0;
        foreach ($fact->result() as $f) {
            $total = $total + $f->haber;
            $total = $total - $f->debe;
        }*/
        $total = $fact->row()->total;
        return $total;
    }

    public function get_socio_total2($sid)
    {        
        $this->db->where('tutor_id',$sid);        
        $this->db->where('tipo !=',5);
        $this->db->where('estado',1);    
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;} 
        $total = 0;
        foreach ($query->result() as $pago) {
            $total = $total + $pago->monto;
            $total = $total - $pago->pagado;
        }
        return $total;
    }


    public function insert_facturacion_col($data)
    {
        $this->db->insert('facturacion_col',$data);
    }

    public function insert_facturacion($data)
    {
        $this->db->insert('facturacion',$data);
    }

    function insert_cobranza_col($data)
    {
        $datos = array(
                'sid' => $data['sid'],
                'periodo' => $data['periodo'],
                'fecha_pago' => $data['fecha_pago'],
                'suc_pago' => $data['suc_pago'],
                'nro_cupon' => $data['nro_cupon'],
                'importe' => $data['importe'],
		'id' => 0
            );
        $this->db->insert('cobranza_col',$datos);
        return $this->db->insert_id();
    }

    function get_cobcols($periodo)
    {
        $query = $this->db->get_where('facturacion_col',array('periodo'=>$periodo));
	
	$registros=array();
        foreach ($query->result() as $fact) {

		$nro_socio=$fact->sid;
		$cobcol= get_cobcol($nro_socio,$periodo);
		$fecha_pago=0;
		$suc_pago=0;
		$nro_cupon=0;
		if ( $cobcol ) {
			$fecha_pago = $cobcol->fecha_pago;
			$suc_pago = $cobcol->suc_pago;
			$nro_cupon = $cobcol->nro_cupon;
		}
            	$registro = array(
                	'id' => $fact->id,
                	'sid' => $fact->sid,
                	'periodo' => $periodo,
                	'importe' => $fact->importe,
                	'cta_socio' => $fact->cta_socio,
                	'actividades' => $fact->actividades,
                	'fecha_pago' => $fecha_pago,
                	'suc_pago' => $suc_pago,
                	'nro_cupon' => $nro_cupon
                );
		$registros[]=$registro;
        }
	
	return $registros;
    }

    function get_cobcol($sid, $periodo) {
        $this->db->where('sid', $sid);
        $this->db->where('periodo', $periodo);
        $query = $this->db->get('cobranza_col');
        if($query->num_rows() == 0){
		return false;
	} else {
		return $query->row();
	}
    }

    public function check_cron($periodo)
    { //comprueba si ya se ejecuto la tarea este mes o si esta en curso
	$anio=substr($periodo,0,4);
	$mes=substr($periodo,5,2);
	$ahora=date($anio.'-'.$mes.'-'.'01 00:00:00');
        $this->db->where('YEAR(date)' , $anio);
        $this->db->where('MONTH(date)' , $mes);        

        $query = $this->db->get('facturacion_cron');
        if($query->num_rows() == 0){
            $this->db->insert( 'facturacion_cron',array('date'=>$ahora, 'des'=>'0','en_curso'=>1) );
            return 'iniciado';
        }else{
            $cron_state = $query->row();
            if($cron_state->en_curso == 1){
                return 'en_curso';
            }else{
                return false;
            }
        }
    }

    public function update_facturacion_cron($periodo, $tipo, $cant, $importe)
    {
	$anio=substr($periodo,0,4);
	$mes=substr($periodo,5,2);
        $this->db->where('YEAR(date)' , $anio);
        $this->db->where('MONTH(date)' , $mes);        
	$query = $this->db->get('facturacion_cron');
	if ( $query->num_rows() == 0 ) {
		return false;
	} else {
		$cron_state = $query->row();
		if ( $cron_state->en_curso == 1 ) {
			$this->db->where( 'Id', $cron_state->Id );
			switch ( $tipo ) {
				// Suspendidos
				case 1:
					$this->db->update( 'facturacion_cron',array('socios_suspendidos'=>$cant) );
					break;
				// Cambio de categoria a Mayor
				case 2:
					$this->db->update( 'facturacion_cron',array('socios_cambio_mayor'=>$cant) );
					break;
				// Facturados
				case 3:
					$cant_act=$cron_state->socios_facturados+$cant;
					$total_act=$cron_state->total_facturado+$importe;
					$this->db->update( 'facturacion_cron',array('socios_facturados'=>$cant_act, 'total_facturado'=>$total_act) );
					break;
				// Debito
				case 4:
					$cant_act=$cron_state->socios_debito+$cant;
					$total_act=$cron_state->total_debito+$importe;
					$this->db->update( 'facturacion_cron',array('socios_debito'=>$cant_act, 'total_debito'=>$total_act) );
					break;
				// Archivo de cobranza COL
				case 5:
					$cant_act=$cron_state->socios_col+$cant;
					$total_act=$cron_state->total_col+$importe;
					$this->db->update( 'facturacion_cron',array('socios_col'=>$cant_act, 'total_col'=>$total_act) );
					break;
			}
			return true;
		} else {
			return false;
		}
	}
    }

    public function insert_facturacion_cron()
    {
    }
    public function get_facturacion($sid)
    {
        $this->db->order_by('DATE(date)','desc');      
        $this->db->order_by('Id','desc');      
        $this->db->where("sid", $sid);
        $query = $this->db->get('facturacion');
        return $query->result();
    }
    public function check_cron_pagos()
    { //comprueba si ya se ejecuto la tarea hoy
        $this->db->where(date('Y'), 'YEAR(date)' , FALSE);
        $this->db->where(date('m'), 'MONTH(date)' , FALSE);
        $this->db->where(date('d'), 'DAY(date)' , FALSE);
        $query = $this->db->get('pagos_cron');
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }
    public function insert_pagos_cron($fecha)
    {
        $this->db->insert('pagos_cron',array('date'=>$fecha,'des'=>'0'));
    }

    public function insert_pago_col($pago)
    {
        $total = $this->get_deuda($pago['sid']);        
        $total = $total + $pago['monto'];
        $descripcion = "Pago acreditado desde: La Coope <br>Fecha: ".$pago['fecha'].' '.$pago['hora'];
        $this->db->insert('facturacion',array('sid'=>$pago['sid'],'haber'=>$pago['monto'],'total'=>$total,'descripcion'=>$descripcion));

    }

    public function insert_pago($pago)
    {
        $total = $this->get_deuda($pago['sid']);        
        $total = $total + $pago['monto'];
        $descripcion = "Pago acreditado desde: CuentaDigital <br>Fecha: ".$pago['fecha'].' '.$pago['hora'];
        $this->db->insert('facturacion',array('sid'=>$pago['sid'],'haber'=>$pago['monto'],'total'=>$total,'descripcion'=>$descripcion));

    }

    public function registrar_pago($tipo,$sid,$monto,$des,$actividad)
    {
        $total = $this->get_socio_total($sid);  
            
        if($tipo == 'debe'){
            $debe = $monto;
            $haber = '0.00';
            $total = $total - $debe;
            if($actividad == 'cs'){
                $aid = 0;
                $tipo = 1;
            }else{
                $aid = $actividad;
                $tipo = 4;
            }
            $pago = array(
                'sid' => $sid,  
                'tutor_id' => $sid,                  
                'aid' => $aid,
                'generadoel' => date('Y-m-d'),
                'descripcion' => $des,
                'monto' => $monto,
                'tipo' => $tipo,
                );
            $this->pagos_model->insert_pago_nuevo($pago);
            $this->registrar_pago2($sid,0);
        }else{
            $haber = $monto;
            $debe = '0.00';
            $total = $total + $haber;
            $this->registrar_pago2($sid,$monto);
        }
        $data = array(
                "sid" => $sid,
                "descripcion" => $des,
                "debe" => $debe,
                "haber" => $haber,
                "total" => $total
            );
        $this->db->insert("facturacion",$data);
        $data['iid'] = $this->db->insert_id();
        $data['fecha'] = date('d/m/Y');
        $data = json_encode($data);
        return $data;
    }

    public function get_deuda($sid){        
        $this->db->where('sid',$sid);
        $this->db->order_by('Id','desc');
        $query = $this->db->get('facturacion');
        if($query->num_rows() == 0){ return 0;}
        $deuda = $query->row()->total;
        return $deuda;
    }

    public function financiar_deuda($socio,$monto,$cuotas,$detalle){
        $inicio = date('Y-m').'-1';
        $nuevafecha = strtotime ( '+'.$cuotas.' month' , strtotime ( $inicio ) ) ;
        $fin = date ( 'Y-m-d' , $nuevafecha );   
        $financiacion = array(
            'sid' => $socio,
            'cuotas' => $cuotas,
            'monto' => $monto,
            'inicio' => $inicio,
            'fin' => $fin,
            'detalle'=>$detalle
            );
        $this->db->insert('financiacion',$financiacion);
    }

    public function get_planes($sid){
        $this->db->where('sid',$sid);
        $query = $this->db->get('financiacion');
        $planes = $query->result();
        $query->free_result();
        return $planes;
    }

    public function cancelar_plan($id){
        $this->db->where('Id',$id);
        $this->db->update('financiacion',array('estado'=>2));
    }

    public function get_financiado_mensual($sid){
        $fecha = date('Y-m-d');
        $this->db->where('sid',$sid);
        $this->db->where('inicio <=',$fecha);
        $this->db->where('fin >',$fecha);        
        $this->db->where('estado',1);
        $query = $this->db->get('financiacion');
        if($query->num_rows() == 0){return false;}
        $planes = $query->result();
        $query->free_result();
        return $planes;
    }

    public function update_cuota($id){        
        $this->db->where('Id',$id);        
        $this->db->where('estado',1);
        $this->db->set('actual','actual+1',false);
        $this->db->update('financiacion');
    }

    public function get_morosos($comision=null,$actividad=null){        

	// Cargo en la variables actividades el filtro en f() de lo que llego por parametros
	// Si viene seteada una comision con todas las actividades de esa comision
	if ( $comision ) {
             $this->db->where('comision',$comision);
             $query = $this->db->get('actividades');
	} else {
	// Si viene seteada una actividad esa actividad puntual
	     if ( $actividad ) {
		if ( $actividad > 0 ) {
             		$this->db->where('id',$actividad);
             		$query = $this->db->get('actividades');
		}
	     }
	}
	// Si vino algun parametro y el SQL no encontro nada salgo con false
	if ( $comision || $actividad ) {
        	if($query->num_rows() == 0){return false;}
		$actividades = $query->result();
	} else {
	// Sino vino parametros pongo null la variable p luego tomar TODOS LOS SOCIOS
		$actividades = null;
	}

	// Busco el conjunto de socios morosos (tanto p actividad como p cuota social)
    $hoy=date('Ym');
    $this->db->select('p.tutor_id sid, SUM(p.monto-p.pagado) deuda');
    $this->db->where('p.estado',1);
    $this->db->where('p.tipo !=',5);
    $this->db->where('DATE_FORMAT(p.generadoel, "%Y%m") <',$hoy);
    if ( $actividades ) {
        $in=array();
        foreach ( $actividades as $actividad ) {
            $in[]=$actividad->Id;
        }
        $this->db->where_in('p.aid',$in);
    }
    $this->db->group_by('p.tutor_id');
    $this->db->having('SUM(p.monto-p.pagado) > 0');
    $query = $this->db->get('pagos as p');
    $morosos = $query->result();

	// Seteo un array vacio para meter toda la info
	// Ciclo los morosos para buscar la info especifica 
	$result_morosos=array();
	foreach ( $morosos as $moroso ) {
		$sid=$moroso->sid;
		// Busco datos fijos del socio
        $this->db->where('Id',$sid);
        $query = $this->db->get('socios',1);
        if ( $query->num_rows() == 0 ) {
            // Llenar el array....
			continue;
		} else {
			$socio = $query->row();
		}

		// Busco la deuda de cuotas sociales
		$hoy=date('Ym');
        $this->db->select('p.tutor_id sid, COUNT(*) meses, MIN(DATE(p.generadoel)) pago, SUM(p.monto-p.pagado) deuda ');
        $this->db->where('p.estado',1);
        $this->db->where('p.tipo',1);
        $this->db->where('p.tutor_id',$sid);
        $this->db->where('DATE_FORMAT(p.generadoel, "%Y%m") <',$hoy);
        $this->db->group_by('p.tutor_id');
        $query = $this->db->get('pagos as p');
        if ( $query->num_rows() == 0 ) {
            $deuda_cuotas = null;
            $meses_cuota = 0;
            $gen_cuota = 0;
            $deuda_cuota = 0;
		} else {
			$dc = $query->row();
			$meses_cuota = $dc->meses;
			$gen_cuota = $dc->pago;
			$deuda_cuota = $dc->deuda;
		}

		// busco la deuda de actividades
        $this->db->select('p.tutor_id sid, p.aid, count(*) meses, min(date(p.generadoel)) pago, sum(p.monto-p.pagado) deuda ');
        $this->db->where('p.estado',1);
        $this->db->where('p.tipo',4);
        $this->db->where('p.tutor_id',$sid);
        $this->db->where('date_format(p.generadoel, "%Y%m") <',$hoy);
        $this->db->group_by('p.tutor_id, p.aid');
        $query = $this->db->get('pagos as p');
        if ( $query->num_rows() == 0 ) {
            $result=array ( 
				'dni' => $socio->dni,
				'sid' => $sid,
				'apynom' => $socio->nombre.", ".$socio->apellido,
				'telefono' => "F: ".$socio->telefono." C: ".$socio->celular,
				'domicilio' => $socio->domicilio,
				'actividad' => "Solo Cuota Social",
				'estado' => $socio->suspendido,
				'meses_cuota' => $meses_cuota,
				'gen_cuota' => $gen_cuota,
				'deuda_cuota' => $deuda_cuota,
				'meses_activ' => 0,
				'gen_activ' => 0,
				'deuda_activ' => 0
            );
            $result_morosos[]=$result;
		} else {
            $deuda_activ = $query->result();
            $query->free_result();
			// Ciclo las actividades con deuda para llenar el array
            $cont=0;
            foreach ( $deuda_activ as $da ) {
                $aid = $da->aid;
                $this->db->where('Id',$aid);
                $query = $this->db->get('actividades',1);
                $activ = $query->row();
                $descr_activ = $activ->nombre;

                if ( $cont++ == 0 ) {
                    $mcuota=$meses_cuota;
                    $gcuota=$gen_cuota;
                    $dcuota=$deuda_cuota;
                } else {
                    $mcuota=0;
                    $gcuota=0;
                    $dcuota=0;
                }
				$result=array ( 
					'dni' => $socio->dni,
					'sid' => $sid,
					'apynom' => $socio->nombre.", ".$socio->apellido,
					'telefono' => "F: ".$socio->telefono." C: ".$socio->celular,
					'domicilio' => $socio->domicilio,
					'actividad' => $descr_activ,
					'estado' => $socio->suspendido,
					'meses_cuota' => $mcuota,
					'gen_cuota' => $gcuota,
					'deuda_cuota' => $dcuota,
					'meses_activ' => $da->meses,
					'gen_activ' => $da->pago,
					'deuda_activ' => $da->deuda
				);
				$result_morosos[]=$result;
			}
		}
	}

    return $result_morosos;
    }

    public function get_pagos_actividad($act){
        $this->db->where('aid',$act);
        $this->db->where('estado',1);
        $query = $this->db->get('actividades_asociadas');
        $asoc = $query->result();
        $query->free_result();
        
        $this->load->model("socios_model");

        foreach ($asoc as $a) {
            $socio = $this->socios_model->get_socio($a->sid);    
            $a->Id = $socio->Id;        
            $a->socio = @$socio->nombre.' '.@$socio->apellido;
            $a->telefono = @$socio->telefono;
            $a->nacimiento = @$socio->nacimiento;
            $a->dni = @$socio->dni;
            $a->suspendido = @$socio->suspendido;
            $a->observaciones = @$socio->observaciones;
            $a->act_nombre = $this->actividades_model->get_actividad($a->aid)->nombre;            
            //@$a->deuda = $this->pagos_model->get_deuda($socio->Id);
            //@$a->deuda = $this->pagos_model->get_ultimo_pago_actividad($a->aid,$socio->Id);
            @$a->deuda = $this->pagos_model->get_deuda_actividad($a->aid,$socio->Id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
	        $array_ahg = $this->pagos_model->get_monto_socio($socio->Id);
            @$a->cuota = $array_ahg['total'];
            /* Fin Modificacion AHG */
            @$a->monto_adeudado = $this->pagos_model->get_socio_total($socio->Id);
        }
        return $asoc;
    } 

    public function get_pagos_profesor($id)
    {
        $this->db->where('profesor',$id);
        $query = $this->db->get('actividades');
        $actividades = $query->result();
        $socios = array();
        foreach ($actividades as $actividad) {
            $socios[] = $this->get_pagos_actividad($actividad->Id);
        }        
        return $socios;
    }

    public function get_usuarios_suspendidos()
    {
        $this->db->where('suspendido',1);
        $this->db->where('estado',1);
        $this->db->order_by('apellido','asc');
        $this->db->order_by('nombre','asc');
        $query = $this->db->get('socios');
        $socios = $query->result();
        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->Id);
        }
        $query->free_result();
        return $socios;
    }

    public function get_socios_activos($value='')
    {
        $this->db->where('suspendido',0);
        $this->db->where('estado',1);
        $this->db->order_by('apellido','asc');
        $this->db->order_by('nombre','asc');
        $query = $this->db->get('socios');
        $socios = $query->result();
        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->Id);
        }
        $query->free_result();
        return $socios;
    }

    public function get_pagos_mensual($aid,$anio,$mes){
	$pagos = 0;
	$this->db->select_sum('monto');
	$this->db->where($mes, 'MONTH(generadoel)' , FALSE);
	$this->db->where($anio, 'YEAR(generadoel)' , FALSE);
	if ( $aid == 0 ) {
		$this->db->where('aid' , 0);
	} else {
		$this->db->where('aid' , '> 0');
	}
	$query = $this->db->get('pagos');
	if($query->num_rows() != 0){
		$pagos = $query->row()->monto;
	} else {
		$pagos = 0;
	}
	
	return $pagos;
    }

    public function get_pagos_categorias($id){
        $this->db->where('estado',1);
        $this->db->where('categoria',$id);
        $query = $this->db->get('socios');
        $socios = $query->result();

        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->Id);
            $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($socio->Id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
	        $array_ahg = $this->pagos_model->get_monto_socio($socio->Id);
            $socio->cuota = $array_ahg['total'];
            /* Fin Modificacion AHG */
        }
        return $socios;

    }

    public function get_ingresos($fecha1='',$fecha2='')
    {    
        /*$this->db->order_by('facturacion.date','asc');
        $this->db->where('facturacion.date >=',$fecha1.' 0:00:00');
        $this->db->where('facturacion.date <=',$fecha2.' 23:59:59');
        $this->db->where('facturacion.haber >',0);
        $this->db->join('socios','socios.Id = facturacion.sid');
        $query = $this->db->get('facturacion');
        if($query->num_rows() == 0){return false;}
        $ingresos = $query->result();
        foreach ($ingresos as $ingreso) {
            $cuota = $acts = array();
            $this->db->where('estado',0);            
            $this->db->where('sid',$ingreso->sid);
            $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
            $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
            $query = $this->db->get('pagos');
            if($query->num_rows() != 0){
                $pagos = $query->result();
                foreach ($pagos as $pago) {
                    if($pago->tipo == 1 || $pago->tipo == 2 || $pago->tipo == 3){
                        $cuota[] = $pago;
                    }else if($pago->tipo == 4){
                        $acts[] = $pago;
                    }
                }
                $ingreso->cuota = $cuota;                
                $ingreso->acts = $acts;                
            }
        }
        $query->free_result();*/

        //$this->db->where('estado',0);            
        //$this->db->where('sid',$ingreso->sid);
        $this->load->model('socios_model');
        $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
        $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){ return false; }
        $pagos = $query->result();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->tutor_id);
        }        
        return $pagos;
    }

     public function get_ingresos_cuentadigital($fecha1='',$fecha2='')
    {            
        $this->load->model('socios_model');
        $this->db->where('fecha >=',$fecha1.' 0:00:00');
        $this->db->where('fecha <=',$fecha2.' 23:59:59');
        $query = $this->db->get('cuentadigital');
        if($query->num_rows() == 0){ return false; }
        $pagos = $query->result();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
        }        
        return $pagos;
    }

    public function get_cobros_actividad($fecha1='',$fecha2='',$actividad=false,$categoria=false)
    {
        $this->load->model('actividades_model');
        $this->load->model('socios_model');
        $actividad = $this->actividades_model->get_actividad($actividad);
        //$this->db->select('sid');
        //$this->db->distinct();
        $this->db->where('estado',0);
        $this->db->where('aid',$actividad->Id);
        $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
        $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $pagos = $query->result();
        $res = array();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
            $pago->deuda = $this->get_deuda_actividad($actividad->Id,$pago->sid);
            if($categoria != ''){
                if(date('Y',strtotime($pago->socio->nacimiento)) != $categoria){
                    continue;
                }                
            }    
            $res[] = $pago;           
        }                 
        return $res;
    }

    public function get_cobros_cuota($fecha1='',$fecha2='',$categoria=false)
    {        
        $this->load->model('socios_model');        
        //$this->db->select('sid');
        //$this->db->distinct();
        $this->db->where('estado',0);
        $this->db->where('tipo',1);
        $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
        $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $pagos = $query->result();
        $res = array();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
            $pago->deuda = $this->get_deuda_cuota($pago->sid);
            if($categoria != ''){
                if(date('Y',strtotime($pago->socio->nacimiento)) != $categoria){
                    continue;
                }                
            }    
            $res[] = $pago;           
        }                 
        return $res;
    }

    public function insert_pago_nuevo($pago)
    {
        if ( $pago['monto'] == 0 AND $pago['tipo'] != 5 ) {
             $pago['pagadoel'] = $pago['generadoel'];
             $pago['estado'] = 0;
        }
        $this->db->insert('pagos',$pago);
    }


    public function registrar_pago2($sid=false,$monto='0')
    {
        if(!$sid){return false;}
        //$this->load->model('pagos_model');
        $this->db->where('tipo',5);
        $this->db->where('tutor_id ',$sid);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){
            $pago = array(
                'sid' => $sid, 
                'tutor_id' => $sid,
                'aid' => 0, 
                'generadoel' => date('Y-m-d'),
                'descripcion' => "A favor",
                'monto' => 0,
                'tipo' => 5,
            );
            $this->insert_pago_nuevo($pago);
            $a_favor = 0;
        }else{            
            $a_favor = $query->row()->monto;            
        }
        $monto = $monto + $a_favor*-1;
        

        $this->db->order_by('generadoel','asc');
        $this->db->order_by('tipo','asc');
        $this->db->where('tipo !=',5);        
        $this->db->where('monto >',0);        
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',1);
        $query = $this->db->get('pagos');
        foreach ($query->result() as $pago) {
            if($monto > 0){
                if( ($pago->monto - $pago->pagado) <= $monto){ 
                    if($pago->pagado == 0){
                        $pagado = $pago->monto;
                        $monto = $monto - $pagado;
                    }else{
                        $pagado = $pago->monto;
                        $monto = $monto - ($pago->monto - $pago->pagado);
                    }
                    $this->db->where('Id',$pago->Id);   
                    $this->db->update('pagos',array('pagado'=>$pagado,'estado'=>0,'pagadoel'=>date('Y-m-d H:i:s')));
                }else{
                    if($pago->pagado == 0){
                        $pagado = $monto;
                    }else{
                        $pagado = $pago->pagado+$monto;
                    }
                    $this->db->where('Id',$pago->Id);
                    $this->db->update('pagos',array('pagado'=>$pagado,'pagadoel'=>date('Y-m-d H:i:s')));
                    $monto = 0;        
                }                
            }
        }

        $this->db->where('tutor_id',$sid);
        $this->db->where('tipo',5);
        $this->db->update('pagos',array('monto'=>$monto*-1));

    }

    public function get_ultimo_pago_actividad($aid,$sid)
    {
        $this->db->order_by('pagadoel', 'desc');
        $this->db->where('aid',$aid);
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_deuda_actividad($aid,$sid)
    {
        $this->db->order_by('generadoel', 'asc');
        $this->db->where('aid',$aid);
        $this->db->where('sid',$sid);
        $this->db->where('estado',1);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_saldo($sid)
    {
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',1);
        $this->db->select_sum('monto');
        $this->db->select_sum('pagado');
        $query = $this->db->get('pagos');
	$saldo=$query->row()->monto-$query->row()->pagado;
	return $saldo;
    }

    public function get_deuda_cuota($sid)
    {
        $this->db->order_by('generadoel', 'asc');
        $this->db->where('tipo',1);
        $this->db->where('sid',$sid);
        $this->db->where('estado',1);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_ultimo_pago_cuota($sid='')
    {
        $this->db->order_by('pagadoel', 'desc');
        $this->db->where('tipo',1);
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_ultimo_pago_socio($sid)
    {
        //$this->db->where('aid',$aid);
        $this->db->order_by('generadoel','asc');
        $this->db->where('tutor_id',$sid);
        $this->db->where('tipo !=',5);
        $this->db->where('tipo !=',4);
        $this->db->where('tipo !=',2);
        $this->db->where('estado',1);
        $query = $this->db->get('pagos',1);
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_pagos_actividad_anterior($act){
        $this->db->where('aid',$act);
        $this->db->where('estado',1);
        $query = $this->db->get('actividades_asociadas');
        $asoc = $query->result();
        $query->free_result();
        
        $this->load->model("socios_model");

        foreach ($asoc as $a) {
            $socio = $this->socios_model->get_socio($a->sid); 
            $a->Id = $socio->Id;           
            $a->socio = @$socio->nombre.' '.@$socio->apellido;
            $a->telefono = @$socio->telefono;
            $a->nacimiento = @$socio->nacimiento;
            $a->dni = @$socio->dni;
            $a->suspendido = @$socio->suspendido;
            $a->act_nombre = $this->actividades_model->get_actividad($a->aid)->nombre;            
            //@$a->deuda = $this->pagos_model->get_deuda($socio->Id);
            $a->deuda = 0;
            $this->db->where('sid',$a->Id);
            $this->db->where('tipo',1);
            $this->db->where('monto >',0);
            $this->db->where('descripcion','Deuda Anterior');
            //$this->db->where('generadoel <','2015-05-01 00:00:00');
            $query = $this->db->get('pagos');
            if($query->num_rows != 0){
                $a->deuda = $query->row()->estado;                
            }
        }
        return $asoc;
    }

    public function get_socios_financiados()
    {        
        $this->db->where('fn.estado',1);
        $this->db->join('socios','socios.Id = fn.sid');
        $query = $this->db->get('financiacion as fn');
        if($query->num_rows() == 0){ return false; }
        $socios = $query->result();
        $query->free_result();
        return $socios;
    }

    public function get_becas($actividad='')
    {
        if($actividad == -1){
            $this->db->where('descuento >',0);
            $this->db->where('estado',1);
            $query = $this->db->get('socios');
            if($query->num_rows() == 0){ return false; }
            $socios = $query->result();
            $query->free_result();
            return $socios;
        }else{
            $this->db->select('aa.*, socios.*, aa.descuento as descuento, aa.monto_porcentaje as monto_porcentaje, socios.Id as Id');
            $this->db->where('aa.aid',$actividad);
            $this->db->where('aa.descuento >',0);
            $this->db->where('aa.estado',1);
            $this->db->join('socios', 'socios.Id = aa.sid', 'left');
            $query = $this->db->get('actividades_asociadas as aa');
            if($query->num_rows() == 0){ return false; }
            $socios = $query->result();
            $query->free_result();
            return $socios;
        }
    }

    public function get_sin_actividades()
    {
        $this->load->model('socios_model');
        $socios = $this->socios_model->get_socios();
        $sin_actividades = array();
        foreach ($socios as $socio) {
            $this->db->where('sid', $socio->Id);
            $this->db->where('estado',1);
            $query  = $this->db->get('actividades_asociadas');
            if( $query->num_rows() == 0 ){
                $sin_actividades[] = $socio;
            }
        }
        return $sin_actividades;
    }
    
    public function insert_cuentadigital($pago='')
    {
        $this->db->insert('cuentadigital',$pago);
    }

    public function get_pagos_edit($socio_id)
    {
        $this->db->select('pagos.*,actividades.nombre');
        $this->db->where('pagos.tutor_id', $socio_id);
        $this->db->where('pagos.generadoel >=',date('Y-m').'-01');
        $this->db->join('actividades', 'actividades.Id = pagos.aid', 'left');
        $query = $this->db->get('pagos');
        if( $query->num_rows() == 0 ){ return false; }
        $pagos = $query->result();
        $query->free_result();
        return $pagos;
    }

    public function eliminar_pago($id)
    {
        $this->db->where('pagos.Id',$id);
        $this->db->join('actividades', 'actividades.Id = pagos.aid', 'left');
        $query = $this->db->get('pagos');
        if( $query->num_rows() == 0 ){ return false; }
        $pago = $query->row();

        //actualizamos saldo a favor
        $a_favor = $pago->pagado;
        if($a_favor > 0){
            $this->db->where('tutor_id', $pago->tutor_id);
            $this->db->where('tipo', 5);
            $this->db->set('monto', 'monto-'.$a_favor, FALSE);
            $this->db->update('pagos');            
        }

        //actualizamos facturacion
        $facturacion = $pago->monto;
        if($facturacion > 0){
            switch ($pago->tipo) {
                case 1:
                    $descripcion = 'Cuota Social - '.date('d/m/Y',strtotime($pago->generadoel));
                    break;
                
                case 2:
                    $descripcion = 'Recargo por Mora - '.date('d/m/Y',strtotime($pago->generadoel));
                    break;

                case 3:
                    $descripcion = 'Financiación de deuda - '.date('d/m/Y',strtotime($pago->generadoel));
                    break;

                case 4:
                    $descripcion = 'Actividad: '.$pago->nombre.' - '.date('d/m/Y',strtotime($pago->generadoel));
                    break;      
            }
            $total = $this->get_socio_total($pago->tutor_id);
            $facturacion = array(
                'sid' => $pago->tutor_id, 
                'descripcion'=> "CORRECCIÓN MANUAL DE PAGOS: ".$descripcion,
                'debe'=>0,
                'haber'=>$facturacion,
                'total'=>$total+$facturacion
            );
            $this->db->insert('facturacion', $facturacion);
        }

        $this->db->where('Id', $id);
        $this->db->delete('pagos');
        $query->free_result();
        return $pago->tutor_id;
    }

    public function get_facturacion_all()
    {        
        $this->db->order_by('facturacion.Id', 'asc');
        //$this->db->join('socios', 'socios.Id = facturacion.sid', 'left');
        $query = $this->db->get('facturacion');
        if( $query->num_rows() == 0 ){ return false; }
        $foo = $query->result();
        $query->free_result();
        return $foo;
    }
}

