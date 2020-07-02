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
        if ( $cupon->monto == 0 ) {
                // Si no tiene lo genero
        	$this->load->model('general_model');
		$entidad = $this->general_model->get_ent($id_entidad);
        	if ( $entidad->cd_id > 0 ) {
			$cuenta_id = $entidad->cd_id;
                	$nombre = substr($datos['nombre'],0,40);
                	$concepto  = $nombre.' ('.$sid.')';
                	$repetir = true;
                	$count = 0;
			$precio = 100;
                	$result = false;
                        $url = 'https://www.CuentaDigital.com/api.php?id='.$cuenta_id.'&codigo='.urlencode($sid).'&precio='.urlencode($precio).'&concepto='.urlencode($concepto).'&xml=1';
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
					// Insertar cupon en la BD
					$this->pagos_model->generar_cupon($id_entidad, $sid, $precio, $result);
					// Poner imagen en directorio cupones
                        	}
                        	if ($count > 5) { $repetir = false; };
                	} while ( $repetir );
        	} else {
			$result = array(); 
			$result['image'] = TODO;
			$result['barcode'] = $datos['dni'];
			$precio = 100;
			// Insertar cupon en la BD
			$this->pagos_model->generar_cupon($id_entidad, $sid, $precio, $result);
			// Poner imagen en directorio cupones
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
}  
?>
