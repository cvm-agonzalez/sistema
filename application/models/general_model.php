<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 */
class General_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database('default');
    }

    public function get_logins($id_entidad, $dias) {
	$qry = "SELECT DISTINCT login FROM log_cambios WHERE id_entidad = $id_entidad AND log_ts > DATE_SUB(CURDATE(), INTERVAL $dias DAY) ;";
        $resultado = $this->db->query($qry)->result();

	return $resultado;
    }

    public function get_logs($id_entidad, $login, $dias) {
	$qry = "SELECT * FROM log_cambios WHERE id_entidad = $id_entidad ";
	if ( $login == "todos" ) {
	} else {
		$qry .= " AND login = '$login' ";
	}
	$qry .= " AND log_ts > DATE_SUB(CURDATE(), INTERVAL $dias DAY) ORDER BY log_ts DESC;";
        $resultado = $this->db->query($qry)->result();
	return $resultado;
    }

    public function write_log($log)
    {
        $this->db->insert('log_cambios',$log);
    }

    public function get_cats($id_entidad){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado > 0');
        $query = $this->db->get("categorias");
        if($query->num_rows() == 0){return false;}
        return $query->result();
    }

    public function get_cat($id){
        $this->db->where('id',$id);
        $query = $this->db->get("categorias");
        return $query->row();
    }

    public function get_entidades(){
        $query = $this->db->get("entidades");
        return $query->result();
    }

    public function get_cat_tipo($id_entidad, $tipo){
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('tipo  COLLATE latin1_general_cs = ',$tipo);
        $query = $this->db->get("categorias");
        return $query->row();
    }
	
    public function copia_cats($id_entidad) {
	$qry = "CREATE TEMPORARY TABLE t1
		SELECT * FROM categorias 
		WHERE id_entidad = 1 AND cid < 10 ORDER BY cid; ";
	$this->db->query($qry);
	
	$qry="UPDATE t1 SET id=0, precio = 10, id_entidad = $id_entidad;";
	$this->db->query($qry);

	$qry="INSERT INTO categorias SELECT * FROM t1; ";
	$this->db->query($qry);

    }

    public function update_cat($idcateg,$categ='')
    {
        $this->db->where('id',$idcateg);
        $this->db->update('categorias',$categ);
    }

    public function insert_cat($categ='')
    {
	$id_entidad = $categ['id_entidad'];
	$qry = "SELECT IF(MAX(cid)>10, MAX(cid), 10) max_id FROM categorias WHERE id_entidad = $id_entidad; ";
	$resultado = $this->db->query($qry)->row();
	if ( $resultado->max_id ) {
		$categ['cid'] = $resultado->max_id+1;
	} else {
		$categ['cid'] = 1;
	}

        $this->db->insert('categorias',$categ);
        return $this->db->insert_id();
    }

    public function delete_cat($idcateg)
    {
    	$this->db->where('id',$idcateg);
	$this->db->update('categorias',array('estado'=>0));
    }

/* entidades */

    public function get_ents(){
        $this->db->where('estado > 0');
        $query = $this->db->get("entidades");
        return $query->result();
    }

    public function get_ent($id){
        $this->db->where('id',$id);
        $query = $this->db->get("entidades");
        return $query->row();
    }

    public function update_ent($id,$entidad='')
    {
        $this->db->where('id',$id);
        $this->db->update('entidades',$entidad);
    }

    public function insert_ent($entidad='')
    {
	$entidad['estado'] = 1;
	$entidad['alta_sistema'] = date('Y-m-d G:i:s');
	$entidad['data'] = '';
        $this->db->insert('entidades',$entidad);
        return $this->db->insert_id();
    }

    public function get_ent_dir($id)
    {
	$qry = "SELECT *, CONCAT(CONCAT(REPEAT('0',4 - LENGTH(id)),id),'_',TRIM(abreviatura)) dir_name FROM entidades WHERE id = $id;";
	$resultado = $this->db->query($qry)->row();
	return $resultado;
    }

    public function delete_ent($id)
    {
    	$this->db->where('id',$id);
	$this->db->update('entidades',array('estado'=>0));
    }

/* grupo entidad */

    public function get_grupos_ent($id_entidad){
        $query = "SELECT ge.* FROM grupo_entidad ge JOIN entidades e ON e.id = $id_entidad AND e.grupo = ge.id; ";
        $resultado = $this->db->query($query)->result();
        return $resultado;
    }

    public function get_grupos(){
        $query = $this->db->get("grupo_entidad");
        return $query->result();
    }

    public function get_grupo($id){
        $this->db->where('id',$id);
        $query = $this->db->get("grupo_entidad");
        return $query->row();
    }

    public function get_ents_grupo($id){
        $this->db->where('grupo',$id);
        $query = $this->db->get("entidades");
        return $query->result();
    }

