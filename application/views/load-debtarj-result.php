  <div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> Resultado Cruce</strong></div>
        <div class="panel-body">
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Movimiento</div></th>
                                                <th><div class="th-resumen">Periodo</div></th>
                                                <th><div class="th-resumen">Fecha</div></th>
                                                <th><div class="th-resumen">Cantidad</div></th>
                                                <th><div class="th-resumen">Importe</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                                <td>Generacion Debito</td>
                                                <td><?=$datos_gen->periodo?></td>
                                                <td><?=$datos_gen->fecha_debito?></td>
                                                <td><?=$datos_gen->cant_generada?></td>
                                                <td><?=$datos_gen->total_generado?></td>
                                        </tr>
                                        <tr>
                                                <td>Acreditacion Tarjetera</td>
                                                <td><?=$datos_gen->periodo?></td>
                                                <td><?=$datos_gen->fecha_debito?></td>
                                                <td><?=$datos_gen->cant_acreditada?></td>
                                                <td><?=$datos_gen->total_acreditado?></td>
                                        </tr>
                                                                    
				</tbody>
		</div>
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Socio</div></th>
                                                <th><div class="th-resumen">Apellido y Nombre</div></th>
                                                <th><div class="th-resumen">Tarjeta</div></th>
                                                <th><div class="th-resumen">Numero</div></th>
                                                <th><div class="th-resumen">Importe</div></th>
                                                <th><div class="th-resumen">Mensaje</div></th>
                                        </tr>
                                </thead>
                                <tbody>

					<? foreach ( $debitos_error as $deberr ) { 
						$apynom=$deberr->apellido.', '.$deberr->nombre;
						if ( $deberr->nro_renglon != null ) {
							$mensaje="Renglon= ".$deberr->nro_renglon." - No vino acreditado en archivo de tarjetera";
						} else {
							$mensaje="No vino acreditada en archivo de tarjetera";
						}
					?>
                                        	<tr>
                                                	<td><?=$deberr->sid?></td>
'                                                	<td><?=$apynom?></td>
                                                	<td><?=$deberr->marca?></td>
                                                	<td><?=$deberr->nro_tarjeta?></td>
                                                	<td><?=$deberr->importe?></td>
                                                	<td><?=$mensaje?></td>

                                        	</tr>
					<? } ?>
                                                                    
				</tbody>
		</div>
		</div>

                    <a class="btn btn-primary" href="<?=$baseurl?>admin/debtarj/list-debtarj">Volver al Listado de Debitos</a> 
                    <a class="btn btn-success" href="<?=$baseurl?>admin/debtarj/subearchivo/<?=$id_marca?>/<?=$fecha_debito?>/excel">Bajar a EXCEL</a>                        
                </div>
            </div>
        </div>
    </div>
  </div>
