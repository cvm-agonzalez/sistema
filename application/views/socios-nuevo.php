<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Socio</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/<?=$action?>" method="post">
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Nombre</label>
                    <div class="col-sm-9">
                        <input type="text" id="nombre" name="nombre" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Apellido</label>
                    <div class="col-sm-9">
                        <input type="text" id="apellido" name="apellido" class="form-control">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">DNI</label>                    
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="dni" required>
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
                            <input type="date" id="fechan"
                                   class="form-control"                                   
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
                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Localidad</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="localidad" value="Bahía Blanca" name="localidad">  
                    </div>
                </div>

                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Nacionalidad</label>
                    <div class="col-sm-9">
                        <input type="mail" class="form-control" id="nacionalidad" name="Nacionalidad" value="Argentina">
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
                        <label for="" class="col-sm-3">DNI Contacto #1</label>
                        <div id="r1-data">
                            <div class="col-sm-5">
                                <input type="text" name="r1" id="r1" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <a href="#" id="r-buscar" data-id="r1" class="btn btn-primary">Buscar</a> <i id="r1-loading" class="fa fa-spinner fa-spin hidden"></i>
                            </div>
                        </div>
                        <div id="r1-result" class="hidden"></div>
                        <input type="hidden" name="r1-id" id="r1-id" class="form-control">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="" class="col-sm-3">DNI Contacto #2</label>
                        <div id="r2-data">
                            <div class="col-sm-5">
                                <input type="text" name="r2" id="r2" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <a href="#" id="r-buscar" data-id="r2" class="btn btn-primary">Buscar</a> <i id="r2-loading" class="fa fa-spinner fa-spin hidden"></i>
                            </div>
                        </div>
                        <div id="r2-result" class="hidden"></div>
                        <input type="hidden" name="r2-id" id="r2-id" class="form-control">
                    </div>                    
                </div>

                

                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Tutor de Grupo Familiar</label>
                        <div id="r3-data">
                            <div class="col-sm-5">
                                <input type="text" name="r3" id="r3" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <a href="#" id="r-buscar" data-id="r3" class="btn btn-primary">Buscar</a> <i id="r3-loading" class="fa fa-spinner fa-spin hidden"></i>
                            </div>
                        </div>
                        <div id="r3-result" class="hidden"></div>
                        <input type="hidden" name="r3-id" id="r3-id" class="form-control">
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Categoría de Socio</label>
                    <div class="col-sm-9">
                        <span class=" ui-select">
                            <select id="s_cate" name="categoria" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <?
                                foreach ($categorias as $cat) {                                                           
                                ?>
                                    <option value="<?=$cat->Id?>" data-precio="<?=$cat->precio?>"><?=$cat->nomb?></option>
                                <?
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                </div>  
                
                                  
                <div class="form-group col-lg-12">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label for="" class="col-lg-3 col-md-3 col-xs-12">Foto</label>
                        <div class="col-lg-9 col-md-9 col-xs-12" align="center">
                            <div id="my_camera" style="width:320px; height:240px; float:left; border:2px solid #23ae89"></div> 
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12" align="center">
                        <div class="col-sm-12">
                            <div align="center" class="pull-left" style="background-color:#EEE;">
                                <div id="my_result" style="width:320px; height:220px;"></div>
                                <div style="margin-top:4px;">
                                    <a href="javascript:void(take_snapshot())" class="btn btn-success" style="background-color:#23ae89; color:#FFF;"><i class="fa fa-camera"></i> Capturar Imágen</a>
                                    <span class="btn btn-success fileinput-button">                              
                                        <span><i class="fa fa-cloud-upload"></i> Subir Imágen</span>
                                        <!-- The file input field used as target for the file upload widget -->
                                        <input id="fileupload" type="file" name="files[]" multiple>
                                    </span>
                                    <div id="progress" class="progress" style="diplay:none;">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>
                                </div>
                            </div>                                                                            
                        </div>
                    </div>      
                </div>           
                <div class="clearfix"></div>   
                <div class="form-group col-lg-6" data-ng-controller="DatepickerDemoCtrl">
                    <label for="" class="col-sm-3">Fecha de Ingreso</label>
                    <div class="col-sm-9">
                        <div class="input-group ui-datepicker">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="date"
                                   class="form-control"                                   
                                   close-text="Cerrar" name="alta" value="">
                        </div>  
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Deuda</label>
                    <div class="col-sm-9">
                        <input type="text" name="deuda" class="form-control">
                    </div>
                </div> <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Socio #</label>
                    <div class="col-sm-9">
                        <input type="number" name="socio_n" class="form-control" value="">                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <p>
                        <li>El número de socio personalizado deberá ser menor a 28.852.</li>
                        <li>Si no desea utilizar un número personalizado deje este campo en blanco</li>
                    </p>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Descuento $</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="number" name="descuento" step="any" max="100" min="0" required class="form-control" id="descuento" value="0.00">                            
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <div class="alert alert-info">Este socio abonará $ <span id="a_pagar"></span> de cuota social.</div>
                </div>
                <div class="clearfix"></div>
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

<!-- Modal -->
            
                <div class="panel-body" data-ng-controller="ModalDemoCtrl">
                    <script type="text/ng-template" id="myModalContent.html">                    
                        <div class="modal-header">
                            <h3>Agregar Socio</h3>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" data-id="" id="form-tutor" action="#" method="post">
                                <div class="form-group col-lg-12">
                                    <label for="" class="col-sm-3">DNI</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="tutor-dni" name="tutor-dni" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="" class="col-sm-3">Nombre</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="tutor-nombre" name="tutor-nombre" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="" class="col-sm-3">Apellido</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="tutor-apellido" name="tutor-apellido" class="form-control">
                                    </div>
                                </div>                                
                                <div class="form-group col-lg-12">
                                    <label for="" class="col-sm-3">Teléfono</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="tutor-telefono" name="tutor-telefono" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="" class="col-sm-3">Email</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="tutor-email" name="tutor-email" class="form-control">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" onclick="submit_tutor();">Agregar</button>
                            <button class="btn btn-alert" id="modal_close" ng-click="cancel()">Cancel</button>
                        </div>
                    </script>
                    <button class="btn btn-primary hidden" id="modal_open" ng-click="open()">vm</button>
                </div>
            <!-- end Modal -->