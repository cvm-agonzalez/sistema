<form class="form-horizontal ng-pristine ng-valid" action="#" method="post" id="load-asoc-activ-form" enctype="multipart/form-data">
    <br>
                <div class="form-group col-lg-18">
                     <label for="" class="col-sm-9">Actividad</label>
                     <div class="col-sm-5">
                       <span class=" ui-select">
                       <select name="actividad" id="actividad" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                       <? foreach ( $actividades as $actividad ) { ?>
				<option value="<?=$actividad->Id?>" ><?=$actividad->nombre?></option>
                        <?}?>
                       </select>
                       </span>
                     </div>
                </div>

                <div class="form-group col-lg-18">
                     <label for="fuente" class="col-sm-9">Fuente de Datos</label>
                     <div class="col-sm-5">
                       <span class="ui-select">
                       <select id="fuente" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
				<option value="" > Seleccionar Fuente de Datos</option>
				<option value="txt" > Archivo </option>
				<option value="bd" > Base de Datos</option>
                       </select>
                       </span>
                     </div>
                </div>

                    <div class="form-group hidden" id="archivo-form">
                        <label for="archivo" class="col-sm-4">Elegir Archivo</label>
			<div class="clearfix"></div>
                        <div class="col-sm-8">
    				<input type="hidden" name="MAX_FILE_SIZE" value="100000">
			        <input id="userfile" name="userfile" type="file">
                        </div>
			<br>
			<div class="clearfix"></div>
			<br>
                      	<div class="col-sm-5">
                       		<span class="ui-select">
                       			<select id="dato1col" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                                		<option value="" > Clave Datos 1 columna </option>
                                		<option value="dni" > Documento </option>
                                		<option value="sid" > Id de Asociado </option>
                       			</select>
                       		</span>
                     	</div>
                    </div>

		<div class="clearfix"></div>

		<br>
		<br>

                <div class="form-group col-lg-18">
                     <div class="col-sm-5">
                                        <button class="btn btn-success">Procesar</button> <i id="reg-cargando" class="fa fa-spinner fa-spin hidden"></i>

		     </div>
		</div>


</form> 

