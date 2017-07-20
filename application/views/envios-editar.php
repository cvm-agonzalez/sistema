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
                           <option value="comisiones" <? if($envio->grupo == 'comisiones'){ echo 'selected'; } ?>>Seleccionar por Comisión</option>
                        </select>
                     </div>
                     <? $grupo_data = json_decode($envio->data); ?>
                     <div class="form-group" id="grupo-categorias" <? if($envio->grupo != 'categorias'){ echo 'style="display:none;"'; } ?>>
                        <label>Categorías</label>
                        <select class="form-control" id="categorias-select" multiple>
                           <?
                           foreach ($categorias as $cat) {                           
                           ?>
                           <option value="<?=$cat->Id?>" <? if($envio->grupo == 'categorias' && in_array($cat->Id, $grupo_data)){ echo 'selected'; } ?>><?=$cat->nomb?></option>                           
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
                           <option value="<?=$actividad->Id?>" <? if($envio->grupo == 'actividades' && in_array($actividad->Id, $grupo_data)){ echo 'selected'; } ?>><?=$actividad->nombre?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-comisiones" <? if($envio->grupo != 'comisiones'){ echo 'style="display:none;"'; } ?>>
                        <label>Comisiones</label>
                        <select class="form-control" id="comisiones-select" multiple>
                           <?
                           foreach ($profesores as $profesor) {                           
                           ?>
                           <option value="<?=$profesor->Id?>" <? if($envio->grupo == 'comisiones' && in_array($profesor->Id, $grupo_data)){ echo 'selected'; } ?>><?=$profesor->nombre?> <?=$profesor->apellido?></option>                           
                           <?
                           }
                           ?>
                        </select>
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