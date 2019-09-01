<div class="container" style="margin-top:50px;">
    <div class="starter-template">
        <h1>Deuda Anterior</h1>
        <div id="actividades_print">
	        <div class="col-xs-6">
	        	<label>Seleccione Actividad:</label>
	        	<select class="form-control" id="actividades_select_anterior"> 
	        		<option value="">--</option>
	        		<?
	        		foreach ($actividades as $aa) {        		
	        		?>
	        		<option value="<?=$aa->id?>" <? if($actividad->id == $aa->id ){ echo 'selected'; } ?>><?=$aa->nombre?></option>
	        		<?
	        		}
	        		?>
	    		</select>
	    	</div>
	    	<div class="clearfix"></div>
	    </div>
    	<div id="listado_actividad" class="hidden">
    		
	        
		</div>
  	</div>


    <?
    if($socios){
    ?>
    <style>
    @media print {
        #actividades_print,#actividades_table_length,#actividades_table_filter,#actividades_table_info,#actividades_table_paginate{display:none;}
        td{ font-size: 12px;}
    }
    </style>
    <div class="pull-left">
        <h3><?=$actividad->nombre?>: <?=count($socios)?></h3>
    </div>
    <div class="pull-right hidden-print">
        <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
        <a href="<?=base_url()?>imprimir/anterior_excel/<?=$actividad->id?>" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
    </div>
    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="actividades_table">
        <thead>
            <tr>
                <th>Nombre y Apellido</th>
                <th>Socio</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Fecha de Nacimiento</th>
                <th>Fecha de Alta</th>
                <th>Sin Deuda Hasta el 30/04/2015</th>
                <th>Resumen</th>
            </tr>
        </thead>
    	        
        <tbody>
        	<?
        	foreach ($socios as $socio) {    	
        	?>
            <tr>
                <td><?=@$socio->socio?></td>
                <td># <?=@$socio->id?></td>
                <td><?=@$socio->telefono?></td>
                <td><?=@$socio->dni?></td>
                <td><?=@$socio->nacimiento?></td>
                <td><?=$socio->date?></td>
                <td align="center">
                	<?
                    if($socio->deuda == 0){
                    ?>
                    <i class="fa fa-check text-success fa-2x"></i>
                    <?
                    }else{
                    ?>
                    <i class="fa fa-times text-danger fa-2x"></i>
                    <?
                    }
                    ?>
                </td>
                <td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$socio->id?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>
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
    	$('#actividades_table').DataTable({
    		"language": {
    	 	   "url": "<?=base_url()?>scripts/ES_ar.txt"	 	   
    		},
    		"order": [[ 3, "desc" ]],
            "paging": false

    	});
    </script>
    <? } ?>

</div><!-- /.container -->
