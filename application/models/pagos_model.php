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

    public function get_cupon($sid, $id_entidad){
        $this->load->model("socios_model");
        $socio = $this->socios_model->get_socio($sid);
        if($socio->tutor != 0){
            $grupo_familiar = true;
            //si el socio pertenece a un grupo familiar buscamos el cupon del tutor
            return $this->get_cupon($socio->tutor);
        }

        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->where('sid',$sid);
        $query = $this->db->get('cupones');
        if($query->num_rows() == 0){
            $cupon = new stdClass();
            $cupon->monto = '0';
            return $cupon;
        }else{
            return $query->row();
        }
    }

    public function get_cupones_old($id_entidad) 
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',0);
        $this->db->where('DATE(date) < DATE_SUB(CURDATE(), INTERVAL 360 DAY)');
        $query = $this->db->get('cupones');
        if($query->num_rows() == 0){return false;}
        $cupones = $query->result();
        return $cupones;
    }

    public function get_cupon_by_id($id='')
    {
        $this->db->where('id',$id);
        $query = $this->db->get('cupones');
        if($query->num_rows() == 0){return false;}
        $cupon = $query->row();
        $query->free_result();
        return $cupon;
    }

    public function get_monto_socio($sid){ // devuelve el importe que deberá pagar un socio o su tutor, en caso de pertenecer a un grupo familiar
        $grupo_familiar = $tutor = false;
        $monto = 0;

        //buscamos si el socio pertenece a un grupo familiar
        $this->load->model("socios_model");
        $socio = $this->socios_model->get_socio($sid);
	$id_entidad = $socio->id_entidad;

	// Pongo el valor del excedente de grupo familiar de 4 al 10% del valor de la cuota del grupo
        $this->load->model("general_model");
        $cat_flia = $this->general_model->get_cat_tipo($id_entidad, "F");
	$precio_grupo = $cat_flia->precio;
        $precio_excedente = $precio_grupo * 0.10;

        //obtenemos el precio de su categoria
        $this->load->model("general_model");
        $cat_socio = $this->general_model->get_cat($socio->categoria);
	$precio_socio = $cat_socio->precio;

        if($socio->tutor != 0){
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
                $fam_actividades = $this->get_actividades_socio($id_entidad, $familiar->id);
                $cat_fam = $this->general_model->get_cat($familiar->categoria);
                $familiares[] = array('datos' => $familiar, 'actividades' => $fam_actividades, 'valor_cat' => $cat_fam->precio);
            }

            //buscamos las actividades del socio titular
            $socio_actividades =  $this->get_actividades_socio($id_entidad, $sid);
            //buscamos los familiares excedentes
            $excedente = $total_familiares - 4; // 4 = total del grupo familiar
            $monto_excedente = 0;
            for ($i=0; $i < $excedente; $i++) {
                $monto_excedente = $monto_excedente + $precio_excedente;
            }

	    // Si tiene hasta 2 tutoreados se toman categorias de cada uno, sino grupo familiar
	    if ( $total_familiares > 3 ) {
            	$monto = $precio_grupo - ( $precio_grupo * $socio->descuento / 100); //valor de la cuota de grupo familiar
            	$total = $monto + ( $monto_excedente - ($monto_excedente * $socio->descuento / 100) ); //cuota mensual mas el excedente en caso de ser mas socios de lo permitido en el girpo fliar
		$grupo_familiar = true;
	    } else {
		$monto = $total = 0;
		// Ciclo por los familiares y agrego lo propio del socio
		foreach ($familiares as $fam ) {
			$monto = $monto + $fam['valor_cat'];
		}
		$monto = $monto + $precio_socio;
		$total = $monto;
	    }

            foreach ($socio_actividades['actividad'] as $actividad) {
		// actividades del titular del grupo familiar
		if ( $actividad->monto_porcentaje == 0 ) {
			if ( $actividad->precio > 0 ) {
                		 $total = $total + ( $actividad->precio - $actividad->descuento );
			}
		} else {
                	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100) );
		}
		if ( $actividad->federado == 0 ) {
			$total=$total+$actividad->seguro;
		}
            }
            foreach ($familiares as $familiar) {
                foreach($familiar['actividades']['actividad'] as $actividad){
		//actividades del los socios del grupo famlilar
		    if ( $actividad->monto_porcentaje == 0 ) {
			if ( $actividad->precio > 0 ) {
                    		$total = $total + ( $actividad->precio - $actividad->descuento );
			}
		    } else {
                    	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100) );
		    }
		    if ( $actividad->federado == 0 ) {
			    $total=$total+$actividad->seguro;
		    }
                }
            }

            $financiacion = $this->get_financiado_mensual($id_entidad, $socio->id);
            $f_total = 0;
            if($financiacion){
                foreach ($financiacion as $plan) {
                    $f_total = $f_total + round($plan->monto/$plan->cuotas,2);
                }
            }

            $total = $total + $f_total;
	    if ( $grupo_familiar ) {
		$xcateg = "Grupo Familiar";
		$tcateg = $cat_flia->tipo;
	    } else {
		$xcateg = "Tutor";
		$tcateg = $cat_socio->tipo;
	    }
            $cuota = array(
                "tid" => $sid,
                "id_entidad" => $id_entidad,
                "titular" => $socio->apellido.' '.$socio->nombre,
                "total" => $total,
                "categoria" => $xcateg,
                "categ_tipo" => $tcateg,
                "cuota" => $monto,
                "familiares" => $familiares,
                "actividades" => $socio_actividades,
                "excedente" => $excedente,
                "monto_excedente" => $monto_excedente- ($monto_excedente * $socio->descuento / 100),
                "financiacion" => $financiacion,
                "descuento" => $socio->descuento,
                "cuota_neta"=>$precio_grupo
            );
            return $cuota;

        }else{ //si no esta en un grupo familiar
            $socio_actividades =  $this->get_actividades_socio($id_entidad, $sid); //buscamos las actividades del socio
            $socio_cuota = $cat->precio - ($cat->precio * $socio->descuento / 100); //precio de la cuota
            $total = $socio_cuota; //cuota mensual
            foreach ($socio_actividades['actividad'] as $actividad) {
		//actividades del socio
		if ( $actividad->monto_porcentaje == 0 ) {
			if ( $actividad->precio > 0 ) {
                		$total = $total + ( $actividad->precio - $actividad->descuento );
			}
		} else {
                	$total = $total + ( $actividad->precio - ($actividad->precio * $actividad->descuento /100 ) );
		}
		if ( $actividad->federado == 0 ) {
			$total=$total+$actividad->seguro;
		}
            }

            $financiacion = $this->get_financiado_mensual($id_entidad, $socio->id);
            $f_total = 0;
            if($financiacion){
                foreach ($financiacion as $plan) {
                    $f_total = $f_total + round($plan->monto/$plan->cuotas,2);
                }
            }


            $total = $total + $f_total;
            $cuota = array(
                "tid" => $sid,
                "id_entidad" => $id_entidad,
                "titular" => $socio->apellido.' '.$socio->nombre,
                "total" => $total,
                "categoria" => $cat->nombre,
                "categ_tipo" => $cat->tipo,
                "cuota" => $socio_cuota,
                "familiares" => '0',
                "actividades" => $socio_actividades,
                "excedente" => '0',
                "monto_excedente" => '0',
                "financiacion" => $financiacion,
                "descuento" => $socio->descuento,
                "cuota_neta"=>$cat->precio
            );
        return $cuota;
        }
    }

    function get_actividades_socio($id_entidad, $sid){
        $this->load->model("socios_model");  //buscamos datos del socio
        $socio = $this->socios_model->get_socio($sid);
        $this->load->model("actividades_model"); //buscamos las actividades del socio
        $actividades = $this->actividades_model->get_act_asoc($id_entidad,$sid);
        $act = array();
        foreach ($actividades as $actividad) {

                    if($actividad->estado != '0'){
                        $act[] = $actividad;
                    }
                }
        $actividades_socio = array('actividad' =>$act);
        return $actividades_socio; // devolvemos los datos de la actividad y el usuario correspondiente
    }

    function generar_cupon($id_entidad, $sid, $monto,$cupon)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('sid',$sid); 
	// ponemos en 0 todos los cupones de este socio
        $this->db->update('cupones',array('estado'=>'0'));
        $data = array(
                'sid' => $sid,
                'id_entidad' => $id_entidad,
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
        $this->db->order_by('id','desc');
        $fact = $this->db->get('facturacion');
        if($fact->num_rows() == 0){return false;}
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
                'id_entidad' => $data['id_entidad'],
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

    function get_cobcols($id_entidad, $periodo)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('periodo',$periodo);
        $query = $this->db->get('facturacion_col');

	$registros=array();
        foreach ($query->result() as $fact) {

		$nro_socio=$fact->sid;
		$cobcol= get_cobcol($id_entidad, $nro_socio,$periodo);
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
                	'id_entidad' => $id_entidad,
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

    function get_cobcol($id_entidad, $sid, $periodo) {
        $this->db->where('id_entidad', $id_entidad);
        $this->db->where('sid', $sid);
        $this->db->where('periodo', $periodo);
        $query = $this->db->get('cobranza_col');
        if($query->num_rows() == 0){
		return false;
	} else {
		return $query->row();
	}
    }

    public function check_cron($id_entidad, $periodo)
    { //comprueba si ya se ejecuto la tarea este mes o si esta en curso
	$anio=substr($periodo,0,4);
	$mes=substr($periodo,4,2);
	$ahora=date($anio.'-'.$mes.'-'.'01 00:00:00');
        $this->db->where('YEAR(date)' , $anio);
        $this->db->where('MONTH(date)' , $mes);
        $this->db->where('id_entidad' , $id_entidad);

        $query = $this->db->get('facturacion_cron');
        if($query->num_rows() == 0){
            $this->db->insert( 'facturacion_cron',array('id_entidad'=>$id_entidad,'date'=>$ahora, 'des'=>'0','en_curso'=>1) );
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

    public function update_facturacion_cron($id_entidad, $periodo, $tipo, $cant, $importe)
    {
	$anio=substr($periodo,0,4);
	$mes=substr($periodo,4,2);
        $this->db->where('id_entidad' , $id_entidad);
        $this->db->where('YEAR(date)' , $anio);
        $this->db->where('MONTH(date)' , $mes);
	$query = $this->db->get('facturacion_cron');
	if ( $query->num_rows() == 0 ) {
		return false;
	} else {
		$cron_state = $query->row();
		if ( $cron_state->en_curso == 1 ) {
			$this->db->where( 'id', $cron_state->id );
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

    public function get_facturacion_cron($id_entidad, $periodo)
    {
        $anio=substr($periodo,0,4);
        $mes=substr($periodo,4,2);
        $this->db->where('id_entidad' , $id_entidad);
        $this->db->where('YEAR(date)' , $anio);
        $this->db->where('MONTH(date)' , $mes);
        $query = $this->db->get('facturacion_cron');
        if ( $query->num_rows() == 0 ) {
                return false;
        } else {
		return $query->row();
	}
     }


    public function insert_facturacion_cron()
    {
    }

    public function get_facturacion($id_entidad, $sid)
    {
        $this->db->order_by('DATE(date)','desc');
        $this->db->order_by('id','desc');
        $this->db->where("sid", $sid);
        $this->db->where("id_entidad", $id_entidad);
        $query = $this->db->get('facturacion');
        return $query->result();
    }

    public function check_cron_pagos($id_entidad)
    { //comprueba si ya se ejecuto la tarea hoy
        $this->db->where('id_entidad' , $id_entidad);
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
    public function insert_pagos_cron($id_entidad, $fecha)
    {
        $this->db->insert('pagos_cron',array('id_entidad'=>$id_entidad,'date'=>$fecha,'des'=>'0'));
    }

    public function insert_pago_col($id_entidad, $pago)
    {
        $total = $this->get_deuda($pago['sid']);
        $total = $total + $pago['monto'];
        $descripcion = "Pago acreditado desde: La Coope <br>Fecha: ".$pago['fecha'].' '.$pago['hora'];
        $this->db->insert('facturacion',array('id_entidad'=>$id_entidad,'sid'=>$pago['sid'],'haber'=>$pago['monto'],'total'=>$total,'descripcion'=>$descripcion,'origen'=>'1'));

    }

    public function insert_pago($id_entidad, $pago)
    {
        $total = $this->get_deuda($pago['sid']);
        $total = $total + $pago['monto'];
        $descripcion = "Pago acreditado desde: CuentaDigital <br>Fecha: ".$pago['fecha'].' '.$pago['hora'];
        $this->db->insert('facturacion',array('id_entidad'=>$id_entidad, 'sid'=>$pago['sid'],'haber'=>$pago['monto'],'total'=>$total,'descripcion'=>$descripcion,'origen'=>'2'));

    }

    public function registrar_pago($id_entidad,$tipo,$sid,$monto,$des,$actividad,$ajuste='0',$origen='0')
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
		// Si en la leyenda pongo que es un Seguro lo tipifico como 6 pero con el aid de la actividad
		if ( substr($des,0,6) == "Seguro" ) {
                	$aid = $actividad;
                	$tipo = 6;
		} else {
                	$aid = $actividad;
                	$tipo = 4;
		}
            }
            $pago = array(
                'sid' => $sid,
                'tutor_id' => $sid,
                'id_entidad' => $id_entidad,
                'aid' => $aid,
                'generadoel' => date('Y-m-d'),
                'descripcion' => $des,
                'monto' => $monto,
                'tipo' => $tipo,
                );
            $this->pagos_model->insert_pago_nuevo($pago);
            $this->registrar_pago2($id_entidad,$sid,0);
        }else{
            $haber = $monto;
            $debe = '0.00';
            $total = $total + $haber;
            $this->registrar_pago2($id_entidad,$sid,$monto,$ajuste);
        }
	if ( $ajuste == 1 ) {
		$orig=4;
	} else {
		$orig=3;
	}
        $data = array(
                "sid" => $sid,
                "id_entidad" => $id_entidad,
                "descripcion" => $des,
                "debe" => $debe,
                "haber" => $haber,
                "total" => $total,
		"origen" => $orig
            );
        $this->db->insert("facturacion",$data);
        $data['iid'] = $this->db->insert_id();
        $data['fecha'] = date('d/m/Y');
        $data = json_encode($data);
        return $data;
    }

    public function busca_fact_mes($id_entidad, $sid){
      $mes = date('Ym');
      $this->db->where('id_entidad',$id_entidad);
      $this->db->where('sid',$sid);
      $this->db->where('aid',0);
      $this->db->where('DATE_FORMAT(generadoel,"%Y%m")',$mes);
      $query = $this->db->get('pagos');
      if($query->num_rows() == 0){ return false;}
      $fact_mes = $query->row();
      return $fact_mes;
    }

    public function get_deuda_monto($id_entidad, $sid){
      $this->db->select('p.tutor_id sid, SUM(p.monto-p.pagado) deuda');
      $this->db->where('p.id_entidad',$id_entidad);
      $this->db->where('p.estado',1);
      $this->db->where('p.aid',0);
      $this->db->where('p.tutor_id',$sid);
      $this->db->where('DATE_FORMAT(p.generadoel,"%Y%m") < DATE_FORMAT(CURDATE(),"%Y%m")');
      $this->db->group_by('p.tutor_id');
      $this->db->having('SUM(p.monto-p.pagado) > 0');
      $query = $this->db->get('pagos as p');
      if($query->num_rows() == 0){ return false;}
      $deuda = $query->row()->deuda;
      return $deuda;

    }

    public function get_deuda_aviso($id_entidad){
      $this->db->select('p.tutor_id sid, p.sid tutoreado, s.dni, s.apellido, s.nombre, s.mail, SUM(p.monto-p.pagado) deuda');
      $this->db->where('p.id_entidad',$id_entidad);
      $this->db->where('p.estado',1);
      $this->db->where('s.suspendido',0);
      $this->db->where('DATE_ADD(s.alta, INTERVAL 35 DAY)< CURDATE()');
      $this->db->join('socios as s','s.id = p.tutor_id');
      $this->db->group_by('p.tutor_id');
      $this->db->having('SUM(p.monto-p.pagado) > 260');
      $query = $this->db->get('pagos as p');
      if($query->num_rows() == 0){ return false;}
      $deudores = $query->result();
      $query->free_result();
      return $deudores;
    }

    public function get_deuda($sid){
        $this->db->where('sid',$sid);
        $this->db->order_by('id','desc');
        $query = $this->db->get('facturacion');
        if($query->num_rows() == 0){ return 0;}
        $deuda = $query->row()->total;
        return $deuda;
    }

    public function get_deuda_sinhoy($sid){
        $this->db->where('sid',$sid);
        $this->db->where('DATE(date) < CURDATE()');
        $this->db->order_by('id','desc');
        $query = $this->db->get('facturacion');
        if($query->num_rows() == 0){ return 0;}
        $deuda = $query->row()->total;
        return $deuda;
    }

    public function financiar_deuda($id_entidad, $socio,$monto,$cuotas,$detalle){
        $inicio = date('Y-m-d');
        $fin = $inicio;
        $financiacion = array(
            'sid' => $socio,
            'id_entidad' => $id_entidad,
            'cuotas' => $cuotas,
            'monto' => $monto,
            'inicio' => $inicio,
            'fin' => $fin,
            'detalle'=>$detalle
            );
        $this->db->insert('financiacion',$financiacion);

	$credito=$monto-($monto/$cuotas);
	$this->registrar_pago($id_entidad,'haber',$socio,$credito,'Refinanciacion de $ '.$monto.' de deuda en '.$cuotas.' cuotas',0,1);
    }

    public function get_planes($id_entidad, $sid){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('sid',$sid);
        $query = $this->db->get('financiacion');
        $planes = $query->result();
        $query->free_result();
        return $planes;
    }

    public function cancelar_plan($id){
        $this->db->where('id',$id);
        $this->db->update('financiacion',array('estado'=>2));
    }

    public function get_financiado_mensual($id_entidad, $sid){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('sid',$sid);
        $this->db->where('estado',1);
        $query = $this->db->get('financiacion');
        if($query->num_rows() == 0){return false;}
        $planes = $query->result();
        $query->free_result();
        return $planes;
    }

    public function update_cuota($id){
        $this->db->where('id',$id);
        $this->db->where('estado',1);
        $query = $this->db->get('financiacion');
        if($query->num_rows() == 0){return false;}
        $plan = $query->row();

	$hoy = date('Y-m-d');
        $this->db->where('id',$id);
        $this->db->where('estado',1);
	if ( $plan->actual+1 >= $plan->cuotas ) {
        	$this->db->set('actual','actual+1',false);
        	$this->db->set('fin',"'".$hoy."'",false);
        	$this->db->set('estado',2,false);
        	$this->db->update('financiacion');
	} else {
        	$this->db->set('actual','actual+1',false);
        	$this->db->set('fin',"'".$hoy."'",false);
        	$this->db->update('financiacion');
	}
    }

    public function get_morosos($id_entidad,$comision=null,$actividad=null){

	$solo_cta_social = 0;
	// Cargo en la variables actividades el filtro en f() de lo que llego por parametros
	// Si viene seteada una comision con todas las actividades de esa comision
	if ( $comision ) {
		if ( $comision > 0 ) {
             		$this->db->where('id_entidad',$id_entidad);
             		$this->db->where('comision',$comision);
             		$query = $this->db->get('actividades');
	     		if ( $actividad ) {
             			$this->db->where('id',$actividad);
             			$query = $this->db->get('actividades');
	     		}
		} else {
		// Si viene comision = -1 es solo cuota social
			$actividades = null;
			$solo_cta_social = 1;
		}
	}

	// Si vino algun parametro y el SQL no encontro nada salgo con false
	if ( $comision == -1 ) {
		// Sino vino parametros  vino -1 pongo null la variable p luego tomar TODOS LOS SOCIOS de la cuota social
		$actividades = null;
	} else {
		if($query->num_rows() == 0){return false;}
		$actividades = $query->result();
	}

	// Busco el conjunto de socios morosos (tanto p actividad como p cuota social)
	$hoy=date('Ym');
	$this->db->select('p.tutor_id sid, SUM(p.monto-p.pagado) deuda');
	$this->db->where('p.id_entidad',$id_entidad);
	$this->db->where('p.estado',1);
	$this->db->where('p.tipo !=',5);
	$this->db->where('DATE_FORMAT(p.generadoel, "%Y%m") <',$hoy);
    	if ( $actividades ) {
		$in=array();
        	foreach ( $actividades as $actividad ) {
            		$in[]=$actividad->id;
        	}
        	$this->db->where_in('p.aid',$in);
    	} else {
		if ( $solo_cta_social == 1 ) {
        		$this->db->where_in('p.aid','0');
		}
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
        $this->db->where('id',$sid);
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
        $this->db->where('p.id_entidad',$id_entidad);
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
        $this->db->where('p.id_entidad',$id_entidad);
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
				'nro_socio' => $socio->nro_socio,
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
                $this->db->where('id',$aid);
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

    public function get_pagos_actividad($id_entidad, $act){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('aid',$act);
        $this->db->where('estado',1);
        $query = $this->db->get('actividades_asociadas');
        $asoc = $query->result();
        $query->free_result();

        $this->load->model("socios_model");

        foreach ($asoc as $a) {
            $socio = $this->socios_model->get_socio($a->sid);
            $a->id = $socio->id;
            $a->nro_socio = $socio->nro_socio;
            $a->socio = @$socio->nombre.' '.@$socio->apellido;
            $a->telefono = @$socio->telefono;
            $a->nacimiento = @$socio->nacimiento;
            $a->dni = @$socio->dni;
            $a->suspendido = @$socio->suspendido;
            $a->observaciones = @$socio->observaciones;
            $a->act_nombre = $this->actividades_model->get_actividad($a->aid)->nombre;
            @$a->deuda = $this->pagos_model->get_deuda_actividad($id_entidad,$a->aid,$socio->id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
	        $array_ahg = $this->pagos_model->get_monto_socio($socio->id);
            @$a->cuota = $array_ahg['total'];
            /* Fin Modificacion AHG */
            @$a->monto_adeudado = $this->pagos_model->get_socio_total($socio->id);
        }
        return $asoc;
    }

    public function get_pagos_profesor($id_entidad, $id)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('profesor',$id);
        $query = $this->db->get('actividades');
        $actividades = $query->result();
        $socios = array();
        foreach ($actividades as $actividad) {
            $socios[] = $this->get_pagos_actividad($id_entidad, $actividad->id);
        }
        return $socios;
    }

    public function get_usuarios_suspendidos($id_entidad)
    {

        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('suspendido',1);
        $this->db->where('estado',1);
        $this->db->order_by('apellido','asc');
        $this->db->order_by('nombre','asc');
        $query = $this->db->get('socios');
        $socios = $query->result();
        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->id);

            $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad,$socio->id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
            $array_ahg = $this->pagos_model->get_monto_socio($socio->id);
            $socio->cuota = $array_ahg['total'];
// Falta meses adeudados

        }
        $query->free_result();
        return $socios;
    }

    public function get_socios_activos($id_entidad, $value='')
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('suspendido',0);
        $this->db->where('estado',1);
        $this->db->order_by('apellido','asc');
        $this->db->order_by('nombre','asc');
        $query = $this->db->get('socios');
        $socios = $query->result();
        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->id);

            $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad,$socio->id);
            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
            $array_ahg = $this->pagos_model->get_monto_socio($socio->id);
            $socio->cuota = $array_ahg['total'];

        }
        $query->free_result();
        return $socios;
    }

    public function get_pagos_mensual($id_entidad,$aid,$anio,$mes){
	$pagos = 0;
	$this->db->select_sum('monto');
	$this->db->where('id_entidad' , $id_entidad);
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

    public function get_pagos_categorias($id_entidad, $id){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->where('categoria',$id);
        $query = $this->db->get('socios');
        $socios = $query->result();

        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->get_deuda($socio->id);
            $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad, $socio->id);
             if($socio->deuda){
                $hoy = new DateTime();
                $d2 = new DateTime($socio->deuda->generadoel);
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
                    if( $hoy->format('%m') != $d2->format('%m') && $socio->deuda->monto != '0.00' ){
                    $meses_a = "Saldo del mes anterior";
                    }else{
                    $meses_a = "Cuota al Día";
                    }
                }
            }else{
                $meses_a = "Aún no se registró ningun pago";
            }
	    $socio->meses = $meses_a;

            if($socio->deuda_monto < 0){
                $monto_a = "$ ".$socio->deuda_monto*-1;
            }else{
                $monto_a = "Cuota al Día";
            }
	    $socio->txt_deuda = $monto_a;


            /* Modificado AHG para manejo de array en PHP 5.3 que tengo en mi maquina */
		$array_ahg = $this->pagos_model->get_monto_socio($socio->id);
            	$socio->cuota = $array_ahg['total'];
            /* Fin Modificacion AHG */
        }
        return $socios;

    }

    public function get_ingresos($id_entidad, $fecha1='',$fecha2='')
    {
        $this->load->model('socios_model');
        $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
        $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){ return false; }
        $pagos = $query->result();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->tutor_id);
        }
        return $pagos;
    }

     public function get_ingresos_cuentadigital($id_entidad, $fecha1='',$fecha2='')
    {
        $this->load->model('socios_model');
        $this->db->where('fecha >=',$fecha1.' 0:00:00');
        $this->db->where('fecha <=',$fecha2.' 23:59:59');
        $this->db->where('id_entidad', $id_entidad);
        $query = $this->db->get('cuentadigital');
        if($query->num_rows() == 0){ return false; }
        $pagos = $query->result();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
        }
        return $pagos;
    }

     public function get_ingresos_cooperativa($id_entidad, $fecha1='',$fecha2='')
    {
        $this->load->model('socios_model');
        $this->db->where('fecha_pago >=',$fecha1);
        $this->db->where('fecha_pago <=',$fecha2);
        $this->db->where('id_entidad', $id_entidad);
        $query = $this->db->get('cobranza_col');
        if($query->num_rows() == 0){ return false; }
        $pagos = $query->result();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
        }
        return $pagos;
    }


    public function get_cobros_actividad($id_entidad, $fecha1='',$fecha2='',$actividad=false,$categoria=false)
    {
        $this->load->model('actividades_model');
        $this->load->model('socios_model');
        $actividad = $this->actividades_model->get_actividad($actividad);
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',0);
        $this->db->where('aid',$actividad->id);
        $this->db->where('pagadoel >=',$fecha1.' 0:00:00');
        $this->db->where('pagadoel <=',$fecha2.' 23:59:59');
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $pagos = $query->result();
        $res = array();
        foreach ($pagos as $pago) {
            $pago->socio = $this->socios_model->get_socio($pago->sid);
            $pago->deuda = $this->get_deuda_actividad($id_entidad,$actividad->id,$pago->sid);
            if($categoria != ''){
                if(date('Y',strtotime($pago->socio->nacimiento)) != $categoria){
                    continue;
                }
            }
            $res[] = $pago;
        }
        return $res;
    }

    public function get_cobros_cuota($id_entidad, $fecha1='',$fecha2='',$categoria=false)
    {
        $this->load->model('socios_model');
        $this->db->where('id_entidad',$id_entidad);
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
            $pago->deuda = $this->get_deuda_cuota($id_entidad, $pago->sid);
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


    public function registrar_pago2($id_entidad,$sid=false,$monto='0',$ajuste='0')
    {
        if(!$sid){return false;}
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('tipo',5);
        $this->db->where('tutor_id ',$sid);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){
            $pago = array(
                'sid' => $sid,
                'id_entidad' => $id_entidad,
                'tutor_id' => $sid,
                'aid' => 0,
                'generadoel' => date('Y-m-d'),
                'descripcion' => "A favor",
                'monto' => 0,
		'ajuste' => 0,
                'tipo' => 5
            );
            $this->insert_pago_nuevo($pago);
            $a_favor = 0;
        }else{
            $a_favor = $query->row()->monto;
        }
        $monto = $monto + $a_favor*-1;


        $this->db->order_by('generadoel','asc');
        $this->db->order_by('tipo','asc');
        $this->db->where('id_entidad',$id_entidad);
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
                    $this->db->where('id',$pago->id);
                    $this->db->update('pagos',array('pagado'=>$pagado,'estado'=>0,'pagadoel'=>date('Y-m-d H:i:s'),'ajuste'=>$ajuste));
                }else{
                    if($pago->pagado == 0){
                        $pagado = $monto;
                    }else{
                        $pagado = $pago->pagado+$monto;
                    }
                    $this->db->where('id',$pago->id);
                    $this->db->update('pagos',array('pagado'=>$pagado,'pagadoel'=>date('Y-m-d H:i:s'),'ajuste'=>$ajuste));
                    $monto = 0;
                }
            }
        }

        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('tutor_id',$sid);
        $this->db->where('tipo',5);
        $this->db->update('pagos',array('monto'=>$monto*-1));

    }

    public function get_ultimo_pago_actividad($id_entidad,$aid,$sid)
    {
        $this->db->order_by('pagadoel', 'desc');
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('aid',$aid);
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_deuda_actividad($id_entidad,$aid,$sid)
    {
        $this->db->order_by('generadoel', 'asc');
        $this->db->where('id_entidad',$id_entidad);
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

    public function get_deuda_cuota($id_entidad,$sid)
    {
        $this->db->order_by('generadoel', 'asc');
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('tipo',1);
        $this->db->where('sid',$sid);
        $this->db->where('estado',1);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_ultimo_pago_cuota($id_entidad,$sid='')
    {
        $this->db->order_by('pagadoel', 'desc');
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('tipo',1);
        $this->db->where('tutor_id',$sid);
        $this->db->where('estado',0);
        $query = $this->db->get('pagos');
        if($query->num_rows() == 0){return false;}
        $ultimo_pago = $query->row();
        $query->free_result();
        return $ultimo_pago;
    }

    public function get_ultimo_pago_socio($id_entidad,$sid)
    {
        //$this->db->where('aid',$aid);
        $this->db->order_by('generadoel','asc');
        $this->db->where('id_entidad',$id_entidad);
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

    public function get_socios_financiados($id_entidad)
    {
        $this->db->where('fn.id_entidad',$id_entidad);
        $this->db->where('fn.estado',1);
        $this->db->join('socios','socios.id = fn.sid');
        $query = $this->db->get('financiacion as fn');
        if($query->num_rows() == 0){ return false; }
        $socios = $query->result();
        $query->free_result();
        return $socios;
    }

    public function get_becas($id_entidad,$actividad='')
    {
        if($actividad == -1){
            $this->db->where('id_entidad',$id_entidad);
            $this->db->where('descuento >',0);
            $this->db->where('estado',1);
            $query = $this->db->get('socios');
            if($query->num_rows() == 0){ return false; }
            $socios = $query->result();
            $query->free_result();
            return $socios;
        }else{
            $this->db->select('aa.*, socios.*, aa.descuento as descuento, aa.monto_porcentaje as monto_porcentaje, socios.id as id');
            $this->db->where('aa.id_entidad',$id_entidad);
            $this->db->where('aa.aid',$actividad);
            $this->db->where('aa.descuento >',0);
            $this->db->where('aa.estado',1);
            $this->db->join('socios', 'socios.id = aa.sid', 'left');
            $query = $this->db->get('actividades_asociadas as aa');
            if($query->num_rows() == 0){ return false; }
            $socios = $query->result();
            $query->free_result();
            return $socios;
        }
    }

    public function get_sin_actividades($id_entidad)
    {
        $this->load->model('socios_model');
        $socios = $this->socios_model->get_socios($id_entidad);
        $sin_actividades = array();
        foreach ($socios as $socio) {
            $this->db->where('id_entidad', $id_entidad);
            $this->db->where('sid', $socio->id);
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

    public function get_pagos_edit($id_entidad, $socio_id)
    {
        $this->db->select('pagos.*,actividades.nombre');
        $this->db->where('pagos.id_entidad', $id_entidad);
        $this->db->where('pagos.tutor_id', $socio_id);
        $this->db->where('pagos.generadoel >=',date('Y-m').'-01');
        $this->db->join('actividades', 'actividades.id = pagos.aid', 'left');
        $query = $this->db->get('pagos');
        if( $query->num_rows() == 0 ){ return false; }
        $pagos = $query->result();
        $query->free_result();
        return $pagos;
    }

    public function revertir_fact($id_entidad, $sid, $aid, $periodo) {
	// Busco el registro metido en la tabla pagos
        $this->db->where('pagos.id_entidad',$id_entidad);
        $this->db->where('pagos.aid',$aid);
        $this->db->where('pagos.sid',$sid);
        $this->db->where('DATE_FORMAT(pagos.generadoel,"%Y%m")',$periodo);
        $query = $this->db->get('pagos');
        if( $query->num_rows() == 0 ){ return false; }
        $pago = $query->row();
	
	$monto = $pago->monto;
	
	if ( $pago->pagado == 0 ) {
		$this->db->where('id',$pago->id);
		$this->db->update('pagos',array('pagado'=>$monto,'estado'=>0,'pagadoel'=>date('Y-m-d H:i:s'),'ajuste'=>1));
	} else {
		$pagado = $pago->pagado;
		$this->db->where('id',$pago->id);
		$this->db->update('pagos',array('pagado'=>$monto,'estado'=>0,'pagadoel'=>date('Y-m-d H:i:s'),'ajuste'=>1));
		// Pongo a favor lo que tenia pagado
		$this->db->where('id_entidad', $id_entidad);
		$this->db->where('tutor_id', $pago->tutor_id);
		$this->db->where('tipo', 5);
		$this->db->set('monto', 'monto-'.$pagado, FALSE);
		$this->db->update('pagos');
	}
	
	// Meto un ajuste del monto de lo generado
	$total = $this->get_socio_total($pago->tutor_id);
	$facturacion = array(
		'sid' => $pago->tutor_id,
		'id_entidad'=> $id_entidad,
		'descripcion'=> "REVERSION FACTURACION",
		'debe'=>0,
		'haber'=>$monto,
		'total'=>$total+$monto,
		'origen'=>0
	);
	$this->db->insert('facturacion', $facturacion);

    }

    public function eliminar_pago($id_entidad,$id)
    {
        $this->db->where('pagos.id',$id);
        $this->db->join('actividades', 'actividades.id = pagos.aid', 'left');
        $query = $this->db->get('pagos');
        if( $query->num_rows() == 0 ){ return false; }
        $pago = $query->row();

        //actualizamos saldo a favor
        $a_favor = $pago->pagado;
        if($a_favor > 0){
            $this->db->where('id_entidad', $id_entidad);
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
                'id_entidad'=> $id_entidad,
                'debe'=>0,
                'haber'=>$facturacion,
                'total'=>$total+$facturacion,
		'origen'=>3
            );
            $this->db->insert('facturacion', $facturacion);
        }

        $this->db->where('id', $id);
        $this->db->delete('pagos');
        $query->free_result();
        return $pago->tutor_id;
    }

    public function get_meses_ingresos($id_entidad) {
	$qry="DROP TEMPORARY TABLE IF EXISTS tmp_meses; ";
        $this->db->query($qry);
	$qry="CREATE TEMPORARY TABLE tmp_meses ( mes integer, descr_mes char(30), INDEX(mes) );  ";
        $this->db->query($qry);
	$qry=" INSERT INTO tmp_meses VALUES (  1, 'Enero' ); ";
        $this->db->query($qry);
	$qry=" INSERT INTO tmp_meses VALUES (  2, 'Febrero' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  3, 'Marzo' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  4, 'Abril' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  5, 'Mayo' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  6, 'Junio' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  7, 'Julio' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  8, 'Agosto' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES (  9, 'Setiembre' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES ( 10, 'Octubre' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES ( 11, 'Noviembre' );";
        $this->db->query($qry);
	$qry="INSERT INTO tmp_meses VALUES ( 12, 'Diciembre' ); ";
        $this->db->query($qry);
	$qry="SELECT DATE_FORMAT(f.date,'%Y%m') mes, CONCAT(m.descr_mes,'-',DATE_FORMAT(f.date,'%Y')) descr_mes, COUNT(*) movimientos 
		FROM facturacion f
			JOIN tmp_meses m ON DATE_FORMAT(f.date, '%m') = m.mes
		WHERE f.date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
		GROUP BY 1; ";
        $meses = $this->db->query($qry)->result();

        return $meses;
    }

    public function get_facturacion_all($id_entidad)
    {
        $qry="SELECT f.sid, s.nro_socio, f.id, f.date, f.descripcion, IF(f.debe = 0, 'H', 'D') tipo, f.debe, f.haber FROM facturacion f JOIN socios s ON f.sid = s.id WHERE f.id_entidad = $id_entidad ORDER BY f.id; ";
        $facturacion = $this->db->query($qry)->result();

        return $facturacion;

    }
}
