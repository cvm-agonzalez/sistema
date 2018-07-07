<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Estado de Socios</title>

	<!-- Bootstrap -->
	<link href="<?=base_url()?>styles/bootstrap.min.css" rel="stylesheet">
	<link href="<?=base_url()?>styles/jquery.dataTables.min.css" rel="stylesheet">
	<link href="<?=base_url()?>styles/daterangepicker-bs3.css" rel="stylesheet">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
    </head>
    <body>

     <nav class="navbar navbar-default">
      <div class="container-fluid">
       <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
         <span class="sr-only">Toggle navigation</span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
       </button>
       <a class="navbar-brand" href="#">Club Villa Mitre</a>
     </div>

   </div>
 </nav>

 <div class="container">
  <legend>Estado de Socios</legend>
  <form class="form-horizontal" role="search" id="search_form">
   <div class="form-group">
    <div class="col-sm-10">          
      <input type="number" id="socio_input" class="form-control" placeholder="Buscar por DNI o código de barra" autofocus required>
    </div>
    <div class="col-sm-2">
      <button type="submit" id="s_btn" data-loading-text="Buscando..." class="btn btn-success btn-block"><i class="fa fa-search"></i> Buscar</button>        
    </div>
  </div>
  </form>

<div id="socio_info" style="display: none;"></div>
</div>
<script type="text/javascript">var base_url = '<?=base_url()?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="<?=base_url()?>scripts/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>scripts/bootstrap3.min.js"></script>
<script src="<?=base_url()?>scripts/dataTables.bootstrap.js"></script>
<script src="<?=base_url()?>scripts/moment.min.js"></script>
<script src="<?=base_url()?>scripts/daterangepicker.js"></script>
<script src="<?=base_url()?>scripts/estado.js"></script>  	

</body>
</html>
