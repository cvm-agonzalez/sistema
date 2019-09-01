<style>
.sortable-list {
	background-color: #EEE;
	list-style: none;
	margin: 0;
	min-height: 60px;
	padding: 10px;
}
.sortable-item {
	background-color: #FFF;
	border: 1px solid #000;	
	display: block;
	font-weight: bold;
	margin-bottom: 5px;
	padding: 20px 0;
	text-align: center;
}
.baja{
	background-color: #FF495F;
	color:#FFF;
}
</style>
<div class="row" id="lista">
	<div class="col-lg-6">
		<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Actividades Asociadas</h3>
            </div>
            <div class="panel-body">            	
                <ul class="sortable-list" id="actividades_si">
                <? 
                $actividades_asociadas = array();
                foreach ($actividades_asoc as $actividad){                                
                	if($actividad->estado == '1'){
                		$actividades_asociadas[] = $actividad->id; 		
                	}                
                ?>                	
					<li class="sortable-item <? if($actividad->estado == '0'){ echo 'baja'; } ?>" id="asoc_<?=$actividad->asoc_id?>">
						<div class="pull-right cruz" id="cruz_<?=$actividad->asoc_id?>" style="margin-top:-20px;">
							<?
							if($actividad->estado != '0'){ 
							?>
								<strong>Fecha de Alta:</strong> <?=$actividad->alta?><br>
								<a href="#" style="color:green" class="actividad_beca" id="actividad_beca_<?=$actividad->asoc_id?>" data-id="<?=$actividad->asoc_id?>" data-beca="<?=$actividad->descuento?>">									
									Configurar Beca
								</a>
								<a href="#" id="pone_peso" onclick="pone_peso('<?=$actividad->asoc_id?>')" data-beca="<?=$actividad->descuento?>">$</a>
								<a href="#" id="pone_porc" onclick="pone_porc('<?=$actividad->asoc_id?>')" data-beca="<?=$actividad->descuento?>">%</a>
								<a href="#" id="federado" onclick="federado('<?=$actividad->asoc_id?>')" data-id="<?=$actividad->asoc_id?>"><?if($actividad->federado == 0){ echo 'No Federado'; } else { echo 'Federado'; } ?></a>
								<br>
								<a href="#" id="quitar_actividad" onclick="quitar_act('<?=$actividad->asoc_id?>','<?=$actividad->id?>')" data-id="<?=$actividad->asoc_id?>">									
									Dar de Baja <i class="fa fa-times"></i>
								</a>
							<?
							}else{
							?>
								
									<strong>Fecha de Alta:</strong> <?=$actividad->alta?><br>
									<strong>Fecha de Baja:</strong> <?=$actividad->baja?>
								
							<?
							}
							?>
						</div>						
						#<span><?=$actividad->id?> <?=$actividad->nombre?>
						<?
						if($actividad->descuento > 0){
						?>
							&nbsp;<label class="label label-info">Beca <?=$actividad->descuento?><? if ($actividad->monto_porcentaje == 0 ) { echo '$'; } else { echo '%'; } ?></label>
						<? } ?>
						</span>
					</li>				
				<? } ?>
				</ul>
            </div>
        </div>
	</div>
	<div class="col-lg-6">
		<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Actividades NO Asociadas</h3>
            </div>
            <div class="panel-body">
            	Buscar Actividad: <input class="form-control" type="text" id="activ"><br>            	
                <ul class="sortable-list" id="actividades_no">
                <? foreach ($actividades as $actividad){ 
                	if(!in_array($actividad->id, $actividades_asociadas)){
                	?>
					<li class="sortable-item" id="no_asoc_<?=$actividad->id?>">
						<div class="pull-left " style="margin-top:-5px; margin-left:5px;">
							<a class="btn btn-success" href="#" id="asociar_actividad" data-id="<?=$actividad->id?>">
								<i class="fa fa-arrow-left"></i>
							</a>
       					                 <input type="hidden" name="solosoc" id="solosoc" class="form-control" data-id="<?=$actividad->solo_socios?>">
						</div>
						#<span><?=$actividad->id?> <?=$actividad->nombre?>

						</span>
					</li>
				<? 
					}	
				} 
				?>
				</ul>
				
            </div>
        </div>
	</div>
