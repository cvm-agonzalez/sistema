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
                        <label>Activos/Suspendidos</label>
                        <select class="form-control" id="activ-select" >
                           <option value=" ">Elegir</option>
                           <option value="1">Solo Activos</option>
                           <option value="2">Activos mas Suspendidos</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Grupo</label>
                        <select class="form-control" id="grupo-select">
                           <option value="1">Todos los socios</option>
                           <option value="categorias">Seleccionar por Categoría</option>
                           <option value="actividades">Seleccionar por Actividad</option>
                           <option value="socconactiv">Seleccionar Socios con Actividad</option>
                           <option value="socsinactiv">Seleccionar Socios sin Actividad</option>
                           <option value="soccomision">Socios por Comisión</option>
                           <option value="titcomision">Integrantes de la Comisión</option>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-categorias" style="display:none;">
                        <label>Categorías</label>
                        <select class="form-control" id="categorias-select" multiple>
                           <?
                           foreach ($categorias as $cat) {                           
                           ?>
                           <option value="<?=$cat->id?>"><?=$cat->nombre?></option>                           
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
                           <option value="<?=$actividad->id?>"><?=$actividad->nombre?></option>                           
                           <?
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group" id="grupo-comisiones" style="display:none;">
                        <label>Comisiones</label>
                        <select class="form-control" id="comisiones-select" multiple>
                           <?
                           foreach ($comisiones as $comision) {                           
                           ?>
                           <option value="<?=$comision->id?>"><?=$comision->descripcion?></option>                           
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
