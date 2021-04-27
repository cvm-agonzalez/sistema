<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Socios_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function register($datos)
    {
        $this->db->insert('socios', $datos);
        $sid = $this->db->insert_id();   

        // Verifico si tiene cupon y si no lo genero
	$datos['id'] = $sid;
        $this->cupon_cobro($datos);

	// Retorno el ID insertado
        return $sid;
    }

    public function checkDNI($id_entidad, $dni,$id=null)
    {
        if($id){
            $query = $this->db->get_where('socios', array('dni' => $dni, 'id_entidad' => $id_entidad, 'id !=' =>$id, 'estado' =>'1'),1);
        }else{
            $query = $this->db->get_where('socios', array('dni' => $dni, 'id_entidad' => $id_entidad, 'estado' =>'1'),1);
        }
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    public function getmails_check() {
        $qry="SELECT id sid, id_entidad, categoria, CONCAT(nombre,', ',apellido) apynom, mail FROM socios WHERE suspendido = 0 AND validmail_st IN (0, 2) AND mail != '' LIMIT 100";
        $resultado = $this->db->query($qry)->result();
        if ( $resultado ) {
                return $resultado;
        } else {
                return false;
        }
    }


    public function get_tutores($id_entidad) {
	$qry="SELECT DISTINCT t.id id_tutor, t.dni dni_tutor, CONCAT(TRIM(t.nombre),', ',TRIM(t.apellido)) tutor,
			s.id, s.dni, s.nro_socio, CONCAT(TRIM(s.nombre),', ',TRIM(s.apellido)) socio,
			s.nacimiento, s.observaciones
		FROM socios s
			JOIN socios t ON s.tutor = t.id
		WHERE s.id_entidad = $id_entidad AND 
			s.tutor > 0
		ORDER BY t.id, s.id; ";
        $socios = $this->db->query($qry)->result();

        $this->load->model('pagos_model');
        foreach ($socios as $socio) {
            $socio->deuda_monto = $this->pagos_model->get_deuda($socio->id);

            $socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad,$socio->id);
            $array_ahg = $this->pagos_model->get_monto_socio($socio->id);
            $socio->cuota = $array_ahg['total'];

        }

        return $socios;
    }

    public function get_prox_nosocio($id_entidad) {
	$qry="SELECT MIN(nro_socio) min_nsocio FROM socios WHERE id_entidad = $id_entidad; ";
        $resultado = $this->db->query($qry)->result();
	if ( $resultado[0]->min_nsocio >= 0 ) { 
		$proximo = -1;
	} else {
		$proximo = $resultado[0]->min_nsocio-1;
	}
	return $proximo;
    }

    public function get_prox_nsocio($id_entidad) {
	$qry="SELECT MAX(nro_socio) max_nsocio FROM socios WHERE id_entidad = $id_entidad; ";
        $resultado = $this->db->query($qry)->result();
	return $resultado[0]->max_nsocio+1;
    }

    public function listar($id_entidad){
/*	Cambiado por el SQL con JOIN para optimizar lecturas Ago2017 */
        $this->load->model('pagos_model');
	$qry="SELECT s.*, GROUP_CONCAT(DISTINCT a.nombre) actividades, SUM(p.monto-p.pagado) deuda
		FROM socios s
        		LEFT JOIN actividades_asociadas aa ON ( s.id = aa.sid AND aa.estado = 1 )
        		LEFT JOIN actividades a ON ( aa.aid = a.id )
        		LEFT JOIN pagos p ON ( s.id = p.tutor_id AND p.aid = 0 AND p.estado = 1 AND DATE_FORMAT(p.generadoel,'%Y%m') < DATE_FORMAT(CURDATE(),'%Y%m') )
		WHERE s.estado = 1 AND s.id_entidad = $id_entidad
		GROUP BY s.id; ";
        $resultado = $this->db->query($qry)->result();
        $socios = array();
        foreach ( $resultado as $socio ) {
            $socios[$socio->id]['datos'] = $socio;
            $cuota = $this->pagos_model->get_monto_socio($socio->id);
	    if ( !$cuota ) { $cuota = 0; };
            $socios[$socio->id]['cuota'] = $cuota;
        }        

        return $socios;
    }

    public function get_socio($id)
    {
        if(!$id || $id == '0'){
            $socio = new stdClass();
            $socio->id=0;
            $socio->nombre=0;
            $socio->apellido=0;
            $socio->dni=0;
            return $socio;
        }else{
            $query = $this->db->get_where('socios',array('id' => $id,'estado'=> '1'),1);
            if($query->num_rows() == 0){return false;}
            return $query->row();
        }
    }

    public function get_socios_comision($id_entidad, $comision, $activ)
    {
	$query="SELECT s.* 
		FROM socios s 
			JOIN actividades_asociadas aa ON ( s.id = aa.sid AND aa.estado = 1 ) 
			JOIN actividades a ON ( a.id = aa.aid ) 
		WHERE s.id_entidad = $id_entidad AND a.comision IN ( ";
	
	$cont = 0;
	foreach ( $comision as $id_comision ) {
		if ( $cont > 0 ) { $query .= ", "; };
		$query .= ' '.$id_comision.' ';
		$cont++;
	}
	$query .= " ) "; 
	if ( $activ == 1 ) {
		$query .= " AND s.suspendido = 0; ";
	} else {
		$query .= " ; ";
	}
        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_socios_titu_comision($id_entidad, $comision, $activ)
    {
	$query="SELECT s.* 
		FROM socios s 
			JOIN profesores p ON ( s.id = p.sid )
		WHERE s.id_entidad = $id_entidad AND p.comision IN ( ";
	
        $cont = 0;
        foreach ( $comision as $id_comision ) {
                if ( $cont > 0 ) { $query .= ", "; };
                $query .= ' '.$id_comision.' ';
                $cont++;
        }
        $query .= " ) ";
        if ( $activ == 1 ) {
                $query .= " AND s.suspendido = 0; ";
        } else {
                $query .= " ; ";
        }

        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_socios_conact($id_entidad, $activ)
    {
	$query="SELECT s.* FROM socios s LEFT JOIN actividades_asociadas aa ON ( s.id = aa.sid AND aa.estado = 1 ) WHERE s.id_entidad = $id_entidad AND aa.sid = s.id "; 
	if ( $activ == 1 ) {
		$query .= " AND s.suspendido = 0; ";
	} else {
		$query .= " ; ";
	}
        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_socios_sinact($id_entidad, $activ)
    {
	$query="SELECT s.* FROM socios s LEFT JOIN actividades_asociadas aa ON ( s.id = aa.sid AND aa.estado = 1 ) WHERE s.id_entidad = $id_entidad AND aa.sid is NULL "; 
	if ( $activ == 1 ) {
		$query .= " AND s.suspendido = 0; ";
	} else {
		$query .= " ; ";
	}
        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_carnets($id_entidad, $categoria, $foto, $actividad)
    {
	$query="SELECT s.* 
		FROM socios s 
			LEFT JOIN actividades_asociadas aa ON ( s.id = aa.sid AND aa.estado = 1 ) 
		WHERE s.id_entidad = $id_entidad "; 
	if ( $categoria > 0 ) {
		$query .= " AND s.categoria = $categoria ";
	}
	if ( $actividad == -1 ) {
		$query .= " AND aa.sid IS NULL ";
	}
	if ( $actividad > 0 ) {
		$query .= " AND aa.aid = $actividad AND aa.sid = s.id ";
	}
	$query .= "; ";
        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_socios_export($id_entidad)
    {
	$query="SELECT s.* , CONCAT(c.id,'-',c.nombre) categ_nombre, IF(t.apellido IS NULL,'', CONCAT(t.id,'#',t.nombre,', ',t.apellido)) apynom_tutor, SUM(p.pagado-p.monto) saldo
		FROM socios s 
			LEFT JOIN categorias c ON ( s.categoria = c.id ) 
			LEFT JOIN socios t ON ( s.tutor = t.id ) 
			LEFT JOIN pagos p ON ( s.id = p.tutor_id ) 
		WHERE s.id_entidad = $id_entidad AND s.suspendido = 0 
		GROUP BY s.id"; 
        $result = $this->db->query($query)->result(); 
        return $result;
    }

    public function get_socios($id_entidad)
    {
        $query = $this->db->get_where('socios',array('id_entidad'=>$id_entidad,'estado'=>1));
        return $query->result();
    }

    public function get_socio_full($id)
    {
            $query = $this->db->get_where('socios',array('id' => $id),1);
            if($query->num_rows() == 0){return false;}
            return $query->row();
    }

    public function get_socio_by($id_entidad, $by)
    {
        $by['id_entidad'] = $id_entidad;
        $by['estado'] = 1;
        $query = $this->db->get_where('socios',$by,1);
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    public function borrar_socio($id){
        $this->db->where('id', $id); 
        $this->db->update('socios',array("estado"=>'0'));
    }

    public function update_socio($id_entidad, $id, $data){
	// Verifico si cambio la categoria para registrar ese ecambio
	$data['id_entidad'] = $id_entidad;
	$categ = $this->get_cat($id_entidad, $id, $data);
	if ( $categ['cambio'] == 1 ) {
        	$this->load->model('pagos_model');
		if ( $categ['precio_ant'] != $categ['precio_new'] ) {
			$diferencia = $categ['precio_new'] - $categ['precio_ant'];
			if ( $diferencia > 0 ) {
				$this->pagos_model->registrar_pago($id_entidad, 'debe',$id,$diferencia,'Cambio de Categoria de '.$categ['descr_ant'].' a '.$categ['descr_new'],0,0);
			} else {
				$this->pagos_model->registrar_pago($id_entidad, 'haber',$id,-$diferencia,'Cambio de Categoria de '.$categ['descr_ant'].' a '.$categ['descr_new'],0,0);
			}
		} else {
			$this->pagos_model->registrar_pago($id_entidad, 'debe',$id,0.00,'Cambio de Categoria de '.$categ['descr_ant'].' a '.$categ['descr_new'],0,0);
		}
	}
	// Actualizo los cambio del socio
        $this->db->where('id', $id);
        $this->db->update('socios', $data); 
	
	// Verifico si tiene cupon y si no lo genero
	$data['id'] = $id;
        $this->cupon_cobro($data);
    }



    public function cupon_cobro( $datos ) {
	// Asigno variables
	$sid = $datos['id'];
	$id_entidad = $datos['id_entidad'];
	// Busco cupon
        $this->load->model('pagos_model');
        $cupon = $this->pagos_model->get_cupon($sid, $id_entidad );
        if ( !$cupon ) {
                // Si no tiene lo genero
        	$this->load->model('general_model');
		$entidad = $this->general_model->get_ent($id_entidad);
		$ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;
        	if ( $entidad->cd_id > 0 ) {
			$cuenta_id = $entidad->cd_id;
                	$nombre = trim(substr($datos['nombre'],0,40));
                	$apellido = trim(substr($datos['apellido'],0,40));
                	$concepto  = $apellido.", ".$nombre.' ('.$sid.')';
                	$repetir = true;
                	$count = 0;
			$precio = 100;
                	$result = false;
                        $url = 'https://www.cuentaDigital.com/api.php?id='.$cuenta_id.'&codigo='.urlencode($sid).'&precio='.urlencode($precio).'&concepto='.urlencode($concepto).'&xml=1';
                	do{
                        	$count++;
                        	$a = file_get_contents($url);
                        	$a = trim($a);
                        	$xml = simplexml_load_string($a);
                        	if (($xml->ACTION) != 'INVOICE_GENERATED') {
                                	$repetir = true;
                                	sleep(1);
                        	} else {
                                	$repetir = false;
                                	$result = array(); 
                                	$result['image'] = $xml->INVOICE->BARCODEBASE64;
                                	$result['barcode'] = $xml->INVOICE->PAYMENTCODE1;
                                	$result['codlink'] = substr($xml->INVOICE->PAYMENTCODE2,-10);
					// Insertar cupon en la BD
					$cid = $this->pagos_model->generar_cupon($id_entidad, $sid, $precio, $result);
					// Poner imagen en directorio cupones
                        		$path_cupon = "entidades/".$ent_dir."/cupones/".$cid.".png";
                        		$data = base64_decode($result['image']);
                        		$img = imagecreatefromstring($data);
                        		if ($img !== false) {
                                                header('Content-Type: image/png');
                            			imagepng($img,$path_cupon,0);
                            			imagedestroy($img);
					}
                        	}
                        	if ($count > 5) { $repetir = false; };
                	} while ( $repetir );
        	} 
        }
    }

    public function get_categoria($id_entidad, $id){
	$this->db->where('id_entidad', $id_entidad);
	$this->db->where('id', $id);
        $query = $this->db->get('categorias');
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    public function get_cat($id_entidad, $id, $data){
	$cat_new=$data['categoria'];
	$query="SELECT s.categoria id_ant, c1.nombre descr_ant, c1.precio precio_ant, $cat_new id_new, c2.nombre descr_new, c2.precio precio_new,
			IF ( $cat_new != s.categoria , 1, 0) cambio
		FROM socios s
			LEFT JOIN categorias c1 ON ( s.categoria = c1.id )
			LEFT JOIN categorias c2 ON ( $cat_new = c2.id )
		WHERE s.id_entidad = $id_entidad AND s.id = $id; ";
        $result = $this->db->query($query)->row(); 
        if ( $result ) {
	    $cat = array( 'id_ant' => $result->id_ant,
			'descr_ant' => $result->descr_ant,
			'precio_ant' => $result->precio_ant,
			'id_new' => $result->id_new,
			'descr_new' => $result->descr_new,
			'precio_new' => $result->precio_new,
			'cambio' => $result->cambio
		);
            return $cat;
        }else{
            return false;
        }
    }

    public function update_lCon()
    {   
        $uid = $this->session->userdata('id_usuario');
        $fecha = date("Y-m-d G:i:s");
        $data = array('lCon'=>$fecha);
        $this->db->where('id',$uid);
        $this->db->update('admin',$data);
    }

    public function es_tutor($id_entidad, $sid){    
        $query = $this->db->get_where('socios', array('id_entidad' => $id_entidad, 'estado' =>'1','tutor =' => $sid));
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }
    public function listar_tutores($id_entidad){    
        $this->db->select('tutor');
        $this->db->distinct('tutor');
        $query = $this->db->get_where('socios', array('id_entidad' => $id_entidad, 'estado' =>'1','tutor !=' => '0'));
        $tutores = $query->result();
        return $tutores;
    }
    public function get_cumpleanios($id_entidad){ // esta funcion devuelve los socios que cumplen 18 años entre el 21 del mes pasado y el 20 de este mes
        
	$this->load->model('general_model');
        $cat_menor = $this->general_model->get_cat_tipo($id_entidad, "m");
	if ( $cat_menor ) {

        	$mes = date('m'); //mes actual
        	$mes_ant = date('m')-1; //mes anterior
        	if(count($mes_ant) == 1){ 
            	$mes_ant = '0'.$mes_ant;
        	}
        	$anio = date('Y')-18; //18 años antes
	
        	$this->db->where($anio, 'YEAR(nacimiento)' , FALSE);
        	$this->db->where($mes_ant, 'MONTH(nacimiento)' , FALSE);
        	$this->db->where('21 <=', 'DAY(nacimiento)' , FALSE);
        	$this->db->where('id_entidad', $id_entidad);
        	$this->db->where('categoria', $cat_menor->id);
        	$this->db->get('socios');
        	$query1 = $this->db->last_query(); //buscamos los que nacieron el mes anterior despues del 21 inclusive
	
        	$this->db->where($anio, 'YEAR(nacimiento)' , FALSE);
        	$this->db->where($mes, 'MONTH(nacimiento)' , FALSE);
        	$this->db->where('20 >=', 'DAY(nacimiento)' , FALSE);
        	$this->db->where('id_entidad', $id_entidad);
        	$this->db->where('categoria', $cat_menor->id);
        	$this->db->get('socios');
        	$query2 = $this->db->last_query(); // buscamos los que nacieron este mes antes del 20 inclusive
	
        	$query = $this->db->query($query1." UNION ".$query2);
        	return $query->result();
		
	} else {

		return false;
	}

    }

    public function actualizar_menor($id_entidad, $id_menor){
	$this->load->model('general_model');
        $cat_mayor = $this->general_model->get_cat_tipo($id_entidad, "M");

	if ( $cat_mayor ) {
        	$this->db->where('id',$id_menor);
        	$this->db->update('socios',array('tutor'=>'0','categoria'=>$cat_mayor->id));
		$this->load->model('pagos_model');
		$this->pagos_model->registrar_pago($id_entidad, 'debe',$id_menor,0.00,'Cambio de Categoria de Menor a Mayor - Proceso Facturacion',0,0);
		return true;
	} else {
		return false;
	}

    }

    public function get_socios_pagan($id_entidad, $facturado=false){
        if($facturado){
            $this->db->where('facturado', 0);
        }
        $this->db->where('tutor', 0);
        $this->db->where('estado', 1);
        $this->db->where('suspendido', 0);
        $this->db->where('id_entidad', $id_entidad);
        $query = $this->db->get('socios');
        return $query->result();
    }

    public function check_u_n($id_entidad, $sn){
        $this->db->where('id_entidad', $id_entidad);
        $this->db->where('nro_socio', $sn);
        $this->db->where('estado', 1);
        $query = $this->db->get('socios');
        if($query->num_rows() == 0){
            return false;
        }else{
            return true;
        }
    }

    public function get_resumen_mail($sid){
        $this->load->model('pagos_model');
        $this->load->model('debtarj_model');
        $resumen = $this->pagos_model->get_monto_socio($sid);
        $res['resumen'] = $resumen;
        $socio = $this->get_socio($sid);
        $res['mail'] = $socio->mail;
        $res['deuda'] = $this->pagos_model->get_deuda_sinhoy($sid);
        $res['sid'] = $sid;
        $res['debtarj'] = null;
	
        return $res;
    }

    public function insert_deuda($id_entidad,$sid,$deuda){
        $this->load->model('pagos_model');
        $total = $this->pagos_model->get_socio_total($id_entidad, $sid);
        $total = $total - $deuda;
        $insert = array(
            'sid'=> $sid,
            'descripcion'=>'Ingreso Manual',
            'id_entidad'=>$id_entidad,
            'debe'=>$deuda,
            'haber' => 0,
            'total' => $total
            );
        $this->db->insert('facturacion',$insert);
    }

    public function suspender($id,$si='si'){
        if($si == 'si'){
	    $hoy = date("Y-m-d");
            $this->db->where('id',$id);
            $this->db->update('socios',array('suspendido'=>1, 'fecha_baja'=>$hoy));
        }else{
            $this->db->where('id',$id);
            $this->db->update('socios',array('suspendido'=>0, 'fecha_baja'=>null));
        }
    }

    public function get_socio_by_dni($id_entidad, $dni)
    {
        $this->db->where('id_entidad', $id_entidad);
        $this->db->where('dni', $dni);
        $query = $this->db->get('socios');
        if( $query->num_rows() == 0 ){ return false; }
        $socio = $query->row();
        $query->free_result();
        return $socio;
    }

    public function get_socio_by_barcode($id_entidad, $barcode)
    {
        $this->db->where('id_entidad', $id_entidad);
        $this->db->where('barcode', $barcode);
        $query = $this->db->get('cupones');
        if( $query->num_rows() == 0 ){ return false; }
        $cupon = $query->row();    
        $socio = $this->get_socio($cupon->sid);
        if(!$socio){ return false; }
        return $socio;
    }

    public function get_padron_app($id_entidad)
    {
	$query="DROP TEMPORARY TABLE IF EXISTS tmp_saldos; ";
        $this->db->query($query);
	$query="CREATE TEMPORARY TABLE tmp_saldos ( INDEX ( sid ) )
		SELECT tutor_id sid, MAX(IF(estado = 1,DATE_FORMAT(generadoel,'%Y%m'),0)) ult_impago, SUM(pagado-monto) saldo, IF(SUM(pagado-monto)>=0,1,0) aldia
		FROM pagos 
		GROUP BY 1; ";
        $this->db->query($query);
	$query="SELECT s.dni, s.id sid, CONCAT(s.apellido,', ',s.nombre) apynom, c.barcode, p.saldo, 
			CASE WHEN aldia=0 AND p.ult_impago < DATE_FORMAT(CURDATE(), '%Y%m') THEN 99
			     WHEN aldia=0 AND p.ult_impago = DATE_FORMAT(CURDATE(), '%Y%m') THEN 10
			     WHEN aldia=1 THEN 1
			END semaforo
                FROM socios s 
                        LEFT JOIN cupones c ON ( s.id = c.sid AND c.estado = 1 ) 
                        LEFT JOIN tmp_saldos p ON ( s.id = p.sid ) 
                WHERE s.id_entidad = $id_entidad AND s.suspendido = 0 
                GROUP BY s.id";
        $result = $this->db->query($query)->result();
        return $result;
    }

    public function get_status_by_dni($dni)
    {
                $query="DROP TEMPORARY TABLE IF EXISTS tmp_saldos; ";
                $this->db->query($query);
                $query="CREATE TEMPORARY TABLE tmp_saldos ( INDEX ( sid ) )
                        SELECT p.tutor_id sid, MIN(IF(p.estado = 1,IF (p.tipo != 5 ,DATE_FORMAT(p.generadoel,'%Y%m'),'2100-01-01'),0)) ult_impago, SUM(p.pagado-p.monto) saldo, IF(SUM(p.pagado-p.monto)>=0,1,0) aldia
                        FROM pagos p
                                JOIN socios s ON s.dni = $dni AND s.id = p.tutor_id
			WHERE p.estado = 1
                        GROUP BY 1; ";
                $this->db->query($query);
                $query="SELECT s.dni, s.id sid, CONCAT(s.apellido,', ',s.nombre) apynom, c.barcode, p.saldo, 
                                CASE WHEN aldia=0 AND p.ult_impago < DATE_FORMAT(CURDATE(), '%Y%m') THEN 99
                                     WHEN aldia=0 AND p.ult_impago = DATE_FORMAT(CURDATE(), '%Y%m') THEN 10
                                     WHEN aldia=1 THEN 1
                                END semaforo
                        FROM socios s 
                                LEFT JOIN cupones c ON ( s.id = c.sid AND c.estado = 1 ) 
                                LEFT JOIN tmp_saldos p ON ( s.id = p.sid ) 
                        WHERE s.dni = $dni 
                        GROUP BY s.id; ";
                $result = $this->db->query($query)->row();
                return $result;
     }

    public function get_carnet_by_dni($id_entidad, $dni)
    {
        $this->load->model('general_model');
        $this->db->where('id_entidad', $id_entidad);
        $this->db->where('dni', $dni);
        $query = $this->db->get('socios');
        if( $query->num_rows() == 0 ){ return false; }
        $socio = $query->row();
	$ent_directorio = $this->general_model->get_ent_dir($id_entidad)->dir_name;
	if(file_exists( BASEPATH."../entidades/".$ent_directorio."/socios/".$socio->id.".jpg" )){
                $url_foto = "entidades/".$ent_directorio."/socios/".$socio->id.".jpg";
	} else {
		$url_foto = "sinfoto";
	}

	$ret_socio = array('nro_socio' => $socio->nro_socio, 'apynom' => $socio->apellido.', '.$socio->nombre, 'domicilio' => $socio->domicilio, 'nacimiento' => $socio->nacimiento, 'ingreso' => $socio->alta, 'estado' => $socio->suspendido, 'foto' => urlencode($url_foto));
        $query->free_result();
        return $ret_socio;
    }


}  
?>
