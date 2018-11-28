<form class="form-horizontal ng-pristine ng-valid" action="#" method="post" id="reg-pago-form">	      					
	<div id="paso2" style="margin-top:30px;">		
		<div class="well">
			<legend>Registrar Pago</legend>
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group">
					<label for="tipo" class="col-sm-4">Debe / Haber</label>
	                <div class="col-sm-8">
	                    <span class="ui-select">
	                        <select id="tipo" style="margin:0px; border:1px solid #cbd5dd; padding:6px 15px 6px 10px;">	                                
	                            <option value="haber">Haber</option>
	                            <option value="debe">Debe</option>
	                        </select>
	                    </span>
	                </div>
	            </div>

                    <div class="form-group" id="aju-ing">
                                        <label for="ajuste-ingreso" class="col-sm-4">Ajuste / Ingreso</label>
                        <div class="col-sm-8">
                            <span class="ui-select">
                                <select id="ajuste-ingreso" style="margin:0px; border:1px solid #cbd5dd; padding:6px 15px 6px 10px;">  
                                    <option value="1">Ajuste</option>
                                    <option value="0">Ingreso Efectivo</option>
                                </select>
                            </span>
                        </div>
                    </div>

	            <div class="form-group hidden" id="cuota-act">
					<label for="actividad-cuota" class="col-sm-4">Cuota / Actividad</label>
	                <div class="col-sm-8">
	                    <span class="ui-select">
	                        <select id="actividad-cuota" style="margin:0px; border:1px solid #cbd5dd; padding:6px 15px 6px 10px;">	                                
	                            <option value="cs">Cuota Social</option>
	                            <optgroup label="Actividad">
	                            	<?
	                            	foreach ($activ_asoc as $actividad) {	                            	
						if ( $actividad->estado == 1 ) {
	                            	?>
	                            			<option value="<?=$actividad->Id?>"><?=$actividad->nombre?></option>
	                            	<?
	                            		}
					}
	                            	?>
	                            </optgroup>
	                        </select>
	                    </span>
	                </div>
	            </div>              


				<div class="form-group">
		           	<label for="monto" class="col-sm-4">Monto</label>
		            <div class="col-sm-8">
		               	<input id="monto" class="form-control" type="number" autocomplete="off" step="any" style="width:200px;" required>
		            </div>
		        </div>	


		        <div class="form-group">
		           	<label for="des" class="col-sm-4">Descripción</label>
		            <div class="col-sm-8">
		               	<textarea id="des" class="form-control" style="width:200px;" required></textarea>
		            </div>
		        </div>	
				<div align="center" style="width:100%">
			    	<div class="form-group">
			           	
			            <div class="col-sm-12">
			               	<button class="btn btn-success">Agregar</button> <i id="reg-cargando" class="fa fa-spinner fa-spin hidden"></i>
			            </div>
			        </div>
			    </div>
	        </div>			        
	    	<div class="clearfix"></div>
		</div>
    </div>

	<div class="clearfix"></div>
</form>

<div id="rp-detalles">
	<div class="panel panel-default table-dynamic">
    	<div class="panel-heading"><strong><span class="fa fa-user"></span> Detalles del Socio: <?=$socio->nombre?> <?=$socio->apellido?></strong></div>
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
			<tbody id="reg-resumen">
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
						<div class="ver_mas" align="right"><a class="btn btn-primary hidden" href="#" id="ver_mas" data-toggle="0" data-id="<?=$ingreso->Id?>">Ver Más</a></div>
					</td>
					<td class="debe">$ <?=$ingreso->debe?></td>
					<td class="haber">$ <?=$ingreso->haber?></td>
					<td class="<? if($ingreso->total < 0){ echo 'debe'; }else{ echo 'haber'; } ?>">$ <?=$ingreso->total?></td>
				</tr>
				<?
				}
				?>											
			</tbody>
			<style type="text/css">
			.socios_desc{max-height: 24px; margin-top: 5px; overflow: hidden; float: left; width: 70%;}
			.ver_mas{float: right; width: 30%;}
			</style>
		</table>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("div#socio_desc").each(function(){

	    var id = $(this).data('id');	 
	    if($(this).height() >= 24){                        
	        $(this).addClass('socios_desc');
	    }else{
	        $("a#ver_mas[data-id="+id+"]").addClass("hidden");
	    }
	})	
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

$("#reg-pago-form").submit(function(){
	var agree = confirm("Seguro que desea registrar este pago?");
	if(!agree){return false;}
	$("#reg-cargando").removeClass('hidden');
	var tipo = $("#tipo").val();	
	if(tipo == 'haber'){
		var ajuing = $("#ajuste-ingreso").val();
	}else{
		var ajuing = false;
	}
	if(tipo == 'debe'){
		var actividad = $("#actividad-cuota").val();
	}else{
		var actividad = false;
	}
	var monto = $("#monto").val();
	var des = $("#des").val();
	$.post("<?=$baseurl?>admin/pagos/registrar/do",{sid: <?=$socio->Id?>, tipo: tipo, monto: monto, des: des, actividad:actividad, ajuing:ajuing})
	.done(function(data){
		var data = $.parseJSON(data);
		var newTr = '<tr><td>'+data.iid+'</td><td>'+data.fecha+'</td><td><div class="socios_desc" id="socio_desc_'+data.iid+'">'+data.descripcion+'</div><div class="ver_mas" align="right"><a class="btn btn-primary" href="#" id="ver_mas" data-toggle="0" data-id="'+data.iid+'">Ver Más</a></div></td><td class="debe">$ '+data.debe+'</td><td class="haber">$ '+data.haber+'</td><td>$ '+data.total+'</td></tr>'
		$("#reg-resumen").prepend(newTr);
		$("#monto").val("");
		$("#des").val("");
		$("#reg-cargando").addClass('hidden');
	})
	//console.log(tipo+monto+des);

	return false;
})

$(document).on('change','#tipo',function(){
	if($(this).val() == 'debe'){
		$("#cuota-act").removeClass('hidden');
		$("#aju-ing").addClass('hidden');
	}else{
		$("#cuota-act").addClass('hidden');
		$("#aju-ing").removeClass('hidden');
	}
})
</script>
