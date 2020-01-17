<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Estado extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		$data = array();
		$this->load->view('estado/index', $data, FALSE);
	}

	public function ver() {
	        $id_entidad = $this->session->userdata('id_entidad');
		$this->load->model('socios_model');
		$this->load->model('pagos_model');
		$dni = $this->uri->segment(3);
		$socio = $this->socios_model->get_socio_by_dni($id_entidad, $dni);
		$data = array();
	        $data['socio'] = $socio;

		$this->load->view('estado/index2', $data, FALSE);

		if($socio){
			$socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad, $socio->id);
			$socio->cuota = $this->pagos_model->get_monto_socio($socio->id)['total'];			
			$socio->facturacion = $this->pagos_model->get_facturacion($id_entidad, $socio->id);
			if($socio->nro_socio == ''){
				$nro_socio = $socio->id;
			}else{
				$nro_socio = $socio->nro_socio;
			}
			?>

			<h2>#<?=$nro_socio?> - <?=$socio->apellido?>, <?=$socio->nombre?></h2>			
			<?
			if($socio->deuda){                      
	            $hoy = new DateTime();
	            $d2 = new DateTime($socio->deuda->generadoel);                
	            $interval = $d2->diff($hoy);
	            $meses = $interval->format('%m');
	            if($meses > 0){
	            ?>
	            	<div class="alert alert-danger">
	            		<i class="fa fa-warning"></i> SOCIO CON DEUDA | Debe <?=$meses?> <? if($meses > 1){ echo 'Meses';}else{echo 'Mes';} ?>
	            	</div>
	            <?
	            }else{
	                if( $hoy->format('%m') != $d2->format('%m') && $socio->deuda->monto != '0.00' ){
	                ?>
	                <div class="alert alert-warning"><i class="fa fa-info"></i> Saldo del mes anterior</div>
	                <?
	                }else{                    
	                ?>
	                <div class="alert alert-success"><i class="fa fa-check-square"></i> Cuota al Día</div>
	                <?                
	                }
	            }
	        }else{
	            ?>
	            <div class="alert alert-success"><i class="fa fa-check-square"></i> Sin Deuda</div>
	            <?
	        }
	        $data['facturacion'] = $socio->facturacion;
	        $this->load->view('estado/resumen2', $data, FALSE);
		}else{
			?>
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-triangle"></i>
				No se encontró ningún socio.
			</div>
			<?		
		}
	}

	public function get_socio()
	{
	        $id_entidad = $this->session->userdata('id_entidad');
		header('Access-Control-Allow-Origin: *'); 
		$this->load->model('socios_model');
		$this->load->model('pagos_model');
		$input = $this->input->post('socio_input');
		if(strlen($input) > 9){			
			$socio = $this->socios_model->get_socio_by_barcode($id_entidad,$input);
		}else{
			$socio = $this->socios_model->get_socio_by_dni($id_entidad,$input);
		}
		if($socio){
			$socio->deuda = $this->pagos_model->get_ultimo_pago_socio($id_entidad,$socio->id);
			$socio->cuota = $this->pagos_model->get_monto_socio($socio->id)['total'];			
			$socio->facturacion = $this->pagos_model->get_facturacion($id_entidad,$socio->id);
			if($socio->nro_socio == ''){
				$nro_socio = $socio->id;
			}else{
				$nro_socio = $socio->nro_socio;
			}
			?>

			<h2>#<?=$nro_socio?> - <?=$socio->apellido?>, <?=$socio->nombre?></h2>			
			<?
			if($socio->deuda){                      
	            $hoy = new DateTime();
	            $d2 = new DateTime($socio->deuda->generadoel);                
	            $interval = $d2->diff($hoy);
	            $meses = $interval->format('%m');
	            if($meses > 0){
	            ?>
	            	<div class="alert alert-danger">
	            		<i class="fa fa-warning"></i> SOCIO CON DEUDA | Debe <?=$meses?> <? if($meses > 1){ echo 'Meses';}else{echo 'Mes';} ?>
	            	</div>
	            <?
	            }else{
	                if( $hoy->format('%m') != $d2->format('%m') && $socio->deuda->monto != '0.00' ){
	                ?>
	                <div class="alert alert-warning"><i class="fa fa-info"></i> Saldo del mes anterior</div>
	                <?
	                }else{                    
	                ?>
	                <div class="alert alert-success"><i class="fa fa-check-square"></i> Cuota al Día</div>
	                <?                
	                }
	            }
	        }else{
	            ?>
	            <div class="alert alert-success"><i class="fa fa-check-square"></i> Sin Deuda</div>
	            <?
	        }
	        $data['facturacion'] = $socio->facturacion;
	        $this->load->view('estado/resumen', $data, FALSE);
		}else{
			?>
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-triangle"></i>
				No se encontró ningún socio.
			</div>
			<?		
		}
		//echo json_encode($socio);
	}

}

/* End of file estado.php */
/* Location: ./application/controllers/estado.php */
