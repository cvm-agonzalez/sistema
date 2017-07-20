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
		<div style="float:left; width:50%">
			<h2>Listado de Morosos</h2>
			Deben más de <?=$meses?> meses.
			<br><br>
			Actividad: <?=$actividad?>
		</div>
		<div style="float:left; width:50%" align="right">
			<img src="<?=base_url()?>images/g1.jpg" width="100">
		</div>
		<br><br>
		<table id="clientes" border="1" style="border:1px solid #CCC" width="100%">
            <thead>
                <tr>                        
                    <th align="left"><div class="th">
                        Nombre y Apellido                            
                    </div></th>
                    <th align="left"><div class="th">
                        Deuda                        
                    </div></th>                    
                </tr>
            </thead>
            <tbody>
                <?
                if($morosos){
                foreach ($morosos as $moroso) {                                        
                ?>
                <tr>
                    <td><?=$moroso->nomb?></td>
                    <td>$ <?=$moroso->deuda*-1?></td>                    
                </tr>
                <?
                }
                }
                ?>
            </tbody>
        </table> 
	</body>
</html>