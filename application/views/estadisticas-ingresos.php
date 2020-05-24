<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
    <div class="row">

	<section class="page page-profile">
    	<div class="panel panel-default">
        	<div class="panel-body">

		<form class="form-horizontal ng-pristine ng-valid" action="#" method="post" id="estad-ing-form" enctype="multipart/form-data">

                	<div class="form-group col-lg-18">
                     		<label for="" class="col-sm-9">Meses</label>
                     		<div class="col-sm-5">
                       			<span class=" ui-select">
                       			<select name="meses" id="meses" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                       			<? foreach ( $meses as $mesx ) { ?>
				                        <option value="<?=$mesx->mes?>" <? if($mesx->mes == $mes){ echo 'selected'; } ?>><?=$mesx->descr_mes?></option>
                        			<?}?>
                       			</select>
                       			</span>
                     		</div>
                	</div>
	
                	<div class="form-group col-lg-18">
                     		<div class="col-sm-5">
                                                <button id="btn_procesar" class="btn btn-success">Procesar</button> <i id="reg-cargando" class="fa fa-spinner fa-spin hidden"></i>
                                                <button id="estad_ing_excel" value="" class="btn btn-success">EXCEL</button>
                                                <input type="hidden" name="arma_excel" id="arma_excel" value='0' class="form-control">
                     		</div>
                	</div>
		</form>



		<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="ingresos_table">
			<thead>
	        	   <tr>
	            		<th>Dia</th>
	            		<th>Ingresos Cooperativa</th>
	            		<th>Ingresos Cuenta Digital</th>
	            		<th>Ingresos Debito Tarjeta</th>	                      
	            		<th>Ingresos Manuales</th>	                      
	            		<th>Ajustes</th>
	        	   </tr>
	    		</thead>
	    		<tbody>
	    		<?
	    		if($ingresos_tabla){
	    			foreach ($ingresos_tabla as $mes) {	    	
	    		?>
				<tr>				
					<td><?=$mes->dia?></td>
					<td align="right"><?=$mes->ing_col?></td>
					<td align="right"><?=$mes->ing_cd?></td>
					<td align="right"><?=$mes->ing_debito?></td>
					<td align="right"><?=$mes->ing_manual?></td>
					<td align="right"><?=$mes->ajustes?></td>
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
