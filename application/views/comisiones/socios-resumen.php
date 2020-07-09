
    <!-- Modal -->            
        <div class="panel-body" data-ng-controller="ModalDemoCtrl">

            <script type="text/ng-template" id="myModalContent.html">                    
                <div class="modal-header">
                    <h3>Detalle de Cuota</h3>
                </div>
                <div class="modal-body" id="detalle_de_cuota">
                    <div class="col-sm-6">                    
                            <h3><strong>Titular:</strong> <?=$cuota['titular']?> 
                                <?
                                if($cuota['categoria'] == 'Grupo Familiar'){
                                ?>
                                <a target="_self" href="<?=base_url()?>admin/socios/resumen/<?=$cuota['tid']?>" class="btn btn-success"> Ver Resumen </a>
                                <? } ?>
                            </h3>
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
                                    <td>Cuota Mensual <?=$actividad->nombre?> - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                                    <td>$<?if ( $actividad->monto_porcentaje == 0 ) { if ( $actividad->precio == 0 ) { echo '0.00';} else { echo $actividad->precio - $actividad->descuento; } ; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); }?><td>
                                    <? }else{ ?>                   
                                    <td>Cuota Mensual <?=$actividad->nombre?></td>
                                    <td>$<?=$actividad->precio?><td>
                                    <? } ?>
				    <? if ( $actividad->seguro > 0 && $actividad->federado == 0 ) { ?>
                                    	<td>Seguro  <?=$actividad->nombre?></td>
                                    	<td>$<?=$actividad->seguro?><td>
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
                                                <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ] - $ <?=$actividad->precio?> <label class="label label-info"><?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0) { echo '$ BECADOS'; } else { echo '% BECADO'; } ?></label></td>
                                                <td>$<?if ( $actividad->monto_porcentaje == 0 ) { if ( $actividad->precio == 0 ) { echo '0.00'; } else { echo $actividad->precio - $actividad->descuento; } ; } else { echo $actividad->precio - ($actividad->precio * $actividad->descuento / 100); }?><td>
                                                <? }else{ ?>                   
                                                <td>Cuota Mensual <?=$actividad->nombre?> [<?=$familiar['datos']->nombre.' '.$familiar['datos']->apellido?> ]</td>
                                                <td>$<?=$actividad->precio?><td>
                                            <? } ?>     
	                                    <? if ( $actividad->seguro > 0 && $actividad->federado == 0 ) { ?>
                                        	<td>Seguro  <?=$actividad->nombre?></td>
                                        	<td>$<?=$actividad->seguro?><td>
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
                    </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?=base_url()?>admin/pagos/cupon/<?=$this->uri->segment(4)?>" class="btn btn-primary">Generar Cupón</a>
                    <button class="btn btn-alert" id="modal_close" ng-click="cancel()">Cerrar</button>
                </div>
            </script>
            <button class="btn btn-primary hidden" id="modal_open" ng-click="open()">vm</button>

        </div>
    <!-- end Modal -->
<div class="page page-table" data-ng-controller="tableCtrl">
	<div class="panel panel-default table-dynamic">
    	<div class="panel-heading">
    		<div class="pull-left">
    			<strong><span class="fa fa-user"></span> Detalles del Socio: <?=$socio->nombre?> <?=$socio->apellido?></strong>
    		</div>
    		<div class="pull-right">
    			<button id="valor_cuota" class="btn btn-danger" ng-click="open()">Cuota Mensual <strong>$ <?=$cuota['total']?></strong></button>
    		</div>
    		<div class="clearfix"></div>
    	</div>
		<div class="panel-body">
			<?
			if($socio->suspendido == 1){
			?>
			<div class="alert alert-danger" style="font-size:16px;">
				<div class="pull-left" style="margin-top:6px;"><i class="fa fa-exclamation-triangle"></i> USUARIO SUSPENDIDO</div>
				<div class="pull-right"><a href="<?=base_url()?>admin/socios/desuspender/<?=$socio->id?>" class="btn btn-success">Desuspender</a></div>
				<div class="clearfix"></div>
			</div>
			<?
			}
			?>
			<table class="table table-bordered table-striped table-responsive table-resumen">
				<thead>
					<tr>
						<th><div class="th-resumen"># ID</div></th>
						<th><div class="th-resumen">Fecha</div></th>
						<th><div class="th-resumen">Descripción</div></th>
						<th><div class="th-resumen">Debe</div></th>
						<th><div class="th-resumen">Haber</div></th>
						<th><div class="th-resumen">Total</div></th>
					</tr>
				</thead>
				<tbody>
					<?
					function mostrar_fecha($fecha)
				    {
				        $fecha = explode('-', $fecha);
				        $fecha[2] = explode(' ',$fecha[2]);
				        return $fecha[2][0].'/'.$fecha[1].'/'.$fecha[0];
				    }	
					foreach ($facturacion as $ingreso) {				
					?>
					<tr>
						<td><?=$ingreso->id?></td>
						<td><?=mostrar_fecha($ingreso->date)?></td>
						<td>
							<div class="" id="socio_desc" data-id="<?=$ingreso->id?>"><?=$ingreso->descripcion?></div>
							<div class="ver_mas" align="right"><a class="btn btn-primary" href="#" id="ver_mas" data-toggle="0" data-id="<?=$ingreso->id?>">Ver Más</a></div>
						</td>
						<td class="debe">$ <?=$ingreso->debe?></td>
						<td class="haber">$ <?=$ingreso->haber?></td>
						<td class="<? if($ingreso->total < 0){ echo 'debe'; }else{ echo 'haber'; } ?>">$ <?=$ingreso->total?></td>
					</tr>
					<?
					}
					?>											
				</tbody>			
			</table>
		</div>
	</div>
	<style type="text/css">
					.socios_desc{max-height: 24px; margin-top: 5px; overflow: hidden; float: left; width: 70%;}
					.ver_mas{float: right; width: 30%;}
				</style>
	<script>
   
                $("div#socio_desc").each(function(){

                    var id = $(this).data('id');
                    console.log($(this).height());
                    if($(this).height() >= 24){                        
                        $(this).addClass('socios_desc');                        
                    }else{                       
                        $("a#ver_mas[data-id="+id+"]").addClass("hidden");
                    }
                })
                $("a#ver_mas").click(function(){
                    var id = $(this).data('id');
                    var toggle = $(this).data('toggle');
                    if(toggle == '0'){     
                        $("div[data-id="+id+"]").removeClass('socios_desc');
                        $(this).data('toggle','1');
                        $(this).text('Ver Menos');
                    }else{
                        $("div[data-id="+id+"]").addClass('socios_desc');
                        $(this).data('toggle','0');
                        $(this).text('Ver Más'); 
                    }
                })
         
	</script>

</div>
