
<div class="page page-table" data-ng-controller="tableCtrl">
	<div class="panel panel-default table-dynamic">
    	<div class="panel-heading">
    		<div class="pull-left">
    			<strong><span class="fa fa-user"></span> Detalles del Socio: <?=$socio->nombre?> <?=$socio->apellido?></strong>
    		</div>
    		<div class="pull-right">
    			<button id="valor_cuota" class="btn btn-danger" ng-click="open()">Cuota Mensual <strong>$ <?=$cuota?></strong></button>
    		</div>
    		<div class="clearfix"></div>
    	</div>
		<div class="panel-body">
			<?
			if($socio->suspendido == 1){
			?>
			<div class="alert alert-danger" style="font-size:16px;">
				<div class="pull-left" style="margin-top:6px;"><i class="fa fa-exclamation-triangle"></i> USUARIO SUSPENDIDO</div>
				<div class="pull-right"><a href="<?=base_url()?>admin/socios/desuspender/<?=$socio->Id?>" class="btn btn-success">Desuspender</a></div>
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
						<td><?=$ingreso->Id?></td>
						<td><?=mostrar_fecha($ingreso->date)?></td>
						<td>
							<div class="" id="socio_desc" data-id="<?=$ingreso->Id?>"><?=$ingreso->descripcion?></div>
							<div class="ver_mas" align="right"><a class="btn btn-primary" href="#" id="ver_mas" data-toggle="0" data-id="<?=$ingreso->Id?>">Ver Más</a></div>
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