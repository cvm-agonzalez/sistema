<div class="page page-table" >
	<div class="panel panel-default table-dynamic">
      	<div class="panel-heading"><strong><span class="fa fa-dollar"></span> GENERAR DEBITOS</strong></div>
	<div class="panel-body">
		<form id="gen_debtarj_form" method="post">
		<? if ( $flag == 1 ) { ?>
			<div class="col-sm-12">
                     		<label for="" <strong> <span class="col-sm-3">Periodo ya generado, si quiere descartar generacion anterior vuelva a apretar Genera Nuevo Mes </strong> </label>
			</div>
			<div class="clearfix"></div>
		<?}?>

		<div class="form-group col-lg-5" style="padding-top:20px;">
		<div class="col-sm-7">
                     <label for="" class="col-sm-3">Periodo </label>
		     <input type="text" name="periodo" id="periodo" class="form-control" value="<?=$ult_debito?>" >
		     <input type="text" class="hidden" name="flag" id="flag" value="<?=$flag?>" >
		</div>

                <div class="form-group col-lg-18">
                     <label for="" class="col-sm-9">Marca Tarjeta</label>
                     <div class="col-sm-7">
                       <span class=" ui-select">
                       <select name="id_marca" id="id_marca" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
		       <? foreach ( $tarjetas as $tarjeta ) { ?>
              <option value="<?=$tarjeta->id?>" data-marca="<?=$tarjeta->descripcion?>" <? if($id_marca_sel == $tarjeta->id){echo 'selected';} ?> ><?=$tarjeta->descripcion?></option>

			<?}?>
                       </select>
                       </span>
                     </div>
                </div>

                <div class="col-sm-12">
			<label for="" class="col-sm-12" > &nbsp; </label>
		</div>

                <div class="form-group col-lg-18">
			<div class="col-sm-4">
                     	<button class="btn-success" data-text="generar" data-action="<?=$baseurl?>admin/debtarj/gen_nvo/" >Genera Nuevo Mes  <i id="gen_nvo" class="fa fa-spin fa-spinner hidden"></i></button>
			</div>
					
			<div class="col-sm-4">
                     	<button class="btn-success" data-text="bajar" data-action="<?=$baseurl?>admin/debtarj/baja_arch/" >Baja Archivo  <i id="baja_arch" class="fa fa-spin fa-spinner hidden"></i></button>
			</div>                        

			<div class="col-sm-4" id="btn_total" hidden >
                     	<button class="btn-success" data-text="bajar" data-action="<?=$baseurl?>admin/debtarj/baja_arch?tot=1" >Baja Archivo Totales <i id="baja_arch_tot" class="fa fa-spin fa-spinner hidden"></i></button>
			</div>                        
		</div>
		</form>

		<div class="clearfix"></div>

		<div class="panel panel-default table-dynamic">
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Marca</div></th>
                                                <th><div class="th-resumen">Periodo</div></th>
                                                <th><div class="th-resumen">Fecha Debito</div></th>
                                                <th><div class="th-resumen">Cantidad</div></th>
                                                <th><div class="th-resumen">Total</div></th>
                                                <th><div class="th-resumen">Estado</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?
                                        function mostrar_fecha($fecha)
                                    {
                                        $fecha = explode('-', $fecha);
                                        $fecha[2] = explode(' ',$fecha[2]);
                                        return $fecha[2][0].'/'.$fecha[1].'/'.$fecha[0];
                                    }
                                        foreach ($debitos_gen as $debgen) {
						$xmarca="";
                                        ?>
                                        <tr>
		       				<? foreach ( $tarjetas as $tarjeta ) { 
             		  				if ( $tarjeta->id == $debgen->id_marca ) {
								$xmarca = $tarjeta->descripcion;
							}
						   }
						   $xestado="";
						   switch ( $debgen->estado ) {
							case 0: $xestado="BAJA"; break;
							case 1: $xestado="ACTIVO"; break;
							default: $xestado="INDEFINIDO"; break;
						   }
						?>
                                                <td><?=$xmarca?></td>
                                                <td><?=$debgen->periodo?></td>
                                                <td><?=mostrar_fecha($debgen->fecha_debito)?></td>
                                                <td align="right"><?=$debgen->cant_generada?></td>
                                                <td align="right"><?=$debgen->total_generado?></td>
                                                <td><?=$xestado?></td>
                                        </tr>
					<? } ?>
                                                                    
				</tbody>
		</div>
		</div>

        </div>
      </div>
    </div>
</div>
