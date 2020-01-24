<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Estadisticas_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function facturacion_mensual($id_entidad){        
        $fecha = date('Y-m-j');
        $ret = "[";
            for ($i=0; $i <= 12; $i++) {
                $nuevafecha = strtotime ( '-'.$i.' month' , strtotime ( $fecha ) ) ;                            
                $n_mes = date ( 'm' , $nuevafecha );
                $n_anio = date ( 'Y' , $nuevafecha ); 

                $facturacion = 0;
                $this->db->select_sum('monto');
                $this->db->where('id_entidad' , $id_entidad);
                $this->db->where($n_mes, 'MONTH(generadoel)' , FALSE);
                $this->db->where($n_anio, 'YEAR(generadoel)' , FALSE);
                $query = $this->db->get('pagos');
                if($query->num_rows() != 0){
                    $facturacion = $query->row()->monto;                    
                }
                if(!$facturacion){ $facturacion = 0;}

                $pagos = 0;
                $this->db->select_sum('pagado');
                $this->db->where('id_entidad' , $id_entidad);
                $this->db->where($n_mes, 'MONTH(generadoel)' , FALSE);
                $this->db->where($n_anio, 'YEAR(generadoel)' , FALSE);
                $query = $this->db->get('pagos');
                if($query->num_rows() != 0){
                    $pagos = $query->row()->pagado;                    
                }
                if(!$pagos){ $pagos = 0;}

                $ret .= "{ y: '".$n_anio."-".$n_mes."', a: ".$facturacion.", b:".$pagos." },";            
            }
        $ret .= "]";
        return $ret;
    }

    public function facturacion_anual($id_entidad){        
        $fecha = date('Y-m-j');
        $ret = "[";
            for ($i=0; $i <= 10; $i++) {
                $nuevafecha = strtotime ( '-'.$i.' year' , strtotime ( $fecha ) ) ;                            
                $n_mes = date ( 'm' , $nuevafecha );
                $n_anio = date ( 'Y' , $nuevafecha ); 

                $facturacion = 0;
                $this->db->select_sum('monto');
                $this->db->where('id_entidad' , $id_entidad);
                $this->db->where($n_anio, 'YEAR(generadoel)' , FALSE);
                $query = $this->db->get('pagos');
                if($query->num_rows() != 0){
                    $facturacion = $query->row()->monto;                    
                }
                if(!$facturacion){ $facturacion = 0;}

                $pagos = 0;
                $this->db->select_sum('pagado');
                $this->db->where('id_entidad' , $id_entidad);
                $this->db->where($n_anio, 'YEAR(generadoel)' , FALSE);
                $query = $this->db->get('pagos');
                if($query->num_rows() != 0){
                    $pagos = $query->row()->pagado;                    
                }
                if(!$pagos){ $pagos = 0;}

                $ret .= "{ y: '".$n_anio."', a: ".$facturacion.", b:".$pagos." },";            
            }
        $ret .= "]";
        return $ret;
    }


    public function ingresos_tabla($id_entidad, $mes){        
        $qry = "SELECT DATE_FORMAT(f.date, '%d-%m-%Y') dia, 
                        SUM(IF(f.origen=1 , f.haber, 0 )) ing_col,
                        SUM(IF(f.origen=2 , f.haber, 0 )) ing_cd,
                        SUM(IF(f.origen=3 , f.haber, 0 )) ing_manual,
                        SUM(IF(f.origen=0 , f.haber, 0 )) ajustes
                FROM facturacion f 
		WHERE DATE_FORMAT(f.date,'%Y%m') = $mes AND
			haber > 0 
		GROUP BY 1
		ORDER BY 1; ";

        $resultado = $this->db->query($qry)->result();
        return $resultado;

    }

    public function cobranza_tabla($id_entidad, $id_actividad='-1', $id_comision='0'){        
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_cobranza;";
        $this->db->query($qry);

        $qry = "CREATE TEMPORARY TABLE tmp_cobranza
                SELECT DATE_FORMAT(p.generadoel, '%Y%m') periodo, COUNT(DISTINCT p.tutor_id) socios, COUNT(*) cuotas, SUM(p.monto) facturado,
                        $id_actividad id_act,
                        SUM(IF(p.estado=0 AND DATE_FORMAT(p.pagadoel, '%Y%m') = DATE_FORMAT(p.generadoel, '%Y%m'), p.pagado, 0 )) pagado_mes,
                        SUM(IF(p.estado=0 AND DATE_FORMAT(p.pagadoel, '%Y%m') > DATE_FORMAT(p.generadoel, '%Y%m'), p.pagado, 0 )) pagado_mora,
                        SUM(IF(p.estado=1 AND p.pagado > 0,  p.pagado, 0 )) pago_parcial,
                        SUM(IF(p.estado=1 AND p.pagado = 0,  p.monto, 0 )) impago
                FROM pagos p ";

	if ( $id_comision > 0 ) {
		$qry1 ="DROP TEMPORARY TABLE IF EXISTS tmp_actividades; ";
        	$this->db->query($qry1);
		$qry1 ="CREATE TEMPORARY TABLE tmp_actividades ( INDEX ( aid ) )
			SELECT a.id as aid FROM actividades a WHERE a.id_entidad = $id_entidad AND a.comision = $id_comision; ";
        	$this->db->query($qry1);
	}

	$qry = "CREATE TEMPORARY TABLE tmp_cobranza
		SELECT DATE_FORMAT(p.generadoel, '%Y%m') periodo, COUNT(DISTINCT p.tutor_id) socios, COUNT(*) cuotas, SUM(p.monto) facturado,
			$id_actividad id_act,
			SUM(IF(p.estado=0 AND DATE_FORMAT(p.pagadoel, '%Y%m') = DATE_FORMAT(p.generadoel, '%Y%m'), p.pagado, 0 )) pagado_mes,
			SUM(IF(p.estado=0 AND DATE_FORMAT(p.pagadoel, '%Y%m') > DATE_FORMAT(p.generadoel, '%Y%m'), p.pagado, 0 )) pagado_mora,
			SUM(IF(p.estado=1 AND p.pagado > 0,  p.pagado, 0 )) pago_parcial,
			SUM(IF(p.estado=1 AND p.pagado = 0,  p.monto, 0 )) impago
		FROM pagos p ";
	if ( $id_comision > 0 ) {
		$qry .= "	LEFT JOIN tmp_actividades USING ( aid ) ";
	}
	if ( $id_actividad == -2 ) {
		$qry .= "	LEFT JOIN actividades_asociadas aa ON ( p.tutor_id = aa.sid AND aa.estado = 1 ) ";
	}
	$qry .= "WHERE p.id_entidad = $id_entidad AND DATE_FORMAT(p.generadoel,'%Y%m') >= DATE_FORMAT( DATE_SUB(CURDATE(), INTERVAL 1 YEAR), '%Y%m' ) ";
	if ( $id_actividad == -2 ) {
		$qry .= "AND aa.aid IS NULL ";
	} else {
		if ( $id_actividad == -1 ) {
		} else {
			if ( $id_actividad == -3 ) {
				$qry .= "AND p.aid = 0 ";
			} else { 
				if ( $id_actividad >= 0 && $id_comision == 0 ) {
					$qry .= "AND p.aid = $id_actividad ";
				}
			}
		}
	}
	$qry .= "GROUP BY 1; ";
        $this->db->query($qry);

	$qry = "SELECT t.periodo, t.socios, t.cuotas, t.facturado, t.pagado_mes, 
			ROUND((t.pagado_mes/t.facturado)*100,2) porc_cobranza,
			t.pagado_mora, 
			ROUND((t.pagado_mora/t.facturado)*100,2) porc_mora,
			t.pago_parcial, t.impago,
			ROUND((t.impago/t.facturado)*100,2) porc_impago
		FROM tmp_cobranza t; ";

        $resultado = $this->db->query($qry)->result();
        return $resultado;
    }

    public function actividades_mensual($id_entidad){        
        $this->load->model('actividades_model');
        $actividades = $this->actividades_model->get_actividades($id_entidad);
        $labels = $keys = $data = "[";        
        $letras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','aa','ab','ac','ad','ae','af','ag','ah','ai','aj','ak','al','am','an');
        $cont = 0;
        foreach ($actividades as $actividad) {
            $labels .= '"'.$actividad->nombre.'",';
            $keys .= '"'.$letras[$cont].'",';        
            $cont++;
        }

        $fecha = date('Y-m-j');

        $labels .= "]";
        $keys .= "]";
        

        $fecha = date('Y-m-j');
        for ($i=0; $i <= 12; $i++) {
            $nuevafecha = strtotime ( '-'.$i.' month' , strtotime ( $fecha ) ) ;                            
            $n_mes = date ( 'm' , $nuevafecha );
            $n_anio = date ( 'Y' , $nuevafecha );
            $data .= '{y: "'.$n_anio.'-'.$n_mes.'",';

            $fechaa = $n_anio.'-'.$n_mes.'-01 00:00:00';
            $cont = 0;
            $activ = $activas = $bajas = 0;
            foreach ($actividades as $actividad) {

                $data .= $letras[$cont].': ';

                $this->db->where('estado',1);
                $this->db->where('id_entidad',$id_entidad);
                $this->db->where('aid',$actividad->id);
                $this->db->where('date <',$fechaa);
                $query = $this->db->get('actividades_asociadas');
                $activas = $query->num_rows();
                
                $this->db->where('estado',0);
                $this->db->where('aid',$actividad->id);
                $this->db->where('id_entidad',$id_entidad);
                $this->db->where('date_alta <',$fechaa);
                $this->db->where('date >',$fechaa);
                $query = $this->db->get('actividades_asociadas');
                $bajas = $query->num_rows();

                $activ = $bajas+$activas;

                $data .=  '"'.$activ.'",';
            $cont++;
            }

            $data .= '},';
        }
        $data .= "]";
        $ret = array(
            'data' => $data,
            'keys' => $keys,
            'labels' => $labels
            );

        return $ret;
    }

    public function actividades_anual($id_entidad){        
        $this->load->model('actividades_model');
        $actividades = $this->actividades_model->get_actividades($id_entidad);
        $labels = $keys = $data = "[";        
        $letras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','aa','ab','ac','ad','ae','af','ag','ah','ai','aj','ak','al','am','an');
        $cont = 0;
        foreach ($actividades as $actividad) {
            $labels .= '"'.$actividad->nombre.'",';
            $keys .= '"'.$letras[$cont].'",';        
            $cont++;
        }

        $fecha = date('Y-m-j');

        $labels .= "]";
        $keys .= "]";
        

        $fecha = date('Y-m-j');
        for ($i=0; $i <= 10; $i++) {
            $nuevafecha = strtotime ( '-'.$i.' year' , strtotime ( $fecha ) ) ;                            
            $n_mes = date ( 'm' , $nuevafecha );
            $n_anio = date ( 'Y' , $nuevafecha );
            $data .= '{y: "'.$n_anio.'",';
            
            $anio = $n_anio+1;
            $fechaa = $anio.'-01-01 00:00:00';
            $cont = 0;
            $activ = $activas = $bajas = 0;
            foreach ($actividades as $actividad) {

                $data .= $letras[$cont].': ';

                $this->db->where('estado',1);
                $this->db->where('aid',$actividad->id);
                $this->db->where('id_entidad',$id_entidad);
                $this->db->where('date <',$fechaa);
                $query = $this->db->get('actividades_asociadas');
                $activas = $query->num_rows();
                
                $this->db->where('estado',0);
                $this->db->where('aid',$actividad->id);
                $this->db->where('id_entidad',$id_entidad);
                $this->db->where('date <',$fechaa);
                $this->db->where('date_alta <',$fechaa);
                $this->db->where('date >',$fechaa);
                $query = $this->db->get('actividades_asociadas');
                $bajas = $query->num_rows();

                $activ = $bajas+$activas;

                $data .=  '"'.$activ.'",';
            $cont++;
            }

            $data .= '},';
        }
        $data .= "]";
        $ret = array(
            'data' => $data,
            'keys' => $keys,
            'labels' => $labels
            );

        return $ret;
    }
}
?>
