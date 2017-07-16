  <div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> Resultado Generacion</strong></div>
        <div class="panel-body">
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Renglon</div></th>
                                                <th><div class="th-resumen">Asociado</div></th>
                                                <th><div class="th-resumen">Observacion</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
		       				<? foreach ( $result as $res ) { 
						?>
                                                <td><?=$res['renglon']?></td>
                                                <td><?=$res['sid']?></td>
                                                <td><?=$res['mensaje']?></td>
                                        </tr>
					<? } ?>
                                                                    
				</tbody>
		</div>
		</div>

                    <a class="btn btn-primary" href="<?=$baseurl?>admin/debtarj/list-debtarj">Volver al Listado de Debitos</a> 
                    <a class="btn btn-success" href="<?=$baseurl?>admin">Volver a pantalla principal</a>                        
                </div>
            </div>
        </div>
    </div>
  </div>
