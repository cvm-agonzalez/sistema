<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
    <div class="row">

	<section class="page page-profile">
    	<div class="panel panel-default">
        	<div class="panel-body">

		<form class="form-horizontal ng-pristine ng-valid" action="#" method="post" id="estad-comi-form" enctype="multipart/form-data">

                	<div class="form-group col-lg-18">
                     		<label for="" class="col-sm-9">Comisión</label>
                     		<div class="col-sm-5">
                       			<span class=" ui-select">
                       			<select name="comision" id="comision" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
					<option value="-1" <? if($id_comision == -1){ echo 'selected'; $xcomi="Total";} ?>> Total Cobranza</option>
                       			<? foreach ( $comisiones as $comision ) { ?>
				                        <option value="<?=$comision->id?>" <? if($comision->id == $id_comision){ echo 'selected'; $xcomi=$comision->descripcion; } ?>><?=$comision->descripcion?></option>
                        			<?}?>
                       			</select>
                       			</span>
                     		</div>
                	</div>
	
                	<div class="form-group col-lg-18">
                     		<div class="col-sm-5">
                                        	<button id="btn_procesar" class="btn btn-success">Procesar</button> <i id="reg-cargando" class="fa fa-spinner fa-spin hidden"></i>
                                        	<button id="estad_comi_excel" value="" class="btn btn-success">EXCEL</button>
						<input type="hidden" name="arma_excel" id="arma_excel" value='0' class="form-control">
                     		</div>
                	</div>
		</form>



		<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="cobranza_table">
			<thead>
	        	   <tr>
	            		<th>Periodo</th>
	            		<th>Comisión</th>
	            		<th>Socios</th>
	            		<th>Cuotas</th>	                      
	            		<th>Facturado</th>
	            		<th>Cobrado al Dia</th>
	            		<th>Efectividad</th>
	            		<th>Cobrado Atrasado</th>
	            		<th>% Mora</th>
	            		<th>Ingresos Mes</th>
	            		<th>Impago</th>
	            		<th>% Impago</th>
	        	   </tr>
	    		</thead>
	    		<tbody>
	    		<?
	    		if($cobranza_tabla){
	    			foreach ($cobranza_tabla as $mes) {	    	
	    		?>
				<tr>				
					<td><?=$mes->periodo?></td>
					<td><?=$xcomi?></td>
					<td align="right"><?=$mes->socios?></td>
					<td align="right"><?=$mes->cuotas?></td>
					<td align="right"><?=$mes->facturado?></td>
					<td align="right"><?=$mes->pagado_mes_mes?></td>
					<td align="right"><?=$mes->porc_cobranza?></td>
					<td align="right"><?=$mes->pagado_mora?></td>
					<td align="right"><?=$mes->porc_mora?></td>
					<td align="right"><?=$mes->pagado_mes?></td>
					<td align="right"><?=$mes->impago?></td>
					<td align="right"><?=$mes->porc_impago?></td>
				</tr>
				<?
					}
					}
				?>
			</tbody>
		</table>
            </form>
       		</div>
    	</div>
	</section>
    </div>
</div>    
