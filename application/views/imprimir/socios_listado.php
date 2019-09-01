<meta charset="utf-8">
<style>
@media print {
    #actividades_print,#actividades_table_length,#actividades_table_filter,#actividades_table_info,#actividades_table_paginate{display:none;}
    td{ font-size: 12px;}
}
</style>
<div class="pull-left">
    <h3><?=$titulo?>: <?=count($socios)?> | <?=date('d/m/Y H:i')?></h3>
</div>
<div class="pull-right hidden-print">
    <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
    <a href="<?=base_url()?>imprimir/socios_excel/<?=$id?>" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
</div>
<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="socios_table">
    <thead>
        <tr>
            <th>Nombre y Apellido</th>
            <th>Teléfono</th>
            <th>DNI</th>
            <th>Fecha de Alta</th>        
            <th>Monto Adeudado</th>
            <th>Meses Adeudados</th>
            <th class="hidden-print">Resumen</th>
        </tr>
    </thead>
	        
    <tbody>
    	<?
    	foreach ($socios as $socio) {    	
    	?>
        <tr>
            <td><?=@$socio->apellido?> <?=@$socio->nombre?> </td>
            <td><?=@$socio->telefono?></td>
            <td><?=@$socio->dni?></td>
            <td><?=@$socio->alta?></td>
            <td>
                <? if($socio->deuda_monto < 0){ ?>
                    $ <?=$socio->deuda_monto*-1?>                
                <?
                }else{
                ?>
                    <div class="label label-success"> $ 0</div>
                <?
                }
                ?>
            </td>
            <td>
                <?
                if($socio->deuda){                      
                    $hoy = new DateTime();
                    $d2 = new DateTime($socio->deuda->generadoel);                
                    $interval = $d2->diff($hoy);
                    $meses = $interval->format('%m');
                    if($meses > 0){
                    ?>
                    <div class="label label-danger">Debe <?=$meses?> <? if($meses > 1){ echo 'Meses';}else{echo 'Mes';} ?></div>                
                    <?
                    }else{
                        if( $hoy->format('%m') != $d2->format('%m') && $socio->deuda->monto != '0.00' ){
                        ?>
                        <div class="label label-warning">Saldo del mes anterior</div>
                        <?
                        }else{                    
                        ?>
                        <div class="label label-success">Cuota al Día</div>
                        <?                
                        }
                    }
                }else{
                    ?>
                    <label class="label label-warning">Sin Deuda / Aún no se registró ningun pago.</label>
                    <?
                }
                ?>
            </td> 
            <td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$socio->id?>" class="btn btn-warning btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>           
        </tr> 
        <?
    	}
        ?>          
    </tbody>   
</table>
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
