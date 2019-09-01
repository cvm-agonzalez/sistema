<html>
	<head>
		<meta charset="utf-8">
		<title>Imprimir</title>
		<style type="text/css">
		body{
			font-family: 'Arial';
			font-size: 14px;
		}
		</style>
	</head>
	<body onload="window.print(); window.close();">
		<div style="float:left; width:70%">
			<h2>Listado de Socios por Actividad</h2>			
			<br>
			Actividad: <strong><?=$actividad?></strong>
		</div>
		<div style="float:left; width:30%" align="right">
                         <img src="<?=$baseurl?>entidades/<?=$ent_directorio?>/g1.jpg" alt="" width="100">
		</div>
		<br><br>
		<table id="clientes" border="1" style="border:1px solid #CCC" width="100%">
            <thead>
                <tr>                        
                    <th align="left"><div class="th">
                        Nombre y Apellido                            
                    </div></th>
                    <th align="left"><div class="th">
                        Ultimo Pago                        
                    </div></th>                    
                </tr>
            </thead>
            <tbody>
                <?
                if($socios){
                foreach ($socios as $socio) {                                        
                ?>
                <tr>
                    <td><?=$socio->socio?></td>
                    <td>
                        <?
                        if($socio->ultimo_pago != 0){
                            echo time_ago($socio->ultimo_pago);                            
                        }else{
                            echo 'Aún no se registró ningun pago';
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
	</body>
</html>
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
            $retval = "Menos de 1 Mes";
        }
        if ($granularity == '0') { break; }
    }
    return ''.$retval.'';      
}
?>