/**/

/**
ENVIOS
**/
    public function insert_envio($envio='')
    {
        $this->db->insert('envios',$envio);
        return $this->db->insert_id();
    }

    public function get_socios_by($id_entidad, $grupo,$data='',$activ)
    {
        if($grupo == 1){
            $this->db->where('id_entidad',$id_entidad);            
            $this->db->where('estado',1);            
	    if ( $activ == 1 ) {
		$this->db->where('suspendido',0);
	    } 
            $query = $this->db->get('socios');
            if($query->num_rows() == 0){return false;}
            $socios = $query->result();
        }else{
            switch ($grupo) {
                case 'categorias':
                    foreach ($data as $id) {
                        $this->db->or_where('categoria',$id);
                    }
                    $this->db->where('id_entidad',$id_entidad);            
                    $this->db->where('estado',1);            
		    if ( $activ == 1 ) {
			$this->db->where('suspendido',0);
	    	    } 
                    $query = $this->db->get('socios');
                    if($query->num_rows() == 0){return false;}
                    $socios = $query->result();
                    break;
                
                case 'actividades':
                    foreach ($data as $id) {        
                        $this->db->or_where('aid',$id);
                    }
                    $this->db->where('id_entidad',$id_entidad);            
                    $this->db->where('estado',1);
                    $query = $this->db->get('actividades_asociadas');
                    if($query->num_rows() == 0){return false;}
                    $actividades = $query->result();
                    $socios = array();
                    $this->load->model('socios_model');
                    foreach ($actividades as $actividad) {
                        $soc = $this->socios_model->get_socio($actividad->sid);
			if ( $activ == 1 ) {
				if ( $soc->suspendido == 0 ) {
                        		$socios[] = $soc;
				}
			} else {
                        		$socios[] = $soc;
			}
                    }
                    break;

                case 'socconactiv':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_conact($id_entidad, $activ);
                    break;

                case 'socsinactiv':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_sinact($id_entidad, $activ);
                    break;

                case 'soccomision':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_comision($$id_entidad, data, $activ);
                    break;

                case 'titcomision':
                    $this->load->model('socios_model');
		    $socios=$this->socios_model->get_socios_titu_comision($id_entidad, $data, $activ);
                    break;

            }
        }
        return $socios;
        
    }

    public function insert_envios_data($envio_data)
    {
        $this->db->insert('envios_data',$envio_data);
    }

    public function update_envio($id='',$envio)
    {
        $this->db->where('id',$id);
        $this->db->update('envios',$envio);
    }

    public function get_envios($id_entidad)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->order_by('id','desc');
        $query = $this->db->get('envios');
        if($query->num_rows() == 0){ return false; }
        $envios = $query->result();
        foreach ($envios as $envio) {
            $this->db->where('eid',$envio->id);
            $query = $this->db->get('envios_data');
            $envio->total = $query->num_rows();

            $this->db->where('eid',$envio->id);
            $this->db->where('estado',1);
            $query = $this->db->get('envios_data');
            $envio->enviados = $query->num_rows();
        }
        $query->free_result();
        return $envios;
    }

    public function get_envio($id='')
    {
        $this->db->where('id',$id);
        $query = $this->db->get('envios');
        if($query->num_rows() == 0){ return false; }
        $envio = $query->row();

        $this->db->where('eid',$envio->id);
        $query = $this->db->get('envios_data');
        $envio->total = $query->num_rows();

        $this->db->where('eid',$envio->id);
        $this->db->where('estado',1);
        $query = $this->db->get('envios_data');
        $envio->enviados = $query->num_rows();
        $query->free_result();

        return $envio;
    }

    public function get_envios_data($id)
    {
        $this->db->where('eid',$id);
        $query = $this->db->get('envios_data');        
        if($query->num_rows() == 0){ return false; }
        $envios_data = $query->result();
        $query->free_result();
        return $envios_data;
    }

    public function clear_envio_data($id)
    {
        $this->db->where('eid',$id);
        $this->db->delete('envios_data');
    }

    public function get_envio_data($id='')
    {
        $this->db->where('eid',$id);
        $this->db->where('estado',0);
        $query = $this->db->get('envios_data',1);
        if($query->num_rows() == 0){ return false; }
        $envio = $query->row();
        $query->free_result();
        return $envio;
    }

    public function enviado($id='')
    {
        $this->db->where('id',$id);
        $this->db->update('envios_data',array('estado'=>1));
    }

    public function get_enviados($id='')
    {
        $this->db->where('eid',$id);
        $this->db->where('estado',1);
        $query = $this->db->get('envios_data');
        $enviados = $query->num_rows();
        $query->free_result();
        return $enviados;
    }
