<div id="paso2" style="padding:30px;" class="col-lg-12">
	<div class="panel panel-default">
        <div class="panel-heading"><strong><span class="glyphicon glyphicon-th"></span> Financiar Deuda</strong></div>
        	<div class="panel-body">
				<form id="financiar_form">
					<div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Monto</label>
				            <div class="col-sm-3">
				               	<input class="form-control" id="monto" value="<?=$deuda*(-1)?>" required>
				            </div>
				        </div>            			               
				    </div>
				    <div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Cantidad de Cuotas</label>
				            <div class="col-sm-3">
				               	<input class="form-control" id="cuotas" required>
				            </div>
				        </div>	
				    </div>
				    <div class="col-sm-12">
				        <div class="form-group">
				           	<label for="" class="col-sm-3">Valor de Cuota</label>
				            <div class="col-sm-3">
				               	<input class="form-control" disabled id="valor-cuota">
				            </div>
				        </div>				        
				    </div>
				    <div class="col-sm-12">
				        <div class="form-group">
				           	<label for="" class="col-sm-3">Detalle</label>
				            <div class="col-sm-3">
				               	<textarea class="form-control" id="detalle"></textarea>
				            </div>
				        </div>				        
				    </div>	
				    <div class="clearfix"></div>
					<div align="center" style="width:100%">
				    	<div class="form-group">
				           	
				            <div class="col-sm-6">
				               	<button id="fin_btn" class="btn-success">Financiar <i class="fa fa-spin fa-spinner hidden"></i></button>
				            </div>
				        </div>
				    </div>
				</form>
			</div>
		</div>
	</div>
</div>

<div style="padding:30px;" class="col-lg-12">
	<div class="panel panel-default">
        <div class="panel-heading"><strong><span class="glyphicon glyphicon-th"></span> Planes Anteriores de este Socio</strong></div>
        	<div class="panel-body">
				<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Inicio</th>
                            <th>Monto</th>
                            <th>Cuotas</th>
                            <th>Finaliza</th>
                            <th>Detalle</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?
                    	foreach ($planes as $plan) {                    	
                    	?>
                        <tr>
                            <td><?=$plan->inicio?></td>
                            <td>$ <?=$plan->monto?></td>
                            <td><?=$plan->cuotas?></td>
                            <td><?=$plan->fin?></td>
                            <td><?=$plan->detalle?></td>
                            <td>
                            	<? if($plan->estado == 1){ ?>
                            	<label class="label label-danger">Activo</label>
                            	<? }else if($plan->estado == 2){ ?>
                            	<label class="label label-warning">Cancelado</label>
                            	<? }else if($plan->estado == 2){ ?>
                            	<label class="label label-danger">Finalizado</label>
                            	<? } ?>
                            </td>
                            <td>
                            	<a href="#" id="cancelar_plan" data-id="<?=$plan->Id?>">
                            		<i class="fa fa-times" style="color:#F00;"></i>
                            	</a>
                            </td>
                        </tr>
                        <?
                    	}
                        ?>                                             
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
	$("#cuotas").keyup(function(){
        if($("#cuotas").val() && $("#monto").val()){
            if($.isNumeric($("#cuotas").val()) && $.isNumeric($("#monto").val())){
                if($("#cuotas").val() <= 0){
                    alert("Ingrese un numero mayor que 0");
                    return false;
                }
                    var valor_cuota = $("#monto").val()/$("#cuotas").val();
                    valor_cuota = valor_cuota.toFixed(2);
                        $("#valor-cuota").val(valor_cuota);
            }else{
                alert("Por Favor Ingrese solo Números en los campos Monto y Cantidad de Cuotas");
            }
        }
    })

    $("#financiar_form").submit(function(e){
    	var monto = $("#monto").val();
    	var cuotas = $("#cuotas").val();
    	var detalle = $("#detalle").val();
    	$("#fin_btn").attr('disabled',true);
    	$("#fin_btn").children().removeClass('hidden');
    	$.post('<?=base_url()?>admin/pagos/deuda/financiar',{monto:monto,cuotas:cuotas,detalle:detalle,sid:'<?=$this->uri->segment(5)?>'})
    	.done(function(){
                 $("#deuda-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
                 $.get( "<?=base_url()?>admin/pagos/deuda/get/<?=$this->uri->segment(5)?>" ).done(function(data){                    
                    $("#deuda-div").html(data);
                    $("#deuda-div").slideDown();                  
                })                           
    	})
    	e.preventDefault();
    	return false;

    })

    $("a#cancelar_plan").click(function(){
    	var agree = confirm("Seguro que desea cancelar este Plan?");
    	if(agree){
    		var id = $(this).data('id');
			$.post('<?=base_url()?>admin/pagos/deuda/cancelar_plan',{id:id})
	    	.done(function(){
	                 $("#deuda-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
	                 $.get( "<?=base_url()?>admin/pagos/deuda/get/<?=$this->uri->segment(5)?>" ).done(function(data){                    
	                    $("#deuda-div").html(data);
	                    $("#deuda-div").slideDown();                  
	                })                           
	    	})
    	}else{
    		return false;
    	}
    })
</script>