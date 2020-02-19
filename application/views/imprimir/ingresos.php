<div class="container" style="margin-top:50px;">
	<div class="starter-template hidden-print">
		<h1>Ingresos</h1>
		<?
		if($fecha1 && $fecha2){
			$value = 'value="'.date('d/m/Y',strtotime($fecha1)).' - '.date('d/m/Y',strtotime($fecha2)).'"';
			$inicio = false;
		}else{
			$value = '';
			$inicio = true;
		}
		?>
			<input class="form-control" name="daterange" <?=$value?>>		
	</div>
	<?
	if($ingresos){
	?>
			<div class="">
				<div class="pull-right hidden-print">
		    		<button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
		    		<a href="<?=base_url()?>imprimir/ingresos_excel/<?=$fecha1?>/<?=$fecha2?>/" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
				</div>
				<h3 class="page-header">Pagos ingresados del <?=date('d/m/Y',strtotime($fecha1))?> al <?=date('d/m/Y',strtotime($fecha2))?></h3>
			</div>
			<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="ingresos_table">
	    		<thead>
	        		<tr>
	            		<th>Facturado El</th>
	            		<th>Pagado El</th>
	            		<th>Descripción</th>
	            		<th>Monto</th>
	            		<th>Pagado</th>
	            		<th>#ID</th>	           	            
	            		<th>Nro Socio</th>	           	            
	            		<th>Socio/Tutor</th>	           	            
	            		<th>Observaciones</th>	           	            
	            		<th class="hidden-print">Operaciones</th>	           
	        		</tr>
	    		</thead>
		       		 
	    		<tbody>
	    			<?
	    			$total = 0;
	    			foreach ($ingresos as $ingreso) {   
	    				$total = $total + $ingreso->pagado;
	    			?>
	        		<tr>
	        			<td><?=date('Y-m-d',strtotime($ingreso->generadoel))?></td>
	        			<td><?=date('Y-m-d',strtotime($ingreso->pagadoel))?></td>
	        			<td><?=$ingreso->descripcion?></td>
	        			<td align="right">$ <?=$ingreso->monto?></td>
	        			<td align="right">$ <?=$ingreso->pagado?></td>
	        			<td align="right"># <?=$ingreso->sid?></td>	        
	        			<td align="right"><?=$ingreso->nro_socio?></td>	        
	        			<td><?=$ingreso->socio->nombre?> <?=$ingreso->socio->apellido?></td>	        
	        			<td><?if($ingreso->ajuste==1){echo 'Ajuste Contable de Sistema'.$ingreso->socio->observaciones;} else { echo $ingreso->socio->observaciones;}?></td>	        
	        			
	        			<td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$ingreso->socio->id?>" class="btn btn-warning btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>	        
	        		</tr>
	        		<?
	    			}
	    			?>
	    		</tbody>
	    		<tfoot>
	    			<td colspan="3">Total</td>
	    			<td colspan="4">$ <?=number_format($total,2)?></td>
	    		</tfoot>
			</table>
		<?
	} else {
		if ( !$inicio ) {
			?>
				<div class="">
					<h3 class="label label-danger">NO EXISTE INFORMACION</h3>
				</div>
			<?
		}
	}
		?>
</div>