/**
COMISIONES
**/

    public function get_actividades_comision($id_entidad)
    {
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('id',$this->session->userdata('id'));
        $query = $this->db->get('profesores');
        if($query->num_rows() == 0){return false;}
        $profesor = $query->row();

        $this->db->where('comision',$profesor->comision);
        $this->db->where('id_entidad',$id_entidad);
        $query = $this->db->get('actividades');
        if($query->num_rows() == 0){return false;}
        $actividades = $query->result();
        $query->free_result();
        return $actividades;

    }

    public function get_reporte($id_entidad, $aid='')
    {
        $this->load->model('actividades_model');
        $this->load->model('socios_model');
        $this->load->model('pagos_model');
        $reporte = new STDClass();
        $reporte->actividad = $this->actividades_model->get_actividad($aid);

        $this->db->select('sid');
        $this->db->distinct();
        $this->db->where('id_entidad',$id_entidad);
        $this->db->where('estado',1);
        $this->db->where('aid',$aid);
        $query = $this->db->get('actividades_asociadas');
        if($query->num_rows() == 0){ $socios = false; }
        $socios = $query->result();
        if($socios){
            foreach ($socios as $socio) {
                $socio->info = $this->socios_model->get_socio($socio->sid);
                $socio->deuda = $this->pagos_model->get_deuda_monto($socio->sid);
                
                $this->db->order_by('date','desc');
                $this->db->where('haber >',0);
                $this->db->where('sid',$socio->sid);
                $query = $this->db->get('facturacion',1);
                if($query->num_rows() == 0){
                    $socio->ultimo_pago = 'No se registran pagos.';
                }else{
                    $socio->ultimo_pago = $query->row()->date;
                }
            }
        }
        $reporte->socios = $socios;        
        return $reporte;
    }

    public function armo_cuerpo_email($id_entidad, $id_socio) {
	// armo mail
	$mail = $this->socios_model->get_resumen_mail($id_socio);
	$deuda = $this->pagos_model->get_deuda($id_socio);
	
	$cuota3 = $mail['resumen'];            

                $this->load->model("general_model");
                $ent_dir = $this->general_model->get_ent_dir($id_entidad)->dir_name;
                $head_mail = 'https://gestionsocios.com.ar/entidades/'.$ent_dir.'/email_head.jpg';

	
	// Armo encabezado con escudo y datos de cabecera
	$cuerpo  = "<table class='table table-hover' style='font-family:verdana' width='100%' >";
        $cuerpo .= "<thead>";
	$cuerpo .= "<tr>";
	$cuerpo .= "<th> <img src='$head_mail' alt='' ></th>";
	//$cuerpo .= "<th> <img src='http://clubvillamitre.com/images/Escudo-CVM_100.png' alt='' ></th>";
        //$cuerpo .= "<th style='font-size:30; background-color: #105401; color:#FFF' align='center'>CLUB VILLA MITRE</th>";
        $cuerpo .= "</tr>";
        $cuerpo .= "</thead>";
	$cuerpo .= "</table>";

	// Datos del Titular
        $cuerpo .= '<h3 style="font-family:verdana"><strong>Titular:</strong> '.$mail['sid'].'-'.$cuota3['titular'].'</h3>';
	
	// Analizo deuda previa a la facturación para poner mensaje acorde
        if($deuda < 0 ){
		$cuerpo .= "<h4 style='font-family:verdana' ><strong>Al d&iacute;a de la fecha Ud. adeuda $ ".abs($deuda)."</strong></h4>";
                $cuerpo .= "<h4 style='font-family:verdana' ><strong>PONGASE EN CONTACTO CON SECRETARIA PARA REGULARIZAR SU SITUACION</strong></h4>";
	} else {
		if($deuda == 0) {
			$cuerpo .= "<h4 style='font-family:verdana' ><strong>Usted esta al d&iacute;a con sus cuotas</strong></h4>";
		} else {
			if ( $mail['debtarj'] == null ) {
				$cuerpo .= "<h4 style='font-family:verdana' ><strong>Usted posee un saldo a favor de $ ".abs($deuda)."</strong></h4>";                
			}
		}
	}
	
	// Si es con grupo familiar
	if($cuota3['categ_tipo'] == 'F'){
		$cuerpo .= "<h5 style='font-family:verdana;'><strong>Integrantes</strong></h5><ul>";
		foreach ($cuota3['familiares'] as $familiar) {          
			$cuerpo .= "<li style='font-family:verdana;'>".$familiar['datos']->nombre." ".$familiar['datos']->apellido."</li>";                    
                }                    
		$cuerpo .= '</ul>';            
	}
           	 
	
	// Armo tabla de conceptos facturados en el mes
	
	// Titulos
	$cuerpo .= '<table class="table table-hover" width="100%" style="font-family: "Verdana";">
			<thead>
				<tr style="background-color: #666 !important; color:#FFF;">                        
					<th style="padding:5px;" align="left">Facturaci&oacute;n del Mes</th>
					<th style="padding:5px;" align="right">Monto</th>                        
				</tr>
			</thead>
		<tbody> ';
	
	// Cuota de Socio
	$cuerpo .= '<tr style="background: #CCC;">
			<td style="padding: 5px;">Cuota Mensual '.$cuota3['categoria'].'</td>
			<td style="padding: 5px;" align="right">$ '.$cuota3['cuota'].'</td>
		</tr>';
	// Si tiene descuento en la cuota social
	if($cuota3['descuento'] != 0.00){                        
		$cuerpo .= '<tr style="background: #CCC;">                    
				<td style="padding: 5px;">Descuento sobre cuota social</td>
				<td style="padding: 5px;" align="right">'.$cuota3['descuento'].'%</td>
			</tr>';                        
	}
		
	// Actividades
	foreach ($cuota3['actividades']['actividad'] as $actividad) {
		$cuerpo .= '<tr style="background: #CCC;">
				<td style="padding: 5px;">Cuota Mensual '.$actividad->nombre.'</td>
				<td style="padding: 5px;" align="right">$ '.$actividad->precio.'</td>
			</tr>';                        
	
		// Si tiene descuento lo pongo detallado
		if ( $actividad->descuento > 0 ) {
			if ( $actividad->monto_porcentaje == 0 ) {
				$msj_act=$actividad->descuento."$ ";
				$msj_act_valor=$actividad->precio-$actividad->descuento;
			} else {
				$msj_act=$actividad->descuento."% ";
				$msj_act_valor=$actividad->precio * $actividad->descuento / 100;
			}
			$cuerpo .= '<tr style="background: #CCC;">
					<td style="padding: 5px;">Descuento sobre Actividad '.$actividad->nombre.$msj_act.'</td>
					<td style="padding: 5px;" align="right">-$ '.$msj_act_valor.'</td>
				</tr>';                        
		}
		// Si tiene seguro lo pongo detallado
		if ( $actividad->seguro > 0 ) {
			$cuerpo .= '<tr style="background: #CCC;">
					<td style="padding: 5px;">Seguro Actividad '.$actividad->nombre.'</td>
					<td style="padding: 5px;" align="right">$ '.$actividad->seguro.'</td>
				</tr>';                        
		}
	} 
	
	// Familiares
	if($cuota3['familiares'] != 0){
		foreach ($cuota3['familiares'] as $familiar) {
			foreach($familiar['actividades']['actividad'] as $actividad){                           
				$cuerpo .= '<tr style="background: #CCC;">                    
						<td style="padding: 5px;">Cuota Mensual '.$actividad->nombre.' ['.$familiar['datos']->nombre.' '.$familiar['datos']->apellido.' ]</td>
						<td style="padding: 5px;" align="right">$ '.$actividad->precio.'</td>
					</tr>';
				// Si tiene descuento lo pongo detallado
				if ( $actividad->descuento > 0 ) {
					if ( $actividad->monto_porcentaje == 0 ) {
						$msj_act=$actividad->descuento."$ ";
						$msj_act_valor=$actividad->precio-$actividad->descuento;
					} else {
						$msj_act=$actividad->descuento."% ";
						$msj_act_valor=$actividad->precio * $actividad->descuento / 100;
					}
					$cuerpo .= '<tr style="background: #CCC;">
							<td style="padding: 5px;">Descuento sobre Actividad '.$actividad->nombre.$msj_act.'</td>
							<td style="padding: 5px;" align="right">-$ '.$msj_act_valor.'</td>
						</tr>';                        
				}
				// Si tiene seguro lo pongo detallado
				if ( $actividad->seguro > 0 ) {
					$cuerpo .= '<tr style="background: #CCC;">
							<td style="padding: 5px;">Seguro Actividad '.$actividad->nombre.'</td>
							<td style="padding: 5px;" align="right">$ '.$actividad->seguro.'</td>
						</tr>';                        
				}
		
			}                                   
		}
	}
	
	// Cuota Excedente
	if($cuota3['excedente'] >= 1){
		$cuerpo .='<tr style="background: #CCC;">                    
				<td style="padding: 5px;">Socio Extra (x'.$cuota3['excedente'].')</td>
				<td style="padding: 5px;" align="right">$ '.$cuota3['monto_excedente'].'</td>
			</tr>';                        
	}
	
	// Financiacion
	if($cuota3['financiacion']){
		foreach ($cuota3['financiacion'] as $plan) {                 
			$cuerpo .= '<tr style="background: #CCC;">                    
					<td style="padding: 5px;">Financiaci&oacute;n de Deuda - Cuota '.$plan->actual.'/'.$plan->cuotas.' ('.$plan->detalle.')</td>
					<td style="padding: 5px;" align="right">$ '.round($plan->monto/$plan->cuotas,2).'</td>
				</tr>';
		}
	}
	
	$cuerpo .= '</tbody>
			<tfoot>
			<tr>                        
				<th style="font-family:verdana;" align="left">TOTAL FACTURADO DEL MES</th>
				<th style="font-family:verdana;" align="right">$ '.$cuota3['total'].'</th>                        
			</tr> ';
	
	$resta_pagar = $cuota3['total'];
	$abonar=0;
	if ( $deuda < 0 ) {
		$abs_deuda = abs($deuda);
		$abonar = abs($deuda)+$cuota3['total'];
		$cuerpo .= '<tr>                        
				<th style="font-family:verdana;" align="left">DEUDA ANTERIOR</th>
				<th style="font-family:verdana;" align="right">$ '.$abs_deuda.'</th>                        
			</tr> 
			<tr>                        
				<th style="font-family:verdana;" align="left">TOTAL A ABONAR</th>
				<th style="font-family:verdana;" align="right">$ '.$abonar.'</th>                        
			</tr> ';
	} else { 
		if ( $deuda > 0 ) {
			if ( $mail['debtarj'] == null ) {
				$cuerpo .= '<tr>                        
						<th style="font-family:verdana;" align="left">SALDO A FAVOR ANTERIOR</th>
						<th style="font-family:verdana;" align="right">$ '.abs($deuda).'</th>                        
					</tr> ';
			} else {
				$cuerpo .= '<tr>                        
						<th style="font-family:verdana;" align="left">UD. ESTA ADHERIDO AL DEBITO AUTOMATICO</th>
					</tr> ';
			}
			$resta_pagar=$cuota3['total']-$deuda;
			if ( $resta_pagar > 0 ) {
				$cuerpo .= '<tr>                        
					<th style="font-family:verdana;" align="left">TOTAL A ABONAR</th>
					<th style="font-family:verdana;" align="right">$ '.$resta_pagar.'</th>
				</tr> ';
			} else { 
				if ( $resta_pagar < 0 ) {
					$cuerpo .= '<tr>                        
							<th style="font-family:verdana;" align="left">QUEDA A FAVOR</th>
							<th style="font-family:verdana;" align="right">$ '.abs($resta_pagar).'</th>                        
						</tr>';
				} else {
					if ( $resta_pagar == 0 ) {
						$cuerpo .= '<tr>                        
								<th style="font-family:verdana;" align="left">USTED ESTA AL DIA CON SUS PAGOS</th>
								<th style="font-family:verdana;" align="right">$ 0</th>                        
							</tr>';
					}
				}
			}
		}
	}
	
	$cuerpo .= '</tfoot> </table>';
	$cuerpo .= '';
	
	$acobrar= $mail['deuda'] - $cuota3['total'];

	$total=0;
	if($acobrar < 0){
		$cuerpo .= '<p style="font-family:verdana; font-style:italic;">Recuerde que iene 10 d&iacute;as para regularizar su situaci&oacute;n, contactese con Secretaria</p>';
	} else {
		if ($resta_pagar > 0 ) {
			$cuerpo .= '<p style="font-family:verdana; font-style:italic;">Recuerde que tiene hasta el d&iacute;a 10 para cancelar su saldo</p>';
		}
	}
	
	$cuerpo .= "<br> <br>";
	$cuerpo .= "<p style='font-family:verdana'> <b>ADMINISTRACION</b></p>";
	$cuerpo .= "<br> <br>";
	//$cuerpo .= "<img src='http://clubvillamitre.com/images/2doZocalo3.png' alt=''>";
	
	$arr_return = array (
		'acobrar' => $abonar,
		'cuerpo' => $cuerpo,
		'mail_destino' => $mail['mail']
	);

	return $arr_return;
    }

}
?>
