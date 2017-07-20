<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-question-circle"></span> SOPORTE</strong></div>
        <div class="panel-body">
        	<form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/soporte/enviar" method="post">
                <div class="form-group col-lg-12">
                    <label for="" class="col-sm-12">Consulta</label>
                    <div class="col-sm-12">
                        <textarea type="text" rows="8" id="consulta" name="consulta" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                	<div class="col-sm-12">
	                	<button class="btn btn-primary">Enviar Consulta</button>
	                </div>
               	</div>
            </form>
       	</div>
    </div>
</section>