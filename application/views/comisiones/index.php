<div class="container" style="margin-top:50px;">	
	<h3 class="page-header">
		Pagos por Actividad
	</h3>
	<?
	if($actividades){
		?>
		<form action="<?=base_url()?>comisiones" class="hidden-print" method="post">
			<div class="col-xs-10">
				<label>Seleccionar Actividad</label>
				<select class="form-control" name="actividad_id" required>
					<option value="">--Seleccionar Actividad--</option>
				<?
				foreach ($actividades as $actividad) {
					?>
					<option value="<?=$actividad->Id?>" <? if($actividad_id == $actividad->Id){ echo 'selected'; } ?> ><?=$actividad->nombre?></option>
					<?
				}
				?>
				</select>
			</div>
			<div class="col-xs-2">
				<label>&nbsp;</label><br>
				<button class="btn btn-success btn-block">Generar</button>
			</div>
			<div class="clearfix"></div>
		</form>
		<hr class="hidden-print">
		<?
	}
	if($reporte){
	?>
	<div class="pull-left" style="margin-bottom:10px;">
		<h2 class="page-heading"><?=$reporte->actividad->nombre?></h2>
	</div>
	<div class="pull-right hidden-print" style="margin-bottom:10px;">
		<button class="btn btn-info" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button>
		<a href="<?=base_url()?>comisiones/excel/<?=$actividad_id?>" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
	</div>
	<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="comisiones_table">
		<thead>
	        <tr>
	            <th>Apellido / Nombre</th>
	            <th>Socio #</th>
	            <th>DNI</th>
	            <th>Fecha de Nacimiento</th>	                      
	            <th>Deuda</th>
	            <th>Último pago</th>
	            <th>Opciones</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?
	    	if($reporte->socios){
	    		foreach ($reporte->socios as $socio) {	    	
	    	?>
			<tr>				
				<td><?=$socio->info->apellido?> <?=$socio->info->nombre?></td>
				<td><?=$socio->info->Id?></td>
				<td><?=$socio->info->dni?></td>
				<td><?=date('Y/m/d',strtotime($socio->info->nacimiento))?></td>
				<td>
					<? 
					if($socio->deuda >= 0){
					?>
					<label class="label label-success">Socio sin Deuda</label>
					<?
					}else{
					?>
					<label class="label label-warning">$ <?=number_format($socio->deuda*-1,2)?></label>
					<?
					}
					?>
				</td>
				<td><?=$socio->ultimo_pago?></td>				
				<td><a href="<?=base_url()?>estado/ver/<?=$socio->info->dni ?>">Ver Resumen</a> </td>
			</tr>
			<?
				}
			}
			?>
		</tbody>
	</table>
	<?
	}
	if(!$reporte && !$actividades){
		?>
		-- Esta comisión no posee actividades asociadas -- 
		<?
	}
	?>
</div>
