  <div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> ERROR</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <h4><?print_r($mensaje1)?></h4>
			<? if (isset($mensaje2)) { ?> <h4><?print_r($mensaje2)?></h4> <? }; ?>
			<? if (isset($mensaje3)) { ?> <h4><?print_r($mensaje3)?></h4> <? }; ?>
			<? if (isset($mensaje4)) { ?> <h4><?print_r($mensaje4)?></h4> <? }; ?>
                </div>
		<a class="btn btn-primary" href="javascript:history.back(-1);" title="Ir la página anterior">Volver</a>
		<? if ( isset($msj_boton) ) { ?> <a class="btn btn-primary" href="<?=$url_boton?>"><? echo $msj_boton ?></a> <? }; ?>
		<? if ( isset($msj_boton2) ) { ?> <a class="btn btn-primary" href="<?=$url_boton2?>"><? echo $msj_boton2 ?></a> <? }; ?>
            </div>
        </div>
    </div>
  </div>
