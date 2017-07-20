  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> RESUMEN ENVIADO</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <? if($enviado == 'ok'){ ?>
                    <h4>El resumen se envio correctamente.</h4>
                    <? }else if($enviado == 'no_mail'){ ?>
                    <h4>El resumen no se envio ya que el socio no posee una dirección de correo válida.</h4>
                    <? } ?>
                    <a class="btn btn-primary" href="<?=$baseurl?>admin/socios">Volver al Listado de Socios</a>
                </div>
            </div>
        </div>
    </div>
  </div>