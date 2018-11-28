  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-dollar"></span> GENERAR CUPÓN </strong></div>		
	    <div class="panel-body">
	    	<div class="cols-lg-12">
	    		<div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
					<i class="fa fa-users text-large stat-icon"></i>
                </div>
                <form id="gen_cupon_form">
	                <div class="form-group col-lg-5" style="padding-top:20px;">
	                	<div id="r2-data" <? if($socio->Id != 0){ echo 'class="hidden"'; }?>>
	                        <div class="col-sm-7">
	                            <input type="text" name="r2" id="r2" class="form-control" placeholder="Ingrese Nombre, Apellido o DNI del socio">
	                        </div>
	                        <div class="col-sm-4">
	                            <button type="submit" href="#" id="r-buscar" data-id="r2" class="btn btn-primary">Buscar</button> <i id="r2-loading" class="fa fa-spinner fa-spin hidden"></i>
	                        </div>                        
	                    </div>
	                    <div id="r2-result" <? if($socio->Id == 0){ echo 'class="hidden size-h3"'; }else{ echo 'class="size-h3"'; }?>>
	                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                        <? echo $socio->nombre.' '.$socio->apellido.' ('.$socio->dni.')'; ?> <a href="#" onclick="cleear('r2')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>
	                        </div>
	                        <input type="hidden" name="r2-id" id="r2-id" class="form-control" value="<?=$socio->Id?>">
	                    </div>
	                </div>
	            </form>
                <div class="form-group col-lg-6 <? if(!$socio->Id){ echo 'hidden'; } ?>" style="padding-top:20px;" id="accesos_directos">
                    <a id="acceso_editar" class="btn btn-success" href="<?=$baseurl?>admin/socios/editar/<?=$socio->Id?>"><i class="fa fa-user"></i> Editar este socio</a>                        
                    <a id="acceso_actividad" class="btn btn-info" href="<?=$baseurl?>admin/actividades/asociar/<?=$socio->Id?>"><i class="fa fa-table"></i> Asociar Actividad</a>
                    <div class="btn-group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i> Más Acciones...
                        <span class="caret"></span>
                        </button><ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupDrop1">
                            <li><a id="acceso_ver_resumen" href="<?=$baseurl?>admin/socios/resumen/<?=$socio->Id?>">Ver Resumen</a></li>
                            <li><a id="acceso_pago" href="<?=$baseurl?>admin/pagos/registrar/<?=$socio->Id?>">Registrar Pago</a></li>
                            <li><a id="acceso_deuda" href="<?=$baseurl?>admin/pagos/deuda/<?=$socio->Id?>">Financiar Deuda</a></li>
                            <li><a id="acceso_resumen" href="<?=$baseurl?>admin/socios/enviar_resumen/<?=$socio->Id?>">Enviar Resumen</a></li>
                            <li><a id="acceso_debtarj" href="<?=$baseurl?>admin/debtarj/<?=$socio->Id?>">Adherir Debito Tarjeta</a></li>
                                <li><a id="imprimir_carnet" data-id="<?=$socio->Id?>" href="#">Imprimir Carnet</a></li>
                                <li><a id="acceso_financiar" href="<?=$baseurl?>admin/pagos/deuda/<?=$socio->Id?>">Financiar Deuda</a></li>
                                <li><a id="acceso_suspender" href="<?=$baseurl?>admin/socios/suspender/<?=$socio->Id?>">Suspender Socio</a></li>
                                <li><a id="acceso_reinscribir" href="<?=$baseurl?>admin/socios/reinscribir/<?=$socio->Id?>">Reinscribir Socio</a></li>

                        </ul>
                    </div>
				</div>


            <div class="col-lg-12" id="cupon-div" style="display:none;">

            </div>
	      	



	      	<form class="form-horizontal ng-pristine ng-valid hidden" action="<?=$baseurl?>admin/pagos/cupon" method="post">	      		
				<div id="rp-client2"></div>								
					<div class="form-group">
						<div class="col-sm-3"></div>
						<label for="" class="col-sm-2">Cliente</label>
		                <div class="col-sm-3">
			               	<input class="form-control" style="width:200px;">
			            </div>
		            </div>	            
		            <div class="form-group">
		            	<div class="col-sm-3"></div>
			           	<label for="" class="col-sm-2">Monto</label>
			            <div class="col-sm-3">
			               	<input class="form-control" style="width:200px;">
			            </div>
			        </div>	   
			    
			        <div class="form-group">
			        	<div class="col-sm-3"></div>
			           	<label for="" class="col-sm-2">Descripción</label>
			            <div class="col-sm-3">
			               	<textarea class="form-control" style="width:200px;"></textarea>
			            </div>
			        </div>
			        <div class="form-group">
			        	<div class="col-sm-3"></div>
			           	<label for="" class="col-sm-2"></label>
			            <div class="col-sm-3">
			               	<button class="btn-success" onclick="return agregarPago();">Generar</button>
			            </div>	
				    </div>			      
				<div class="clearfix"></div>
			</form>
			</div>
        </div> 



    </div>
  </div>
