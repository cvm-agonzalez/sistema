<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
   <div class="row">
      <div class="col-md-12">
         <section class="panel panel-default">
            <div class="panel-heading">
               <span class="glyphicon glyphicon-th"></span> EDITAR ENVIO               
            </div>
            <div class="panel-body">
               <form id="envios-step1" method="post">
                  <fieldset>
                     <div class="form-group">
                        <label>Asunto</label>
                        <input type="text" id="envio-titulo" value="<?=$envio->titulo?>" class="form-control" required autofocus>
                     </div>
                     <div class="form-group">
                        <label>Grupo</label>
                        <select class="form-control" id="grupo-select">
                           <option value="1">Todos los socios</option>
                           <option value="categorias" <? if($envio->grupo == 'categorias'){ echo 'selected'; } ?>>Seleccionar por Categoría</option>
                           <option value="actividades" <? if($envio->grupo == 'actividades'){ echo 'selected'; } ?>>Seleccionar por Actividad</option>
                           <option value="socconactiv" <? if($envio->grupo == 'socconactiv'){ echo 'selected'; } ?>>Seleccionar Socios con Actividad</option>
                           <option value="socsinactiv" <? if($envio->grupo == 'socsinactiv'){ echo 'selected'; } ?>>Seleccionar Socios sin Actividad</option>
                           <option value="soccomision" <? if($envio->grupo == 'soccomision'){ echo 'selected'; } ?>>Socios por Comisión</option>
                           <option value="titcomision" <? if($envio->grupo == 'titcomision'){ echo 'selected'; } ?>>Integrantes de la Comisión</option>
                        </select>

                     </div>
                     <? $grupo_data = json_decode($envio->data); ?>
                     <div class="form-group" id="grupo-categorias" <? if($envio->grupo != 'categorias'){ echo 'style="display:none;"'; } ?>>
                        <label>Categorías</label>
                        <select class="form-control" id="categorias-select" multiple>
                           <?
                           foreach ($categorias as $cat) {                           
                           ?>
                           <option value="<?=$cat->id?>" <? if($envio->grupo == 'categorias' && in_array($cat->id, $grupo_data)){ echo 'selected'; } ?>><?=$cat->nombre?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-actividades" <? if($envio->grupo != 'actividades'){ echo 'style="display:none;"'; } ?>>
                        <label>Actividades</label>
                        <select class="form-control" id="actividades-select" multiple>
                           <?
                           foreach ($actividades as $actividad) {                           
                           ?>
                           <option value="<?=$actividad->id?>" <? if($envio->grupo == 'actividades' && in_array($actividad->id, $grupo_data)){ echo 'selected'; } ?>><?=$actividad->nombre?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-comisiones" <? if($envio->grupo != 'comisiones'){ echo 'style="display:none;"'; } ?>>
                        <label>Comisiones</label>
                        <select class="form-control" id="comisiones-select" multiple>
                           <?
                           foreach ($comisiones as $comision) {                           
                           ?>
                           <option value="<?=$comision->id?>" <? if( ( $envio->grupo == 'soccomision' || $envio->grupo == 'titcomision' ) && in_array($comision->id, $grupo_data)){ echo 'selected'; } ?>><?=$comsion->descripcion?> </option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                <div class="form-group">
                                <span class="btn btn-success fileinput-button">
                                    <span><i class="fa fa-cloud-upload"></i> Subir Imágen</span>
                                    <input id="fileupload_mail" type="file" name="files[]" multiple>
                                </span>
                </div>

                     <div align="right">
                        <button type="submit" id="envios-continuar" class="btn btn-success btn-block">Continuar <i class="fa fa-arrow-right"></i></button>
                     </div>
                  </fieldset>
               </form>
               <div id="step2">

               </div>
            </div>
         </div>
      </div>
   </div>
</div>
