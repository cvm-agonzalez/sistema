<div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> <? if ($debtarj) { echo "EDITAR DEBITO TARJETA CREDITO"; } else { echo "ADHERIR DEBITO TARJETA CREDITO"; }?> </strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
                            <i class="fa fa-users text-large stat-icon"></i>
                    </div>
                    <form id="cab_debtarj_form">
                        <div class="form-group col-lg-5" style="padding-top:20px;">                                            
                            <div id="r2-data" <? if($socio->id != 0){ echo 'class="hidden"'; }?>>
                                <div class="col-sm-7">
                                    <input type="text" name="r2" id="r2" class="form-control" placeholder="Ingrese Nombre, Apellido o DNI del socio">
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" href="#" id="r-buscar" data-id="r2" class="btn btn-primary">Buscar</button> <i id="r2-loading" class="fa fa-spinner fa-spin hidden"></i>
                                </div>
                            </div>
                            <div id="r2-result" <? if($socio->id == 0){ echo 'class="hidden size-h3"'; }else{ echo 'class="size-h3"'; }?>>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <? echo $socio->nro_socio.'-'.$socio->nombre.' '.$socio->apellido.' ('.$socio->dni.')'; ?> <a href="#" onclick="cleear('r2')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>
                            </div>
                            <input type="hidden" name="r2-id" id="r2-id" class="form-control" value="<?=$socio->id?>">
                        </div> 
                    </form>
                    <div class="form-group col-lg-6 <? if(!$socio->id){ echo 'hidden'; } ?>" style="padding-top:20px;" id="accesos_directos">
                        <a id="acceso_editar" class="btn btn-success" href="<?=$baseurl?>admin/socios/editar/<?=$socio->id?>"><i class="fa fa-user"></i> Editar este socio</a>                        
                        <a id="acceso_actividad" class="btn btn-info" href="<?=$baseurl?>admin/actividades/asociar/<?=$socio->id?>"><i class="fa fa-table"></i> Asociar Actividad</a>
                        <div class="btn-group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i> Más Acciones...
                            <span class="caret"></span>
                            </button><ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupDrop1">
                                <li><a id="acceso_ver_resumen" href="<?=$baseurl?>admin/socios/resumen/<?=$socio->id?>">Ver Resumen</a></li>
                                <li><a id="acceso_pago" href="<?=$baseurl?>admin/pagos/registrar/<?=$socio->id?>">Registrar Pago</a></li>
                                <li><a id="acceso_resumen" href="<?=$baseurl?>admin/socios/enviar_resumen/<?=$socio->id?>">Enviar Resumen</a></li>
                            </ul>
                        </div>
                    </div>                   
                </div>            
                <div class="col-lg-12" id="debtarj-div" style="display:none;">
                    
                </div>
            </div>

        	<div class="panel-body">
				<form class="form-horizontal ng-pristine ng-valid" id="carga_debtarj_form" >

		                <div class="form-group col-lg-6">
                    		<label for="" class="col-sm-3">Marca Tarjeta</label>
                    		<div class="col-sm-9">
                        	<span class=" ui-select">
                            	<select name="id_marca" id="id_marca" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
<? foreach ( $tarjetas as $tarjeta ) { ?>
             <option value="<?=$tarjeta->id?>" data-marca="<?=$tarjeta->descripcion?>" <? if($debtarj && $debtarj->id_marca == $tarjeta->id){echo 'selected';} ?> ><?=$tarjeta->descripcion?></option>
<?}?>
                            	</select>
                        	</span>
                    		</div>
                		</div>

				    <div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Nro de Tarjeta</label>
				            <div class="col-sm-3">
				               	<input class="form-control" name="nro_tarjeta" id="nro_tarjeta" value="<?if ($debtarj) { echo $debtarj->nro_tarjeta; } else { echo ""; } ?>" required>
		                                <input type="hidden" id="id_debito" value="<?if ($debtarj) { echo $debtarj->id; } else { echo '0'; }?>">
				            </div>
				        </div>	
				    </div>
				    <div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Fecha de Adhesion</label>
				            <div class="col-sm-3">
				               	<input class="form-control" name="fecha_adhesion" id="fecha_adhesion" value="<?if ($debtarj) { echo $fecha_db; } else { echo "$fecha"; } ?>" required>
				            </div>
				        </div>	
				    </div>
			        <div>
                        <input type="hidden" name="sid" id="sid" class="form-control" value="<?=$socio->id?>">
			        </div>

				    <div class="clearfix"></div>
					<div align="left" style="width:100%">
				    	<div class="form-group">
				            <div class="col-sm-6">
				               	<button class="btn-success" id="boton-deb" <?if ($debtarj) { echo " data-text='btnmodif' > Modificar"; } else { echo " data-text='btnagregar' > Agregar"; } ?> <i id="reg-cargando" class="fa fa-spin fa-spinner hidden"></i></button>
				            </div>
				        </div>
				    </div>
				</form>
                <?if ($debtarj) { ?>
				    <form id="elimina_debtarj_form" action="<?=$baseurl?>admin/debtarj/eliminar/<?=$debtarj->id?>" >
                            <button class="btn-success" type="submit" > Eliminar </button> 
				    </form>
                <?}?>
			</div>
        </div>

    </div>
</div>

