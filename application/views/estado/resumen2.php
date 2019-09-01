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
       <a class="navbar-brand" href="#"><?=$ent_nombre?></a>
     </div>

   </div>
   </nav>

 <div class="container">
  <legend>Estado de Socios</legend>
  <form class="form-horizontal" role="search" id="search_form">
   <div class="form-group">
    <div class="col-sm-10">          
      <input type="number" id="socio_input" class="form-control" <? if ( isset($socio) ) { echo '"value='.$socio.'"'; } ?> placeholder="Buscar por DNI o código de barra" autofocus required>
    </div>
  </div>
</form>

<div id="socio_info" style="display: none;"></div>

<table class="table table-bordered table-striped table-responsive table-resumen">
	<thead>
		<tr>
			<th><div class="th-resumen"># ID</div></th>
			<th><div class="th-resumen">Fecha</div></th>
			<th><div class="th-resumen">Descripción</div></th>
			<th><div class="th-resumen">Debe</div></th>
			<th><div class="th-resumen">Haber</div></th>
			<th><div class="th-resumen">Total</div></th>
		</tr>
	</thead>
	<tbody>
		<?
		function mostrar_fecha($fecha)
		{
			$fecha = explode('-', $fecha);
			$fecha[2] = explode(' ',$fecha[2]);
			return $fecha[2][0].'/'.$fecha[1].'/'.$fecha[0];
		}	
		foreach ($facturacion as $ingreso) {				
			?>
			<tr class="<? if($ingreso->debe != 0){ echo 'danger'; }else{ echo 'success'; } ?>">
				<td><?=$ingreso->Id?></td>
				<td><?=mostrar_fecha($ingreso->date)?></td>
				<td>
					<div class="" id="socio_desc" data-id="<?=$ingreso->Id?>"><?=$ingreso->descripcion?></div>

				</td>
				<td class="debe">$ <?=$ingreso->debe?></td>
				<td class="haber">$ <?=$ingreso->haber?></td>
				<td class="<? if($ingreso->total < 0){ echo 'debe'; }else{ echo 'haber'; } ?>">$ <?=$ingreso->total?></td>
			</tr>
			<?
		}
		?>											
	</tbody>			
</table>
</div>
</body>
</html>
