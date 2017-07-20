  <div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> RELACION ACTIVIDADES</strong></div>


        <div class="panel-body">
                <div class="form-group col-lg-18">

                         <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Asociado</div></th>
                                                <th><div class="th-resumen">DNI</div></th>
                                                <th><div class="th-resumen">Apellido y Nombre</div></th>
                                                <th><div class="th-resumen">Acción</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                                <? foreach ( $asociados as $asoc ) {
                                                ?>
                                                <td><?=$asoc['sid']?></td>
                                                <td><?=$asoc['dni']?></td>
                                                <td><?=$asoc['apynom']?></td>
                                                <td><?=$asoc['accion']?></td>
                                        </tr>
                                        <? } ?>

                                </tbody>
                </div>
        </div>




        <div class="panel-body">
            <div class="row">
		<a class="btn btn-primary" href="<?=$baseurl?>admin">Volver a la Pantalla Ppal</a>
            </div>
        </div>

    </div>
  </div>
