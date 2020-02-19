<meta charset="utf-8">
<style>
@media print {
    #actividades_print,#actividades_table_length,#actividades_table_filter,#actividades_table_info,#actividades_table_paginate{display:none;}
    td{ font-size: 12px;}
}
</style>
<div class="container" style="width:90%; margin-top:90px;">
<div class="pull-left">
    <h3>Socios sin Actividades Asociadas | <?=date('d/m/Y H:i')?></h3>
</div>
<div class="pull-right hidden-print">
    <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
    <a href="<?=base_url()?>imprimir/sin_actividad_excel" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
</div>
<div class="clearfix"></div>
<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="socios_table">
    <thead>
        <tr>
            <th>Nombre y Apellido</th>
            <th>#ID</th>
            <th>Nro Socio</th>
            <th>Teléfono</th>
            <th>DNI</th>
            <th>Fecha de Nacimiento</th>            
            <th>Fecha de Ingreso</th>            
            <th class="hidden-print">Resumen</th>
        </tr>
    </thead>
	        
    <tbody>
    	<?
        if($socios){
    	foreach ($socios as $socio) {    	
    	?>
        <tr>
            <td><?=@$socio->apellido?> <?=@$socio->nombre?> </td>
            <td align="right"># <?=@$socio->id?></td>
            <td align="right"><?=@$socio->nro_socio?></td>
            <td align="right"><?=@$socio->telefono?></td>
            <td align="right"><?=@$socio->dni?></td>
            <td><?=@$socio->nacimiento?></td>            
            <td><?=@$socio->alta?></td>            
            <td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$socio->id?>" class="btn btn-warning btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>           
        </tr> 
        <?
    	}
        }
        ?>
    </tbody>   
</table>
</div>
<?
function time_ago($date,$granularity=2) {
    $retval = '';
    $date = strtotime($date);
    $difference = time() - $date;
    $periods = array(
        'mes' => 2628000
        );

    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = floor($difference/$value);
            $difference %= $value;
            $retval .= ($retval ? ' ' : '').$time.' ';
            $retval .= (($time > 1) ? $key.'es' : $key);
            $granularity--;
        }else{
            $retval = "1 Mes";
        }
        if ($granularity == '0') { break; }
    }
    return ''.$retval.'';      
}
?>
<script type="text/javascript">
	$('#socios_table').DataTable({
		"language": {
	 	   "url": "<?=base_url()?>scripts/ES_ar.txt"	 	   
		},
		"order": [[ 3, "desc" ]],
        "paging":   false,       	

	});
</script>
