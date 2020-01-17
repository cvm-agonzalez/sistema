<div class="container" style="margin-top:50px;">
	<div class="starter-template hidden-print">
		<h1>Morosos</h1>
		<div id="actividades_print">
			<form class="row" method="post" action="<?=base_url()?>imprimir/listado/morosos">
				<div class="col-sm-5">
					<label>Comision</label>
					<select class="form-control" name="comisiones" id="comisiones">
		
						<option value="" >Seleccione Comision</option>
						<option value="-1" <? if('-1' == $comision_sel){ echo 'selected'; } ?>>Cuota Social</option>
						<? foreach ($comisiones as $comision) { 
							?>
							<option value="<?=$comision->id?>" <? if($comision_sel == $comision->id){ echo 'selected'; } ?>><?=$comision->descripcion?></option>
							<?
						}
						?>
					</select>
				</div>
				<div class="col-sm-5">
					<label>Actividad</label>
					<select class="form-control" name="morosos_activ" id="morosos_activ" >
						<option value="">Seleccionar</option>
						<?
						foreach ($actividades as $actividad) {                        
							?>
							<option value="<?=$actividad->id?>" <? if($actividad->id == $actividad_sel){ echo 'selected'; } ?>><?=$actividad->nombre?></option>
							<?
						}   
						?>
					</select>                    
				</div>				
				<div class="col-sm-2" style="margin-top:25px;">
					<button class="btn btn-success" id="filtro_morosos"> Generar</button>                    
				</div>
				<div class="clearfix"></div>			
			</form>
		</div>
	</div>
	<div id="listado_morosos">
		<? 
		if($morosos){
		?>
		<div class="pull-right hidden-print">
		    <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
		    <a href="<?=base_url()?>imprimir/morosos_excel/<?=$comision_sel?>/<?=$actividad_sel?>/" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
		</div>
		<h3 class="page-header">Listado de Morosos </h3>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="morosos_table">
		    <thead>
		        <tr>
		            <th>DNI</th>
		            <th>ID</th>
		            <th>Nombre</th>
		            <th>Teléfonos</th>
		            <th>Domicilio</th>
		            <th>Actividad</th>			            
		            <th>Estado Asoc</th>			            
		            <th>Deuda Cta Soc</th>			            
		            <th>UltPago Cuota</th>			            
		            <th>Deuda Actividad</th>			            
		            <th>UltPago Actividad</th>			            
		            <th class="hidden-print">Operaciones</th>	           
		        </tr>
		    </thead>
			        
		    <tbody>
		    	<?
		    	
		    	foreach ($morosos as $ingreso) {    	
				switch ( $ingreso['estado'] ) {
					case 1: $xestado="SUSP"; break;
					case 0: $xestado="ACTI"; break;
				}
		    	?>
		        <tr>			        	
		        	<td><?=$ingreso['dni']?></td>
		        	<td><?=$ingreso['sid']?></td>
		        	<td><?=$ingreso['apynom']?></td>
		        	<td><?=$ingreso['telefono']?></td>
		        	<td><?=$ingreso['domicilio']?></td>
		        	<td><?=$ingreso['actividad']?></td>
		        	<td><?=$xestado?></td>
		        	<td>$ <?=$ingreso['deuda_cuota']*-1?></td>			        	
		        	<td><?=$ingreso['gen_cuota']?></td>			        	
		        	<td>$ <?=$ingreso['deuda_activ']*-1?></td>			        	
		        	<td><?=$ingreso['gen_activ']?></td>			        	
		        	<td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$ingreso['sid']?>" class="btn btn-warning btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>	        
		        </tr>
		        <?
		    	}		    	
		    	?>
		    </tbody>
		</table>
		<? } ?>
	</div>	

</div><!-- /.container -->
