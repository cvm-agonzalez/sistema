<meta charset="utf-8">
<style>
@media print {
    #profesores_print,#profesores_table_length,#profesores_table_filter,#profesores_table_info,#profesores_table_paginate{display:none;}
    td{ font-size: 12px;}
}
</style>
<h3><?=@$profesor->nombre?> <?=@$profesor->apellido?> - <?=date('d/m/Y')?></h3>
<table id="profesores_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Nombre y Apellido</th>
            <th>Actividad</th>
            <th>Teléfono</th>
            <th>Fecha de Alta</th>
            <th>Meses Adeudados</th>
        </tr>
    </thead>
	        
    <tbody>
    	<?
    	foreach ($socios as $s) {	
            foreach ($s as $socio) {
                
    	?>
        <tr>
            <td><?=@$socio->socio?></td>
            <td><?=@$socio->act_nombre?></td>
            <td><?=@$socio->telefono?></td>
            <td><?=date('d/m/Y',strtotime($socio->date))?></td>
            <td>
            	<?
                if(@$socio->suspendido != 1){
                    if($socio->ultimo_pago != 0){
                        echo time_ago($socio->ultimo_pago);                                           
                    }                    
                }else{
                    echo '<label class="label label-danger">Usuario Suspendido</label>';
                }

                ?>
            </td>
        </tr> 
        <?
            }
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
	$('#profesores_table').DataTable({
		"language": {
	 	   "url": "<?=base_url()?>scripts/ES_ar.txt"	 	   
		},
		"order": [[ 4, "desc" ]]       	

	});
</script>