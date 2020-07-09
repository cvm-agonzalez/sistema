<div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> REGISTRA NUEVO DEBITO TARJETA CREDITO </strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
                            <i class="fa fa-users text-large stat-icon"></i>
                    </div>
                    <form id="cab_debtarj_form">
			    <div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <? echo $socio->nombre.' '.$socio->apellido.' ('.$socio->dni.')'; ?> <a href="#" onclick="cleear('r2')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>
                            </div>
                    </form>
                </div>            
                <div class="col-lg-12" id="debtarj-div" style="display:none;">
                    
                </div>
            </div>

        	<div class="panel-body">
				<form class="form-horizontal ng-pristine ng-valid" id="nvo_debtarj_form" >

		                <div class="form-group col-lg-6">
                    		<label for="" class="col-sm-3">Marca Tarjeta</label>
                    		<div class="col-sm-9">
                        	<span class=" ui-select">
                            	<select name="id_marca" id="id_marca" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
<? foreach ( $tarjetas as $tarjeta ) { ?>
             <option value="<?=$tarjeta->id?>" data-marca="<?=$tarjeta->descripcion?>" ><?=$tarjeta->descripcion?></option>
<?}?>
                            	</select>
                        	</span>
                    		</div>
                		</div>

				    <div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Nro de Tarjeta</label>
				            <div class="col-sm-3">
				               	<input class="form-control" name="nro_tarjeta" id="nro_tarjeta" value="" required>
		                                <input type="hidden" id="id_debito" value="0">
				            </div>
				        </div>	
				    </div>
				    <div class="col-sm-12">
						<div class="form-group">
				           	<label for="" class="col-sm-3">Fecha de Adhesion</label>
				            <div class="col-sm-3">
				               	<input class="form-control" name="fecha_adhesion" id="fecha_adhesion" value="<? echo "$fecha"; ?>" required>
				            </div>
				        </div>	
				    </div>
			        <div>
                        <input type="hidden" name="sid" id="sid" class="form-control" value="<?=$socio->id?>">
			        </div>

				    <div class="clearfix"></div>
					<div align="left" style="width:100%">
				    	<div class="form-group">
				            <div class="col-sm-6">
				               	<button class="btn-success" id="boton-deb" data-text='btnnuevo' > Grabar <i id="reg-cargando" class="fa fa-spin fa-spinner hidden"></i></button>
				            </div>
				        </div>
				    </div>
				</form>
			</div>
        </div>

    </div>
</div>

