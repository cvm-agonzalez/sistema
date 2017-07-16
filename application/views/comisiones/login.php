<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Club Villa Mitre | Comisiones | Iniciar Sesión</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?=base_url()?>styles/bootstrap.min.css">    
	<link rel="stylesheet" href="<?=base_url()?>styles/jquery.dataTables.min.css">    
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?=base_url()?>assets/js/html5shiv.js"></script>
      <script src="<?=base_url()?>assets/js/respond.min.js"></script>
      <![endif]-->    
  </head>
  <body>
  	<div class="col-md-6 col-md-offset-3" style="margin-top:10px;">
  		<div align="center">
  			<img src="<?=base_url()?>images/logo.png" width="200">
  		</div>
  		<div class="panel panel-default">
  			<div class="panel-heading"><i class="fa fa-lock"></i> Iniciar Sesión</div>
  			<div class="panel-body">
  				<form class="form-horizontal" action="<?=base_url()?>comisiones/log" method="post">
  					<fieldset>
  						<div class="form-group">
  							<label for="email" class="col-lg-2 control-label">Email</label>
  							<div class="col-lg-10">
  								<input type="email" class="form-control" id="email" name="email" autofocus required>
  							</div>
  						</div>
  						<div class="form-group">
  							<label for="pass" class="col-lg-2 control-label">Contraseña</label>
  							<div class="col-lg-10">
  								<input type="password" class="form-control" id="pass" name="pass" required>
  							</div>
  						</div>
  						<div class="form-group">
  							<label for="pass" class="col-lg-2 control-label">&nbsp;</label>
  							<div class="col-lg-10">		              
  								<button class="btn btn-primary btn-block">Iniciar Sesión</button>
  							</div>
  						</div>
  						<?
  						if(@validation_errors()){
  						?>
  						<div class="alert alert-danger"><?=validation_errors()?></div>
  						<?
  						}
  						?>
  					</fieldset>
  				</form>
  			</div>
  		</div>
  	</div>
  	<script type="text/javascript" src="<?=base_url()?>scripts/vendor.js"></script>
  	<script type="text/javascript" src="<?=base_url()?>scripts/bootstrap.min.js"></script>
  	<script type="text/javascript" src="<?=base_url()?>scripts/jquery.dataTables.min.js"></script>
  	<script type="text/javascript">
  		$(document).ready(function(){
  			$("#presupuestos_table").DataTable({
  				"oLanguage": {
  					"sUrl": "<?=base_url()?>assets/js/data.tables.spanish.lang"
  				},      
  				"aaSorting": [[ 5, "desc" ]]
  			});
  			$("#clientes_table").DataTable({
  				"oLanguage": {
  					"sUrl": "<?=base_url()?>assets/js/data.tables.spanish.lang"
  				},      
  				"aaSorting": [[ 0, "desc" ]]
  			});
  			$("#empleados_table").DataTable({
  				"oLanguage": {
  					"sUrl": "<?=base_url()?>assets/js/data.tables.spanish.lang"
  				},      
  				"aaSorting": [[ 0, "desc" ]]
  			});
  		})
  	</script>
</body>
</html>