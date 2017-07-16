<form class="form-horizontal ng-pristine ng-valid" action="#" method="post" id="load-debtarj-form" enctype="multipart/form-data">
    <br>
                <div class="form-group col-lg-18">
                     <label for="" class="col-sm-9">Marca Tarjeta</label>
                     <div class="col-sm-5">
                       <span class=" ui-select">
                       <select name="marca" id="marca" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                       <? foreach ( $tarjetas as $tarjeta ) { ?>
				<option value="<?=$tarjeta->id?>" ><?=$tarjeta->descripcion?></option>
                        <?}?>
                       </select>
                       </span>
                     </div>
                </div>
                <div class="form-group col-lg-18">
                     <label for="" class="col-sm-9">Fecha Debito</label>
                     <div class="col-sm-5">
    			<input type="text" name="fecha" id="fecha">
                     </div>
                </div>

                    <div class="form-group" id="archivo-form">
                        <label for="archivo" class="col-sm-4">Elegir Archivo</label>
			<div class="clearfix"></div>
                        <div class="col-sm-8">
    				<input type="hidden" name="MAX_FILE_SIZE" value="100000">
			        <input id="userfile" name="userfile" type="file">
                        </div>
			<br>
			<div class="clearfix"></div>
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

