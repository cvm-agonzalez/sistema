<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Socio</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/<?=$action?>" method="post">
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Nombre</label>
                    <div class="col-sm-9">
                        <input type="text" id="nombre" value="<?$socio->nombre?>" name="nombre" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Apellido</label>
                    <div class="col-sm-9">
                        <input type="text" id="apellido" value="<?=$socio->apellido?>" name="apellido" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Documento</label>
                    <div class="col-sm-2">
                        <span class="ui-select">
                            <select name="tipo_dni" style="margin:0px; width:80px; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option value="dni">DNI</option>
                                <option value="lc">LC</option>
                                <option value="le">LE</option>
                            </select>
                        </span>
                    </div>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="dni">
                    </div>
                </div>                
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Nacionalidad</label>
                    <div class="col-sm-9">
                        <input type="mail" class="form-control" name="Nacionalidad">
                    </div>
                </div>                    
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">E-Mail</label>
                    <div class="col-sm-9">
                        <input type="mail" class="form-control" name="mail">
                    </div>
                </div>    

                <div class="form-group col-lg-6" data-ng-controller="DatepickerDemoCtrl">
                    <label for="" class="col-sm-3">Fecha de Nacimiento</label>
                    <div class="col-sm-9">
                    <div class="input-group ui-datepicker">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="fechan"
                               class="form-control"
                               datepicker-popup="dd-MM-yyyy"
                               ng-model="dt"
                               is-open="opened"
                               min="minDate"
                               max="'2015-06-22'"
                               datepicker-options="dateOptions"                               
                               ng-required="true" 
                               close-text="Cerrar" name="nacimiento">
                    </div>  
                    </div>
                </div> 
                
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Domicilio</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="domicilio" name="domicilio">
                    </div>
                </div>        

                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Localidad</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="localidad">
                    </div>
                </div>       


                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Teléfono</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="telefono">
                    </div>
                </div>       
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Celular</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="celular">
                    </div>
                </div>               
                <div class="clearfix"></div>
                <div id="menor" style="display: none;">
                    <div class="form-group col-lg-6">
                        <label for="" class="col-sm-3">Responsable #1</label>
                        <div class="col-sm-9">
                            <input type="text" name="r1" class="form-control">
                        </div>
                    </div>
                     <div class="form-group col-lg-6">
                        <label for="" class="col-sm-3">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="text" name="r1_tel" class="form-control">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-6">
                        <label for="" class="col-sm-3">Responsable #2</label>
                        <div class="col-sm-9">
                            <input type="text" name="r2" class="form-control">
                        </div>
                    </div>                    
                    <div class="form-group col-lg-6">
                        <label for="" class="col-sm-3">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="text"  name="r2_tel" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Categoría de Socio</label>
                    <div class="col-sm-9">
                        <span class=" ui-select">
                            <select name="categoria" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option value="activo">Activos</option>
                                <option value="cadete">Cadete</option>
                                <option value="grupo">Grupo Familiar</option>
                                <option value="jubilado">Jubilados</option>
                                <option value="vitalicio">Vitalicios</option>
                            </select>
                        </span>
                    </div>
                </div>  
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Tutor de Grupo Familiar</label>
                    <div class="col-sm-9">
                        <span class=" ui-select">
                            <select name="tutor" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option value="">-- -- -- -- -- --</option>
                                <option value="">Carlos Alvarez</option>
                                <option value="">Jose Velez</option>
                                <option value="">Cristian Castro</option>
                                <option value="">Miguel Almohada</option>                            
                            </select>
                        </span>
                    </div>
                </div>
                <hr>                     
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Fotos</label>
                    <div class="col-sm-9" align="center">
                        <div id="my_camera" style="width:320px; height:240px; float:right; border:2px solid #23ae89"></div> 
                    </div>
                </div>
                <div class="form-group col-lg-6" align="center">
                    <div class="col-sm-12">
                        <div align="center" class="pull-left" style="background-color:#EEE;">
                            <div id="my_result" style="width:320px; height:220px;"></div>
                            <div style="margin-top:4px;">
                                <a href="javascript:void(take_snapshot())" style="background-color:#23ae89; padding:5px;color:#FFF;">Capturar Imágen</a>
                            </div>
                        </div>                                                                            
                    </div>
                </div> 
                <hr>      
                <div class="clearfix"></div>   
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Deuda</label>
                    <div class="col-sm-9">
                        <input type="text" name="deuda" class="form-control">
                    </div>
                </div> <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Observaciones</label>
                    <div class="col-sm-9">
                        <textarea name="observaciones" id="" class="form-control" rows="4"></textarea>
                    </div>
                </div>  
                <div class="clearfix"></div>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </div>
</section>