<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
	<div class="row">
		<div class="col-md-12">
			<section class="panel panel-default">
				<div class="panel-heading">
					<span class="glyphicon glyphicon-th"></span> ENVIAR        
				</div>
				<div class="panel-body">
					<h3 class="page-heading">Enviando: <?=$envio->titulo?></h3>
					<h4 class="alert alert-info">Enviados: <span id="enviados"><?=$envio->enviados?></span>/<?=$envio->total?></h4>
					<label>Estado</label>
					<div class="well" id="estado">Iniciando envio...</div>
					<button class="btn btn-warning" id="pausar_envio"><i class="fa fa-pause"></i></button>
					<button class="btn btn-success" disabled id="reanudar_envio"><i class="fa fa-play"></i></button>
				</div>
			</section>
		</div>
	</div>
</div>