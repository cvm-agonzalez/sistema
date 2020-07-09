<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class Comisiones_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function get_comisiones($id_entidad) {
        $query = $this->db->where('id_entidad',$id_entidad);
        $query = $this->db->where('estado','1');
        $query = $this->db->get('comisiones');
        if($query->num_rows() == 0){return false;}
        $comisiones = $query->result();
        return $comisiones;
    }

    public function get_comision($id_entidad, $id_comision) {
        $query = $this->db->where('id_entidad',$id_entidad);
        $query = $this->db->where('id',$id_comision);
        $query = $this->db->get('comisiones');
        if($query->num_rows() == 0){return false;}
        $comision = $query->row();
        return $comision;
    }


    public function resumen($id_entidad, $comision, $periodo, $anio_corte,$suspendido) {
	/*	Funcion que arma el total de socios y su situacion con la actividad */
        $this->load->model('pagos_model');

        /* Total de socios a revisar porque tienen o tuvieron actividad relacionada de la comision */
        $qry = "DROP TEMPORARY TABLE IF EXISTS tmp_socios ; ";
        $this->db->query($qry);
        $qry = "CREATE TEMPORARY TABLE tmp_socios ( INDEX (sid) )
                SELECT DISTINCT aa.sid, s.dni, s.apellido, s.nombre, s.suspendido
                FROM actividades_asociadas aa 
                        JOIN actividades a ON ( a.id_entidad = $id_entidad AND a.comision = $comision AND a.id = aa.aid )
                        JOIN socios s ON ( s.id_entidad = $id_entidad AND aa.sid = s.id AND s.suspendido = $suspendido )
                WHERE aa.id_entidad = $id_entidad; ";
        $this->db->query($qry);

	/* Saldo de cuotas sociales */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_saldos_cuotasoc ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_saldos_cuotasoc ( INDEX (sid) )
		SELECT ts.sid, 
			SUM(pagado-monto) saldo,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') < $anio_corte, pagado-monto,0)) saldo_ant,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') = $anio_corte, pagado-monto,0)) saldo_corte,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') > $anio_corte, pagado-monto,0)) saldo_hoy
		FROM tmp_socios ts
			LEFT JOIN pagos p ON ( ts.sid = p.sid AND p.aid = 0 AND p.estado = 1 )
		GROUP BY 1; ";
        $this->db->query($qry);


	/* Saldo de actividades que tienen en relacion activa */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_saldos_activ ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_saldos_activ ( INDEX (sid) )
		SELECT aa.sid, aa.aid, 
			IFNULL(SUM(pagado-monto),0) saldo,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') < $anio_corte, pagado-monto,0)) saldo_ant,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') = $anio_corte, pagado-monto,0)) saldo_corte,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') > $anio_corte, pagado-monto,0)) saldo_hoy
		FROM tmp_socios ts
        		JOIN actividades_asociadas aa ON ( ts.sid = aa.sid AND aa.estado = 1 )
        		JOIN actividades a ON ( a.id = aa.aid AND a.comision = $comision )
        		LEFT JOIN pagos p ON ( ts.sid = p.sid AND p.aid = a.id AND p.estado = 1 )
		GROUP BY 1,2; ";
        $this->db->query($qry);

	/* Busco los socios que tuvieron alguna vez relacionada esta actividad y que tienen su relacion desactiva ahora */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_activ_desrel ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_activ_desrel ( INDEX (sid,aid) )
		SELECT DISTINCT ts.sid, aa.aid
		FROM tmp_socios ts
			JOIN actividades_asociadas aa ON ( ts.sid = aa.sid AND aa.estado = 0 )
			JOIN actividades a ON ( aa.aid = a.id AND a.comision = $comision )
			LEFT JOIN tmp_saldos_activ tsa ON ( ts.sid = tsa.sid AND aa.aid = tsa.aid )
		WHERE tsa.sid is NULL; ";
        $this->db->query($qry);

	/* Saldo de actividades que tienen su relacion desactiva */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_saldos_activ_desrel ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_saldos_activ_desrel ( INDEX (sid) )
		SELECT tad.sid, p.aid, 
			IFNULL(SUM(pagado-monto),0) saldo,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') < $anio_corte, pagado-monto,0)) saldo_ant,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') = $anio_corte, pagado-monto,0)) saldo_corte,
			SUM(IF(DATE_FORMAT(p.generadoel,'%Y') > $anio_corte, pagado-monto,0)) saldo_hoy
		FROM tmp_activ_desrel tad
        		LEFT JOIN pagos p ON ( tad.sid = p.sid AND p.aid = tad.aid AND p.estado = 1 )
		GROUP BY 1,2; ";
        $this->db->query($qry);

	/* Armo temporal con el detalle de deudas de cuotas sociales */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_det_socios1 ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_det_socios1
		SELECT ts.*, IF(tsc.saldo > 0, 1, 0) socio_deuda_cs, 
			IF(tsc.saldo > 0, tsc.saldo, 0) deuda_cs,
			IF(tsc.saldo_ant > 0, tsc.saldo_ant, 0) deuda_cs_ant,
			IF(tsc.saldo_corte > 0, tsc.saldo_corte, 0) deuda_cs_corte,
			IF(tsc.saldo_hoy > 0, tsc.saldo_hoy, 0) deuda_cs_hoy
		FROM tmp_socios ts
			LEFT JOIN tmp_saldos_cuotasoc tsc USING ( sid ) ; ";
        $this->db->query($qry);

	/* Agrego a la temporal el detalle de deudas de los socios relacionados a las actividades */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_det_socios2 ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_det_socios2
		SELECT t1.*, tsa.aid aid_rel, 
			IF(tsa.saldo > 0, tsa.saldo, 0) deuda_act_rel,
			IF(tsa.saldo_ant > 0, 1, 0) socio_deuda_ant_act_rel, IF(tsa.saldo_ant > 0, tsa.saldo_ant, 0) deuda_ant_act_rel,
			IF(tsa.saldo_corte > 0, 1, 0) socio_deuda_corte_act_rel, IF(tsa.saldo_corte > 0, tsa.saldo_corte, 0) deuda_corte_act_rel,
			IF(tsa.saldo_hoy > 0, 1, 0) socio_deuda_hoy_act_rel, IF(tsa.saldo_hoy > 0, tsa.saldo_hoy, 0) deuda_hoy_act_rel
		FROM tmp_det_socios1 t1
			LEFT JOIN tmp_saldos_activ tsa USING ( sid ) ; ";
        $this->db->query($qry);

	/* Armo la temporal final con todos los detalles agregandole los socios con relacion desactivada */
	$qry = "DROP TEMPORARY TABLE IF EXISTS tmp_detalle_socios ; ";
        $this->db->query($qry);
	$qry = "CREATE TEMPORARY TABLE tmp_detalle_socios
		SELECT t2.*, tsad.aid aid_desrel, 
			IF(tsad.saldo > 0, tsad.saldo, 0) deuda_act_desrel,
			IF(tsad.saldo_ant > 0, 1, 0) socio_deuda_ant_act_desrel, IF(tsad.saldo_ant > 0, tsad.saldo_ant, 0) deuda_ant_act_desrel,
			IF(tsad.saldo_corte > 0, 1, 0) socio_deuda_corte_act_desrel, IF(tsad.saldo_corte > 0, tsad.saldo_corte, 0) deuda_corte_act_desrel,
			IF(tsad.saldo_hoy > 0, 1, 0) socio_deuda_hoy_act_desrel, IF(tsad.saldo_hoy > 0, tsad.saldo_hoy, 0) deuda_hoy_act_desrel
		FROM tmp_det_socios2 t2
			LEFT JOIN tmp_saldos_activ_desrel tsad USING ( sid ) ; ";
        $this->db->query($qry);

	/* Armo RESUMEN final */
	$qry = "SELECT a.id, a.aid, a.nombre descr_activ,

			COUNT(DISTINCT(IF(tsa.saldo is NOT NULL ,s.id,NULL))) socios_rel, 
			COUNT(DISTINCT(IF(tsa.saldo is NOT NULL AND aa.descuento > 0,s.id,NULL))) socios_becados, 
			COUNT(DISTINCT(IF(tsa.saldo is NOT NULL AND s.suspendido=0,s.id,NULL))) soc_act_rel, 
			COUNT(DISTINCT(IF(tsa.saldo is NOT NULL AND s.suspendido=1,s.id,NULL))) soc_susp_rel, 
		
			COUNT(DISTINCT(IF(tsa.saldo is NULL ,s.id,NULL))) socios_desrel, 
			COUNT(DISTINCT(IF(tsa.saldo is NULL AND s.suspendido=0,s.id,NULL))) soc_act_desrel, 
			COUNT(DISTINCT(IF(tsa.saldo is NULL AND s.suspendido=1,s.id,NULL))) soc_susp_desrel, 
		
        		SUM(IF(tsc.saldo < 0, 1, 0)) socio_deuda_cs,
        		SUM(IF(tsc.saldo < 0, tsc.saldo, 0)) deuda_cs,
        		SUM(IF(tsc.saldo_ant < 0, tsc.saldo_ant, 0)) deuda_cs_ant,
        		SUM(IF(tsc.saldo_corte < 0, tsc.saldo_corte, 0)) deuda_cs_corte,
        		SUM(IF(tsc.saldo_hoy < 0, tsc.saldo_hoy, 0)) deuda_cs_hoy,
		
        		SUM(IF(tsa.saldo < 0, 1, 0)) socio_deuda_act_rel,
        		SUM(IF(tsa.saldo < 0, tsa.saldo, 0)) deuda_act_rel,
        		SUM(IF(tsa.saldo_ant < 0, tsa.saldo_ant, 0)) deuda_act_rel_ant,
        		SUM(IF(tsa.saldo_corte < 0, tsa.saldo_corte, 0)) deuda_act_rel_corte,
        		SUM(IF(tsa.saldo_hoy < 0, tsa.saldo_hoy, 0)) deuda_act_rel_hoy,
		
        		SUM(IF(tsad.saldo < 0, 1, 0)) socio_deuda_act_desrel,
			SUM(IF(tsad.saldo < 0, tsad.saldo, 0)) deuda_act_desrel,
        		SUM(IF(tsad.saldo_ant < 0, tsad.saldo_ant, 0)) deuda_act_desrel_ant,
        		SUM(IF(tsad.saldo_corte < 0, tsad.saldo_corte, 0)) deuda_act_desrel_corte,
        		SUM(IF(tsad.saldo_hoy < 0, tsad.saldo_hoy, 0)) deuda_act_desrel_hoy
		
		FROM actividades a
			JOIN actividades_asociadas aa ON ( a.id = aa.aid )
			JOIN socios s ON ( aa.sid = s.id )
        		LEFT JOIN tmp_saldos_cuotasoc tsc USING ( sid )
        		LEFT JOIN tmp_saldos_activ tsa ON aa.sid = tsa.sid AND aa.aid = tsa.aid
				LEFT JOIN tmp_saldos_activ_desrel tsad ON aa.sid = tsad.sid AND aa.aid = tsad.aid
		WHERE a.id_entidad = $id_entidad AND a.comision = $comision
		GROUP BY a.id ;";

        $resultado = $this->db->query($qry)->result();

        return $resultado;
    }

}  
?>
