<?
	if(intval($cuota['total']) != intval($cupon->monto)){
	?>
	<div class="panel panel-default">
    	<div class="panel-heading"><span class="glyphicon glyphicon-th"></span> Tiene un nuevo cupón para utilizar</div>
		<div class="panel-body">	
			<div class="col-sm-6">       	   
		    	<h3><strong>Titular:</strong> <?=$cuota['titular']?></h3>
		    	<h5><strong>Categoría:</strong> <?=$cuota['categoria']?></h5>
		    	<?
		    	if($cuota['categoria'] == 'Grupo Familiar'){
		    	?>
		    	<h5><strong>Integrantes</strong></h5>
		    	<ul>
		    		<? foreach ($cuota['familiares'] as $familiar) { ?>    		
		    		<li><?=$familiar['datos']->nombre?> <?=$familiar['datos']->apellido?></li>
		    		<?
		    		}
		    		?>
		    	</ul>
		    	<?
		    	}
		    	?>	    
	   		</div>
	    	<div class="col-sm-6" style="margin-top:30px;">
				<button id="gen_cupon" data-monto="<?=$cuota['total']?>" data-id="<?=$cuota['tid']?>" class="btn btn-warning">Generar Nuevo Cupón</button>
			</div>
	    	<table class="table table-hover" width="80%;">
	            <thead>
	                <tr>                        
	                    <th>Descripción</th>
	                    <th>Monto</th>                        
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
	                    	Cuota Mensual <?=$cuota['categoria']?>
	                    	<? if($cuota['descuento'] > 0.00){ ?>
	                    	- $ <?=$cuota['cuota_neta']?> &nbsp;<label class="label label-info"><?=$cuota['descuento']?>% BECADO</label>
	                    	<? } ?>
	                    </td>
	                    <td>$<?=$cuota['cuota']?></td>
	                </tr>
	                <? foreach ($cuota['actividades']['actividad'] as $actividad) {?>
	                <tr>                    
	                    <? if($actividad->descuento > 0){ ?>
                        <td>Cuota Mensual <?=$actividad->nombre?> - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0 ) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                        <td>$<? if ( $actividad->monto_porcentaje == 0 ) { echo $actividad->precio - $actividad->descuento; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); } ?><td>
                        <? }else{ ?>                   
                        <td>Cuota Mensual <?=$actividad->nombre?></td>
                        <td>$<?=$actividad->precio?><td>
                        <? } ?>
	                </tr>	
	                <?
	               	} 
	               	if($cuota['familiares'] != 0){
		               	foreach ($cuota['familiares'] as $familiar) {
		               		foreach($familiar['actividades']['actividad'] as $actividad){		               		
			               	?>
			               	<tr> 
			               		<? if($actividad->descuento > 0){ ?>
                                    <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ] - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0 ) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                        	    <td>$<? if ( $actividad->monto_porcentaje == 0 ) { echo $actividad->precio - $actividad->descuento; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); } ?><td>
                                    <? }else{ ?>                   
                                    <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ]</td>
                                    <td>$<?=$actividad->precio?><td>
                                <? } ?>			                   
			                </tr>
			               	<?	 	 
		               		}              		            	
		               	}
	               	}
	               	if($cuota['excedente'] >= 1){
	               	?>
	               			<tr>                    
			                    <td>Socio Extra (x<?=$cuota['excedente']?>)</td>
			                    <td>$<?=$cuota['monto_excedente']?><td>
			                </tr>
	               	<?
	               	}

	               	if($cuota['financiacion']){
	               		foreach ($cuota['financiacion'] as $plan) {	               	
	               	?>
	               			<tr>                    
			                    <td>Financiación de Deuda (<?=$plan->detalle?>)</td>
			                    <td>$<?=round($plan->monto/$plan->cuotas,2)?><td>
			                </tr>
	               	<?
	               		}
	               	}	               	
	                ?>	                                             
	            </tbody>
	            <tfoot>
	                <tr>                        
	                    <th>Total</th>
	                    <th>$<?=$cuota['total']?></th>                        
	                </tr>
	            </tfoot>
	        </table>			
	    </span>
		</div>
	</div>
	<?
	}else{
	?>
	<div class="panel panel-default">
	    <div class="panel-heading"><span class="glyphicon glyphicon-th"></span> CUPON ACTUAL</div>
	    <div class="panel-body">
		<?
		if($cupon == '0'){
			echo 'Todavia no se ha generado ningun cupón';
		}else{
			?>	
			<div class="col-sm-6">
				<h3><strong>Titular:</strong> <?=$cuota['titular']?></h3>
		    	<h5><strong>Categoría:</strong> <?=$cuota['categoria']?></h5>
		    	<?
		    	if($cuota['categoria'] == 'Grupo Familiar'){
		    	?>
		    	<h5><strong>Integrantes</strong></h5>
		    	<ul>
		    		<? foreach ($cuota['familiares'] as $familiar) { ?>    		
		    		<li><?=$familiar['datos']->nombre?> <?=$familiar['datos']->apellido?></li>
		    		<?
		    		}
		    		?>
		    	</ul>
		    	<?
		    	}
		    	?>
		    </div>
		    <div class="col-sm-6" style="margin-top:30px;" align="center">
		    	<img src="<?=$baseurl?>images/cupones/<?=$cupon->Id?>.png"><br><br>
				<button class="btn btn-primary" id="print_cupon" data-id="<?=$cupon->Id?>">Imprimir</button>
				
			</div>
	    	<table class="table table-hover" width="80%;">
	            <thead>
	                <tr>                        
	                    <th>Descripción</th>
	                    <th>Monto</th>                        
	                </tr>
	            </thead>

	            <tbody>
	                <tr>
	                    <td>
	                    	Cuota Mensual <?=$cuota['categoria']?>
	                    	<? if($cuota['descuento'] > 0.00){ ?>
	                    	- $ <?=$cuota['cuota_neta']?> &nbsp;<label class="label label-info"><?=$cuota['descuento']?>% BECADO</label>
	                    	<? } ?>
	                    </td>
	                    <td>$<?=$cuota['cuota']?></td>
	                </tr>
	                <? foreach ($cuota['actividades']['actividad'] as $actividad) {?>
	                <tr>                    
	                    <? if($actividad->descuento > 0){ ?>
                        <td>Cuota Mensual <?=$actividad->nombre?> - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0 ) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                        <td>$<? if ( $actividad->monto_porcentaje == 0 ) { echo $actividad->precio - $actividad->descuento; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); } ?><td>
                        <? }else{ ?>                   
                        <td>Cuota Mensual <?=$actividad->nombre?></td>
                        <td>$<?=$actividad->precio?><td>
                        <? } ?>
	                </tr>	
	                <?
	               	} 
	               	if($cuota['familiares'] != 0){
		               	foreach ($cuota['familiares'] as $familiar) {
		               		foreach($familiar['actividades']['actividad'] as $actividad){		               		
			               	?>
			               	<tr> 
			               		<? if($actividad->descuento > 0){ ?>
                                    <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ] - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0 ) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                        	    <td>$<? if ( $actividad->monto_porcentaje == 0 ) { echo $actividad->precio - $actividad->descuento; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); } ?><td>
                                    <? }else{ ?>                   
                                    <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ]</td>
                                    <td>$<?=$actividad->precio?><td>
                                <? } ?>			                   
			                </tr>
			               	<?	 	 
		               		}              		            	
		               	}
	               	}
	               	if($cuota['excedente'] >= 1){
	               	?>
	               			<tr>                    
			                    <td>Socio Extra (x<?=$cuota['excedente']?>)</td>
			                    <td>$<?=$cuota['monto_excedente']?><td>
			                </tr>
	               	<?
	               	}	
	               	if($cuota['financiacion']){
	               		foreach ($cuota['financiacion'] as $plan) {	               	
	               	?>
	               			<tr>                    
			                    <td>Financiación de Deuda (<?=$plan->detalle?> - Cuota <?=$plan->actual?>/<?=$plan->cuotas?>)</td>
			                    <td>$<?=round($plan->monto/$plan->cuotas,2)?><td>
			                </tr>
	               	<?
	               		}
	               	}	               	
	                ?>	                                             
	            </tbody>
	            <tfoot>
	                <tr>                        
	                    <th>Total</th>
	                    <th>$<?=$cuota['total']?></th>                        
	                </tr>
	            </tfoot>
	        </table>
			
			<?
		}
		?>
		</div>
	</div>
	<?
	}

?>
<!--<button class="btn btn-success">Generar Cupón Manual</button>-->

<script type="text/javascript">
	$("button#gen_cupon").click(function(){
		$(this).attr("disabled", "disabled");
		$(this).removeClass("btn-warning");
        $(this).addClass("btn-primary");  
        $(this).html("<i class='fa fa-spinner fa-spin'></i> Generando Cupón..."); 
	    var id = $(this).data('id');
	    var monto = $(this).data('monto');
	    $.post("<?=$baseurl?>admin/pagos/cupon/generar",{id: id, monto:monto}).done(function(data){            
	    	get_cupon(data);	
	    })
	    
	})
	$("button#print_cupon").click(function(){
		var id = $(this).data('id');
        window.open('<?=$baseurl?>admin/pagos/cupon/imprimir/'+id,'','width:40, height:20');
        console.log("0asdsa");
    })
</script>
