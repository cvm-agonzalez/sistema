  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> ASOCIAR ACTIVIDADES</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
                            <i class="fa fa-users text-large stat-icon"></i>
                    </div>
                    <div class="form-group col-lg-5" style="padding-top:20px;">
                       <form id="activ_asoc_form">
                            <div id="r2-data" <? if($socio->id != 0){ echo 'class="hidden"'; }?>>
                                <div class="col-sm-7">
                                    <input type="text" name="r2" id="r2" class="form-control" placeholder="Ingrese Nombre, Apellido o DNI del socio">
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" id="r-buscar" data-id="r2" class="btn btn-primary">Buscar</button> <i id="r2-loading" class="fa fa-spinner fa-spin hidden"></i>
                                </div>
                            </div>
                            <div id="r2-result" <? if($socio->id == 0){ echo 'class="hidden size-h3"'; }else{ echo 'class="size-h3"'; }?>>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <? echo $socio->nombre.' '.$socio->apellido.' ('.$socio->dni.')'; ?> <a href="#" onclick="cleear('r2')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>
                            </div>
                            <input type="hidden" name="r2-id" id="r2-id" class="form-control" value="<?=$socio->id?>">
                        </form>
                    </div> 
                    <div class="form-group col-lg-6 <? if(!$socio->id){ echo 'hidden'; } ?>" style="padding-top:20px;" id="accesos_directos">
                        <a id="acceso_editar" class="btn btn-success" href="<?=$baseurl?>admin/socios/editar/<?=$socio->id?>"><i class="fa fa-user"></i> Editar este socio</a>                        
                        <a id="acceso_cupon" class="btn btn-info" href="<?=$baseurl?>admin/pagos/cupon/<?=$socio->id?>"><i class="fa fa-dollar"></i> Generar Cupón</a>
                        <div class="btn-group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i> Más Acciones...
                            <span class="caret"></span>
                            </button><ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupDrop1">
                                <li><a id="acceso_ver_resumen" href="<?=$baseurl?>admin/socios/resumen/<?=$socio->id?>">Ver Resumen</a></li>
                                <li><a id="acceso_pago" href="<?=$baseurl?>admin/pagos/registrar/<?=$socio->id?>">Registrar Pago</a></li>
                                <li><a id="acceso_deuda" href="<?=$baseurl?>admin/pagos/deuda/<?=$socio->id?>">Financiar Deuda</a></li>
                                <li><a id="acceso_resumen" href="<?=$baseurl?>admin/socios/enviar_resumen/<?=$socio->id?>">Enviar Resumen</a></li>
                            </ul>
                        </div>
                    </div>                   
                </div>            
                <div class="col-lg-12" id="asociar-div" style="display:none;">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
            
    <div class="panel-body" data-ng-controller="ModalDemoCtrl">
        <script type="text/ng-template" id="myModalContent.html">                    
            <div class="modal-header">
                <h3>Configurar Beca</h3>
            </div>
            <form class="form-horizontal" data-id="" id="form-beca" action="#" method="post">
                <div class="modal-body">
                    <div class="form-group col-lg-12">
                        <label for="" class="col-sm-3">Porcentaje </label>
                        <label for="" class="col-sm-3">Becado</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="number" step="any" min="0" max="100000" id="beca-porcien" name="beca-porcien" class="form-control" required>                                
                                <input type="hidden" id="beca-id" name="beca-id" class="form-control" required>                                
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>                    
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <button class="btn btn-alert" type="button" id="modal_close" ng-click="cancel()">Cancel</button>
                </div>
            </form>
        </script>
        <button class="btn btn-primary hidden" id="modal_open" ng-click="open()">vm</button>
    </div>
<!-- end Modal -->

<script type="text/javascript">
    $(document).on("click",".actividad_beca",function(){
            var beca = $(this).data('beca');
            var id = $(this).data('id');
            angular.element("#modal_open").triggerHandler('click');
            $("#beca-porcien").val(beca);
            $("#beca-id").val(id);
            return false;
        })
        $(document).on('submit','#form-beca',function(e){
            var id = $("#beca-id").val();
            var beca = $("#beca-porcien").val();
            console.log(id,beca);
            e.preventDefault();
            $.post("<?=base_url()?>admin/actividades/becar",{id:id,beca:beca})
            .done(function(){
                angular.element("#modal_close").triggerHandler('click');
                $("#actividad_beca_"+id).data('beca',beca);
            })
        })
</script>
