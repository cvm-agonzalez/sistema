<style type="text/css">
.autocomplete-suggestions { background:url(img/shadow.png) no-repeat bottom right; position:absolute; top:0px; left:0px; margin:6px 0 0 6px; /* IE6 fix: */ _background:none; _margin:1px 0 0 0; } 
.autocomplete-suggestion { padding: 5px; border:1px solid #999; background:#FFF; cursor:default; text-align:left; max-height:350px; overflow:auto; margin:-6px 6px 6px 0px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; } 
.autocomplete-suggestion .selected { background:#F0F0F0; } 
.autocomplete-suggestion div { padding:2px 5px; white-space:nowrap; overflow:hidden; } 
.autocomplete-suggestion strong { font-weight:normal; color:#3399FF; } 
</style>
<div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> EDITAR PAGOS</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
                            <i class="fa fa-users text-large stat-icon"></i>
                    </div>
                    <form id="pagos_deuda_form">
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
                    </form>
                    <div class="form-group col-lg-6 <? if(!$socio->Id){ echo 'hidden'; } ?>" style="padding-top:20px;" id="accesos_directos">
                        <a id="acceso_editar" class="btn btn-success" href="<?=$baseurl?>admin/socios/editar/<?=$socio->Id?>"><i class="fa fa-user"></i> Editar este socio</a>                        
                        <a id="acceso_pago" class="btn btn-info" href="<?=$baseurl?>admin/pagos/registrar/<?=$socio->Id?>"><i class="fa fa-dollar"></i> Registrar Pago</a>
                        <div class="btn-group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i> Más Acciones...
                            <span class="caret"></span>
                            </button><ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupDrop1">
                                <li><a id="acceso_ver_resumen" href="<?=$baseurl?>admin/socios/resumen/<?=$socio->Id?>">Ver Resumen</a></li>
                                <li><a id="acceso_cupon" href="<?=$baseurl?>admin/pagos/cupon/<?=$socio->Id?>">Generar Cupón</a></li>
                                <li><a id="acceso_actividad" href="<?=$baseurl?>admin/actividades/asociar/<?=$socio->Id?>">Asociar Actividad</a> </li>
                                <li><a id="acceso_resumen" href="<?=$baseurl?>admin/socios/enviar_resumen/<?=$socio->Id?>">Enviar Resumen</a></li>
                                <li><a id="acceso_debtarj" href="<?=$baseurl?>admin/debtarj/<?=$socio->Id?>">Adherir Debito Tarjeta</a></li>
                                <li><a id="imprimir_carnet" data-id="<?=$socio->Id?>" href="#">Imprimir Carnet</a></li>
                                <li><a id="acceso_financiar" href="<?=$baseurl?>admin/pagos/deuda/<?=$socio->Id?>">Financiar Deuda</a></li>
                                <li><a id="acceso_suspender" href="<?=$baseurl?>admin/socios/suspender/<?=$socio->Id?>">Suspender Socio</a></li>
                                <li><a id="acceso_reinscribir" href="<?=$baseurl?>admin/socios/reinscribir/<?=$socio->Id?>">Reinscribir Socio</a></li>

                            </ul>
                        </div>
                    </div>                   
                </div>            
                <div class="col-lg-12" id="deuda-div" style="display:none;">
                    
                </div>
            </div>
        </div>
    </div>
</div>
