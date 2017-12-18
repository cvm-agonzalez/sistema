  <div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> Chequeo Actividades</strong></div>
        <div class="panel-body">
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Asociado</div></th>
                                                <th><div class="th-resumen">DNI</div></th>
                                                <th><div class="th-resumen">Apellido y Nombre</div></th>
                                                <th><div class="th-resumen">Estado</div></th>
                                                <th><div class="th-resumen">Chequeo</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
		       				<? foreach ( $asociados as $asoc ) { 
						switch ( $asoc['estado_asoc'] ) {
							case 0: $xestado = "ACTIVO"; break;
							case 1: $xestado = "SUSPENDIDO"; break;
							default: $xestado = "INDEFINIDO"; break;
						}
						?>
                                                <td><?=$asoc['sid']?></td>
                                                <td><?=$asoc['dni']?></td>
                                                <td><?=$asoc['apynom']?></td>
                                                <td><?=$xestado?></td>
                                                <td><?if ($asoc['actividad'] == 1) { echo 'Relacionada'; } else { echo 'Sin Relacionar'; } ?></td>
                                        </tr>
					<? } ?>
                                                                    
				</tbody>
		</div>
		</div>

                    <a class="btn btn-primary" href="<?=$baseurl?>admin/actividades/baja-relacionadas">Da de Baja las Relacionadas</a> 
                    <a class="btn btn-primary" href="<?=$baseurl?>admin/actividades/bajarel-contrafact">Baja y contramovimiento</a> 
                    <a class="btn btn-primary" href="<?=$baseurl?>admin/actividades/alta-sin-relacionar">Relaciona las que no Estan</a> 
                    <a class="btn btn-success" href="<?=$baseurl?>admin">Volver a pantalla principal</a>                        
                </div>
            </div>
        </div>
    </div>
  </div>
