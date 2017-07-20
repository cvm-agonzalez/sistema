<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Socio</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/admins/agregar" method="post">
                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre y Apellido</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Tipo y Nro. de Documento</label>
                    <div class="col-sm-1">
                        <span class="ui-select">
                            <select style="margin:0px; width:80px; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option value="dni">DNI</option>
                                <option value="lc">LC</option>
                                <option value="le">LE</option>
                            </select>
                        </span>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control">
                    </div>
                </div>                
                <div class="form-group">
                    <label for="" class="col-sm-2">Nacionalidad</label>
                    <div class="col-sm-10">
                        <input type="mail" class="form-control">
                    </div>
                </div>                    
                <div class="form-group">
                    <label for="" class="col-sm-2">E-Mail</label>
                    <div class="col-sm-10">
                        <input type="mail" class="form-control">
                    </div>
                </div>    

                <div class="form-group" data-ng-controller="DatepickerDemoCtrl">
                    <label for="" class="col-sm-2">Fecha de Nacimiento</label>
                    <div class="col-sm-10">
                    <div class="input-group ui-datepicker">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" 
                               class="form-control"
                               datepicker-popup="{{format}}"
                               ng-model="dt"
                               is-open="opened"
                               min="minDate"
                               max="'2015-06-22'"
                               datepicker-options="dateOptions" 
                               date-disabled="disabled(date, mode)"
                               ng-required="true" 
                               close-text="Cerrar">
                    </div>  
                    </div>
                </div> 
                
                <div class="form-group">
                    <label for="" class="col-sm-2">Domicilio</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>        

                <div class="form-group">
                    <label for="" class="col-sm-2">Localidad</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>       


                <div class="form-group">
                    <label for="" class="col-sm-2">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>       


                <div class="form-group">
                    <label for="" class="col-sm-2">Celular</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>   

                <button id="menorBtn" onclick="return false;" style="margin:20px;">Menor de Edad</button><br>

                <div id="menor" style="display: none;">
                    <div class="form-group">
                        <label for="" class="col-sm-2">Nombre del Padre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="" class="col-sm-2">Teléfono</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="col-sm-2">Nombre de la Madre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control">
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label for="" class="col-sm-2">Teléfono</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2">Categoría de Socio</label>
                    <div class="col-sm-10">
                        <span class=" ui-select">
                            <select style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option>Activos</option>
                                <option>Cadete</option>
                                <option>Grupo Familiar</option>
                                <option>Jubilados</option>
                                <option>Vitalicios</option>
                            </select>
                        </span>
                    </div>
                </div>  
                <div class="form-group">
                    <label for="" class="col-sm-2">Tutor de Grupo Familiar</label>
                    <div class="col-sm-10">
                        <span class=" ui-select">
                            <select style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <option>-- -- -- -- -- --</option>
                                <option>Carlos Alvarez</option>
                                <option>Jose Velez</option>
                                <option>Cristian Castro</option>
                                <option>Miguel Almohada</option>                            
                            </select>
                        </span>
                    </div>
                </div>     
                <div class="form-group">
                    <label for="" class="col-sm-2">Observaciones</label>
                    <div class="col-sm-10">
                        <textarea name="" id="" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Fotos</label>
                    <div class="col-sm-10">
                            <div align="center" class="pull-left" style="background-color:#EEE;">
                                <div id="my_result" style="width:320px; height:240px;"></div>
                                <div style="margin-top:4px;">
                                    <a href="javascript:void(take_snapshot())" style="background-color:#23ae89; padding:5px;color:#FFF;">Capturar Imágen</a>
                                </div>
                            </div>

                            <div id="my_camera" style="width:320px; height:240px; float:right; border:2px solid #23ae89"></div>
                            
                            <div class="clearfix"></div>                        
                    </div>
                </div>       

                <div class="form-group">
                    <label for="" class="col-sm-2">Deuda</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>   

                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </div>
</section>