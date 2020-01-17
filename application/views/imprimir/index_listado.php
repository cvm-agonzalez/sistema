<?
function is_active($seccion1,$seccion2)
{
  if($seccion1 == $seccion2){
    echo 'active';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Impresión de Listados</title>

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
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color:#222533 !important;">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!--<a class="navbar-brand" href="#"><?=$ent_nombre?> - Listados</a>-->
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="font-size:18px !important;">Socios <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class=""><a href="<?=base_url()?>imprimir/listado/socios">Activos/Suspendidos</a></li>              
                <li class=""><a href="<?=base_url()?>imprimir/listado/actividades">Actividades</a></li>              
                <li class=""><a href="<?=base_url()?>imprimir/listado/morosos">Morosos</a></li>              
                <li class=""><a href="<?=base_url()?>imprimir/listado/categorias">Categorías</a></li>              
                <li class=""><a href="<?=base_url()?>imprimir/listado/becas">Becas</a></li>              
                <li class=""><a href="<?=base_url()?>imprimir/listado/sin_actividades">Sin Actividades</a></li>              
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="visible-print" style="margin-bottom:-40px;">
      <div class="pull-left">
        <img src="<?=base_url()?>images/logo.png" width="80">
      </div>
      <div class="pull-left" style="margin:20px 10px;">    
        Listado Generado el <?=date('d/m/Y H:i');?>        
      </div>
      <div class="clearfix"></div>
    </div>
