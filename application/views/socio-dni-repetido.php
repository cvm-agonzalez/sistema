  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> SOCIOS</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <h4>Atención el DNI del socio que intento agregar ya se encuentra en nuestra base de datos.</h4>                    
                    <p>Nombre y Apellido: <?=$prev_user[0]->nombre.' '.$prev_user[0]->apellido?><br>
                    Fecha de Alta: <?=$prev_user[0]->alta?>
                    <? $uid = $prev_user[0]->id; ?>

                    </p>
                    <a class="btn btn-primary" href="<?=$baseurl?>admin/socios"><i class="fa fa-arrow-left"></i> Volver al Listado</a> 
                    <a class="btn btn-danger" href="<?=$baseurl?>admin/socios/agregar"><i class="fa fa-plus"></i> Nuevo socio</a><br><br>
                    <a class="btn btn-success" href="<?=$baseurl?>admin/socios/editar/<?=$uid?>"><i class="fa fa-user"></i> Editar este socio</a>
                    <a class="btn btn-warning" href="<?=$baseurl?>admin/actividades/asociar/<?=$uid?>"><i class="fa fa-calendar"></i> Asociar Actividades a este socio</a>
                    <a class="btn btn-info" href="<?=$baseurl?>admin/pagos/cupon/<?=$uid?>"><i class="fa fa-dollar"></i> Generar Cupón</a>
                    
                    
                </div>
            </div>
        </div>
    </div>
  </div>