</div>
<script type="text/javascript">
		
		$.extend($.expr[":"], {
			"containsIN": function(elem, i, match, array) {
				return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
			}
		});
		$("#activ").keyup(function(){
            var filter = $(this).val();
            var list = $("#actividades_no");
            if(filter) {
              // this finds all links in a list that contain the input,
              // and hide the ones not containing the input while showing the ones that do
              $(list).find("span:not(:containsIN(" + filter + "))").parent().slideUp();
              $(list).find("span:containsIN(" + filter + ")").parent().slideDown();
            } else {
              $(list).find("li").slideDown();
            }
            return false;
        })

        .keyup( function () {
            // fire the above change event after every letter
            $(this).change();
        });

        /*$("a#quitar_actividad").click(function(){
        	var agree= confirm("Seguro que desea dar de baja esta actividad para este usuario?");
        	if(agree){
	        	var id = $(this).data('id');
	        	$("#asoc_"+id).addClass('baja');
	        	$(this).hide();	        
	        	$.get("<?=$baseurl?>admin/actividades/baja/<?=$sid?>/"+id);
	        }else{
	        	return false;
	        }
        })*/

        $("a#asociar_actividad").click(function(){
        	var agree= confirm("Seguro que desea asociar esta actividad a este usuario?");
        	if(agree){
	        	var id = $(this).data('id');
	        	$("#no_asoc_"+id).slideUp();
	        	var facturar = 'false';
	        	var fecha = new Date();
	        	fecha = fecha.getDate();	        	
	        	var sifed = confirm("Es federado de la actividad p no cobrar el SEGURO?");
			if ( sifed ) {
				var federado = 1;
			} else {
				var federado = 0;
			}
        		if(fecha < 35){
	        		var agree= confirm("Desea que esta actividad sea facturada?");
	        		if(agree){
	        			facturar = 'true';
	        		}
        		}
	        	$.get("<?=$baseurl?>admin/actividades/alta/<?=$sid?>/"+id+"/"+facturar+"/"+federado,function(data){
	        		var actividad = $.parseJSON(data);
	        		var newLi = '<li class="sortable-item" id="asoc_'+actividad.asoc_id+'"><div class="pull-right cruz" id="cruz_'+actividad.asoc_id+'" style="margin-top:-20px;">Fecha de Alta: '+actividad.alta+'<br> <a href="#" style="color:green" class="actividad_beca" id="actividad_beca_'+actividad.asoc_id+'" data-id="'+actividad.asoc_id+'" data-beca="0">Configurar Beca</a><br><a href="#" onclick="quitar_act('+actividad.asoc_id+','+actividad.id+')" id="quitar_actividad" data-id="'+actividad.asoc_id+'">Dar de Baja <i class="fa fa-times"></i></a></div>#<span>'+actividad.id+' '+actividad.nombre+'</span></li>';
	        	$( newLi ).prependTo( $("#actividades_si") ); 
	        	});
        	}else{
        		return false;	
        	}
        })
        function pone_peso(aid){ 
                var agree= confirm("Pone Pesos ");
	        $.get("<?=$baseurl?>admin/actividades/pone_peso/"+aid,function(data){
		});
	}
        function pone_porc(aid){ 
                var agree= confirm("Pone Porcentaje");
	        $.get("<?=$baseurl?>admin/actividades/pone_porc/"+aid,function(data){
		});
	}
        function federado(aid){ 
                var agree= confirm("Cambia Federado"+aid);
	        $.get("<?=$baseurl?>admin/actividades/federado/"+aid,function(data){
		});
	}
        function quitar_act(id,aid){ 
            var agree= confirm("Seguro que desea dar de baja esta actividad para este usuario?");
        	if(agree){

	        	$("#asoc_"+id).addClass('baja');	        	
	        	$("#no_asoc_"+aid).slideDown();
	        	$("#cruz_"+id).html('');    
	        	$.get("<?=$baseurl?>admin/actividades/baja/<?=$sid?>/"+id,function(data){
	        		var actividad = $.parseJSON(data);
	        		console.log(id);
	        		$("#cruz_"+actividad.asoc_id).html('Fecha de Alta: '+actividad.alta+'<br>Fecha de Baja: '+actividad.baja); 
	        	});
	        }else{
	        	return false;
	        }
        }

        
</script>
