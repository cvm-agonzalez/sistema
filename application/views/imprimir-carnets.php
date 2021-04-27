<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Imprimir Carnets </strong></div>
        <div class="panel-body">
                <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/carnets-do" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Categoria</label>
                    <div class="col-sm-10">
                        <select name="categoria" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="0" > TODAS </option>
			<? foreach ( $categorias as $categoria ) {  ?>
                        	<option value="<?=$categoria->id?>" <?if ($cat_sel) { if ( $cat_sel == $categoria->id ) { echo 'selected';} } ?>> <?=$categoria->nombre?> </option>
			<? } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Con Foto</label>
                    <div class="col-sm-10">
                        <select name="foto" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="-1" <? if ( $foto_sel ) { if ( $foto_sel== '-1' ) { echo 'selected'; } } ?>> TODOS </option>
                        <option value="SI" <? if ( $foto_sel ) { if ( $foto_sel == 'SI' ) { echo 'selected'; } } ?>> CON FOTO </option>
                        <option value="NO" <? if ( $foto_sel ) { if ( $foto_sel == 'NO' ) { echo 'selected'; } } ?>> SIN FOTO </option>
                        </select>
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Actividad</label>
                    <div class="col-sm-10">
                        <select name="actividad" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="-1" <? if ( $act_sel ) { if ( $act_sel == -1 ) { echo 'selected'; } } ?>> SIN ACTIVIDAD </option>
                        <option value="0" <? if ( $act_sel ) { if ( $act_sel == 0 ) { echo 'selected'; } } ?>> TODAS </option>
                        <? foreach ( $actividades as $actividad ) {  ?>
                                <option value="<?=$actividad->id?>" <? if ( $act_sel ) { if ( $act_sel == $actividad->id ) { echo 'selected'; } } ?>> <?=$actividad->nombre?> </option>
                        <? } ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Buscar Socios</button>

            </form>
	<? if ( $carnets ) { ?>
            <form class="form-horizontal ng-pristine ng-valid" id="carnets_hojas">
		<br> <br>
                <div class="form-group">
                    <label for="" class="col-sm-2" style="font-weight:bold" align="center"><b>Carnets para imprimir <?=count($carnets)?></b></label>
			<input type="hidden" name="cat_sel" value="<?=$cat_sel?>">                                
			<input type="hidden" name="foto_sel" value="<?=$foto_sel?>">                                
			<input type="hidden" name="act_sel" value="<?=$act_sel?>">                                
		</div>

		<div>
		<? $hoja = 1; $cant=0; $renglon=0; $total=count($carnets);
			do {
                	?> 
				<input type="radio" id="hoja<?=$hoja?>" name="hojas" value="<?=$hoja?>" class="btn btn-success">
				<label for="hoja<?=$hoja?>"> Hoja <?=$hoja?></label>
			<? 
				$cant = $cant + 5;
				$hoja++;
				$renglon++;
				if ( $renglon == 10 ) {
					?> </div> <div> <?
					$renglon=0;
				}
			} while ( $cant < $total )
		?>
		</div>
		<br>
		<div>
                	<button id="btn_print_hoja" value="" class="btn btn-success">Imprimir Hoja Seleccionada</button>
		</div>
            </form>
	<? } else {?>
		<div>
		    <br> <br>
                    <label for="" class="col-sm-10" style="font-weight:bold" align="center"><b>NO HAY Carnets CON ESE FILTRO </b></label>
		</div>

	<? } ?>
        </div>
    </div>
</section>                    
