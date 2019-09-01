<div class="container" style="margin-top:50px;">
	<div class="starter-template hidden-print">
		<h1>Cobros por Actividad</h1>		
		<?
		
		if($fecha1 && $fecha2){
			$value = 'value="'.date('Y-m-d',strtotime($fecha1)).' - '.date('Y-m-d',strtotime($fecha2)).'"';
		}else{
			$value = '';
		}
		?>
		<form class="row" method="post" action="<?=base_url()?>imprimir/cobros/actividades">
			<div class="col-sm-3">
				<label>Rango de Fechas</label>
				<input class="form-control" name="daterange" <?=$value?> required>
			</div>
			<div class="col-sm-4">
				<label>Actividad</label>
				<select class="form-control" name="actividad" required>
					<option value="">---</option>
					<option value="-1" <? if('-1' == $actividad_s){echo 'selected';} ?>>Cuota Social</option>
					<?
					foreach ($actividades as $actividad) {
					?>
					<option value="<?=$actividad->id?>" <? if($actividad->id == $actividad_s){echo 'selected';} ?>><?=$actividad->nombre?></option>
					<?
					}
					?>
				</select>
			</div>
			<div class="col-sm-3">
				<label>Categoría</label>
				<select class="form-control" name="categoria">
					<option value="">Todas</option>
					<?
					for ($i=date('Y'); $i > date('Y')-100; $i--) { 					
					?>
					<option value="<?=$i?>" <? if($i == $categoria){echo 'selected';} ?>><?=$i?></option>
					<?
					}
					?>
				</select>
			</div>
			<div class="col-sm-2">
				<br>
				<button type="submit" class="btn btn-success">Generar</button>
			</div>
		</form>
	</div>
	<?
	if($cobros){
	?>
	<div class="">
		<div class="pull-right hidden-print">
		    <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
		    <a href="<?=base_url()?>imprimir/cobros_actividad_excel/<?=$fecha1?>/<?=$fecha2?>/<?=$actividad_s?>/<?=$categoria?>" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
		</div>
		<h3 class="page-header">Pagos ingresados del <?=date('d/m/Y',strtotime($fecha1))?> al <?=date('d/m/Y',strtotime($fecha2))?></h3>
		<? if(isset($actividad_info)){ ?>
        <h3>ACTIVIDAD: <?=$actividad_info->nombre?></h3>
        <? } ?>
	</div>
	<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="actividades_cobros">
	    <thead>
	        <tr>
	            <th width="80">Facturado El</th>
	            <th width="80">Pagado El</th>	            
	            <th>Monto</th>
	            <th>Act/Seguro</th>
	            <th>Socio</th>	            
	            <th>Fecha de Nacimiento</th>
	            <th>Observaciones</th>
	            <th>Deuda</th>
	            <th class="hidden-print">Operaciones</th>	           
	        </tr>
	    </thead>
		        
	    <tbody>
	    	<?
	    	$total = 0;
	    	foreach ($cobros as $ingreso) {    	
	    	$total = $total + $ingreso->pagado;
	    	?>
	        <tr>
	        	<td><?=date('Y-m-d',strtotime($ingreso->generadoel))?></td>
	        	<td><?=date('Y-m-d',strtotime($ingreso->pagadoel))?></td>
	        	<td>$ <?=$ingreso->pagado?></td>
	        	<td><? if ($ingreso->tipo == 6) { echo "Seguro"; } else { echo "Actividad"; } ?> </td>	        		       
	        	<td>#<?=$ingreso->sid?> - <?=$ingreso->socio->nombre?> <?=$ingreso->socio->apellido?></td>	        		       
	        	<td><?=date('Y/m/d',strtotime($ingreso->socio->nacimiento))?></td>
	        	<td><?=$ingreso->socio->observaciones?></td>
	        	<td>
					<?
	                if($ingreso->deuda){                      
	                    $hoy = new DateTime();
	                    $d2 = new DateTime($ingreso->deuda->generadoel);                
	                    $interval = $d2->diff($hoy);
	                    $meses = $interval->format('%m');
	                    if($meses > 0){
	                    ?>
	                    <div class="label label-danger">Debe <?=$meses?> <? if($meses > 1){ echo 'Meses';}else{echo 'Mes';} ?></div>                
	                    <?
	                    }else{
	                        if( $hoy->format('%m') == $d2->format('%m')){
	                        ?>
	                        <div class="label label-warning">Mes Actual</div>
	                        <?
	                        }else{                    
	                        ?>
	                        <div class="label label-success">Cuota al Día</div>
	                        <?                
	                        }
	                    }
	                }else{
	                    ?>
	                    <div class="label label-success">Cuota al Día</div>
	                    <?
	                }
	                ?>
	        	</td>
	        	<td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$ingreso->socio->id?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>
	        </tr>
	        <?
	    	}
	    	?>
	    </tbody>
	    <tfoot>
	    	<td colspan="2">Total</td>
	    	<td colspan="6">$ <?=number_format($total,2)?></td>
	    </tfoot>
	</table>
	<?
	}
	?>
</div>
