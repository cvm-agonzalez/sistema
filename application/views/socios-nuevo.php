<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Socio</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/<?=$action?>" method="post">

                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Socio #</label>
                    <div class="col-sm-9">
                        <input type="number" name="nro_socio" id="nro_socio" class="form-control" value="<?=$prox_nsocio?>">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">DNI</label>
                    <div class="col-sm-9">
                        <input type="number" id="dni" class="form-control" name="dni" required>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                        <div class="col-sm-3">
                                <a href="#" id="valid_dni" class="btn btn-primary">Validar</a>
                        </div>
                        <div class="col-sm-3">
                                <div id="result_dni" class="hidden">
                                        <label for="" >DNI existente en otro socio</label>
                                </div>
                                <div id="result_nsoc" class="hidden">
                                        <label for="" > Numero de socio existente en otro socio</label>
                                </div>
                                <div id="result_OK" class="hidden">
                                        <label for="" > <b>Datos de DNI y nro socio OK</b></label>
                                </div>
                        </div>
                        <div class="col-sm-6">
                                <label for="" > El número de socio personalizado respeta numeracion anterior al sistema y NO PUEDE REPETIRSE</label>
                                <label for="" > El sistema sugiere un numero consecutivo en base al ultimo usado</label>
                        </div>
                </div>

		<div id="resto_datos" class="hidden">
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
                

                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Tutor de Grupo Familiar</label>
                        <div id="tutor_dni-data">
                            <div class="col-sm-5">
                                <input type="number" name="tutor_dni" id="tutor_dni" value='0' class="form-control">
                                <input type="hidden" name="tutor_sid" id="tutor_sid" value='0' class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <a href="#" id="r-buscar" data-id="tutor_dni" class="btn btn-primary">Buscar</a> <i id="tutor_dni-loading" class="fa fa-spinner fa-spin hidden"></i>
                            </div>
                        </div>
                        <div id="tutor_dni-result" >
                        </div>

                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Categoría de Socio</label>
                    <div class="col-sm-9">
                        <span class=" ui-select">
                            <select id="s_cate" name="categoria" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                <?
                                foreach ($categorias as $cat) {                                                           
                                ?>
                                    <option value="<?=$cat->id?>" data-precio="<?=$cat->precio?>"><?=$cat->nombre?></option>
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
				   <?$ingreso=date('Y-m-d')?>
                                   close-text="Cerrar" name="alta" value="<?=$ingreso?>">
                        </div>  
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="" class="col-sm-3">Deuda</label>
                    <div class="col-sm-9">
                        <input type="text" name="deuda" class="form-control">
                    </div>
                </div> <div class="clearfix"></div>

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
                <button type="submit" id="save_btn" class="btn btn-success">Guardar</button>

		</div>
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
