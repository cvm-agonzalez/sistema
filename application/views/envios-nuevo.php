<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
   <div class="row">
      <div class="col-md-12">
         <section class="panel panel-default">
            <div class="panel-heading">
               <span class="glyphicon glyphicon-th"></span> NUEVO ENVIO               
            </div>
            <div class="panel-body">
               <form id="envios-step1" method="post">
                  <fieldset>
                     <div class="form-group">
                        <label>Asunto</label>
                        <input type="text" id="envio-titulo" class="form-control" required autofocus>
                     </div>
                     <div class="form-group">
                        <label>Grupo</label>
                        <select class="form-control" id="grupo-select">
                           <option value="1">Todos los socios</option>
                           <option value="categorias">Seleccionar por Categoría</option>
                           <option value="actividades">Seleccionar por Actividad</option>
                           <option value="comisiones">Seleccionar por Comisión</option>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-categorias" style="display:none;">
                        <label>Categorías</label>
                        <select class="form-control" id="categorias-select" multiple>
                           <?
                           foreach ($categorias as $cat) {                           
                           ?>
                           <option value="<?=$cat->Id?>"><?=$cat->nomb?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-actividades" style="display:none;">
                        <label>Actividades</label>
                        <select class="form-control" id="actividades-select" multiple>
                           <?
                           foreach ($actividades as $actividad) {                           
                           ?>
                           <option value="<?=$actividad->Id?>"><?=$actividad->nombre?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-comisiones" style="display:none;">
                        <label>Comisiones</label>
                        <select class="form-control" id="comisiones-select" multiple>
                           <?
                           foreach ($profesores as $profesor) {                           
                           ?>
                           <option value="<?=$profesor->Id?>"><?=$profesor->nombre?> <?=$profesor->apellido?></option>                           
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