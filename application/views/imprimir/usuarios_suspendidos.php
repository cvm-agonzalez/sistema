<meta charset="utf-8">
<style>
@media print {
    #suspendidos_print,#suspendidos_table_length,#suspendidos_table_filter,#suspendidos_table_info,#suspendidos_table_paginate{display:none;}
    td{ font-size: 12px;}
}
</style>
<div class="container" style="margin-top:50px;">
    <div class="starter-template">
    <h3>Usuarios Suspendidos - <?=date('d/m/Y')?></h3>
    <table id="suspendidos_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Nombre y Apellido</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Fecha de Alta</th>
                <th>Deuda</th>
            </tr>
        </thead>
    	        
        <tbody>
        	<?
        	foreach ($socios as $socio) {    	
        	?>
            <tr>
                <td><?=@$socio->nombre?> <?=@$socio->apellido?></td>
                <td><?=@$socio->telefono?></td>
                <td><?=@$socio->dni?></td>
                <td><?=date('d/m/Y',strtotime($socio->alta))?></td>
                <td>
                	$ <?=@$socio->deuda*-1?>
                </td>
            </tr> 
            <?
        	}
            ?>          
        </tbody>   
    </table>
</div>
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
	
</script>